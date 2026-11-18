<?php

use App\Models\Assessment;
use App\Models\AssessmentPeserta;
use App\Models\BlockchainRecord;
use App\Models\Departemen;
use App\Models\HasilAssessment;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\PengajuanPengembangan;
use App\Models\User;
use App\Services\BlockchainService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates an employee with a PT', function () {
    $hrd = User::factory()->create([
        'username' => 'hrd-test',
        'role' => 'hrd',
    ]);
    $departemen = Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);
    Jabatan::create([
        'nama_jabatan' => 'Crew',
    ]);

    $this->actingAs($hrd)
        ->post(route('hrd.karyawan.store'), [
            'nik' => 'KRY001',
            'nama' => 'Budi',
            'pt' => 'OSMI',
            'email' => 'budi@example.com',
            'username' => 'budi-test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'departemen_id' => $departemen->id,
            'tanggal_masuk' => '2026-10-22',
            'status' => 'aktif',
        ])
        ->assertRedirect(route('hrd.karyawan.index'));

    $this->assertDatabaseHas('karyawans', [
        'nik' => 'KRY001',
        'pt' => 'OSMI',
    ]);

    $this->get(route('hrd.karyawan.index'))
        ->assertSee('OSMI');
});

it('updates an employee PT from the edit page', function () {
    $hrd = User::factory()->create([
        'username' => 'hrd-test',
        'role' => 'hrd',
    ]);
    $employeeUser = User::factory()->create([
        'username' => 'employee-test',
        'role' => 'karyawan',
    ]);
    $departemen = Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);
    $jabatan = Jabatan::create([
        'nama_jabatan' => 'Crew',
    ]);
    $karyawan = Karyawan::create([
        'user_id' => $employeeUser->id,
        'nik' => 'KRY002',
        'nama' => 'Siti',
        'pt' => 'OSMI',
        'departemen_id' => $departemen->id,
        'jabatan_id' => $jabatan->id,
        'tanggal_masuk' => '2026-10-22',
        'status' => 'aktif',
    ]);

    $this->actingAs($hrd)
        ->get(route('hrd.karyawan.edit', $karyawan))
        ->assertOk()
        ->assertSee('OSMI');

    $this->put(route('hrd.karyawan.update', $karyawan), [
        'nik' => $karyawan->nik,
        'nama' => $karyawan->nama,
        'pt' => 'GCNS',
        'departemen_id' => $departemen->id,
        'tanggal_masuk' => '2026-10-22',
        'status' => 'aktif',
    ])
        ->assertRedirect(route('hrd.karyawan.show', $karyawan));

    $this->assertDatabaseHas('karyawans', [
        'id' => $karyawan->id,
        'pt' => 'GCNS',
    ]);

    $this->get(route('hrd.karyawan.show', $karyawan))
        ->assertSee('GCNS');
});

it('permanently deletes an employee and all related data', function () {
    $hrd = User::factory()->create([
        'username' => 'hrd-delete-employee',
        'role' => 'hrd',
    ]);
    $employeeUser = User::factory()->create([
        'username' => 'employee-delete-test',
        'role' => 'karyawan',
    ]);
    $departemen = Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);
    $jabatan = Jabatan::create([
        'nama_jabatan' => 'Operator',
    ]);
    $karyawan = Karyawan::create([
        'user_id' => $employeeUser->id,
        'nik' => 'EMP-DELETE-1',
        'nama' => 'Karyawan Dihapus',
        'departemen_id' => $departemen->id,
        'jabatan_id' => $jabatan->id,
        'tanggal_masuk' => '2026-01-01',
        'status' => 'aktif',
    ]);
    $assessment = Assessment::create([
        'nama_assessment' => 'Assessment Karyawan',
        'jabatan_id' => $jabatan->id,
        'status' => 'draft',
    ]);
    $peserta = AssessmentPeserta::create([
        'assessment_id' => $assessment->id,
        'karyawan_id' => $karyawan->id,
        'status' => 'ditugaskan',
    ]);
    $hasilAssessment = HasilAssessment::create([
        'assessment_peserta_id' => $peserta->id,
        'nilai_akhir' => 80,
        'standar_nilai' => 70,
        'status' => 'lulus',
        'tanggal_ujian' => '2026-01-01 10:00:00',
    ]);
    $pengajuan = PengajuanPengembangan::create([
        'karyawan_id' => $karyawan->id,
        'hasil_assessment_id' => $hasilAssessment->id,
        'jenis_pengajuan' => 'kenaikan_jabatan',
        'tanggal_pengajuan' => '2026-01-02',
    ]);

    $this->actingAs($hrd)
        ->delete(route('hrd.karyawan.destroy', $karyawan))
        ->assertRedirect(route('hrd.karyawan.index'))
        ->assertSessionHas('success', 'Karyawan beserta seluruh akun dan data terkait berhasil dihapus.');

    $this->assertModelMissing($karyawan);
    $this->assertModelMissing($employeeUser);
    $this->assertDatabaseMissing('assessment_peserta', [
        'id' => $peserta->id,
    ]);
    $this->assertDatabaseMissing('hasil_assessments', [
        'id' => $hasilAssessment->id,
    ]);
    $this->assertDatabaseMissing('pengajuan_pengembangans', [
        'id' => $pengajuan->id,
    ]);

    foreach ([
        ['pengajuan_pengembangan_dihapus', $pengajuan->id],
        ['hasil_assessment_dihapus', $hasilAssessment->id],
        ['assessment_peserta_dihapus', $peserta->id],
        ['karyawan_dihapus', $karyawan->id],
    ] as [$eventType, $entityId]) {
        $event = BlockchainRecord::query()
            ->where('entity_type', $eventType)
            ->where('entity_id', $entityId)
            ->firstOrFail();

        expect($event->data_snapshot['source_deleted'])->toBeTrue();
    }

    expect(app(BlockchainService::class)->verifyDataIntegrity()['valid'])
        ->toBeTrue();
});

it('shows a confirmed delete action for each employee', function () {
    $hrd = User::factory()->create([
        'username' => 'hrd-delete-employee-view',
        'role' => 'hrd',
    ]);
    $employeeUser = User::factory()->create([
        'username' => 'employee-delete-view',
        'role' => 'karyawan',
    ]);
    $departemen = Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);
    $jabatan = Jabatan::create([
        'nama_jabatan' => 'Operator',
    ]);
    $karyawan = Karyawan::create([
        'user_id' => $employeeUser->id,
        'nik' => 'EMP-DELETE-2',
        'nama' => 'Karyawan Hapus',
        'departemen_id' => $departemen->id,
        'jabatan_id' => $jabatan->id,
        'tanggal_masuk' => '2026-01-01',
        'status' => 'aktif',
    ]);

    $this->actingAs($hrd)
        ->get(route('hrd.karyawan.index'))
        ->assertSee(route('hrd.karyawan.destroy', $karyawan))
        ->assertSee('name="_method" value="DELETE"', false)
        ->assertSee('Hapus karyawan ini beserta akun login', false);
});
