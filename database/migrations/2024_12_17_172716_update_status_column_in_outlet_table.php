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
        Schema::table('outlet', function (Blueprint $table) {
            Schema::table('outlet', function (Blueprint $table) {
                $table->dropColumn('status');
            });
    
            Schema::table('outlet', function (Blueprint $table) {
                $table->string('status')->nullable()->default('active')->after('alamat_outlet');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outlet', function (Blueprint $table) {
            Schema::table('outlet', function (Blueprint $table) {
                $table->dropColumn('status');
            });

            Schema::table('outlet', function (Blueprint $table) {
                $table->string('status')->after('alamat_outlet')->nullable();
            });
        });
    }
};
