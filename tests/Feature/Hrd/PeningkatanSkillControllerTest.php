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
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * @return array{
 *     assessment: Assessment,
 *     hasilAssessment: HasilAssessment,
 *     jabatanAsal: Jabatan,
 *     jabatanTujuan: Jabatan,
 *     karyawan: Karyawan,
 *     peserta: AssessmentPeserta
 * }
 */
function createPeningkatanSkillFixture(
    string $statusHasil = 'lulus',
    string $statusPeserta = 'selesai',
    string $statusKaryawan = 'aktif',
    string $namaJabatanAsal = 'Operator Loader LEV 1',
    int $levelJabatanAsal = 1,
    string $namaJabatanTujuan = 'Operator Loader LEVEL 2',
    int $levelJabatanTujuan = 2,
    string $namaKaryawan = 'Karyawan Skill'
): array {
    $departemen = Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);
    $jabatanAsal = Jabatan::create([
        'nama_jabatan' => $namaJabatanAsal,
        'tipe_jabatan' => 'operasional',
        'level_jabatan' => $levelJabatanAsal,
    ]);
    $jabatanTujuan = Jabatan::create([
        'nama_jabatan' => $namaJabatanTujuan,
        'tipe_jabatan' => 'operasional',
        'level_jabatan' => $levelJabatanTujuan,
    ]);
    $user = User::factory()->create([
        'role' => 'karyawan',
    ]);
    $karyawan = Karyawan::create([
        'user_id' => $user->id,
        'nik' => 'EMP-SKILL-'.str_pad(
            (string) (Karyawan::count() + 1),
            3,
            '0',
            STR_PAD_LEFT
        ),
        'nama' => $namaKaryawan,
        'departemen_id' => $departemen->id,
        'jabatan_id' => $jabatanAsal->id,
        'tanggal_masuk' => '2024-01-01',
        'status' => $statusKaryawan,
    ]);
    $assessment = Assessment::create([
        'nama_assessment' => 'Assessment '.$namaJabatanTujuan,
        'jabatan_id' => $jabatanTujuan->id,
        'departemen_id' => $departemen->id,
        'standar_nilai' => 80,
        'durasi' => 60,
        'status' => 'selesai',
    ]);
    $peserta = AssessmentPeserta::create([
        'assessment_id' => $assessment->id,
        'karyawan_id' => $karyawan->id,
        'status' => $statusPeserta,
    ]);
    $hasilAssessment = HasilAssessment::create([
        'assessment_peserta_id' => $peserta->id,
        'nilai_akhir' => 90,
        'standar_nilai' => 80,
        'status' => $statusHasil,
        'tanggal_ujian' => '2026-01-01 10:00:00',
    ]);

    return compact(
        'assessment',
        'hasilAssessment',
        'jabatanAsal',
        'jabatanTujuan',
        'karyawan',
        'peserta'
    );
}

it('shows only completed passing assessments for the next job level', function () {
    $hrd = User::factory()->create([
        'role' => 'hrd',
    ]);
    createPeningkatanSkillFixture(
        namaKaryawan: 'Karyawan Eligible'
    );
    $pending = createPeningkatanSkillFixture(
        namaJabatanTujuan: 'Operator Loader LEVEL 2',
        namaKaryawan: 'Karyawan Pending'
    );
    PengajuanPengembangan::create([
        'karyawan_id' => $pending['karyawan']->id,
        'hasil_assessment_id' => $pending['hasilAssessment']->id,
        'jenis_pengajuan' => 'peningkatan_skill',
        'jabatan_asal_id' => $pending['jabatanAsal']->id,
        'jabatan_tujuan_id' => $pending['jabatanTujuan']->id,
        'status' => 'diproses',
        'tanggal_pengajuan' => '2026-01-02',
    ]);
    createPeningkatanSkillFixture(
        namaJabatanAsal: 'Operator Loader LEVEL 1',
        namaJabatanTujuan: 'Operator Forklift LEVEL 2',
        namaKaryawan: 'Karyawan Jabatan Berbeda'
    );
    createPeningkatanSkillFixture(
        statusPeserta: 'sedang_mengerjakan',
        namaKaryawan: 'Karyawan Belum Selesai'
    );
    createPeningkatanSkillFixture(
        statusKaryawan: 'nonaktif',
        namaKaryawan: 'Karyawan Nonaktif'
    );
    createPeningkatanSkillFixture(
        namaJabatanAsal: 'Operator Loader LEVEL 5',
        levelJabatanAsal: 5,
        namaJabatanTujuan: 'Operator Loader LEVEL 6',
        levelJabatanTujuan: 6,
        namaKaryawan: 'Karyawan Level Maksimal'
    );

    $this->actingAs($hrd)
        ->get(route('hrd.peningkatan-skill.create'))
        ->assertSee('Karyawan Eligible')
        ->assertSee('Operator Loader LEVEL 2')
        ->assertDontSee('Karyawan Pending')
        ->assertDontSee('Karyawan Jabatan Berbeda')
        ->assertDontSee('Karyawan Belum Selesai')
        ->assertDontSee('Karyawan Nonaktif')
        ->assertDontSee('Karyawan Level Maksimal');
});

