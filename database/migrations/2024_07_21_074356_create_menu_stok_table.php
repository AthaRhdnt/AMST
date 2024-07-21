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
        Schema::create('menu_stok', function (Blueprint $table) {
            $table->unsignedBigInteger('id_menu');
            $table->unsignedBigInteger('id_barang');
            $table->integer('jumlah');
            $table->timestamps();

            $table->foreign('id_menu')->references('id_menu')->on('menu')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_barang')->references('id_barang')->on('stok')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_stok');
    }
};
