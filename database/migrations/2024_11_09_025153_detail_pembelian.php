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
        Schema::create('detail_pembelian', function (Blueprint $table) {
            $table->id('id_detail_pembelian');
            $table->unsignedBigInteger('id_pembelian');
            $table->unsignedBigInteger('id_barang');
            $table->integer('jumlah');
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();

            $table->foreign('id_pembelian')->references('id_pembelian')->on('pembelian')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_barang')->references('id_barang')->on('stok')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pembelian');
    }
};
