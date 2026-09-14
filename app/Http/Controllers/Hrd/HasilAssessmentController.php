<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\HasilAssessment;
use Illuminate\Http\Request;

class HasilAssessmentController extends Controller
{
    /**
     * Menampilkan daftar hasil assessment.
     */
    public function index(Request $request)
    {
        $query = HasilAssessment::with([
            'assessmentPeserta.karyawan',
            'assessmentPeserta.assessment.jabatan',
        ]);

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan nama karyawan
        if ($request->filled('search')) {
            $search = $request->search;

            $query->whereHas(
                'assessmentPeserta.karyawan',
                function ($q) use ($search) {
                    $q->where('nama', 'like', '%' . $search . '%')
                        ->orWhere('nik', 'like', '%' . $search . '%');
                }
            );
        }

        $hasilAssessments = $query
            ->latest('tanggal_ujian')
            ->paginate(10)
            ->withQueryString();

        return view(
            'hrd.hasil-assessment.index',
            compact('hasilAssessments')
        );
    }

    /**
     * Menampilkan detail hasil assessment.
     */
    public function show(HasilAssessment $hasilAssessment)
    {
        $hasilAssessment->load([
            'assessmentPeserta.karyawan',
            'assessmentPeserta.assessment.jabatan',
            'assessmentPeserta.assessment.skill',
            'assessmentPeserta.assessment.soals.pilihanJawabans',
            'assessmentPeserta.jawabans.pilihanJawaban',
        ]);

        return view(
            'hrd.hasil-assessment.show',
            compact('hasilAssessment')
        );
    }
}