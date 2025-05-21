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
    $keranjang = Keranjang::where('user_id', Auth::id())->get();

    $total = $keranjang->sum(function ($item) {
        return $item->harga * $item->jumlah;
    });

    return view('pembeli.keranjangBelanja.index', compact('keranjang', 'total'));
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

    public function tambah($id)
{
    $item = Keranjang::where('user_id', Auth::id())->where('id', $id)->first();
    if ($item) {
        $item->jumlah++;
        $item->save();
    }
    return redirect()->route('keranjang.index');
}

public function kurang($id)
{
    $item = Keranjang::where('user_id', Auth::id())->where('id', $id)->first();
    if ($item) {
        $item->jumlah--;
        if ($item->jumlah <= 0) {
            $item->delete();
        } else {
            $item->save();
        }
    }
    return redirect()->route('keranjang.index');
}


    public function hapus($id)
{
    Keranjang::where('user_id', Auth::id())->where('id', $id)->delete();
    return redirect()->route('keranjang.index');
}

    public function update(Request $request)
    {
        $checked = $request->input('checked_items', []);
        $keranjang = session('keranjang', []);
        $total = 0;

        foreach ($checked as $id) {
            if (isset($keranjang[$id])) {
                $total += $keranjang[$id]['harga'] * $keranjang[$id]['jumlah'];
            }
        }

        return back()->with('success', 'Checkout berhasil. Total: Rp ' . number_format($total, 0, ',', '.'));
    }

    public function hapusAjax(Request $request)
    {
        $id = $request->input('id');
        $keranjang = session('keranjang', []);
        if (isset($keranjang[$id])) {
            unset($keranjang[$id]);
            session()->put('keranjang', $keranjang);
        }
        return response()->json(['success' => true]);
    }
}
