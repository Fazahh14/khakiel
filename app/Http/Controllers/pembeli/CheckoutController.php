<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use App\Models\Produk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Midtrans\Snap;
use Midtrans\Config;

class CheckoutController extends Controller
{
    /**
     * Proses pemilihan produk dari keranjang atau langsung beli
     */
    public function storeProduk(Request $request)
    {
        try {
            $langsungBeli = $request->input('langsung_beli', false);
            $produkInput = $request->input('produk', []);
            $produkTerpilih = [];

            if ($langsungBeli) {
                // Produk dari halaman langsung beli (checkbox)
                foreach ($produkInput as $data) {
                    if (!empty($data['check'])) {
                        $produkTerpilih[] = [
                            'id'     => $data['id'],
                            'nama'   => $data['nama'],
                            'harga'  => (int) $data['harga'],
                            'jumlah' => max(1, (int) ($data['jumlah'] ?? 1)),
                            'gambar' => $data['gambar'] ?? null,
                        ];
                    }
                }
                session(['checkout_langsung' => true]);
            } else {
                // Produk dari keranjang (session)
                $keranjang = session('keranjang', []);
                foreach ($produkInput as $id => $data) {
                    if (!empty($data['check']) && isset($keranjang[$id])) {
                        $produkTerpilih[] = [
                            'id'     => $id,
                            'nama'   => $keranjang[$id]['nama'],
                            'harga'  => (int) $keranjang[$id]['harga'],
                            'jumlah' => max(1, (int) $keranjang[$id]['jumlah']),
                            'gambar' => $keranjang[$id]['gambar'] ?? null,
                        ];
                    }
                }
                session(['checkout_langsung' => false]);
            }

            if (empty($produkTerpilih)) {
                $redirectRoute = $langsungBeli ? 'pembeli.produk.index' : 'keranjang.index';
                return redirect()->route($redirectRoute)->with('error', 'Silakan pilih produk untuk checkout.');
            }

            // Hitung total harga
            $total = collect($produkTerpilih)->sum(fn($item) => $item['harga'] * $item['jumlah']);

            // Simpan ke session
            session([
                'checkout_produk' => $produkTerpilih,
                'checkout_total'  => $total,
            ]);

            return redirect()->route('checkout.form');
        } catch (\Exception $e) {
            Log::error('Checkout Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses checkout.');
        }
    }

    /**
     * Tampilkan form checkout berdasarkan produk yang sudah dipilih
     */
    public function form()
    {
        try {
            $produk = session('checkout_produk');
            $total = session('checkout_total');

            if (!$produk || empty($produk)) {
                return redirect()->route('keranjang.index')->with('error', 'Produk tidak tersedia untuk checkout.');
            }

            return view('pembeli.checkout.form', compact('produk', 'total'));
        } catch (\Exception $e) {
            Log::error('Checkout Form Error: ' . $e->getMessage());
            return redirect()->route('keranjang.index')->with('error', 'Terjadi kesalahan saat menampilkan form checkout.');
        }
    }

