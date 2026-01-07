<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk tabel users, password_reset_tokens, dan sessions
 * Mendukung fitur 2FA, soft delete, dan ownership tracking
 */
return new class extends Migration {
    /**
     * Jalankan migration
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Kolom tambahan untuk ASN
            $table->string('nip')->nullable()->comment('NIP ASN');
            $table->string('jabatan')->nullable()->comment('Jabatan/Posisi');
            $table->string('bidang')->nullable()->comment('Bidang/Unit Kerja');

            // Status dan keamanan
            $table->boolean('is_active')->default(true)->comment('Status aktif user');
            $table->boolean('two_factor_enabled')->default(false)->comment('Status 2FA');
            $table->text('two_factor_secret')->nullable()->comment('Secret key 2FA');

            // Tracking login
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();

            // Ownership tracking
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();

            // Index untuk performa
            $table->index('is_active');
            $table->index('nip');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
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
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
