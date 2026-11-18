<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |----------------------------------------------------------------------
        | 1. Restrukturisasi tabel soals
        |----------------------------------------------------------------------
        | - Hapus skill_id, tingkat_kesulitan
        | - Ganti tipe_soal enum
        | - Tambah tipe_prosedur
        */
        // skill_id di soals sudah dihapus di percobaan migration sebelumnya
        // Hanya drop kolom yang masih ada
        Schema::table('soals', function (Blueprint $table) {
            if (Schema::hasColumn('soals', 'skill_id')) {
                $table->dropForeign(['skill_id']);
                $table->dropColumn('skill_id');
            }
            if (Schema::hasColumn('soals', 'tingkat_kesulitan')) {
                $table->dropColumn('tingkat_kesulitan');
            }
            if (Schema::hasColumn('soals', 'bobot')) {
                $table->dropColumn('bobot');
            }
        });

        Schema::table('soals', function (Blueprint $table) {
            $table->string('tipe_soal', 50)
                ->default('pilihan_ganda')
                ->change();
        });

        DB::statement("UPDATE soals SET tipe_soal = 'pilihan_ganda' WHERE tipe_soal NOT IN ('pilihan_ganda','multi_jawaban','benar_salah')");

        Schema::table('soals', function (Blueprint $table) {
            $table->enum('tipe_soal', [
                'pilihan_ganda',
                'multi_jawaban',
                'benar_salah',
            ])->default('pilihan_ganda')->change();

            if (! Schema::hasColumn('soals', 'tipe_prosedur')) {
                $table->enum('tipe_prosedur', [
                    'pengoperasian_jabatan',
                    'keselamatan_kerja',
                    'peraturan_perusahaan',
                ])->default('pengoperasian_jabatan')->after('tipe_soal');
            }
        });

        Schema::table('assessments', function (Blueprint $table) {
            if (Schema::hasColumn('assessments', 'skill_id')) {
                $table->dropForeign(['skill_id']);
                $table->dropColumn('skill_id');
            }
        });

        /*
        |----------------------------------------------------------------------
        | 3. Tabel konfigurasi soal per assessment
        |----------------------------------------------------------------------
        | HRD menentukan berapa soal per tipe_prosedur per tipe_soal
        | yang diambil acak dari bank soal saat assessment dibuat.
        */
        Schema::create('assessment_konfigurasi_soals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('assessment_id')
                ->constrained('assessments')
                ->cascadeOnDelete();

            $table->enum('tipe_prosedur', [
                'pengoperasian_jabatan',
                'keselamatan_kerja',
                'peraturan_perusahaan',
            ]);

            $table->enum('tipe_soal', [
                'pilihan_ganda',
                'multi_jawaban',
                'benar_salah',
            ]);

            // Jumlah soal yang diambil acak dari bank soal
            $table->unsignedSmallInteger('jumlah_soal');

            $table->timestamps();

            $table->unique(
                ['assessment_id', 'tipe_prosedur', 'tipe_soal'],
                'uq_assessment_konfigurasi'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_konfigurasi_soals');

        Schema::table('assessments', function (Blueprint $table) {
            $table->foreignId('skill_id')
                ->nullable()
                ->constrained('skills')
                ->nullOnDelete();
        });

        Schema::table('soals', function (Blueprint $table) {
            $table->dropColumn('tipe_prosedur');
            $table->decimal('bobot', 5, 2)->default(1.00);
            $table->enum('tingkat_kesulitan', ['mudah', 'sedang', 'sulit'])->default('sedang');
            $table->foreignId('skill_id')->constrained('skills')->cascadeOnDelete();
        });
    }
};
