<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();

            // Relasi ke users
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            // Data utama karyawan
            $table->string('nik')->unique();
            $table->string('nama');

            // Relasi ke departemen
            $table->foreignId('departemen_id')
                ->constrained('departemens')
                ->restrictOnDelete();

            // Relasi ke jabatan
            $table->foreignId('jabatan_id')
                ->constrained('jabatans')
                ->restrictOnDelete();

            $table->string('level')->nullable();
            $table->date('tanggal_masuk');

            $table->enum('status', ['aktif', 'nonaktif'])
                ->default('aktif');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};