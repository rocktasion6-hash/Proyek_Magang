<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use Illuminate\Http\JsonResponse;

class AssessmentController extends Controller
{
    /**
     * Menampilkan daftar assessment.
     */
    public function index(): JsonResponse
    {
        $assessments = Assessment::with([
            'jabatan',
            'skill',
        ])
            ->withCount([
                'peserta',
                'soals',
            ])
            ->orderByDesc('id')
            ->get();

        $data = $assessments->map(function ($assessment) {
            return [
                'id' => $assessment->id,
                'nama_assessment' => $assessment->nama_assessment,

                'jabatan' => $assessment->jabatan
                    ? [
                        'id' => $assessment->jabatan->id,
                        'nama_jabatan' =>
                            $assessment->jabatan->nama_jabatan,
                        'level_jabatan' =>
                            $assessment->jabatan->level_jabatan,
                    ]
                    : null,

                'skill' => $assessment->skill
                    ? [
                        'id' => $assessment->skill->id,
                        'nama_skill' =>
                            $assessment->skill->nama_skill,
                    ]
                    : null,

                'standar_nilai' => $assessment->standar_nilai,
                'durasi' => $assessment->durasi,

                'tanggal_mulai' =>
                    $assessment->tanggal_mulai,

                'tanggal_selesai' =>
                    $assessment->tanggal_selesai,

                'status' => $assessment->status,

                'jumlah_soal' =>
                    $assessment->soals_count,

                'jumlah_peserta' =>
                    $assessment->peserta_count,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Data assessment berhasil diambil.',
            'data' => $data,
        ]);
    }

    /**
     * Menampilkan detail assessment.
     */
    public function show(int $id): JsonResponse
    {
        $assessment = Assessment::with([
            'jabatan',
            'skill',
            'soals.pilihanJawabans',
            'peserta.karyawan',
        ])->find($id);

        if (!$assessment) {
            return response()->json([
                'success' => false,
                'message' => 'Assessment tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        $data = [
            'id' => $assessment->id,

            'nama_assessment' =>
                $assessment->nama_assessment,

            'jabatan' => $assessment->jabatan
                ? [
                    'id' => $assessment->jabatan->id,
                    'nama_jabatan' =>
                        $assessment->jabatan->nama_jabatan,
                    'level_jabatan' =>
                        $assessment->jabatan->level_jabatan,
                ]
                : null,

            'skill' => $assessment->skill
                ? [
                    'id' => $assessment->skill->id,
                    'nama_skill' =>
                        $assessment->skill->nama_skill,
                ]
                : null,

            'standar_nilai' =>
                $assessment->standar_nilai,

            'durasi' =>
                $assessment->durasi,

            'tanggal_mulai' =>
                $assessment->tanggal_mulai,

            'tanggal_selesai' =>
                $assessment->tanggal_selesai,

            'status' =>
                $assessment->status,

            'soal' => $assessment->soals
                ->sortBy('pivot.nomor_soal')
                ->values()
                ->map(function ($soal) {
                    return [
                        'id' => $soal->id,

                        'nomor_soal' =>
                            $soal->pivot->nomor_soal,

                        'pertanyaan' =>
                            $soal->pertanyaan,

                        'tipe_soal' =>
                            $soal->tipe_soal,

                        'tingkat_kesulitan' =>
                            $soal->tingkat_kesulitan,

                        'bobot' =>
                            $soal->bobot,

                        'pilihan_jawaban' =>
                            $soal->pilihanJawabans
                                ->map(function ($pilihan) {
                                    return [
                                        'id' =>
                                            $pilihan->id,

                                        'kode' =>
                                            $pilihan->kode,

                                        'teks_jawaban' =>
                                            $pilihan->teks_jawaban,
                                    ];
                                })
                                ->values(),
                    ];
                }),

            'peserta' => $assessment->peserta
                ->map(function ($peserta) {
                    return [
                        'id' => $peserta->id,

                        'karyawan_id' =>
                            $peserta->karyawan_id,

                        'nama_karyawan' =>
                            $peserta->karyawan
                                ? $peserta->karyawan->nama
                                : null,

                        'nik' =>
                            $peserta->karyawan
                                ? $peserta->karyawan->nik
                                : null,

                        'percobaan_ke' =>
                            $peserta->percobaan_ke,

                        'status' =>
                            $peserta->status,

                        'waktu_mulai' =>
                            $peserta->waktu_mulai,

                        'waktu_selesai' =>
                            $peserta->waktu_selesai,
                    ];
                })
                ->values(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Detail assessment berhasil diambil.',
            'data' => $data,
        ]);
    }
}