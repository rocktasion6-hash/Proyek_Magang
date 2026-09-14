<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentPeserta;
use App\Models\AssessmentSoal;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Skill;
use App\Models\Soal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AssessmentController extends Controller
{
    /**
     * Menampilkan daftar assessment.
     */
    public function index()
    {
        $assessments = Assessment::with([
            'jabatan',
            'skill',
        ])
            ->withCount('peserta')
            ->latest()
            ->paginate(10);

        return view('hrd.assessment.index', compact(
            'assessments'
        ));
    }

    /**
     * Menampilkan form tambah assessment.
     */
    public function create()
    {
        $jabatans = Jabatan::orderBy('nama_jabatan')->get();

        $skills = Skill::orderBy('nama_skill')->get();

        $karyawans = Karyawan::with([
            'jabatan',
            'departemen',
        ])
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        $soals = Soal::with([
            'jabatan',
            'skill',
        ])
            ->where('status', true)
            ->orderBy('id')
            ->get();

        return view('hrd.assessment.create', compact(
            'jabatans',
            'skills',
            'karyawans',
            'soals'
        ));
    }

    /**
     * Menyimpan assessment baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_assessment' => [
                'required',
                'string',
                'max:255',
            ],

            'jabatan_id' => [
                'required',
                'exists:jabatans,id',
            ],

            'skill_id' => [
                'nullable',
                'exists:skills,id',
            ],

            'standar_nilai' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],

            'durasi' => [
                'required',
                'integer',
                'min:1',
            ],

            'tanggal_mulai' => [
                'required',
                'date',
            ],

            'tanggal_selesai' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai',
            ],

            'soal_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'soal_ids.*' => [
                'integer',
                'exists:soals,id',
            ],

            'karyawan_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'karyawan_ids.*' => [
                'integer',
                'exists:karyawans,id',
            ],
        ], [
            'nama_assessment.required' =>
                'Nama assessment wajib diisi.',

            'jabatan_id.required' =>
                'Jabatan wajib dipilih.',

            'standar_nilai.required' =>
                'Standar nilai wajib diisi.',

            'durasi.required' =>
                'Durasi wajib diisi.',

            'tanggal_mulai.required' =>
                'Tanggal mulai wajib diisi.',

            'tanggal_selesai.required' =>
                'Tanggal selesai wajib diisi.',

            'tanggal_selesai.after_or_equal' =>
                'Tanggal selesai harus sama atau setelah tanggal mulai.',

            'soal_ids.required' =>
                'Minimal satu soal harus dipilih.',

            'soal_ids.min' =>
                'Minimal satu soal harus dipilih.',

            'karyawan_ids.required' =>
                'Minimal satu peserta harus dipilih.',

            'karyawan_ids.min' =>
                'Minimal satu peserta harus dipilih.',
        ]);

        /*
         * Pastikan soal yang dipilih benar-benar ada
         * pada jabatan dan skill yang sesuai.
         */
        $jumlahSoalValid = Soal::whereIn(
            'id',
            $validated['soal_ids']
        )
            ->where('jabatan_id', $validated['jabatan_id'])
            ->when(
                $validated['skill_id'] ?? null,
                function ($query, $skillId) {
                    $query->where('skill_id', $skillId);
                }
            )
            ->count();

        if ($jumlahSoalValid !== count($validated['soal_ids'])) {
            return back()
                ->withInput()
                ->withErrors([
                    'soal_ids' =>
                        'Terdapat soal yang tidak sesuai dengan jabatan atau skill assessment.',
                ]);
        }

        DB::transaction(function () use ($validated) {

            /*
             * ========================================
             * 1. BUAT ASSESSMENT
             * ========================================
             */

            $assessment = Assessment::create([
                'nama_assessment' =>
                    $validated['nama_assessment'],

                'jabatan_id' =>
                    $validated['jabatan_id'],

                'skill_id' =>
                    $validated['skill_id'] ?? null,

                'standar_nilai' =>
                    $validated['standar_nilai'],

                'durasi' =>
                    $validated['durasi'],

                'tanggal_mulai' =>
                    $validated['tanggal_mulai'],

                'tanggal_selesai' =>
                    $validated['tanggal_selesai'],

                'status' => 'aktif',
            ]);

            /*
             * ========================================
             * 2. HUBUNGKAN SOAL
             * ========================================
             */

            foreach (
                $validated['soal_ids']
                as $index => $soalId
            ) {
                AssessmentSoal::create([
                    'assessment_id' =>
                        $assessment->id,

                    'soal_id' =>
                        $soalId,

                    'nomor_soal' =>
                        $index + 1,
                ]);
            }

            /*
             * ========================================
             * 3. TENTUKAN PESERTA
             * ========================================
             */

            foreach (
                $validated['karyawan_ids']
                as $karyawanId
            ) {
                AssessmentPeserta::create([
                    'assessment_id' =>
                        $assessment->id,

                    'karyawan_id' =>
                        $karyawanId,

                    'status' => 'ditugaskan',
                ]);
            }
        });

        return redirect()
            ->route('hrd.assessment.index')
            ->with(
                'success',
                'Assessment berhasil dibuat dan peserta berhasil ditugaskan.'
            );
    }

    /**
     * Menampilkan detail assessment.
     */
    public function show(Assessment $assessment)
    {
        $assessment->load([
            'jabatan',
            'skill',
            'soals',
            'peserta.karyawan',
        ]);

        return view(
            'hrd.assessment.show',
            compact('assessment')
        );
    }
}