<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    config([
        'database.connections.warehouse' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ],
    ]);

    DB::purge('warehouse');

    Schema::connection('warehouse')->create('dim_waktu', function ($table) {
        $table->id();
        $table->date('tanggal');
        $table->unsignedSmallInteger('tahun');
        $table->unsignedTinyInteger('bulan');
        $table->string('nama_bulan');
    });

    Schema::connection('warehouse')->create('dim_departemen', function ($table) {
        $table->id();
        $table->string('nama_departemen');
    });

    Schema::connection('warehouse')->create('dim_jabatan', function ($table) {
        $table->id();
        $table->string('nama_jabatan');
        $table->integer('level_jabatan');
        $table->string('tipe_jabatan');
        $table->unsignedTinyInteger('urutan_struktural')->nullable();
    });

    Schema::connection('warehouse')->create('dim_skill', function ($table) {
        $table->id();
        $table->string('nama_skill');
    });

    Schema::connection('warehouse')->create('bridge_jabatan_skill', function ($table) {
        $table->id();
        $table->unsignedBigInteger('jabatan_id');
        $table->unsignedBigInteger('skill_id');
        $table->unsignedTinyInteger('level_dibutuhkan');
    });

    Schema::connection('warehouse')->create('dim_karyawan', function ($table) {
        $table->id();
        $table->string('nama');
        $table->string('nik');
        $table->string('status');
        $table->unsignedBigInteger('jabatan_id');
        $table->unsignedBigInteger('departemen_id');
    });

    Schema::connection('warehouse')->create('fact_assessment', function ($table) {
        $table->id();
        $table->unsignedBigInteger('waktu_id');
        $table->string('status');
        $table->decimal('nilai_akhir', 5, 2);
    });

    Schema::connection('warehouse')->create('fact_pengembangan', function ($table) {
        $table->id();
        $table->unsignedBigInteger('waktu_id');
        $table->unsignedBigInteger('karyawan_id');
        $table->unsignedBigInteger('jabatan_asal_id')->nullable();
        $table->unsignedBigInteger('jabatan_tujuan_id')->nullable();
        $table->unsignedBigInteger('skill_id')->nullable();
        $table->unsignedBigInteger('pengajuan_asal_id');
        $table->string('jenis_pengajuan');
        $table->string('status');
        $table->date('tanggal_keputusan')->nullable();
        $table->unsignedTinyInteger('level_sebelum')->nullable();
        $table->unsignedTinyInteger('level_sesudah')->nullable();
    });

    Schema::connection('warehouse')->create('fact_riwayat_jabatan', function ($table) {
        $table->id();
        $table->unsignedBigInteger('waktu_id');
        $table->unsignedBigInteger('karyawan_id');
        $table->unsignedBigInteger('jabatan_id');
        $table->unsignedBigInteger('pengajuan_asal_id')->nullable();
        $table->string('jenis_perubahan');
        $table->date('tanggal_mulai');
        $table->date('tanggal_selesai')->nullable();
        $table->integer('durasi_hari')->nullable();
        $table->unsignedInteger('jumlah_perubahan')->default(1);
    });

    Schema::connection('warehouse')->create('fact_riwayat_skill', function ($table) {
        $table->id();
        $table->unsignedBigInteger('waktu_id');
        $table->unsignedBigInteger('karyawan_id');
        $table->unsignedBigInteger('skill_id');
        $table->unsignedBigInteger('pengajuan_asal_id')->nullable();
        $table->unsignedTinyInteger('level_sebelum');
        $table->unsignedTinyInteger('level_sesudah');
        $table->smallInteger('perubahan_level');
        $table->date('tanggal_perubahan');
        $table->unsignedInteger('jumlah_perubahan')->default(1);
    });
});

