<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
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

    public function items()
    {
        return $this->hasMany(TransaksiItem::class);
    }

    public function updateStatusOtomatis()
    {
        if (
            Schema::hasColumn('transaksis', 'status_pembayaran') &&
            $this->status_pembayaran === 'sudah bayar' &&
            $this->status !== 'sedang diproses'
        ) {
            $this->status = 'sedang diproses';
            $this->save();
        }
    }
}
