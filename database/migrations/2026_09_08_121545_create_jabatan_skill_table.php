<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jabatan_skill', function (Blueprint $table) {
            $table->id();

            $table->foreignId('jabatan_id')
                ->constrained('jabatans')
                ->cascadeOnDelete();

            $table->foreignId('skill_id')
                ->constrained('skills')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('level_dibutuhkan')->default(1);

            $table->timestamps();

            $table->unique(['jabatan_id', 'skill_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jabatan_skill');
    }
};