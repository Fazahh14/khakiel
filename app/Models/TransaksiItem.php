<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiItem extends Model
{
    protected $table = 'transaksi_items'; // pastikan sesuai

    protected $fillable = [
        'transaksi_id',
        'produk_id',
        'nama_produk',
        'qty',
        'harga',
    ];

    // Relasi ke produk (opsional jika ada model Produk)
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    // Relasi ke transaksi induk (INI YANG BENAR)
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }
}
