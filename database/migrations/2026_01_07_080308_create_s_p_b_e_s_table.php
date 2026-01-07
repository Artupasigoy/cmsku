<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('s_p_b_e_s', function (Blueprint $table) {
            $table->id();
            $table->string('nama_aplikasi');
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();
            $table->string('url')->nullable();
            $table->string('domain')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('kategori')->nullable();
            $table->string('opd_pengelola')->nullable();
            $table->integer('tahun_operasional')->nullable();
            $table->enum('status', ['aktif', 'maintenance', 'discontinued'])->default('aktif');
            $table->integer('order')->default(0);
            $table->boolean('is_featured')->default(false);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('s_p_b_e_s');
    }
};
