<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk tabel login_histories
 * Mencatat semua percobaan login untuk audit dan keamanan
 */
return new class extends Migration {
    /**
     * Jalankan migration
     */
    public function up(): void
    {
        Schema::create('login_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('email')->nullable()->comment('Email yang digunakan untuk login');
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->string('location')->nullable()->comment('Lokasi berdasarkan IP');
            $table->boolean('success')->default(false)->comment('Status login berhasil/gagal');
            $table->string('failure_reason')->nullable()->comment('Alasan gagal login');
            $table->timestamp('attempted_at');

            // Index untuk reporting dan keamanan
            $table->index(['user_id', 'attempted_at']);
            $table->index(['ip_address', 'attempted_at']);
            $table->index('success');
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('login_histories');
    }
};
