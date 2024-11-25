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
        Schema::table('riwayat_stok', function (Blueprint $table) {
            $table->string('stok_awal')->after('id_barang')->nullable();
            $table->string('stok_akhir')->after('jumlah_pakai')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_stok', function (Blueprint $table) {
            $table->dropColumn('stok_awal');
            $table->dropColumn('stok_akhir');
            
        });
    }
};
