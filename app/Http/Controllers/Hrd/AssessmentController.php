<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentKonfigurasiSoal;
use App\Models\AssessmentPeserta;
use App\Models\AssessmentSoal;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Soal;
use App\Models\SoalUpload;
use App\Services\BlockchainService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;


class AssessmentController extends Controller
{
    private const TIPE_PROSEDURS = [
        'pengoperasian_jabatan' => 'Prosedur Pengoperasian Jabatan',
        'keselamatan_kerja' => 'Prosedur Pengoperasian Keselamatan',
        'peraturan_perusahaan' => 'Peraturan Perusahaan',
    ];

    private const TIPE_PROSEDUR_LEVEL_SATU = [
        'keselamatan_kerja',
        'peraturan_perusahaan',
    ];

    private const TIPE_SOALS = [
        'pilihan_ganda' => 'Pilihan Ganda / Pilihan Tunggal',
        'multi_jawaban' => 'Multi Jawaban',
        'benar_salah' => 'Benar / Salah',
    ];

    /**
     * Menampilkan daftar assessment.
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'kategori' => ['sometimes', Rule::in([
                'semua',
                'pemindahan_jabatan',
                'peningkatan_skill',
                'kenaikan_jabatan',
            ])],
        ]);
        $kategori = $validated['kategori'] ?? 'semua';

        $query = Assessment::query()
            ->with(['jabatan', 'departemen', 'soalUpload'])
            ->withCount('peserta')
            ->when($kategori === 'pemindahan_jabatan', fn (Builder $query) => $query->whereHas(
                'jabatan',
                fn (Builder $query) => $query->where('tipe_jabatan', 'operasional')
                    ->where('level_jabatan', 1)
            ))
            ->when($kategori === 'peningkatan_skill', fn (Builder $query) => $query->whereHas(
                'jabatan',
                fn (Builder $query) => $query->where('tipe_jabatan', 'operasional')
                    ->whereBetween('level_jabatan', [2, 5])
            ))
            ->when($kategori === 'kenaikan_jabatan', fn (Builder $query) => $query->whereHas(
                'jabatan',
                fn (Builder $query) => $query->where('tipe_jabatan', 'struktural')
            ));

        $assessments = $query->latest()->paginate(10)->withQueryString();

        return view('hrd.assessment.index', compact('assessments', 'kategori'));
    }

    /**
     * Menampilkan form ubah informasi assessment.
     */
    public function edit(Assessment $assessment): View|RedirectResponse
    {
        if ($this->hasStartedParticipants($assessment)) {
            return redirect()->route('hrd.assessment.index')
                ->with('error', 'Assessment tidak dapat diubah karena sudah mulai dikerjakan atau memiliki hasil.');
        }

        return view('hrd.assessment.edit', compact('assessment'));
    }

