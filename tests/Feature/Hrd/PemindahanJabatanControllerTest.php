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

function createPemindahanJabatanFixture(
    string $statusHasil = 'lulus',
    string $statusPeserta = 'selesai',
    string $statusKaryawan = 'aktif',
    string $namaJabatanAsal = 'Crew',
    string $namaJabatanTujuan = 'Operator Loader',
    int $levelJabatanTujuan = 1,
    string $tipeJabatanTujuan = 'operasional',
    string $namaKaryawan = 'Karyawan Transfer'
): array {
    $departemen = Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);
    $jabatanAsal = Jabatan::create([
        'nama_jabatan' => $namaJabatanAsal,
        'tipe_jabatan' => 'operasional',
        'level_jabatan' => 1,
    ]);
    $jabatanTujuan = Jabatan::create([
        'nama_jabatan' => $namaJabatanTujuan,
        'tipe_jabatan' => $tipeJabatanTujuan,
        'level_jabatan' => $levelJabatanTujuan,
    ]);
    $user = User::factory()->create([
        'role' => 'karyawan',
    ]);
    $karyawan = Karyawan::create([
        'user_id' => $user->id,
        'nik' => 'EMP-TRANSFER-'.str_pad(
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
        'nama_assessment' => 'Assessment Pemindahan',
        'jabatan_id' => $jabatanTujuan->id,
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

it('shows only passing completed level one assessments for active crew without pending requests', function () {
    $hrd = User::factory()->create([
        'role' => 'hrd',
    ]);
    createPemindahanJabatanFixture(
        namaKaryawan: 'Karyawan Eligible'
    );
    $pending = createPemindahanJabatanFixture(
        namaJabatanTujuan: 'Operator Loader Pending',
        namaKaryawan: 'Karyawan Pending'
    );
    PengajuanPengembangan::create([
        'karyawan_id' => $pending['karyawan']->id,
        'hasil_assessment_id' => $pending['hasilAssessment']->id,
        'jenis_pengajuan' => 'pemindahan_jabatan',
        'jabatan_asal_id' => $pending['jabatanAsal']->id,
        'jabatan_tujuan_id' => $pending['jabatanTujuan']->id,
        'status' => 'diproses',
        'tanggal_pengajuan' => '2026-01-02',
    ]);
    createPemindahanJabatanFixture(
        statusHasil: 'tidak_lulus',
        namaJabatanTujuan: 'Operator Forklift',
        namaKaryawan: 'Karyawan Tidak Lulus'
    );
    createPemindahanJabatanFixture(
        statusPeserta: 'sedang_mengerjakan',
        namaJabatanTujuan: 'Operator Excavator',
        namaKaryawan: 'Karyawan Belum Selesai'
    );
    createPemindahanJabatanFixture(
        statusKaryawan: 'nonaktif',
        namaJabatanTujuan: 'Operator Crane',
        namaKaryawan: 'Karyawan Nonaktif'
    );
    createPemindahanJabatanFixture(
        namaJabatanAsal: 'Operator Loader Level 1',
        namaJabatanTujuan: 'Operator Loader Level 2',
        levelJabatanTujuan: 2,
        namaKaryawan: 'Karyawan Bukan Crew'
    );
    createPemindahanJabatanFixture(
        namaJabatanTujuan: 'Supervisor',
        tipeJabatanTujuan: 'struktural',
        namaKaryawan: 'Karyawan Struktural'
    );

    $this->actingAs($hrd)
        ->get(route('hrd.pemindahan-jabatan.create'))
        ->assertSee('Karyawan Eligible')
        ->assertSee('Operator Loader')
        ->assertDontSee('Karyawan Pending')
        ->assertDontSee('name="jabatan_tujuan_id"', false)
        ->assertDontSee('Operator Forklift')
        ->assertDontSee('Operator Excavator')
        ->assertDontSee('Operator Crane')
        ->assertDontSee('Operator Loader Level 2')
        ->assertDontSee('Supervisor');
});

it('creates a transfer request using the passing assessment destination', function () {
    $hrd = User::factory()->create([
        'role' => 'hrd',
    ]);
    $fixture = createPemindahanJabatanFixture();

    $this->actingAs($hrd)
        ->post(route('hrd.pemindahan-jabatan.store'), [
            'hasil_assessment_id' => $fixture['hasilAssessment']->id,
            'tanggal_pengajuan' => '2026-01-02',
            'catatan' => 'Lulus assessment jabatan tujuan.',
        ])
        ->assertRedirect(route('hrd.pemindahan-jabatan.index'))
        ->assertSessionHas(
            'success',
            'Pengajuan pemindahan jabatan berhasil dibuat.'
        );

    $this->assertDatabaseHas('pengajuan_pengembangans', [
        'karyawan_id' => $fixture['karyawan']->id,
        'hasil_assessment_id' => $fixture['hasilAssessment']->id,
        'jenis_pengajuan' => 'pemindahan_jabatan',
        'jabatan_asal_id' => $fixture['jabatanAsal']->id,
        'jabatan_tujuan_id' => $fixture['jabatanTujuan']->id,
        'status' => 'diajukan',
    ]);
    $this->assertDatabaseHas('blockchain_records', [
        'entity_type' => 'pengajuan_pengembangan',
        'entity_id' => PengajuanPengembangan::query()
            ->where('jenis_pengajuan', 'pemindahan_jabatan')
            ->value('id'),
    ]);
});

it('rejects a transfer request when the employee does not follow the crew to level one flow', function () {
    $hrd = User::factory()->create([
        'role' => 'hrd',
    ]);
    $fixture = createPemindahanJabatanFixture(
        namaJabatanAsal: 'Operator Loader Level 1',
        namaJabatanTujuan: 'Operator Loader Level 2',
        levelJabatanTujuan: 2
    );

    $this->actingAs($hrd)
        ->from(route('hrd.pemindahan-jabatan.create'))
        ->post(route('hrd.pemindahan-jabatan.store'), [
            'hasil_assessment_id' => $fixture['hasilAssessment']->id,
            'tanggal_pengajuan' => '2026-01-02',
        ])
        ->assertRedirect(route('hrd.pemindahan-jabatan.create'))
        ->assertSessionHasErrors([
            'hasil_assessment_id' => 'Pemindahan jabatan hanya dapat diajukan oleh karyawan yang saat ini menjabat sebagai Crew.',
        ]);

    $this->assertDatabaseMissing('pengajuan_pengembangans', [
        'hasil_assessment_id' => $fixture['hasilAssessment']->id,
        'jenis_pengajuan' => 'pemindahan_jabatan',
    ]);
});

it('rejects a transfer request when the assessment is not for an operational level one position', function () {
    $hrd = User::factory()->create([
        'role' => 'hrd',
    ]);
    $fixture = createPemindahanJabatanFixture(
        namaJabatanTujuan: 'Operator Loader Level 2',
        levelJabatanTujuan: 2
    );

    $this->actingAs($hrd)
        ->from(route('hrd.pemindahan-jabatan.create'))
        ->post(route('hrd.pemindahan-jabatan.store'), [
            'hasil_assessment_id' => $fixture['hasilAssessment']->id,
            'tanggal_pengajuan' => '2026-01-02',
        ])
        ->assertRedirect(route('hrd.pemindahan-jabatan.create'))
        ->assertSessionHasErrors([
            'hasil_assessment_id' => 'Assessment pemindahan harus ditujukan untuk jabatan operasional Level 1 selain Crew.',
        ]);

    $this->assertDatabaseMissing('pengajuan_pengembangans', [
        'hasil_assessment_id' => $fixture['hasilAssessment']->id,
        'jenis_pengajuan' => 'pemindahan_jabatan',
    ]);
});

it('updates the employee position and history when approving a transfer request', function () {
    $this->travelTo(Carbon::parse('2026-02-02 12:00:00'));

    $hrd = User::factory()->create([
        'role' => 'hrd',
    ]);
    $fixture = createPemindahanJabatanFixture();
    $pengajuan = PengajuanPengembangan::create([
        'karyawan_id' => $fixture['karyawan']->id,
        'hasil_assessment_id' => $fixture['hasilAssessment']->id,
        'jenis_pengajuan' => 'pemindahan_jabatan',
        'jabatan_asal_id' => $fixture['jabatanAsal']->id,
        'jabatan_tujuan_id' => $fixture['jabatanTujuan']->id,
        'status' => 'diajukan',
        'tanggal_pengajuan' => '2026-01-02',
    ]);

    $this->actingAs($hrd)
        ->post(route('hrd.pemindahan-jabatan.approve', $pengajuan))
        ->assertRedirect()
        ->assertSessionHas(
            'success',
            'Pemindahan jabatan berhasil disetujui dan jabatan karyawan telah diperbarui.'
        );

    $this->assertDatabaseHas('karyawans', [
        'id' => $fixture['karyawan']->id,
        'jabatan_id' => $fixture['jabatanTujuan']->id,
    ]);
    $this->assertDatabaseHas('pengajuan_pengembangans', [
        'id' => $pengajuan->id,
        'status' => 'disetujui',
    ]);
    expect($pengajuan->fresh()->tanggal_keputusan->toDateString())
        ->toBe('2026-02-02');

    $this->assertDatabaseHas('riwayat_jabatans', [
        'karyawan_id' => $fixture['karyawan']->id,
        'jabatan_id' => $fixture['jabatanTujuan']->id,
        'pengajuan_pengembangan_id' => $pengajuan->id,
        'hasil_assessment_id' => $fixture['hasilAssessment']->id,
        'jenis_perubahan' => 'pemindahan',
    ]);
    $this->assertDatabaseHas('blockchain_records', [
        'entity_type' => 'keputusan_pemindahan_jabatan',
        'entity_id' => $pengajuan->id,
    ]);
});

it('does not approve a transfer request when its assessment is not completed', function () {
    $hrd = User::factory()->create([
        'role' => 'hrd',
    ]);
    $fixture = createPemindahanJabatanFixture(
        statusPeserta: 'sedang_mengerjakan'
    );
    $pengajuan = PengajuanPengembangan::create([
        'karyawan_id' => $fixture['karyawan']->id,
        'hasil_assessment_id' => $fixture['hasilAssessment']->id,
        'jenis_pengajuan' => 'pemindahan_jabatan',
        'jabatan_asal_id' => $fixture['jabatanAsal']->id,
        'jabatan_tujuan_id' => $fixture['jabatanTujuan']->id,
        'status' => 'diajukan',
        'tanggal_pengajuan' => '2026-01-02',
    ]);

    $this->actingAs($hrd)
        ->post(route('hrd.pemindahan-jabatan.approve', $pengajuan))
        ->assertRedirect()
        ->assertSessionHas(
            'error',
            'Data peserta assessment tidak sesuai dengan pengajuan.'
        );

    $this->assertDatabaseHas('karyawans', [
        'id' => $fixture['karyawan']->id,
        'jabatan_id' => $fixture['jabatanAsal']->id,
    ]);
    $this->assertDatabaseHas('pengajuan_pengembangans', [
        'id' => $pengajuan->id,
        'status' => 'diajukan',
    ]);
    $this->assertDatabaseMissing('blockchain_records', [
        'entity_type' => 'keputusan_pemindahan_jabatan',
        'entity_id' => $pengajuan->id,
    ]);
});

it('records the transfer processing status with its HRD actor', function () {
    $hrd = User::factory()->create([
        'role' => 'hrd',
    ]);
    $fixture = createPemindahanJabatanFixture();
    $pengajuan = PengajuanPengembangan::create([
        'karyawan_id' => $fixture['karyawan']->id,
        'hasil_assessment_id' => $fixture['hasilAssessment']->id,
        'jenis_pengajuan' => 'pemindahan_jabatan',
        'jabatan_asal_id' => $fixture['jabatanAsal']->id,
        'jabatan_tujuan_id' => $fixture['jabatanTujuan']->id,
        'status' => 'diajukan',
        'tanggal_pengajuan' => '2026-01-02',
    ]);

    $this->actingAs($hrd)
        ->post(route('hrd.pemindahan-jabatan.process', $pengajuan))
        ->assertRedirect()
        ->assertSessionHas(
            'success',
            'Pengajuan pemindahan jabatan sedang diproses.'
        );

    $event = BlockchainRecord::query()
        ->where('entity_type', 'status_pengajuan_pengembangan')
        ->where('entity_id', $pengajuan->id)
        ->firstOrFail();

    expect($event->data_snapshot['actor_user_id'])->toBe($hrd->id);
    expect($event->data_snapshot['source_data']['status'])->toBe('diproses');
});