it('reports completed development from history and keeps year-based decisions distinct', function () {
    $warehouse = DB::connection('warehouse');

    $warehouse->table('dim_waktu')->insert([
        ['id' => 1, 'tanggal' => '2026-03-05', 'tahun' => 2026, 'bulan' => 3, 'nama_bulan' => 'Maret'],
        ['id' => 2, 'tanggal' => '2025-03-05', 'tahun' => 2025, 'bulan' => 3, 'nama_bulan' => 'Maret'],
    ]);
    $warehouse->table('dim_departemen')->insert([
        'id' => 1,
        'nama_departemen' => 'Produksi',
    ]);
    $warehouse->table('dim_jabatan')->insert([
        ['id' => 1, 'nama_jabatan' => 'Operator Loader Level 1', 'level_jabatan' => 1, 'tipe_jabatan' => 'operasional', 'urutan_struktural' => null],
        ['id' => 2, 'nama_jabatan' => 'Wakil Foreman', 'level_jabatan' => 1, 'tipe_jabatan' => 'struktural', 'urutan_struktural' => 1],
        ['id' => 3, 'nama_jabatan' => 'Foreman', 'level_jabatan' => 1, 'tipe_jabatan' => 'struktural', 'urutan_struktural' => 2],
    ]);
    $warehouse->table('dim_skill')->insert([
        'id' => 1,
        'nama_skill' => 'Keselamatan Kerja',
    ]);
    $warehouse->table('dim_karyawan')->insert([
        'id' => 1,
        'nama' => 'Karyawan Uji',
        'nik' => 'TEST-001',
        'status' => 'aktif',
        'jabatan_id' => 2,
        'departemen_id' => 1,
    ]);
    $warehouse->table('fact_pengembangan')->insert([
        ['id' => 1, 'waktu_id' => 1, 'karyawan_id' => 1, 'jabatan_asal_id' => 1, 'jabatan_tujuan_id' => 2, 'skill_id' => null, 'pengajuan_asal_id' => 101, 'jenis_pengajuan' => 'kenaikan_jabatan', 'status' => 'disetujui', 'tanggal_keputusan' => '2026-03-05', 'level_sebelum' => null, 'level_sesudah' => null],
        ['id' => 2, 'waktu_id' => 1, 'karyawan_id' => 1, 'jabatan_asal_id' => 2, 'jabatan_tujuan_id' => 3, 'skill_id' => null, 'pengajuan_asal_id' => 102, 'jenis_pengajuan' => 'kenaikan_jabatan', 'status' => 'disetujui', 'tanggal_keputusan' => '2025-03-05', 'level_sebelum' => null, 'level_sesudah' => null],
        ['id' => 3, 'waktu_id' => 1, 'karyawan_id' => 1, 'jabatan_asal_id' => null, 'jabatan_tujuan_id' => null, 'skill_id' => 1, 'pengajuan_asal_id' => 103, 'jenis_pengajuan' => 'peningkatan_skill', 'status' => 'disetujui', 'tanggal_keputusan' => '2026-03-05', 'level_sebelum' => 1, 'level_sesudah' => 2],
    ]);
    $warehouse->table('fact_riwayat_jabatan')->insert([
        ['id' => 1, 'waktu_id' => 1, 'karyawan_id' => 1, 'jabatan_id' => 2, 'pengajuan_asal_id' => 101, 'jenis_perubahan' => 'kenaikan', 'tanggal_mulai' => '2026-03-05', 'tanggal_selesai' => null, 'durasi_hari' => null, 'jumlah_perubahan' => 1],
        ['id' => 2, 'waktu_id' => 1, 'karyawan_id' => 1, 'jabatan_id' => 3, 'pengajuan_asal_id' => 102, 'jenis_perubahan' => 'kenaikan', 'tanggal_mulai' => '2026-03-05', 'tanggal_selesai' => null, 'durasi_hari' => null, 'jumlah_perubahan' => 1],
        ['id' => 3, 'waktu_id' => 1, 'karyawan_id' => 1, 'jabatan_id' => 1, 'pengajuan_asal_id' => 104, 'jenis_perubahan' => 'pemindahan', 'tanggal_mulai' => '2026-03-05', 'tanggal_selesai' => null, 'durasi_hari' => null, 'jumlah_perubahan' => 1],
        ['id' => 4, 'waktu_id' => 1, 'karyawan_id' => 1, 'jabatan_id' => 1, 'pengajuan_asal_id' => null, 'jenis_perubahan' => 'awal', 'tanggal_mulai' => '2026-03-05', 'tanggal_selesai' => null, 'durasi_hari' => null, 'jumlah_perubahan' => 1],
    ]);
    $warehouse->table('fact_riwayat_skill')->insert([
        'id' => 1,
        'waktu_id' => 1,
        'karyawan_id' => 1,
        'skill_id' => 1,
        'pengajuan_asal_id' => 103,
        'level_sebelum' => 1,
        'level_sesudah' => 2,
        'perubahan_level' => 1,
        'tanggal_perubahan' => '2026-03-05',
        'jumlah_perubahan' => 1,
    ]);

    $hrd = User::make([
        'name' => 'HRD Test',
        'role' => 'hrd',
    ]);

    $response = $this->actingAs($hrd)
        ->get(route('hrd.laporan.index', ['tahun' => 2026]));

    $response
        ->assertOk()
        ->assertViewHas('totalPerubahanJabatan', 3)
        ->assertViewHas('totalPromosiStruktural', 2)
        ->assertViewHas('totalPemindahanAktual', 1)
        ->assertViewHas('totalPeningkatanSkill', 1)
        ->assertViewHas('totalDisetujui', 3)
        ->assertViewHas('dataDisetujuiBulanan', function ($data) {
            return $data[2] === 2;
        })
        ->assertSee('Promosi Struktural per Urutan tahun 2026')
        ->assertSee('Tanggal Keputusan');

    expect($response->viewData('labelPromosiPerUrutan')->all())->toBe([
        'Urutan 1 - Wakil Foreman',
        'Urutan 2 - Foreman',
    ]);

    $this->getJson(route('api.reporting.ringkasan'))
        ->assertOk()
        ->assertJsonPath('data.realisasi_pengembangan.promosi_struktural', 2)
        ->assertJsonPath('data.realisasi_pengembangan.pemindahan_jabatan', 1)
        ->assertJsonPath('data.realisasi_pengembangan.peningkatan_skill', 1)
        ->assertJsonPath('data.realisasi_pengembangan.promosi_per_urutan.0.urutan_struktural', 1);
});

