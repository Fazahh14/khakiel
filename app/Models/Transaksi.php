<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Transaksi extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'nama',
        'alamat',
        'telepon',
        'tanggal_pesanan',
        'total',
        'status',
        'metode',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke item transaksi jika kamu ingin menambahkan banyak produk
    public function items()
    {
        return $this->hasMany(TransaksiItem::class);
    }
}
