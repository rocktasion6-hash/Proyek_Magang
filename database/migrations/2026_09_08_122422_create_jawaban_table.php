<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jawabans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('assessment_peserta_id')
                ->constrained('assessment_peserta')
                ->cascadeOnDelete();

            $table->foreignId('soal_id')
                ->constrained('soals')
                ->cascadeOnDelete();

            $table->foreignId('pilihan_jawaban_id')
                ->constrained('pilihan_jawabans')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique([
                'assessment_peserta_id',
                'soal_id',
                'pilihan_jawaban_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jawabans');
    }
};