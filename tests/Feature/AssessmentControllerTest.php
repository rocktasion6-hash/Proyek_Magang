<?php

use App\Models\Assessment;
use App\Models\AssessmentPeserta;
use App\Models\BlockchainRecord;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Soal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('does not let an employee start an inactive assessment', function () {
    $jabatan = Jabatan::create([
        'nama_jabatan' => 'Operator',
    ]);
    $departemen = Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);
    $employeeUser = User::factory()->create([
        'username' => 'employee-inactive-assessment',
        'role' => 'karyawan',
    ]);
    $karyawan = Karyawan::create([
        'user_id' => $employeeUser->id,
        'nik' => 'EMP-INACTIVE-1',
        'nama' => 'Karyawan Assessment',
        'departemen_id' => $departemen->id,
        'jabatan_id' => $jabatan->id,
        'tanggal_masuk' => '2026-01-01',
        'status' => 'aktif',
    ]);
    $assessment = Assessment::create([
        'nama_assessment' => 'Assessment Tidak Aktif',
        'jabatan_id' => $jabatan->id,
        'status' => 'draft',
    ]);
    $peserta = AssessmentPeserta::create([
        'assessment_id' => $assessment->id,
        'karyawan_id' => $karyawan->id,
        'status' => 'ditugaskan',
    ]);

    $this->actingAs($employeeUser)
        ->post(route('karyawan.assessment.start', $peserta))
        ->assertRedirect(route('karyawan.assessment.show', $peserta))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('assessment_peserta', [
        'id' => $peserta->id,
        'status' => 'ditugaskan',
    ]);
});

it('records the employee and time when an assessment starts', function () {
    $departemen = Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);
    $jabatan = Jabatan::create([
        'nama_jabatan' => 'Operator',
    ]);
    $employeeUser = User::factory()->create([
        'role' => 'karyawan',
    ]);
    $karyawan = Karyawan::create([
        'user_id' => $employeeUser->id,
        'nik' => 'EMP-ASSESSMENT-START',
        'nama' => 'Karyawan Assessment',
        'departemen_id' => $departemen->id,
        'jabatan_id' => $jabatan->id,
        'tanggal_masuk' => '2026-01-01',
        'status' => 'aktif',
    ]);
    $assessment = Assessment::create([
        'nama_assessment' => 'Assessment Aktif',
        'jabatan_id' => $jabatan->id,
        'status' => 'aktif',
    ]);
    $participant = AssessmentPeserta::create([
        'assessment_id' => $assessment->id,
        'karyawan_id' => $karyawan->id,
        'status' => 'ditugaskan',
    ]);

    $this->actingAs($employeeUser)
        ->post(route('karyawan.assessment.start', $participant))
        ->assertRedirect(route('karyawan.assessment.kerjakan', $participant));

    $event = BlockchainRecord::query()
        ->where('entity_type', 'assessment_dimulai')
        ->where('entity_id', $participant->id)
        ->firstOrFail();

    expect($event->data_snapshot['actor_user_id'])->toBe($employeeUser->id);
    expect($event->data_snapshot['source_data']['status'])
        ->toBe('sedang_mengerjakan');
});

