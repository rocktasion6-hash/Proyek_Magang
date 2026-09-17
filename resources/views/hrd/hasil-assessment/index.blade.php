<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Hasil Penilaian - HRD</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>

<body class="bg-gray-100 min-h-screen">
@extends('layouts.hrd')
@section('title', 'Hasil Penilaian')
@section('page_title', 'Hasil Penilaian')
@section('content')
    <nav class="bg-white border-b border-gray-200">

        <div class="max-w-7xl mx-auto px-6 py-4">

            <div class="flex items-center justify-between">

                <div>

                    <h1 class="text-xl font-bold text-gray-800">
                        Sistem Penilaian Jabatan
                    </h1>

                    <p class="text-sm text-gray-500">
                        Hasil Penilaian
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


        <!-- Header -->
        <div class="mb-6">

            <h2 class="text-2xl font-bold text-gray-800">
                Hasil Penilaian
            </h2>

            <p class="text-gray-500 mt-1">
                Lihat hasil assessment yang telah dikerjakan oleh karyawan.
            </p>

        </div>


        <!-- Filter -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">

            <form
                method="GET"
                action="{{ route('hrd.hasil-assessment.index') }}"
            >

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <!-- Search -->
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Cari Karyawan
                        </label>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Nama atau NIK..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2"
                        >

                    </div>


                    <!-- Status -->
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                        </label>

                        <select
                            name="status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2"
                        >

                            <option value="">
                                Semua Status
                            </option>

                            <option
                                value="lulus"
                                @selected(request('status') === 'lulus')
                            >
                                Lulus
                            </option>

                            <option
                                value="tidak_lulus"
                                @selected(request('status') === 'tidak_lulus')
                            >
                                Tidak Lulus
                            </option>

                        </select>

                    </div>


                    <!-- Tombol -->
                    <div class="flex items-end gap-3">

                        <button
                            type="submit"
                            class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium"
                        >
                            Filter
                        </button>

                        <a
                            href="{{ route('hrd.hasil-assessment.index') }}"
                            class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium"
                        >
                            Reset
                        </a>

                    </div>

                </div>

            </form>

        </div>


        <!-- Tabel -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="px-6 py-3 text-left">
                                #
                            </th>

                            <th class="px-6 py-3 text-left">
                                Karyawan
                            </th>

                            <th class="px-6 py-3 text-left">
                                Assessment
                            </th>

                            <th class="px-6 py-3 text-left">
                                Jabatan
                            </th>

                            <th class="px-6 py-3 text-left">
                                Nilai
                            </th>

                            <th class="px-6 py-3 text-left">
                                Standar
                            </th>

                            <th class="px-6 py-3 text-left">
                                Status
                            </th>

                            <th class="px-6 py-3 text-left">
                                Tanggal
                            </th>

                            <th class="px-6 py-3 text-left">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse ($hasilAssessments as $index => $hasil)

                            @php
                                $peserta = $hasil->assessmentPeserta;
                            @endphp

                            <tr class="hover:bg-gray-50">

                                <td class="px-6 py-4">
                                    {{ $hasilAssessments->firstItem() + $index }}
                                </td>


                                <td class="px-6 py-4">

                                    <p class="font-semibold text-gray-800">
                                        {{ $peserta->karyawan->nama }}
                                    </p>

                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $peserta->karyawan->nik }}
                                    </p>

                                </td>


                                <td class="px-6 py-4">

                                    <p class="font-medium text-gray-800">
                                        {{ $peserta->assessment->nama_assessment }}
                                    </p>

                                </td>


                                <td class="px-6 py-4 text-gray-600">
                                    {{ $peserta->assessment->jabatan?->nama_jabatan ?? '-' }}
                                </td>


                                <td class="px-6 py-4">

                                    <span class="font-bold text-gray-800">
                                        {{ number_format($hasil->nilai_akhir, 2) }}
                                    </span>

                                </td>


                                <td class="px-6 py-4 text-gray-600">
                                    {{ number_format($hasil->standar_nilai, 2) }}
                                </td>


                                <td class="px-6 py-4">

                                    @if ($hasil->status === 'lulus')

                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            Lulus
                                        </span>

                                    @else

                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                            Tidak Lulus
                                        </span>

                                    @endif

                                </td>


                                <td class="px-6 py-4 text-gray-600">
                                    {{ $hasil->tanggal_ujian?->format('d M Y H:i') }}
                                </td>


                                <td class="px-6 py-4">

                                    <a
                                        href="{{ route('hrd.hasil-assessment.show', $hasil) }}"
                                        class="text-blue-600 hover:text-blue-800 font-medium"
                                    >
                                        Detail
                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="9"
                                    class="px-6 py-10 text-center text-gray-500"
                                >
                                    Belum ada hasil assessment.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            @if ($hasilAssessments->hasPages())

                <div class="px-6 py-4 border-t border-gray-200">

                    {{ $hasilAssessments->links() }}

                </div>

            @endif

        </div>

    </main>
@endsection
</body>

</html>