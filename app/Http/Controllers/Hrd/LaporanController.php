<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $dw = DB::connection('warehouse');

        /*
        |--------------------------------------------------------------------------
        | FILTER TAHUN
        |--------------------------------------------------------------------------
        */

        // Mendukung ?tahun=2026 maupun ?year=2026
        $tahun = $request->get('tahun', $request->get('year', now()->year));

        // Daftar tahun yang tersedia di warehouse
        $years = $dw->table('dim_waktu')
            ->select('tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        // Tambahkan tahun sekarang kalau belum ada
        if (! $years->contains((int) now()->year)) {
            $years->push((int) now()->year);
        }

        $years = $years->sortDesc()->values();

        $tahunTersedia = collect(['semua'])->concat($years);
        /*
        |--------------------------------------------------------------------------
        | DATA MASTER
        |--------------------------------------------------------------------------
        | Diambil dari dimension table warehouse.
        |--------------------------------------------------------------------------
        */

        $totalKaryawan = $dw->table('dim_karyawan')
            ->where('status', 'aktif')
            ->count();

        $totalDepartemen = $dw->table('dim_departemen')
            ->count();

        $totalJabatan = $dw->table('dim_jabatan')
            ->count();

        $totalSkill = $dw->table('dim_jabatan')
            ->where('tipe_jabatan', 'operasional')
            ->whereRaw('LOWER(TRIM(nama_jabatan)) != ?', ['crew'])
            ->whereBetween('level_jabatan', [2, 5])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | ASSESSMENT
        |--------------------------------------------------------------------------
        */

        $assessmentQuery = $dw->table('fact_assessment as fa')
            ->join('dim_waktu as dwk', 'dwk.id', '=', 'fa.waktu_id');

        if ($tahun !== 'semua') {
            $assessmentQuery->where('dwk.tahun', $tahun);
        }

        $totalAssessment = (clone $assessmentQuery)->count();

        $totalLulus = (clone $assessmentQuery)
            ->where('fa.status', 'lulus')
            ->count();

        $totalTidakLulus = (clone $assessmentQuery)
            ->where('fa.status', 'tidak_lulus')
            ->count();

        $rataRataNilai = round(
            (float) ((clone $assessmentQuery)->avg('fa.nilai_akhir') ?? 0),
            2
        );

        $persentaseLulus = $totalAssessment > 0
            ? round(($totalLulus / $totalAssessment) * 100, 2)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | PENGEMBANGAN
        |--------------------------------------------------------------------------
        */

        $pengembanganQuery = $dw->table('fact_pengembangan as fp')
            ->join('dim_waktu as dwk', 'dwk.id', '=', 'fp.waktu_id');

        if ($tahun !== 'semua') {
            $pengembanganQuery->where('dwk.tahun', $tahun);
        }

        $totalPengembangan = (clone $pengembanganQuery)->count();

        $totalDiajukan = (clone $pengembanganQuery)
            ->where('fp.status', 'diajukan')
            ->count();

        $totalDiproses = (clone $pengembanganQuery)
            ->where('fp.status', 'diproses')
            ->count();

        $totalDisetujui = (clone $pengembanganQuery)
            ->where('fp.status', 'disetujui')
            ->count();

        $totalDitolak = (clone $pengembanganQuery)
            ->where('fp.status', 'ditolak')
            ->count();

        $keputusanBulananQuery = $dw->table('fact_pengembangan as fp')
            ->join('dim_waktu as dwk', 'dwk.tanggal', '=', 'fp.tanggal_keputusan')
            ->select(
                'dwk.bulan',
                'fp.status',
                DB::raw('COUNT(*) as jumlah')
            )
            ->whereIn('fp.status', ['disetujui', 'ditolak']);

        if ($tahun !== 'semua') {
            $keputusanBulananQuery->where('dwk.tahun', $tahun);
        }

        $keputusanBulanan = $keputusanBulananQuery
            ->groupBy('dwk.bulan', 'fp.status')
            ->orderBy('dwk.bulan')
            ->get();

        $dataDisetujuiBulananArray = array_fill(1, 12, 0);
        $dataDitolakBulananArray = array_fill(1, 12, 0);

        foreach ($keputusanBulanan as $item) {
            if ($item->status === 'disetujui') {
                $dataDisetujuiBulananArray[(int) $item->bulan] = (int) $item->jumlah;
            } else {
                $dataDitolakBulananArray[(int) $item->bulan] = (int) $item->jumlah;
            }
        }

        $dataDisetujuiBulanan = array_values($dataDisetujuiBulananArray);
        $dataDitolakBulanan = array_values($dataDitolakBulananArray);

        /*
        |--------------------------------------------------------------------------
        | JUMLAH KARYAWAN PER JABATAN
        |--------------------------------------------------------------------------
        */

        $karyawanPerJabatan = $dw->table('dim_jabatan as dj')
            ->leftJoin('dim_karyawan as dk', 'dk.jabatan_id', '=', 'dj.id')
            ->select(
                'dj.nama_jabatan',
                'dj.tipe_jabatan',
                'dj.urutan_struktural',
                DB::raw("COUNT(CASE WHEN dk.status = 'aktif' THEN 1 END) as jumlah")
            )
            ->groupBy(
                'dj.id',
                'dj.nama_jabatan',
                'dj.tipe_jabatan',
                'dj.urutan_struktural'
            )
            ->orderByDesc('jumlah')
            ->get();

        $karyawanPerJabatan->each(function ($jabatan) {
            $jabatan->label_jabatan = $jabatan->tipe_jabatan === 'struktural'
                && $jabatan->urutan_struktural !== null
                ? $jabatan->nama_jabatan.' (Urutan '.$jabatan->urutan_struktural.')'
                : $jabatan->nama_jabatan;
        });

        /*
        |--------------------------------------------------------------------------
        | JUMLAH KARYAWAN PER DEPARTEMEN
        |--------------------------------------------------------------------------
        */

        $karyawanPerDepartemen = $dw->table('dim_departemen as dd')
            ->leftJoin(
                'dim_karyawan as dk',
                'dk.departemen_id',
                '=',
                'dd.id'
            )
            ->select(
                'dd.nama_departemen',
                DB::raw("COUNT(CASE WHEN dk.status = 'aktif' THEN 1 END) as jumlah")
            )
            ->groupBy('dd.id', 'dd.nama_departemen')
            ->orderByDesc('jumlah')
            ->get();

        $riwayatJabatanQuery = $dw->table('fact_riwayat_jabatan as frj')
            ->join('dim_waktu as dwk', 'dwk.id', '=', 'frj.waktu_id')
            ->whereIn('frj.jenis_perubahan', ['kenaikan', 'pemindahan']);

        if ($tahun !== 'semua') {
            $riwayatJabatanQuery->where('dwk.tahun', $tahun);
        }

        $totalPerubahanJabatan = (clone $riwayatJabatanQuery)->count();

        $peningkatanSkillTerealisasiQuery = $dw->table('fact_pengembangan as fp')
            ->join('dim_waktu as dwk', 'dwk.tanggal', '=', 'fp.tanggal_keputusan')
            ->where('fp.jenis_pengajuan', 'peningkatan_skill')
            ->where('fp.status', 'disetujui');

        if ($tahun !== 'semua') {
            $peningkatanSkillTerealisasiQuery->where('dwk.tahun', $tahun);
        }

        $totalPeningkatanSkill = $peningkatanSkillTerealisasiQuery->count();

        $promosiStrukturalQuery = $dw->table('fact_riwayat_jabatan as frj')
            ->join('dim_waktu as dwk', 'dwk.id', '=', 'frj.waktu_id')
            ->join('dim_jabatan as dj', 'dj.id', '=', 'frj.jabatan_id')
            ->where('frj.jenis_perubahan', 'kenaikan')
            ->where('dj.tipe_jabatan', 'struktural');

        if ($tahun !== 'semua') {
            $promosiStrukturalQuery->where('dwk.tahun', $tahun);
        }

        $totalPromosiStruktural = (clone $promosiStrukturalQuery)->count();

        $promosiPerUrutan = (clone $promosiStrukturalQuery)
            ->select(
                'dj.urutan_struktural',
                'dj.nama_jabatan',
                DB::raw('COUNT(*) as jumlah')
            )
            ->groupBy('dj.urutan_struktural', 'dj.nama_jabatan')
            ->orderBy('dj.urutan_struktural')
            ->get();

        $labelPromosiPerUrutan = $promosiPerUrutan
            ->map(fn ($item) => 'Urutan '.$item->urutan_struktural.' - '.$item->nama_jabatan)
            ->values();
        $dataPromosiPerUrutan = $promosiPerUrutan
            ->pluck('jumlah')
            ->map(fn ($jumlah) => (int) $jumlah)
            ->values();

        $pemindahanAktualQuery = clone $riwayatJabatanQuery;
        $totalPemindahanAktual = $pemindahanAktualQuery
            ->where('frj.jenis_perubahan', 'pemindahan')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | PENGEMBANGAN BERDASARKAN JENIS
        |--------------------------------------------------------------------------
        */

        $pengembanganPerJenisQuery = $dw->table('fact_pengembangan as fp')
            ->join('dim_waktu as dwk', 'dwk.id', '=', 'fp.waktu_id')
            ->select(
                'fp.jenis_pengajuan',
                DB::raw('COUNT(*) as jumlah')
            );

        if ($tahun !== 'semua') {
            $pengembanganPerJenisQuery->where('dwk.tahun', $tahun);
        }

        $pengembanganPerJenis = $pengembanganPerJenisQuery
            ->groupBy('fp.jenis_pengajuan')
            ->orderByDesc('jumlah')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | DATA GRAFIK PENGEMBANGAN BERDASARKAN JENIS
        |--------------------------------------------------------------------------
        */

        $labelPengembanganJenis = [];
        $dataPengembanganJenis = [];

        foreach ($pengembanganPerJenis as $item) {
            $label = match ($item->jenis_pengajuan) {
                'kenaikan_jabatan' => 'Kenaikan Jabatan',
                'pemindahan_jabatan' => 'Pemindahan Jabatan',
                'peningkatan_skill' => 'Peningkatan Skill',
                default => ucwords(str_replace('_', ' ', $item->jenis_pengajuan)),
            };

            $labelPengembanganJenis[] = $label;
            $dataPengembanganJenis[] = (int) $item->jumlah;
        }

        /*
        |--------------------------------------------------------------------------
        | TREND ASSESSMENT PER BULAN
        |--------------------------------------------------------------------------
        */

        $assessmentBulananQuery = $dw->table('fact_assessment as fa')
            ->join('dim_waktu as dwk', 'dwk.id', '=', 'fa.waktu_id')
            ->select(
                'dwk.bulan',
                'dwk.nama_bulan',
                DB::raw('COUNT(*) as jumlah')
            );

        if ($tahun !== 'semua') {
            $assessmentBulananQuery->where('dwk.tahun', $tahun);
        }

        $assessmentBulanan = $assessmentBulananQuery
            ->groupBy('dwk.bulan', 'dwk.nama_bulan')
            ->orderBy('dwk.bulan')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Pastikan grafik punya 12 bulan
        |--------------------------------------------------------------------------
        */

        $namaBulan = [
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
        ];

        $assessmentBulananData = array_fill(1, 12, 0);

        foreach ($assessmentBulanan as $item) {
            $assessmentBulananData[(int) $item->bulan] = (int) $item->jumlah;
        }

        $labelBulan = array_values($namaBulan);
        $dataAssessmentBulanan = array_values($assessmentBulananData);

        /*
        |--------------------------------------------------------------------------
        | TREND LULUS DAN TIDAK LULUS PER BULAN
        |--------------------------------------------------------------------------
        */

        $lulusBulananQuery = $dw->table('fact_assessment as fa')
            ->join('dim_waktu as dwk', 'dwk.id', '=', 'fa.waktu_id')
            ->select(
                'dwk.bulan',
                DB::raw('COUNT(*) as jumlah')
            )
            ->where('fa.status', 'lulus');

        if ($tahun !== 'semua') {
            $lulusBulananQuery->where('dwk.tahun', $tahun);
        }

        $lulusBulanan = $lulusBulananQuery
            ->groupBy('dwk.bulan')
            ->orderBy('dwk.bulan')
            ->get();

        $tidakLulusBulananQuery = $dw->table('fact_assessment as fa')
            ->join('dim_waktu as dwk', 'dwk.id', '=', 'fa.waktu_id')
            ->select(
                'dwk.bulan',
                DB::raw('COUNT(*) as jumlah')
            )
            ->where('fa.status', 'tidak_lulus');

        if ($tahun !== 'semua') {
            $tidakLulusBulananQuery->where('dwk.tahun', $tahun);
        }

        $tidakLulusBulanan = $tidakLulusBulananQuery
            ->groupBy('dwk.bulan')
            ->orderBy('dwk.bulan')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Siapkan 12 bulan untuk grafik
        |--------------------------------------------------------------------------
        */

        $dataLulusBulananArray = array_fill(1, 12, 0);
        $dataTidakLulusBulananArray = array_fill(1, 12, 0);

        foreach ($lulusBulanan as $item) {
            $dataLulusBulananArray[(int) $item->bulan] = (int) $item->jumlah;
        }

        foreach ($tidakLulusBulanan as $item) {
            $dataTidakLulusBulananArray[(int) $item->bulan] = (int) $item->jumlah;
        }

        $dataLulusBulanan = array_values($dataLulusBulananArray);
        $dataTidakLulusBulanan = array_values($dataTidakLulusBulananArray);

        /*
        |--------------------------------------------------------------------------
        | DEVELOPMENT TERBARU
        |--------------------------------------------------------------------------
        */

        $latestPengembanganQuery = $dw->table('fact_pengembangan as fp')
            ->join('dim_waktu as dwk', 'dwk.id', '=', 'fp.waktu_id')
            ->join(
                'dim_karyawan as dk',
                'dk.id',
                '=',
                'fp.karyawan_id'
            )
            ->leftJoin(
                'dim_jabatan as ja',
                'ja.id',
                '=',
                'fp.jabatan_asal_id'
            )
            ->leftJoin(
                'dim_jabatan as jt',
                'jt.id',
                '=',
                'fp.jabatan_tujuan_id'
            )
            ->leftJoin(
                'dim_skill as ds',
                'ds.id',
                '=',
                'fp.skill_id'
            )
            ->select(
                'fp.id',
                'fp.jenis_pengajuan',
                'fp.status',
                'fp.level_sebelum as level_sebelum',
                'fp.level_sesudah as level_sesudah',
                'fp.pengajuan_asal_id',
                'fp.jabatan_asal_id',
                'fp.jabatan_tujuan_id',
                'dk.nama as nama_karyawan',
                'dk.nik',
                'ja.nama_jabatan as jabatan_asal',
                'ja.tipe_jabatan as tipe_jabatan_asal',
                'ja.urutan_struktural as urutan_asal',
                'jt.nama_jabatan as jabatan_tujuan',
                'jt.tipe_jabatan as tipe_jabatan_tujuan',
                'jt.urutan_struktural as urutan_tujuan',
                'ds.nama_skill',
                'dwk.tanggal',
                'dwk.tahun',
                'dwk.nama_bulan',
                'wkeputusan.tanggal as tanggal_keputusan'
            )
            ->leftJoin(
                'dim_waktu as wkeputusan',
                'wkeputusan.tanggal',
                '=',
                'fp.tanggal_keputusan'
            );

        if ($tahun !== 'semua') {
            $latestPengembanganQuery->where('dwk.tahun', $tahun);
        }

        $latestPengembangan = $latestPengembanganQuery
            ->orderByDesc('dwk.tanggal')
            ->orderByDesc('fp.id')
            ->limit(10)
            ->get();

        $skillProgressionJabatanIds = $latestPengembangan
            ->where('jenis_pengajuan', 'peningkatan_skill')
            ->flatMap(fn ($pengajuan) => [
                $pengajuan->jabatan_asal_id,
                $pengajuan->jabatan_tujuan_id,
            ])
            ->filter()
            ->unique()
            ->values();

        $skillMappingsByJabatan = $skillProgressionJabatanIds->isEmpty()
            ? collect()
            : $dw->table('bridge_jabatan_skill as bjs')
                ->join('dim_skill as ds', 'ds.id', '=', 'bjs.skill_id')
                ->whereIn('bjs.jabatan_id', $skillProgressionJabatanIds)
                ->select(
                    'bjs.jabatan_id',
                    'bjs.skill_id',
                    'bjs.level_dibutuhkan',
                    'ds.nama_skill'
                )
                ->get()
                ->groupBy('jabatan_id');

        $latestPengembangan->each(function (object $pengajuan) use ($skillMappingsByJabatan): void {
            if ($pengajuan->jenis_pengajuan !== 'peningkatan_skill') {
                return;
            }

            $sourceSkills = $skillMappingsByJabatan
                ->get((int) $pengajuan->jabatan_asal_id, collect())
                ->keyBy('skill_id');

            $pengajuan->skill_progressions = $skillMappingsByJabatan
                ->get((int) $pengajuan->jabatan_tujuan_id, collect())
                ->map(function (object $targetSkill) use ($sourceSkills): object {
                    $sourceSkill = $sourceSkills->get($targetSkill->skill_id);

                    return (object) [
                        'nama_skill' => $targetSkill->nama_skill,
                        'level_sebelum' => $sourceSkill?->level_dibutuhkan,
                        'level_sesudah' => $targetSkill->level_dibutuhkan,
                    ];
                })
                ->values();
        });

        $labelTahun = $tahun === 'semua' ? 'semua tahun' : 'tahun '.$tahun;

        /*
        |--------------------------------------------------------------------------
        | ALIAS VARIABEL
        |--------------------------------------------------------------------------
        | Supaya tetap kompatibel dengan nama variabel yang mungkin sudah
        | dipakai di view lama.
        |--------------------------------------------------------------------------
        */

        $jumlahKaryawan = $totalKaryawan;
        $jumlahDepartemen = $totalDepartemen;
        $jumlahJabatan = $totalJabatan;
        $jumlahSkill = $totalSkill;

        $jumlahAssessment = $totalAssessment;
        $jumlahLulus = $totalLulus;
        $jumlahTidakLulus = $totalTidakLulus;

        /*
        |--------------------------------------------------------------------------
        | Alias untuk kompatibilitas dengan view lama
        |--------------------------------------------------------------------------
        */

        $totalHasilAssessment = $totalAssessment;
        $totalAssessmentLulus = $totalLulus;
        $totalAssessmentTidakLulus = $totalTidakLulus;

        $totalPengajuanPengembangan = $totalPengembangan;
        $totalPengembanganDiajukan = $totalDiajukan;
        $totalPengembanganDiproses = $totalDiproses;
        $totalPengembanganDisetujui = $totalDisetujui;
        $totalPengembanganDitolak = $totalDitolak;

        $pengembanganDiajukan = $totalDiajukan;
        $pengembanganDiproses = $totalDiproses;
        $pengembanganDisetujui = $totalDisetujui;
        $pengembanganDitolak = $totalDitolak;

        $tingkatKelulusan = $persentaseLulus;

        $pengembanganTerbaru = $latestPengembangan;

        return view('hrd.laporan.index', compact(
            // filter
            'tahun',
            'years',
            'tahunTersedia',

            // master
            'totalKaryawan',
            'totalDepartemen',
            'totalJabatan',
            'totalSkill',

            // assessment
            'totalAssessment',
            'totalLulus',
            'totalTidakLulus',
            'rataRataNilai',
            'persentaseLulus',

            // development
            'totalPengembangan',
            'totalDiajukan',
            'totalDiproses',
            'totalDisetujui',
            'totalDitolak',
            'totalPerubahanJabatan',
            'totalPeningkatanSkill',
            'totalPromosiStruktural',
            'totalPemindahanAktual',
            'labelPromosiPerUrutan',
            'dataPromosiPerUrutan',
            'dataDisetujuiBulanan',
            'dataDitolakBulanan',
            'labelTahun',

            // grafik
            'karyawanPerJabatan',
            'karyawanPerDepartemen',
            'pengembanganPerJenis',
            'labelPengembanganJenis',
            'dataPengembanganJenis',
            'assessmentBulanan',
            'labelBulan',
            'dataAssessmentBulanan',
            'dataLulusBulanan',
            'dataTidakLulusBulanan',

            // tabel
            'latestPengembangan',
            'pengembanganTerbaru',

            // alias kompatibilitas
            'jumlahKaryawan',
            'jumlahDepartemen',
            'jumlahJabatan',
            'jumlahSkill',

            'jumlahAssessment',
            'jumlahLulus',
            'jumlahTidakLulus',

            'totalHasilAssessment',
            'totalAssessmentLulus',
            'totalAssessmentTidakLulus',

            'totalPengajuanPengembangan',
            'totalPengembanganDiajukan',
            'totalPengembanganDiproses',
            'totalPengembanganDisetujui',
            'totalPengembanganDitolak',

            'pengembanganDiajukan',
            'pengembanganDiproses',
            'pengembanganDisetujui',
            'pengembanganDitolak',

            'tingkatKelulusan',
        ));
    }
}
