<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlockchainRecord;
use App\Models\PengajuanPengembangan;
use Illuminate\Http\JsonResponse;

class PengembanganController extends Controller
{
    /**
     * Menampilkan semua pengajuan pengembangan.
     */
    public function index(): JsonResponse
    {
        $pengajuans = PengajuanPengembangan::with([
            'karyawan.departemen',
            'karyawan.jabatan',
            'hasilAssessment.assessmentPeserta.assessment',
            'jabatanAsal',
            'jabatanTujuan',
            'skill',
        ])
            ->orderByDesc('id')
            ->get();

        $data = $pengajuans->map(function ($pengajuan) {
            return $this->formatPengajuan($pengajuan);
        });

        return response()->json([
            'success' => true,
            'message' =>
                'Data pengajuan pengembangan berhasil diambil.',
            'data' => $data,
        ]);
    }

    /**
     * Menampilkan satu pengajuan pengembangan.
     */
    public function show(int $id): JsonResponse
    {
        $pengajuan = PengajuanPengembangan::with([
            'karyawan.departemen',
            'karyawan.jabatan',
            'hasilAssessment.assessmentPeserta.assessment',
            'jabatanAsal',
            'jabatanTujuan',
            'skill',
        ])->find($id);

        if (!$pengajuan) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Pengajuan pengembangan tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' =>
                'Detail pengajuan pengembangan berhasil diambil.',
            'data' => $this->formatPengajuan($pengajuan),
        ]);
    }

    /**
     * Memformat data pengajuan.
     */
    private function formatPengajuan(
        PengajuanPengembangan $pengajuan
    ): array {
        $hasil = $pengajuan->hasilAssessment;
        $peserta = $hasil?->assessmentPeserta;
        $assessment = $peserta?->assessment;
        $karyawan = $pengajuan->karyawan;

        /*
         * Ambil seluruh blockchain record yang
         * berhubungan dengan pengajuan ini.
         */
        $blockchainRecords = BlockchainRecord::query()
            ->where('entity_id', $pengajuan->id)
            ->whereIn('entity_type', [
                'pengajuan_pengembangan',
                'keputusan_kenaikan_jabatan',
                'keputusan_pemindahan_jabatan',
                'keputusan_peningkatan_skill',
            ])
            ->orderBy('block_number')
            ->get();

        return [
            'id' => $pengajuan->id,

            'karyawan' => $karyawan
                ? [
                    'id' => $karyawan->id,
                    'nik' => $karyawan->nik,
                    'nama' => $karyawan->nama,

                    'departemen' =>
                        $karyawan->departemen
                            ? [
                                'id' =>
                                    $karyawan->departemen->id,

                                'nama_departemen' =>
                                    $karyawan->departemen
                                        ->nama_departemen,
                            ]
                            : null,

                    'jabatan_sekarang' =>
                        $karyawan->jabatan
                            ? [
                                'id' =>
                                    $karyawan->jabatan->id,

                                'nama_jabatan' =>
                                    $karyawan->jabatan
                                        ->nama_jabatan,

                                'level_jabatan' =>
                                    $karyawan->jabatan
                                        ->level_jabatan,
                            ]
                            : null,
                ]
                : null,

            'jenis_pengajuan' =>
                $pengajuan->jenis_pengajuan,

            'hasil_assessment' => $hasil
                ? [
                    'id' => $hasil->id,

                    'nilai_akhir' =>
                        $hasil->nilai_akhir,

                    'standar_nilai' =>
                        $hasil->standar_nilai,

                    'status' =>
                        $hasil->status,

                    'tanggal_ujian' =>
                        $hasil->tanggal_ujian,

                    'assessment_peserta' =>
                        $peserta
                            ? [
                                'id' =>
                                    $peserta->id,

                                'percobaan_ke' =>
                                    $peserta->percobaan_ke,

                                'status' =>
                                    $peserta->status,
                            ]
                            : null,

                    'assessment' =>
                        $assessment
                            ? [
                                'id' =>
                                    $assessment->id,

                                'nama_assessment' =>
                                    $assessment
                                        ->nama_assessment,
                            ]
                            : null,
                ]
                : null,

            'jabatan_asal' =>
                $pengajuan->jabatanAsal
                    ? [
                        'id' =>
                            $pengajuan->jabatanAsal->id,

                        'nama_jabatan' =>
                            $pengajuan->jabatanAsal
                                ->nama_jabatan,

                        'level_jabatan' =>
                            $pengajuan->jabatanAsal
                                ->level_jabatan,
                    ]
                    : null,

            'jabatan_tujuan' =>
                $pengajuan->jabatanTujuan
                    ? [
                        'id' =>
                            $pengajuan->jabatanTujuan->id,

                        'nama_jabatan' =>
                            $pengajuan->jabatanTujuan
                                ->nama_jabatan,

                        'level_jabatan' =>
                            $pengajuan->jabatanTujuan
                                ->level_jabatan,
                    ]
                    : null,

            'skill' =>
                $pengajuan->skill
                    ? [
                        'id' =>
                            $pengajuan->skill->id,

                        'nama_skill' =>
                            $pengajuan->skill
                                ->nama_skill,
                    ]
                    : null,

            'level_sebelum' =>
                $pengajuan->level_sebelum,

            'level_sesudah' =>
                $pengajuan->level_sesudah,

            'status' =>
                $pengajuan->status,

            'tanggal_pengajuan' =>
                $pengajuan->tanggal_pengajuan,

            'tanggal_keputusan' =>
                $pengajuan->tanggal_keputusan,

            'catatan' =>
                $pengajuan->catatan,

            'blockchain' =>
                $blockchainRecords
                    ->map(function ($record) {
                        return [
                            'id' =>
                                $record->id,

                            'block_number' =>
                                $record->block_number,

                            'entity_type' =>
                                $record->entity_type,

                            'entity_id' =>
                                $record->entity_id,

                            'data_hash' =>
                                $record->data_hash,

                            'previous_hash' =>
                                $record->previous_hash,

                            'block_hash' =>
                                $record->block_hash,

                            'data_snapshot' =>
                                $record->data_snapshot,

                            'created_at' =>
                                $record->created_at,
                        ];
                    })
                    ->values(),
        ];
    }
}