<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Detail Hasil Assessment - HRD</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>


<body class="bg-gray-100 min-h-screen">
@extends('layouts.hrd')
@section('title', 'Detail Hasil Penilaian')
@section('page_title', 'Detail Hasil Penilaian')
@section('content')
    <main class="max-w-5xl mx-auto px-6 py-10">


        <!-- Header -->
        <div class="mb-6">

            <h1 class="text-2xl font-bold text-gray-800">
                Detail Hasil Assessment
            </h1>

            <p class="text-gray-500 mt-1">
                Hasil ujian karyawan.
            </p>

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
                        Skill
                    </p>

                    <p class="font-semibold text-gray-800 mt-1">
                        {{ $hasilAssessment->assessmentPeserta->assessment->skill?->nama_skill ?? 'Umum' }}
                    </p>

                </div>

            </div>

        </div>


        <!-- Hasil Nilai -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">

            <h2 class="text-lg font-semibold text-gray-800 mb-5">
                Hasil Penilaian
            </h2>


            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">


                <div class="bg-gray-50 rounded-xl p-5 text-center">

                    <p class="text-sm text-gray-500">
                        Nilai Akhir
                    </p>

                    <p class="text-4xl font-bold text-gray-800 mt-2">
                        {{ number_format($hasilAssessment->nilai_akhir, 2) }}
                    </p>

                </div>


                <div class="bg-gray-50 rounded-xl p-5 text-center">

                    <p class="text-sm text-gray-500">
                        Standar Kelulusan
                    </p>

                    <p class="text-4xl font-bold text-gray-800 mt-2">
                        {{ number_format($hasilAssessment->standar_nilai, 2) }}
                    </p>

                </div>


                <div class="bg-gray-50 rounded-xl p-5 text-center">

                    <p class="text-sm text-gray-500">
                        Status
                    </p>

                    <div class="mt-3">

                        @if ($hasilAssessment->status === 'lulus')

                            <span class="inline-flex px-4 py-2 rounded-full bg-green-100 text-green-700 font-semibold">
                                LULUS
                            </span>

                        @else

                            <span class="inline-flex px-4 py-2 rounded-full bg-red-100 text-red-700 font-semibold">
                                TIDAK LULUS
                            </span>

                        @endif

                    </div>

                </div>

            </div>


            <div class="mt-6 text-sm text-gray-500">

                Tanggal ujian:
                {{ $hasilAssessment->tanggal_ujian?->format('d M Y H:i') }}

            </div>

        </div>


        <!-- Detail Jawaban -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">

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


        <div class="mt-6">

            <a
                href="{{ route('hrd.hasil-assessment.index') }}"
                class="inline-flex px-5 py-3 bg-gray-800 hover:bg-gray-900 text-white rounded-lg font-medium"
            >
                ← Kembali
            </a>

        </div>

    </main>
@endsection
</body>

</html>