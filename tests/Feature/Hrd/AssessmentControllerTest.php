<?php

use App\Models\Assessment;
use App\Models\AssessmentPeserta;
use App\Models\BlockchainRecord;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Soal;
use App\Models\SoalUpload;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders automatically filtered participants without employee selection controls', function () {
    $hrd = User::factory()->create([
        'username' => 'hrd-assessment-create-page',
        'role' => 'hrd',
    ]);
    $departemen = Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);
    $jabatan = Jabatan::create([
        'nama_jabatan' => 'Operator',
    ]);
    $employeeUser = User::factory()->create([
        'username' => 'employee-assessment-create-page',
        'role' => 'karyawan',
    ]);
    Karyawan::create([
        'user_id' => $employeeUser->id,
        'nik' => 'EMP-ASSESSMENT-CREATE',
        'nama' => 'Karyawan Produksi',
        'departemen_id' => $departemen->id,
        'jabatan_id' => $jabatan->id,
        'tanggal_masuk' => '2026-01-01',
        'status' => 'aktif',
    ]);

    $this->actingAs($hrd)
        ->get(route('hrd.assessment.create'))
        ->assertSee('Peserta Assessment')
        ->assertSee('data-karyawan-row', false)
        ->assertSee('data-departemen-id="'.$departemen->id.'"', false)
        ->assertSee('Karyawan Produksi')
        ->assertSee('Peserta Assessment Otomatis')
        ->assertDontSee('name="karyawan_ids[]"', false)
        ->assertSee('Susun Soal Assessment')
        ->assertSee('id="pilihan-soal"', false)
        ->assertSee('id="tambah-soal"', false)
        ->assertSee('Urutan yang ditambahkan menjadi urutan soal dalam assessment.')
        ->assertSee('Standar kelulusan otomatis mengikuti standar nilai yang ditetapkan pada jabatan.')
        ->assertDontSee('name="standar_nilai"', false);
});

it('filters the assessment index by development category', function (string $kategori, array $assessmentsVisible, array $assessmentsHidden) {
    $hrd = User::factory()->create([
        'role' => 'hrd',
    ]);
    $jabatanLevelSatu = Jabatan::create([
        'nama_jabatan' => 'Analis Kimia Level 1',
        'tipe_jabatan' => 'operasional',
        'level_jabatan' => 1,
    ]);
    $jabatanLevelDua = Jabatan::create([
        'nama_jabatan' => 'Analis Kimia Level 2',
        'tipe_jabatan' => 'operasional',
        'level_jabatan' => 2,
    ]);
    $jabatanStruktural = Jabatan::create([
        'nama_jabatan' => 'Wakil Foreman',
        'tipe_jabatan' => 'struktural',
        'urutan_struktural' => 1,
    ]);
    Assessment::create([
        'nama_assessment' => 'Assessment Pemindahan Jabatan',
        'jabatan_id' => $jabatanLevelSatu->id,
    ]);
    Assessment::create([
        'nama_assessment' => 'Assessment Peningkatan Skill',
        'jabatan_id' => $jabatanLevelDua->id,
    ]);
    Assessment::create([
        'nama_assessment' => 'Assessment Kenaikan Jabatan',
        'jabatan_id' => $jabatanStruktural->id,
    ]);

    $response = $this->actingAs($hrd)
        ->get(route('hrd.assessment.index', ['kategori' => $kategori]));

    foreach ($assessmentsVisible as $namaAssessment) {
        $response->assertSee($namaAssessment);
    }

    foreach ($assessmentsHidden as $namaAssessment) {
        $response->assertDontSee($namaAssessment);
    }
})->with([
    'pemindahan jabatan hanya menampilkan assessment level 1' => [
        'pemindahan_jabatan',
        ['Assessment Pemindahan Jabatan'],
        ['Assessment Peningkatan Skill', 'Assessment Kenaikan Jabatan'],
    ],
    'peningkatan skill hanya menampilkan assessment level 2 sampai 5' => [
        'peningkatan_skill',
        ['Assessment Peningkatan Skill'],
        ['Assessment Pemindahan Jabatan', 'Assessment Kenaikan Jabatan'],
    ],
    'kenaikan jabatan hanya menampilkan assessment struktural' => [
        'kenaikan_jabatan',
        ['Assessment Kenaikan Jabatan'],
        ['Assessment Pemindahan Jabatan', 'Assessment Peningkatan Skill'],
    ],
    'semua menampilkan seluruh kategori' => [
        'semua',
        ['Assessment Pemindahan Jabatan', 'Assessment Peningkatan Skill', 'Assessment Kenaikan Jabatan'],
        [],
    ],
]);

