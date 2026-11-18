<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\PilihanJawaban;
use App\Models\Soal;
use App\Models\SoalUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SoalController extends Controller
{
    // Label tipe prosedur
    const LABEL_PROSEDUR = [
        'pengoperasian_jabatan' => 'Prosedur Pengoperasian Jabatan',
        'keselamatan_kerja' => 'Prosedur Keselamatan Kerja',
        'peraturan_perusahaan' => 'Prosedur Peraturan Perusahaan',
    ];

    // SOP jumlah soal per level
    const SOP_JUMLAH = [
        1 => 150,
        2 => 50,
        3 => 50,
        4 => 50,
        5 => 50,
    ];

    public function index(Request $request)
    {
        $uploads = SoalUpload::with(['jabatan', 'departemen'])
            ->withCount('soals')
            ->latest()
            ->paginate(20);

        return view('hrd.soal.index', compact('uploads'));
    }

    public function create()
    {
        $jabatans = $this->queryJabatansBankSoal()
            ->orderBy('tipe_jabatan')
            ->orderByRaw('LOWER(TRIM(nama_jabatan))')
            ->orderBy('level_jabatan')
            ->get();

        return view('hrd.soal.create', compact('jabatans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jabatan_id' => ['required', 'exists:jabatans,id'],
            'pertanyaan' => ['required', 'string', 'min:5'],
            'tipe_soal' => ['required', Rule::in(['pilihan_ganda', 'multi_jawaban', 'benar_salah'])],
            'nama_prosedur' => ['nullable', 'string'],
            'nilai_point' => ['nullable', 'numeric', 'min:0'],
            'pilihan' => ['required', 'array', 'min:2'],
            'pilihan.*.teks_jawaban' => ['required', 'string'],
            'jawaban_benar' => ['required', 'array', 'min:1'],
        ]);

        $jabatan = Jabatan::findOrFail($validated['jabatan_id']);

        DB::transaction(function () use ($validated) {
            $soal = Soal::create([
                'jabatan_id' => $validated['jabatan_id'],
                'pertanyaan' => $validated['pertanyaan'],
                'tipe_prosedur' => Soal::tipeProsedurDariNama($validated['nama_prosedur'] ?? null),
                'nama_prosedur' => $validated['nama_prosedur'] ?? null,
                'nilai_point' => $validated['nilai_point'] ?? null,
                'tipe_soal' => $validated['tipe_soal'],
                'status' => true,
            ]);

            $kodes = ['A', 'B', 'C', 'D', 'E'];
            foreach ($validated['pilihan'] as $index => $pilihan) {
                if (empty(trim($pilihan['teks_jawaban']))) {
                    continue;
                }
                PilihanJawaban::create([
                    'soal_id' => $soal->id,
                    'kode' => $kodes[$index],
                    'teks_jawaban' => $pilihan['teks_jawaban'],
                    'is_benar' => in_array($index + 1, $validated['jawaban_benar']),
                ]);
            }
        });

        return redirect()->route('hrd.soal.index')
            ->with('success', 'Soal berhasil ditambahkan.');
    }

    /*
    |--------------------------------------------------------------------------
    | Upload Excel
    |--------------------------------------------------------------------------
    | Format kolom Excel:
    | nomor | prosedur pengoperasian | nilai point | pertanyaan | a | b | c | d | e (opsional) | jawaban benar
    |
    | prosedur pengoperasian : teks bebas
    | nilai point            : angka desimal
    | jawaban benar          : A atau A,B (multi_jawaban)
    | tipe_soal otomatis     : 1 jawaban = pilihan_ganda, >1 = multi_jawaban
    */
    public function uploadForm(Request $request)
    {
        $departemens = Departemen::orderBy('nama_departemen')->get();
        $departemen = $request->filled('departemen_id')
            ? Departemen::findOrFail($request->integer('departemen_id'))
            : null;
        $jabatans = $this->queryJabatansBankSoal()
            ->orderBy('tipe_jabatan')
            ->orderByRaw('LOWER(TRIM(nama_jabatan))')
            ->orderBy('level_jabatan')
            ->get();

        $bankJabatans = $jabatans->map(fn (Jabatan $jabatan): array => [
            'id' => (int) $jabatan->id,
            'nama' => $jabatan->nama_jabatan,
            'level' => $jabatan->tipe_jabatan === 'struktural' ? 1 : (int) $jabatan->level_jabatan,
            'nama_dasar' => $this->namaJabatanTanpaLevel($jabatan->nama_jabatan),
            'tipe' => $jabatan->tipe_jabatan,
        ])->values();

        return view('hrd.soal.upload', compact('departemens', 'departemen', 'jabatans', 'bankJabatans'));
    }

    public function uploadStore(Request $request)
    {
        $validated = $request->validate([
            'departemen_id' => ['required', 'exists:departemens,id'],
            'jabatan_id' => ['required', 'exists:jabatans,id'],
        ]);

        $jabatan = Jabatan::findOrFail($validated['jabatan_id']);
        $departemen = Departemen::findOrFail($validated['departemen_id']);

        $isJabatanStruktural = $jabatan->tipe_jabatan === 'struktural';

        if (! $isJabatanStruktural && ($jabatan->isCrew() || $jabatan->tipe_jabatan !== 'operasional' || (int) $jabatan->level_jabatan < 1 || (int) $jabatan->level_jabatan > 5)) {
            return back()->withInput()->withErrors([
                'jabatan_id' => 'Bank soal hanya dapat diupload untuk jabatan operasional level 1 sampai 5 atau jabatan struktural.',
            ]);
        }

        if ($isJabatanStruktural && ((int) $jabatan->urutan_struktural < 1 || (int) $jabatan->urutan_struktural > 6)) {
            return back()->withInput()->withErrors([
                'jabatan_id' => 'Jabatan struktural harus memiliki urutan jabatan dari 1 sampai 6.',
            ]);
        }

        if ($isJabatanStruktural) {
            $jabatanPerLevel = collect([1 => collect([$jabatan])]);
            $levelTujuan = 1;
        } else {
            $jabatanPerLevel = Jabatan::query()
                ->where('tipe_jabatan', 'operasional')
                ->whereBetween('level_jabatan', [1, (int) $jabatan->level_jabatan])
                ->get()
                ->filter(fn (Jabatan $kandidat): bool => $this->namaJabatanTanpaLevel($kandidat->nama_jabatan)
                    === $this->namaJabatanTanpaLevel($jabatan->nama_jabatan))
                ->groupBy('level_jabatan');

            $levelTidakValid = collect(range(1, (int) $jabatan->level_jabatan))
                ->filter(fn (int $level): bool => $jabatanPerLevel->get($level)?->count() !== 1)
                ->values();

            if ($levelTidakValid->isNotEmpty()) {
                return back()->withInput()->withErrors([
                    'jabatan_id' => 'Jabatan harus memiliki tepat satu data untuk setiap level 1 sampai '
                        .$jabatan->level_jabatan.'. Level bermasalah: '.$levelTidakValid->implode(', ').'.',
                ]);
            }

            $levelTujuan = (int) $jabatan->level_jabatan;
        }

        $levelKeys = array_map('strval', range(1, $levelTujuan));
        $pesanPilihFile = $isJabatanStruktural
            ? 'Pilih satu file Excel untuk jabatan struktural ini.'
            : 'Pilih satu file Excel untuk setiap level dari level 1 sampai level tujuan.';
        $pesanFileWajib = $isJabatanStruktural
            ? 'File Excel wajib dipilih untuk jabatan struktural ini.'
            : 'File Excel wajib dipilih untuk setiap level.';
        $request->validate([
            'file_excel' => ['required', 'array:'.implode(',', $levelKeys), 'size:'.$levelTujuan],
            'file_excel.*' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ], [
            'file_excel.required' => $pesanPilihFile,
            'file_excel.array' => $pesanPilihFile,
            'file_excel.size' => $pesanPilihFile,
            'file_excel.*.required' => $pesanFileWajib,
        ]);

        $files = $request->file('file_excel');
        $errors = [];
        $soalsPerLevel = [];

        foreach (range(1, $levelTujuan) as $level) {
            $jabatanLevel = $jabatanPerLevel->get($level)->first();
            $labelBankSoal = $isJabatanStruktural ? 'Jabatan '.$jabatan->nama_jabatan : 'Level '.$level;
            $rows = $this->bacaFile($files[$level]);

            if (empty($rows)) {
                $errors[] = "$labelBankSoal: File kosong atau format tidak dikenali.";

                continue;
            }

            $soalsBaru = [];

            foreach ($rows as $i => $row) {
                $no = $i + 1;
                $pertanyaan = trim($row['pertanyaan'] ?? '');
                $namaProsedur = trim($row['prosedur pengoperasian'] ?? '') ?: null;
                $nilaiPoint = is_numeric($row['nilai point'] ?? null) ? (float) $row['nilai point'] : null;

                if (empty($pertanyaan)) {
                    $errors[] = "$labelBankSoal, baris $no: Pertanyaan kosong.";

                    continue;
                }

                $rawJawaban = trim((string) ($row['jawaban benar'] ?? ''));

                if ($rawJawaban === '1' || $rawJawaban === '0') {
                    $tipeSoal = 'benar_salah';
                    $pilihan = ['A' => 'Benar', 'B' => 'Salah'];
                    $jawabanBenar = [$rawJawaban === '1' ? 'A' : 'B'];
                } else {
                    $jawabanBenar = array_values(array_filter(str_split(strtoupper($rawJawaban))));

                    $pilihan = [];
                    foreach (['A', 'B', 'C', 'D', 'E'] as $kode) {
                        $teks = trim($row[strtolower($kode)] ?? '');
                        if ($teks !== '') {
                            $pilihan[$kode] = $teks;
                        }
                    }

                    if (count($pilihan) < 2) {
                        $errors[] = "$labelBankSoal, baris $no: Minimal 2 pilihan jawaban.";

                        continue;
                    }

                    if (empty($jawabanBenar) || empty(array_intersect($jawabanBenar, array_keys($pilihan)))) {
                        $errors[] = "$labelBankSoal, baris $no: Jawaban benar tidak ditemukan di pilihan.";

                        continue;
                    }

                    $tipeSoal = count($jawabanBenar) > 1 ? 'multi_jawaban' : 'pilihan_ganda';
                }

                $soalsBaru[] = [
                    'jabatan_id' => $jabatanLevel->id,
                    'pertanyaan' => $pertanyaan,
                    'tipe_prosedur' => Soal::tipeProsedurDariNama($namaProsedur),
                    'nama_prosedur' => $namaProsedur,
                    'nilai_point' => $nilaiPoint,
                    'tipe_soal' => $tipeSoal,
                    'pilihan' => $pilihan,
                    'jawaban_benar' => $jawabanBenar,
                ];
            }

            $sopJumlah = self::SOP_JUMLAH[$level] ?? 50;
            if (! $isJabatanStruktural && count($soalsBaru) > $sopJumlah) {
                $errors[] = 'Level '.$level.': Jumlah soal ('.count($soalsBaru).") melebihi SOP ($sopJumlah soal).";
            }

            $soalsPerLevel[$level] = $soalsBaru;
        }

        if (! empty($errors)) {
            return back()->withErrors(['file_excel' => implode(' | ', array_slice($errors, 0, 10))])
                ->with('upload_errors', $errors);
        }

        DB::transaction(function () use ($soalsPerLevel, $jabatanPerLevel, $files, $levelTujuan, $departemen): void {
            foreach (range(1, $levelTujuan) as $level) {
                $jabatanLevel = $jabatanPerLevel->get($level)->first();
                $soalsBaru = $soalsPerLevel[$level];

                $upload = SoalUpload::create([
                    'jabatan_id' => $jabatanLevel->id,
                    'departemen_id' => $departemen->id,
                    'nama_file' => $files[$level]->getClientOriginalName(),
                    'jumlah_soal' => count($soalsBaru),
                ]);

                foreach ($soalsBaru as $data) {
                    $soal = Soal::create([
                        'jabatan_id' => $data['jabatan_id'],
                        'upload_id' => $upload->id,
                        'pertanyaan' => $data['pertanyaan'],
                        'tipe_prosedur' => $data['tipe_prosedur'],
                        'nama_prosedur' => $data['nama_prosedur'],
                        'nilai_point' => $data['nilai_point'],
                        'tipe_soal' => $data['tipe_soal'],
                        'status' => true,
                    ]);

                    foreach ($data['pilihan'] as $kode => $teks) {
                        PilihanJawaban::create([
                            'soal_id' => $soal->id,
                            'kode' => $kode,
                            'teks_jawaban' => $teks,
                            'is_benar' => in_array($kode, $data['jawaban_benar']),
                        ]);
                    }
                }
            }
        });

        $jumlahSoal = array_sum(array_map('count', $soalsPerLevel));

        return redirect()->route('hrd.soal.index')
            ->with('success', $jumlahSoal.' soal dari '.$levelTujuan.' file berhasil diupload untuk jabatan '.$jabatan->nama_jabatan.'.');
    }

    public function showUpload(SoalUpload $soalUpload)
    {
        $soalUpload->load(['jabatan', 'departemen', 'soals.pilihanJawabans']);

        $labelTipe = [
            'pilihan_ganda' => 'Pilihan Ganda',
            'multi_jawaban' => 'Multi Jawaban',
            'benar_salah' => 'Benar/Salah',
        ];

        return view('hrd.soal.show-upload', compact('soalUpload', 'labelTipe'));
    }

    public function destroyUpload(SoalUpload $soalUpload)
    {
        // Hapus semua soal + pilihan jawaban dalam batch ini
        DB::transaction(function () use ($soalUpload) {
            foreach ($soalUpload->soals as $soal) {
                $soal->pilihanJawabans()->delete();
                $soal->delete();
            }
            $soalUpload->delete();
        });

        return redirect()->route('hrd.soal.index')
            ->with('success', 'File "'.$soalUpload->nama_file_tampilan.'" dan semua soalnya berhasil dihapus.');
    }

    public function show(Soal $soal)
    {
        $soal->load(['jabatan', 'pilihanJawabans']);

        return view('hrd.soal.show', compact('soal'));
    }

    public function edit(Soal $soal)
    {
        $soal->load('pilihanJawabans');

        $jabatans = $this->queryJabatansBankSoal()
            ->orderBy('tipe_jabatan')
            ->orderByRaw('LOWER(TRIM(nama_jabatan))')
            ->orderBy('level_jabatan')
            ->get();

        return view('hrd.soal.edit', compact('soal', 'jabatans'));
    }

    public function update(Request $request, Soal $soal)
    {
        $validated = $request->validate([
            'jabatan_id' => ['required', 'exists:jabatans,id'],
            'pertanyaan' => ['required', 'string', 'min:5'],
            'tipe_soal' => ['required', Rule::in(['pilihan_ganda', 'multi_jawaban', 'benar_salah'])],
            'nama_prosedur' => ['nullable', 'string'],
            'nilai_point' => ['nullable', 'numeric', 'min:0'],
            'pilihan' => ['required', 'array', 'min:2'],
            'pilihan.*.teks_jawaban' => ['required', 'string'],
            'jawaban_benar' => ['required', 'array', 'min:1'],
        ]);

        DB::transaction(function () use ($validated, $soal) {
            $soal->update([
                'jabatan_id' => $validated['jabatan_id'],
                'pertanyaan' => $validated['pertanyaan'],
                'tipe_prosedur' => Soal::tipeProsedurDariNama($validated['nama_prosedur'] ?? null),
                'nama_prosedur' => $validated['nama_prosedur'] ?? null,
                'nilai_point' => $validated['nilai_point'] ?? null,
                'tipe_soal' => $validated['tipe_soal'],
            ]);

            $soal->pilihanJawabans()->delete();

            $kodes = ['A', 'B', 'C', 'D', 'E'];
            foreach ($validated['pilihan'] as $index => $pilihan) {
                if (empty(trim($pilihan['teks_jawaban']))) {
                    continue;
                }
                PilihanJawaban::create([
                    'soal_id' => $soal->id,
                    'kode' => $kodes[$index],
                    'teks_jawaban' => $pilihan['teks_jawaban'],
                    'is_benar' => in_array($index + 1, $validated['jawaban_benar']),
                ]);
            }
        });

        return redirect()->route('hrd.soal.index')
            ->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(Soal $soal)
    {
        $soal->delete();

        return redirect()->route('hrd.soal.index')
            ->with('success', 'Soal berhasil dihapus.');
    }

    // -------------------------------------------------------------------------

    private function validasiJawabanBenar(string $tipeSoal, int $jumlah): void
    {
        if ($tipeSoal === 'pilihan_ganda' && $jumlah !== 1) {
            abort(422, 'Pilihan ganda hanya boleh 1 jawaban benar.');
        }
        if ($tipeSoal === 'benar_salah' && $jumlah !== 1) {
            abort(422, 'Benar/salah hanya boleh 1 jawaban benar.');
        }
        if ($tipeSoal === 'multi_jawaban' && $jumlah < 2) {
            abort(422, 'Multi jawaban minimal 2 jawaban benar.');
        }
    }

    private function namaJabatanTanpaLevel(string $namaJabatan): string
    {
        $namaDasar = preg_replace('/\s+(?:level|lev|lv)\s*\.?\s*\d+\s*$/iu', '', trim($namaJabatan));

        return mb_strtolower(trim($namaDasar ?? $namaJabatan));
    }

    private function queryJabatansBankSoal(): Builder
    {
        return Jabatan::query()
            ->where(function (Builder $query): void {
                $query->where(function (Builder $query): void {
                    $query->where('tipe_jabatan', 'operasional')
                        ->whereRaw('LOWER(TRIM(nama_jabatan)) != ?', ['crew'])
                        ->whereBetween('level_jabatan', [1, 5]);
                })->orWhere(function (Builder $query): void {
                    $query->where('tipe_jabatan', 'struktural')
                        ->whereBetween('urutan_struktural', [1, 6]);
                });
            });
    }

    private function bacaFile(UploadedFile $file): array
    {
        $path = $file->getRealPath();
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

        if (empty($sheet) || empty($sheet[0])) {
            return [];
        }

        $header = array_map('strtolower', array_map('trim', $sheet[0]));
        $rows = [];

        foreach (array_slice($sheet, 1) as $line) {
            if (empty(array_filter($line, fn ($v) => $v !== null && $v !== ''))) {
                continue;
            }
            $line = array_pad(array_values($line), count($header), '');
            $rows[] = array_combine($header, array_map(fn ($v) => $v ?? '', $line));
        }

        return $rows;
    }
}
