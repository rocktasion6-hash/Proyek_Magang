<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawan_skill', function (Blueprint $table) {
            $table->id();

            $table->foreignId('karyawan_id')
                ->constrained('karyawans')
                ->cascadeOnDelete();

            $table->foreignId('skill_id')
                ->constrained('skills')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('level_skill')->default(1);

            $table->date('tanggal_penilaian')->nullable();

            $table->timestamps();

            $table->unique(['karyawan_id', 'skill_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawan_skill');
    }
};