    /**
     * Memperbarui informasi assessment.
     */
    public function update(Request $request, Assessment $assessment): RedirectResponse
    {
        if ($this->hasStartedParticipants($assessment)) {
            return redirect()->route('hrd.assessment.index')
                ->with('error', 'Assessment tidak dapat diubah karena sudah mulai dikerjakan atau memiliki hasil.');
        }

        $validated = $request->validate([
            'nama_assessment' => ['required', 'string', 'max:255'],
            'durasi' => ['required', 'integer', 'min:1'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        DB::transaction(function () use ($assessment, $validated): void {
            $assessment->update($validated);

            app(BlockchainService::class)
                ->recordAssessmentEvent(
                    'assessment_diubah',
                    $assessment->refresh()
                );
        });

        return redirect()->route('hrd.assessment.index')
            ->with('success', 'Assessment berhasil diperbarui.');
    }

    /**
     * Mengaktifkan atau menonaktifkan assessment.
     */
    public function toggleStatus(Assessment $assessment): RedirectResponse
    {
        $toggleError = null;
        $status = null;

        DB::transaction(function () use (
            $assessment,
            &$toggleError,
            &$status
        ): void {
            $lockedAssessment = Assessment::query()
                ->whereKey($assessment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! in_array($lockedAssessment->status, ['aktif', 'draft'], true)) {
                $toggleError = 'Status assessment ini tidak dapat diubah.';

                return;
            }

            $lockedAssessment->update([
                'status' => $lockedAssessment->status === 'aktif' ? 'draft' : 'aktif',
            ]);

            app(BlockchainService::class)
                ->recordAssessmentEvent(
                    'assessment_status_diubah',
                    $lockedAssessment->refresh()
                );

            $status = $lockedAssessment->status;
        });

        if ($toggleError !== null) {
            return back()->with('error', $toggleError);
        }

        return back()->with(
            'success',
            $status === 'aktif'
                ? 'Assessment berhasil diaktifkan.'
                : 'Assessment berhasil dinonaktifkan.'
        );
    }

    /**
     * Menghapus assessment yang belum memiliki riwayat pengerjaan.
     */
    public function destroy(Assessment $assessment): RedirectResponse
    {
        $destroyError = null;

        DB::transaction(function () use ($assessment, &$destroyError): void {
            $lockedAssessment = Assessment::query()
                ->whereKey($assessment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($this->hasStartedParticipants($lockedAssessment)) {
                $destroyError = 'Assessment tidak dapat dihapus karena sudah mulai dikerjakan atau memiliki hasil.';

                return;
            }

            $blockchain = app(BlockchainService::class);

            foreach ($lockedAssessment->peserta()->lockForUpdate()->get() as $participant) {
                $blockchain->recordAssessmentParticipantEvent(
                    'assessment_peserta_dihapus',
                    $participant,
                    true
                );
            }

            $blockchain->recordAssessmentEvent(
                'assessment_dihapus',
                $lockedAssessment,
                true
            );

            $lockedAssessment->delete();
        });

        if ($destroyError !== null) {
            return back()->with('error', $destroyError);
        }

        return redirect()->route('hrd.assessment.index')
            ->with('success', 'Assessment berhasil dihapus.');
    }

    private function hasStartedParticipants(Assessment $assessment): bool
    {
        return $assessment->peserta()
            ->where(function (Builder $query): void {
                $query->where('status', '!=', 'ditugaskan')
                    ->orWhereHas('jawabans')
                    ->orWhereHas('hasilAssessment');
            })
            ->exists();
    }

    /**
     * Menampilkan form tambah assessment.
     */
    public function create(): View
    {
        $departemens = Departemen::orderBy('nama_departemen')->get();

        $jabatans = Jabatan::query()
            ->where(function (Builder $query): void {
                $query->where(function (Builder $query): void {
                    $query->where('tipe_jabatan', 'operasional')
                        ->whereRaw('LOWER(TRIM(nama_jabatan)) != ?', ['crew'])
                        ->whereBetween('level_jabatan', [1, 5]);
                })->orWhere(function (Builder $query): void {
                    $query->where('tipe_jabatan', 'struktural')
                        ->whereBetween('urutan_struktural', [1, 6]);
                });
            })
            ->orderBy('tipe_jabatan')
            ->orderByRaw('LOWER(TRIM(nama_jabatan))')
            ->orderBy('level_jabatan')
            ->get();

        $karyawans = Karyawan::with(['jabatan', 'departemen'])
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        $soalUploads = SoalUpload::query()
            ->with(['jabatan', 'departemen'])
            ->whereNotNull('departemen_id')
            ->whereIn('jabatan_id', $jabatans->modelKeys())
            ->latest()
            ->get();
        $jabatanBankSoals = $jabatans->map(fn (Jabatan $jabatan): array => [
            'id' => (int) $jabatan->id,
            'nama' => $jabatan->nama_jabatan,
            'level' => $jabatan->tipe_jabatan === 'struktural' ? 1 : (int) $jabatan->level_jabatan,
            'nama_dasar' => $jabatan->namaDasar(),
            'jenis' => $jabatan->tipe_jabatan,
            'urutan_struktural' => (int) $jabatan->urutan_struktural,
        ])->values();

        $jumlahSoalTersedia = Soal::query()
            ->selectRaw('upload_id, nama_prosedur, tipe_soal, COUNT(*) AS jumlah')
            ->whereIn('upload_id', $soalUploads->modelKeys())
            ->where('status', true)
            ->groupBy('upload_id', 'nama_prosedur', 'tipe_soal')
            ->get();

        $jumlahPerBank = [];

        foreach ($jumlahSoalTersedia as $baris) {
            $tipeProsedur = Soal::tipeProsedurDariNama($baris->nama_prosedur);

            if (! isset(self::TIPE_SOALS[$baris->tipe_soal])) {
                continue;
            }

            $jumlahPerBank[$baris->upload_id][$tipeProsedur][$baris->tipe_soal] =
                ($jumlahPerBank[$baris->upload_id][$tipeProsedur][$baris->tipe_soal] ?? 0)
                + (int) $baris->jumlah;
        }

        $bankSoals = $soalUploads->map(function (SoalUpload $soalUpload) use ($jumlahPerBank): array {
            $jumlahTersedia = [];

            foreach (self::TIPE_PROSEDURS as $tipeProsedur => $labelProsedur) {
                foreach (self::TIPE_SOALS as $tipeSoal => $labelSoal) {
                    $jumlahTersedia[$tipeProsedur][$tipeSoal] =
                        $jumlahPerBank[$soalUpload->id][$tipeProsedur][$tipeSoal] ?? 0;
                }
            }

            return [
                'id' => $soalUpload->id,
                'jabatan_id' => $soalUpload->jabatan_id,
                'departemen_id' => $soalUpload->departemen_id,
                'nama_dasar' => $soalUpload->jabatan->namaDasar(),
                'nama_file' => $soalUpload->nama_file_tampilan,
                'nama_jabatan' => $soalUpload->jabatan?->nama_jabatan ?? 'Jabatan tidak tersedia',
                'level_jabatan' => (int) $soalUpload->jabatan?->level_jabatan,
                'jumlah_soal' => array_sum(array_map('array_sum', $jumlahTersedia)),
                'jumlah_tersedia' => $jumlahTersedia,
            ];
        });

        $tipeProsedurs = self::TIPE_PROSEDURS;
        $tipeSoals = self::TIPE_SOALS;

        return view('hrd.assessment.create', compact(
            'departemens',
            'jabatans',
            'karyawans',
            'bankSoals',
            'jabatanBankSoals',
            'tipeSoals'
        ));
    }

    /**
     * Menyimpan assessment baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_assessment' => ['required', 'string', 'max:255'],
            'departemen_id' => ['required', 'exists:departemens,id'],
            'jabatan_id' => ['required', 'exists:jabatans,id'],
            'durasi' => ['required', 'integer', 'min:1'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'soal_levels' => ['required', 'array', 'min:1', 'max:5'],
            'soal_levels.*' => ['required', 'integer', 'distinct', 'min:1', 'max:5'],
            'prosedur_levels' => ['sometimes', 'array'],
            'prosedur_levels.*' => ['required', 'array', 'min:1', 'max:3'],
            'prosedur_levels.*.*' => ['required', Rule::in(array_keys(self::TIPE_PROSEDURS))],
            // Konfigurasi soal: array of {level_soal, tipe_prosedur, tipe_soal, jumlah_soal}
            'konfigurasi' => ['required', 'array', 'min:1', 'max:45'],
            'konfigurasi.*.level_soal' => ['required', 'integer', 'min:1', 'max:5'],
            'konfigurasi.*.tipe_prosedur' => ['required', Rule::in(['pengoperasian_jabatan', 'keselamatan_kerja', 'peraturan_perusahaan'])],
            'konfigurasi.*.tipe_soal' => ['required', Rule::in(['pilihan_ganda', 'multi_jawaban', 'benar_salah'])],
            'konfigurasi.*.jumlah_soal' => ['required', 'integer', 'min:0', 'max:65535'],
        ]);

        $jabatan = Jabatan::findOrFail($validated['jabatan_id']);

        $isJabatanStruktural = $jabatan->tipe_jabatan === 'struktural';

        if (! $isJabatanStruktural && ($jabatan->tipe_jabatan !== 'operasional' || (int) $jabatan->level_jabatan < 1 || (int) $jabatan->level_jabatan > 5)) {
            return back()->withInput()->withErrors([
                'jabatan_id' => 'Assessment hanya dapat dibuat untuk jabatan operasional level 1 sampai 5 atau jabatan struktural.',
            ]);
        }

        if ($isJabatanStruktural && ((int) $jabatan->urutan_struktural < 1 || (int) $jabatan->urutan_struktural > 6)) {
            return back()->withInput()->withErrors([
                'jabatan_id' => 'Jabatan struktural harus memiliki urutan jabatan dari 1 sampai 6.',
            ]);
        }

        $levelTujuan = $isJabatanStruktural ? 1 : (int) $jabatan->level_jabatan;
        $urutanStrukturalTujuan = (int) $jabatan->urutan_struktural;
        $karyawansPesertaQuery = Karyawan::query()
            ->where('status', 'aktif')
            ->where('departemen_id', $validated['departemen_id']);

        if ($isJabatanStruktural) {
            $karyawansPesertaQuery->whereHas(
                'jabatan',
                fn (Builder $query) => $query->where(
                    'tipe_jabatan',
                    $urutanStrukturalTujuan === 1 ? 'operasional' : 'struktural'
                )->when(
                    $urutanStrukturalTujuan !== 1,
                    fn (Builder $query) => $query->where('urutan_struktural', $urutanStrukturalTujuan - 1)
                )
            );
        } elseif ($levelTujuan === 1) {
            $karyawansPesertaQuery->whereHas(
                'jabatan',
                fn (Builder $query) => $query->whereRaw('LOWER(TRIM(nama_jabatan)) = ?', ['crew'])
            );
        } else {
            $namaJabatanTujuan = $jabatan->namaDasar();
            $jabatanAsalIds = Jabatan::query()
                ->where('tipe_jabatan', 'operasional')
                ->where('level_jabatan', $levelTujuan - 1)
                ->get()
                ->filter(fn (Jabatan $kandidat): bool => $kandidat->namaDasar() === $namaJabatanTujuan)
                ->modelKeys();

            $karyawansPesertaQuery->whereIn('jabatan_id', $jabatanAsalIds);
        }

        $karyawansPeserta = $karyawansPesertaQuery->get();

        if ($karyawansPeserta->isEmpty()) {
            return back()->withInput()->withErrors([
                'departemen_id' => $isJabatanStruktural
                    ? 'Tidak ada karyawan aktif yang berada satu tahap jabatan sebelum jabatan struktural tujuan di departemen yang dipilih.'
                    : ($levelTujuan === 1
                        ? 'Tidak ada karyawan Crew aktif di departemen yang dipilih.'
                        : 'Tidak ada karyawan aktif dengan jabatan satu level di bawah jabatan tujuan di departemen yang dipilih.'),
            ]);
        }

        $validated['standar_nilai'] = $jabatan->standar_nilai;
        $levelSoals = array_map('intval', $validated['soal_levels']);

        if (array_diff($levelSoals, range(1, $levelTujuan)) !== []) {
            return back()->withInput()->withErrors([
                'soal_levels' => "Pilih level/SOP dari level 1 sampai level tujuan ($levelTujuan).",
            ]);
        }

        $prosedurPerLevel = [];
        foreach ($validated['prosedur_levels'] ?? [] as $level => $prosedurs) {
            $prosedurPerLevel[(int) $level] = array_values($prosedurs);
        }

        foreach ($prosedurPerLevel as $level => $prosedurs) {
            if (! in_array($level, $levelSoals, true)) {
                return back()->withInput()->withErrors([
                    'prosedur_levels' => 'Prosedur hanya dapat dipilih untuk level/SOP yang disertakan.',
                ]);
            }

            if (count($prosedurs) !== count(array_unique($prosedurs))) {
                return back()->withInput()->withErrors([
                    'prosedur_levels' => 'Kategori prosedur tidak boleh dipilih lebih dari satu kali pada level yang sama.',
                ]);
            }

            if (
                $level !== 1
                && array_intersect(self::TIPE_PROSEDUR_LEVEL_SATU, $prosedurs) !== []
            ) {
                return back()->withInput()->withErrors([
                    'prosedur_levels' => 'Prosedur keselamatan dan peraturan perusahaan hanya dapat diambil dari Level/SOP 1.',
                ]);
            }
        }

        $kombinasi = array_map(
            fn (array $konfigurasi): string => (int) $konfigurasi['level_soal'].'|'
                .$konfigurasi['tipe_prosedur'].'|'.$konfigurasi['tipe_soal'],
            $validated['konfigurasi']
        );

        if (count($kombinasi) !== count(array_unique($kombinasi))) {
            return back()->withInput()->withErrors([
                'konfigurasi' => 'Kombinasi level, prosedur, dan tipe soal tidak boleh dipilih lebih dari satu kali.',
            ]);
        }

        foreach ($validated['konfigurasi'] as $konfigurasi) {
            $levelSoal = (int) $konfigurasi['level_soal'];

            if (
                ! in_array($levelSoal, $levelSoals, true)
                || ! in_array($konfigurasi['tipe_prosedur'], $prosedurPerLevel[$levelSoal] ?? [], true)
            ) {
                return back()->withInput()->withErrors([
                    'konfigurasi' => 'Konfigurasi soal harus sesuai dengan level/SOP dan prosedur yang dipilih.',
                ]);
            }
        }

        $validated['konfigurasi'] = array_values(array_filter(
            $validated['konfigurasi'],
            fn (array $konfigurasi): bool => $konfigurasi['jumlah_soal'] > 0
        ));

        $levelDenganSoal = array_unique(array_map(
            fn (array $konfigurasi): int => (int) $konfigurasi['level_soal'],
            $validated['konfigurasi']
        ));

        if ($validated['konfigurasi'] === [] || array_diff($levelSoals, $levelDenganSoal) !== []) {
            return back()->withInput()->withErrors([
                'konfigurasi' => 'Setiap level/SOP yang dipilih harus memiliki minimal satu soal dengan jumlah lebih dari 0.',
            ]);
        }

        $validatedBanks = $request->validate([
            'bank_uploads' => ['required', 'array'],
            'bank_uploads.*' => ['required', 'integer', 'distinct', 'exists:soal_uploads,id'],
        ], [
            'bank_uploads.required' => 'Pilih satu file bank soal untuk setiap level/SOP yang dipilih.',
            'bank_uploads.*.required' => 'Pilih file bank soal untuk setiap level/SOP yang dipilih.',
        ]);

        $levelBankTerpilih = array_map('intval', array_keys($validatedBanks['bank_uploads']));
        sort($levelBankTerpilih);
        $levelSoalsTerurut = $levelSoals;
        sort($levelSoalsTerurut);

        if ($levelBankTerpilih !== $levelSoalsTerurut) {
            return back()->withInput()->withErrors([
                'bank_uploads' => 'Pilih tepat satu file bank soal untuk setiap level/SOP yang dipilih.',
            ]);
        }

        if ($isJabatanStruktural) {
            $jabatanPerLevel = collect([1 => collect([$jabatan])]);
        } else {
            $jabatansTerkait = Jabatan::query()
                ->where('tipe_jabatan', 'operasional')
                ->whereBetween('level_jabatan', [1, $levelTujuan])
                ->get();
            $jabatanIdsKumulatif = $this->jabatanIdsKumulatif($jabatan, $jabatansTerkait);
            $jabatanPerLevel = $jabatansTerkait
                ->filter(fn (Jabatan $kandidat): bool => in_array((int) $kandidat->id, $jabatanIdsKumulatif, true))
                ->groupBy(fn (Jabatan $kandidat): int => (int) $kandidat->level_jabatan);
        }
        $levelTidakValid = collect($levelSoals)
            ->filter(fn (int $level): bool => $jabatanPerLevel->get($level)?->count() !== 1)
            ->values();

        if ($levelTidakValid->isNotEmpty()) {
            return back()->withInput()->withErrors([
                'soal_levels' => 'Jabatan harus memiliki tepat satu data untuk setiap level/SOP yang dipilih. Level bermasalah: '
                    .$levelTidakValid->implode(', ').'.',
            ]);
        }

        $bankUploadIdsPerLevel = $validatedBanks['bank_uploads'];
        $bankUploadsTerpilih = SoalUpload::query()
            ->with('jabatan')
            ->where('departemen_id', $validated['departemen_id'])
            ->whereIn('id', array_values($bankUploadIdsPerLevel))
            ->get()
            ->keyBy(fn (SoalUpload $bankUpload): int => $isJabatanStruktural
                ? 1
                : (int) $bankUpload->jabatan?->level_jabatan);

        foreach ($levelSoals as $level) {
            $bankUpload = $bankUploadsTerpilih->get($level);
            $jabatanLevel = $jabatanPerLevel->get($level)?->first();

            if (! $bankUpload || ! $jabatanLevel || (int) $bankUpload->jabatan_id !== (int) $jabatanLevel->id
                || (int) $bankUpload->getKey() !== (int) $bankUploadIdsPerLevel[$level]) {
                return back()->withInput()->withErrors([
                    'bank_uploads' => $isJabatanStruktural
                        ? 'File bank soal yang dipilih tidak sesuai dengan departemen dan jabatan struktural assessment.'
                        : "File bank soal yang dipilih untuk level $level tidak sesuai dengan departemen dan jabatan assessment.",
                ]);
            }
        }

        $soalUpload = $bankUploadsTerpilih->get($levelTujuan) ?? $bankUploadsTerpilih->first();
        $bankLevelPerUpload = $bankUploadsTerpilih->mapWithKeys(
            fn (SoalUpload $bankUpload): array => [
                $bankUpload->id => $isJabatanStruktural ? 1 : (int) $bankUpload->jabatan->level_jabatan,
            ]
        );
        $soalPools = [];
        $soalsTersedia = Soal::query()
            ->whereIn('upload_id', array_values($bankUploadIdsPerLevel))
            ->where('status', true)
            ->get(['id', 'upload_id', 'nama_prosedur', 'tipe_soal']);

        foreach ($soalsTersedia as $soal) {
            $levelSoal = $bankLevelPerUpload->get($soal->upload_id);
            $tipeProsedur = Soal::tipeProsedurDariNama($soal->nama_prosedur);
            $soalPools[$levelSoal][$tipeProsedur][$soal->tipe_soal][] = $soal->id;
        }

        // Pastikan setiap kombinasi level, prosedur, dan tipe soal tersedia cukup.
        foreach ($validated['konfigurasi'] as $k) {
            $tersedia = count($soalPools[$k['level_soal']][$k['tipe_prosedur']][$k['tipe_soal']] ?? []);

            if ($tersedia < $k['jumlah_soal']) {
                return back()->withInput()->withErrors([
                    'konfigurasi' => 'Bank soal tidak cukup: '
                        .'Level '.$k['level_soal'].' / '.self::TIPE_PROSEDURS[$k['tipe_prosedur']].' / '
                        .self::TIPE_SOALS[$k['tipe_soal']]." hanya tersedia {$tersedia} soal, dibutuhkan {$k['jumlah_soal']}.",
                ]);
            }
        }

        DB::transaction(function () use ($validated, $jabatan, $soalUpload, $soalPools, $karyawansPeserta) {

            $assessment = Assessment::create([
                'nama_assessment' => $validated['nama_assessment'],
                'jabatan_id' => $jabatan->id,
                'departemen_id' => $validated['departemen_id'],
                'soal_upload_id' => $soalUpload->id,
                'standar_nilai' => $validated['standar_nilai'],
                'durasi' => $validated['durasi'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'status' => 'aktif',
            ]);

            $blockchain = app(BlockchainService::class);
            $blockchain->recordAssessmentEvent('assessment_dibuat', $assessment);

            // Simpan konfigurasi & ambil soal acak
            $nomorSoal = 1;
            foreach ($validated['konfigurasi'] as $k) {
                AssessmentKonfigurasiSoal::create([
                    'assessment_id' => $assessment->id,
                    'level_soal' => $k['level_soal'],
                    'tipe_prosedur' => $k['tipe_prosedur'],
                    'tipe_soal' => $k['tipe_soal'],
                    'jumlah_soal' => $k['jumlah_soal'],
                ]);

                $soalAcak = Soal::whereIn('id', $soalPools[$k['level_soal']][$k['tipe_prosedur']][$k['tipe_soal']] ?? [])
                    ->where('tipe_soal', $k['tipe_soal'])
                    ->where('status', true)
                    ->inRandomOrder()
                    ->limit($k['jumlah_soal'])
                    ->get();

                foreach ($soalAcak as $soal) {
                    AssessmentSoal::create([
                        'assessment_id' => $assessment->id,
                        'soal_id' => $soal->id,
                        'nomor_soal' => $nomorSoal++,
                    ]);
                }
            }

            // Peserta
            foreach ($karyawansPeserta as $karyawan) {
                $participant = AssessmentPeserta::create([
                    'assessment_id' => $assessment->id,
                    'karyawan_id' => $karyawan->id,
                    'percobaan_ke' => 1,
                    'status' => 'ditugaskan',
                    'waktu_mulai' => null,
                    'waktu_selesai' => null,
                ]);

                $blockchain->recordAssessmentParticipantEvent(
                    'assessment_peserta_ditugaskan',
                    $participant
                );
            }
        });

        return redirect()->route('hrd.assessment.index')
            ->with('success', 'Assessment berhasil dibuat.');
    }

    /**
     * Menugaskan assessment yang sama kembali kepada karyawan.
     */
    public function tugaskanUlang(Request $request, Assessment $assessment)
    {
        $validated = $request->validate([
            'karyawan_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'karyawan_ids.*' => [
                'integer',
                'distinct',
                'exists:karyawans,id',
            ],
        ], [
            'karyawan_ids.required' => 'Minimal satu karyawan harus dipilih.',

            'karyawan_ids.min' => 'Minimal satu karyawan harus dipilih.',
        ]);

        if ($assessment->status === 'dibatalkan') {
            return back()
                ->with(
                    'error',
                    'Assessment yang sudah dibatalkan tidak dapat ditugaskan kembali.'
                );
        }

        if ($assessment->departemen_id !== null) {
            $jumlahPesertaSesuaiDepartemen = Karyawan::query()
                ->whereIn('id', $validated['karyawan_ids'])
                ->where('departemen_id', $assessment->departemen_id)
                ->count();

            if ($jumlahPesertaSesuaiDepartemen !== count($validated['karyawan_ids'])) {
                return back()->with(
                    'error',
                    'Peserta assessment harus berasal dari departemen yang sama.'
                );
            }
        }

        DB::transaction(function () use (
            $validated,
            $assessment
        ) {
            foreach ($validated['karyawan_ids'] as $karyawanId) {

                /*
                 * Ambil percobaan terakhir karyawan
                 * pada assessment yang sama.
                 */
                $pesertaTerakhir = AssessmentPeserta::where(
                    'assessment_id',
                    $assessment->id
                )
                    ->where('karyawan_id', $karyawanId)
                    ->orderByDesc('percobaan_ke')
                    ->lockForUpdate()
                    ->first();

                /*
                 * Jika masih ada percobaan yang belum selesai,
                 * jangan buat percobaan baru.
                 */
                if (
                    $pesertaTerakhir &&
                    in_array(
                        $pesertaTerakhir->status,
                        [
                            'ditugaskan',
                            'sedang_mengerjakan',
                        ]
                    )
                ) {
                    continue;
                }

                /*
                 * Tentukan nomor percobaan berikutnya.
                 */
                $percobaanBerikutnya =
                    ($pesertaTerakhir?->percobaan_ke ?? 0) + 1;

                /*
                 * Buat peserta baru.
                 */
                $participant = AssessmentPeserta::create([
                    'assessment_id' => $assessment->id,

                    'karyawan_id' => $karyawanId,

                    'percobaan_ke' => $percobaanBerikutnya,

                    'status' => 'ditugaskan',

                    'waktu_mulai' => null,

                    'waktu_selesai' => null,
                ]);

                app(BlockchainService::class)
                    ->recordAssessmentParticipantEvent(
                        'assessment_peserta_ditugaskan_ulang',
                        $participant
                    );
            }
        });

        return back()->with(
            'success',
            'Assessment berhasil ditugaskan kembali. Percobaan baru dibuat tanpa mengubah hasil ujian sebelumnya.'
        );
    }

    /**
     * Menampilkan detail assessment.
     */
    public function show(Assessment $assessment)
    {
        $assessment->load([
            'jabatan',
            'departemen',
            'soalUpload',
            'konfigurasiSoals',
            'soals.pilihanJawabans',
            'peserta.karyawan',
        ]);

        $karyawans = Karyawan::query()
            ->where('status', 'aktif')
            ->when(
                $assessment->departemen_id !== null,
                fn (Builder $query) => $query->where('departemen_id', $assessment->departemen_id)
            )
            ->orderBy('nama')
            ->get();

        return view('hrd.assessment.show', compact('assessment', 'karyawans'));
    }

    /**
     * @param  Collection<int, Jabatan>  $jabatans
     * @return array<int, int>
     */
    private function jabatanIdsKumulatif(Jabatan $jabatan, Collection $jabatans): array
    {
        $namaDasar = $jabatan->namaDasar();

        return $jabatans
            ->filter(fn (Jabatan $kandidat): bool => (int) $kandidat->level_jabatan <= (int) $jabatan->level_jabatan
                && $kandidat->namaDasar() === $namaDasar)
            ->map(fn (Jabatan $kandidat): int => (int) $kandidat->getKey())
            ->all();
    }
}
