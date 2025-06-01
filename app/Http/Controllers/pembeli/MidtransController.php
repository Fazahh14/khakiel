<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Produk;
use App\Models\StatusPesanan;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        try {
            $notif = new Notification();


            $status = $notif->transaction_status;
            $fraudStatus = $notif->fraud_status ?? null;
            $orderId = $notif->order_id;
            $orderId     = $notif->order_id;
            $status      = $notif->transaction_status;
            $fraudStatus = $notif->fraud_status ?? null;

            $transaksi = Transaksi::where('order_id', $orderId)->first();

            if (!$transaksi) {
                Log::warning("Transaksi dengan order_id {$orderId} tidak ditemukan.");
                return response()->json(['status' => 'not found'], 404);
            }
            if ($status === 'capture' || $status === 'settlement') {
                if ($fraudStatus === 'challenge') {
                    $transaksi->status_pembayaran = 'challenge';
                } else {
                    $transaksi->status_pembayaran = 'sudah bayar';
                    $transaksi->status = 'sedang diproses'; // Update status pesanan otomatis
                }
            } elseif (in_array($status, ['deny', 'expire', 'cancel'])) {
                $transaksi->status_pembayaran = 'gagal';
            // Update status berdasarkan status dari Midtrans
            if ($status === 'capture' && $fraudStatus === 'accept') {
                $transaksi->status_pembayaran = 'sudah bayar';
                $transaksi->status = 'sedang diproses';
            } elseif ($status === 'settlement') {
                $transaksi->status_pembayaran = 'sudah bayar';
                $transaksi->status = 'sedang diproses';
            } elseif ($status === 'pending') {
                $transaksi->status_pembayaran = 'pending';
                $transaksi->status = 'pending';
            } elseif (in_array($status, ['deny', 'expire', 'cancel'])) {
                $transaksi->status_pembayaran = 'gagal';
                $transaksi->status = 'dibatalkan';
            }

            $transaksi->save();

            // Kurangi stok jika transaksi sukses
            if (in_array($transaksi->status_pembayaran, ['sudah bayar'])) {
                foreach ($transaksi->items as $item) {
                    $produkModel = Produk::find($item->produk_id);
                    if ($produkModel) {
                        $produkModel->stok = max(0, $produkModel->stok - $item->qty);
                        $produkModel->save();
                    }
                }
            }

            // Simpan atau update ke tabel status_pesanan
            StatusPesanan::updateOrCreate(
                ['transaksi_id' => $transaksi->id],
                ['status_pesanan' => $transaksi->status]
            );

            return response()->json(['message' => 'Notifikasi diproses']);
        } catch (\Exception $e) {
            Log::error('Midtrans Callback Error: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan'], 500);
        }
    }
}
}