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
function createKenaikanJabatanFixture(
    string $tipeJabatanAsal = 'operasional',
    ?int $urutanAsal = null,
    int $urutanTujuan = 1,
    string $statusHasil = 'lulus',
    string $statusPeserta = 'selesai',
    string $statusKaryawan = 'aktif',
    string $namaKaryawan = 'Karyawan Promosi'
): array {
    $departemen = Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);
    $jabatanAsal = Jabatan::create([
        'nama_jabatan' => $tipeJabatanAsal === 'struktural'
            ? 'Jabatan Struktural '.$urutanAsal
            : 'Operator Loader Level 1',
        'tipe_jabatan' => $tipeJabatanAsal,
        'level_jabatan' => 1,
        'urutan_struktural' => $urutanAsal,
    ]);
    $jabatanTujuan = Jabatan::create([
        'nama_jabatan' => 'Jabatan Tujuan Urutan '.$urutanTujuan,
        'tipe_jabatan' => 'struktural',
        'level_jabatan' => 1,
        'urutan_struktural' => $urutanTujuan,
    ]);
    $user = User::factory()->create([
        'role' => 'karyawan',
    ]);
    $karyawan = Karyawan::create([
        'user_id' => $user->id,
        'nik' => 'EMP-PROMOTION-'.str_pad(
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
        'nama_assessment' => 'Assessment '.$jabatanTujuan->nama_jabatan,
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

it('shows only passing completed assessments for the next structural step', function () {
    $hrd = User::factory()->create([
        'role' => 'hrd',
    ]);
    createKenaikanJabatanFixture(
        namaKaryawan: 'Karyawan Operasional Eligible'
    );
    createKenaikanJabatanFixture(
        tipeJabatanAsal: 'struktural',
        urutanAsal: 1,
        urutanTujuan: 2,
        namaKaryawan: 'Karyawan Struktural Eligible'
    );
    $pending = createKenaikanJabatanFixture(
        namaKaryawan: 'Karyawan Pending'
    );
    PengajuanPengembangan::create([
        'karyawan_id' => $pending['karyawan']->id,
        'hasil_assessment_id' => $pending['hasilAssessment']->id,
        'jenis_pengajuan' => 'kenaikan_jabatan',
        'jabatan_asal_id' => $pending['jabatanAsal']->id,
        'jabatan_tujuan_id' => $pending['jabatanTujuan']->id,
        'status' => 'diproses',
        'tanggal_pengajuan' => '2026-01-02',
    ]);
    createKenaikanJabatanFixture(
        tipeJabatanAsal: 'struktural',
        urutanAsal: 1,
        urutanTujuan: 3,
        namaKaryawan: 'Karyawan Tahap Terlewat'
    );
    createKenaikanJabatanFixture(
        statusPeserta: 'sedang_mengerjakan',
        namaKaryawan: 'Karyawan Assessment Belum Selesai'
    );
    createKenaikanJabatanFixture(
        statusKaryawan: 'nonaktif',
        namaKaryawan: 'Karyawan Nonaktif'
    );
    createKenaikanJabatanFixture(
        tipeJabatanAsal: 'struktural',
        urutanAsal: 6,
        urutanTujuan: 1,
        namaKaryawan: 'Karyawan Urutan Maksimal'
    );

    $this->actingAs($hrd)
        ->get(route('hrd.kenaikan-jabatan.create'))
        ->assertSee('Karyawan Operasional Eligible')
        ->assertSee('Karyawan Struktural Eligible')
        ->assertSee('Jabatan Tujuan Urutan 1')
        ->assertSee('Jabatan Tujuan Urutan 2')
        ->assertDontSee('Karyawan Pending')
        ->assertDontSee('Karyawan Tahap Terlewat')
        ->assertDontSee('Karyawan Assessment Belum Selesai')
        ->assertDontSee('Karyawan Nonaktif')
        ->assertDontSee('Karyawan Urutan Maksimal')
        ->assertDontSee('name="jabatan_tujuan_id"', false);
});

it('creates an operational employee promotion to structural step one from its passing assessment', function () {
    $hrd = User::factory()->create([
        'role' => 'hrd',
    ]);
    $fixture = createKenaikanJabatanFixture();

    $this->actingAs($hrd)
        ->post(route('hrd.kenaikan-jabatan.store'), [
            'hasil_assessment_id' => $fixture['hasilAssessment']->id,
            'tanggal_pengajuan' => '2026-01-02',
        ])
        ->assertRedirect(route('hrd.kenaikan-jabatan.index'))
        ->assertSessionHas(
            'success',
            'Pengajuan kenaikan jabatan berhasil dibuat.'
        );

    $this->assertDatabaseHas('pengajuan_pengembangans', [
        'karyawan_id' => $fixture['karyawan']->id,
        'hasil_assessment_id' => $fixture['hasilAssessment']->id,
        'jenis_pengajuan' => 'kenaikan_jabatan',
        'jabatan_asal_id' => $fixture['jabatanAsal']->id,
        'jabatan_tujuan_id' => $fixture['jabatanTujuan']->id,
        'status' => 'diajukan',
    ]);
    $this->assertDatabaseHas('blockchain_records', [
        'entity_type' => 'pengajuan_pengembangan',
        'entity_id' => PengajuanPengembangan::query()
            ->where('jenis_pengajuan', 'kenaikan_jabatan')
            ->value('id'),
    ]);
});

it('rejects a promotion assessment that skips a structural step', function () {
    $hrd = User::factory()->create([
        'role' => 'hrd',
    ]);
    $fixture = createKenaikanJabatanFixture(
        tipeJabatanAsal: 'struktural',
        urutanAsal: 1,
        urutanTujuan: 3
    );

    $this->actingAs($hrd)
        ->from(route('hrd.kenaikan-jabatan.create'))
        ->post(route('hrd.kenaikan-jabatan.store'), [
            'hasil_assessment_id' => $fixture['hasilAssessment']->id,
            'tanggal_pengajuan' => '2026-01-02',
        ])
        ->assertRedirect(route('hrd.kenaikan-jabatan.create'))
        ->assertSessionHasErrors([
            'hasil_assessment_id' => 'Assessment harus ditujukan untuk tahap struktural berikutnya sesuai jabatan karyawan saat ini.',
        ]);

    $this->assertDatabaseMissing('pengajuan_pengembangans', [
        'hasil_assessment_id' => $fixture['hasilAssessment']->id,
        'jenis_pengajuan' => 'kenaikan_jabatan',
    ]);
});

it('marks a new promotion request as being processed', function () {
    $hrd = User::factory()->create([
        'role' => 'hrd',
    ]);
    $fixture = createKenaikanJabatanFixture();
    $pengajuan = PengajuanPengembangan::create([
        'karyawan_id' => $fixture['karyawan']->id,
        'hasil_assessment_id' => $fixture['hasilAssessment']->id,
        'jenis_pengajuan' => 'kenaikan_jabatan',
        'jabatan_asal_id' => $fixture['jabatanAsal']->id,
        'jabatan_tujuan_id' => $fixture['jabatanTujuan']->id,
        'status' => 'diajukan',
        'tanggal_pengajuan' => '2026-01-02',
    ]);

    $this->actingAs($hrd)
        ->post(route('hrd.kenaikan-jabatan.process', $pengajuan))
        ->assertRedirect()
        ->assertSessionHas(
            'success',
            'Pengajuan kenaikan jabatan sedang diproses.'
        );

    $this->assertDatabaseHas('pengajuan_pengembangans', [
        'id' => $pengajuan->id,
        'status' => 'diproses',
    ]);

    $event = BlockchainRecord::query()
        ->where('entity_type', 'status_pengajuan_pengembangan')
        ->where('entity_id', $pengajuan->id)
        ->firstOrFail();

    expect($event->data_snapshot['actor_user_id'])->toBe($hrd->id);
    expect($event->data_snapshot['source_data']['status'])->toBe('diproses');
});

it('approves the next structural step and records the promotion history', function () {
    $this->travelTo(Carbon::parse('2026-02-02 12:00:00'));

    $hrd = User::factory()->create([
        'role' => 'hrd',
    ]);
    $fixture = createKenaikanJabatanFixture(
        tipeJabatanAsal: 'struktural',
        urutanAsal: 1,
        urutanTujuan: 2
    );
    $pengajuan = PengajuanPengembangan::create([
        'karyawan_id' => $fixture['karyawan']->id,
        'hasil_assessment_id' => $fixture['hasilAssessment']->id,
        'jenis_pengajuan' => 'kenaikan_jabatan',
        'jabatan_asal_id' => $fixture['jabatanAsal']->id,
        'jabatan_tujuan_id' => $fixture['jabatanTujuan']->id,
        'status' => 'diajukan',
        'tanggal_pengajuan' => '2026-01-02',
    ]);

    $this->actingAs($hrd)
        ->post(route('hrd.kenaikan-jabatan.approve', $pengajuan))
        ->assertRedirect()
        ->assertSessionHas(
            'success',
            'Kenaikan jabatan berhasil disetujui dan jabatan karyawan telah diperbarui.'
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
        'jenis_perubahan' => 'kenaikan',
    ]);
    $this->assertDatabaseHas('blockchain_records', [
        'entity_type' => 'keputusan_kenaikan_jabatan',
        'entity_id' => $pengajuan->id,
    ]);
});

it('rejects a promotion request and records the decision', function () {
    $this->travelTo(Carbon::parse('2026-02-02 12:00:00'));

    $hrd = User::factory()->create([
        'role' => 'hrd',
    ]);
    $fixture = createKenaikanJabatanFixture();
    $pengajuan = PengajuanPengembangan::create([
        'karyawan_id' => $fixture['karyawan']->id,
        'hasil_assessment_id' => $fixture['hasilAssessment']->id,
        'jenis_pengajuan' => 'kenaikan_jabatan',
        'jabatan_asal_id' => $fixture['jabatanAsal']->id,
        'jabatan_tujuan_id' => $fixture['jabatanTujuan']->id,
        'status' => 'diajukan',
        'tanggal_pengajuan' => '2026-01-02',
    ]);

    $this->actingAs($hrd)
        ->post(route('hrd.kenaikan-jabatan.reject', $pengajuan), [
            'catatan' => 'Belum memenuhi persyaratan.',
        ])
        ->assertRedirect()
        ->assertSessionHas(
            'success',
            'Pengajuan kenaikan jabatan telah ditolak dan keputusan telah dicatat ke blockchain.'
        );

    $this->assertDatabaseHas('pengajuan_pengembangans', [
        'id' => $pengajuan->id,
        'status' => 'ditolak',
        'catatan' => 'Belum memenuhi persyaratan.',
    ]);
    expect($pengajuan->fresh()->tanggal_keputusan->toDateString())
        ->toBe('2026-02-02');

    $this->assertDatabaseHas('blockchain_records', [
        'entity_type' => 'keputusan_kenaikan_jabatan',
        'entity_id' => $pengajuan->id,
    ]);
});

it('does not approve a promotion after the employee has changed jobs', function () {
    $hrd = User::factory()->create([
        'role' => 'hrd',
    ]);
    $fixture = createKenaikanJabatanFixture();
    $jabatanBaru = Jabatan::create([
        'nama_jabatan' => 'Teknisi',
        'tipe_jabatan' => 'operasional',
    ]);
    $fixture['karyawan']->update([
        'jabatan_id' => $jabatanBaru->id,
    ]);
    $pengajuan = PengajuanPengembangan::create([
        'karyawan_id' => $fixture['karyawan']->id,
        'hasil_assessment_id' => $fixture['hasilAssessment']->id,
        'jenis_pengajuan' => 'kenaikan_jabatan',
        'jabatan_asal_id' => $fixture['jabatanAsal']->id,
        'jabatan_tujuan_id' => $fixture['jabatanTujuan']->id,
        'status' => 'diajukan',
        'tanggal_pengajuan' => '2026-01-02',
    ]);

    $this->actingAs($hrd)
        ->post(route('hrd.kenaikan-jabatan.approve', $pengajuan))
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
        'entity_type' => 'keputusan_kenaikan_jabatan',
        'entity_id' => $pengajuan->id,
    ]);
});
