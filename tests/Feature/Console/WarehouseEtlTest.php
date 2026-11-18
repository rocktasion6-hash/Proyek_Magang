<?php

use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

it('preserves the published warehouse data when a source query fails', function () {
    config([
        'database.connections.warehouse' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ],
    ]);

    DB::purge('warehouse');

    $warehouseTables = [
        'fact_riwayat_skill',
        'fact_riwayat_jabatan',
        'fact_pengembangan',
        'fact_assessment',
        'bridge_jabatan_skill',
        'dim_karyawan',
        'dim_skill',
        'dim_jabatan',
        'dim_departemen',
        'dim_waktu',
    ];

    foreach ($warehouseTables as $tableName) {
        Schema::connection('warehouse')->create($tableName, function (Blueprint $table): void {
            $table->id();
        });
    }

    DB::connection('warehouse')->table('dim_departemen')->insert(['id' => 1]);

    expect(fn () => $this->artisan('warehouse:etl')->assertFailed())
        ->toThrow(QueryException::class);

    $this->assertDatabaseHas('dim_departemen', ['id' => 1], 'warehouse');
});

it('reports source records skipped for missing required relations', function () {
    config([
        'database.connections.warehouse' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ],
    ]);

    DB::purge('warehouse');

    foreach ([
        'fact_riwayat_skill',
        'fact_riwayat_jabatan',
        'fact_pengembangan',
        'fact_assessment',
        'bridge_jabatan_skill',
        'dim_karyawan',
        'dim_skill',
        'dim_jabatan',
        'dim_departemen',
    ] as $tableName) {
        Schema::connection('warehouse')->create($tableName, function (Blueprint $table): void {
            $table->id();
        });
    }

    Schema::connection('warehouse')->create('dim_waktu', function (Blueprint $table): void {
        $table->id();
        $table->date('tanggal');
        $table->unsignedSmallInteger('tahun');
        $table->unsignedTinyInteger('bulan');
        $table->string('nama_bulan');
        $table->unsignedTinyInteger('kuartal');
        $table->unsignedTinyInteger('hari');
    });

    foreach (['departemens', 'jabatans', 'skills'] as $tableName) {
        Schema::create($tableName, function (Blueprint $table): void {
            $table->id();
        });
    }

    Schema::create('karyawans', function (Blueprint $table): void {
        $table->id();
        $table->date('tanggal_masuk')->nullable();
    });

    Schema::create('hasil_assessments', function (Blueprint $table): void {
        $table->id();
        $table->unsignedBigInteger('assessment_peserta_id');
        $table->date('tanggal_ujian')->nullable();
    });

    Schema::create('assessment_peserta', function (Blueprint $table): void {
        $table->id();
    });

    Schema::create('jabatan_skill', function (Blueprint $table): void {
        $table->id();
        $table->unsignedBigInteger('jabatan_id');
        $table->unsignedBigInteger('skill_id');
        $table->unsignedTinyInteger('level_dibutuhkan');
    });

    Schema::create('pengajuan_pengembangans', function (Blueprint $table): void {
        $table->id();
        $table->date('tanggal_pengajuan')->nullable();
        $table->date('tanggal_keputusan')->nullable();
    });

    Schema::create('riwayat_jabatans', function (Blueprint $table): void {
        $table->id();
        $table->date('tanggal_mulai')->nullable();
    });

    Schema::create('riwayat_skills', function (Blueprint $table): void {
        $table->id();
        $table->date('tanggal_perubahan')->nullable();
    });

    DB::table('hasil_assessments')->insert([
        'id' => 10,
        'assessment_peserta_id' => 999,
        'tanggal_ujian' => '2026-03-05',
    ]);

    $result = $this->artisan('warehouse:etl');

    $result
        ->expectsOutputToContain('Fact Assessment: 0 dimuat, 1 dilewati.')
        ->assertSuccessful();

    $this->assertDatabaseCount('fact_assessment', 0, 'warehouse');
});