it('automatically assigns active crew from the selected department for a level one transfer assessment', function () {
    $hrd = User::factory()->create([
        'username' => 'hrd-assessment-transfer-participants',
        'role' => 'hrd',
    ]);
    $departemen = Departemen::create([
        'nama_departemen' => 'Testing Center',
    ]);
    $departemenLain = Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);
    $crew = Jabatan::create([
        'nama_jabatan' => 'Crew',
    ]);
    $jabatanAsalLain = Jabatan::create([
        'nama_jabatan' => 'Teknisi',
    ]);
    $jabatanTujuan = Jabatan::create([
        'nama_jabatan' => 'Analis Kimia Level 1',
        'tipe_jabatan' => 'operasional',
        'level_jabatan' => 1,
    ]);
    $crewUser = User::factory()->create([
        'username' => 'employee-assessment-transfer-crew',
        'role' => 'karyawan',
    ]);
    $crewTerpilih = Karyawan::create([
        'user_id' => $crewUser->id,
        'nik' => 'EMP-TRANSFER-CREW',
        'nama' => 'Crew Testing Center',
        'departemen_id' => $departemen->id,
        'jabatan_id' => $crew->id,
        'tanggal_masuk' => '2026-01-01',
        'status' => 'nonaktif',
    ]);
    $asalUser = User::factory()->create([
        'username' => 'employee-assessment-transfer-other-role',
        'role' => 'karyawan',
    ]);
    $karyawanJabatanLain = Karyawan::create([
        'user_id' => $asalUser->id,
        'nik' => 'EMP-TRANSFER-OTHER-ROLE',
        'nama' => 'Teknisi Testing Center',
        'departemen_id' => $departemen->id,
        'jabatan_id' => $jabatanAsalLain->id,
        'tanggal_masuk' => '2026-01-01',
        'status' => 'aktif',
    ]);
    $crewDepartemenLainUser = User::factory()->create([
        'username' => 'employee-assessment-transfer-other-department',
        'role' => 'karyawan',
    ]);
    $crewDepartemenLain = Karyawan::create([
        'user_id' => $crewDepartemenLainUser->id,
        'nik' => 'EMP-TRANSFER-OTHER-DEPARTMENT',
        'nama' => 'Crew Produksi',
        'departemen_id' => $departemenLain->id,
        'jabatan_id' => $crew->id,
        'tanggal_masuk' => '2026-01-01',
        'status' => 'aktif',
    ]);
    $inactiveCrewUser = User::factory()->create([
        'username' => 'employee-assessment-transfer-inactive',
        'role' => 'karyawan',
    ]);
    $crewTidakAktif = Karyawan::create([
        'user_id' => $inactiveCrewUser->id,
        'nik' => 'EMP-TRANSFER-INACTIVE',
        'nama' => 'Crew Tidak Aktif',
        'departemen_id' => $departemen->id,
        'jabatan_id' => $crew->id,
        'tanggal_masuk' => '2026-01-01',
        'status' => 'nonaktif',
    ]);
    $bankSoal = SoalUpload::create([
        'jabatan_id' => $jabatanTujuan->id,
        'departemen_id' => $departemen->id,
        'nama_file' => 'analis-kimia-level-1.xlsx',
        'jumlah_soal' => 1,
    ]);
    $soal = Soal::create([
        'jabatan_id' => $jabatanTujuan->id,
        'upload_id' => $bankSoal->id,
        'pertanyaan' => 'Pertanyaan analis kimia',
        'tipe_soal' => 'pilihan_ganda',
        'nama_prosedur' => 'Prosedur Pengoperasian Jabatan',
        'status' => true,
    ]);
    $payload = [
        'nama_assessment' => 'Assessment Pemindahan Analis Kimia',
        'departemen_id' => $departemen->id,
        'jabatan_id' => $jabatanTujuan->id,
        'durasi' => 60,
        'tanggal_mulai' => '2026-10-24T09:00',
        'tanggal_selesai' => '2026-10-24T10:00',
        'soal_levels' => [1],
        'prosedur_levels' => [
            1 => ['pengoperasian_jabatan'],
        ],
        'bank_uploads' => [
            1 => $bankSoal->id,
        ],
        'konfigurasi' => [
            [
                'level_soal' => 1,
                'tipe_prosedur' => 'pengoperasian_jabatan',
                'tipe_soal' => 'pilihan_ganda',
                'jumlah_soal' => 1,
            ],
        ],
    ];

    $this->actingAs($hrd)
        ->post(route('hrd.assessment.store'), $payload)
        ->assertSessionHasErrors([
            'departemen_id' => 'Tidak ada karyawan Crew aktif di departemen yang dipilih.',
        ]);

    $this->assertDatabaseMissing('assessments', [
        'nama_assessment' => 'Assessment Pemindahan Analis Kimia',
    ]);

    $crewTerpilih->update([
        'status' => 'aktif',
    ]);

    $this->post(route('hrd.assessment.store'), $payload)
        ->assertRedirect(route('hrd.assessment.index'));

    $assessment = Assessment::query()
        ->where('nama_assessment', 'Assessment Pemindahan Analis Kimia')
        ->firstOrFail();
    $this->assertDatabaseHas('assessment_peserta', [
        'assessment_id' => $assessment->id,
        'karyawan_id' => $crewTerpilih->id,
    ]);
    $this->assertDatabaseMissing('assessment_peserta', [
        'assessment_id' => $assessment->id,
        'karyawan_id' => $karyawanJabatanLain->id,
    ]);
    $this->assertDatabaseMissing('assessment_peserta', [
        'assessment_id' => $assessment->id,
        'karyawan_id' => $crewDepartemenLain->id,
    ]);
    $this->assertDatabaseMissing('assessment_peserta', [
        'assessment_id' => $assessment->id,
        'karyawan_id' => $crewTidakAktif->id,
    ]);
    $this->assertDatabaseHas('assessment_soal', [
        'assessment_id' => $assessment->id,
        'soal_id' => $soal->id,
    ]);

    $assessmentEvent = BlockchainRecord::query()
        ->where('entity_type', 'assessment_dibuat')
        ->where('entity_id', $assessment->id)
        ->firstOrFail();
    $participant = $assessment->peserta()->firstOrFail();
    $participantEvent = BlockchainRecord::query()
        ->where('entity_type', 'assessment_peserta_ditugaskan')
        ->where('entity_id', $participant->id)
        ->firstOrFail();

    expect($assessmentEvent->data_snapshot['actor_user_id'])->toBe($hrd->id);
    expect($participantEvent->data_snapshot['source_data']['status'])
        ->toBe('ditugaskan');
});

