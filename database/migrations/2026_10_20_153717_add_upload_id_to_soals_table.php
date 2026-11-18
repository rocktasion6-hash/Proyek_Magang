<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('soals', function (Blueprint $table) {
            $table->foreignId('upload_id')
                ->nullable()
                ->constrained('soal_uploads')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('soals', function (Blueprint $table) {
            $table->dropForeign(['upload_id']);
            $table->dropColumn('upload_id');
        });
    }
};
