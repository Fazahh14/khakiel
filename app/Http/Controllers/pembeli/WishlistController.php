<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
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

    public function tambah(Request $request)
    {
        $request->validate(['produk_id' => 'required|exists:produk,id']);

        $user = Auth::user();

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

    public function hapus($produk_id)
    {
        $user = Auth::user();

        Wishlist::where('user_id', $user->id)
            ->where('produk_id', $produk_id)
            ->delete();

        return back()->with('success', 'Produk dihapus dari kesukaan.');
    }

    public function count()
    {
        $count = Auth::check() ? Auth::user()->wishlist()->count() : 0;
        return response()->json(['count' => $count]);
    }
}
