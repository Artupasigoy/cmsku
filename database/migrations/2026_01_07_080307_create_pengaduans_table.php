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
        Schema::create('pengaduans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tiket')->unique();
            $table->string('nama_pelapor');
            $table->string('email');
            $table->string('telepon')->nullable();
            $table->text('alamat')->nullable();
            $table->string('kategori')->nullable();
            $table->string('judul');
            $table->longText('isi_pengaduan');
            $table->string('lokasi_kejadian')->nullable();
            $table->date('tanggal_kejadian')->nullable();
            $table->json('lampiran')->nullable();
            $table->enum('status', ['baru', 'diproses', 'ditanggapi', 'selesai', 'ditolak'])->default('baru');
            $table->text('tanggapan')->nullable();
            $table->timestamp('tanggal_tanggapan')->nullable();
            $table->boolean('is_anonymous')->default(false);

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
        Schema::dropIfExists('pengaduans');
    }
};
