<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
{
    $tanggal = $request->input('tanggal');
    $rekap = null;

    if ($tanggal) {
        $tanggalCarbon = Carbon::parse($tanggal);

        $transaksis = Transaksi::with('items.produk')
            ->whereDate('created_at', $tanggalCarbon)
            ->get();

        if ($transaksis->isNotEmpty()) {
            $totalQty = 0;
            $totalPendapatan = 0;
            $items = [];

            foreach ($transaksis as $transaksi) {
                foreach ($transaksi->items as $item) {
                    $totalItem = $item->total ?? ($item->qty * ($item->produk->harga ?? 0));
                    $items[] = [
                        'tanggal' => $transaksi->created_at,
                        'nama_produk' => $item->produk->nama ?? '-',
                        'qty' => $item->qty,
                        'total' => $totalItem,
                    ];

                    $totalQty += $item->qty;
                    $totalPendapatan += $totalItem;
                }
            }

            $rekap = [
                'tanggal' => $tanggalCarbon,
                'jumlah_transaksi' => $transaksis->count(),
                'total_qty' => $totalQty,
                'total_harga' => $totalPendapatan,
                'items' => $items,
            ];
        }
    }

    return view('admin.laporan.index', compact('rekap'));
}
}
