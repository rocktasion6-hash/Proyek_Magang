<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

it('allows the same procedure and question type at different source levels', function () {
    Schema::dropIfExists('assessment_konfigurasi_soals');
    Schema::create('assessment_konfigurasi_soals', function (Blueprint $table): void {
        $table->id();
        $table->unsignedBigInteger('assessment_id');
        $table->string('tipe_prosedur');
        $table->string('tipe_soal');
        $table->unsignedSmallInteger('jumlah_soal');
        $table->timestamps();
        $table->unique(
            ['assessment_id', 'tipe_prosedur', 'tipe_soal'],
            'uq_assessment_konfigurasi'
        );
    });

    $migration = require database_path(
        'migrations/2026_11_09_141305_add_level_soal_to_assessment_konfigurasi_soals.php'
    );

    try {
        $migration->up();

        DB::table('assessment_konfigurasi_soals')->insert([
            [
                'assessment_id' => 1,
                'level_soal' => 1,
                'tipe_prosedur' => 'pengoperasian_jabatan',
                'tipe_soal' => 'pilihan_ganda',
                'jumlah_soal' => 2,
            ],
            [
                'assessment_id' => 1,
                'level_soal' => 2,
                'tipe_prosedur' => 'pengoperasian_jabatan',
                'tipe_soal' => 'pilihan_ganda',
                'jumlah_soal' => 3,
            ],
        ]);

        expect(DB::table('assessment_konfigurasi_soals')->count())->toBe(2);
        expect(Schema::hasColumn('assessment_konfigurasi_soals', 'level_soal'))->toBeTrue();

        $migration->down();

        expect(Schema::hasColumn('assessment_konfigurasi_soals', 'level_soal'))->toBeFalse();
        expect(DB::table('assessment_konfigurasi_soals')->count())->toBe(1);
        expect((int) DB::table('assessment_konfigurasi_soals')->value('jumlah_soal'))->toBe(5);
    } finally {
        Schema::dropIfExists('assessment_konfigurasi_soals');
    }
});

it('resumes when the source-level column was added before a failed migration', function () {
    Schema::dropIfExists('assessment_konfigurasi_soals');
    Schema::create('assessment_konfigurasi_soals', function (Blueprint $table): void {
        $table->id();
        $table->unsignedBigInteger('assessment_id');
        $table->unsignedTinyInteger('level_soal')->nullable();
        $table->string('tipe_prosedur');
        $table->string('tipe_soal');
        $table->unsignedSmallInteger('jumlah_soal');
        $table->timestamps();
        $table->unique(
            ['assessment_id', 'tipe_prosedur', 'tipe_soal'],
            'uq_assessment_konfigurasi'
        );
    });

    $migration = require database_path(
        'migrations/2026_11_09_141305_add_level_soal_to_assessment_konfigurasi_soals.php'
    );

    try {
        $migration->up();

        expect(Schema::hasColumn('assessment_konfigurasi_soals', 'level_soal'))->toBeTrue();
        expect(Schema::hasIndex('assessment_konfigurasi_soals', 'uq_assessment_konfigurasi_level'))->toBeTrue();
        expect(Schema::hasIndex('assessment_konfigurasi_soals', 'uq_assessment_konfigurasi'))->toBeFalse();
    } finally {
        Schema::dropIfExists('assessment_konfigurasi_soals');
    }
});
