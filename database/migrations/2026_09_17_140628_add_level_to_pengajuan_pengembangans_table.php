<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_pengembangans', function (Blueprint $table) {
            $table->unsignedTinyInteger('level_sebelum')
                ->nullable()
                ->after('skill_id');

            $table->unsignedTinyInteger('level_sesudah')
                ->nullable()
                ->after('level_sebelum');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_pengembangans', function (Blueprint $table) {
            $table->dropColumn([
                'level_sebelum',
                'level_sesudah',
            ]);
        });
    }
};