it('creates a skill progression request with the exact next level and stores its level values', function () {
    $hrd = User::factory()->create([
        'role' => 'hrd',
    ]);
    $fixture = createPeningkatanSkillFixture();

    $this->actingAs($hrd)
        ->post(route('hrd.peningkatan-skill.store'), [
            'hasil_assessment_id' => $fixture['hasilAssessment']->id,
            'tanggal_pengajuan' => '2026-01-02',
            'catatan' => 'Lulus assessment jabatan level berikutnya.',
        ])
        ->assertRedirect(route('hrd.peningkatan-skill.index'))
        ->assertSessionHas(
            'success',
            'Pengajuan peningkatan skill berhasil dibuat.'
        );

    $pengajuan = PengajuanPengembangan::query()
        ->where('jenis_pengajuan', 'peningkatan_skill')
        ->firstOrFail();

    $this->assertDatabaseHas('pengajuan_pengembangans', [
        'id' => $pengajuan->id,
        'karyawan_id' => $fixture['karyawan']->id,
        'hasil_assessment_id' => $fixture['hasilAssessment']->id,
        'jabatan_asal_id' => $fixture['jabatanAsal']->id,
        'jabatan_tujuan_id' => $fixture['jabatanTujuan']->id,
        'level_sebelum' => 1,
        'level_sesudah' => 2,
        'status' => 'diajukan',
    ]);
    $this->assertDatabaseHas('blockchain_records', [
        'entity_type' => 'pengajuan_pengembangan',
        'entity_id' => $pengajuan->id,
    ]);
});

it('rejects an assessment for a different job progression', function () {
    $hrd = User::factory()->create([
        'role' => 'hrd',
    ]);
    $fixture = createPeningkatanSkillFixture(
        namaJabatanAsal: 'Operator Forklift LEV 1',
        namaJabatanTujuan: 'Operator Forklift LEVEL 2'
    );
    $jabatanTujuan = Jabatan::create([
        'nama_jabatan' => 'Operator Loader LEVEL 2',
        'tipe_jabatan' => 'operasional',
        'level_jabatan' => 2,
    ]);
    $fixture['assessment']->update([
        'jabatan_id' => $jabatanTujuan->id,
    ]);

    $this->actingAs($hrd)
        ->from(route('hrd.peningkatan-skill.create'))
        ->post(route('hrd.peningkatan-skill.store'), [
            'hasil_assessment_id' => $fixture['hasilAssessment']->id,
            'tanggal_pengajuan' => '2026-01-02',
        ])
        ->assertRedirect(route('hrd.peningkatan-skill.create'))
        ->assertSessionHasErrors([
            'hasil_assessment_id' => 'Assessment yang digunakan harus untuk jabatan operasional satu level di atas jabatan karyawan saat ini dan pada jalur jabatan yang sama.',
        ]);

    $this->assertDatabaseMissing('pengajuan_pengembangans', [
        'hasil_assessment_id' => $fixture['hasilAssessment']->id,
        'jenis_pengajuan' => 'peningkatan_skill',
    ]);
});

