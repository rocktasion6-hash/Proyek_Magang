<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Models\PilihanJawaban;
use App\Models\Soal;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SoalController extends Controller
{
    /**
     * Menampilkan daftar bank soal.
     */
    public function index(Request $request)
    {
        $query = Soal::with([
            'jabatan',
            'skill',
            'pilihanJawabans',
        ]);

        if ($request->filled('jabatan_id')) {
            $query->where('jabatan_id', $request->jabatan_id);
        }

        if ($request->filled('skill_id')) {
            $query->where('skill_id', $request->skill_id);
        }

        if ($request->filled('tipe_soal')) {
            $query->where('tipe_soal', $request->tipe_soal);
        }

        if ($request->filled('tingkat_kesulitan')) {
            $query->where(
                'tingkat_kesulitan',
                $request->tingkat_kesulitan
            );
        }

        $soals = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $jabatans = Jabatan::orderBy('nama_jabatan')->get();

        $skills = Skill::orderBy('nama_skill')->get();

        return view('hrd.soal.index', compact(
            'soals',
            'jabatans',
            'skills'
        ));
    }

    /**
     * Menampilkan form tambah soal.
     */
    public function create()
    {
        $jabatans = Jabatan::orderBy('nama_jabatan')->get();

        $skills = Skill::orderBy('nama_skill')->get();

        return view('hrd.soal.create', compact(
            'jabatans',
            'skills'
        ));
    }

    /**
     * Menyimpan soal baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jabatan_id' => [
                'required',
                'exists:jabatans,id',
            ],

            'skill_id' => [
                'required',
                'exists:skills,id',
            ],

            'pertanyaan' => [
                'required',
                'string',
                'min:5',
            ],

            'tipe_soal' => [
                'required',
                Rule::in([
                    'pilihan_tunggal',
                    'multi_jawaban',
                    'benar_salah',
                ]),
            ],

            'tingkat_kesulitan' => [
                'required',
                Rule::in([
                    'mudah',
                    'sedang',
                    'sulit',
                ]),
            ],

            'bobot' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'pilihan' => [
                'required',
                'array',
                'min:2',
            ],

            'pilihan.*.kode' => [
                'required',
                'string',
                'size:1',
            ],

            'pilihan.*.teks_jawaban' => [
                'required',
                'string',
            ],

            'jawaban_benar' => [
                'required',
                'array',
                'min:1',
            ],

            'jawaban_benar.*' => [
                'integer',
            ],
        ], [
            'jabatan_id.required' => 'Jabatan wajib dipilih.',
            'skill_id.required' => 'Skill wajib dipilih.',
            'pertanyaan.required' => 'Pertanyaan wajib diisi.',
            'tipe_soal.required' => 'Tipe soal wajib dipilih.',
            'tingkat_kesulitan.required' => 'Tingkat kesulitan wajib dipilih.',
            'bobot.required' => 'Bobot wajib diisi.',
            'pilihan.required' => 'Pilihan jawaban wajib diisi.',
            'pilihan.min' => 'Minimal harus ada 2 pilihan jawaban.',
            'jawaban_benar.required' => 'Jawaban benar wajib dipilih.',
            'jawaban_benar.min' => 'Minimal satu jawaban benar harus dipilih.',
        ]);

        /*
         * Validasi jumlah jawaban benar
         * berdasarkan tipe soal.
         */
        $jumlahBenar = count($validated['jawaban_benar']);

        if (
            $validated['tipe_soal'] === 'pilihan_tunggal'
            && $jumlahBenar !== 1
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'jawaban_benar' =>
                        'Pilihan tunggal hanya boleh memiliki satu jawaban benar.',
                ]);
        }

        if (
            $validated['tipe_soal'] === 'benar_salah'
            && $jumlahBenar !== 1
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'jawaban_benar' =>
                        'Soal benar/salah hanya boleh memiliki satu jawaban benar.',
                ]);
        }

        if (
            $validated['tipe_soal'] === 'multi_jawaban'
            && $jumlahBenar < 2
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'jawaban_benar' =>
                        'Soal multi jawaban harus memiliki minimal dua jawaban benar.',
                ]);
        }

        DB::transaction(function () use ($validated) {

            $soal = Soal::create([
                'jabatan_id' => $validated['jabatan_id'],
                'skill_id' => $validated['skill_id'],
                'pertanyaan' => $validated['pertanyaan'],
                'tipe_soal' => $validated['tipe_soal'],
                'tingkat_kesulitan' => $validated['tingkat_kesulitan'],
                'bobot' => $validated['bobot'],
                'status' => true,
            ]);

            foreach ($validated['pilihan'] as $index => $pilihan) {

                $pilihanId = $index + 1;

                PilihanJawaban::create([
                    'soal_id' => $soal->id,
                    'kode' => strtoupper($pilihan['kode']),
                    'teks_jawaban' => $pilihan['teks_jawaban'],
                    'is_benar' => in_array(
                        $pilihanId,
                        $validated['jawaban_benar']
                    ),
                ]);
            }
        });

        return redirect()
            ->route('hrd.soal.index')
            ->with('success', 'Soal berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail soal.
     */
    public function show(Soal $soal)
    {
        $soal->load([
            'jabatan',
            'skill',
            'pilihanJawabans',
        ]);

        return view('hrd.soal.show', compact('soal'));
    }

    /**
    * Menampilkan form edit soal.
    */
    public function edit(Soal $soal)
    {
        $soal->load('pilihanJawabans');

        $jabatans = Jabatan::orderBy('nama_jabatan')->get();

        $skills = Skill::orderBy('nama_skill')->get();

        return view('hrd.soal.edit', compact(
            'soal',
            'jabatans',
            'skills'
        ));
    }

    /**
    * Memperbarui soal.
    */
    public function update(Request $request, Soal $soal)
    {
        $validated = $request->validate([
            'jabatan_id' => [
                'required',
                'exists:jabatans,id',
            ],

            'skill_id' => [
                'required',
                'exists:skills,id',
            ],

            'pertanyaan' => [
                'required',
                'string',
                'min:5',
            ],

            'tipe_soal' => [
                'required',
                Rule::in([
                    'pilihan_tunggal',
                    'multi_jawaban',
                    'benar_salah',
                ]),
            ],

            'tingkat_kesulitan' => [
                'required',
                Rule::in([
                    'mudah',
                    'sedang',
                    'sulit',
                ]),
            ],

            'bobot' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'pilihan' => [
                'required',
                'array',
                'min:2',
            ],

            'pilihan.*.kode' => [
                'required',
                'string',
                'size:1',
            ],

            'pilihan.*.teks_jawaban' => [
                'required',
                'string',
            ],

            'jawaban_benar' => [
                'required',
                'array',
                'min:1',
            ],

            'jawaban_benar.*' => [
                'integer',
            ],
        ], [
            'jabatan_id.required' => 'Jabatan wajib dipilih.',
            'skill_id.required' => 'Skill wajib dipilih.',
            'pertanyaan.required' => 'Pertanyaan wajib diisi.',
            'tipe_soal.required' => 'Tipe soal wajib dipilih.',
            'tingkat_kesulitan.required' => 'Tingkat kesulitan wajib dipilih.',
            'bobot.required' => 'Bobot wajib diisi.',
            'pilihan.required' => 'Pilihan jawaban wajib diisi.',
            'pilihan.min' => 'Minimal harus ada 2 pilihan jawaban.',
            'jawaban_benar.required' => 'Jawaban benar wajib dipilih.',
            'jawaban_benar.min' => 'Minimal satu jawaban benar harus dipilih.',
        ]);

        $jumlahBenar = count($validated['jawaban_benar']);

        // Pilihan tunggal harus tepat 1 jawaban benar
        if (
            $validated['tipe_soal'] === 'pilihan_tunggal'
            && $jumlahBenar !== 1
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'jawaban_benar' =>
                        'Pilihan tunggal hanya boleh memiliki satu jawaban benar.',
                ]);
        }

        // Benar / salah harus tepat 1 jawaban benar
        if (
            $validated['tipe_soal'] === 'benar_salah'
            && $jumlahBenar !== 1
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'jawaban_benar' =>
                        'Soal benar/salah hanya boleh memiliki satu jawaban benar.',
                ]);
        }

        // Multi jawaban minimal 2 jawaban benar
        if (
            $validated['tipe_soal'] === 'multi_jawaban'
            && $jumlahBenar < 2
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'jawaban_benar' =>
                        'Soal multi jawaban harus memiliki minimal dua jawaban benar.',
                ]);
        }

        DB::transaction(function () use ($validated, $soal) {

        // Update data soal
        $soal->update([
            'jabatan_id' => $validated['jabatan_id'],
            'skill_id' => $validated['skill_id'],
            'pertanyaan' => $validated['pertanyaan'],
            'tipe_soal' => $validated['tipe_soal'],
            'tingkat_kesulitan' => $validated['tingkat_kesulitan'],
            'bobot' => $validated['bobot'],
        ]);

        // Hapus pilihan jawaban lama
        $soal->pilihanJawabans()->delete();

        // Masukkan pilihan jawaban baru
        foreach ($validated['pilihan'] as $index => $pilihan) {

            $pilihanId = $index + 1;

            PilihanJawaban::create([
                'soal_id' => $soal->id,
                'kode' => strtoupper($pilihan['kode']),
                'teks_jawaban' => $pilihan['teks_jawaban'],
                'is_benar' => in_array(
                    $pilihanId,
                    $validated['jawaban_benar']
                ),
            ]);
        }
    });

    return redirect()
        ->route('hrd.soal.index')
        ->with('success', 'Soal berhasil diperbarui.');
    }

    /**
    * Menghapus soal.
    */
    public function destroy(Soal $soal)
    {
        DB::transaction(function () use ($soal) {

            // Pilihan jawaban akan ikut terhapus
            // karena FK pilihan_jawabans menggunakan cascadeOnDelete().
            $soal->delete();
        });

        return redirect()
            ->route('hrd.soal.index')
            ->with('success', 'Soal berhasil dihapus.');
    }
}