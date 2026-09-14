<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_peserta', function (Blueprint $table) {
            $table->id();

            $table->foreignId('assessment_id')
                ->constrained('assessments')
                ->cascadeOnDelete();

            $table->foreignId('karyawan_id')
                ->constrained('karyawans')
                ->cascadeOnDelete();

            $table->enum('status', [
                'ditugaskan',
                'sedang_mengerjakan',
                'selesai',
                'dibatalkan'
            ])->default('ditugaskan');

            $table->dateTime('waktu_mulai')
                ->nullable();

            $table->dateTime('waktu_selesai')
                ->nullable();

            $table->timestamps();

            // Satu karyawan tidak boleh memiliki
            // penugasan yang sama dua kali.
            $table->unique([
                'assessment_id',
                'karyawan_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_peserta');
    }
};