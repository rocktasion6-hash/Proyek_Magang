<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessment_peserta', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Nomor percobaan ujian
            |--------------------------------------------------------------------------
            */

            if (! Schema::hasColumn('assessment_peserta', 'percobaan_ke')) {
                $table->unsignedInteger('percobaan_ke')
                    ->default(1)
                    ->after('karyawan_id');
            }

            /*
            |--------------------------------------------------------------------------
            | Hapus aturan:
            | assessment + karyawan hanya boleh satu kali
            |--------------------------------------------------------------------------
            */

            $table->dropForeign(['assessment_id']);

            $table->dropUnique([
                'assessment_id',
                'karyawan_id',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Ganti menjadi:
            | assessment + karyawan + percobaan
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'assessment_id',
                'karyawan_id',
                'percobaan_ke',
            ]);

            $table->foreign('assessment_id')
                ->references('id')
                ->on('assessments')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('assessment_peserta', function (Blueprint $table) {

            $table->dropForeign(['assessment_id']);

            $table->dropUnique([
                'assessment_id',
                'karyawan_id',
                'percobaan_ke',
            ]);

            $table->unique([
                'assessment_id',
                'karyawan_id',
            ]);

            $table->foreign('assessment_id')
                ->references('id')
                ->on('assessments')
                ->onDelete('cascade');

            $table->dropColumn('percobaan_ke');
        });
    }
};