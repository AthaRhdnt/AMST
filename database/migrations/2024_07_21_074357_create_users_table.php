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
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_user'); // Custom primary key
            $table->string('nama_user'); // Custom name field
            $table->unsignedBigInteger('id_role'); // Foreign key for roles
            $table->string('username')->unique(); // Custom username field
            $table->string('password'); // Password field
            $table->rememberToken(); // Remember token for sessions
            $table->timestamps(); // Timestamps

            // Foreign key constraint
            $table->foreign('id_role')->references('id_role')->on('roles')->onDelete('cascade')->onUpdate('cascade');
        });

        // Password reset tokens table
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('username')->primary(); // Use username instead of email
            $table->string('token'); // Reset token
            $table->timestamp('created_at')->nullable(); // Token creation time
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens'); // Keep this if using password reset
        Schema::dropIfExists('users');
    }
};
