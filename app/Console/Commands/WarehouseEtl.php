<?php

namespace App\Console\Commands;

use App\Models\Assessment;
use App\Models\Departemen;
use App\Models\HasilAssessment;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\PengajuanPengembangan;
use App\Models\RiwayatJabatan;
use App\Models\RiwayatSkill;
use App\Models\Skill;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WarehouseEtl extends Command
{
    protected $signature = 'warehouse:etl';

    protected $description =
        'Menjalankan proses ETL database aplikasi ke Data Warehouse';

    public function handle(): int
    {
        $this->info(
            'Memulai proses ETL Data Warehouse...'
        );

        $dw = DB::connection('warehouse');

        /*
        |--------------------------------------------------------------------------
        | Hapus data fact terlebih dahulu
        |--------------------------------------------------------------------------
        */

        $dw->statement('SET FOREIGN_KEY_CHECKS=0');

        try {
        // Kosongkan fact table terlebih dahulu
            $dw->table('fact_riwayat_skill')->truncate();
            $dw->table('fact_riwayat_jabatan')->truncate();
            $dw->table('fact_pengembangan')->truncate();
            $dw->table('fact_assessment')->truncate();

        // Setelah fact kosong, kosongkan dimension table
            $dw->table('dim_karyawan')->truncate();
            $dw->table('dim_skill')->truncate();
            $dw->table('dim_jabatan')->truncate();
            $dw->table('dim_departemen')->truncate();
            $dw->table('dim_waktu')->truncate();
        } finally {
        // Wajib diaktifkan kembali
            $dw->statement('SET FOREIGN_KEY_CHECKS=1');
        }
       

        /*
        |--------------------------------------------------------------------------
        | DIM DEPARTEMEN
        |--------------------------------------------------------------------------
        */

        $departemenMap = [];

        foreach (Departemen::orderBy('id')->get() as $departemen) {

            $id = $dw->table(
                'dim_departemen'
            )->insertGetId([
                'departemen_asal_id' =>
                    $departemen->id,
                'nama_departemen' =>
                    $departemen->nama_departemen,
                'deskripsi' =>
                    $departemen->deskripsi,
            ]);

            $departemenMap[$departemen->id] = $id;
        }

        $this->info(
            'Dim Departemen selesai.'
        );

        /*
        |--------------------------------------------------------------------------
        | DIM JABATAN
        |--------------------------------------------------------------------------
        */

        $jabatanMap = [];

        foreach (Jabatan::orderBy('id')->get() as $jabatan) {

            $id = $dw->table(
                'dim_jabatan'
            )->insertGetId([
                'jabatan_asal_id' =>
                    $jabatan->id,
                'nama_jabatan' =>
                    $jabatan->nama_jabatan,
                'level_jabatan' =>
                    $jabatan->level_jabatan,
                'standar_nilai' =>
                    $jabatan->standar_nilai,
                'deskripsi' =>
                    $jabatan->deskripsi,
            ]);

            $jabatanMap[$jabatan->id] = $id;
        }

        $this->info(
            'Dim Jabatan selesai.'
        );

        /*
        |--------------------------------------------------------------------------
        | DIM SKILL
        |--------------------------------------------------------------------------
        */

        $skillMap = [];

        foreach (Skill::orderBy('id')->get() as $skill) {

            $id = $dw->table(
                'dim_skill'
            )->insertGetId([
                'skill_asal_id' =>
                    $skill->id,
                'nama_skill' =>
                    $skill->nama_skill,
                'deskripsi' =>
                    $skill->deskripsi,
            ]);

            $skillMap[$skill->id] = $id;
        }

        $this->info(
            'Dim Skill selesai.'
        );

        /*
        |--------------------------------------------------------------------------
        | DIM KARYAWAN
        |--------------------------------------------------------------------------
        */

        $karyawanMap = [];

        foreach (
            Karyawan::with([
                'departemen',
                'jabatan',
            ])->orderBy('id')->get()
            as $karyawan
        ) {

            $id = $dw->table(
                'dim_karyawan'
            )->insertGetId([
                'karyawan_asal_id' =>
                    $karyawan->id,

                'nik' =>
                    $karyawan->nik,

                'nama' =>
                    $karyawan->nama,

                'departemen_id' =>
                    $departemenMap[
                        $karyawan->departemen_id
                    ],

                'jabatan_id' =>
                    $jabatanMap[
                        $karyawan->jabatan_id
                    ],

                'tanggal_masuk' =>
                    $karyawan->tanggal_masuk,

                'status' =>
                    $karyawan->status,
            ]);

            $karyawanMap[$karyawan->id] = $id;
        }

        $this->info(
            'Dim Karyawan selesai.'
        );

        /*
        |--------------------------------------------------------------------------
        | Kumpulkan tanggal untuk DIM WAKTU
        |--------------------------------------------------------------------------
        */

        $tanggalCollection = collect();

        HasilAssessment::query()
            ->pluck('tanggal_ujian')
            ->each(function ($tanggal) use (
                $tanggalCollection
            ) {
                if ($tanggal) {
                    $tanggalCollection->push(
                        Carbon::parse($tanggal)
                            ->toDateString()
                    );
                }
            });

        PengajuanPengembangan::query()
            ->pluck('tanggal_pengajuan')
            ->each(function ($tanggal) use (
                $tanggalCollection
            ) {
                if ($tanggal) {
                    $tanggalCollection->push(
                        Carbon::parse($tanggal)
                            ->toDateString()
                    );
                }
            });

        RiwayatJabatan::query()
            ->pluck('tanggal_mulai')
            ->each(function ($tanggal) use (
                $tanggalCollection
            ) {
                if ($tanggal) {
                    $tanggalCollection->push(
                        Carbon::parse($tanggal)
                            ->toDateString()
                    );
                }
            });

        RiwayatSkill::query()
            ->pluck('tanggal_perubahan')
            ->each(function ($tanggal) use (
                $tanggalCollection
            ) {
                if ($tanggal) {
                    $tanggalCollection->push(
                        Carbon::parse($tanggal)
                            ->toDateString()
                    );
                }
            });

        Karyawan::query()
            ->pluck('tanggal_masuk')
            ->each(function ($tanggal) use (
                $tanggalCollection
            ) {
                if ($tanggal) {
                    $tanggalCollection->push(
                        Carbon::parse($tanggal)
                            ->toDateString()
                    );
                }
            });

        $tanggalCollection = $tanggalCollection
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $waktuMap = [];

        foreach ($tanggalCollection as $tanggal) {

            $date = Carbon::parse($tanggal);

            $id = $dw->table(
                'dim_waktu'
            )->insertGetId([
                'tanggal' =>
                    $date->toDateString(),

                'tahun' =>
                    $date->year,

                'bulan' =>
                    $date->month,

                'nama_bulan' =>
                    $this->namaBulan(
                        $date->month
                    ),

                'kuartal' =>
                    $date->quarter,

                'hari' =>
                    $date->day,
            ]);

            $waktuMap[
                $date->toDateString()
            ] = $id;
        }

        $this->info(
            'Dim Waktu selesai.'
        );

        /*
        |--------------------------------------------------------------------------
        | FACT ASSESSMENT
        |--------------------------------------------------------------------------
        */

        $hasilAssessments = HasilAssessment::with([
            'assessmentPeserta.karyawan',
            'assessmentPeserta.assessment.jabatan',
            'assessmentPeserta.assessment.skill',
        ])->get();

        $factAssessmentMap = [];

        foreach (
            $hasilAssessments as $hasil
        ) {

            $peserta =
                $hasil->assessmentPeserta;

            if (!$peserta) {
                continue;
            }

            $karyawan =
                $peserta->karyawan;

            $assessment =
                $peserta->assessment;

            if (!$karyawan || !$assessment) {
                continue;
            }

            $tanggal = Carbon::parse(
                $hasil->tanggal_ujian
            )->toDateString();

            $factId = $dw->table(
                'fact_assessment'
            )->insertGetId([
                'waktu_id' =>
                    $waktuMap[$tanggal],

                'karyawan_id' =>
                    $karyawanMap[$karyawan->id],

                'jabatan_id' =>
                    $jabatanMap[
                        $assessment->jabatan_id
                    ] ?? null,

                'skill_id' =>
                    $assessment->skill_id
                        ? (
                            $skillMap[
                                $assessment->skill_id
                            ] ?? null
                        )
                        : null,

                'assessment_asal_id' =>
                    $assessment->id,

                'hasil_assessment_asal_id' =>
                    $hasil->id,

                'nilai_akhir' =>
                    $hasil->nilai_akhir,

                'standar_nilai' =>
                    $hasil->standar_nilai,

                'status' =>
                    $hasil->status,

                'jumlah_peserta' =>
                    1,
            ]);

            $factAssessmentMap[
                $hasil->id
            ] = $factId;
        }

        $this->info(
            'Fact Assessment selesai: ' .
            $hasilAssessments->count()
        );

        /*
        |--------------------------------------------------------------------------
        | FACT PENGEMBANGAN
        |--------------------------------------------------------------------------
        */

        $pengembangans =
            PengajuanPengembangan::with([
                'karyawan',
                'skill',
                'jabatanAsal',
                'jabatanTujuan',
            ])->get();

        foreach (
            $pengembangans as $pengembangan
        ) {

            if (!$pengembangan->karyawan) {
                continue;
            }

            $tanggal = Carbon::parse(
                $pengembangan->tanggal_pengajuan
            )->toDateString();

            $dw->table(
                'fact_pengembangan'
            )->insert([
                'waktu_id' =>
                    $waktuMap[$tanggal],

                'karyawan_id' =>
                    $karyawanMap[
                        $pengembangan->karyawan_id
                    ],

                'hasil_assessment_asal_id' =>
                    $pengembangan->hasil_assessment_id
                        ? (
                            $factAssessmentMap[
                                $pengembangan->hasil_assessment_id
                            ] ?? null
                        )
                        : null,

                'skill_id' =>
                    $pengembangan->skill_id
                        ? (
                            $skillMap[
                                $pengembangan->skill_id
                            ] ?? null
                        )
                        : null,

                'jabatan_asal_id' =>
                    $pengembangan->jabatan_asal_id
                        ? (
                            $jabatanMap[
                                $pengembangan->jabatan_asal_id
                            ] ?? null
                        )
                        : null,

                'jabatan_tujuan_id' =>
                    $pengembangan->jabatan_tujuan_id
                        ? (
                            $jabatanMap[
                                $pengembangan->jabatan_tujuan_id
                            ] ?? null
                        )
                        : null,

                'pengajuan_asal_id' =>
                    $pengembangan->id,

                'jenis_pengajuan' =>
                    $pengembangan->jenis_pengajuan,

                'status' =>
                    $pengembangan->status,

                'level_sebelum' =>
                    $pengembangan->level_sebelum,

                'level_sesudah' =>
                    $pengembangan->level_sesudah,

                'jumlah_pengajuan' =>
                    1,
            ]);
        }

        $this->info(
            'Fact Pengembangan selesai: ' .
            $pengembangans->count()
        );

        /*
        |--------------------------------------------------------------------------
        | FACT RIWAYAT JABATAN
        |--------------------------------------------------------------------------
        */

        $riwayatJabatans =
            RiwayatJabatan::with([
                'karyawan',
            ])->get();

        foreach (
            $riwayatJabatans as $riwayat
        ) {

            if (!$riwayat->karyawan) {
                continue;
            }

            $tanggal = Carbon::parse(
                $riwayat->tanggal_mulai
            )->toDateString();

            $durasi = null;

            if ($riwayat->tanggal_selesai) {

                $durasi = Carbon::parse(
                    $riwayat->tanggal_mulai
                )->diffInDays(
                    Carbon::parse(
                        $riwayat->tanggal_selesai
                    )
                );
            }

            $dw->table(
                'fact_riwayat_jabatan'
            )->insert([
                'waktu_id' =>
                    $waktuMap[$tanggal],

                'karyawan_id' =>
                    $karyawanMap[
                        $riwayat->karyawan_id
                    ],

                'jabatan_id' =>
                    $jabatanMap[
                        $riwayat->jabatan_id
                    ],

                'pengajuan_asal_id' =>
                    $riwayat->pengajuan_pengembangan_id,

                'hasil_assessment_asal_id' =>
                    $riwayat->hasil_assessment_id,

                'jenis_perubahan' =>
                    $riwayat->jenis_perubahan,

                'tanggal_mulai' =>
                    $riwayat->tanggal_mulai,

                'tanggal_selesai' =>
                    $riwayat->tanggal_selesai,

                'durasi_hari' =>
                    $durasi,

                'jumlah_perubahan' =>
                    1,
            ]);
        }

        $this->info(
            'Fact Riwayat Jabatan selesai: ' .
            $riwayatJabatans->count()
        );

        /*
        |--------------------------------------------------------------------------
        | FACT RIWAYAT SKILL
        |--------------------------------------------------------------------------
        */

        $riwayatSkills =
            RiwayatSkill::with([
                'karyawan',
                'skill',
            ])->get();

        foreach (
            $riwayatSkills as $riwayat
        ) {

            if (
                !$riwayat->karyawan ||
                !$riwayat->skill
            ) {
                continue;
            }

            $tanggal = Carbon::parse(
                $riwayat->tanggal_perubahan
            )->toDateString();

            $perubahanLevel =
                $riwayat->level_sesudah -
                $riwayat->level_sebelum;

            $dw->table(
                'fact_riwayat_skill'
            )->insert([
                'waktu_id' =>
                    $waktuMap[$tanggal],

                'karyawan_id' =>
                    $karyawanMap[
                        $riwayat->karyawan_id
                    ],

                'skill_id' =>
                    $skillMap[
                        $riwayat->skill_id
                    ],

                'pengajuan_asal_id' =>
                    $riwayat->pengajuan_pengembangan_id,

                'hasil_assessment_asal_id' =>
                    $riwayat->hasil_assessment_id,

                'level_sebelum' =>
                    $riwayat->level_sebelum,

                'level_sesudah' =>
                    $riwayat->level_sesudah,

                'perubahan_level' =>
                    $perubahanLevel,

                'tanggal_perubahan' =>
                    $riwayat->tanggal_perubahan,

                'jumlah_perubahan' =>
                    1,
            ]);
        }

        $this->info(
            'Fact Riwayat Skill selesai: ' .
            $riwayatSkills->count()
        );

        $this->newLine();

        $this->info(
            '========================================'
        );

        $this->info(
            'ETL DATA WAREHOUSE SELESAI'
        );

        $this->info(
            '========================================'
        );

        return self::SUCCESS;
    }

    private function namaBulan(
        int $bulan
    ): string {
        return match ($bulan) {
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
            default => '-',
        };
    }
}