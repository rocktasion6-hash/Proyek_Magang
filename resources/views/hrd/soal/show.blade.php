
@extends('layouts.hrd')

@section('title', 'Detail Bank Soal')
@section('page_title', 'Detail Bank Soal')

@section('content')

@php
    // Mendukung nama variabel upload atau soalUpload.
    $bankSoal = $upload ?? $soalUpload ?? null;

    if (!$bankSoal) {
        throw new \RuntimeException(
            'Data bank soal tidak tersedia dari controller.'
        );
    }

    $daftarSoal = $bankSoal->soals
        ->sortBy('nomor')
        ->values();

    $namaTipe = [
        'pilihan_ganda' => 'Pilihan Ganda',
        'pilihan_tunggal' => 'Pilihan Ganda',
        'multi_jawaban' => 'Multi Jawaban',
        'benar_salah' => 'Benar / Salah',
    ];

    $namaProsedur = [
        'peraturan_perusahaan' => 'Peraturan Perusahaan',
        'pengoperasian_jabatan' => 'SOP',
        'keselamatan_kerja' => 'K3',
    ];

    $jumlahPG = $daftarSoal->filter(
        fn ($soal) => in_array(
            $soal->tipe_soal,
            ['pilihan_ganda', 'pilihan_tunggal']
        )
    )->count();

    $jumlahMJ = $daftarSoal
        ->where('tipe_soal', 'multi_jawaban')
        ->count();

    $jumlahBS = $daftarSoal
        ->where('tipe_soal', 'benar_salah')
        ->count();
@endphp

<style>
    #detailBankSoal {
        width: 100%;
        max-width: none;
        margin: 0;
        color: #374151;
    }

    #detailBankSoal .db-card {
        background: #ffffff;
        border: 1px solid #e0e5df;
        border-radius: 10px;
        overflow: hidden;
    }

    #detailBankSoal .db-card-header {
        background: #f4f6f3;
        border-bottom: 1px solid #e0e5df;
        padding: 14px 20px;
    }

    #detailBankSoal .db-section-title {
        font-size: 14px;
        font-weight: 700;
        color: #304c3f;
        margin: 0;
    }

    #detailBankSoal .db-label {
        color: #6b7280;
        font-size: 12px;
        margin-bottom: 5px;
    }

    #detailBankSoal .db-value {
        color: #374151;
        font-size: 14px;
        font-weight: 600;
        overflow-wrap: anywhere;
    }

    #detailBankSoal .db-question {
        border-bottom: 1px solid #e8ece8;
    }

    #detailBankSoal .db-question:last-child {
        border-bottom: none;
    }

    #detailBankSoal .db-question summary {
        list-style: none;
        cursor: pointer;
    }

    #detailBankSoal .db-question summary::-webkit-details-marker {
        display: none;
    }

    #detailBankSoal .db-question[open] .db-chevron {
        transform: rotate(180deg);
    }

    #detailBankSoal .db-chevron {
        transition: transform .2s ease;
    }

    #detailBankSoal .db-option {
        border: 1px solid #e0e5df;
        border-radius: 8px;
        background: #ffffff;
        padding: 12px;
    }

    #detailBankSoal .db-option.correct {
        background: #edf5ee;
        border-color: #bfd7c3;
    }
</style>

