<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusPesanan extends Model
{
    protected $fillable = ['transaksi_id', 'status_pesanan'];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }
}
