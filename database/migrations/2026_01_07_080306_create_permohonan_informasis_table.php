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
        Schema::create('permohonan_informasis', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_registrasi')->unique();
            $table->string('nama_pemohon');
            $table->string('nik')->nullable();
            $table->string('email');
            $table->string('telepon')->nullable();
            $table->text('alamat')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->text('rincian_informasi');
            $table->text('tujuan_penggunaan')->nullable();
            $table->string('cara_memperoleh')->default('email');
            $table->string('cara_mendapat_salinan')->default('softcopy');
            $table->enum('status', ['diterima', 'diproses', 'ditolak', 'selesai'])->default('diterima');
            $table->timestamp('tanggal_permohonan')->nullable();
            $table->timestamp('tanggal_respon')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->string('file_dokumen')->nullable();

            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_informasis');
    }
};
