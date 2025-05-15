<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Tampilkan daftar artikel dengan fitur pencarian dan artikel terbaru.
     */
    public function index(Request $request)
    {
        $query = Artikel::query();

        // Pencarian berdasarkan judul
        if ($request->has('search') && $request->search !== null) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        // Ambil data artikel utama (dengan pagination)
        $artikels = $query->latest()->paginate(6);

        // Ambil 5 artikel terbaru untuk sidebar
        $latestArtikels = Artikel::latest()->take(5)->get();

        return view('pembeli.blog.index', compact('artikels', 'latestArtikels'));
    }

    /**
     * Tampilkan detail satu artikel berdasarkan ID.
     */
    public function show($id)
    {
        // Ambil artikel berdasarkan ID
        $artikel = Artikel::findOrFail($id);

        // Ambil 5 artikel terbaru untuk sidebar
        $latestArtikels = Artikel::latest()->take(5)->get();

        return view('pembeli.blog.show', compact('artikel', 'latestArtikels'));
    }
}
