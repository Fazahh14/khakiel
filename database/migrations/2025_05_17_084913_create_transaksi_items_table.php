<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiItemsTable extends Migration
{
    public function up()
    {
        Schema::create('transaksi_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksis')->onDelete('cascade');
            $table->unsignedBigInteger('produk_id')->nullable(); // bisa null kalau produk dihapus, tapi tetap simpan nama produk
            $table->string('nama_produk');
            $table->integer('qty')->unsigned();
            $table->bigInteger('harga')->unsigned(); // harga per item
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi_items');
    }
}