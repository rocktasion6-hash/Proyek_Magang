<?php

namespace App\Http\Controllers;

use App\Models\AssessmentPeserta;
use App\Models\HasilAssessment;
use App\Models\PengajuanPengembangan;
use Illuminate\Support\Facades\Auth;

class KaryawanDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ambil data karyawan berdasarkan akun yang sedang login
        $karyawan = $user->karyawan;

        // Jika akun belum memiliki data karyawan
        if (!$karyawan) {
            abort(404, 'Data karyawan tidak ditemukan.');
        }

        // ========================================
        // ASSESSMENT YANG DITUGASKAN
        // ========================================

        $assessmentAktif = AssessmentPeserta::with([
            'assessment.jabatan',
            'assessment.skill',
        ])
            ->where('karyawan_id', $karyawan->id)
            ->whereIn('status', [
                'ditugaskan',
                'sedang_mengerjakan',
            ])
            ->latest()
            ->get();


        // ========================================
        // HASIL ASSESSMENT TERAKHIR
        // ========================================

        $hasilAssessmentTerakhir = HasilAssessment::with([
            'assessmentPeserta.assessment',
        ])
            ->whereHas('assessmentPeserta', function ($query) use ($karyawan) {
                $query->where('karyawan_id', $karyawan->id);
            })
            ->latest('tanggal_ujian')
            ->first();


        // ========================================
        // TOTAL ASSESSMENT
        // ========================================

        $totalAssessment = AssessmentPeserta::where(
            'karyawan_id',
            $karyawan->id
        )->count();


        // ========================================
        // TOTAL ASSESSMENT LULUS
        // ========================================

        $totalLulus = HasilAssessment::where('status', 'lulus')
            ->whereHas('assessmentPeserta', function ($query) use ($karyawan) {
                $query->where('karyawan_id', $karyawan->id);
            })
            ->count();


        // ========================================
        // PENGAJUAN PENGEMBANGAN
        // ========================================

        $pengajuanTerbaru = PengajuanPengembangan::with([
            'jabatanAsal',
            'jabatanTujuan',
            'skill',
            'hasilAssessment',
        ])
            ->where('karyawan_id', $karyawan->id)
            ->latest('tanggal_pengajuan')
            ->take(5)
            ->get();


        // ========================================
        // KIRIM DATA KE VIEW
        // ========================================

        return view('karyawan.dashboard', compact(
            'karyawan',
            'assessmentAktif',
            'hasilAssessmentTerakhir',
            'totalAssessment',
            'totalLulus',
            'pengajuanTerbaru'
        ));
    }
}