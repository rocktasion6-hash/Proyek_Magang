<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Assessment - HRD</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="bg-gray-100 min-h-screen">
@extends('layouts.hrd')
@section('title', 'Assessment')
@section('page_title', 'Assessment')
@section('content')
    <nav class="bg-white border-b border-gray-200">

        <div class="max-w-7xl mx-auto px-6 py-4">

            <div class="flex items-center justify-between">

                <div>

                    <h1 class="text-xl font-bold text-gray-800">
                        Sistem Penilaian Jabatan
                    </h1>

                    <p class="text-sm text-gray-500">
                        Assessment
                    </p>

                </div>

                <div class="flex items-center gap-4">

                    <span class="text-sm text-gray-600">
                        {{ auth()->user()->name }}
                    </span>

                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm"
                        >
                            Logout
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </nav>


    <main class="max-w-7xl mx-auto px-6 py-8">

        @if (session('success'))

            <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 mb-6">
                {{ session('success') }}
            </div>

        @endif

        @if (session('error'))

            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-6">
                {{ session('error') }}
            </div>

        @endif


        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

            <div>

                <h2 class="text-2xl font-bold text-gray-800">
                    Assessment
                </h2>

                <p class="text-gray-500 mt-1">
                    Kelola assessment dan peserta ujian.
                </p>

            </div>


            <a
                href="{{ route('hrd.assessment.create') }}"
                class="inline-flex items-center justify-center px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium"
            >
                + Buat Assessment
            </a>

        </div>

        @php
            $kategoriAssessment = [
                'semua' => 'Semua',
                'pemindahan_jabatan' => 'Pemindahan Jabatan',
                'peningkatan_skill' => 'Peningkatan Skill',
                'kenaikan_jabatan' => 'Kenaikan Jabatan',
            ];
        @endphp

        <nav
            aria-label="Kategori assessment"
            class="mb-6 flex gap-2 overflow-x-auto border-b border-gray-200"
        >
            @foreach ($kategoriAssessment as $nilaiKategori => $labelKategori)
                <a
                    href="{{ route('hrd.assessment.index', ['kategori' => $nilaiKategori]) }}"
                    @if ($kategori === $nilaiKategori) aria-current="page" @endif
                    class="shrink-0 border-b-2 px-4 py-3 text-sm font-medium {{ $kategori === $nilaiKategori ? 'border-blue-600 text-blue-700' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}"
                >
                    {{ $labelKategori }}
                </a>
            @endforeach
        </nav>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="px-6 py-3 text-left">
                                #
                            </th>

                            <th class="px-6 py-3 text-left">
                                Assessment
                            </th>

                            <th class="px-6 py-3 text-left">
                                Jabatan
                            </th>

                            <th class="px-6 py-3 text-left">
                                Departemen
                            </th>

                            <th class="px-6 py-3 text-left">
                                Standar
                            </th>

                            <th class="px-6 py-3 text-left">
                                Durasi
                            </th>

                            <th class="px-6 py-3 text-left">
                                Peserta
                            </th>

                            <th class="px-6 py-3 text-left">
                                Status
                            </th>

                            <th class="px-6 py-3 text-left">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse ($assessments as $index => $assessment)

                            <tr class="hover:bg-gray-50">

                                <td class="px-6 py-4">
                                    {{ $assessments->firstItem() + $index }}
                                </td>


                                <td class="px-6 py-4">

                                    <p class="font-semibold text-gray-800">
                                        {{ $assessment->nama_assessment }}
                                    </p>

                                    <p class="text-xs text-gray-500 mt-1">
                                        Bank soal: {{ $assessment->soalUpload?->nama_file_tampilan ?? '-' }}
                                    </p>

                                </td>


                                <td class="px-6 py-4 text-gray-600">
                                    {{ $assessment->jabatan?->nama_jabatan ?? '-' }}
                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    {{ $assessment->departemen?->nama_departemen ?? '-' }}
                                </td>


                                <td class="px-6 py-4 text-gray-600">
                                    {{ number_format($assessment->standar_nilai, 0) }}
                                </td>


                                <td class="px-6 py-4 text-gray-600">
                                    {{ $assessment->durasi }} menit
                                </td>


                                <td class="px-6 py-4 text-gray-600">
                                    {{ $assessment->peserta_count }} orang
                                </td>


                                <td class="px-6 py-4">

                                    @if (in_array($assessment->status, ['aktif', 'draft'], true))
                                        @php
                                            $isAssessmentActive = $assessment->status === 'aktif';
                                        @endphp

                                        <form
                                            method="POST"
                                            action="{{ route('hrd.assessment.toggle-status', $assessment) }}"
                                        >
                                            @csrf
                                            <button
                                                type="submit"
                                                role="switch"
                                                aria-checked="{{ $isAssessmentActive ? 'true' : 'false' }}"
                                                aria-label="{{ $isAssessmentActive ? 'Nonaktifkan' : 'Aktifkan' }} {{ $assessment->nama_assessment }}"
                                                class="inline-flex items-center gap-2 rounded-full focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                                            >
                                                <span
                                                    aria-hidden="true"
                                                    class="relative h-6 w-11 rounded-full transition-colors duration-200 {{ $isAssessmentActive ? 'bg-green-500' : 'bg-gray-300' }}"
                                                >
                                                    <span
                                                        class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow transition-transform duration-200 {{ $isAssessmentActive ? 'translate-x-5' : 'translate-x-0' }}"
                                                    ></span>
                                                </span>
                                                <span class="text-sm font-medium {{ $isAssessmentActive ? 'text-green-700' : 'text-gray-600' }}">
                                                    {{ $isAssessmentActive ? 'Aktif' : 'Tidak Aktif' }}
                                                </span>
                                            </button>
                                        </form>

                                    @elseif ($assessment->status === 'selesai')

                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                            Selesai
                                        </span>

                                    @else

                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                            Dibatalkan
                                        </span>

                                    @endif

                                </td>


                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <a
                                            href="{{ route('hrd.assessment.show', $assessment) }}"
                                            class="font-medium text-blue-600 hover:text-blue-800"
                                        >
                                            Detail
                                        </a>

                                        <a
                                            href="{{ route('hrd.assessment.edit', $assessment) }}"
                                            class="font-medium text-indigo-600 hover:text-indigo-800"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('hrd.assessment.destroy', $assessment) }}"
                                            onsubmit="return confirm('Hapus assessment ini? Assessment yang sudah mulai dikerjakan atau memiliki hasil tidak dapat dihapus.')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="font-medium text-red-600 hover:text-red-800"
                                            >
                                                Hapus
                                            </button>
                                        </form>
                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="9"
                                    class="px-6 py-10 text-center text-gray-500"
                                >
                                    Belum ada assessment.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            @if ($assessments->hasPages())

                <div class="px-6 py-4 border-t border-gray-200">

                    {{ $assessments->links() }}

                </div>

            @endif

        </div>

    </main>
@endsection
</body>
</html>