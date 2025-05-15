<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('produk_id');
            $table->string('order_id')->nullable();
            $table->string('nama')->nullable(); // bisa dipakai di kelola pesanan
            $table->text('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->date('tanggal_pesanan')->nullable();
            $table->integer('total')->nullable();
            $table->string('status')->default('pending'); // bisa diganti oleh admin
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('produk_id')->references('id')->on('produk')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
