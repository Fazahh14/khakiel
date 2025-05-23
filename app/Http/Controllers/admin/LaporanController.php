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
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $rekap = null;
        $grafikLabels = [];
        $grafikData = [];

        if ($startDate && $endDate) {
            $rekap = $this->rekapPerRentangTanggal($startDate, $endDate, $grafikLabels, $grafikData);
        }

        return view('admin.laporan.index', compact(
            'rekap', 'grafikLabels', 'grafikData', 'startDate', 'endDate'
        ));
    }

    private function rekapPerRentangTanggal($startDate, $endDate, &$grafikLabels, &$grafikData)
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $transaksis = Transaksi::with('items.produk')
            ->whereBetween('tanggal_pesanan', [$start, $end])
            ->whereIn('status', ['sedang diproses', 'selesai'])
            ->orderBy('tanggal_pesanan', 'asc')
            ->get();

        $rekap = $this->hitungRekap($transaksis);

        $perHari = [];
        $period = new \DatePeriod($start, new \DateInterval('P1D'), $end->copy()->addDay());

        foreach ($period as $date) {
            $perHari[$date->format('Y-m-d')] = 0;
        }

        foreach ($transaksis as $transaksi) {
            $tgl = Carbon::parse($transaksi->tanggal_pesanan)->format('Y-m-d');
            foreach ($transaksi->items as $item) {
                $totalItem = $item->total ?? ($item->qty * ($item->produk->harga ?? 0));
                $perHari[$tgl] += $totalItem;
            }
        }

        $grafikLabels = array_map(fn($d) => Carbon::parse($d)->format('d M'), array_keys($perHari));
        $grafikData = array_values($perHari);

        return $rekap;
    }

    private function hitungRekap($transaksis)
    {
        $items = [];
        $totalPendapatan = 0;

        foreach ($transaksis as $transaksi) {
            foreach ($transaksi->items as $item) {
                $totalItem = $item->total ?? ($item->qty * ($item->produk->harga ?? 0));
                $items[] = [
                    'tanggal' => Carbon::parse($transaksi->tanggal_pesanan)->format('d-m-Y H:i'),
                    'nama_produk' => $item->produk->nama ?? '-',
                    'qty' => $item->qty,
                    'total' => $totalItem,
                ];
                $totalPendapatan += $totalItem;
            }
        }

        return [
            'items' => $items,
            'total_harga' => $totalPendapatan,
        ];
    }
}
