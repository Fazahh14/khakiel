<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Log;
use Midtrans\Notification;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        try {
            $notif = new Notification();

            $status = $notif->transaction_status;
            $orderId = $notif->order_id;

            $transaksi = Transaksi::where('order_id', $orderId)->first();

            if (!$transaksi) {
                Log::warning("Transaksi dengan order_id {$orderId} tidak ditemukan.");
                return response()->json(['status' => 'not found'], 404);
            }

           if ($status === 'settlement' || $status === 'capture') {
    $transaksi->status_pembayaran = 'sudah bayar';
    $transaksi->save();

    // Panggil fungsi untuk update status otomatis
    $transaksi->updateStatusOtomatis();
}

            $transaksi->save();

            return response()->json(['message' => 'Notifikasi berhasil diproses']);
        } catch (\Exception $e) {
            Log::error('Midtrans Callback Error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memproses callback'], 500);
        }
    }
}
