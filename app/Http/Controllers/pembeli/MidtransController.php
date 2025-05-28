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
            // Membuat instance notification dari Midtrans
            $notif = new Notification();

            // Ambil status transaksi dan fraud status
            $status = $notif->transaction_status;
            $fraudStatus = $notif->fraud_status ?? null;
            $orderId = $notif->order_id;

            // Cari transaksi berdasarkan order_id
            $transaksi = Transaksi::where('order_id', $orderId)->first();

            if (!$transaksi) {
                Log::warning("Transaksi dengan order_id {$orderId} tidak ditemukan.");
                return response()->json(['status' => 'not found'], 404);
            }

            // Update status berdasarkan transaction_status dan fraud_status dari Midtrans
            if ($status === 'capture') {
                if ($fraudStatus === 'challenge') {
                    // Pembayaran masih di challenge oleh Midtrans (manual review)
                    $transaksi->status_pembayaran = 'challenge';
                } else {
                    // Pembayaran sukses
                    $transaksi->status_pembayaran = 'sudah bayar';
                    $transaksi->updateStatusOtomatis();
                }
            } elseif ($status === 'settlement') {
                // Pembayaran berhasil settled
                $transaksi->status_pembayaran = 'sudah bayar';
                $transaksi->updateStatusOtomatis();
            } elseif ($status === 'pending') {
                // Pembayaran masih pending menunggu user membayar
                $transaksi->status_pembayaran = 'pending';
            } elseif (in_array($status, ['deny', 'expire', 'cancel'])) {
                // Pembayaran gagal, dibatalkan atau expired
                $transaksi->status_pembayaran = 'gagal';
            }

            // Simpan perubahan
            $transaksi->save();

            return response()->json(['message' => 'Notifikasi berhasil diproses']);
        } catch (\Exception $e) {
            Log::error('Midtrans Callback Error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memproses callback'], 500);
        }
    }
}
