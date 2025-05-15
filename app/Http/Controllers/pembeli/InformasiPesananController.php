<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;

class InformasiPesananController extends Controller
{
    public function index()
    {
        // Sementara tampilkan semua transaksi tanpa filter user
        $pesanans = Transaksi::orderBy('created_at', 'desc')->paginate(10);

        return view('pembeli.informasipesanan.index', compact('pesanans'));
    }
}
