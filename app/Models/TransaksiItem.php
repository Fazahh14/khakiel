<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiItem extends Model
{
    protected $fillable = ['transaksi_id', 'produk_id', 'jumlah', 'harga'];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }
}