it('rejects a skill progression while another development request is pending', function () {
    $hrd = User::factory()->create([
        'role' => 'hrd',
    ]);
    $fixture = createPeningkatanSkillFixture();
    PengajuanPengembangan::create([
        'karyawan_id' => $fixture['karyawan']->id,
        'hasil_assessment_id' => $fixture['hasilAssessment']->id,
        'jenis_pengajuan' => 'pemindahan_jabatan',
        'jabatan_asal_id' => $fixture['jabatanAsal']->id,
        'jabatan_tujuan_id' => $fixture['jabatanTujuan']->id,
        'status' => 'diproses',
        'tanggal_pengajuan' => '2026-01-02',
    ]);

    $this->actingAs($hrd)
        ->from(route('hrd.peningkatan-skill.create'))
        ->post(route('hrd.peningkatan-skill.store'), [
            'hasil_assessment_id' => $fixture['hasilAssessment']->id,
            'tanggal_pengajuan' => '2026-01-03',
        ])
        ->assertRedirect(route('hrd.peningkatan-skill.create'))
        ->assertSessionHasErrors([
            'hasil_assessment_id' => 'Karyawan ini masih memiliki pengajuan pengembangan yang belum selesai diputuskan.',
        ]);

    $this->assertDatabaseMissing('pengajuan_pengembangans', [
        'karyawan_id' => $fixture['karyawan']->id,
        'jenis_pengajuan' => 'peningkatan_skill',
    ]);
});

it('approves a next-level progression and records valid position history', function () {
    $this->travelTo(Carbon::parse('2026-02-02 12:00:00'));

    $hrd = User::factory()->create([
        'role' => 'hrd',
    ]);
    $fixture = createPeningkatanSkillFixture();
    $pengajuan = PengajuanPengembangan::create([
        'karyawan_id' => $fixture['karyawan']->id,
        'hasil_assessment_id' => $fixture['hasilAssessment']->id,
        'jenis_pengajuan' => 'peningkatan_skill',
        'jabatan_asal_id' => $fixture['jabatanAsal']->id,
        'jabatan_tujuan_id' => $fixture['jabatanTujuan']->id,
        'status' => 'diajukan',
        'tanggal_pengajuan' => '2026-01-02',
    ]);

    $this->actingAs($hrd)
        ->post(route('hrd.peningkatan-skill.approve', $pengajuan))
        ->assertRedirect()
        ->assertSessionHas(
            'success',
            'Peningkatan skill berhasil disetujui dan level skill karyawan telah diperbarui.'
        );

    $this->assertDatabaseHas('karyawans', [
        'id' => $fixture['karyawan']->id,
        'jabatan_id' => $fixture['jabatanTujuan']->id,
    ]);
    $this->assertDatabaseHas('pengajuan_pengembangans', [
        'id' => $pengajuan->id,
        'level_sebelum' => 1,
        'level_sesudah' => 2,
        'status' => 'disetujui',
    ]);
    expect($pengajuan->fresh()->tanggal_keputusan->toDateString())
        ->toBe('2026-02-02');

    $this->assertDatabaseHas('riwayat_jabatans', [
        'karyawan_id' => $fixture['karyawan']->id,
        'jabatan_id' => $fixture['jabatanTujuan']->id,
        'pengajuan_pengembangan_id' => $pengajuan->id,
        'hasil_assessment_id' => $fixture['hasilAssessment']->id,
        'jenis_perubahan' => 'kenaikan',
    ]);
    $this->assertDatabaseHas('blockchain_records', [
        'entity_type' => 'keputusan_peningkatan_skill',
        'entity_id' => $pengajuan->id,
    ]);
});

