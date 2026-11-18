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

class PeningkatanSkillController extends Controller
{
    public function index(Request $request)
    {
        $query = PengajuanPengembangan::with([
            'karyawan.departemen',
            'skill',
            'hasilAssessment.assessmentPeserta.assessment',
        ])->where(
            'jenis_pengajuan',
            'peningkatan_skill'
        );

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
            'hrd.peningkatan-skill.index',
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
                                $query->where('tipe_jabatan', 'operasional')
                                    ->whereBetween('level_jabatan', [1, 4])
                                    ->whereRaw(
                                        'LOWER(TRIM(nama_jabatan)) != ?',
                                        ['crew']
                                    );
                            });
                    });
            })
            ->whereDoesntHave(
                'assessmentPeserta.karyawan.pengajuanPengembangans',
                function (Builder $query) {
                    $query->whereIn('status', ['diajukan', 'diproses']);
                }
            )
            ->latest('tanggal_ujian')
            ->get()
            ->filter(function (HasilAssessment $hasilAssessment): bool {
                $peserta = $hasilAssessment->assessmentPeserta;
                $jabatanAsal = $peserta?->karyawan?->jabatan;
                $jabatanTujuan = $peserta?->assessment?->jabatan;

                return $jabatanAsal !== null
                    && $jabatanTujuan !== null
                    && $this->isEligibleSourcePosition($jabatanAsal)
                    && $this->isNextLevelPosition(
                        $jabatanAsal,
                        $jabatanTujuan
                    );
            })
            ->values();

        return view(
            'hrd.peningkatan-skill.create',
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
                ->withErrors(['hasil_assessment_id' => 'Hasil assessment harus berstatus lulus.'])
                ->withInput();
        }

        $peserta = $hasilAssessment->assessmentPeserta;

        if (! $peserta || ! $peserta->karyawan || ! $peserta->assessment) {
            return back()
                ->withErrors(['hasil_assessment_id' => 'Data peserta atau assessment tidak lengkap.'])
                ->withInput();
        }

        if ($peserta->status !== 'selesai') {
            return back()
                ->withErrors(['hasil_assessment_id' => 'Assessment karyawan belum selesai.'])
                ->withInput();
        }

        $karyawan = $peserta->karyawan;
        $jabatanAsal = $karyawan->jabatan;

        if ($karyawan->status !== 'aktif') {
            return back()
                ->withErrors(['hasil_assessment_id' => 'Hanya karyawan aktif yang dapat mengajukan peningkatan skill.'])
                ->withInput();
        }

        if (! $jabatanAsal || ! $this->isEligibleSourcePosition($jabatanAsal)) {
            return back()
                ->withErrors(['hasil_assessment_id' => 'Karyawan harus berada di jabatan operasional non-Crew Level 1 sampai 4.'])
                ->withInput();
        }

        $jabatanTujuan = $peserta->assessment->jabatan;

        if (
            ! $jabatanTujuan ||
            ! $this->isNextLevelPosition($jabatanAsal, $jabatanTujuan)
        ) {
            return back()
                ->withErrors(['hasil_assessment_id' => 'Assessment yang digunakan harus untuk jabatan operasional satu level di atas jabatan karyawan saat ini dan pada jalur jabatan yang sama.'])
                ->withInput();
        }

        $sudahAda = PengajuanPengembangan::query()
            ->where('karyawan_id', $karyawan->id)
            ->whereIn('status', ['diajukan', 'diproses'])
            ->exists();

        if ($sudahAda) {
            return back()
                ->withErrors(['hasil_assessment_id' => 'Karyawan ini masih memiliki pengajuan pengembangan yang belum selesai diputuskan.'])
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
                'jenis_pengajuan' => 'peningkatan_skill',
                'jabatan_asal_id' => $jabatanAsal->id,
                'jabatan_tujuan_id' => $jabatanTujuan->id,
                'skill_id' => null,
                'level_sebelum' => $jabatanAsal->level_jabatan,
                'level_sesudah' => $jabatanTujuan->level_jabatan,
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
            ->route('hrd.peningkatan-skill.index')
            ->with('success', 'Pengajuan peningkatan skill berhasil dibuat.');
    }

    public function show(PengajuanPengembangan $pengajuan)
    {
        abort_if(
            $pengajuan->jenis_pengajuan !== 'peningkatan_skill',
            404
        );

        $pengajuan->load([
            'karyawan.departemen',
            'karyawan.jabatan',
            'jabatanAsal',
            'jabatanTujuan',
            'hasilAssessment.assessmentPeserta.assessment.jabatan',
        ]);

        return view(
            'hrd.peningkatan-skill.show',
            compact('pengajuan')
        );
    }

    public function process(PengajuanPengembangan $pengajuan)
    {
        abort_if(
            $pengajuan->jenis_pengajuan !== 'peningkatan_skill',
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
            'Pengajuan peningkatan skill sedang diproses.'
        );
    }

    public function approve(PengajuanPengembangan $pengajuan)
    {
        abort_if(
            $pengajuan->jenis_pengajuan !== 'peningkatan_skill',
            404
        );

        $approvalError = null;

        DB::transaction(function () use ($pengajuan, &$approvalError): void {
            $dataPengajuan = PengajuanPengembangan::query()
                ->whereKey($pengajuan->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($dataPengajuan->jenis_pengajuan !== 'peningkatan_skill') {
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
                    'Peningkatan skill hanya dapat disetujui untuk karyawan aktif.';

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
                ! $this->isEligibleSourcePosition($jabatanAsal) ||
                ! $this->isNextLevelPosition($jabatanAsal, $jabatanTujuan)
            ) {
                $approvalError =
                    'Jabatan tujuan harus satu level di atas jabatan operasional karyawan dengan nama jabatan yang sama.';

                return;
            }

            $levelSebelum = (int) $jabatanAsal->level_jabatan;
            $levelSesudah = (int) $jabatanTujuan->level_jabatan;

            if (
                ($dataPengajuan->level_sebelum !== null &&
                    (int) $dataPengajuan->level_sebelum !== $levelSebelum) ||
                ($dataPengajuan->level_sesudah !== null &&
                    (int) $dataPengajuan->level_sesudah !== $levelSesudah) ||
                (int) $peserta->assessment->jabatan_id !== (int) $jabatanTujuan->id
            ) {
                $approvalError =
                    'Level atau jabatan pada assessment tidak sesuai dengan pengajuan.';

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

            RiwayatJabatan::where('karyawan_id', $karyawan->id)
                ->whereNull('tanggal_selesai')
                ->update(['tanggal_selesai' => $tanggal]);

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
                'keterangan' => 'Peningkatan skill: '.$jabatanAsal->nama_jabatan.' → '.$jabatanTujuan->nama_jabatan,
            ]);

            $dataPengajuan->update([
                'level_sebelum' => $levelSebelum,
                'level_sesudah' => $levelSesudah,
                'status' => 'disetujui',
                'tanggal_keputusan' => $tanggal,
            ]);

            app(BlockchainService::class)
                ->recordPengembanganEvent(
                    'keputusan_peningkatan_skill',
                    $dataPengajuan->refresh()
                );
        });

        if ($approvalError !== null) {
            return back()->with('error', $approvalError);
        }

        return back()->with(
            'success',
            'Peningkatan skill berhasil disetujui dan level skill karyawan telah diperbarui.'
        );
    }

    public function reject(
        Request $request,
        PengajuanPengembangan $pengajuan
    ) {
        abort_if(
            $pengajuan->jenis_pengajuan !== 'peningkatan_skill',
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
            | Update status pengajuan
            |--------------------------------------------------------------------------
            */

            $dataPengajuan->update([
                'status' => 'ditolak',
                'tanggal_keputusan' => $tanggal,
                'catatan' => $validated['catatan'],
            ]);

            app(BlockchainService::class)
                ->recordPengembanganEvent(
                    'keputusan_peningkatan_skill',
                    $dataPengajuan->refresh()
                );
        });

        if ($rejectionError !== null) {
            return back()->with('error', $rejectionError);
        }

        return back()->with(
            'success',
            'Pengajuan peningkatan skill telah ditolak dan keputusan telah dicatat ke blockchain.'
        );
    }

    private function isEligibleSourcePosition(Jabatan $jabatan): bool
    {
        return $jabatan->tipe_jabatan === 'operasional'
            && ! $jabatan->isCrew()
            && (int) $jabatan->level_jabatan >= 1
            && (int) $jabatan->level_jabatan < 5;
    }

    private function isNextLevelPosition(
        Jabatan $jabatanAsal,
        Jabatan $jabatanTujuan
    ): bool {
        return $jabatanTujuan->tipe_jabatan === 'operasional'
            && (int) $jabatanTujuan->level_jabatan === (int) $jabatanAsal->level_jabatan + 1
            && $jabatanTujuan->namaDasar() === $jabatanAsal->namaDasar();
    }
}
