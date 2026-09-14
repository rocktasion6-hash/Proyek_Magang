<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();

            $table->string('nama_assessment');

            // Assessment untuk jabatan tertentu
            $table->foreignId('jabatan_id')
                ->constrained('jabatans')
                ->restrictOnDelete();

            // Bisa digunakan khusus untuk skill tertentu
            $table->foreignId('skill_id')
                ->nullable()
                ->constrained('skills')
                ->nullOnDelete();

            // Nilai minimal untuk dinyatakan lulus
            $table->decimal('standar_nilai', 5, 2)->default(80.00);

            // Durasi ujian dalam menit
            $table->unsignedInteger('durasi')->default(60);

            $table->dateTime('tanggal_mulai')->nullable();
            $table->dateTime('tanggal_selesai')->nullable();

            $table->enum('status', [
                'draft',
                'aktif',
                'selesai',
                'dibatalkan'
            ])->default('draft');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};