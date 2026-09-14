<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_assessments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('assessment_peserta_id')
                ->constrained('assessment_peserta')
                ->cascadeOnDelete();

            $table->decimal('nilai_akhir', 5, 2);

            $table->decimal('standar_nilai', 5, 2);

            $table->enum('status', [
                'lulus',
                'tidak_lulus'
            ]);

            $table->dateTime('tanggal_ujian');

            $table->dateTime('waktu_mulai')
                ->nullable();

            $table->dateTime('waktu_selesai')
                ->nullable();

            $table->timestamps();

            // Satu penugasan assessment memiliki
            // satu hasil akhir.
            $table->unique('assessment_peserta_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_assessments');
    }
};