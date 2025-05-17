<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class KelolaStatusPesananController extends Controller
{
    public function index()
    {
        // Cek apakah kolom status_pembayaran tersedia dulu
        if (Schema::hasColumn('transaksis', 'status_pembayaran')) {
            // Update otomatis jika sudah bayar
            Transaksi::where('status_pembayaran', 'sudah bayar')
                ->where('status', '!=', 'sedang diproses') // biar gak overwrite yg udah diproses
                ->update(['status' => 'sedang diproses']);
        }

        $transaksis = Transaksi::with('items.produk')->orderBy('id', 'asc')->paginate(25);
        return view('admin.statuspesanan.index', compact('transaksis'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,belum diproses,sedang diproses,selesai'
        ]);

        $transaksi = Transaksi::findOrFail($id);

        $newStatus = $request->status;

        // Cek jika status_pembayaran == 'sudah bayar' maka ubah otomatis
        if (Schema::hasColumn('transaksis', 'status_pembayaran') &&
            $transaksi->status_pembayaran === 'sudah bayar') {
            $newStatus = 'sedang diproses';
        }

        $transaksi->update([
            'status' => $newStatus
        ]);

        return redirect()->route('admin.kelolastatuspesanan.index')
                         ->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function kirimwa($id)
    {
        $transaksi = Transaksi::with('items.produk')->findOrFail($id);

        if (!$transaksi->telepon) {
            return back()->with('error', 'Nomor telepon pelanggan tidak tersedia.');
        }

        $no = preg_replace('/[^0-9]/', '', $transaksi->telepon);
        if (substr($no, 0, 1) == '0') {
            $no = '62' . substr($no, 1);
        }
$pesan = "🎉 Hai {$transaksi->nama}, Sahabat setia Khakiel!\n";
$pesan .= "Kami baru saja mencatat bahwa kamu tergoda buat checkout 😄\n\n";
$pesan .= "📦 Detail pesanan kamu:\n";
foreach ($transaksi->items as $item) {
    $pesan .= "• {$item->produk->nama} (qty: {$item->qty})\n";
}
$pesan .= "\n💰 Total belanja: Rp " . number_format($transaksi->total, 0, ',', '.') . "\n";
$pesan .= "📌 Status: {$transaksi->status}\n\n";
$pesan .= "Terima kasih sudah belanja di Khakiel. Kamu memang top! 🚀\n";
$pesan .= "Kalau ada yang mau ditanyain, tim kami siap bantu kapan pun! 🤝";

        try {
            $response = Http::withHeaders([
                'Authorization' => env('FONNTE_API_KEY', 'suizRXTkDa7FMcqYPjkL')
            ])->asForm()->post(env('FONNTE_API_URL') . '/send', [
                'target' => $no,
                'message' => $pesan,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                return back()->with('success', 'Pesan WhatsApp berhasil dikirim.');
            } else {
                return back()->with('error', 'Gagal mengirim pesan WhatsApp: ' . $response->body());
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->delete();

        return redirect()->route('admin.kelolastatuspesanan.index')
                         ->with('success', 'Pesanan berhasil dihapus.');
    }
}
