@extends('layouts.hrd')
@section('title', 'Detail Hasil Penilaian')
@section('page_title', 'Detail Hasil Penilaian')
@section('content')

{{-- Tombol Cetak Laporan A4 --}}
<div class="flex justify-end mb-4 print:hidden">
    
{{-- Tombol Download PDF dan Print --}}
<div class="flex flex-wrap justify-end gap-3 mb-4 print:hidden">

    <!-- Download PDF -->
    <a href="{{ route('hrd.hasil-assessment.pdf', $hasilAssessment) }}"
       class="inline-flex items-center gap-2
              bg-[#2c4a3e] hover:bg-[#4a7c59]
              text-white px-5 py-3 rounded-xl
              text-sm font-semibold shadow-sm transition">

        <svg xmlns="http://www.w3.org/2000/svg"
             width="18" height="18"
             viewBox="0 0 24 24"
             fill="none" stroke="currentColor"
             stroke-width="2">
            <path d="M12 3v12m-5-5 5 5 5-5"/>
            <path d="M5 17v4h14v-4"/>
        </svg>

        Download PDF
    </a>

    <!-- Print Laporan -->
    <button type="button"
            onclick="window.print()"
            class="inline-flex items-center gap-2
                   bg-white border border-gray-300
                   hover:bg-gray-100 text-[#2c4a3e]
                   px-5 py-3 rounded-xl
                   text-sm font-semibold transition">

        <svg xmlns="http://www.w3.org/2000/svg"
             width="18" height="18"
             viewBox="0 0 24 24"
             fill="none" stroke="currentColor"
             stroke-width="2">
            <path d="M6 9V3h12v6M6 18H4V9h16v9h-2M6 14h12v7H6z"/>
        </svg>

        Print Laporan
    </button>

</div>

</div>

<style>
    @page {
        size: A4 portrait;
        margin: 12mm;
    }

    @media print {
        body {
            background: white !important;
        }

        #hrd-sidebar,
        .topbar,
        .print\:hidden {
            display: none !important;
        }

        #hrd-app,
        #hrd-app .main,
        #hrd-app .content {
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .max-w-7xl,
        .max-w-6xl {
            max-width: none !important;
            width: 100% !important;
        }

        table {
            width: 100%;
            font-size: 10px;
        }

        tr {
            break-inside: avoid;
        }

        .shadow-sm {
            box-shadow: none !important;
        }

        * {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

    <div class="w-full max-w-7xl mx-auto space-y-6">


        
        <!-- Header Detail Hasil Assessment -->
            <div class="mb-6 bg-gradient-to-r from-[#f1f7f2] to-white
                         border border-[#dce8df] rounded-2xl p-6
                         shadow-sm">

            <div class="flex flex-col md:flex-row
                md:items-center justify-between gap-4">

             <div class="flex items-start gap-4">

            <!-- Icon -->
            <div class="w-14 h-14 bg-[#dceee0]
                        rounded-xl flex items-center
                        justify-center flex-shrink-0">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-7 h-7 text-[#2c4a3e]"
                     fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M9 12h6m-6 4h6M9 8h6
                             M5 4h14v16H5z"/>
                </svg>

            </div>

            <!-- Judul -->
            <div>
                <p class="text-xs font-bold uppercase
                          tracking-wider text-[#4a7c59] mb-1">
                    Laporan Hasil Assessment
                </p>

                <h1 class="text-xl md:text-2xl font-bold
                           text-[#2c4a3e]">
                    {{ $hasilAssessment->assessmentPeserta->assessment->nama_assessment }}
                </h1>

                <p class="text-sm text-gray-500 mt-2">
                    {{ $hasilAssessment->assessmentPeserta->karyawan->nama }}
                    <span class="mx-2">•</span>
                    NIK:
                    {{ $hasilAssessment->assessmentPeserta->karyawan->nik }}
                </p>
            </div>

        </div>

        <!-- Status -->
        <div class="flex-shrink-0">
            @if ($hasilAssessment->status === 'lulus')
                <span class="inline-flex items-center
                             bg-green-100 text-green-800
                             px-4 py-2 rounded-full
                             text-sm font-bold">
                    ✓ LULUS
                </span>
            @else
                <span class="inline-flex items-center
                             bg-red-100 text-red-700
                             px-4 py-2 rounded-full
                             text-sm font-bold">
                    TIDAK LULUS
                </span>
            @endif
        </div>

    </div>

</div>



        <!-- Informasi Karyawan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

            <h2 class="text-lg font-semibold text-gray-800 mb-5">
                Informasi Karyawan
            </h2>


            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div>

                    <p class="text-sm text-gray-500">
                        Nama
                    </p>

                    <p class="font-semibold text-gray-800 mt-1">
                        {{ $hasilAssessment->assessmentPeserta->karyawan->nama }}
                    </p>

                </div>


                <div>

                    <p class="text-sm text-gray-500">
                        NIK
                    </p>

                    <p class="font-semibold text-gray-800 mt-1">
                        {{ $hasilAssessment->assessmentPeserta->karyawan->nik }}
                    </p>

                </div>


                <div>

                    <p class="text-sm text-gray-500">
                        Jabatan
                    </p>

                    <p class="font-semibold text-gray-800 mt-1">
                        {{ $hasilAssessment->assessmentPeserta->karyawan->jabatan?->nama_jabatan ?? '-' }}
                    </p>

                </div>

            </div>

        </div>


        <!-- Informasi Assessment -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">

            <h2 class="text-lg font-semibold text-gray-800 mb-5">
                Informasi Assessment
            </h2>


            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div>

                    <p class="text-sm text-gray-500">
                        Assessment
                    </p>

                    <p class="font-semibold text-gray-800 mt-1">
                        {{ $hasilAssessment->assessmentPeserta->assessment->nama_assessment }}
                    </p>

                </div>


                <div>

                    <p class="text-sm text-gray-500">
                        Jabatan Tujuan
                    </p>

                    <p class="font-semibold text-gray-800 mt-1">
                        {{ $hasilAssessment->assessmentPeserta->assessment->jabatan?->nama_jabatan ?? '-' }}
                    </p>

                </div>


                <div>

                    <p class="text-sm text-gray-500">
                        File Bank Soal
                    </p>

                    <p class="font-semibold text-gray-800 mt-1">
                        {{ $hasilAssessment->assessmentPeserta->assessment->soalUpload?->nama_file_tampilan ?? '-' }}
                    </p>

                </div>

            </div>

        </div>


        <!-- Hasil Nilai: hanya perbaikan tampilan, menggunakan data lama -->
        <section class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 sm:p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-1 h-8 rounded-full bg-[#e3b535]"></div>
                <div>
                    <h2 class="text-lg font-bold text-[#2c4a3e]">Ringkasan Nilai Tes</h2>
                    <p class="text-sm text-gray-500">Hasil assessment dan standar kelulusan peserta.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="rounded-xl border border-[#dce8df] bg-[#f1f7f2] p-5">
                    <p class="text-sm font-semibold text-[#4a7c59]">Nilai Tes</p>
                    <p class="text-5xl font-extrabold text-[#2c4a3e] mt-2 tabular-nums">
                        {{ number_format($hasilAssessment->nilai_akhir, 2) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-3">Nilai akhir peserta</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-gray-50 p-5">
                    <p class="text-sm font-semibold text-gray-600">Standar Kelulusan</p>
                    <p class="text-5xl font-extrabold text-gray-800 mt-2 tabular-nums">
                        {{ number_format($hasilAssessment->standar_nilai, 2) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-3">Batas nilai untuk lulus</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5">
                    <p class="text-sm font-semibold text-gray-600">Status Assessment</p>
                    <div class="mt-4">
                        @if ($hasilAssessment->status === 'lulus')
                            <span class="inline-flex items-center gap-2 rounded-full bg-green-100 px-4 py-2 text-base font-bold text-green-800">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6"/></svg>
                                LULUS
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-red-100 px-4 py-2 text-base font-bold text-red-700">TIDAK LULUS</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-4">Berdasarkan hasil penilaian tersimpan</p>
                </div>
            </div>

            <div class="mt-5 border-t border-gray-100 pt-4 text-sm text-gray-500">
                Tanggal ujian:
                <span class="font-medium text-gray-700">{{ $hasilAssessment->tanggal_ujian?->format('d M Y H:i') ?? '-' }}</span>
            </div>
        </section>


{{-- Analisis Poin Pengetahuan --}}
@php
    $assessment = $hasilAssessment->assessmentPeserta->assessment;
    $jawabans = $hasilAssessment->assessmentPeserta->jawabans;

    $kategoriPengetahuan = [
        'peraturan_perusahaan' => 'PP - Peraturan Perusahaan',
        'pengoperasian_jabatan' => 'SOP - Prosedur Pengoperasian Jabatan',
        'keselamatan_kerja' => 'K3 - Prosedur Pengoperasian Keselamatan Kerja',
    ];

    $analisisPengetahuan = [];

    foreach ($kategoriPengetahuan as $kode => $nama) {
        $soals = $assessment->soals->where('tipe_prosedur', $kode);

        $poinMaksimal = 0;
        $poinPeserta = 0;

        foreach ($soals as $soal) {
            $bobot = $assessment->nilaiPointUntukSoal($soal);
            $poinMaksimal += $bobot;

            $dipilih = $jawabans
                ->where('soal_id', $soal->id)
                ->pluck('pilihan_jawaban_id')
                ->map(fn ($id) => (string) $id)
                ->sort()
                ->values()
                ->toArray();

            $kunci = $soal->pilihanJawabans
                ->where('is_benar', true)
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->sort()
                ->values()
                ->toArray();

            if (count($dipilih) > 0 && $dipilih === $kunci) {
                $poinPeserta += $bobot;
            }
        }

        $persentase = $poinMaksimal > 0
            ? ($poinPeserta / $poinMaksimal) * 100
            : 0;

        $analisisPengetahuan[] = [
            'nama' => $nama,
            'maksimal' => $poinMaksimal,
            'peserta' => $poinPeserta,
            'persentase' => $persentase,
        ];
    }
@endphp

<div class="bg-white rounded-xl shadow-sm border
            border-gray-100 p-6">

    <h2 class="text-lg font-bold text-[#2c4a3e] mb-2">
        Analisis Poin Pengetahuan
    </h2>

    <p class="text-sm text-gray-500 mb-5">
        Rincian pencapaian nilai berdasarkan kategori pengetahuan.
    </p>

    <div class="overflow-x-auto">
        <table class="w-full text-sm border-collapse">
            <thead class="bg-[#e8f1ea] text-[#2c4a3e]">
                <tr>
                    <th class="p-3 text-left">Kategori Pengetahuan</th>
                    <th class="p-3 text-center">Poin Maksimal</th>
                    <th class="p-3 text-center">Poin Peserta</th>
                    <th class="p-3 text-center">Persentase</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($analisisPengetahuan as $item)
                    <tr class="border-b border-gray-100">
                        <td class="p-3 font-medium">
                            {{ $item['nama'] }}
                        </td>
                        <td class="p-3 text-center">
                            {{ number_format($item['maksimal'], 2) }}
                        </td>
                        <td class="p-3 text-center font-semibold">
                            {{ number_format($item['peserta'], 2) }}
                        </td>
                        <td class="p-3 text-center">
                            {{ number_format($item['persentase'], 2) }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>


{{-- Analisis Kesalahan per Tipe Jawaban --}}
@php
    $rekapKesalahan = [];

    $kategoriAnalisis = [
        'peraturan_perusahaan' => 'PP',
        'pengoperasian_jabatan' => 'SOP',
        'keselamatan_kerja' => 'K3',
    ];

    $tipeAnalisis = [
        'pilihan_ganda' => 'PG',
        'multi_jawaban' => 'MJ',
        'benar_salah' => 'Benar/Salah',
    ];

    $peserta = $hasilAssessment->assessmentPeserta;
    $soalAssessment = $peserta->assessment->soals;
    $jawabanPeserta = $peserta->jawabans;

    foreach ($kategoriAnalisis as $kode => $label) {
        $rekapKesalahan[$kode] = [
            'nama' => $label,
            'tipe' => [],
        ];

        foreach ($tipeAnalisis as $tipe => $namaTipe) {
            $rekapKesalahan[$kode]['tipe'][$tipe] = [
                'benar' => 0,
                'salah' => 0,
            ];
        }
    }

    foreach ($soalAssessment as $soal) {
        $kategori = $soal->tipe_prosedur;
        $tipe = $soal->tipe_soal;

        // Tipe pilihan_tunggal termasuk PG.
        if ($tipe === 'pilihan_tunggal') {
            $tipe = 'pilihan_ganda';
        }

        if (
            !isset($rekapKesalahan[$kategori]) ||
            !isset($tipeAnalisis[$tipe])
        ) {
            continue;
        }

        $dipilih = $jawabanPeserta
            ->where('soal_id', $soal->id)
            ->pluck('pilihan_jawaban_id')
            ->map(fn ($id) => (string) $id)
            ->sort()
            ->values()
            ->toArray();

        $kunci = $soal->pilihanJawabans
            ->where('is_benar', true)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->sort()
            ->values()
            ->toArray();

        $benar = count($dipilih) > 0 &&
                 $dipilih === $kunci;

        if ($benar) {
            $rekapKesalahan[$kategori]
                ['tipe'][$tipe]['benar']++;
        } else {
            $rekapKesalahan[$kategori]
                ['tipe'][$tipe]['salah']++;
        }
    }
@endphp

<div class="bg-white rounded-xl shadow-sm
            border border-gray-100 p-6">

    <h2 class="text-lg font-bold text-[#2c4a3e] mb-2">
        Analisis Kesalahan per Tipe Jawaban
    </h2>

    <p class="text-sm text-gray-500 mb-5">
        Jumlah jawaban benar dan salah untuk PP,
        SOP, dan K3 berdasarkan tipe soal.
    </p>

    <div class="overflow-x-auto">
        <table class="w-full text-sm border-collapse">
            <thead class="bg-[#e8f1ea] text-[#2c4a3e]">
                <tr>
                    <th class="p-3 text-left">Kategori</th>
                    <th class="p-3 text-center">PG</th>
                    <th class="p-3 text-center">MJ</th>
                    <th class="p-3 text-center">Benar/Salah</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($rekapKesalahan as $kategori)
                    <tr class="border-b border-gray-100">
                        <td class="p-3 font-bold text-[#2c4a3e]">
                            {{ $kategori['nama'] }}
                        </td>

                        @foreach ($tipeAnalisis as $kodeTipe => $namaTipe)
                            @php
                                $hasil = $kategori['tipe'][$kodeTipe];
                            @endphp

                            <td class="p-3 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-green-700 font-medium">
                                        {{ $hasil['benar'] }} Benar
                                    </span>

                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $hasil['salah'] > 0
                                            ? 'bg-red-100 text-red-700'
                                            : 'bg-green-100 text-green-700' }}">
                                        {{ $hasil['salah'] }} Salah
                                    </span>
                                </div>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


{{-- Detail Kesalahan Jawaban --}}
@php
    $detailKesalahan = [];

    foreach ($soalAssessment->sortBy('pivot.nomor_soal') as $soal) {
        $kategori = $soal->tipe_prosedur;
        $tipe = $soal->tipe_soal === 'pilihan_tunggal'
            ? 'pilihan_ganda'
            : $soal->tipe_soal;

        if (
            !isset($kategoriAnalisis[$kategori]) ||
            !isset($tipeAnalisis[$tipe])
        ) {
            continue;
        }

        $pilihanPeserta = $jawabanPeserta
            ->where('soal_id', $soal->id)
            ->pluck('pilihan_jawaban_id')
            ->filter(fn ($id) => $id !== null)
            ->map(fn ($id) => (string) $id)
            ->unique()->sort()->values()->all();

        $pilihanBenar = $soal->pilihanJawabans
            ->where('is_benar', true)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->unique()->sort()->values()->all();

        $benar = count($pilihanPeserta) > 0
            && $pilihanPeserta === $pilihanBenar;

        if ($benar) {
            continue;
        }

        $jawabanDipilih = $soal->pilihanJawabans
            ->filter(fn ($pilihan) =>
                in_array((string) $pilihan->id, $pilihanPeserta, true)
            )
            ->map(fn ($pilihan) =>
                $pilihan->kode . '. ' . $pilihan->teks_jawaban
            )->values()->all();

        $jawabanBenar = $soal->pilihanJawabans
            ->where('is_benar', true)
            ->map(fn ($pilihan) =>
                $pilihan->kode . '. ' . $pilihan->teks_jawaban
            )->values()->all();

        $grup = $kategori . '_' . $tipe;

        $detailKesalahan[$grup]['nama'] =
            $kategoriAnalisis[$kategori] . ' — ' .
            $tipeAnalisis[$tipe];

        $detailKesalahan[$grup]['soal'][] = [
            'nomor' => $soal->pivot->nomor_soal,
            'pertanyaan' => $soal->pertanyaan,
            'dipilih' => $jawabanDipilih,
            'kunci' => $jawabanBenar,
        ];
    }
@endphp

<div class="bg-white rounded-xl shadow-sm border
            border-gray-100 p-6 print:hidden">

    <h2 class="text-lg font-bold text-[#2c4a3e] mb-2">
        Detail Kesalahan Jawaban
    </h2>

    <p class="text-sm text-gray-500 mb-5">
        Buka kategori untuk melihat soal yang
        dijawab salah oleh peserta.
    </p>

    @forelse ($detailKesalahan as $grup)
        <details class="border border-gray-200
                        rounded-xl mb-3 overflow-hidden">

            <summary class="p-4 cursor-pointer
                            bg-[#f4f8f5] hover:bg-[#e8f1ea]
                            font-semibold text-[#2c4a3e]">

                {{ $grup['nama'] }}

                <span class="ml-2 text-xs font-medium
                             text-red-700">
                    ({{ count($grup['soal']) }} salah)
                </span>
            </summary>

            <div class="p-4 space-y-4">

                @foreach ($grup['soal'] as $item)
                    <div class="border border-gray-100
                                rounded-lg p-4">

                        <p class="font-bold text-gray-800 mb-2">
                            Soal Nomor {{ $item['nomor'] }}
                        </p>

                        <p class="text-sm text-gray-700 mb-4">
                            {{ $item['pertanyaan'] }}
                        </p>

                        <div class="bg-red-50 rounded-lg p-3 mb-3">
                            <p class="text-xs font-bold text-red-700 mb-2">
                                Jawaban Peserta
                            </p>

                            @forelse ($item['dipilih'] as $jawaban)
                                <p class="text-sm text-red-800 mb-1">
                                    {{ $jawaban }}
                                </p>
                            @empty
                                <p class="text-sm text-red-700">
                                    Tidak dijawab
                                </p>
                            @endforelse
                        </div>

                        <div class="bg-green-50 rounded-lg p-3">
                            <p class="text-xs font-bold text-green-700 mb-2">
                                Jawaban yang Benar
                            </p>

                            @forelse ($item['kunci'] as $jawaban)
                                <p class="text-sm text-green-800 mb-1">
                                    {{ $jawaban }}
                                </p>
                            @empty
                                <p class="text-sm text-gray-500">
                                    Kunci jawaban belum tersedia
                                </p>
                            @endforelse
                        </div>

                    </div>
                @endforeach

            </div>
        </details>
    @empty
        <div class="bg-green-50 text-green-800
                    rounded-lg p-4 text-sm">
            Tidak ditemukan jawaban salah pada
            soal yang dapat dianalisis.
        </div>
    @endforelse
</div>


        <!-- Detail Jawaban -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6 print:hidden">

            <h2 class="text-lg font-semibold text-gray-800 mb-5">
                Detail Jawaban
            </h2>


            @foreach (
                $hasilAssessment
                    ->assessmentPeserta
                    ->assessment
                    ->soals
                    ->sortBy(function ($soal) {
                        return $soal->pivot->nomor_soal;
                    })
                as $index => $soal
            )

                @php

                    $jawabanKaryawan =
                        $hasilAssessment
                            ->assessmentPeserta
                            ->jawabans
                            ->where('soal_id', $soal->id);

                @endphp


                <div class="border border-gray-200 rounded-xl p-5 mb-4">

                    <div class="flex gap-4">

                        <div class="w-8 h-8 flex-shrink-0 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold">

                            {{ $soal->pivot->nomor_soal }}

                        </div>


                        <div class="flex-1">

                            <p class="font-semibold text-gray-800">

                                {{ $soal->pertanyaan }}

                            </p>


                            <div class="mt-4 space-y-2">

                                @foreach ($soal->pilihanJawabans as $pilihan)

                                    @php

                                        $dipilih =
                                            $jawabanKaryawan
                                                ->contains(
                                                    'pilihan_jawaban_id',
                                                    $pilihan->id
                                                );

                                    @endphp


                                    <div
                                        class="flex items-start gap-3 p-3 rounded-lg
                                        {{
                                            $pilihan->is_benar
                                                ? 'bg-green-50 border border-green-200'
                                                : ($dipilih
                                                    ? 'bg-red-50 border border-red-200'
                                                    : 'bg-gray-50')
                                        }}"
                                    >

                                        <div class="font-semibold">
                                            {{ $pilihan->kode }}.
                                        </div>


                                        <div class="flex-1">

                                            {{ $pilihan->teks_jawaban }}

                                        </div>


                                        <div class="text-xs">

                                            @if ($dipilih)

                                                <span class="font-medium text-blue-600">
                                                    Dipilih
                                                </span>

                                            @endif

                                            @if ($pilihan->is_benar)

                                                <span class="font-medium text-green-600">
                                                    Jawaban Benar
                                                </span>

                                            @endif

                                        </div>

                                    </div>

                                @endforeach

                            </div>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>


        <div class="mt-6 print:hidden">

            <a
                href="{{ route('hrd.hasil-assessment.index') }}"
                class="inline-flex px-5 py-3 bg-gray-800 hover:bg-gray-900 text-white rounded-lg font-medium"
            >
                ← Kembali
            </a>

        </div>

    </div>
@endsection
