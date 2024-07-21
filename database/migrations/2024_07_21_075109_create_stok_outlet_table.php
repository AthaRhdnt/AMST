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
        Schema::create('stok_outlet', function (Blueprint $table) {
            $table->unsignedBigInteger('id_outlet');
            $table->unsignedBigInteger('id_barang');
            $table->integer('jumlah');
            $table->timestamps();

            $table->foreign('id_outlet')->references('id_outlet')->on('outlet')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_barang')->references('id_barang')->on('stok')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_outlet');
    }
};
