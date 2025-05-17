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
        'status_pembayaran',
        'metode',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(TransaksiItem::class);
    }
}