it('creates a structural assessment from its single bank and assigns the previous structural step', function () {
    $hrd = User::factory()->create([
        'username' => 'hrd-assessment-structural',
        'role' => 'hrd',
    ]);
    $departemen = Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);
    $departemenLain = Departemen::create([
        'nama_departemen' => 'Logistik',
    ]);
    $jabatanOperasional = Jabatan::create([
        'nama_jabatan' => 'Operator Loader Level 1',
        'tipe_jabatan' => 'operasional',
        'level_jabatan' => 1,
    ]);
    $jabatanStrukturalTujuan = Jabatan::create([
        'nama_jabatan' => 'Wakil Foreman',
        'tipe_jabatan' => 'struktural',
        'urutan_struktural' => 1,
    ]);
    $jabatanStrukturalLain = Jabatan::create([
        'nama_jabatan' => 'Foreman',
        'tipe_jabatan' => 'struktural',
        'urutan_struktural' => 2,
    ]);
    $karyawanTerpilihUser = User::factory()->create([
        'username' => 'employee-assessment-structural-eligible',
        'role' => 'karyawan',
    ]);
    $karyawanTerpilih = Karyawan::create([
        'user_id' => $karyawanTerpilihUser->id,
        'nik' => 'EMP-STRUCTURAL-ELIGIBLE',
        'nama' => 'Operator Produksi',
        'departemen_id' => $departemen->id,
        'jabatan_id' => $jabatanOperasional->id,
        'tanggal_masuk' => '2026-01-01',
        'status' => 'aktif',
    ]);
    $karyawanStrukturalUser = User::factory()->create([
        'username' => 'employee-assessment-structural-ineligible',
        'role' => 'karyawan',
    ]);
    $karyawanStruktural = Karyawan::create([
        'user_id' => $karyawanStrukturalUser->id,
        'nik' => 'EMP-STRUCTURAL-INELIGIBLE',
        'nama' => 'Foreman Produksi',
        'departemen_id' => $departemen->id,
        'jabatan_id' => $jabatanStrukturalLain->id,
        'tanggal_masuk' => '2026-01-01',
        'status' => 'aktif',
    ]);
    $karyawanTahapSatuUser = User::factory()->create([
        'username' => 'employee-assessment-structural-previous-step',
        'role' => 'karyawan',
    ]);
    $karyawanTahapSatu = Karyawan::create([
        'user_id' => $karyawanTahapSatuUser->id,
        'nik' => 'EMP-STRUCTURAL-PREVIOUS-STEP',
        'nama' => 'Wakil Foreman Produksi',
        'departemen_id' => $departemen->id,
        'jabatan_id' => $jabatanStrukturalTujuan->id,
        'tanggal_masuk' => '2026-01-01',
        'status' => 'aktif',
    ]);
    $karyawanDepartemenLainUser = User::factory()->create([
        'username' => 'employee-assessment-structural-other-department',
        'role' => 'karyawan',
    ]);
    $karyawanDepartemenLain = Karyawan::create([
        'user_id' => $karyawanDepartemenLainUser->id,
        'nik' => 'EMP-STRUCTURAL-OTHER-DEPARTMENT',
        'nama' => 'Operator Logistik',
        'departemen_id' => $departemenLain->id,
        'jabatan_id' => $jabatanOperasional->id,
        'tanggal_masuk' => '2026-01-01',
        'status' => 'aktif',
    ]);
    $bankSoal = SoalUpload::create([
        'jabatan_id' => $jabatanStrukturalTujuan->id,
        'departemen_id' => $departemen->id,
        'nama_file' => 'wakil-foreman.xlsx',
        'jumlah_soal' => 1,
    ]);
    $soal = Soal::create([
        'jabatan_id' => $jabatanStrukturalTujuan->id,
        'upload_id' => $bankSoal->id,
        'pertanyaan' => 'Pertanyaan Wakil Foreman',
        'tipe_soal' => 'pilihan_ganda',
        'nama_prosedur' => 'Prosedur Jabatan',
        'status' => true,
    ]);
    $bankForeman = SoalUpload::create([
        'jabatan_id' => $jabatanStrukturalLain->id,
        'departemen_id' => $departemen->id,
        'nama_file' => 'foreman.xlsx',
        'jumlah_soal' => 1,
    ]);
    $soalForeman = Soal::create([
        'jabatan_id' => $jabatanStrukturalLain->id,
        'upload_id' => $bankForeman->id,
        'pertanyaan' => 'Pertanyaan Foreman',
        'tipe_soal' => 'pilihan_ganda',
        'nama_prosedur' => 'Prosedur Jabatan',
        'status' => true,
    ]);

    $this->actingAs($hrd)
        ->get(route('hrd.assessment.create'))
        ->assertSee('Wakil Foreman (Kenaikan Jabatan)');

    $this->post(route('hrd.assessment.store'), [
        'nama_assessment' => 'Assessment Wakil Foreman',
        'departemen_id' => $departemen->id,
        'jabatan_id' => $jabatanStrukturalTujuan->id,
        'durasi' => 60,
        'tanggal_mulai' => '2026-10-24T09:00',
        'tanggal_selesai' => '2026-10-24T10:00',
        'soal_levels' => [1],
        'prosedur_levels' => [
            1 => ['pengoperasian_jabatan'],
        ],
        'bank_uploads' => [
            1 => $bankSoal->id,
        ],
        'konfigurasi' => [
            [
                'level_soal' => 1,
                'tipe_prosedur' => 'pengoperasian_jabatan',
                'tipe_soal' => 'pilihan_ganda',
                'jumlah_soal' => 1,
            ],
        ],
    ])->assertRedirect(route('hrd.assessment.index'));

    $assessment = Assessment::query()
        ->where('nama_assessment', 'Assessment Wakil Foreman')
        ->firstOrFail();
    $this->assertDatabaseHas('assessment_peserta', [
        'assessment_id' => $assessment->id,
        'karyawan_id' => $karyawanTerpilih->id,
    ]);
    $this->assertDatabaseMissing('assessment_peserta', [
        'assessment_id' => $assessment->id,
        'karyawan_id' => $karyawanStruktural->id,
    ]);
    $this->assertDatabaseMissing('assessment_peserta', [
        'assessment_id' => $assessment->id,
        'karyawan_id' => $karyawanDepartemenLain->id,
    ]);
    $this->assertDatabaseHas('assessment_soal', [
        'assessment_id' => $assessment->id,
        'soal_id' => $soal->id,
    ]);

    $this->post(route('hrd.assessment.store'), [
        'nama_assessment' => 'Assessment Foreman',
        'departemen_id' => $departemen->id,
        'jabatan_id' => $jabatanStrukturalLain->id,
        'durasi' => 60,
        'tanggal_mulai' => '2026-10-24T09:00',
        'tanggal_selesai' => '2026-10-24T10:00',
        'soal_levels' => [1],
        'prosedur_levels' => [
            1 => ['pengoperasian_jabatan'],
        ],
        'bank_uploads' => [
            1 => $bankForeman->id,
        ],
        'konfigurasi' => [
            [
                'level_soal' => 1,
                'tipe_prosedur' => 'pengoperasian_jabatan',
                'tipe_soal' => 'pilihan_ganda',
                'jumlah_soal' => 1,
            ],
        ],
    ])->assertRedirect(route('hrd.assessment.index'));

    $assessmentForeman = Assessment::query()
        ->where('nama_assessment', 'Assessment Foreman')
        ->firstOrFail();
    $this->assertDatabaseHas('assessment_peserta', [
        'assessment_id' => $assessmentForeman->id,
        'karyawan_id' => $karyawanTahapSatu->id,
    ]);
    $this->assertDatabaseMissing('assessment_peserta', [
        'assessment_id' => $assessmentForeman->id,
        'karyawan_id' => $karyawanTerpilih->id,
    ]);
    $this->assertDatabaseMissing('assessment_peserta', [
        'assessment_id' => $assessmentForeman->id,
        'karyawan_id' => $karyawanStruktural->id,
    ]);
    $this->assertDatabaseHas('assessment_soal', [
        'assessment_id' => $assessmentForeman->id,
        'soal_id' => $soalForeman->id,
    ]);
});

