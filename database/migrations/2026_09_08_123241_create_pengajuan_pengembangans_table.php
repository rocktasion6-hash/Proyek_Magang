<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_pengembangans', function (Blueprint $table) {
            $table->id();

            // Karyawan yang mengajukan / menjadi objek pengembangan
            $table->foreignId('karyawan_id')
                ->constrained('karyawans')
                ->cascadeOnDelete();

            // Hasil ujian yang menjadi dasar pengajuan
            $table->foreignId('hasil_assessment_id')
                ->constrained('hasil_assessments')
                ->restrictOnDelete();

            // Jenis proses pengembangan
            $table->enum('jenis_pengajuan', [
                'kenaikan_jabatan',
                'pemindahan_jabatan',
                'peningkatan_skill'
            ]);

            // Jabatan saat ini
            $table->foreignId('jabatan_asal_id')
                ->nullable()
                ->constrained('jabatans')
                ->nullOnDelete();

            // Jabatan tujuan untuk kenaikan/pemindahan
            $table->foreignId('jabatan_tujuan_id')
                ->nullable()
                ->constrained('jabatans')
                ->nullOnDelete();

            // Skill yang ditingkatkan
            $table->foreignId('skill_id')
                ->nullable()
                ->constrained('skills')
                ->nullOnDelete();

            // Status proses HRD
            $table->enum('status', [
                'diajukan',
                'diproses',
                'disetujui',
                'ditolak'
            ])->default('diajukan');

            $table->date('tanggal_pengajuan');

            $table->date('tanggal_keputusan')
                ->nullable();

            $table->text('catatan')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_pengembangans');
    }
};