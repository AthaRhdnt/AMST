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
        Schema::table('detail_pembelian', function (Blueprint $table) {
            // Drop the existing foreign key and column
            $table->dropForeign(['id_pembelian']);
            $table->dropColumn('id_pembelian');

            // Add the new column and foreign key
            $table->unsignedBigInteger('id_transaksi')->after('id_detail_pembelian');
            $table->foreign('id_transaksi')->references('id_transaksi')->on('transaksi')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_pembelian', function (Blueprint $table) {
            // Drop the new foreign key and column
            $table->dropForeign(['id_transaksi']);
            $table->dropColumn('id_transaksi');

            // Recreate the old column and foreign key
            $table->unsignedBigInteger('id_pembelian')->after('id_detail_pembelian');
            $table->foreign('id_pembelian')->references('id_pembelian')->on('pembelian')->onDelete('cascade')->onUpdate('cascade');
        });
    }
};
