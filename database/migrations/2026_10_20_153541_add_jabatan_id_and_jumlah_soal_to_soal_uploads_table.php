<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('soal_uploads', function (Blueprint $table) {
            $table->foreignId('jabatan_id')
                ->nullable()
                ->constrained('jabatans')
                ->nullOnDelete();

            $table->unsignedInteger('jumlah_soal')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('soal_uploads', function (Blueprint $table) {
            $table->dropForeign(['jabatan_id']);
            $table->dropColumn(['jabatan_id', 'jumlah_soal']);
        });
    }
};
