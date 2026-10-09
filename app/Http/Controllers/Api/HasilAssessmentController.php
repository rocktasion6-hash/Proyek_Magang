<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HasilAssessment;
use App\Models\BlockchainRecord;
use Illuminate\Http\JsonResponse;

class HasilAssessmentController extends Controller
{
    /**
     * Menampilkan semua hasil assessment.
     */
    public function index(): JsonResponse
    {
        $hasilAssessments = HasilAssessment::with([
            'assessmentPeserta.karyawan',
            'assessmentPeserta.assessment.jabatan',
            'assessmentPeserta.assessment.skill',
        ])
            ->orderByDesc('id')
            ->get();

        $data = $hasilAssessments->map(function ($hasil) {

            $peserta = $hasil->assessmentPeserta;
            $assessment = $peserta?->assessment;
            $karyawan = $peserta?->karyawan;

            $blockchain = BlockchainRecord::query()
                ->where('entity_type', 'hasil_assessment')
                ->where('entity_id', $hasil->id)
                ->orderByDesc('id')
                ->first();

            return [
                'id' => $hasil->id,

                'karyawan' => $karyawan
                    ? [
                        'id' => $karyawan->id,
                        'nik' => $karyawan->nik,
                        'nama' => $karyawan->nama,
                    ]
                    : null,

                'assessment' => $assessment
                    ? [
                        'id' => $assessment->id,
                        'nama_assessment' =>
                            $assessment->nama_assessment,

                        'jabatan' => $assessment->jabatan
                            ? [
                                'id' =>
                                    $assessment->jabatan->id,
                                'nama_jabatan' =>
                                    $assessment->jabatan->nama_jabatan,
                            ]
                            : null,

                        'skill' => $assessment->skill
                            ? [
                                'id' =>
                                    $assessment->skill->id,
                                'nama_skill' =>
                                    $assessment->skill->nama_skill,
                            ]
                            : null,
                    ]
                    : null,

                'assessment_peserta' => $peserta
                    ? [
                        'id' => $peserta->id,
                        'percobaan_ke' =>
                            $peserta->percobaan_ke,
                        'status' =>
                            $peserta->status,
                    ]
                    : null,

                'nilai_akhir' =>
                    $hasil->nilai_akhir,

                'standar_nilai' =>
                    $hasil->standar_nilai,

                'status' =>
                    $hasil->status,

                'tanggal_ujian' =>
                    $hasil->tanggal_ujian,

                'waktu_mulai' =>
                    $hasil->waktu_mulai,

                'waktu_selesai' =>
                    $hasil->waktu_selesai,

                'blockchain' => $blockchain
                    ? [
                        'block_number' =>
                            $blockchain->block_number,

                        'data_hash' =>
                            $blockchain->data_hash,

                        'previous_hash' =>
                            $blockchain->previous_hash,

                        'block_hash' =>
                            $blockchain->block_hash,
                    ]
                    : null,
            ];
        });

        return response()->json([
            'success' => true,
            'message' =>
                'Data hasil assessment berhasil diambil.',
            'data' => $data,
        ]);
    }

    /**
     * Menampilkan satu hasil assessment.
     */
    public function show(int $id): JsonResponse
    {
        $hasil = HasilAssessment::with([
            'assessmentPeserta.karyawan',
            'assessmentPeserta.assessment.jabatan',
            'assessmentPeserta.assessment.skill',
        ])->find($id);

        if (!$hasil) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Hasil assessment tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        $peserta = $hasil->assessmentPeserta;
        $assessment = $peserta?->assessment;
        $karyawan = $peserta?->karyawan;

        $blockchain = BlockchainRecord::query()
            ->where('entity_type', 'hasil_assessment')
            ->where('entity_id', $hasil->id)
            ->orderByDesc('id')
            ->first();

        $data = [
            'id' => $hasil->id,

            'karyawan' => $karyawan
                ? [
                    'id' => $karyawan->id,
                    'nik' => $karyawan->nik,
                    'nama' => $karyawan->nama,
                ]
                : null,

            'assessment' => $assessment
                ? [
                    'id' => $assessment->id,
                    'nama_assessment' =>
                        $assessment->nama_assessment,

                    'jabatan' => $assessment->jabatan
                        ? [
                            'id' =>
                                $assessment->jabatan->id,
                            'nama_jabatan' =>
                                $assessment->jabatan->nama_jabatan,
                            'level_jabatan' =>
                                $assessment->jabatan->level_jabatan,
                        ]
                        : null,

                    'skill' => $assessment->skill
                        ? [
                            'id' =>
                                $assessment->skill->id,
                            'nama_skill' =>
                                $assessment->skill->nama_skill,
                        ]
                        : null,

                    'standar_nilai' =>
                        $assessment->standar_nilai,

                    'durasi' =>
                        $assessment->durasi,

                    'status' =>
                        $assessment->status,
                ]
                : null,

            'assessment_peserta' => $peserta
                ? [
                    'id' => $peserta->id,

                    'percobaan_ke' =>
                        $peserta->percobaan_ke,

                    'status' =>
                        $peserta->status,

                    'waktu_mulai' =>
                        $peserta->waktu_mulai,

                    'waktu_selesai' =>
                        $peserta->waktu_selesai,
                ]
                : null,

            'hasil' => [
                'nilai_akhir' =>
                    $hasil->nilai_akhir,

                'standar_nilai' =>
                    $hasil->standar_nilai,

                'status' =>
                    $hasil->status,

                'tanggal_ujian' =>
                    $hasil->tanggal_ujian,

                'waktu_mulai' =>
                    $hasil->waktu_mulai,

                'waktu_selesai' =>
                    $hasil->waktu_selesai,
            ],

            'blockchain' => $blockchain
                ? [
                    'block_number' =>
                        $blockchain->block_number,

                    'data_hash' =>
                        $blockchain->data_hash,

                    'previous_hash' =>
                        $blockchain->previous_hash,

                    'block_hash' =>
                        $blockchain->block_hash,
                ]
                : null,
        ];

        return response()->json([
            'success' => true,
            'message' =>
                'Detail hasil assessment berhasil diambil.',
            'data' => $data,
        ]);
    }
}