it('uses the target assessment level to display and score cumulative question points', function (
    int $level,
    float $multiPoint,
    float $expectedScore
) {
    $departemen = Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);
    $jabatanLevelSatu = Jabatan::create([
        'nama_jabatan' => 'Operator Loader Level 1',
        'tipe_jabatan' => 'operasional',
        'level_jabatan' => 1,
    ]);
    $jabatanTujuan = $level === 1
        ? $jabatanLevelSatu
        : Jabatan::create([
            'nama_jabatan' => "Operator Loader Level $level",
            'tipe_jabatan' => 'operasional',
            'level_jabatan' => $level,
        ]);
    $employeeUser = User::factory()->create([
        'username' => "employee-point-level-$level",
        'role' => 'karyawan',
    ]);
    $karyawan = Karyawan::create([
        'user_id' => $employeeUser->id,
        'nik' => "EMP-POINT-LEVEL-$level",
        'nama' => 'Karyawan Assessment',
        'departemen_id' => $departemen->id,
        'jabatan_id' => $jabatanTujuan->id,
        'tanggal_masuk' => '2026-01-01',
        'status' => 'aktif',
    ]);
    $assessment = Assessment::create([
        'nama_assessment' => "Assessment Operator Loader Level $level",
        'jabatan_id' => $jabatanTujuan->id,
        'departemen_id' => $departemen->id,
        'status' => 'aktif',
    ]);
    $peserta = AssessmentPeserta::create([
        'assessment_id' => $assessment->id,
        'karyawan_id' => $karyawan->id,
        'status' => 'sedang_mengerjakan',
        'waktu_mulai' => now()->subMinutes(5),
    ]);
    $soalMulti = Soal::create([
        'jabatan_id' => $jabatanLevelSatu->id,
        'pertanyaan' => 'Pertanyaan multi dari bank level satu',
        'tipe_soal' => 'multi_jawaban',
        'nilai_point' => 3,
        'status' => true,
    ]);
    $jawabanMultiA = $soalMulti->pilihanJawabans()->create([
        'kode' => 'A',
        'teks_jawaban' => 'Pilihan benar A',
        'is_benar' => true,
    ]);
    $soalMulti->pilihanJawabans()->create([
        'kode' => 'B',
        'teks_jawaban' => 'Pilihan benar B',
        'is_benar' => true,
    ]);
    $soalMulti->pilihanJawabans()->create([
        'kode' => 'C',
        'teks_jawaban' => 'Pilihan salah',
        'is_benar' => false,
    ]);
    $soalPilihanGanda = Soal::create([
        'jabatan_id' => $jabatanLevelSatu->id,
        'pertanyaan' => 'Pertanyaan pilihan ganda dari bank level satu',
        'tipe_soal' => 'pilihan_ganda',
        'nilai_point' => 7,
        'status' => true,
    ]);
    $jawabanPilihanGanda = $soalPilihanGanda->pilihanJawabans()->create([
        'kode' => 'A',
        'teks_jawaban' => 'Pilihan benar',
        'is_benar' => true,
    ]);
    $soalPilihanGanda->pilihanJawabans()->create([
        'kode' => 'B',
        'teks_jawaban' => 'Pilihan salah',
        'is_benar' => false,
    ]);
    $assessment->soals()->attach($soalMulti->id, ['nomor_soal' => 1]);
    $assessment->soals()->attach($soalPilihanGanda->id, ['nomor_soal' => 2]);

    $examPage = $this->actingAs($employeeUser)
        ->get(route('karyawan.assessment.kerjakan', $peserta));

    $examPage->assertSee('Poin '.number_format($multiPoint, 2))
        ->assertSee('Poin 1.00')
        ->assertDontSee('Poin 7.00');

    $this->post(route('karyawan.assessment.submit', $peserta), [
        'jawaban' => [
            $soalMulti->id => [$jawabanMultiA->id],
            $soalPilihanGanda->id => $jawabanPilihanGanda->id,
        ],
    ])->assertRedirect(route('karyawan.assessment.hasil', $peserta));

    $this->assertDatabaseHas('hasil_assessments', [
        'assessment_peserta_id' => $peserta->id,
        'nilai_akhir' => $expectedScore,
    ]);
    $participantEvent = BlockchainRecord::query()
        ->where('entity_type', 'assessment_diselesaikan')
        ->where('entity_id', $peserta->id)
        ->firstOrFail();
    $resultEvent = BlockchainRecord::query()
        ->where('entity_type', 'hasil_assessment')
        ->where('entity_id', $peserta->hasilAssessment->id)
        ->firstOrFail();

    expect($participantEvent->data_snapshot['source_data']['status'])
        ->toBe('selesai');
    expect($resultEvent->data_snapshot['actor_user_id'])
        ->toBe($employeeUser->id);
    $this->assertDatabaseHas('soals', [
        'id' => $soalMulti->id,
        'nilai_point' => 3,
    ]);
    $this->assertDatabaseHas('soals', [
        'id' => $soalPilihanGanda->id,
        'nilai_point' => 7,
    ]);
})->with([
    'level 1 retains three multi-answer points' => [1, 3.0, 25.0],
    'level 2 uses two multi-answer points' => [2, 2.0, 33.33],
    'level 3 normalizes level 1 multi-answer points to two' => [3, 2.0, 33.33],
    'level 4 uses one multi-answer point' => [4, 1.0, 50.0],
    'level 5 uses one multi-answer point' => [5, 1.0, 50.0],
]);
