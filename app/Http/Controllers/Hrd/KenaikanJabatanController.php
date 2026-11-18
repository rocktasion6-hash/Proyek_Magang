<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\BlockchainRecord;
use App\Models\HasilAssessment;
use App\Models\Jabatan;
use App\Models\PengajuanPengembangan;
use App\Models\RiwayatJabatan;
use App\Services\BlockchainService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KenaikanJabatanController extends Controller
{
    public function index(Request $request)
    {
        $query = PengajuanPengembangan::with([
            'karyawan.departemen',
            'jabatanAsal',
            'jabatanTujuan',
            'hasilAssessment.assessmentPeserta.assessment',
        ])->where('jenis_pengajuan', 'kenaikan_jabatan');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->whereHas('karyawan', function ($q) use ($search) {
                $q->where('nama', 'like', '%'.$search.'%')
                    ->orWhere('nik', 'like', '%'.$search.'%');
            });
        }

        $pengajuans = $query
            ->latest('tanggal_pengajuan')
            ->paginate(10)
            ->withQueryString();

        return view('hrd.kenaikan-jabatan.index', compact('pengajuans'));
    }

    public function create()
    {
        $hasilAssessments = HasilAssessment::query()
            ->with([
                'assessmentPeserta.karyawan.jabatan',
                'assessmentPeserta.assessment.jabatan',
            ])
            ->where('status', 'lulus')
            ->whereHas('assessmentPeserta', function (Builder $query) {
                $query->where('status', 'selesai')
                    ->whereHas('karyawan', function (Builder $query) {
                        $query->where('status', 'aktif')
                            ->whereHas('jabatan', function (Builder $query) {
                                $query->whereIn('tipe_jabatan', [
                                    'operasional',
                                    'struktural',
                                ]);
                            });
                    })
                    ->whereHas('assessment.jabatan', function (Builder $query) {
                        $query->where('tipe_jabatan', 'struktural')
                            ->whereBetween('urutan_struktural', [1, 6]);
                    });
            })
            ->whereDoesntHave(
                'assessmentPeserta.karyawan.pengajuanPengembangans',
                function (Builder $query) {
                    $query->whereIn('status', ['diajukan', 'diproses']);
                }
            )
            ->whereDoesntHave('pengajuanPengembangans', function (Builder $query) {
                $query->where('jenis_pengajuan', 'kenaikan_jabatan')
                    ->whereIn('status', [
                        'diajukan',
                        'diproses',
                        'disetujui',
                    ]);
            })
            ->latest('tanggal_ujian')
            ->get();

        $hasilAssessments = $hasilAssessments
            ->filter(function (HasilAssessment $hasilAssessment): bool {
                $peserta = $hasilAssessment->assessmentPeserta;
                $jabatanAsal = $peserta?->karyawan?->jabatan;
                $jabatanTujuan = $peserta?->assessment?->jabatan;

                return $jabatanAsal !== null
                    && $jabatanTujuan !== null
                    && $this->isValidProgression($jabatanAsal, $jabatanTujuan);
            })
            ->values();

        return view(
            'hrd.kenaikan-jabatan.create',
            compact('hasilAssessments')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hasil_assessment_id' => ['required', 'exists:hasil_assessments,id'],
            'tanggal_pengajuan' => ['required', 'date'],
            'catatan' => ['nullable', 'string'],
        ]);

        $hasilAssessment = HasilAssessment::with([
            'assessmentPeserta.karyawan.jabatan',
            'assessmentPeserta.assessment.jabatan',
        ])->findOrFail($validated['hasil_assessment_id']);

        if ($hasilAssessment->status !== 'lulus') {
            return back()
                ->withErrors([
                    'hasil_assessment_id' => 'Karyawan harus memiliki hasil assessment dengan status lulus.',
                ])
                ->withInput();
        }

        $peserta = $hasilAssessment->assessmentPeserta;

        if (! $peserta || ! $peserta->karyawan || ! $peserta->assessment) {
            return back()
                ->withErrors([
                    'hasil_assessment_id' => 'Data peserta atau assessment tidak lengkap.',
                ])
                ->withInput();
        }

        if ($peserta->status !== 'selesai') {
            return back()
                ->withErrors([
                    'hasil_assessment_id' => 'Assessment karyawan belum selesai.',
                ])
                ->withInput();
        }

        $karyawan = $peserta->karyawan;
        $jabatanAsal = $karyawan->jabatan;
        $jabatanTujuan = $peserta->assessment->jabatan;

        if ($karyawan->status !== 'aktif') {
            return back()
                ->withErrors([
                    'hasil_assessment_id' => 'Hanya karyawan aktif yang dapat mengajukan kenaikan jabatan.',
                ])
                ->withInput();
        }

        if (
            ! $jabatanAsal ||
            ! $jabatanTujuan ||
            ! $this->isValidProgression($jabatanAsal, $jabatanTujuan)
        ) {
            return back()
                ->withErrors([
                    'hasil_assessment_id' => 'Assessment harus ditujukan untuk tahap struktural berikutnya sesuai jabatan karyawan saat ini.',
                ])
                ->withInput();
        }

        $sudahAda = PengajuanPengembangan::query()
            ->where('karyawan_id', $karyawan->id)
            ->whereIn('status', ['diajukan', 'diproses'])
            ->exists();

        if ($sudahAda) {
            return back()
                ->withErrors([
                    'hasil_assessment_id' => 'Karyawan ini masih memiliki pengajuan pengembangan yang belum selesai diputuskan.',
                ])
                ->withInput();
        }

        $hasilSudahDigunakan = PengajuanPengembangan::query()
            ->where('hasil_assessment_id', $hasilAssessment->id)
            ->where('jenis_pengajuan', 'kenaikan_jabatan')
            ->whereIn('status', [
                'diajukan',
                'diproses',
                'disetujui',
            ])
            ->exists();

        if ($hasilSudahDigunakan) {
            return back()
                ->withErrors([
                    'hasil_assessment_id' => 'Hasil assessment ini sudah digunakan untuk pengajuan kenaikan jabatan.',
                ])
                ->withInput();
        }

        DB::transaction(function () use (
            $karyawan,
            $hasilAssessment,
            $jabatanAsal,
            $jabatanTujuan,
            $validated
        ): void {
            $pengajuan = PengajuanPengembangan::create([
                'karyawan_id' => $karyawan->id,
                'hasil_assessment_id' => $hasilAssessment->id,
                'jenis_pengajuan' => 'kenaikan_jabatan',
                'jabatan_asal_id' => $jabatanAsal->id,
                'jabatan_tujuan_id' => $jabatanTujuan->id,
                'skill_id' => null,
                'status' => 'diajukan',
                'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
                'tanggal_keputusan' => null,
                'catatan' => $validated['catatan'] ?? null,
            ]);

            $blockchainRecordExists = BlockchainRecord::query()
                ->where('entity_type', 'pengajuan_pengembangan')
                ->where('entity_id', $pengajuan->id)
                ->exists();

            if (! $blockchainRecordExists) {
                app(BlockchainService::class)
                    ->recordPengembanganEvent(
                        'pengajuan_pengembangan',
                        $pengajuan
                    );
            }
        });

        return redirect()
            ->route('hrd.kenaikan-jabatan.index')
            ->with(
                'success',
                'Pengajuan kenaikan jabatan berhasil dibuat.'
            );
    }

    public function show(PengajuanPengembangan $pengajuan)
    {
        /*
        |--------------------------------------------------------------------------
        | Pastikan yang dibuka memang kenaikan jabatan
        |--------------------------------------------------------------------------
        */

        abort_if(
            $pengajuan->jenis_pengajuan !== 'kenaikan_jabatan',
            404
        );

        $pengajuan->load([
            'karyawan.departemen',
            'karyawan.jabatan',
            'jabatanAsal',
            'jabatanTujuan',
            'hasilAssessment.assessmentPeserta.assessment.jabatan',
            'hasilAssessment.assessmentPeserta.assessment.soalUpload',
        ]);

        return view(
            'hrd.kenaikan-jabatan.show',
            compact('pengajuan')
        );
    }

    public function process(PengajuanPengembangan $pengajuan)
    {
        abort_if(
            $pengajuan->jenis_pengajuan !== 'kenaikan_jabatan',
            404
        );

        $processError = null;

        DB::transaction(function () use ($pengajuan, &$processError): void {
            $dataPengajuan = PengajuanPengembangan::query()
                ->whereKey($pengajuan->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $dataPengajuan->jenis_pengajuan !== 'kenaikan_jabatan' ||
                $dataPengajuan->status !== 'diajukan'
            ) {
                $processError = 'Pengajuan ini tidak dapat diproses.';

                return;
            }

            $dataPengajuan->update([
                'status' => 'diproses',
            ]);

            app(BlockchainService::class)
                ->recordPengembanganEvent(
                    'status_pengajuan_pengembangan',
                    $dataPengajuan->refresh()
                );
        });

        if ($processError !== null) {
            return back()->with('error', $processError);
        }

        return back()->with(
            'success',
            'Pengajuan kenaikan jabatan sedang diproses.'
        );
    }

    public function approve(PengajuanPengembangan $pengajuan)
    {
        abort_if(
            $pengajuan->jenis_pengajuan !== 'kenaikan_jabatan',
            404
        );

        $approvalError = null;

        DB::transaction(function () use ($pengajuan, &$approvalError): void {
            $dataPengajuan = PengajuanPengembangan::query()
                ->whereKey($pengajuan->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($dataPengajuan->jenis_pengajuan !== 'kenaikan_jabatan') {
                $approvalError = 'Jenis pengajuan tidak sesuai.';

                return;
            }

            if (! in_array($dataPengajuan->status, ['diajukan', 'diproses'], true)) {
                $approvalError = 'Pengajuan ini tidak dapat disetujui.';

                return;
            }

            $hasilAssessment = HasilAssessment::with([
                'assessmentPeserta.karyawan',
                'assessmentPeserta.assessment',
            ])->find($dataPengajuan->hasil_assessment_id);

            if (! $hasilAssessment || $hasilAssessment->status !== 'lulus') {
                $approvalError =
                    'Pengajuan tidak dapat disetujui karena hasil assessment tidak lulus.';

                return;
            }

            $peserta = $hasilAssessment->assessmentPeserta;

            if (
                ! $peserta ||
                $peserta->status !== 'selesai' ||
                ! $peserta->assessment ||
                (int) $peserta->karyawan_id !== (int) $dataPengajuan->karyawan_id
            ) {
                $approvalError =
                    'Data peserta assessment tidak sesuai dengan pengajuan atau assessment belum selesai.';

                return;
            }

            $karyawan = $dataPengajuan->karyawan()
                ->lockForUpdate()
                ->first();

            if (! $karyawan || $karyawan->status !== 'aktif') {
                $approvalError =
                    'Kenaikan jabatan hanya dapat disetujui untuk karyawan aktif.';

                return;
            }

            if ((int) $karyawan->jabatan_id !== (int) $dataPengajuan->jabatan_asal_id) {
                $approvalError =
                    'Jabatan karyawan sudah berubah sejak pengajuan dibuat. Periksa kembali dan buat pengajuan baru jika diperlukan.';

                return;
            }

            $jabatanAsal = Jabatan::find($dataPengajuan->jabatan_asal_id);
            $jabatanTujuan = Jabatan::find($dataPengajuan->jabatan_tujuan_id);

            if (
                ! $jabatanAsal ||
                ! $jabatanTujuan ||
                ! $this->isValidProgression($jabatanAsal, $jabatanTujuan) ||
                (int) $peserta->assessment->jabatan_id !== (int) $jabatanTujuan->id
            ) {
                $approvalError =
                    'Jabatan tujuan tidak sesuai dengan urutan kenaikan dan assessment yang lulus.';

                return;
            }

            $adaPengajuanLain = PengajuanPengembangan::query()
                ->where('karyawan_id', $karyawan->id)
                ->where('id', '!=', $dataPengajuan->id)
                ->whereIn('status', ['diajukan', 'diproses'])
                ->exists();

            if ($adaPengajuanLain) {
                $approvalError =
                    'Karyawan masih memiliki pengajuan pengembangan lain yang belum selesai diputuskan.';

                return;
            }

            $tanggal = Carbon::now()->toDateString();

            RiwayatJabatan::query()
                ->where('karyawan_id', $karyawan->id)
                ->whereNull('tanggal_selesai')
                ->update([
                    'tanggal_selesai' => $tanggal,
                ]);

            $karyawan->update([
                'jabatan_id' => $jabatanTujuan->id,
            ]);

            RiwayatJabatan::create([
                'karyawan_id' => $karyawan->id,
                'jabatan_id' => $jabatanTujuan->id,
                'jenis_perubahan' => 'kenaikan',
                'pengajuan_pengembangan_id' => $dataPengajuan->id,
                'hasil_assessment_id' => $dataPengajuan->hasil_assessment_id,
                'tanggal_mulai' => $tanggal,
                'tanggal_selesai' => null,
                'keterangan' => 'Kenaikan jabatan berdasarkan hasil assessment.',
            ]);

            $dataPengajuan->update([
                'status' => 'disetujui',
                'tanggal_keputusan' => $tanggal,
            ]);

            app(BlockchainService::class)
                ->recordPengembanganEvent(
                    'keputusan_kenaikan_jabatan',
                    $dataPengajuan->refresh()
                );
        });

        if ($approvalError !== null) {
            return back()->with('error', $approvalError);
        }

        return back()->with(
            'success',
            'Kenaikan jabatan berhasil disetujui dan jabatan karyawan telah diperbarui.'
        );
    }

    public function reject(
        Request $request,
        PengajuanPengembangan $pengajuan
    ) {
        abort_if(
            $pengajuan->jenis_pengajuan !== 'kenaikan_jabatan',
            404
        );

        $validated = $request->validate([
            'catatan' => ['required', 'string'],
        ]);

        $rejectionError = null;

        DB::transaction(function () use (
            $pengajuan,
            $validated,
            &$rejectionError
        ): void {
            $dataPengajuan = PengajuanPengembangan::query()
                ->whereKey($pengajuan->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $dataPengajuan->jenis_pengajuan !== 'kenaikan_jabatan' ||
                ! in_array($dataPengajuan->status, ['diajukan', 'diproses'], true)
            ) {
                $rejectionError = 'Pengajuan ini tidak dapat ditolak.';

                return;
            }

            $tanggal = Carbon::now()->toDateString();

            $dataPengajuan->update([
                'status' => 'ditolak',
                'tanggal_keputusan' => $tanggal,
                'catatan' => $validated['catatan'],
            ]);

            app(BlockchainService::class)
                ->recordPengembanganEvent(
                    'keputusan_kenaikan_jabatan',
                    $dataPengajuan->refresh()
                );
        });

        if ($rejectionError !== null) {
            return back()->with('error', $rejectionError);
        }

        return back()->with(
            'success',
            'Pengajuan kenaikan jabatan telah ditolak dan keputusan telah dicatat ke blockchain.'
        );
    }

    private function isValidProgression(
        Jabatan $jabatanAsal,
        Jabatan $jabatanTujuan
    ): bool {
        if (
            $jabatanTujuan->tipe_jabatan !== 'struktural' ||
            (int) $jabatanTujuan->urutan_struktural < 1 ||
            (int) $jabatanTujuan->urutan_struktural > 6
        ) {
            return false;
        }

        if ($jabatanAsal->tipe_jabatan === 'operasional') {
            return (int) $jabatanTujuan->urutan_struktural === 1;
        }

        if (
            $jabatanAsal->tipe_jabatan !== 'struktural' ||
            (int) $jabatanAsal->urutan_struktural < 1 ||
            (int) $jabatanAsal->urutan_struktural >= 6
        ) {
            return false;
        }

        return (int) $jabatanTujuan->urutan_struktural ===
            (int) $jabatanAsal->urutan_struktural + 1;
    }
}
