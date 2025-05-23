<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Keranjang;
use Illuminate\Support\Facades\Auth;
use App\Models\Produk;


class KeranjangPembeliController extends Controller
{
   
    public function index()
    {
        $keranjang = Keranjang::with('produk')->where('user_id', Auth::id())->get();

        foreach ($keranjang as $item) {
            if ($item->produk === null) {
                $item->delete();
            }
        }

        $keranjang = $keranjang->filter(function ($item) {
            return $item->produk !== null;
        });

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
        $checked_items = session('checked_items', []);
        $total = session('total');

        foreach ($checked as $id) {
            if (isset($keranjang[$id])) {
                $total += $keranjang[$id]['harga'] * $keranjang[$id]['jumlah'];
            }
        }

            return redirect()->route('form.pemesanan')->with([
        'checked_items' => $checked,
        'total' => $total,
    ]);

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