it('lets HRD edit assessment information before participants start', function () {
    $hrd = User::factory()->create([
        'username' => 'hrd-assessment-edit',
        'role' => 'hrd',
    ]);
    $jabatan = Jabatan::create([
        'nama_jabatan' => 'Operator',
        'standar_nilai' => 80,
    ]);
    $assessment = Assessment::create([
        'nama_assessment' => 'Assessment Lama',
        'jabatan_id' => $jabatan->id,
        'standar_nilai' => 80,
        'durasi' => 60,
        'tanggal_mulai' => '2026-10-23 09:00:00',
        'tanggal_selesai' => '2026-10-23 10:00:00',
        'status' => 'aktif',
    ]);

    $this->actingAs($hrd)
        ->get(route('hrd.assessment.edit', $assessment))
        ->assertOk()
        ->assertSee('Assessment Lama')
        ->assertDontSee('name="standar_nilai"', false);

    $this->actingAs($hrd)
        ->put(route('hrd.assessment.update', $assessment), [
            'nama_assessment' => 'Assessment Revisi',
            'durasi' => 90,
            'tanggal_mulai' => '2026-10-24T09:00',
            'tanggal_selesai' => '2026-10-24T11:00',
        ])
        ->assertRedirect(route('hrd.assessment.index'));

    $this->assertDatabaseHas('assessments', [
        'id' => $assessment->id,
        'nama_assessment' => 'Assessment Revisi',
        'standar_nilai' => 80,
        'durasi' => 90,
        'tanggal_mulai' => '2026-10-24 09:00:00',
        'tanggal_selesai' => '2026-10-24 11:00:00',
    ]);
});

