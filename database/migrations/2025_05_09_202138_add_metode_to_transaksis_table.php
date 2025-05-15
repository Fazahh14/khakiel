<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom 'metode' ke tabel 'transaksis'.
     */
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->string('metode')->after('status')->nullable(); // ✅ tambahkan kolom metode setelah status
        });
    }

    /**
     * Hapus kolom 'metode' saat rollback.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn('metode'); // ✅ hapus kolom metode
        });
    }
};
