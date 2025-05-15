<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;

class KelolaStatusPesananController extends Controller
{
    /**
     * Menampilkan semua status pesanan.
     */
    public function index()
    {
        $transaksis = Transaksi::orderBy('id', 'asc')->paginate(25);
        return view('admin.statuspesanan.index', compact('transaksis'));
    }

    /**
     * Memperbarui status pesanan.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,belum diproses,sedang diproses,selesai'
        ]);

        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update([
            'status' => $request->status
        ]);

        return redirect()->route('admin.kelolastatuspesanan.index')
                         ->with('success', 'Status pesanan berhasil diperbarui.');
    }

    /**
     * Menghapus pesanan.
     */
    public function destroy($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->delete();

        return redirect()->route('admin.kelolastatuspesanan.index')
                         ->with('success', 'Pesanan berhasil dihapus.');
    }
}
