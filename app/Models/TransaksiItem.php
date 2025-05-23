<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiItem extends Model
{
    protected $table = 'transaksi_items'; // pastikan nama tabel sesuai

    protected $fillable = [
        'transaksi_id',
        'produk_id',
        'nama_produk',
        'qty',
        'harga',
    ];

    // Relasi ke produk (asumsi kamu punya model Produk)
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    // Relasi ke transaksi induk
    public function transaksi()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
