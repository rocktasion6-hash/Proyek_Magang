<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('jabatan_id')
                ->constrained('jabatans')
                ->cascadeOnDelete();

            $table->foreignId('skill_id')
                ->constrained('skills')
                ->cascadeOnDelete();

            $table->text('pertanyaan');

            $table->enum('tipe_soal', [
                'pilihan_tunggal',
                'multi_jawaban',
                'benar_salah'
            ]);

            $table->enum('tingkat_kesulitan', [
                'mudah',
                'sedang',
                'sulit'
            ])->default('sedang');

            $table->decimal('bobot', 5, 2)->default(1.00);

            $table->boolean('status')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soals');
    }
};