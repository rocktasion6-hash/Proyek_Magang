
@extends('layouts.hrd')

@section('title', 'Detail Hasil Penilaian')
@section('page_title', 'Detail Hasil Penilaian')

@section('content')

@php
    $peserta = $hasilAssessment->assessmentPeserta;
    $karyawan = $peserta->karyawan;
    $assessment = $peserta->assessment;
    $jawabans = $peserta->jawabans;
    $soals = $assessment->soals;

    $isLulus = $hasilAssessment->status === 'lulus';

    $jabatanAwal = $karyawan->jabatan?->nama_jabatan ?? '-';
    $jabatanTujuan = $assessment->jabatan?->nama_jabatan ?? '-';

    $kategoriLabels = [
        'peraturan_perusahaan' => 'PP',
        'pengoperasian_jabatan' => 'SOP',
        'keselamatan_kerja' => 'K3',
    ];

    $kategoriNama = [
        'peraturan_perusahaan' => 'PP - Peraturan Perusahaan',
        'pengoperasian_jabatan' => 'SOP - Prosedur Pengoperasian Jabatan',
        'keselamatan_kerja' => 'K3 - Keselamatan dan Kesehatan Kerja',
    ];

    $tipeLabels = [
        'pilihan_ganda' => 'Pilihan Ganda',
        'multi_jawaban' => 'Multi Jawaban',
        'benar_salah' => 'Benar/Salah',
    ];

    $normalisasiTipe = function ($tipe) {
        return match ($tipe) {
            'pilihan_tunggal', 'pg' => 'pilihan_ganda',
            'mj' => 'multi_jawaban',
            'bs', 'true_false' => 'benar_salah',
            default => $tipe,
        };
    };

    $urutanTipe = [
        'pilihan_ganda' => 1,
        'multi_jawaban' => 2,
        'benar_salah' => 3,
    ];

    $urutanKategori = [
        'peraturan_perusahaan' => 1,
        'pengoperasian_jabatan' => 2,
        'keselamatan_kerja' => 3,
    ];

    // Urutkan soal berdasarkan tipe dan nomor.
    $soalTerurut = $soals
        ->sortBy(function ($soal) use ($normalisasiTipe, $urutanTipe) {
            $tipe = $normalisasiTipe($soal->tipe_soal);

            return sprintf(
                '%02d-%08d',
                $urutanTipe[$tipe] ?? 99,
                $soal->pivot->nomor_soal ?? 9999
            );
        })
        ->values();

    // Rekap kesalahan berdasarkan kategori dan tipe.
    $rekapKesalahan = [];

    foreach ($kategoriLabels as $kategori => $label) {
        foreach ($tipeLabels as $tipe => $namaTipe) {
            $rekapKesalahan[$kategori][$tipe] = [
                'benar' => 0,
                'salah' => 0,
            ];
        }
    }

    // Analisis poin pengetahuan.
    $analisisPengetahuan = [];

    foreach ($kategoriNama as $kategori => $nama) {
        $soalKategori = $soals->where('tipe_prosedur', $kategori);

        $poinMaksimal = 0;
        $poinPeserta = 0;

        foreach ($soalKategori as $soal) {
            $bobot = $assessment->nilaiPointUntukSoal($soal);

            $poinMaksimal += $bobot;

            $dipilih = $jawabans
                ->where('soal_id', $soal->id)
                ->pluck('pilihan_jawaban_id')
                ->filter(fn ($id) => $id !== null)
                ->map(fn ($id) => (string) $id)
                ->unique()
                ->sort()
                ->values()
                ->all();

            $kunci = $soal->pilihanJawabans
                ->where('is_benar', true)
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->unique()
                ->sort()
                ->values()
                ->all();

            $benar = count($dipilih) > 0 && $dipilih === $kunci;

            if ($benar) {
                $poinPeserta += $bobot;
            }

            $tipe = $normalisasiTipe($soal->tipe_soal);

            if (isset($rekapKesalahan[$kategori][$tipe])) {
                $status = $benar ? 'benar' : 'salah';
                $rekapKesalahan[$kategori][$tipe][$status]++;
            }
        }

        $analisisPengetahuan[] = [
            'nama' => $nama,
            'maksimal' => $poinMaksimal,
            'peserta' => $poinPeserta,
            'persentase' => $poinMaksimal > 0
                ? ($poinPeserta / $poinMaksimal) * 100
                : 0,
        ];
    }

    // Detail kesalahan jawaban.
    $detailKesalahan = [];

    foreach ($soalTerurut as $soal) {
        $kategori = $soal->tipe_prosedur;
        $tipe = $normalisasiTipe($soal->tipe_soal);

        if (
            !isset($kategoriLabels[$kategori]) ||
            !isset($tipeLabels[$tipe])
        ) {
            continue;
        }

        $dipilih = $jawabans
            ->where('soal_id', $soal->id)
            ->pluck('pilihan_jawaban_id')
            ->filter(fn ($id) => $id !== null)
            ->map(fn ($id) => (string) $id)
            ->unique()
            ->sort()
            ->values()
            ->all();

        $kunci = $soal->pilihanJawabans
            ->where('is_benar', true)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->unique()
            ->sort()
            ->values()
            ->all();

        if (count($dipilih) > 0 && $dipilih === $kunci) {
            continue;
        }

        $jawabanPeserta = $soal->pilihanJawabans
            ->filter(fn ($pilihan) =>
                in_array((string) $pilihan->id, $dipilih, true)
            )
            ->map(fn ($pilihan) =>
                $pilihan->kode . '. ' . $pilihan->teks_jawaban
            )
            ->values()
            ->all();

        $jawabanBenar = $soal->pilihanJawabans
            ->where('is_benar', true)
            ->map(fn ($pilihan) =>
                $pilihan->kode . '. ' . $pilihan->teks_jawaban
            )
            ->values()
            ->all();

        $grup = $tipe . '_' . $kategori;

        if (!isset($detailKesalahan[$grup])) {
            $detailKesalahan[$grup] = [
                'tipe' => $tipe,
                'kategori' => $kategori,
                'nama' => $tipeLabels[$tipe]
                    . ' - ' . $kategoriLabels[$kategori],
                'soal' => [],
            ];
        }

        $detailKesalahan[$grup]['soal'][] = [
            'nomor' => $soal->pivot->nomor_soal ?? '-',
            'pertanyaan' => $soal->pertanyaan,
            'dipilih' => $jawabanPeserta,
            'kunci' => $jawabanBenar,
        ];
    }

    $detailKesalahan = collect($detailKesalahan)
        ->sortBy(function ($item) use ($urutanTipe, $urutanKategori) {
            return sprintf(
                '%02d-%02d',
                $urutanTipe[$item['tipe']] ?? 99,
                $urutanKategori[$item['kategori']] ?? 99
            );
        })
        ->values();
