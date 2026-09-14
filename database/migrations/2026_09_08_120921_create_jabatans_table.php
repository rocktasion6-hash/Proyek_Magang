<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jabatans', function (Blueprint $table) {
            $table->id();

            $table->string('nama_jabatan');
            $table->integer('level_jabatan')->default(1);
            $table->text('deskripsi')->nullable();
            $table->decimal('standar_nilai', 5, 2)->default(80.00);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jabatans');
    }
};