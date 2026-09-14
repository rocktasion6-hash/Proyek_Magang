<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_soal', function (Blueprint $table) {
            $table->id();

            $table->foreignId('assessment_id')
                ->constrained('assessments')
                ->cascadeOnDelete();

            $table->foreignId('soal_id')
                ->constrained('soals')
                ->cascadeOnDelete();

            // Urutan soal di dalam assessment
            $table->unsignedInteger('nomor_soal');

            $table->timestamps();

            // Satu soal tidak boleh muncul dua kali
            // dalam assessment yang sama
            $table->unique(['assessment_id', 'soal_id']);

            // Nomor soal juga harus unik dalam satu assessment
            $table->unique(['assessment_id', 'nomor_soal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_soal');
    }
};