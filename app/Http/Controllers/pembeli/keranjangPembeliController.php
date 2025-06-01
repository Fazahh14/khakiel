<?php
namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Keranjang;
use Illuminate\Support\Facades\Auth;

class KeranjangPembeliController extends Controller
{
    public function index()
    {
        $keranjang = Keranjang::with('produk')->where('user_id', Auth::id())->get();

        // Hapus keranjang yang produknya sudah tidak ada (null)
        foreach ($keranjang as $item) {
            if ($item->produk === null) {
                $item->delete();
            }
        }

        // Ambil ulang data setelah hapus produk null
        $keranjang = Keranjang::with('produk')->where('user_id', Auth::id())->get();

        return view('pembeli.keranjangBelanja.index', compact('keranjang'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer',
            'nama' => 'required|string',
            'harga' => 'required|numeric',
            'jumlah' => 'required|integer|min:1',
            'gambar' => 'nullable|string',
        ]);

        $keranjang = Keranjang::where('user_id', Auth::id())
            ->where('produk_id', $validated['id'])
            ->first();

        if ($keranjang) {
            $keranjang->jumlah += $validated['jumlah'];
            $keranjang->save();
        } else {
            Keranjang::create([
                'user_id' => Auth::id(),
                'produk_id' => $validated['id'],
                'nama' => $validated['nama'],
                'harga' => $validated['harga'],
                'jumlah' => $validated['jumlah'],
                'gambar' => $validated['gambar'],
            ]);
        }

        return redirect()->route('keranjang.index')->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function hapusAjax(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $keranjang = Keranjang::where('user_id', Auth::id())
            ->where('id', $request->id)
            ->first();

        if ($keranjang) {
            $keranjang->delete();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus dari keranjang.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Produk tidak ditemukan.'
        ]);
    }
}
