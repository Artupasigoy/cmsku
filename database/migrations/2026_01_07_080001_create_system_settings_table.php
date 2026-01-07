<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk tabel system_settings
 * Menyimpan konfigurasi website yang bisa diubah tanpa developer
 */
return new class extends Migration {
    /**
     * Jalankan migration
     */
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Identifier unik untuk setting');
            $table->text('value')->nullable()->comment('Nilai setting');
            $table->string('type')->default('string')->comment('Tipe data: string, boolean, integer, json, file');
            $table->string('group')->default('general')->comment('Grup setting: general, seo, mail, feature');
            $table->text('description')->nullable()->comment('Deskripsi setting');
            $table->boolean('is_public')->default(false)->comment('Apakah setting bisa diakses publik');
            $table->timestamps();

            $table->index('group');
            $table->index('is_public');
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
