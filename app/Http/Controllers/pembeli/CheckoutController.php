<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
                foreach ($produkInput as $id => $data) {
                    if (!empty($data['check'])) {
                        $produkTerpilih[] = [
                            'id'     => $data['id'],
                            'nama'   => $data['nama'],
                            'harga'  => $data['harga'],
                            'jumlah' => $data['jumlah'],
                            'gambar' => $data['gambar'] ?? null,
                        ];
                    }
                }
                session(['checkout_langsung' => true]);
            } else {
                $keranjang = session('keranjang', []);
                foreach ($produkInput as $id => $data) {
                    if (!empty($data['check']) && isset($keranjang[$id])) {
                        $produkTerpilih[] = [
                            'id'     => $id,
                            'nama'   => $keranjang[$id]['nama'],
                            'harga'  => $keranjang[$id]['harga'],
                            'jumlah' => $keranjang[$id]['jumlah'],
                            'gambar' => $keranjang[$id]['gambar'] ?? null,
                        ];
                    }
                }
                session(['checkout_langsung' => false]);
            }

            if (empty($produkTerpilih)) {
                $route = $langsungBeli ? 'pembeli.produk.index' : 'keranjang.index';
                return redirect()->route($route)->with('error', 'Silakan pilih produk untuk checkout.');
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
                'nama'    => 'required|string|max:100',
                'alamat'  => 'required|string|max:255',
                'telepon' => 'required|string|max:20',
                'metode'  => 'required|in:midtrans,cod,dana',
            ]);

            $produk   = session('checkout_produk');
            $langsung = session('checkout_langsung', false);

            if (!$produk || empty($produk)) {
                return redirect()->route('keranjang.index')->with('error', 'Data produk tidak tersedia.');
            }

            $orderId = 'ORD-' . time() . '-' . strtoupper(uniqid());
            $total   = collect($produk)->sum(fn($item) => $item['harga'] * $item['jumlah']);

            if ($total <= 0) {
                return redirect()->route('checkout.form')->with('error', 'Total transaksi tidak valid.');
            }

            $transaksi = Transaksi::create([
                'user_id'         => Auth::id(),
                'order_id'        => $orderId,
                'nama'            => $validated['nama'],
                'alamat'          => $validated['alamat'],
                'telepon'         => $validated['telepon'],
                'tanggal_pesanan' => now()->toDateString(),
                'total'           => $total,
                'status'          => 'pending',
                'metode'          => $validated['metode'],
            ]);

            if (!$langsung) {
                $keranjang = session('keranjang', []);
                foreach ($produk as $item) {
                    unset($keranjang[$item['id']]);
                }
                session(['keranjang' => $keranjang]);
            }

            if ($validated['metode'] === 'midtrans') {
                return $this->processMidtransPayment($orderId, $total, $produk, $validated, $langsung);
            }

            session()->forget(['checkout_produk', 'checkout_total', 'checkout_langsung']);

            return redirect()->route('pembeli.produk.index')
                ->with('success', 'Pesanan berhasil dibuat. Order ID: ' . $orderId);
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

            $itemDetails = array_map(function ($item) {
                return [
                    'id'       => $item['id'],
                    'price'    => (int) $item['harga'],
                    'quantity' => (int) $item['jumlah'],
                    'name'     => $item['nama'] ?? 'Produk',
                ];
            }, $produk);

            $params = [
                'transaction_details' => [
                    'order_id'     => $orderId,
                    'gross_amount' => $total,
                ],
                'customer_details' => [
                    'first_name' => $data['nama'],
                    'phone'      => $data['telepon'],
                    'billing_address' => [
                        'first_name' => $data['nama'],
                        'address'    => $data['alamat'],
                        'phone'      => $data['telepon'],
                    ],
                ],
                'item_details' => $itemDetails,
                'callbacks'    => [
                    'finish' => route('pembeli.produk.index'),
                ]
            ];

            Log::info('MIDTRANS ITEM DETAILS', $itemDetails);
            Log::info('MIDTRANS PARAMS', $params);

            $snapToken = Snap::getSnapToken($params);

            Log::info('SNAP TOKEN', [$snapToken]);

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

    public function callback(Request $request)
    {
        try {
            $payload      = $request->getContent();
            $notification = json_decode($payload, true);

            $validSignatureKey = hash("sha512", 
                $notification['order_id'] . 
                $notification['status_code'] . 
                $notification['gross_amount'] . 
                config('midtrans.server_key')
            );

            if ($validSignatureKey !== $notification['signature_key']) {
                return response()->json(['status' => 'error', 'message' => 'Invalid signature key'], 403);
            }

            $transaksi = Transaksi::where('order_id', $notification['order_id'])->first();
            if (!$transaksi) {
                return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
            }

            switch ($notification['transaction_status']) {
                case 'capture':
                case 'settlement':
                    $transaksi->status = 'paid';
                    break;
                case 'pending':
                    $transaksi->status = 'pending';
                    break;
                case 'deny':
                case 'expire':
                case 'cancel':
                    $transaksi->status = 'failed';
                    break;
            }

            $transaksi->save();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Midtrans Callback Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function simpanAlamat(Request $request)
    {
        $request->validate([
            'alamat'   => 'required|string|max:255',
            'kota'     => 'required|string|max:100',
            'kode_pos' => 'required|string|max:10',
        ]);

        session([
            'checkout_alamat'   => $request->alamat,
            'checkout_kota'     => $request->kota,
            'checkout_kode_pos' => $request->kode_pos,
        ]);

        return response()->json(['success' => true]);
    }
}
