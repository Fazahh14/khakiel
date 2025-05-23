<?php

namespace App\Http\Controllers\Admin\Produk;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AksesorisController extends Controller
{
    public function index()
    {
        $produk = Produk::where('kategori', 'aksesoris')->get();
        return view('admin.produk.aksesoris.index', compact('produk'));
    }

    public function create()
    {
        return view('admin.produk.aksesoris.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'stok' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:1',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048', // max 2MB
        ]);

        $data['kategori'] = 'aksesoris';

        if ($request->hasFile('gambar')) {
            // Simpan gambar di storage/app/public/produk
            $data['gambar'] = $request->file('gambar')->store('produk', 'public');
        }

        Produk::create($data);

        return redirect()->route('admin.aksesoris.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit($id)
    {
        $produk = Produk::findOrFail($id);
        return view('admin.produk.aksesoris.edit', compact('produk'));
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'stok' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:1',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
        ]);

        $data['kategori'] = 'aksesoris';

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($produk->gambar && Storage::disk('public')->exists($produk->gambar)) {
                Storage::disk('public')->delete($produk->gambar);
            }

            // Simpan gambar baru
            $data['gambar'] = $request->file('gambar')->store('produk', 'public');
        }

        $produk->update($data);

        return redirect()->route('admin.aksesoris.index')->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);

        // Hapus gambar fisik jika ada
        if ($produk->gambar && Storage::disk('public')->exists($produk->gambar)) {
            Storage::disk('public')->delete($produk->gambar);
        }

        $produk->delete();

        return redirect()->route('admin.aksesoris.index')->with('success', 'Produk berhasil dihapus');
    }
}
