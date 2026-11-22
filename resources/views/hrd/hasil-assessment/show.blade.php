
@extends('layouts.hrd')

@section('title', 'Detail Hasil Penilaian')
@section('page_title', 'Detail Hasil Penilaian')

@section('content')

@php
    $peserta = $hasilAssessment->assessmentPeserta;
    $karyawan = $peserta->karyawan;
    $assessment = $peserta->assessment;
    $jawabans = $peserta->jawabans;
    $soalAssessment = $assessment->soals;

    $isLulus = $hasilAssessment->status === 'lulus';

    $nilaiTextClass = $isLulus
        ? 'text-green-700'
        : 'text-red-700';

    $nilaiBgClass = $isLulus
        ? 'bg-green-50 border-green-200'
        : 'bg-red-50 border-red-200';

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
        'keselamatan_kerja' => 'K3 - Prosedur Pengoperasian Keselamatan Kerja',
    ];

    $tipeLabels = [
        'pilihan_ganda' => 'Pilihan Ganda',
        'multi_jawaban' => 'Multi Jawaban',
        'benar_salah' => 'Benar/Salah',
    ];

    $normalisasiTipe = function ($tipe) {
        if (in_array($tipe, ['pilihan_tunggal', 'pg'])) {
            return 'pilihan_ganda';
        }

        if ($tipe === 'mj') {
            return 'multi_jawaban';
        }

        if (in_array($tipe, ['bs', 'true_false'])) {
            return 'benar_salah';
        }

        return $tipe;
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

    // Soal diurutkan berdasarkan tipe jawaban.
    $soalTerurut = $soalAssessment
        ->sortBy(function ($soal) use ($normalisasiTipe, $urutanTipe) {
            $tipe = $normalisasiTipe($soal->tipe_soal);

            return [
                $urutanTipe[$tipe] ?? 99,
                $soal->pivot->nomor_soal ?? 9999,
            ];
        })
        ->values();

    // Rekap benar dan salah per kategori dan tipe.
    $rekapKesalahan = [];

    foreach ($kategoriLabels as $kode => $nama) {
        foreach ($tipeLabels as $tipe => $label) {
            $rekapKesalahan[$kode][$tipe] = [
                'benar' => 0,
                'salah' => 0,
            ];
        }
    }

    // Analisis poin pengetahuan.
    $analisisPengetahuan = [];

    foreach ($kategoriNama as $kode => $nama) {
        $soals = $soalAssessment
            ->where('tipe_prosedur', $kode);

        $poinMaksimal = 0;
        $poinPeserta = 0;

        foreach ($soals as $soal) {
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

            $benar = count($dipilih) > 0
                && $dipilih === $kunci;

            if ($benar) {
                $poinPeserta += $bobot;
            }

            $tipe = $normalisasiTipe($soal->tipe_soal);

            if (isset($rekapKesalahan[$kode][$tipe])) {
                if ($benar) {
                    $rekapKesalahan[$kode][$tipe]['benar']++;
                } else {
                    $rekapKesalahan[$kode][$tipe]['salah']++;
                }
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

        $benar = count($dipilih) > 0
            && $dipilih === $kunci;

        if ($benar) {
            continue;
        }

        $jawabanDipilih = $soal->pilihanJawabans
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

        $detailKesalahan[$grup]['tipe'] = $tipe;
        $detailKesalahan[$grup]['kategori'] = $kategori;

        $detailKesalahan[$grup]['nama'] =
            $tipeLabels[$tipe] . ' - ' . $kategoriLabels[$kategori];

        $detailKesalahan[$grup]['soal'][] = [
            'nomor' => $soal->pivot->nomor_soal,
            'pertanyaan' => $soal->pertanyaan,
            'dipilih' => $jawabanDipilih,
            'kunci' => $jawabanBenar,
        ];
    }

    $detailKesalahan = collect($detailKesalahan)
        ->sortBy(function ($item) use (
            $urutanTipe,
            $urutanKategori
        ) {
            return [
                $urutanTipe[$item['tipe']] ?? 99,
                $urutanKategori[$item['kategori']] ?? 99,
            ];
        })
        ->values();
@endphp

<style>
    /* Judul-judul section, bukan judul utama. */
    .hasil-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 12px;
        margin-bottom: 14px;
        background: #edf5ef;
        border-left: 4px solid #4a7c59;
        border-radius: 0 8px 8px 0;
        color: #2c4a3e;
        font-size: 15px;
        font-weight: 750;
        line-height: 1.4;
    }

    .hasil-section-description {
        margin-top: -6px;
        margin-bottom: 14px;
        padding-left: 12px;
        font-size: 12px;
        color: #6b7280;
    }

    @page {
        size: A4 portrait;
        margin: 12mm;
    }

    @media print {
        body {
            background: #fff !important;
        }

        #hrd-sidebar,
        .topbar,
        .print\:hidden,
        .hasil-no-print {
            display: none !important;
        }

        #hrd-app,
        #hrd-app .main,
        #hrd-app .content,
        .hasil-print-area {
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .hasil-print-area {
            display: block !important;
        }

        .hasil-print-area > div {
            margin-bottom: 12px !important;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .hasil-print-area .grid {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
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

{{-- TOMBOL AKSI: TIDAK MASUK PRINT --}}
<div class="flex flex-wrap justify-end gap-2 mb-4 hasil-no-print">

    <a href="{{ route('hrd.hasil-assessment.pdf', $hasilAssessment) }}"
       class="inline-flex items-center gap-2
              bg-[#2c4a3e] hover:bg-[#4a7c59]
              text-white px-4 py-2.5 rounded-lg
              text-sm font-semibold transition">

        <svg width="17" height="17" fill="none"
             stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24">
            <path d="M12 3v12m-5-5 5 5 5-5"/>
            <path d="M5 17v4h14v-4"/>
        </svg>

        Download PDF
    </a>

    <button type="button"
            onclick="window.print()"
            class="inline-flex items-center gap-2
                   bg-white border border-gray-300
                   hover:bg-gray-100 text-[#2c4a3e]
                   px-4 py-2.5 rounded-lg
                   text-sm font-semibold transition">

        <svg width="17" height="17" fill="none"
             stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24">
            <path d="M6 9V3h12v6M6 18H4V9h16v9h-2M6 14h12v7H6z"/>
        </svg>

        Print Laporan
    </button>

</div>


<div class="w-full max-w-7xl mx-auto">

    {{-- ================================================== --}}
    {{-- AREA YANG BOLEH DICETAK                        --}}
    {{-- ================================================== --}}

    <div class="hasil-print-area space-y-4">

        {{-- HEADER UTAMA --}}
        <div class="bg-gradient-to-r from-[#f1f7f2] to-white
                    border border-[#dce8df] rounded-xl p-5 shadow-sm">

            <div class="flex flex-col md:flex-row
                        md:items-center justify-between gap-4">

                <div class="flex items-start gap-3">

                    <div class="w-11 h-11 bg-[#dceee0]
                                rounded-lg flex items-center
                                justify-center flex-shrink-0">

                        <svg class="w-6 h-6 text-[#2c4a3e]"
                             fill="none" stroke="currentColor"
                             stroke-width="1.8"
                             viewBox="0 0 24 24">

                            <path d="M9 12h6m-6 4h6M9 8h6M5 4h14v16H5z"/>

                        </svg>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase
                                  tracking-wider text-[#4a7c59] mb-1">
                            Laporan Hasil Assessment
                        </p>

                        <h1 class="text-xl md:text-2xl font-extrabold
                                   text-[#2c4a3e]">
                            {{ $assessment->nama_assessment }}
                        </h1>

                        <p class="text-sm text-gray-500 mt-2">
                            Tanggal Ujian:
                            <span class="font-semibold text-gray-700">
                                {{ $hasilAssessment->tanggal_ujian?->format('d M Y H:i') ?? '-' }}
                            </span>
                        </p>
                    </div>

                </div>

                {{-- NILAI TES --}}
                <div class="min-w-[165px] border rounded-xl
                            px-5 py-3 {{ $nilaiBgClass }}">

                    <p class="text-xs uppercase font-bold
                              tracking-wider text-gray-500">
                        Nilai Tes
                    </p>

                    <p class="text-4xl font-extrabold
                              mt-1 tabular-nums {{ $nilaiTextClass }}">
                        {{ number_format($hasilAssessment->nilai_akhir, 2) }}
                    </p>

                    <p class="text-xs text-gray-500 mt-1">
                        Standar:
                        {{ number_format($hasilAssessment->standar_nilai, 2) }}
                    </p>
                </div>

            </div>
        </div>

        {{-- INFORMASI KARYAWAN + INFORMASI JABATAN --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- INFORMASI KARYAWAN --}}
            <div class="bg-white border border-gray-200
                        rounded-xl shadow-sm p-4">

                <h2 class="hasil-section-title">
                    Informasi Karyawan
                </h2>

                <div class="space-y-3">

                    <div>
                        <p class="text-xs text-gray-500 mb-1">
                            Nama Karyawan
                        </p>
                        <p class="text-sm font-semibold text-gray-800">
                            {{ $karyawan->nama }}
                        </p>
                    </div>

                    <div class="border-t border-gray-100 pt-3">
                        <p class="text-xs text-gray-500 mb-1">
                            NIK
                        </p>
                        <p class="text-sm font-semibold text-gray-800">
                            {{ $karyawan->nik }}
                        </p>
                    </div>

                </div>
            </div>

            {{-- INFORMASI JABATAN --}}
            <div class="bg-white border border-gray-200
                        rounded-xl shadow-sm p-4">

                <h2 class="hasil-section-title">
                    Informasi Jabatan
                </h2>

                <div class="space-y-3">

                    <div>
                        <p class="text-xs text-gray-500 mb-1">
                            Jabatan Awal
                        </p>
                        <p class="text-sm font-semibold text-gray-800">
                            {{ $jabatanAwal }}
                        </p>
                    </div>

                    <div class="border-t border-gray-100 pt-3">
                        <p class="text-xs text-gray-500 mb-1">
                            Jabatan Tujuan
                        </p>
                        <p class="text-sm font-semibold text-[#2c4a3e]">
                            {{ $jabatanTujuan }}
                        </p>
                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- ================================================== --}}
    {{-- BAGIAN DI BAWAH INI HANYA UNTUK TAMPILAN LAYAR --}}
    {{-- TIDAK MASUK HASIL PRINT                       --}}
    {{-- ================================================== --}}

    <div class="hasil-no-print space-y-4 mt-4">

        {{-- ANALISIS KESALAHAN PER TIPE JAWABAN --}}
        <section class="bg-white border border-gray-200
                        rounded-xl shadow-sm p-4">

            <h2 class="hasil-section-title">
                Analisis Kesalahan per Tipe Jawaban
            </h2>

            <p class="hasil-section-description">
                Jumlah jawaban benar dan salah berdasarkan kategori PP, SOP, dan K3.
            </p>

            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">

                    <thead class="bg-[#e8f1ea] text-[#2c4a3e]">
                        <tr>
                            <th class="p-3 text-left">Kategori</th>
                            <th class="p-3 text-center">Pilihan Ganda</th>
                            <th class="p-3 text-center">Multi Jawaban</th>
                            <th class="p-3 text-center">Benar/Salah</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($kategoriLabels as $kode => $nama)
                            <tr class="border-b border-gray-100">

                                <td class="p-3 font-bold text-[#2c4a3e]">
                                    {{ $nama }}
                                </td>

                                @foreach ($tipeLabels as $tipe => $label)

                                    @php
                                        $hasil = $rekapKesalahan[$kode][$tipe];
                                    @endphp

                                    <td class="p-3 text-center">
                                        <div class="flex flex-col
                                                    items-center gap-1">

                                            <span class="font-medium text-green-700">
                                                {{ $hasil['benar'] }} Benar
                                            </span>

                                            <span class="px-3 py-1 rounded-full
                                                text-xs font-semibold
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
        </section>


        {{-- ANALISIS POIN PENGETAHUAN --}}
        <section class="bg-white border border-gray-200
                        rounded-xl shadow-sm p-4">

            <h2 class="hasil-section-title">
                Analisis Poin Pengetahuan
            </h2>

            <p class="hasil-section-description">
                Rincian pencapaian poin berdasarkan kategori pengetahuan.
            </p>

            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">

                    <thead class="bg-[#e8f1ea] text-[#2c4a3e]">
                        <tr>
                            <th class="p-3 text-left">
                                Kategori Pengetahuan
                            </th>
                            <th class="p-3 text-center">
                                Poin Maksimal
                            </th>
                            <th class="p-3 text-center">
                                Poin Peserta
                            </th>
                            <th class="p-3 text-center">
                                Persentase
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($analisisPengetahuan as $item)

                            <tr class="border-b border-gray-100">
                                <td class="p-3 font-medium text-gray-800">
                                    {{ $item['nama'] }}
                                </td>

                                <td class="p-3 text-center">
                                    {{ number_format($item['maksimal'], 2) }}
                                </td>

                                <td class="p-3 text-center
                                           font-bold text-[#2c4a3e]">
                                    {{ number_format($item['peserta'], 2) }}
                                </td>

                                <td class="p-3 text-center font-semibold">
                                    {{ number_format($item['persentase'], 2) }}%
                                </td>
                            </tr>

                        @endforeach
                    </tbody>

                </table>
            </div>
        </section>


        {{-- DETAIL KESALAHAN JAWABAN --}}
        <section class="bg-white border border-gray-200
                        rounded-xl shadow-sm p-4">

            <h2 class="hasil-section-title">
                Detail Kesalahan Jawaban
            </h2>

            <p class="hasil-section-description">
                Soal yang salah diurutkan dari Pilihan Ganda,
                Multi Jawaban, lalu Benar/Salah.
            </p>

            @forelse ($detailKesalahan as $grup)

                <details class="border border-gray-200
                                rounded-lg mb-2 overflow-hidden">

                    <summary class="p-3 cursor-pointer
                                    bg-[#f4f8f5] hover:bg-[#e8f1ea]
                                    text-sm font-semibold text-[#2c4a3e]">

                        {{ $grup['nama'] }}

                        <span class="ml-2 text-xs font-medium text-red-700">
                            ({{ count($grup['soal']) }} salah)
                        </span>

                    </summary>

                    <div class="p-3 space-y-3">

                        @foreach ($grup['soal'] as $item)

                            <div class="border border-gray-100
                                        rounded-lg p-3">

                                <p class="font-bold text-sm
                                          text-gray-800 mb-2">
                                    Soal Nomor {{ $item['nomor'] }}
                                </p>

                                <p class="text-sm text-gray-700 mb-3">
                                    {{ $item['pertanyaan'] }}
                                </p>

                                <div class="bg-red-50 rounded-lg p-3 mb-2">

                                    <p class="text-xs font-bold
                                              text-red-700 mb-2">
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

                                    <p class="text-xs font-bold
                                              text-green-700 mb-2">
                                        Jawaban yang Benar
                                    </p>

                                    @forelse ($item['kunci'] as $jawaban)
                                        <p class="text-sm text-green-800 mb-1">
                                            {{ $jawaban }}
                                        </p>
                                    @empty
                                        <p class="text-sm text-gray-500">
                                            Kunci jawaban belum tersedia.
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
                    Tidak ditemukan jawaban salah
                    pada soal yang dapat dianalisis.
                </div>
            @endforelse

        </section>


        {{-- DETAIL SEMUA JAWABAN --}}
        <section class="bg-white border border-gray-200
                        rounded-xl shadow-sm p-4">

            <h2 class="hasil-section-title">
                Detail Jawaban
            </h2>

            <p class="hasil-section-description">
                Seluruh soal dan jawaban peserta berdasarkan tipe.
            </p>

            @foreach ($tipeLabels as $kodeTipe => $namaTipe)

                @php
                    $soalTipe = $soalTerurut->filter(
                        fn ($soal) =>
                            $normalisasiTipe($soal->tipe_soal) === $kodeTipe
                    );
                @endphp

                @if ($soalTipe->isNotEmpty())

                    <div class="mb-5">

                        <div class="flex items-center gap-2 mb-3">

                            <div class="w-8 h-8 bg-[#e8f1ea]
                                        text-[#2c4a3e] rounded-lg
                                        flex items-center justify-center
                                        font-bold text-sm">
                                {{ $loop->iteration }}
                            </div>

                            <div>
                                <h3 class="text-sm font-bold text-[#2c4a3e]">
                                    {{ $namaTipe }}
                                </h3>

                                <p class="text-xs text-gray-500">
                                    {{ $soalTipe->count() }} soal
                                </p>
                            </div>
                        </div>

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

                            <div class="border border-gray-200
                                        rounded-lg p-4 mb-3">

                                <div class="flex flex-col sm:flex-row
                                            sm:items-start justify-between
                                            gap-2 mb-3">

                                    <div class="flex items-start gap-3">

                                        <div class="w-8 h-8 flex-shrink-0
                                                    rounded-full bg-[#e8f1ea]
                                                    text-[#2c4a3e] flex
                                                    items-center justify-center
                                                    font-bold text-sm">

                                            {{ $soal->pivot->nomor_soal }}

                                        </div>

                                        <div>

                                            <p class="text-xs font-semibold
                                                      text-[#4a7c59] mb-1">
                                                {{ $kategoriLabels[$soal->tipe_prosedur] ?? $soal->tipe_prosedur }}
                                            </p>

                                            <p class="text-sm font-semibold
                                                      text-gray-800">
                                                {{ $soal->pertanyaan }}
                                            </p>

                                        </div>

                                    </div>

                                    <span class="flex-shrink-0 px-3 py-1
                                        rounded-full text-xs font-bold
                                        {{ $jawabanBenar
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-red-100 text-red-700' }}">

                                        {{ $jawabanBenar ? 'Benar' : 'Salah' }}

                                    </span>

                                </div>

                                <div class="space-y-2">

                                    @foreach ($soal->pilihanJawabans as $pilihan)

                                        @php
                                            $dipilih = in_array(
                                                (string) $pilihan->id,
                                                $pilihanDipilih,
                                                true
                                            );
                                        @endphp

                                        <div class="flex items-start
                                                    gap-3 p-3 rounded-lg border
                                            {{ $pilihan->is_benar
                                                ? 'bg-green-50 border-green-200'
                                                : ($dipilih
                                                    ? 'bg-red-50 border-red-200'
                                                    : 'bg-gray-50 border-gray-100') }}">

                                            <div class="font-semibold
                                                        text-gray-700">
                                                {{ $pilihan->kode }}.
                                            </div>

                                            <div class="flex-1 text-sm
                                                        text-gray-700">
                                                {{ $pilihan->teks_jawaban }}
                                            </div>

                                            <div class="flex flex-col gap-1
                                                        text-xs text-right
                                                        flex-shrink-0">

                                                @if ($dipilih)
                                                    <span class="font-semibold
                                                                 text-blue-700">
                                                        Dipilih
                                                    </span>
                                                @endif

                                                @if ($pilihan->is_benar)
                                                    <span class="font-semibold
                                                                 text-green-700">
                                                        Jawaban Benar
                                                    </span>
                                                @endif

                                            </div>

                                        </div>

                                    @endforeach

                                    @if (empty($pilihanDipilih))
                                        <p class="text-sm text-red-600">
                                            Peserta tidak memberikan jawaban.
                                        </p>
                                    @endif

                                </div>

                            </div>

                        @endforeach

                    </div>

                @endif

            @endforeach

        </section>


        {{-- KEMBALI --}}
        <div class="pt-1">

            <a href="{{ route('hrd.hasil-assessment.index') }}"
               class="inline-flex items-center gap-2
                      px-4 py-2.5 bg-gray-800
                      hover:bg-gray-900 text-white
                      rounded-lg text-sm font-medium transition">

                ← Kembali ke Hasil Penilaian

            </a>

        </div>

    </div>

</div>

@endsection