<div id="detailBankSoal">

    {{-- HEADER HALAMAN --}}
    <div class="flex flex-col sm:flex-row
                sm:items-center sm:justify-between
                gap-4 mb-5">

        <div>
            <h1 class="text-2xl font-bold text-[#304c3f]">
                Detail Bank Soal
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Informasi file Excel dan daftar soal yang tersimpan.
            </p>
        </div>

        <a href="{{ route('hrd.soal.index') }}"
           class="inline-flex items-center justify-center
                  gap-2 px-4 py-2.5
                  bg-white border border-[#dfe5df]
                  text-[#304c3f] rounded-lg
                  text-sm font-semibold
                  hover:bg-[#f4f6f3] transition">

            <svg class="w-4 h-4"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 viewBox="0 0 24 24">
                <path d="M15 18l-6-6 6-6"/>
            </svg>

            Kembali
        </a>

    </div>


    {{-- INFORMASI BANK SOAL --}}
    <div class="db-card mb-4">

        <div class="db-card-header">
            <h2 class="db-section-title">
                Informasi Bank Soal
            </h2>
        </div>

        <div class="p-5">

            <div class="grid grid-cols-1 md:grid-cols-2
                        xl:grid-cols-4 gap-5">

                <div>
                    <p class="db-label">
                        Nama File
                    </p>

                    <p class="db-value">
                        {{ $bankSoal->nama_file_tampilan }}
                    </p>
                </div>

                <div>
                    <p class="db-label">
                        Departemen
                    </p>

                    <p class="db-value">
                        {{ $bankSoal->departemen?->nama_departemen ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="db-label">
                        Jabatan
                    </p>

                    <p class="db-value">
                        {{ $bankSoal->jabatan?->nama_jabatan ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="db-label">
                        Tanggal Upload
                    </p>

                    <p class="db-value">
                        {{ $bankSoal->created_at?->format('d M Y H:i') ?? '-' }}
                    </p>
                </div>

            </div>

        </div>

    </div>


    {{-- RINGKASAN SOAL --}}
    <div class="db-card mb-4">

        <div class="db-card-header">
            <h2 class="db-section-title">
                Ringkasan Soal
            </h2>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 p-5 gap-4">

            <div class="md:border-r border-gray-100">
                <p class="db-label">
                    Total Soal
                </p>

                <p class="text-2xl font-bold text-[#304c3f]">
                    {{ $daftarSoal->count() }}
                </p>
            </div>

            <div class="md:border-r border-gray-100">
                <p class="db-label">
                    Pilihan Ganda
                </p>

                <p class="text-2xl font-bold text-[#304c3f]">
                    {{ $jumlahPG }}
                </p>
            </div>

            <div class="md:border-r border-gray-100">
                <p class="db-label">
                    Multi Jawaban
                </p>

                <p class="text-2xl font-bold text-[#304c3f]">
                    {{ $jumlahMJ }}
                </p>
            </div>

            <div>
                <p class="db-label">
                    Benar / Salah
                </p>

                <p class="text-2xl font-bold text-[#304c3f]">
                    {{ $jumlahBS }}
                </p>
            </div>

        </div>

    </div>


    {{-- DAFTAR SOAL --}}
    <div class="db-card">

        <div class="db-card-header flex flex-col
                    sm:flex-row sm:items-center
                    sm:justify-between gap-3">

            <div>
                <h2 class="db-section-title">
                    Daftar Soal
                </h2>

                <p class="text-xs text-gray-500 mt-1">
                    Klik soal untuk melihat pilihan jawaban dan kunci.
                </p>
            </div>

            <span class="inline-flex items-center
                         px-3 py-1.5 rounded-md
                         bg-[#e8eee9] text-[#304c3f]
                         text-xs font-semibold">

                {{ $daftarSoal->count() }} Soal

            </span>

        </div>


        @forelse ($daftarSoal as $soal)

            <details class="db-question group">

                <summary class="px-5 py-4
                                hover:bg-[#fafbf9]
                                transition-colors">

                    <div class="flex items-start gap-3">

                        {{-- NOMOR SOAL --}}
                        <div class="w-9 h-9 flex-shrink-0
                                    bg-[#e8eee9]
                                    text-[#304c3f]
                                    rounded-lg
                                    flex items-center
                                    justify-center
                                    text-sm font-bold">

                            {{ $loop->iteration }}

                        </div>

                        <div class="flex-1 min-w-0">

                            {{-- TIPE SOAL --}}
                            <div class="flex flex-wrap items-center
                                        gap-2 mb-2">

                                <span class="px-2.5 py-1
                                             rounded-md
                                             bg-[#edf3ee]
                                             text-[#304c3f]
                                             text-xs font-semibold">

                                    {{ $namaTipe[$soal->tipe_soal]
                                        ?? ucwords(str_replace('_', ' ', $soal->tipe_soal ?? '-')) }}

                                </span>

                                {{-- KATEGORI --}}
                                <span class="px-2.5 py-1
                                             rounded-md
                                             bg-gray-100
                                             text-gray-600
                                             text-xs font-medium">

                                    {{ $namaProsedur[$soal->tipe_prosedur]
                                        ?? $soal->tipe_prosedur
                                        ?? '-' }}

                                </span>

                            </div>

                            {{-- PERTANYAAN --}}
                            <p class="text-sm font-semibold
                                      text-gray-800 leading-6">

                                {{ $soal->pertanyaan }}

                            </p>

                        </div>

                        {{-- ICON PANAH --}}
                        <svg class="db-chevron w-5 h-5
                                    text-gray-400
                                    flex-shrink-0 mt-2"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2"
                             viewBox="0 0 24 24">

                            <path d="M6 9l6 6 6-6"/>

                        </svg>

                    </div>

                </summary>


                {{-- DETAIL SOAL --}}
                <div class="bg-[#fafbf9]
                            px-5 pb-5 pt-2">

                    <div class="sm:ml-12">

                        <p class="text-xs font-bold uppercase
                                  tracking-wide text-[#304c3f]
                                  mb-3">

                            Pilihan Jawaban

                        </p>

                        <div class="space-y-2">

                            @forelse ($soal->pilihanJawabans as $pilihan)

                                <div class="db-option
                                    {{ $pilihan->is_benar ? 'correct' : '' }}">

                                    <div class="flex items-start gap-3">

                                        {{-- KODE JAWABAN --}}
                                        <span class="w-7 h-7
                                                     flex-shrink-0
                                                     rounded-md
                                                     flex items-center
                                                     justify-center
                                                     text-xs font-bold
                                                     {{ $pilihan->is_benar
                                                         ? 'bg-[#4c7b58] text-white'
                                                         : 'bg-[#f4f6f3] text-gray-600' }}">

                                            {{ $pilihan->kode }}

                                        </span>

                                        {{-- ISI JAWABAN --}}
                                        <p class="flex-1 text-sm
                                            {{ $pilihan->is_benar
                                                ? 'font-semibold text-[#304c3f]'
                                                : 'text-gray-700' }}">

                                            {{ $pilihan->teks_jawaban }}

                                        </p>

                                        {{-- KUNCI JAWABAN --}}
                                        @if ($pilihan->is_benar)

                                            <span class="px-2.5 py-1
                                                         bg-[#4c7b58]
                                                         text-white
                                                         rounded-md
                                                         text-xs font-semibold
                                                         flex-shrink-0">

                                                Benar

                                            </span>

                                        @endif

                                    </div>

                                </div>

                            @empty

                                <div class="px-4 py-3
                                            border border-[#e0e5df]
                                            bg-white rounded-lg">

                                    <p class="text-sm text-gray-500">
                                        Pilihan jawaban tidak tersedia.
                                    </p>

                                </div>

                            @endforelse

                        </div>

                    </div>

                </div>

            </details>

        @empty

            {{-- BELUM ADA SOAL --}}
            <div class="p-10 text-center">

                <div class="w-12 h-12 mx-auto
                            bg-[#f4f6f3] rounded-xl
                            flex items-center
                            justify-center mb-3">

                    <svg class="w-6 h-6 text-gray-400"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.5"
                         viewBox="0 0 24 24">

                        <rect x="5" y="4"
                              width="14" height="17"
                              rx="2"/>

                        <path d="M9 10h6M9 14h6M9 18h4"/>

                    </svg>

                </div>

                <p class="text-sm font-semibold text-gray-600">
                    Belum ada soal pada bank soal ini.
                </p>

                <p class="text-xs text-gray-500 mt-1">
                    Data soal akan muncul setelah file Excel berhasil diproses.
                </p>

            </div>

        @endforelse

    </div>

    {{-- TOMBOL KEMBALI BAWAH --}}
    <div class="flex justify-end mt-4">

        <a href="{{ route('hrd.soal.index') }}"
           class="inline-flex items-center justify-center
                  px-5 py-2.5 rounded-lg
                  bg-white border border-[#dfe5df]
                  text-[#304c3f]
                  text-sm font-semibold
                  hover:bg-[#f4f6f3] transition-colors">

            Kembali ke Bank Soal

        </a>

    </div>

</div>

@endsection