it('shows skills mapped to source and target positions for recent skill progression', function () {
    $warehouse = DB::connection('warehouse');

    $warehouse->table('dim_waktu')->insert([
        'id' => 1,
        'tanggal' => '2026-03-05',
        'tahun' => 2026,
        'bulan' => 3,
        'nama_bulan' => 'Maret',
    ]);
    $warehouse->table('dim_departemen')->insert([
        'id' => 1,
        'nama_departemen' => 'Laboratorium',
    ]);
    $warehouse->table('dim_jabatan')->insert([
        ['id' => 1, 'nama_jabatan' => 'Operator Laboratorium Level 1', 'level_jabatan' => 1, 'tipe_jabatan' => 'operasional', 'urutan_struktural' => null],
        ['id' => 2, 'nama_jabatan' => 'Operator Laboratorium Level 2', 'level_jabatan' => 2, 'tipe_jabatan' => 'operasional', 'urutan_struktural' => null],
    ]);
    $warehouse->table('dim_skill')->insert([
        'id' => 1,
        'nama_skill' => 'Analisis Kimia',
    ]);
    $warehouse->table('bridge_jabatan_skill')->insert([
        ['jabatan_id' => 1, 'skill_id' => 1, 'level_dibutuhkan' => 1],
        ['jabatan_id' => 2, 'skill_id' => 1, 'level_dibutuhkan' => 2],
    ]);
    $warehouse->table('dim_karyawan')->insert([
        'id' => 1,
        'nama' => 'Ryadi',
        'nik' => 'TEST-RYADI',
        'status' => 'aktif',
        'jabatan_id' => 1,
        'departemen_id' => 1,
    ]);
    $warehouse->table('fact_pengembangan')->insert([
        [
            'id' => 1,
            'waktu_id' => 1,
            'karyawan_id' => 1,
            'jabatan_asal_id' => 1,
            'jabatan_tujuan_id' => 2,
            'skill_id' => null,
            'pengajuan_asal_id' => 101,
            'jenis_pengajuan' => 'peningkatan_skill',
            'status' => 'disetujui',
            'tanggal_keputusan' => '2026-03-05',
            'level_sebelum' => 1,
            'level_sesudah' => 2,
        ],
        [
            'id' => 2,
            'waktu_id' => 1,
            'karyawan_id' => 1,
            'jabatan_asal_id' => 1,
            'jabatan_tujuan_id' => 2,
            'skill_id' => null,
            'pengajuan_asal_id' => 102,
            'jenis_pengajuan' => 'peningkatan_skill',
            'status' => 'diajukan',
            'tanggal_keputusan' => null,
            'level_sebelum' => 1,
            'level_sesudah' => 2,
        ],
        [
            'id' => 3,
            'waktu_id' => 1,
            'karyawan_id' => 1,
            'jabatan_asal_id' => 1,
            'jabatan_tujuan_id' => 2,
            'skill_id' => null,
            'pengajuan_asal_id' => 103,
            'jenis_pengajuan' => 'kenaikan_jabatan',
            'status' => 'disetujui',
            'tanggal_keputusan' => '2026-03-05',
            'level_sebelum' => 1,
            'level_sesudah' => 2,
        ],
    ]);

    $hrd = User::make([
        'name' => 'HRD Test',
        'role' => 'hrd',
    ]);

    $response = $this->actingAs($hrd)
        ->get(route('hrd.laporan.index', ['tahun' => 2026]))
        ->assertSee('Ryadi')
        ->assertViewHas('totalPeningkatanSkill', 1);

    $visibleText = preg_replace('/\s+/', ' ', strip_tags($response->getContent()));

    expect($visibleText)
        ->toContain('Analisis Kimia Level 1 → Analisis Kimia Level 2');

    $this->getJson(route('api.reporting.ringkasan'))
        ->assertJsonPath('data.realisasi_pengembangan.peningkatan_skill', 1);
});

