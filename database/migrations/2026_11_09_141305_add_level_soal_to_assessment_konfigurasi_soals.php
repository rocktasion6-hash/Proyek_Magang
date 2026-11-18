<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('assessment_konfigurasi_soals', 'level_soal')) {
            Schema::table('assessment_konfigurasi_soals', function (Blueprint $table) {
                $table->unsignedTinyInteger('level_soal')
                    ->nullable()
                    ->after('assessment_id');
            });
        }

        if (! Schema::hasIndex('assessment_konfigurasi_soals', 'uq_assessment_konfigurasi_level')) {
            Schema::table('assessment_konfigurasi_soals', function (Blueprint $table) {
                $table->unique(
                    ['assessment_id', 'level_soal', 'tipe_prosedur', 'tipe_soal'],
                    'uq_assessment_konfigurasi_level'
                );
            });
        }

        if (Schema::hasIndex('assessment_konfigurasi_soals', 'uq_assessment_konfigurasi')) {
            Schema::table('assessment_konfigurasi_soals', function (Blueprint $table) {
                $table->dropUnique('uq_assessment_konfigurasi');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $konfigurasiGabungan = DB::table('assessment_konfigurasi_soals')
            ->select('assessment_id', 'tipe_prosedur', 'tipe_soal')
            ->selectRaw('MIN(id) AS id')
            ->selectRaw('SUM(jumlah_soal) AS jumlah_soal')
            ->groupBy('assessment_id', 'tipe_prosedur', 'tipe_soal')
            ->get();

        foreach ($konfigurasiGabungan as $konfigurasi) {
            $barisTerkait = DB::table('assessment_konfigurasi_soals')
                ->where('assessment_id', $konfigurasi->assessment_id)
                ->where('tipe_prosedur', $konfigurasi->tipe_prosedur)
                ->where('tipe_soal', $konfigurasi->tipe_soal);

            $barisTerkait->where('id', $konfigurasi->id)
                ->update(['jumlah_soal' => $konfigurasi->jumlah_soal]);

            DB::table('assessment_konfigurasi_soals')
                ->where('assessment_id', $konfigurasi->assessment_id)
                ->where('tipe_prosedur', $konfigurasi->tipe_prosedur)
                ->where('tipe_soal', $konfigurasi->tipe_soal)
                ->where('id', '!=', $konfigurasi->id)
                ->delete();
        }

        Schema::table('assessment_konfigurasi_soals', function (Blueprint $table) {
            $table->dropUnique('uq_assessment_konfigurasi_level');
            $table->dropColumn('level_soal');
            $table->unique(
                ['assessment_id', 'tipe_prosedur', 'tipe_soal'],
                'uq_assessment_konfigurasi'
            );
        });
    }
};
