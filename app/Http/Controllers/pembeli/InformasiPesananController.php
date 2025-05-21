<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;

class InformasiPesananController extends Controller
{
    public function index()
    {
        // Ambil transaksi yang hanya milik user yang sedang login
        $pesanans = Transaksi::where('user_id', Auth::id())
                            ->orderBy('created_at', 'asc')
                            ->paginate(10);

        return view('pembeli.informasipesanan.index', compact('pesanans'));
    }
}
