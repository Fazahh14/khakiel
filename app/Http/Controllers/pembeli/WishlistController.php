<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;

class WishlistController extends Controller
{
    /**
     * Menampilkan daftar produk yang disukai.
     */
    public function index()
    {
        $wishlist = session('wishlist', []);
        return view('pembeli.wishlist.index', compact('wishlist'));
    }

    /**
     * Menambahkan produk ke daftar kesukaan.
     */
    public function tambah(Request $request)
    {
        $produk = Produk::findOrFail($request->produk_id);
        $wishlist = session('wishlist', []);

        // Cegah duplikat
        if (!in_array($produk->id, array_column($wishlist, 'id'))) {
            $wishlist[] = [
                'id'     => $produk->id,
                'nama'   => $produk->nama,
                'gambar' => $produk->gambar,
                'harga'  => $produk->harga,
                'stok'   => $produk->stok, // ✅ Menyimpan stok produk
            ];
        }

        session(['wishlist' => $wishlist]);

        return back()->with('success', 'Produk ditambahkan ke daftar kesukaan.');
    }

    /**
     * Menghapus produk dari daftar kesukaan.
     */
    public function hapus($id)
    {
        $wishlist = session('wishlist', []);

        // Filter produk yang tidak memiliki id yang dihapus
        $wishlist = array_filter($wishlist, function ($item) use ($id) {
            return $item['id'] != $id;
        });

        session(['wishlist' => $wishlist]);

        return back()->with('success', 'Produk dihapus dari kesukaan.');
    }
}
