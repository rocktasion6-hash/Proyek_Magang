<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jabatans', function (Blueprint $table) {
            /*
            |------------------------------------------------------------------
            | tipe_jabatan
            |------------------------------------------------------------------
            | operasional : Crew, Operator Loader Lv1, Operator Loader Lv2, …
            | struktural  : Wakil Foreman, Foreman, Wakil Supervisor, …
            */
            $table->enum('tipe_jabatan', ['operasional', 'struktural'])
                ->default('operasional')
                ->after('nama_jabatan');

            /*
            |------------------------------------------------------------------
            | urutan_struktural
            |------------------------------------------------------------------
            | Urutan kenaikan jabatan struktural:
            |   1 = Wakil Foreman
            |   2 = Foreman
            |   3 = Wakil Supervisor
            |   4 = Supervisor
            |   5 = Wakil General Manager
            |   6 = Manager
            | NULL untuk jabatan operasional.
            */
            $table->unsignedTinyInteger('urutan_struktural')
                ->nullable()
                ->after('tipe_jabatan');
        });
    }

    public function down(): void
    {
        Schema::table('jabatans', function (Blueprint $table) {
            $table->dropColumn(['tipe_jabatan', 'urutan_struktural']);
        });
    }
};