    /**
     * Proses submit form checkout: simpan transaksi, update stok, dan proses pembayaran
     */
    public function process(Request $request)
    {
        try {
            // Validasi input form checkout
           $validated = $request->validate([
    'nama'               => 'required|string|max:100',
    'alamat'             => 'required|string|max:255',
    'telepon'            => ['required', 'regex:/^\d{10,16}$/'],
    'metode'             => 'required|in:midtrans',
    'tanggal_pemesanan'  => 'required|date',
    'produk'             => 'required|array',
    'produk.*.jumlah'    => 'required|integer|min:1',
]);


            $produkSession = session('checkout_produk');
            if (!$produkSession || empty($produkSession)) {
                return redirect()->route('keranjang.index')->with('error', 'Data produk tidak tersedia.');
            }

            $produkRequest = $request->input('produk', []);

            // Sinkronisasi jumlah input form dengan data produk session
            $produkFinal = [];
            foreach ($produkSession as $index => $item) {
                $jumlahBaru = isset($produkRequest[$index]['jumlah']) ? (int) $produkRequest[$index]['jumlah'] : $item['jumlah'];
                $produkFinal[] = [
                    'id'     => $item['id'],
                    'nama'   => $item['nama'],
                    'harga'  => $item['harga'],
                    'jumlah' => max(1, $jumlahBaru),
                    'gambar' => $item['gambar'] ?? null,
                ];
            }

            $total = collect($produkFinal)->sum(fn($item) => $item['harga'] * $item['jumlah']);
            if ($total <= 0) {
                return redirect()->route('checkout.form')->with('error', 'Total transaksi tidak valid.');
            }

            // Generate order ID unik
            $orderId = 'ORD-' . time() . '-' . strtoupper(uniqid());

            // Simpan transaksi utama
            // Simpan transaksi utama
$transaksi = Transaksi::create([
    'user_id'         => Auth::id(),
    'order_id'        => $orderId,
    'nama'            => $validated['nama'],
    'alamat'          => $validated['alamat'],
    'telepon'         => $validated['telepon'],
    // Simpan hanya tanggal tanpa waktu
   'tanggal_pesanan' => $validated['tanggal_pemesanan'],
    'total'           => $total,
    'status'          => 'sedang diproses',
    'metode'          => $validated['metode'],
]);


            // Simpan item transaksi dan update stok produk
            foreach ($produkFinal as $item) {
                TransaksiItem::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id'    => $item['id'],
                    'nama_produk'  => $item['nama'],
                    'qty'          => $item['jumlah'],
                    'harga'        => $item['harga'],
                ]);

                $produkModel = Produk::find($item['id']);
                if ($produkModel) {
                    $produkModel->stok = max(0, $produkModel->stok - $item['jumlah']);
                    $produkModel->save();
                }
            }

            // Hapus produk dari keranjang jika bukan checkout langsung
            if (!session('checkout_langsung', false)) {
                $keranjang = session('keranjang', []);
                foreach ($produkFinal as $item) {
                    unset($keranjang[$item['id']]);
                }
                session(['keranjang' => $keranjang]);
            }

            // Proses pembayaran via Midtrans
            if ($validated['metode'] === 'midtrans') {
                return $this->processMidtransPayment($orderId, $total, $produkFinal, $validated, session('checkout_langsung', false));
            }

            // Bersihkan session checkout
            session()->forget(['checkout_produk', 'checkout_total', 'checkout_langsung']);

            return redirect()->route('pembeli.produk.index')
                ->with('success', 'Pesanan berhasil dibuat. Order ID: ' . $orderId);
        } catch (\Exception $e) {
            Log::error('Checkout Process Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses pembayaran.');
        }
    }

    /**
     * Proses pembayaran Midtrans dan tampilkan token snap
     */
    protected function processMidtransPayment($orderId, $total, $produk, $data, $langsung)
    {
        try {
            Config::$serverKey    = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized  = true;
            Config::$is3ds        = true;

            $itemDetails = array_map(fn($item) => [
                'id'       => (string) $item['id'],
                'price'    => (int) $item['harga'],
                'quantity' => (int) $item['jumlah'],
                'name'     => $item['nama'] ?? 'Produk',
            ], $produk);

            $customerDetails = [
                'first_name' => $data['nama'] ?? 'Pembeli',
                'email'      => $data['email'] ?? 'no-reply@example.com',
                'phone'      => $data['telepon'] ?? '0000000000',
                'billing_address' => [
                    'first_name' => $data['nama'] ?? 'Pembeli',
                    'address'    => $data['alamat'] ?? '-',
                    'phone'      => $data['telepon'] ?? '0000000000',
                ],
            ];

            $params = [
                'transaction_details' => [
                    'order_id'     => $orderId,
                    'gross_amount' => $total,
                ],
                'customer_details' => $customerDetails,
                'item_details'     => $itemDetails,
            ];

            $snapToken = Snap::getSnapToken($params);

            // Hapus session checkout
            session()->forget(['checkout_produk', 'checkout_total', 'checkout_langsung']);

            return view('pembeli.checkout.snap', [
                'snapToken' => $snapToken,
                'orderId'   => $orderId,
                'produk'    => $produk,
                'total'     => $total,
                'source'    => $langsung ? 'detail' : 'keranjang',
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage());
            return redirect()->route('checkout.form')->with('error', 'Gagal membuat token pembayaran.');
        }
    }
} 