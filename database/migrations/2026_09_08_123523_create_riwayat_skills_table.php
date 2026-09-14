<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_skills', function (Blueprint $table) {
            $table->id();

            $table->foreignId('karyawan_id')
                ->constrained('karyawans')
                ->cascadeOnDelete();

            $table->foreignId('skill_id')
                ->constrained('skills')
                ->restrictOnDelete();

            // Level skill sebelum peningkatan
            $table->unsignedTinyInteger('level_sebelum');

            // Level skill setelah peningkatan
            $table->unsignedTinyInteger('level_sesudah');

            // Pengajuan yang menjadi dasar perubahan
            $table->foreignId('pengajuan_pengembangan_id')
                ->nullable()
                ->constrained('pengajuan_pengembangans')
                ->nullOnDelete();

            // Hasil ujian yang menjadi dasar perubahan
            $table->foreignId('hasil_assessment_id')
                ->nullable()
                ->constrained('hasil_assessments')
                ->nullOnDelete();

            $table->date('tanggal_perubahan');

            $table->text('keterangan')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_skills');
    }
};