it('does not approve a skill progression when its assessment is unfinished', function () {
    $hrd = User::factory()->create([
        'role' => 'hrd',
    ]);
    $fixture = createPeningkatanSkillFixture(
        statusPeserta: 'sedang_mengerjakan'
    );
    $pengajuan = PengajuanPengembangan::create([
        'karyawan_id' => $fixture['karyawan']->id,
        'hasil_assessment_id' => $fixture['hasilAssessment']->id,
        'jenis_pengajuan' => 'peningkatan_skill',
        'jabatan_asal_id' => $fixture['jabatanAsal']->id,
        'jabatan_tujuan_id' => $fixture['jabatanTujuan']->id,
        'status' => 'diajukan',
        'tanggal_pengajuan' => '2026-01-02',
    ]);

    $this->actingAs($hrd)
        ->post(route('hrd.peningkatan-skill.approve', $pengajuan))
        ->assertRedirect()
        ->assertSessionHas(
            'error',
            'Data peserta assessment tidak sesuai dengan pengajuan atau assessment belum selesai.'
        );

    $this->assertDatabaseHas('karyawans', [
        'id' => $fixture['karyawan']->id,
        'jabatan_id' => $fixture['jabatanAsal']->id,
    ]);
    $this->assertDatabaseHas('pengajuan_pengembangans', [
        'id' => $pengajuan->id,
        'status' => 'diajukan',
    ]);
    $this->assertDatabaseMissing('riwayat_jabatans', [
        'pengajuan_pengembangan_id' => $pengajuan->id,
    ]);
});

it('does not approve a skill progression after the employee has changed jobs', function () {
    $hrd = User::factory()->create([
        'role' => 'hrd',
    ]);
    $fixture = createPeningkatanSkillFixture();
    $jabatanBaru = Jabatan::create([
        'nama_jabatan' => 'Teknisi LEVEL 1',
        'tipe_jabatan' => 'operasional',
        'level_jabatan' => 1,
    ]);
    $fixture['karyawan']->update([
        'jabatan_id' => $jabatanBaru->id,
    ]);
    $pengajuan = PengajuanPengembangan::create([
        'karyawan_id' => $fixture['karyawan']->id,
        'hasil_assessment_id' => $fixture['hasilAssessment']->id,
        'jenis_pengajuan' => 'peningkatan_skill',
        'jabatan_asal_id' => $fixture['jabatanAsal']->id,
        'jabatan_tujuan_id' => $fixture['jabatanTujuan']->id,
        'status' => 'diajukan',
        'tanggal_pengajuan' => '2026-01-02',
    ]);

    $this->actingAs($hrd)
        ->post(route('hrd.peningkatan-skill.approve', $pengajuan))
        ->assertRedirect()
        ->assertSessionHas(
            'error',
            'Jabatan karyawan sudah berubah sejak pengajuan dibuat. Periksa kembali dan buat pengajuan baru jika diperlukan.'
        );

    $this->assertDatabaseHas('karyawans', [
        'id' => $fixture['karyawan']->id,
        'jabatan_id' => $jabatanBaru->id,
    ]);
    $this->assertDatabaseHas('pengajuan_pengembangans', [
        'id' => $pengajuan->id,
        'status' => 'diajukan',
    ]);
    $this->assertDatabaseMissing('riwayat_jabatans', [
        'pengajuan_pengembangan_id' => $pengajuan->id,
    ]);
    $this->assertDatabaseMissing('blockchain_records', [
        'entity_type' => 'keputusan_peningkatan_skill',
        'entity_id' => $pengajuan->id,
    ]);
});

it('records the skill processing status with its HRD actor', function () {
    $hrd = User::factory()->create([
        'role' => 'hrd',
    ]);
    $fixture = createPeningkatanSkillFixture();
    $pengajuan = PengajuanPengembangan::create([
        'karyawan_id' => $fixture['karyawan']->id,
        'hasil_assessment_id' => $fixture['hasilAssessment']->id,
        'jenis_pengajuan' => 'peningkatan_skill',
        'jabatan_asal_id' => $fixture['jabatanAsal']->id,
        'jabatan_tujuan_id' => $fixture['jabatanTujuan']->id,
        'level_sebelum' => 1,
        'level_sesudah' => 2,
        'status' => 'diajukan',
        'tanggal_pengajuan' => '2026-01-02',
    ]);

    $this->actingAs($hrd)
        ->post(route('hrd.peningkatan-skill.process', $pengajuan))
        ->assertRedirect()
        ->assertSessionHas(
            'success',
            'Pengajuan peningkatan skill sedang diproses.'
        );

    $event = BlockchainRecord::query()
        ->where('entity_type', 'status_pengajuan_pengembangan')
        ->where('entity_id', $pengajuan->id)
        ->firstOrFail();

    expect($event->data_snapshot['actor_user_id'])->toBe($hrd->id);
    expect($event->data_snapshot['source_data']['status'])->toBe('diproses');
});
