<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Departemen;
use App\Models\HasilAssessment;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\PengajuanPengembangan;
use App\Models\Skill;

class HrdDashboardController extends Controller
{
    public function index()
    {
        $totalKaryawan = Karyawan::where('status', 'aktif')->count();

        $totalJabatan = Jabatan::count();

        $totalSkill = Skill::count();

        $assessmentAktif = Assessment::where('status', 'aktif')->count();

        $karyawanLulus = HasilAssessment::where('status', 'lulus')
            ->distinct('assessment_peserta_id')
            ->count('assessment_peserta_id');

        $pengajuanMenunggu = PengajuanPengembangan::whereIn('status', [
            'diajukan',
            'diproses',
        ])->count();

        $totalDepartemen = Departemen::count();

        $assessmentTerbaru = Assessment::with([
            'jabatan',
            'skill'
        ])
            ->latest()
            ->take(5)
            ->get();

        return view('hrd.dashboard', compact(
            'totalKaryawan',
            'totalJabatan',
            'totalSkill',
            'assessmentAktif',
            'karyawanLulus',
            'pengajuanMenunggu',
            'totalDepartemen',
            'assessmentTerbaru'
        ));
    }
}