it('counts skill progression positions instead of skill dimension records', function () {
    $warehouse = DB::connection('warehouse');

    $warehouse->table('dim_waktu')->insert([
        'id' => 1,
        'tanggal' => '2026-03-05',
        'tahun' => 2026,
        'bulan' => 3,
        'nama_bulan' => 'Maret',
    ]);

    $warehouse->table('dim_skill')->insert(
        collect(range(1, 6))
            ->map(fn ($number) => [
                'id' => $number,
                'nama_skill' => 'Skill '.$number,
            ])
            ->all()
    );

    $jabatans = [];
    $jabatanId = 1;

    foreach (range(2, 5) as $level) {
        foreach (['Loader', 'Forklift'] as $equipment) {
            $jabatans[] = [
                'id' => $jabatanId++,
                'nama_jabatan' => "Operator {$equipment} Level {$level}",
                'level_jabatan' => $level,
                'tipe_jabatan' => 'operasional',
                'urutan_struktural' => null,
            ];
        }
    }

    $jabatans[] = [
        'id' => $jabatanId++,
        'nama_jabatan' => 'Operator Dump Truck Level 2',
        'level_jabatan' => 2,
        'tipe_jabatan' => 'operasional',
        'urutan_struktural' => null,
    ];

    foreach ([
        ['nama_jabatan' => '  CREW  ', 'level_jabatan' => 2, 'tipe_jabatan' => 'operasional'],
        ['nama_jabatan' => 'Operator Level 1', 'level_jabatan' => 1, 'tipe_jabatan' => 'operasional'],
        ['nama_jabatan' => 'Operator Level 6', 'level_jabatan' => 6, 'tipe_jabatan' => 'operasional'],
        ['nama_jabatan' => 'Supervisor Level 2', 'level_jabatan' => 2, 'tipe_jabatan' => 'struktural'],
    ] as $jabatan) {
        $jabatans[] = [
            'id' => $jabatanId++,
            'nama_jabatan' => $jabatan['nama_jabatan'],
            'level_jabatan' => $jabatan['level_jabatan'],
            'tipe_jabatan' => $jabatan['tipe_jabatan'],
            'urutan_struktural' => null,
        ];
    }

    $warehouse->table('dim_jabatan')->insert($jabatans);

    $hrd = User::make([
        'name' => 'HRD Test',
        'role' => 'hrd',
    ]);

    $this->actingAs($hrd)
        ->get(route('hrd.laporan.index', ['tahun' => 2026]))
        ->assertViewHas('totalSkill', 9)
        ->assertViewHas('jumlahSkill', 9);
});