@endphp

{{-- CSS hanya untuk pengaturan printer --}}
<style>
    @page {
        size: A4 portrait;
        margin: 12mm;
    }

    @media print {
        #hrd-sidebar,
        .topbar,
        .alert,
        .hasil-no-print {
            display: none !important;
        }

        body,
        #hrd-app,
        #hrd-app .main,
        #hrd-app .content,
        .hasil-print-area {
            background: white !important;
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        #hrd-app .main {
            margin-left: 0 !important;
        }

        .hasil-print-area {
            border: none !important;
            box-shadow: none !important;
        }

        .hasil-print-area .grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }

        .hasil-print-area .grid > div {
            break-inside: avoid;
        }

        * {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<div class="w-full min-w-0 space-y-4">

    {{-- ======================================== --}}
    {{-- TOMBOL DOWNLOAD DAN PRINT --}}
    {{-- ======================================== --}}

    <div class="hasil-no-print flex flex-wrap justify-end gap-2">

        <a
            href="{{ route('hrd.hasil-assessment.pdf', $hasilAssessment) }}"
            class="inline-flex h-10 items-center justify-center
                   gap-2 rounded-lg bg-[#4c7b58]
                   px-4 text-sm font-semibold text-white
                   transition-colors hover:bg-[#3e6749]"
        >
            <svg class="h-4 w-4"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 viewBox="0 0 24 24">
                <path d="M12 3v12m-5-5 5 5 5-5"/>
                <path d="M5 17v4h14v-4"/>
            </svg>

            Download PDF
        </a>

        <button
            type="button"
            onclick="window.print()"
            class="inline-flex h-10 items-center justify-center
                   gap-2 rounded-lg border border-[#dfe5df]
                   bg-white px-4 text-sm font-semibold
                   text-[#304c3f] transition-colors
                   hover:bg-[#f4f6f3]"
        >
            <svg class="h-4 w-4"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 viewBox="0 0 24 24">
                <path d="M6 9V3h12v6M6 18H4V9h16v9h-2M6 14h12v7H6z"/>
            </svg>

            Print Laporan
        </button>

    </div>


    {{-- ======================================== --}}
    {{-- LAPORAN UTAMA --}}
    {{-- HANYA BAGIAN INI YANG DICETAK --}}
    {{-- ======================================== --}}

    <div class="hasil-print-area rounded-xl border
                border-[#dfe5df] bg-white p-5 sm:p-6">

        {{-- HEADER --}}
        <div class="flex flex-col justify-between gap-4
                    border-b border-[#e8ece8] pb-5
                    md:flex-row md:items-center">

            <div>

                <p class="mb-1 text-xs font-semibold
                          uppercase tracking-wide
                          text-[#4c7b58]">
                    Laporan Hasil Assessment
                </p>

                <h1 class="text-2xl font-bold text-[#304c3f]">
                    {{ $assessment->nama_assessment }}
                </h1>

                <p class="mt-2 text-sm text-gray-500">
                    Tanggal Ujian:
                    <span class="font-semibold text-gray-800">
                        {{ $hasilAssessment->tanggal_ujian?->format('d M Y H:i') ?? '-' }}
                    </span>
                </p>

            </div>

            {{-- NILAI TES --}}
            <div class="rounded-lg border px-5 py-3
                {{ $isLulus
                    ? 'border-[#d3e3d6] bg-[#f0f6f1]'
                    : 'border-red-200 bg-red-50' }}">

                <p class="text-xs font-bold uppercase
                          tracking-wide text-gray-500">
                    Nilai Tes
                </p>

                <p class="mt-1 text-3xl font-bold
                    {{ $isLulus
                        ? 'text-[#4c7b58]'
                        : 'text-red-700' }}">
                    {{ number_format($hasilAssessment->nilai_akhir, 2) }}
                </p>

                <p class="mt-1 text-xs text-gray-500">
                    Standar:
                    {{ number_format($hasilAssessment->standar_nilai, 2) }}
                </p>

            </div>

        </div>


        {{-- INFORMASI KARYAWAN DAN JABATAN --}}
        <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">

            {{-- INFORMASI KARYAWAN --}}
            <section class="min-w-0 rounded-xl border
                            border-[#dfe5df] bg-white p-4">

                {{-- JUDUL SEPERTI REFERENSI PROFIL & AKUN --}}
                <h2 class="mb-4 rounded-l-md rounded-r-lg
                           border-l-[5px] border-[#4c7b58]
                           bg-[#f4f6f3] px-5 py-3
                           text-base font-bold text-[#304c3f]">
                    Informasi Karyawan
                </h2>

                <div class="divide-y divide-[#e8ece8]">

                    <div class="grid grid-cols-5 gap-3 py-3">

                        <span class="col-span-2 text-sm
                                     font-semibold text-gray-600">
                            Nama Karyawan
                        </span>

                        <span class="col-span-3 break-words
                                     text-sm font-semibold
                                     text-gray-900">
                            {{ $karyawan->nama }}
                        </span>

                    </div>

                    <div class="grid grid-cols-5 gap-3 py-3">

                        <span class="col-span-2 text-sm
                                     font-semibold text-gray-600">
                            NIK
                        </span>

                        <span class="col-span-3 break-words
                                     text-sm font-semibold
                                     text-gray-900">
                            {{ $karyawan->nik }}
                        </span>

                    </div>

                </div>

            </section>


            {{-- INFORMASI JABATAN --}}
            <section class="min-w-0 rounded-xl border
                            border-[#dfe5df] bg-white p-4">

                <h2 class="mb-4 rounded-l-md rounded-r-lg
                           border-l-[5px] border-[#4c7b58]
                           bg-[#f4f6f3] px-5 py-3
                           text-base font-bold text-[#304c3f]">
                    Informasi Jabatan
                </h2>

                <div class="divide-y divide-[#e8ece8]">

                    <div class="grid grid-cols-5 gap-3 py-3">

                        <span class="col-span-2 text-sm
                                     font-semibold text-gray-600">
                            Jabatan Awal
                        </span>

                        <span class="col-span-3 break-words
                                     text-sm font-semibold
                                     text-gray-900">
                            {{ $jabatanAwal }}
                        </span>

                    </div>

                    <div class="grid grid-cols-5 gap-3 py-3">

                        <span class="col-span-2 text-sm
                                     font-semibold text-gray-600">
                            Jabatan Tujuan
                        </span>

                        <span class="col-span-3 break-words
                                     text-sm font-semibold
                                     text-gray-900">
                            {{ $jabatanTujuan }}
                        </span>

                    </div>

                </div>

            </section>

        </div>

    </div>


    {{-- ======================================== --}}
    {{-- ANALISIS KESALAHAN --}}
    {{-- TIDAK IKUT PRINT --}}
    {{-- ======================================== --}}

    <section class="hasil-no-print rounded-xl border
                    border-[#dfe5df] bg-white p-5">

        <h2 class="rounded-l-md rounded-r-lg
                   border-l-[5px] border-[#4c7b58]
                   bg-[#f4f6f3] px-5 py-3
                   text-base font-bold text-[#304c3f]">
            Analisis Kesalahan per Tipe Jawaban
        </h2>

        <p class="mb-4 mt-3 text-sm text-gray-500">
            Jumlah jawaban benar dan salah berdasarkan kategori PP, SOP, dan K3.
        </p>

        <div class="w-full overflow-x-auto rounded-lg
                    border border-[#e0e5df]">

            <table class="w-full min-w-[650px]
                          border-collapse text-sm">

                <thead class="bg-[#f4f6f3] text-[#304c3f]">

                    <tr>

                        <th class="px-4 py-3 text-left font-bold">
                            Kategori
                        </th>

                        @foreach ($tipeLabels as $tipe => $label)
                            <th class="px-4 py-3 text-center font-bold">
                                {{ $label }}
                            </th>
                        @endforeach

                    </tr>

                </thead>

                <tbody class="divide-y divide-[#e8ece8]">

                    @foreach ($kategoriLabels as $kategori => $nama)

                        <tr class="hover:bg-[#fafbf9]">

                            <td class="px-4 py-4
                                       font-bold text-[#304c3f]">
                                {{ $nama }}
                            </td>

                            @foreach ($tipeLabels as $tipe => $label)

                                @php
                                    $rekap = $rekapKesalahan[$kategori][$tipe];
                                @endphp

                                <td class="px-4 py-4 text-center">

                                    <div class="flex flex-col items-center gap-1">

                                        {{-- BENAR --}}
                                        <span class="text-sm font-medium
                                                     text-[#4c7b58]">
                                            {{ $rekap['benar'] }} Benar
                                        </span>

                                        {{-- SALAH TANPA BACKGROUND --}}
                                        <span class="text-sm font-medium
                                            {{ $rekap['salah'] > 0
                                                ? 'text-red-600'
                                                : 'text-gray-500' }}">
                                            {{ $rekap['salah'] }} Salah
                                        </span>

                                    </div>

                                </td>

                            @endforeach

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </section>


    {{-- ======================================== --}}
    {{-- ANALISIS POIN PENGETAHUAN --}}
    {{-- ======================================== --}}

    <section class="hasil-no-print rounded-xl border
                    border-[#dfe5df] bg-white p-5">

        <h2 class="rounded-l-md rounded-r-lg
                   border-l-[5px] border-[#4c7b58]
                   bg-[#f4f6f3] px-5 py-3
                   text-base font-bold text-[#304c3f]">
            Analisis Poin Pengetahuan
        </h2>

        <p class="mb-4 mt-3 text-sm text-gray-500">
            Rincian pencapaian poin berdasarkan kategori pengetahuan.
        </p>

        <div class="w-full overflow-x-auto rounded-lg
                    border border-[#e0e5df]">

            <table class="w-full min-w-[650px]
                          border-collapse text-sm">

                <thead class="bg-[#f4f6f3] text-[#304c3f]">

                    <tr>
                        <th class="px-4 py-3 text-left font-bold">
                            Kategori Pengetahuan
                        </th>

                        <th class="px-4 py-3 text-center font-bold">
                            Poin Maksimal
                        </th>

                        <th class="px-4 py-3 text-center font-bold">
                            Poin Peserta
                        </th>

                        <th class="px-4 py-3 text-center font-bold">
                            Persentase
                        </th>
                    </tr>

                </thead>

                <tbody class="divide-y divide-[#e8ece8]">

                    @foreach ($analisisPengetahuan as $item)

                        <tr class="hover:bg-[#fafbf9]">

                            <td class="px-4 py-4 font-medium text-gray-800">
                                {{ $item['nama'] }}
                            </td>

                            <td class="px-4 py-4 text-center text-gray-700">
                                {{ number_format($item['maksimal'], 2) }}
                            </td>

                            <td class="px-4 py-4 text-center
                                       font-bold text-[#304c3f]">
                                {{ number_format($item['peserta'], 2) }}
                            </td>

                            <td class="px-4 py-4 text-center
                                       font-semibold text-gray-700">
                                {{ number_format($item['persentase'], 2) }}%
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </section>


    {{-- ======================================== --}}
    {{-- DETAIL KESALAHAN JAWABAN --}}
    {{-- ======================================== --}}

    <section class="hasil-no-print rounded-xl border
                    border-[#dfe5df] bg-white p-5">

        <h2 class="rounded-l-md rounded-r-lg
                   border-l-[5px] border-[#4c7b58]
                   bg-[#f4f6f3] px-5 py-3
                   text-base font-bold text-[#304c3f]">
            Detail Kesalahan Jawaban
        </h2>

        <p class="mb-4 mt-3 text-sm text-gray-500">
            Buka kategori untuk melihat soal yang dijawab salah.
        </p>

        <div class="space-y-3">

            @forelse ($detailKesalahan as $grup)

                <details class="group overflow-hidden rounded-lg
                                border border-[#e0e5df]">

                    <summary class="flex cursor-pointer
                                    items-center justify-between gap-3
                                    bg-[#fafbf9] px-4 py-3
                                    hover:bg-[#f4f6f3]">

                        <div class="flex flex-wrap items-center gap-2">

                            <span class="text-sm font-semibold
                                         text-[#304c3f]">
                                {{ $grup['nama'] }}
                            </span>

                            <span class="text-xs font-medium text-red-600">
                                {{ count($grup['soal']) }} salah
                            </span>

                        </div>

                        <svg class="h-4 w-4 shrink-0 text-gray-500
                                    transition-transform
                                    group-open:rotate-180"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2"
                             viewBox="0 0 24 24">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>

                    </summary>

                    <div class="space-y-3 border-t
                                border-[#e0e5df] p-4">

                        @foreach ($grup['soal'] as $item)

                            <div class="rounded-lg border
                                        border-[#e0e5df] p-4">

                                <p class="text-sm font-bold text-[#304c3f]">
                                    Soal Nomor {{ $item['nomor'] }}
                                </p>

                                <p class="mt-2 text-sm leading-6 text-gray-700">
                                    {{ $item['pertanyaan'] }}
                                </p>

                                <div class="mt-4 grid grid-cols-1
                                            gap-3 md:grid-cols-2">

                                    {{-- JAWABAN PESERTA --}}
                                    <div class="rounded-lg border
                                                border-red-200
                                                bg-red-50 p-3">

                                        <p class="mb-2 text-xs
                                                  font-bold text-red-700">
                                            Jawaban Peserta
                                        </p>

                                        @forelse ($item['dipilih'] as $jawaban)

                                            <p class="mb-1 text-sm text-red-800">
                                                {{ $jawaban }}
                                            </p>

                                        @empty

                                            <p class="text-sm text-red-700">
                                                Tidak dijawab
                                            </p>

                                        @endforelse

                                    </div>


                                    {{-- JAWABAN BENAR --}}
                                    <div class="rounded-lg border
                                                border-[#d7e6d9]
                                                bg-[#f0f6f1] p-3">

                                        <p class="mb-2 text-xs
                                                  font-bold text-[#304c3f]">
                                            Jawaban yang Benar
                                        </p>

                                        @forelse ($item['kunci'] as $jawaban)

                                            <p class="mb-1 text-sm text-[#304c3f]">
                                                {{ $jawaban }}
                                            </p>

                                        @empty

                                            <p class="text-sm text-gray-500">
                                                Kunci jawaban tidak tersedia
                                            </p>

                                        @endforelse

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                </details>

            @empty

                <div class="rounded-lg border border-[#d7e6d9]
                            bg-[#f0f6f1] p-4">

                    <p class="text-sm text-[#304c3f]">
                        Tidak ditemukan jawaban salah pada soal
                        yang dapat dianalisis.
                    </p>

                </div>

            @endforelse

        </div>

    </section>


    {{-- ======================================== --}}
    {{-- DETAIL SELURUH JAWABAN --}}
    {{-- ======================================== --}}

    <section class="hasil-no-print rounded-xl border
                    border-[#dfe5df] bg-white p-5">

        <h2 class="rounded-l-md rounded-r-lg
                   border-l-[5px] border-[#4c7b58]
                   bg-[#f4f6f3] px-5 py-3
                   text-base font-bold text-[#304c3f]">
            Detail Jawaban
        </h2>

        <p class="mb-5 mt-3 text-sm text-gray-500">
            Seluruh soal dan jawaban peserta berdasarkan tipe soal.
        </p>

        @foreach ($tipeLabels as $kodeTipe => $namaTipe)

            @php
                $soalTipe = $soalTerurut->filter(
                    fn ($soal) =>
                        $normalisasiTipe($soal->tipe_soal) === $kodeTipe
                );
            @endphp

            @if ($soalTipe->isNotEmpty())

                <div class="mb-6 last:mb-0">

                    {{-- SUBJUDUL TIPE SOAL --}}
                    <div class="mb-4 border-b
                                border-[#e5e9e5] pb-3">

                        <h3 class="text-sm font-bold text-[#304c3f]">
                            {{ $namaTipe }}
                        </h3>

                        <p class="mt-1 text-xs text-gray-500">
                            {{ $soalTipe->count() }} soal
                        </p>

                    </div>

                    <div class="space-y-3">

                        @foreach ($soalTipe as $soal)

                            @php
                                $pilihanDipilih = $jawabans
                                    ->where('soal_id', $soal->id)
                                    ->pluck('pilihan_jawaban_id')
                                    ->filter(fn ($id) => $id !== null)
                                    ->map(fn ($id) => (string) $id)
                                    ->unique()
                                    ->sort()
                                    ->values()
                                    ->all();

                                $pilihanBenar = $soal->pilihanJawabans
                                    ->where('is_benar', true)
                                    ->pluck('id')
                                    ->map(fn ($id) => (string) $id)
                                    ->unique()
                                    ->sort()
                                    ->values()
                                    ->all();

                                $jawabanBenar = count($pilihanDipilih) > 0
                                    && $pilihanDipilih === $pilihanBenar;
                            @endphp

                            <div class="rounded-lg border
                                        border-[#e0e5df] p-4">

                                {{-- HEADER SOAL --}}
                                <div class="mb-4 flex flex-col
                                            justify-between gap-3
                                            sm:flex-row sm:items-start">

                                    <div class="flex items-start gap-3">

                                        <span class="flex h-8 w-8
                                                     shrink-0 items-center
                                                     justify-center rounded-lg
                                                     bg-[#e8eee9]
                                                     text-sm font-bold
                                                     text-[#304c3f]">
                                            {{ $soal->pivot->nomor_soal ?? '-' }}
                                        </span>

                                        <div>

                                            <p class="mb-1 text-xs
                                                      font-semibold
                                                      text-[#4c7b58]">
                                                {{ $kategoriLabels[$soal->tipe_prosedur]
                                                    ?? $soal->tipe_prosedur }}
                                            </p>

                                            <p class="text-sm font-semibold
                                                      leading-6 text-gray-800">
                                                {{ $soal->pertanyaan }}
                                            </p>

                                        </div>

                                    </div>

                                    {{-- STATUS BENAR ATAU SALAH --}}
                                    <span class="self-start rounded-md
                                                 border px-3 py-1
                                                 text-xs font-semibold
                                        {{ $jawabanBenar
                                            ? 'border-[#d3e3d6] bg-[#f0f6f1] text-[#4c7b58]'
                                            : 'border-red-200 bg-red-50 text-red-700' }}">

                                        {{ $jawabanBenar ? 'Benar' : 'Salah' }}

                                    </span>

                                </div>


                                {{-- PILIHAN JAWABAN --}}
                                <div class="space-y-2">

                                    @foreach ($soal->pilihanJawabans as $pilihan)

                                        @php
                                            $dipilih = in_array(
                                                (string) $pilihan->id,
                                                $pilihanDipilih,
                                                true
                                            );
                                        @endphp

                                        <div class="flex items-start gap-3
                                                    rounded-lg border p-3
                                            {{ $pilihan->is_benar
                                                ? 'border-[#d3e3d6] bg-[#f0f6f1]'
                                                : ($dipilih
                                                    ? 'border-red-200 bg-red-50'
                                                    : 'border-[#e0e5df] bg-white') }}">

                                            <span class="text-sm font-bold
                                                         text-gray-700">
                                                {{ $pilihan->kode }}.
                                            </span>

                                            <p class="flex-1 text-sm
                                                      leading-6 text-gray-700">
                                                {{ $pilihan->teks_jawaban }}
                                            </p>

                                            <div class="flex flex-col gap-1
                                                        text-right text-xs">

                                                @if ($dipilih)
                                                    <span class="font-semibold
                                                                 text-[#304c3f]">
                                                        Dipilih
                                                    </span>
                                                @endif

                                                @if ($pilihan->is_benar)
                                                    <span class="font-semibold
                                                                 text-[#4c7b58]">
                                                        Jawaban Benar
                                                    </span>
                                                @endif

                                            </div>

                                        </div>

                                    @endforeach

                                    @if (empty($pilihanDipilih))

                                        <p class="pt-1 text-sm text-red-600">
                                            Peserta tidak memberikan jawaban.
                                        </p>

                                    @endif

                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>

            @endif

        @endforeach

    </section>


    {{-- ======================================== --}}
    {{-- TOMBOL KEMBALI --}}
    {{-- ======================================== --}}

    <div class="hasil-no-print flex justify-end">

        <a
            href="{{ route('hrd.hasil-assessment.index') }}"
            class="inline-flex h-10 items-center justify-center
                   rounded-lg border border-[#ccd7ce]
                   bg-[#f4f6f3] px-5
                   text-sm font-semibold text-[#304c3f]
                   transition hover:bg-[#e8eee9]"
        >
            Kembali ke Hasil Penilaian
        </a>

    </div>

</div>

@endsection