it('lets HRD turn an assessment off and back on', function () {
    $hrd = User::factory()->create([
        'username' => 'hrd-assessment-status',
        'role' => 'hrd',
    ]);
    $jabatan = Jabatan::create([
        'nama_jabatan' => 'Operator',
    ]);
    $assessment = Assessment::create([
        'nama_assessment' => 'Assessment Status',
        'jabatan_id' => $jabatan->id,
        'status' => 'aktif',
    ]);

    $this->actingAs($hrd)
        ->post(route('hrd.assessment.toggle-status', $assessment))
        ->assertRedirect();

    $this->assertDatabaseHas('assessments', [
        'id' => $assessment->id,
        'status' => 'draft',
    ]);

    $this->post(route('hrd.assessment.toggle-status', $assessment))
        ->assertRedirect();

    $this->assertDatabaseHas('assessments', [
        'id' => $assessment->id,
        'status' => 'aktif',
    ]);
});

it('lets HRD delete an assessment with no participant history', function () {
    $hrd = User::factory()->create([
        'username' => 'hrd-assessment-delete',
        'role' => 'hrd',
    ]);
    $jabatan = Jabatan::create([
        'nama_jabatan' => 'Operator',
    ]);
    $assessment = Assessment::create([
        'nama_assessment' => 'Assessment Dihapus',
        'jabatan_id' => $jabatan->id,
    ]);

    $this->actingAs($hrd)
        ->delete(route('hrd.assessment.destroy', $assessment))
        ->assertRedirect(route('hrd.assessment.index'));

    $this->assertDatabaseMissing('assessments', [
        'id' => $assessment->id,
    ]);

    $deletionEvent = BlockchainRecord::query()
        ->where('entity_type', 'assessment_dihapus')
        ->where('entity_id', $assessment->id)
        ->firstOrFail();

    expect($deletionEvent->data_snapshot['source_deleted'])->toBeTrue();
});

it('preserves assessment history when a participant has started', function () {
    $hrd = User::factory()->create([
        'username' => 'hrd-assessment-history',
        'role' => 'hrd',
    ]);
    $jabatan = Jabatan::create([
        'nama_jabatan' => 'Operator',
    ]);
    $departemen = Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);
    $employeeUser = User::factory()->create([
        'username' => 'employee-assessment-history',
        'role' => 'karyawan',
    ]);
    $karyawan = Karyawan::create([
        'user_id' => $employeeUser->id,
        'nik' => 'EMP-ASSESSMENT-1',
        'nama' => 'Karyawan Assessment',
        'departemen_id' => $departemen->id,
        'jabatan_id' => $jabatan->id,
        'tanggal_masuk' => '2026-01-01',
        'status' => 'aktif',
    ]);
    $assessment = Assessment::create([
        'nama_assessment' => 'Assessment Berjalan',
        'jabatan_id' => $jabatan->id,
        'status' => 'aktif',
    ]);
    AssessmentPeserta::create([
        'assessment_id' => $assessment->id,
        'karyawan_id' => $karyawan->id,
        'status' => 'sedang_mengerjakan',
    ]);

    $this->actingAs($hrd)
        ->delete(route('hrd.assessment.destroy', $assessment))
        ->assertRedirect()
        ->assertSessionHas('error');

    $this->assertDatabaseHas('assessments', [
        'id' => $assessment->id,
    ]);
});

it('does not reassign a department assessment to another department', function () {
    $hrd = User::factory()->create([
        'username' => 'hrd-assessment-reassign-department',
        'role' => 'hrd',
    ]);
    $departemen = Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);
    $departemenLain = Departemen::create([
        'nama_departemen' => 'Logistik',
    ]);
    $jabatan = Jabatan::create([
        'nama_jabatan' => 'Operator',
    ]);
    $employeeUser = User::factory()->create([
        'username' => 'employee-assessment-reassign-department',
        'role' => 'karyawan',
    ]);
    $karyawan = Karyawan::create([
        'user_id' => $employeeUser->id,
        'nik' => 'EMP-ASSESSMENT-REASSIGN',
        'nama' => 'Karyawan Departemen Lain',
        'departemen_id' => $departemenLain->id,
        'jabatan_id' => $jabatan->id,
        'tanggal_masuk' => '2026-01-01',
        'status' => 'aktif',
    ]);
    $assessment = Assessment::create([
        'nama_assessment' => 'Assessment Produksi',
        'jabatan_id' => $jabatan->id,
        'departemen_id' => $departemen->id,
        'status' => 'aktif',
    ]);

    $this->actingAs($hrd)
        ->post(route('hrd.assessment.tugaskan-ulang', $assessment), [
            'karyawan_ids' => [$karyawan->id],
        ])
        ->assertRedirect()
        ->assertSessionHas('error', 'Peserta assessment harus berasal dari departemen yang sama.');

    $this->assertDatabaseMissing('assessment_peserta', [
        'assessment_id' => $assessment->id,
        'karyawan_id' => $karyawan->id,
    ]);
});

