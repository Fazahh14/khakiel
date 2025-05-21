<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;


class WishlistController extends Controller
{
    /**
     * Menampilkan daftar produk yang disukai.
     */
    public function index()
    {
        $wishlist = Wishlist::with('produk')
            ->where('user_id', Auth::id())
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->produk->id,
                    'nama' => $item->produk->nama,
                    'gambar' => $item->produk->gambar,
                    'harga' => $item->produk->harga,
                    'stok' => $item->produk->stok,
                ];
            });

        return view('pembeli.wishlist.index', compact('wishlist'));
    }

    /**
     * Menambahkan produk ke daftar kesukaan.
     */
    public function tambah(Request $request)
    {
        $request->validate(['produk_id' => 'required|exists:produk,id']);

        $user = Auth::user();

        // Cegah duplikat
        $existing = Wishlist::where('user_id', $user->id)
            ->where('produk_id', $request->produk_id)
            ->first();

        if (!$existing) {
            Wishlist::create([
                'user_id' => $user->id,
                'produk_id' => $request->produk_id
            ]);
        }

        return back()->with('success', 'Produk ditambahkan ke daftar kesukaan.');
    }

    /**
     * Menghapus produk dari daftar kesukaan.
     */
    public function hapus($id)
    {
        $user = Auth::user();

        Wishlist::where('user_id', $user->id)
            ->where('produk_id', $id)
            ->delete();

        return back()->with('success', 'Produk dihapus dari kesukaan.');
    }

}
