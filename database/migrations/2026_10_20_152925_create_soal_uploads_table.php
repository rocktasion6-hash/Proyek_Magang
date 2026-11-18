<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soal_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('nama_file');
            $table->string('status')->default('pending');
            $table->unsignedInteger('total_soal')->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soal_uploads');
    }
};
