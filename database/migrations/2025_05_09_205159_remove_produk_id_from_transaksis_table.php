<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            // Hapus foreign key dulu (jika ada)
            $table->dropForeign(['produk_id']);

            // Lalu hapus kolomnya
            $table->dropColumn('produk_id');
        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            // Restore kolom produk_id (jika rollback)
            $table->unsignedBigInteger('produk_id')->nullable();

            // Tambahkan kembali foreign key-nya
            $table->foreign('produk_id')->references('id')->on('produk')->onDelete('cascade');
        });
    }
};
