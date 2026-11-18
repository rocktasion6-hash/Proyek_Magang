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

class PemindahanJabatanController extends Controller
{
    public function index(Request $request)
    {
        $query = PengajuanPengembangan::with([
            'karyawan.departemen',
            'jabatanAsal',
            'jabatanTujuan',
            'hasilAssessment.assessmentPeserta.assessment',
        ])->where('jenis_pengajuan', 'pemindahan_jabatan');

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

        return view(
            'hrd.pemindahan-jabatan.index',
            compact('pengajuans')
        );
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
                                $query->whereRaw(
                                    'LOWER(TRIM(nama_jabatan)) = ?',
                                    ['crew']
                                );
                            });
                    })
                    ->whereHas('assessment.jabatan', function (Builder $query) {
                        $query->where('tipe_jabatan', 'operasional')
                            ->where('level_jabatan', 1)
                            ->whereRaw(
                                'LOWER(TRIM(nama_jabatan)) != ?',
                                ['crew']
                            );
                    });
            })
            ->whereDoesntHave(
                'assessmentPeserta.karyawan.pengajuanPengembangans',
                function (Builder $query) {
                    $query->whereIn('status', ['diajukan', 'diproses']);
                }
            )
            ->latest('tanggal_ujian')
            ->get();

        return view(
            'hrd.pemindahan-jabatan.create',
            compact('hasilAssessments')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hasil_assessment_id' => [
                'required',
                'exists:hasil_assessments,id',
            ],
            'tanggal_pengajuan' => [
                'required',
                'date',
            ],
            'catatan' => [
                'nullable',
                'string',
            ],
        ]);

        $hasilAssessment = HasilAssessment::with([
            'assessmentPeserta.karyawan.jabatan',
            'assessmentPeserta.assessment.jabatan',
        ])->findOrFail($validated['hasil_assessment_id']);

        if ($hasilAssessment->status !== 'lulus') {
            return back()
                ->withErrors([
                    'hasil_assessment_id' => 'Karyawan harus lulus assessment sebelum mengajukan pemindahan jabatan.',
                ])
                ->withInput();
        }

        $peserta = $hasilAssessment->assessmentPeserta;

        if (
            ! $peserta ||
            ! $peserta->karyawan ||
            ! $peserta->assessment
        ) {
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

        if ($karyawan->status !== 'aktif') {
            return back()
                ->withErrors([
                    'hasil_assessment_id' => 'Hanya karyawan aktif yang dapat mengajukan pemindahan jabatan.',
                ])
                ->withInput();
        }

        if (! $jabatanAsal?->isCrew()) {
            return back()
                ->withErrors([
                    'hasil_assessment_id' => 'Pemindahan jabatan hanya dapat diajukan oleh karyawan yang saat ini menjabat sebagai Crew.',
                ])
                ->withInput();
        }

        $jabatanTujuan = $peserta->assessment->jabatan;

        if (! $jabatanTujuan?->isLevelSatu()) {
            return back()
                ->withErrors([
                    'hasil_assessment_id' => 'Assessment pemindahan harus ditujukan untuk jabatan operasional Level 1 selain Crew.',
                ])
                ->withInput();
        }

        $sudahAda = PengajuanPengembangan::where(
            'karyawan_id',
            $karyawan->id
        )
            ->whereIn('status', [
                'diajukan',
                'diproses',
            ])
            ->exists();

        if ($sudahAda) {
            return back()
                ->withErrors([
                    'hasil_assessment_id' => 'Karyawan ini masih memiliki pengajuan pengembangan yang belum selesai diputuskan.',
                ])
                ->withInput();
        }

        DB::transaction(function () use (
            $karyawan,
            $hasilAssessment,
            $jabatanAsal,
            $jabatanTujuan,
            $validated
        ) {
            $pengajuan = PengajuanPengembangan::create([
                'karyawan_id' => $karyawan->id,

                'hasil_assessment_id' => $hasilAssessment->id,

                'jenis_pengajuan' => 'pemindahan_jabatan',

                'jabatan_asal_id' => $jabatanAsal->id,

                'jabatan_tujuan_id' => $jabatanTujuan->id,

                'skill_id' => null,

                'status' => 'diajukan',

                'tanggal_pengajuan' => $validated['tanggal_pengajuan'],

                'tanggal_keputusan' => null,

                'catatan' => $validated['catatan'] ?? null,
            ]);

            $blockchain = app(BlockchainService::class);

            $blockchainRecordExists = BlockchainRecord::query()
                ->where(
                    'entity_type',
                    'pengajuan_pengembangan'
                )
                ->where('entity_id', $pengajuan->id)
                ->exists();

            if (! $blockchainRecordExists) {
                $blockchain->recordPengembanganEvent(
                    'pengajuan_pengembangan',
                    $pengajuan
                );
            }
        });

        return redirect()
            ->route('hrd.pemindahan-jabatan.index')
            ->with(
                'success',
                'Pengajuan pemindahan jabatan berhasil dibuat.'
            );
    }

    public function show(PengajuanPengembangan $pengajuan)
    {
        abort_if(
            $pengajuan->jenis_pengajuan !== 'pemindahan_jabatan',
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
            'hrd.pemindahan-jabatan.show',
            compact('pengajuan')
        );
    }

    public function process(PengajuanPengembangan $pengajuan)
    {
        abort_if(
            $pengajuan->jenis_pengajuan !== 'pemindahan_jabatan',
            404
        );

        $processError = null;

        DB::transaction(function () use ($pengajuan, &$processError): void {
            $dataPengajuan = PengajuanPengembangan::query()
                ->whereKey($pengajuan->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($dataPengajuan->status !== 'diajukan') {
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
            return back()->with(
                'error',
                $processError
            );
        }

        return back()->with(
            'success',
            'Pengajuan pemindahan jabatan sedang diproses.'
        );
    }

    public function approve(PengajuanPengembangan $pengajuan)
    {
        abort_if(
            $pengajuan->jenis_pengajuan !== 'pemindahan_jabatan',
            404
        );

        $approvalError = null;

        DB::transaction(function () use (
            $pengajuan,
            &$approvalError
        ) {
            // Kunci pengajuan agar tidak diproses bersamaan.
            $dataPengajuan = PengajuanPengembangan::whereKey(
                $pengajuan->id
            )
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $dataPengajuan->jenis_pengajuan !==
                'pemindahan_jabatan'
            ) {
                $approvalError =
                    'Jenis pengajuan tidak sesuai.';

                return;
            }

            if (! in_array(
                $dataPengajuan->status,
                ['diajukan', 'diproses']
            )) {
                $approvalError =
                    'Pengajuan ini sudah tidak dapat disetujui.';

                return;
            }

            // Pastikan hasil ujian masih lulus.
            $hasilAssessment = HasilAssessment::with([
                'assessmentPeserta.karyawan',
                'assessmentPeserta.assessment',
            ])->find($dataPengajuan->hasil_assessment_id);

            if (
                ! $hasilAssessment ||
                $hasilAssessment->status !== 'lulus'
            ) {
                $approvalError =
                    'Pengajuan tidak dapat disetujui karena hasil assessment tidak lulus.';

                return;
            }

            $peserta = $hasilAssessment->assessmentPeserta;

            if (
                ! $peserta ||
                ! $peserta->karyawan ||
                ! $peserta->assessment ||
                $peserta->status !== 'selesai' ||
                (int) $peserta->karyawan_id !==
                    (int) $dataPengajuan->karyawan_id
            ) {
                $approvalError =
                    'Data peserta assessment tidak sesuai dengan pengajuan.';

                return;
            }

            // Assessment harus sesuai dengan jabatan tujuan.
            if (
                (int) $peserta->assessment->jabatan_id !==
                (int) $dataPengajuan->jabatan_tujuan_id
            ) {
                $approvalError =
                    'Assessment bukan untuk jabatan tujuan yang diajukan.';

                return;
            }

            // Kunci data karyawan dan periksa jabatan terkininya.
            $karyawan = $dataPengajuan->karyawan()
                ->lockForUpdate()
                ->firstOrFail();

            if (
                (int) $karyawan->jabatan_id !==
                (int) $dataPengajuan->jabatan_asal_id
            ) {
                $approvalError =
                    'Jabatan karyawan sudah berubah sejak pengajuan dibuat. Periksa kembali jabatan asal dan buat pengajuan baru jika diperlukan.';

                return;
            }

            if ($karyawan->status !== 'aktif') {
                $approvalError =
                    'Pemindahan tidak dapat disetujui karena karyawan sudah tidak aktif.';

                return;
            }

            $jabatanAsal = Jabatan::find(
                $dataPengajuan->jabatan_asal_id
            );

            $jabatanTujuan = Jabatan::find(
                $dataPengajuan->jabatan_tujuan_id
            );

            if (! $jabatanAsal || ! $jabatanTujuan) {
                $approvalError =
                    'Data jabatan asal atau tujuan tidak ditemukan.';

                return;
            }

            if (
                ! $jabatanAsal->isCrew() ||
                ! $jabatanTujuan->isLevelSatu()
            ) {
                $approvalError =
                    'Pemindahan hanya berlaku dari jabatan Crew ke jabatan operasional Level 1 selain Crew.';

                return;
            }

            if (
                (int) $peserta->assessment->jabatan_id !==
                (int) $jabatanTujuan->id
            ) {
                $approvalError =
                    'Assessment bukan untuk jabatan tujuan yang diajukan.';

                return;
            }

            $tanggal = Carbon::now()->toDateString();

            // Tutup riwayat jabatan sebelumnya.
            RiwayatJabatan::where(
                'karyawan_id',
                $karyawan->id
            )
                ->whereNull('tanggal_selesai')
                ->update([
                    'tanggal_selesai' => $tanggal,
                ]);

            // Perbarui jabatan karyawan.
            $karyawan->update([
                'jabatan_id' => $jabatanTujuan->id,
            ]);

            // Simpan riwayat jabatan baru.
            RiwayatJabatan::create([
                'karyawan_id' => $karyawan->id,

                'jabatan_id' => $jabatanTujuan->id,

                'jenis_perubahan' => 'pemindahan',

                'pengajuan_pengembangan_id' => $dataPengajuan->id,

                'hasil_assessment_id' => $dataPengajuan->hasil_assessment_id,

                'tanggal_mulai' => $tanggal,

                'tanggal_selesai' => null,

                'keterangan' => 'Pemindahan jabatan berdasarkan hasil assessment dan persetujuan HRD.',
            ]);

            // Simpan keputusan.
            $dataPengajuan->update([
                'status' => 'disetujui',
                'tanggal_keputusan' => $tanggal,
            ]);

            // Catat keputusan pada blockchain.
            app(BlockchainService::class)
                ->recordPengembanganEvent(
                    'keputusan_pemindahan_jabatan',
                    $dataPengajuan->refresh()
                );
        });

        if ($approvalError !== null) {
            return back()->with(
                'error',
                $approvalError
            );
        }

        return back()->with(
            'success',
            'Pemindahan jabatan berhasil disetujui dan jabatan karyawan telah diperbarui.'
        );
    }

    public function reject(
        Request $request,
        PengajuanPengembangan $pengajuan
    ) {
        abort_if(
            $pengajuan->jenis_pengajuan !== 'pemindahan_jabatan',
            404
        );

        $validated = $request->validate([
            'catatan' => [
                'required',
                'string',
            ],
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

            if (! in_array($dataPengajuan->status, ['diajukan', 'diproses'], true)) {
                $rejectionError = 'Pengajuan ini tidak dapat ditolak.';

                return;
            }

            $tanggal = Carbon::now()->toDateString();

            /*
            |--------------------------------------------------------------------------
            | Update pengajuan menjadi ditolak
            |--------------------------------------------------------------------------
            */

            $dataPengajuan->update([
                'status' => 'ditolak',
                'tanggal_keputusan' => $tanggal,
                'catatan' => $validated['catatan'],
            ]);

            app(BlockchainService::class)
                ->recordPengembanganEvent(
                    'keputusan_pemindahan_jabatan',
                    $dataPengajuan->refresh()
                );
        });

        if ($rejectionError !== null) {
            return back()->with('error', $rejectionError);
        }

        return back()->with(
            'success',
            'Pengajuan pemindahan jabatan telah ditolak dan keputusan telah dicatat ke blockchain.'
        );
    }
}
