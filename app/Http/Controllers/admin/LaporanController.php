<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        // Logika menampilkan halaman laporan
        return view('admin.laporan.index'); // Pastikan file view ini ada
    }
}
