<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use App\Models\Produk;
use App\Models\Keranjang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

// Jangan lupa pasang package midtrans via composer
use Midtrans\Snap;
use Midtrans\Config;

class CheckoutController extends Controller
{
    public function storeProduk(Request $request)
    {
        try {
            $langsungBeli = $request->input('langsung_beli', false);
            $produkInput = $request->input('produk', []);
            $produkTerpilih = [];

            if ($langsungBeli) {
                foreach ($produkInput as $data) {
                    if (!empty($data['check'])) {
                        $produkTerpilih[] = [
                            'id'     => $data['id'],
                            'nama'   => $data['nama'],
                            'harga'  => (int) $data['harga'],
                            'jumlah' => max(1, (int) ($data['jumlah'] ?? 1)),
                            'gambar' => $data['gambar'] ?? null,
                            'stok'   => Produk::find($data['id'])->stok ?? 0,
                        ];
                    }
                }
                session(['checkout_langsung' => true]);
            } else {
                $keranjang = Keranjang::where('user_id', Auth::id())->get()->keyBy('produk_id');

                foreach ($produkInput as $id => $data) {
                    if (!empty($data['check']) && $keranjang->has($id)) {
                        $item = $keranjang[$id];
                        $jumlah = ($item->jumlah > 0) ? $item->jumlah : 1;

                        $produkTerpilih[] = [
                            'id'     => $data['id'],
                            'nama'   => $data['nama'],
                            'harga'  => (int) $data['harga'],
                            'jumlah' => max(1, (int) ($data['jumlah'] ?? 1)),
                            'gambar' => $data['gambar'] ?? null,
                            'stok'   => Produk::find($data['id'])->stok ?? 0,
                        ];
                    }
                }
                session(['checkout_langsung' => false]);
            }

            if (empty($produkTerpilih)) {
                $redirectRoute = $langsungBeli ? 'pembeli.produk.index' : 'keranjang.index';
                return redirect()->route($redirectRoute)->with('error', 'Silakan pilih produk untuk checkout.');
            }

            $total = collect($produkTerpilih)->sum(fn($item) => $item['harga'] * $item['jumlah']);

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

        public function process(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama'              => 'required|string|max:100',
                'alamat'            => 'required|string|max:255',
                'telepon'           => ['required', 'regex:/^\d{10,16}$/'],
                'tanggal_pemesanan' => 'required|date',
                'produk'            => 'required|array',
                'produk.*.jumlah'   => 'required|integer|min:1',
            ]);

            $produkSession = session('checkout_produk');
            if (!$produkSession || empty($produkSession)) {
                return redirect()->route('keranjang.index')->with('error', 'Data produk tidak tersedia.');
            }

            // Generate order_id unik
            $orderId = 'ORD-' . now()->timestamp . '-' . Auth::id() . '-' . strtoupper(Str::random(5));

            // Hitung total harga
            $total = collect($produkSession)->sum(fn($item) => $item['harga'] * $item['jumlah']);

            // Simpan data sementara di session untuk dipakai callback
            session(['pending_order_'.$orderId => [
                'user_id' => Auth::id(),
                'nama'    => $validated['nama'],
                'alamat'  => $validated['alamat'],
                'telepon' => $validated['telepon'],
                'tanggal_pesanan' => $validated['tanggal_pemesanan'],
                'total'           => $total,
                'status'          => 'sedang diproses',
                'metode'          => $validated['metode'],
                'produk'  => $produkSession,
                'total'   => $total,
            ]]);

            // Konfigurasi Midtrans
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            // Siapkan detail item untuk Midtrans
            $itemDetails = array_map(fn($item) => [
                'id'       => (string) $item['id'],
                'price'    => (int) $item['harga'],
                'quantity' => (int) $item['jumlah'],
                'name'     => $item['nama'] ?? 'Produk',
            ], $produkSession);

            $params = [
                'transaction_details' => [
                    'order_id'     => $orderId,
                    'gross_amount' => $total,
                ],
                'customer_details' => [
                    'first_name' => $validated['nama'],
                    'phone'      => $validated['telepon'],
                ],
                'item_details' => $itemDetails,
            ];

            $snapToken = Snap::getSnapToken($params);

            return view('pembeli.checkout.snap', [
                'snapToken' => $snapToken,
                'orderId'   => $orderId,
            ]);

        } catch (\Exception $e) {
            Log::error('Checkout Process Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses pembayaran.');
                } 
            }

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

            Log::info('Midtrans params:', $params);
            $snapToken = Snap::getSnapToken($params);
            Log::info('Midtrans snap token:', [$snapToken]);

            session()->forget(['checkout_produk', 'checkout_total', 'checkout_langsung']);

            return view('pembeli.checkout.snap', [
                'snapToken' => $snapToken,
                'orderId'   => $orderId,
                'produk'    => $produk,
                'total'     => $total,
                'langsung'  => $langsung,
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans Payment Error: ' . $e->getMessage());
            return redirect()->route('checkout.form')->with('error', 'Gagal memproses pembayaran Midtrans.');
        }
    }
}
