<?php

namespace App\Http\Controllers;

use App\Models\AssessmentPeserta;
use App\Models\HasilAssessment;
use App\Models\Jawaban;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BlockchainRecord;
use App\Services\BlockchainService;
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
{
    /**
     * Daftar assessment milik karyawan.
     */
    public function index()
    {
        $karyawan = Auth::user()->karyawan;

        if (!$karyawan) {
            abort(404, 'Data karyawan tidak ditemukan.');
        }

        $assessmentPeserta = AssessmentPeserta::with([
            'assessment.jabatan',
            'assessment.skill',
        ])
            ->where('karyawan_id', $karyawan->id)
            ->latest()
            ->get();

        return view('karyawan.assessment.index', compact(
            'assessmentPeserta'
        ));
    }

    /**
     * Detail assessment.
     */
    public function show(AssessmentPeserta $peserta)
    {
        $karyawan = Auth::user()->karyawan;

        if (!$karyawan) {
            abort(404, 'Data karyawan tidak ditemukan.');
        }

        if ($peserta->karyawan_id !== $karyawan->id) {
            abort(403, 'Anda tidak memiliki akses ke assessment ini.');
        }

        $peserta->load([
            'assessment.jabatan',
            'assessment.skill',
            'assessment.soals.pilihanJawabans',
        ]);

        return view('karyawan.assessment.show', compact('peserta'));
    }

    /**
     * Memulai ujian.
     */
    public function start(AssessmentPeserta $peserta)
    {
        $karyawan = Auth::user()->karyawan;

        if (!$karyawan) {
            abort(404, 'Data karyawan tidak ditemukan.');
        }

        if ($peserta->karyawan_id !== $karyawan->id) {
            abort(403, 'Anda tidak memiliki akses ke assessment ini.');
        }

        if ($peserta->status === 'selesai') {
            return redirect()
                ->route('karyawan.assessment.hasil', $peserta)
                ->with('error', 'Assessment ini sudah selesai.');
        }

        if ($peserta->status === 'ditugaskan') {
            $peserta->update([
                'status' => 'sedang_mengerjakan',
                'waktu_mulai' => now(),
            ]);
        }

        return redirect()->route(
            'karyawan.assessment.kerjakan',
            $peserta
        );
    }

    /**
     * Menampilkan halaman pengerjaan ujian.
     */
    public function kerjakan(AssessmentPeserta $peserta)
    {
        $karyawan = Auth::user()->karyawan;

        if (!$karyawan) {
            abort(404, 'Data karyawan tidak ditemukan.');
        }

        if ($peserta->karyawan_id !== $karyawan->id) {
            abort(403, 'Anda tidak memiliki akses ke assessment ini.');
        }

        if ($peserta->status === 'selesai') {
            return redirect()
                ->route('karyawan.assessment.hasil', $peserta);
        }

        if ($peserta->status !== 'sedang_mengerjakan') {
            return redirect()
                ->route('karyawan.assessment.show', $peserta)
                ->with('error', 'Assessment belum dimulai.');
        }

        $peserta->load([
            'assessment',
            'assessment.soals' => function ($query) {
                $query->with('pilihanJawabans')
                    ->orderBy('assessment_soal.nomor_soal');
            },
        ]);

        return view('karyawan.assessment.kerjakan', compact(
            'peserta'
        ));
    }

    /**
     * Menyimpan jawaban dan menghitung hasil ujian.
     */
    public function submit(Request $request, AssessmentPeserta $peserta)
    {
        $karyawan = Auth::user()->karyawan;

        if (!$karyawan) {
            abort(404, 'Data karyawan tidak ditemukan.');
        }

        if ($peserta->karyawan_id !== $karyawan->id) {
            abort(403, 'Anda tidak memiliki akses ke assessment ini.');
        }

        if ($peserta->status !== 'sedang_mengerjakan') {
            return redirect()
                ->route('karyawan.assessment.hasil', $peserta)
                ->with('error', 'Assessment sudah tidak dapat dikerjakan.');
        }

        $peserta->load([
            'assessment',
            'assessment.soals.pilihanJawabans',
        ]);

        $jawabanInput = $request->input('jawaban', []);

        $waktuMulai = $peserta->waktu_mulai;
        $waktuSelesai = now();

        $totalBobot = 0;
        $totalNilai = 0;

        DB::transaction(function () use (
            $peserta,
            $jawabanInput,
            $waktuSelesai,
            &$totalBobot,
            &$totalNilai
        ) {
            // Hapus jawaban lama jika ada
            Jawaban::where(
                'assessment_peserta_id',
                $peserta->id
            )->delete();

            foreach ($peserta->assessment->soals as $soal) {

                $bobot = (float) $soal->bobot;

                $totalBobot += $bobot;

                $jawabanKaryawan = $jawabanInput[$soal->id] ?? [];

                // Jika bukan array, ubah menjadi array
                if (!is_array($jawabanKaryawan)) {
                    $jawabanKaryawan = [$jawabanKaryawan];
                }

                // Buang nilai kosong
                $jawabanKaryawan = array_values(
                    array_filter($jawabanKaryawan)
                );

                /*
                 * ========================================
                 * PILIHAN TUNGGAL / BENAR SALAH
                 * ========================================
                 */

                if (
                    in_array($soal->tipe_soal, [
                        'pilihan_tunggal',
                        'benar_salah',
                    ])
                ) {
                    $pilihanId = $jawabanKaryawan[0] ?? null;

                    if ($pilihanId) {

                        $pilihan = $soal->pilihanJawabans
                            ->firstWhere('id', (int) $pilihanId);

                        if ($pilihan) {

                            Jawaban::create([
                                'assessment_peserta_id' => $peserta->id,
                                'soal_id' => $soal->id,
                                'pilihan_jawaban_id' => $pilihan->id,
                            ]);

                            if ($pilihan->is_benar) {
                                $totalNilai += $bobot;
                            }
                        }
                    }
                }

                /*
                 * ========================================
                 * MULTI JAWABAN
                 * ========================================
                 */

                if ($soal->tipe_soal === 'multi_jawaban') {

                    $pilihanBenar = $soal->pilihanJawabans
                        ->where('is_benar', true)
                        ->pluck('id')
                        ->map(fn ($id) => (string) $id)
                        ->sort()
                        ->values()
                        ->toArray();

                    $pilihanDipilih = collect($jawabanKaryawan)
                        ->map(fn ($id) => (string) $id)
                        ->sort()
                        ->values()
                        ->toArray();

                    /*
                     * Simpan semua pilihan yang dipilih karyawan.
                     */
                    foreach ($pilihanDipilih as $pilihanId) {

                        $pilihan = $soal->pilihanJawabans
                            ->firstWhere('id', (int) $pilihanId);

                        if ($pilihan) {
                            Jawaban::create([
                                'assessment_peserta_id' => $peserta->id,
                                'soal_id' => $soal->id,
                                'pilihan_jawaban_id' => $pilihan->id,
                            ]);
                        }
                    }

                    /*
                     * Nilai penuh hanya jika semua pilihan
                     * yang dipilih tepat sama dengan kunci.
                     */
                    if ($pilihanDipilih === $pilihanBenar) {
                        $totalNilai += $bobot;
                    }
                }
            }

            /*
             * Hitung nilai dalam skala 0 - 100.
             */
            $nilaiAkhir = $totalBobot > 0
                ? round(($totalNilai / $totalBobot) * 100, 2)
                : 0;

            $standarNilai = (float) $peserta
                ->assessment
                ->standar_nilai;

            $status = $nilaiAkhir >= $standarNilai
                ? 'lulus'
                : 'tidak_lulus';

            /*
             * Tandai assessment selesai.
             */
            $peserta->update([
                'status' => 'selesai',
                'waktu_selesai' => $waktuSelesai,
            ]);

            /*
             * Simpan hasil assessment.
             */
            $hasil = HasilAssessment::updateOrCreate(
                [
                    'assessment_peserta_id' => $peserta->id,
                ],
                [
                    'nilai_akhir' => $nilaiAkhir,
                    'standar_nilai' => $standarNilai,
                    'status' => $status,
                    'tanggal_ujian' => $peserta->waktu_mulai,
                    'waktu_mulai' => $peserta->waktu_mulai,
                    'waktu_selesai' => $waktuSelesai,
                ]
            
            );
            $blockchain = app(BlockchainService::class);

            $blockchainRecordExists = BlockchainRecord::query()
                ->where('entity_type', 'hasil_assessment')
                ->where('entity_id', $hasil->id)
                ->exists();

            if (!$blockchainRecordExists) {
                $blockchain->addBlock(
                    'hasil_assessment',
                    $hasil->id,
                [
                    'assessment_peserta_id' => $peserta->id,
                    'assessment_id' => $peserta->assessment_id,
                    'karyawan_id' => $peserta->karyawan_id,
                    'nilai_akhir' => $hasil->nilai_akhir,
                    'standar_nilai' => $hasil->standar_nilai,
                    'status' => $hasil->status,
                    'tanggal_ujian' => $hasil->tanggal_ujian,
                    'waktu_mulai' => $hasil->waktu_mulai,
                    'waktu_selesai' => $hasil->waktu_selesai,
                ]
            );
        }
    });
        

        return redirect()->route(
            'karyawan.assessment.hasil',
            $peserta
        );
    }

    /**
     * Menampilkan hasil ujian.
     */
    public function hasil(AssessmentPeserta $peserta)
    {
        $karyawan = Auth::user()->karyawan;

        if (!$karyawan) {
            abort(404, 'Data karyawan tidak ditemukan.');
        }

        if ($peserta->karyawan_id !== $karyawan->id) {
            abort(403, 'Anda tidak memiliki akses ke hasil ini.');
        }

        $peserta->load([
            'assessment',
            'hasilAssessment',
        ]);

        return view('karyawan.assessment.hasil', compact(
            'peserta'
        ));
    }
}