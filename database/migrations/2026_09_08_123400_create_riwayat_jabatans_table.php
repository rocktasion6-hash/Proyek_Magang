<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_jabatans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('karyawan_id')
                ->constrained('karyawans')
                ->cascadeOnDelete();

            $table->foreignId('jabatan_id')
                ->constrained('jabatans')
                ->restrictOnDelete();

            // Jenis perubahan jabatan
            $table->enum('jenis_perubahan', [
                'awal',
                'kenaikan',
                'pemindahan'
            ]);

            // Pengajuan yang menjadi dasar perubahan
            $table->foreignId('pengajuan_pengembangan_id')
                ->nullable()
                ->constrained('pengajuan_pengembangans')
                ->nullOnDelete();

            // Hasil ujian yang menjadi dasar
            $table->foreignId('hasil_assessment_id')
                ->nullable()
                ->constrained('hasil_assessments')
                ->nullOnDelete();

            $table->date('tanggal_mulai');

            $table->date('tanggal_selesai')
                ->nullable();

            $table->text('keterangan')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_jabatans');
    }
};