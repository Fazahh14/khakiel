<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;
use Midtrans\Snap;
use Midtrans\Config;

class CheckoutController extends Controller
{
    public function storeProduk(Request $request)
    {
        $langsungBeli = $request->input('langsung_beli');
        $produkInput = $request->input('produk', []);
        $produkTerpilih = [];

        if ($langsungBeli) {
            foreach ($produkInput as $id => $data) {
                $produkTerpilih[] = [
                    'id'     => $data['id'],
                    'nama'   => $data['nama'],
                    'harga'  => $data['harga'],
                    'jumlah' => $data['jumlah'],
                    'gambar' => $data['gambar'],
                ];
            }
            session(['checkout_langsung' => true]);
        } else {
            $keranjang = session('keranjang', []);
            foreach ($produkInput as $id => $data) {
                if (isset($keranjang[$id]) && isset($data['check'])) {
                    $produkTerpilih[] = [
                        'id'     => $id,
                        'nama'   => $data['nama'],
                        'harga'  => $data['harga'],
                        'jumlah' => $data['jumlah'],
                        'gambar' => $data['gambar'],
                    ];
                }
            }
            session(['checkout_langsung' => false]);
        }

        if (empty($produkTerpilih)) {
            $route = $langsungBeli ? 'pembeli.produk.index' : 'keranjang.index';
            return redirect()->route($route)->with('error', 'Silakan pilih produk untuk checkout.');
        }

        $total = collect($produkTerpilih)->sum(function ($item) {
            return $item['harga'] * $item['jumlah'];
        });

        session([
            'checkout_produk' => $produkTerpilih,
            'checkout_total' => $total
        ]);

        return redirect()->route('checkout.form');
    }

    public function form()
    {
        $produk = session('checkout_produk');
        $total = session('checkout_total');

        if (!$produk) {
            return redirect()->route('keranjang.index')->with('error', 'Produk tidak tersedia.');
        }

        return view('pembeli.checkout.form', compact('produk', 'total'));
    }

    public function process(Request $request)
    {
        // Ambil data produk dari sesi checkout
        $produk = session('checkout_produk');
        $langsung = session('checkout_langsung', false);

        // Jika tidak ada data produk di sesi
        if (!$produk) {
            return redirect()->route('keranjang.index')->with('error', 'Data produk tidak tersedia.');
        }

        // Validasi form checkout
        $request->validate([
            'nama'    => 'required|string|max:100',
            'alamat'  => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
            'metode'  => 'required|in:midtrans,cod,dana',
        ]);

        // Buat ID order unik
        $orderId = 'ORDER-' . strtoupper(uniqid());

        // Hitung total harga seluruh produk
        $total = collect($produk)->sum(function ($item) {
            return $item['harga'] * $item['jumlah'];
        });

        // Simpan ke database transaksi
        $transaksi = Transaksi::create([
            'user_id'         => Auth::id(),
            'order_id'        => $orderId,
            'nama'            => $request->nama,
            'alamat'          => $request->alamat,
            'telepon'         => $request->telepon,
            'tanggal_pesanan' => now()->toDateString(),
            'total'           => $total,
            'status'          => 'pending',
            'metode'          => $request->metode,
        ]);

        // Hapus produk dari session keranjang setelah checkout
        $keranjang = session('keranjang', []);
        foreach ($produk as $item) {
            unset($keranjang[$item['id']]);
        }
        session(['keranjang' => $keranjang]);

        // Jika metode Midtrans, lanjut ke Snap
        if ($request->metode === 'midtrans') {
            Config::$serverKey    = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized  = true;
            Config::$is3ds        = true;

            $itemDetails = [];
            foreach ($produk as $item) {
                $itemDetails[] = [
                    'id'       => $item['id'],
                    'price'    => $item['harga'],
                    'quantity' => $item['jumlah'],
                    'name'     => $item['nama'],
                ];
            }

            $params = [
                'transaction_details' => [
                    'order_id'     => $orderId,
                    'gross_amount' => $total,
                ],
                'customer_details' => [
                    'first_name' => $request->nama,
                    'phone'      => $request->telepon,
                    'address'    => $request->alamat,
                ],
                'item_details' => $itemDetails,
            ];

            $snapToken = Snap::getSnapToken($params);

            // Ambil ID produk jika hanya satu item dan dari langsung beli
            $produkId = null;
            if ($langsung && count($produk) === 1) {
                $produkId = $produk[0]['id'];
            }

            // Hapus session checkout
            session()->forget(['checkout_produk', 'checkout_langsung', 'checkout_total']);

            return view('pembeli.checkout.snap', [
                'snapToken' => $snapToken,
                'produkId'  => $produkId,
                'source'    => $langsung ? 'detail' : 'keranjang',
            ]);
        }

        // Jika bukan Midtrans, langsung redirect selesai
        session()->forget(['checkout_produk', 'checkout_langsung', 'checkout_total']);

        return redirect()->route('pembeli.produk.index')->with('success', 'Pesanan berhasil dilakukan.');
    }

    public function callback(Request $request)
    {
        $data = json_decode($request->getContent());
        $transaksi = Transaksi::where('order_id', $data->order_id)->first();

        if ($transaksi) {
            $transaksi->status = $data->transaction_status;
            $transaksi->save();
        }

        return response()->json(['status' => 'ok']);
    }
}