it('loads configured job skill levels into the warehouse bridge', function () {
    config([
        'database.connections.warehouse' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ],
    ]);

    DB::purge(config('database.default'));
    DB::purge('warehouse');
    $warehouse = DB::connection('warehouse');

    Schema::connection('warehouse')->create('dim_waktu', function (Blueprint $table): void {
        $table->id();
        $table->date('tanggal');
        $table->unsignedSmallInteger('tahun');
        $table->unsignedTinyInteger('bulan');
        $table->string('nama_bulan');
        $table->unsignedTinyInteger('kuartal');
        $table->unsignedTinyInteger('hari');
    });

    Schema::connection('warehouse')->create('dim_departemen', function (Blueprint $table): void {
        $table->id();
        $table->unsignedBigInteger('departemen_asal_id')->unique();
        $table->string('nama_departemen');
        $table->text('deskripsi')->nullable();
    });

    Schema::connection('warehouse')->create('dim_jabatan', function (Blueprint $table): void {
        $table->id();
        $table->unsignedBigInteger('jabatan_asal_id')->unique();
        $table->string('nama_jabatan');
        $table->integer('level_jabatan');
        $table->string('tipe_jabatan')->nullable();
        $table->unsignedTinyInteger('urutan_struktural')->nullable();
        $table->decimal('standar_nilai', 5, 2)->nullable();
        $table->text('deskripsi')->nullable();
    });

    Schema::connection('warehouse')->create('dim_skill', function (Blueprint $table): void {
        $table->id();
        $table->unsignedBigInteger('skill_asal_id')->unique();
        $table->string('nama_skill');
        $table->text('deskripsi')->nullable();
    });

    Schema::connection('warehouse')->create('bridge_jabatan_skill', function (Blueprint $table): void {
        $table->id();
        $table->unsignedBigInteger('jabatan_id');
        $table->unsignedBigInteger('skill_id');
        $table->unsignedTinyInteger('level_dibutuhkan');
    });

    foreach ([
        'dim_karyawan',
        'fact_assessment',
        'fact_pengembangan',
        'fact_riwayat_jabatan',
        'fact_riwayat_skill',
    ] as $tableName) {
        Schema::connection('warehouse')->create($tableName, function (Blueprint $table): void {
            $table->id();
        });
    }

    Schema::create('departemens', function (Blueprint $table): void {
        $table->id();
        $table->string('nama_departemen');
        $table->text('deskripsi')->nullable();
    });

    Schema::create('jabatans', function (Blueprint $table): void {
        $table->id();
        $table->string('nama_jabatan');
        $table->integer('level_jabatan');
        $table->string('tipe_jabatan')->nullable();
        $table->unsignedTinyInteger('urutan_struktural')->nullable();
        $table->decimal('standar_nilai', 5, 2)->nullable();
        $table->text('deskripsi')->nullable();
    });

    Schema::create('skills', function (Blueprint $table): void {
        $table->id();
        $table->string('nama_skill');
        $table->text('deskripsi')->nullable();
    });

    Schema::create('jabatan_skill', function (Blueprint $table): void {
        $table->id();
        $table->unsignedBigInteger('jabatan_id');
        $table->unsignedBigInteger('skill_id');
        $table->unsignedTinyInteger('level_dibutuhkan');
    });

    Schema::create('karyawans', function (Blueprint $table): void {
        $table->id();
        $table->date('tanggal_masuk')->nullable();
    });

    Schema::create('hasil_assessments', function (Blueprint $table): void {
        $table->id();
        $table->date('tanggal_ujian')->nullable();
    });

    Schema::create('pengajuan_pengembangans', function (Blueprint $table): void {
        $table->id();
        $table->date('tanggal_pengajuan')->nullable();
        $table->date('tanggal_keputusan')->nullable();
    });

    Schema::create('riwayat_jabatans', function (Blueprint $table): void {
        $table->id();
        $table->date('tanggal_mulai')->nullable();
    });

    Schema::create('riwayat_skills', function (Blueprint $table): void {
        $table->id();
        $table->date('tanggal_perubahan')->nullable();
    });

    DB::table('departemens')->insert([
        'id' => 1,
        'nama_departemen' => 'Laboratorium',
    ]);
    DB::table('jabatans')->insert([
        'id' => 1,
        'nama_jabatan' => 'Operator Laboratorium Level 2',
        'level_jabatan' => 2,
        'tipe_jabatan' => 'operasional',
        'standar_nilai' => 80,
    ]);
    DB::table('skills')->insert([
        'id' => 1,
        'nama_skill' => 'Analisis Kimia',
    ]);
    DB::table('jabatan_skill')->insert([
        'id' => 1,
        'jabatan_id' => 1,
        'skill_id' => 1,
        'level_dibutuhkan' => 2,
    ]);

    $this->artisan('warehouse:etl')
        ->expectsOutputToContain('Bridge Jabatan Skill selesai: 1')
        ->assertSuccessful();

    expect($warehouse
        ->table('bridge_jabatan_skill')
        ->get(['jabatan_id', 'skill_id', 'level_dibutuhkan'])
        ->map(fn (object $mapping): array => (array) $mapping)
        ->all())
        ->toBe([
            [
                'jabatan_id' => 1,
                'skill_id' => 1,
                'level_dibutuhkan' => 2,
            ],
        ]);
});
