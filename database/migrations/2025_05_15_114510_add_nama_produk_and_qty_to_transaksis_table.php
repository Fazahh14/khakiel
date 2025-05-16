<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('transaksis', function (Blueprint $table) {
        $table->string('nama_produk')->nullable()->after('tanggal_pesanan');
        $table->integer('qty')->default(1)->after('nama_produk');
    });
}

public function down(): void
{
    Schema::table('transaksis', function (Blueprint $table) {
        $table->dropColumn(['nama_produk', 'qty']);
    });
}
};