it('builds a level assessment from question banks up to the target level', function () {
    $hrd = User::factory()->create([
        'username' => 'hrd-cumulative-assessment',
        'role' => 'hrd',
    ]);
    $jabatanLevelSatu = Jabatan::create([
        'nama_jabatan' => 'Operator Loader LEV 1',
        'tipe_jabatan' => 'operasional',
        'level_jabatan' => 1,
    ]);
    $jabatanLevelDua = Jabatan::create([
        'nama_jabatan' => 'Operator Loader LEVEL 2',
        'tipe_jabatan' => 'operasional',
        'level_jabatan' => 2,
        'standar_nilai' => 75,
    ]);
    $departemen = Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);
    $departemenLain = Departemen::create([
        'nama_departemen' => 'Logistik',
    ]);
    $employeeUser = User::factory()->create([
        'username' => 'employee-cumulative-assessment',
        'role' => 'karyawan',
    ]);
    $karyawan = Karyawan::create([
        'user_id' => $employeeUser->id,
        'nik' => 'EMP-CUMULATIVE-1',
        'nama' => 'Karyawan Loader',
        'departemen_id' => $departemen->id,
        'jabatan_id' => $jabatanLevelSatu->id,
        'tanggal_masuk' => '2026-01-01',
        'status' => 'aktif',
    ]);
    $employeeUserLain = User::factory()->create([
        'username' => 'employee-cumulative-assessment-other-department',
        'role' => 'karyawan',
    ]);
    $karyawanDepartemenLain = Karyawan::create([
        'user_id' => $employeeUserLain->id,
        'nik' => 'EMP-CUMULATIVE-2',
        'nama' => 'Karyawan Departemen Lain',
        'departemen_id' => $departemenLain->id,
        'jabatan_id' => $jabatanLevelSatu->id,
        'tanggal_masuk' => '2026-01-01',
        'status' => 'aktif',
    ]);
    $bankLevelSatu = SoalUpload::create([
        'jabatan_id' => $jabatanLevelSatu->id,
        'departemen_id' => $departemen->id,
        'nama_file' => 'operator-loader-level-1.xlsx',
        'jumlah_soal' => 150,
    ]);
    $bankLevelDua = SoalUpload::create([
        'jabatan_id' => $jabatanLevelDua->id,
        'departemen_id' => $departemen->id,
        'nama_file' => 'operator-loader-level-2.xlsx',
        'jumlah_soal' => 50,
    ]);
    $bankLevelSatuTidakDipilih = SoalUpload::create([
        'jabatan_id' => $jabatanLevelSatu->id,
        'departemen_id' => $departemen->id,
        'nama_file' => 'operator-loader-level-1-tidak-dipilih.xlsx',
        'jumlah_soal' => 1,
    ]);
    $jabatanOperatorLain = Jabatan::create([
        'nama_jabatan' => 'Operator Forklift Level 1',
        'tipe_jabatan' => 'operasional',
        'level_jabatan' => 1,
    ]);
    $employeeOperatorLainUser = User::factory()->create([
        'username' => 'employee-cumulative-assessment-other-role',
        'role' => 'karyawan',
    ]);
    $karyawanJabatanLain = Karyawan::create([
        'user_id' => $employeeOperatorLainUser->id,
        'nik' => 'EMP-CUMULATIVE-OTHER-ROLE',
        'nama' => 'Karyawan Operator Forklift',
        'departemen_id' => $departemen->id,
        'jabatan_id' => $jabatanOperatorLain->id,
        'tanggal_masuk' => '2026-01-01',
        'status' => 'aktif',
    ]);
    $bankJabatanLain = SoalUpload::create([
        'jabatan_id' => $jabatanOperatorLain->id,
        'departemen_id' => $departemen->id,
        'nama_file' => 'operator-forklift-level-1.xlsx',
        'jumlah_soal' => 1,
    ]);
    $bankDepartemenLain = SoalUpload::create([
        'jabatan_id' => $jabatanLevelSatu->id,
        'departemen_id' => $departemenLain->id,
        'nama_file' => 'operator-loader-produksi-level-1.xlsx',
        'jumlah_soal' => 1,
    ]);
    $soalLevelSatu = Soal::create([
        'jabatan_id' => $jabatanLevelSatu->id,
        'upload_id' => $bankLevelSatu->id,
        'pertanyaan' => 'Pertanyaan bank level satu',
        'tipe_soal' => 'pilihan_ganda',
        'nama_prosedur' => 'Prosedur Pengoperasian Jabatan',
        'status' => true,
    ]);
    $soalLevelDua = Soal::create([
        'jabatan_id' => $jabatanLevelDua->id,
        'upload_id' => $bankLevelDua->id,
        'pertanyaan' => 'Pertanyaan bank level dua',
        'tipe_soal' => 'pilihan_ganda',
        'nama_prosedur' => 'Prosedur Pengoperasian Jabatan',
        'status' => true,
    ]);
    $soalKeselamatanLevelSatu = Soal::create([
        'jabatan_id' => $jabatanLevelSatu->id,
        'upload_id' => $bankLevelSatu->id,
        'pertanyaan' => 'Pertanyaan prosedur keselamatan level satu',
        'tipe_soal' => 'pilihan_ganda',
        'nama_prosedur' => 'Prosedur Pengoperasian Keselamatan',
        'status' => true,
    ]);
    $soalPeraturanLevelSatu = Soal::create([
        'jabatan_id' => $jabatanLevelSatu->id,
        'upload_id' => $bankLevelSatu->id,
        'pertanyaan' => 'Pertanyaan peraturan perusahaan level satu',
        'tipe_soal' => 'pilihan_ganda',
        'nama_prosedur' => 'Peraturan Perusahaan',
        'status' => true,
    ]);
    $soalLevelSatuTidakDipilih = Soal::create([
        'jabatan_id' => $jabatanLevelSatu->id,
        'upload_id' => $bankLevelSatuTidakDipilih->id,
        'pertanyaan' => 'Pertanyaan bank level satu yang tidak dipilih',
        'tipe_soal' => 'pilihan_ganda',
        'nama_prosedur' => 'Prosedur Pengoperasian Jabatan',
        'status' => true,
    ]);

    $payload = [
        'nama_assessment' => 'Assessment Operator Loader Level 2',
        'departemen_id' => $departemen->id,
        'jabatan_id' => $jabatanLevelDua->id,
        'soal_levels' => [1, 2],
        'prosedur_levels' => [
            1 => ['pengoperasian_jabatan', 'keselamatan_kerja', 'peraturan_perusahaan'],
            2 => ['pengoperasian_jabatan'],
        ],
        'bank_uploads' => [
            1 => $bankLevelSatu->id,
            2 => $bankLevelDua->id,
        ],
        'durasi' => 60,
        'tanggal_mulai' => '2026-10-24T09:00',
        'tanggal_selesai' => '2026-10-24T10:00',
        'konfigurasi' => [
            [
                'level_soal' => 1,
                'tipe_prosedur' => 'peraturan_perusahaan',
                'tipe_soal' => 'pilihan_ganda',
                'jumlah_soal' => 1,
            ],
            [
                'level_soal' => 1,
                'tipe_prosedur' => 'keselamatan_kerja',
                'tipe_soal' => 'pilihan_ganda',
                'jumlah_soal' => 1,
            ],
            [
                'level_soal' => 1,
                'tipe_prosedur' => 'pengoperasian_jabatan',
                'tipe_soal' => 'pilihan_ganda',
                'jumlah_soal' => 1,
            ],
            [
                'level_soal' => 2,
                'tipe_prosedur' => 'pengoperasian_jabatan',
                'tipe_soal' => 'pilihan_ganda',
                'jumlah_soal' => 1,
            ],
        ],
    ];

    $this->actingAs($hrd)
        ->post(route('hrd.assessment.store'), [
            ...$payload,
            'prosedur_levels' => [
                1 => ['pengoperasian_jabatan', 'keselamatan_kerja', 'peraturan_perusahaan'],
                2 => ['pengoperasian_jabatan', 'keselamatan_kerja'],
            ],
        ])
        ->assertSessionHasErrors([
            'prosedur_levels' => 'Prosedur keselamatan dan peraturan perusahaan hanya dapat diambil dari Level/SOP 1.',
        ]);

    $this->assertDatabaseMissing('assessments', [
        'nama_assessment' => 'Assessment Operator Loader Level 2',
    ]);

    $this->actingAs($hrd)
        ->post(route('hrd.assessment.store'), [
            ...$payload,
            'bank_uploads' => [
                1 => $bankJabatanLain->id,
                2 => $bankLevelDua->id,
            ],
        ])
        ->assertSessionHasErrors([
            'bank_uploads' => 'File bank soal yang dipilih untuk level 1 tidak sesuai dengan departemen dan jabatan assessment.',
        ]);

    $this->assertDatabaseMissing('assessments', [
        'nama_assessment' => 'Assessment Operator Loader Level 2',
    ]);

    $this->post(route('hrd.assessment.store'), [
        ...$payload,
        'soal_levels' => [1],
        'prosedur_levels' => [
            1 => ['pengoperasian_jabatan'],
        ],
        'konfigurasi' => [
            [
                'level_soal' => 1,
                'tipe_prosedur' => 'pengoperasian_jabatan',
                'tipe_soal' => 'pilihan_ganda',
                'jumlah_soal' => 1,
            ],
        ],
    ])->assertSessionHasErrors([
        'bank_uploads' => 'Pilih tepat satu file bank soal untuk setiap level/SOP yang dipilih.',
    ]);

    $this->assertDatabaseMissing('assessments', [
        'nama_assessment' => 'Assessment Operator Loader Level 2',
    ]);

    $this->post(route('hrd.assessment.store'), [
        ...$payload,
        'nama_assessment' => 'Assessment Operator Loader Level 2 dengan SOP Level 1 saja',
        'soal_levels' => [1],
        'prosedur_levels' => [
            1 => ['pengoperasian_jabatan'],
        ],
        'bank_uploads' => [
            1 => $bankLevelSatu->id,
        ],
        'konfigurasi' => [
            [
                'level_soal' => 1,
                'tipe_prosedur' => 'pengoperasian_jabatan',
                'tipe_soal' => 'pilihan_ganda',
                'jumlah_soal' => 1,
            ],
        ],
    ])->assertRedirect(route('hrd.assessment.index'));

    $assessmentLevelSatuSaja = Assessment::query()
        ->where('nama_assessment', 'Assessment Operator Loader Level 2 dengan SOP Level 1 saja')
        ->firstOrFail();
    $this->assertDatabaseHas('assessment_soal', [
        'assessment_id' => $assessmentLevelSatuSaja->id,
        'soal_id' => $soalLevelSatu->id,
    ]);
    $this->assertDatabaseMissing('assessment_soal', [
        'assessment_id' => $assessmentLevelSatuSaja->id,
        'soal_id' => $soalLevelDua->id,
    ]);

    $this->post(route('hrd.assessment.store'), [
        ...$payload,
        'bank_uploads' => [
            1 => $bankDepartemenLain->id,
            2 => $bankLevelDua->id,
        ],
    ])->assertSessionHasErrors([
        'bank_uploads' => 'File bank soal yang dipilih untuk level 1 tidak sesuai dengan departemen dan jabatan assessment.',
    ]);

    $this->assertDatabaseMissing('assessments', [
        'nama_assessment' => 'Assessment Operator Loader Level 2',
    ]);

    $this->post(route('hrd.assessment.store'), [
        ...$payload,
        'nama_assessment' => 'Assessment Peserta Otomatis',
        'karyawan_ids' => [$karyawanDepartemenLain->id, $karyawanJabatanLain->id],
    ])->assertRedirect(route('hrd.assessment.index'));

    $assessmentPesertaOtomatis = Assessment::query()
        ->where('nama_assessment', 'Assessment Peserta Otomatis')
        ->firstOrFail();
    $this->assertDatabaseHas('assessment_peserta', [
        'assessment_id' => $assessmentPesertaOtomatis->id,
        'karyawan_id' => $karyawan->id,
    ]);
    $this->assertDatabaseMissing('assessment_peserta', [
        'assessment_id' => $assessmentPesertaOtomatis->id,
        'karyawan_id' => $karyawanDepartemenLain->id,
    ]);
    $this->assertDatabaseMissing('assessment_peserta', [
        'assessment_id' => $assessmentPesertaOtomatis->id,
        'karyawan_id' => $karyawanJabatanLain->id,
    ]);

    $this->post(route('hrd.assessment.store'), $payload)
        ->assertRedirect(route('hrd.assessment.index'));

    $assessment = Assessment::query()->where('nama_assessment', 'Assessment Operator Loader Level 2')->firstOrFail();
    $this->assertDatabaseHas('assessment_soal', [
        'assessment_id' => $assessment->id,
        'soal_id' => $soalPeraturanLevelSatu->id,
        'nomor_soal' => 1,
    ]);
    $this->assertDatabaseHas('assessment_soal', [
        'assessment_id' => $assessment->id,
        'soal_id' => $soalKeselamatanLevelSatu->id,
        'nomor_soal' => 2,
    ]);
    $this->assertDatabaseHas('assessment_soal', [
        'assessment_id' => $assessment->id,
        'soal_id' => $soalLevelSatu->id,
        'nomor_soal' => 3,
    ]);
    $this->assertDatabaseHas('assessment_soal', [
        'assessment_id' => $assessment->id,
        'soal_id' => $soalLevelDua->id,
        'nomor_soal' => 4,
    ]);
    $this->assertDatabaseHas('assessments', [
        'id' => $assessment->id,
        'departemen_id' => $departemen->id,
        'standar_nilai' => 75,
    ]);
    $this->assertDatabaseHas('assessment_peserta', [
        'assessment_id' => $assessment->id,
        'karyawan_id' => $karyawan->id,
    ]);
    $this->assertDatabaseMissing('assessment_peserta', [
        'assessment_id' => $assessment->id,
        'karyawan_id' => $karyawanJabatanLain->id,
    ]);

    $this->assertDatabaseHas('assessment_soal', [
        'assessment_id' => $assessment->id,
        'soal_id' => $soalLevelSatu->id,
    ]);
    $this->assertDatabaseHas('assessment_soal', [
        'assessment_id' => $assessment->id,
        'soal_id' => $soalLevelDua->id,
    ]);
    $this->assertDatabaseHas('assessment_soal', [
        'assessment_id' => $assessment->id,
        'soal_id' => $soalKeselamatanLevelSatu->id,
    ]);
    $this->assertDatabaseHas('assessment_soal', [
        'assessment_id' => $assessment->id,
        'soal_id' => $soalPeraturanLevelSatu->id,
    ]);
    $this->assertDatabaseHas('assessment_konfigurasi_soals', [
        'assessment_id' => $assessment->id,
        'level_soal' => 1,
        'tipe_prosedur' => 'pengoperasian_jabatan',
        'tipe_soal' => 'pilihan_ganda',
        'jumlah_soal' => 1,
    ]);
    $this->assertDatabaseHas('assessment_konfigurasi_soals', [
        'assessment_id' => $assessment->id,
        'level_soal' => 2,
        'tipe_prosedur' => 'pengoperasian_jabatan',
        'tipe_soal' => 'pilihan_ganda',
        'jumlah_soal' => 1,
    ]);
    $this->assertDatabaseHas('assessment_konfigurasi_soals', [
        'assessment_id' => $assessment->id,
        'level_soal' => 1,
        'tipe_prosedur' => 'keselamatan_kerja',
        'tipe_soal' => 'pilihan_ganda',
        'jumlah_soal' => 1,
    ]);
    $this->assertDatabaseHas('assessment_konfigurasi_soals', [
        'assessment_id' => $assessment->id,
        'level_soal' => 1,
        'tipe_prosedur' => 'peraturan_perusahaan',
        'tipe_soal' => 'pilihan_ganda',
        'jumlah_soal' => 1,
    ]);
    $this->assertDatabaseMissing('assessment_konfigurasi_soals', [
        'assessment_id' => $assessment->id,
        'level_soal' => 2,
        'tipe_prosedur' => 'keselamatan_kerja',
    ]);
    $this->assertDatabaseMissing('assessment_konfigurasi_soals', [
        'assessment_id' => $assessment->id,
        'level_soal' => 2,
        'tipe_prosedur' => 'peraturan_perusahaan',
    ]);
    $this->assertDatabaseMissing('assessment_soal', [
        'assessment_id' => $assessment->id,
        'soal_id' => $soalLevelSatuTidakDipilih->id,
    ]);
});
