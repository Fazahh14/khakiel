<?php

namespace App\Http\Controllers\Admin\Produk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;

class VitaminKucingController extends Controller
{
    public function index()
    {
        $produk = Produk::where('kategori', 'vitamin-kucing')->get();
        return view('admin.produk.vitamin-kucing.index', compact('produk'));
    }

    public function create()
    {
        return view('admin.produk.vitamin-kucing.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required',
            'deskripsi' => 'nullable',
            'stok' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:1',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048', // max 2MB
        ]);

        $data['kategori'] = 'vitamin-kucing';

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('produk', 'public');
        }

        Produk::create($data);
        return redirect()->route('admin.vitamin-kucing.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit($id)
    {
        $produk = Produk::findOrFail($id);
        return view('admin.produk.vitamin-kucing.edit', compact('produk'));
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        $data = $request->validate([
            'nama' => 'required',
            'deskripsi' => 'nullable',
            'stok' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:1',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048', // max 2MB
        ]);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('produk', 'public');
        }

        $produk->update($data);
        return redirect()->route('admin.vitamin-kucing.index')->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy($id)
    {
        Produk::findOrFail($id)->delete();
        return redirect()->route('admin.vitamin-kucing.index')->with('success', 'Produk berhasil dihapus');
    }
}
