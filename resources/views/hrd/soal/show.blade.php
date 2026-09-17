<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Detail Soal - HRD</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen">
@extends('layouts.hrd')
@section('title', 'Detail Soal')
@section('page_title', 'Detail Soal')
@section('content')
    <main class="max-w-4xl mx-auto px-6 py-10">

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">

            <div class="mb-8">

                <p class="text-sm text-gray-500">
                    Detail Bank Soal
                </p>

                <h1 class="text-2xl font-bold text-gray-800 mt-1">
                    Soal #{{ $soal->id }}
                </h1>

            </div>


            <!-- Informasi Soal -->
            <div class="space-y-6">

                <div>

                    <p class="text-sm text-gray-500">
                        Pertanyaan
                    </p>

                    <p class="text-lg font-semibold text-gray-800 mt-1">
                        {{ $soal->pertanyaan }}
                    </p>

                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>

                        <p class="text-sm text-gray-500">
                            Jabatan
                        </p>

                        <p class="font-semibold text-gray-800 mt-1">
                            {{ $soal->jabatan?->nama_jabatan ?? '-' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-sm text-gray-500">
                            Skill
                        </p>

                        <p class="font-semibold text-gray-800 mt-1">
                            {{ $soal->skill?->nama_skill ?? '-' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-sm text-gray-500">
                            Tipe Soal
                        </p>

                        <p class="font-semibold text-gray-800 mt-1">
                            {{ ucwords(str_replace('_', ' ', $soal->tipe_soal)) }}
                        </p>

                    </div>


                    <div>

                        <p class="text-sm text-gray-500">
                            Tingkat Kesulitan
                        </p>

                        <p class="font-semibold text-gray-800 mt-1">
                            {{ ucfirst($soal->tingkat_kesulitan) }}
                        </p>

                    </div>


                    <div>

                        <p class="text-sm text-gray-500">
                            Bobot
                        </p>

                        <p class="font-semibold text-gray-800 mt-1">
                            {{ number_format($soal->bobot, 0) }}
                        </p>

                    </div>

                </div>


                <!-- Pilihan Jawaban -->
                <div class="pt-6 border-t border-gray-200">

                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        Pilihan Jawaban
                    </h2>


                    <div class="space-y-3">

                        @foreach ($soal->pilihanJawabans as $pilihan)

                            <div
                                class="border rounded-lg p-4
                                {{ $pilihan->is_benar
                                    ? 'border-green-300 bg-green-50'
                                    : 'border-gray-200 bg-white'
                                }}"
                            >

                                <div class="flex items-start justify-between gap-4">

                                    <div>

                                        <span class="font-bold text-gray-800">
                                            {{ $pilihan->kode }}.
                                        </span>

                                        <span class="text-gray-700">
                                            {{ $pilihan->teks_jawaban }}
                                        </span>

                                    </div>


                                    @if ($pilihan->is_benar)

                                        <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700 font-medium">
                                            Jawaban Benar
                                        </span>

                                    @endif

                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>

            </div>


            <div class="mt-8 pt-6 border-t border-gray-200">

                <a
                    href="{{ route('hrd.soal.index') }}"
                    class="inline-flex px-5 py-3 bg-gray-800 hover:bg-gray-900 text-white rounded-lg font-medium"
                >
                    ← Kembali ke Bank Soal
                </a>

                <a
                    href="{{ route('hrd.soal.edit', $soal) }}"
                    class="inline-flex px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium"
                >
                    Edit Soal
                </a>

                <form method="POST" action="{{ route('hrd.soal.destroy', $soal) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus soal ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex px-5 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium">Hapus Soal</button>
                </form>
            </div>
        </div>
    </main>
    @endsection
</body>
</html>