
@extends('layouts.hrd')

@section('title', 'Hasil Penilaian')
@section('page_title', 'Hasil Penilaian')

@section('content')

@php
    // Statistik berdasarkan data pada halaman pagination saat ini.
    $dataHalaman = $hasilAssessments->getCollection();

    $totalHasil = $dataHalaman->count();

    $totalLulus = $dataHalaman
        ->where('status', 'lulus')
        ->count();

    $totalTidakLulus = $dataHalaman
        ->where('status', 'tidak_lulus')
        ->count();
@endphp

<div class="w-full min-w-0 space-y-5">

    {{-- ================================================= --}}
    {{-- HEADER HALAMAN --}}
    {{-- ================================================= --}}

    <div class="flex flex-col gap-3 sm:flex-row
                sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-[#304c3f]">
                Hasil Penilaian
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Pantau hasil ujian, nilai, dan status kelulusan karyawan.
            </p>
        </div>

    </div>


    {{-- ================================================= --}}
    {{-- REKAP HASIL PENILAIAN --}}
    {{-- ================================================= --}}

    <section class="w-full min-w-0 rounded-xl
                    border border-[#e0e5df] bg-white">

        <div class="border-b border-[#e0e5df] px-5 py-4">

            <h2 class="text-sm font-bold text-[#304c3f]">
                Rekap Hasil Penilaian
            </h2>

            <p class="mt-1 text-xs text-gray-500">
                Ringkasan hasil assessment pada halaman yang ditampilkan.
            </p>

        </div>

        <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-3">

            {{-- TOTAL HASIL --}}
            <div class="min-w-0 rounded-lg border
                        border-[#e5e9e5] bg-[#fafbf9] p-4">

                <p class="text-xs font-semibold uppercase
                          tracking-wide text-gray-500">
                    Total Hasil Ditampilkan
                </p>

                <div class="mt-2 flex items-baseline gap-2">

                    <p class="text-3xl font-bold text-[#304c3f]">
                        {{ $totalHasil }}
                    </p>

                    <span class="text-xs text-gray-500">
                        Hasil
                    </span>

                </div>

                <div class="mt-3 h-1.5 w-full overflow-hidden
                            rounded-full bg-[#e8eee9]">

                    <div class="h-full rounded-full bg-[#4c7b58]"
                         style="width: {{ $totalHasil > 0 ? 100 : 0 }}%">
                    </div>

                </div>

            </div>


            {{-- LULUS --}}
            <div class="min-w-0 rounded-lg border
                        border-[#e5e9e5] bg-[#fafbf9] p-4">

                <p class="text-xs font-semibold uppercase
                          tracking-wide text-gray-500">
                    Lulus
                </p>

                <div class="mt-2 flex items-baseline gap-2">

                    <p class="text-3xl font-bold text-[#4c7b58]">
                        {{ $totalLulus }}
                    </p>

                    <span class="text-xs text-gray-500">
                        Hasil
                    </span>

                </div>

                <div class="mt-3 h-1.5 w-full overflow-hidden
                            rounded-full bg-[#e8eee9]">

                    <div class="h-full rounded-full bg-[#4c7b58]"
                         style="width: {{ $totalHasil > 0
                             ? ($totalLulus / $totalHasil) * 100
                             : 0 }}%">
                    </div>

                </div>

            </div>


            {{-- TIDAK LULUS --}}
            <div class="min-w-0 rounded-lg border
                        border-[#e5e9e5] bg-[#fafbf9] p-4">

                <p class="text-xs font-semibold uppercase
                          tracking-wide text-gray-500">
                    Tidak Lulus
                </p>

                <div class="mt-2 flex items-baseline gap-2">

                    <p class="text-3xl font-bold text-red-600">
                        {{ $totalTidakLulus }}
                    </p>

                    <span class="text-xs text-gray-500">
                        Hasil
                    </span>

                </div>

                <div class="mt-3 h-1.5 w-full overflow-hidden
                            rounded-full bg-[#e8eee9]">

                    <div class="h-full rounded-full bg-red-400"
                         style="width: {{ $totalHasil > 0
                             ? ($totalTidakLulus / $totalHasil) * 100
                             : 0 }}%">
                    </div>

                </div>

            </div>

        </div>

    </section>


    {{-- ================================================= --}}
    {{-- FILTER DATA --}}
    {{-- ================================================= --}}

    <section class="w-full min-w-0 rounded-xl
                    border border-[#e0e5df] bg-white">

        <div class="border-b border-[#e0e5df] px-5 py-4">

            <h2 class="text-sm font-bold text-[#304c3f]">
                Filter Data
            </h2>

            <p class="mt-1 text-xs text-gray-500">
                Cari hasil penilaian berdasarkan nama, NIK, atau status.
            </p>

        </div>

        <form method="GET"
              action="{{ route('hrd.hasil-assessment.index') }}">

            <div class="grid grid-cols-1 items-end
                        gap-3 p-5 md:grid-cols-12">

                {{-- CARI KARYAWAN --}}
                <div class="min-w-0 md:col-span-6">

                    <label for="search"
                           class="mb-2 block text-xs
                                  font-semibold text-[#304c3f]">
                        Cari Karyawan
                    </label>

                    <input
                        id="search"
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama atau NIK..."
                        class="block h-11 w-full min-w-0
                               rounded-lg border border-[#dfe5df]
                               bg-white px-3 text-sm text-gray-800
                               placeholder:text-gray-400
                               outline-none transition
                               focus:border-[#4c7b58]
                               focus:ring-2 focus:ring-[#dce8df]"
                    >

                </div>


                {{-- STATUS KELULUSAN --}}
                <div class="min-w-0 md:col-span-3">

                    <label for="status"
                           class="mb-2 block text-xs
                                  font-semibold text-[#304c3f]">
                        Status Kelulusan
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="block h-11 w-full min-w-0
                               rounded-lg border border-[#dfe5df]
                               bg-white px-3 text-sm text-gray-800
                               outline-none transition
                               focus:border-[#4c7b58]
                               focus:ring-2 focus:ring-[#dce8df]"
                    >

                        <option value="">
                            Semua Status
                        </option>

                        <option value="lulus"
                            @selected(request('status') === 'lulus')>
                            Lulus
                        </option>

                        <option value="tidak_lulus"
                            @selected(request('status') === 'tidak_lulus')>
                            Tidak Lulus
                        </option>

                    </select>

                </div>


                {{-- FILTER DAN RESET --}}
                <div class="min-w-0 md:col-span-3">

                    <div class="flex w-full items-center gap-2">

                        {{-- FILTER --}}
                        <button
                            type="submit"
                            class="inline-flex h-11 min-w-0 flex-1
                                   items-center justify-center gap-2
                                   whitespace-nowrap rounded-lg
                                   bg-[#4c7b58] px-3
                                   text-sm font-semibold text-white
                                   transition-colors
                                   hover:bg-[#3e6749]"
                        >

                            <svg class="h-4 w-4 shrink-0"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="2"
                                 viewBox="0 0 24 24">

                                <circle cx="11" cy="11" r="7"/>
                                <path d="m20 20-4-4"/>

                            </svg>

                            Filter

                        </button>


                        {{-- RESET --}}
                        <a
                            href="{{ route('hrd.hasil-assessment.index') }}"
                            class="inline-flex h-11 min-w-0 flex-1
                                   items-center justify-center
                                   whitespace-nowrap rounded-lg
                                   border border-[#dfe5df]
                                   bg-white px-3
                                   text-sm font-semibold text-[#304c3f]
                                   transition-colors
                                   hover:bg-[#f4f6f3]"
                        >
                            Reset
                        </a>

                    </div>

                </div>

            </div>

        </form>

    </section>


    {{-- ================================================= --}}
    {{-- TABEL HASIL PENILAIAN --}}
    {{-- ================================================= --}}

    <section class="w-full min-w-0 overflow-hidden
                    rounded-xl border border-[#e0e5df] bg-white">

        {{-- HEADER TABEL --}}
        <div class="flex flex-col gap-3 border-b
                    border-[#e0e5df] px-5 py-4
                    sm:flex-row sm:items-center
                    sm:justify-between">

            <div class="min-w-0">

                <h2 class="text-sm font-bold text-[#304c3f]">
                    Daftar Hasil Penilaian
                </h2>

                <p class="mt-1 text-xs text-gray-500">
                    Data hasil assessment karyawan.
                </p>

            </div>

            <div class="flex shrink-0 items-center gap-2">

                <span class="text-xs text-gray-500">
                    Total Data
                </span>

                <span class="rounded-md bg-[#e8eee9]
                             px-3 py-1.5 text-xs font-bold
                             text-[#304c3f]">

                    {{ $hasilAssessments->total() }}

                </span>

            </div>

        </div>


        {{-- CONTAINER SCROLL TABEL --}}
        <div class="w-full min-w-0 overflow-x-auto">

            <table class="w-full min-w-[1050px]
                          table-auto border-collapse text-sm">

                <thead class="bg-[#f4f6f3]">

                    <tr class="text-xs uppercase
                               tracking-wide text-[#304c3f]">

                        <th class="whitespace-nowrap px-4 py-4
                                   text-left font-bold">
                            No
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            Karyawan
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            Assessment
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            Jabatan
                        </th>

                        <th class="px-4 py-4 text-center font-bold">
                            Nilai
                        </th>

                        <th class="px-4 py-4 text-center font-bold">
                            Standar
                        </th>

                        <th class="px-4 py-4 text-center font-bold">
                            Status
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            Tanggal Ujian
                        </th>

                        <th class="px-4 py-4 text-center font-bold">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-[#e8ece8]">

                    @forelse ($hasilAssessments as $index => $hasil)

                        @php
                            $peserta = $hasil->assessmentPeserta;
                            $karyawan = $peserta?->karyawan;
                            $assessment = $peserta?->assessment;
                            $isLulus = $hasil->status === 'lulus';
                        @endphp

                        <tr class="transition-colors
                                   hover:bg-[#fafbf9]">

                            {{-- NOMOR --}}
                            <td class="whitespace-nowrap px-4 py-4
                                       text-gray-600">

                                {{ $hasilAssessments->firstItem() + $index }}

                            </td>


                            {{-- KARYAWAN --}}
                            <td class="px-4 py-4">

                                <div class="min-w-[130px]">

                                    <p class="font-semibold text-gray-800">
                                        {{ $karyawan?->nama ?? '-' }}
                                    </p>

                                    <p class="mt-1 text-xs text-gray-500">
                                        NIK: {{ $karyawan?->nik ?? '-' }}
                                    </p>

                                </div>

                            </td>


                            {{-- ASSESSMENT --}}
                            <td class="px-4 py-4">

                                <p class="min-w-[160px]
                                          font-semibold text-gray-800">
                                    {{ $assessment?->nama_assessment ?? '-' }}
                                </p>

                            </td>


                            {{-- JABATAN --}}
                            <td class="px-4 py-4 text-gray-600">

                                {{ $assessment?->jabatan?->nama_jabatan ?? '-' }}

                            </td>


                            {{-- NILAI --}}
                            <td class="px-4 py-4 text-center">

                                <span class="font-bold
                                    {{ $isLulus
                                        ? 'text-[#4c7b58]'
                                        : 'text-red-600' }}">

                                    {{ number_format($hasil->nilai_akhir, 2) }}

                                </span>

                            </td>


                            {{-- STANDAR --}}
                            <td class="px-4 py-4 text-center
                                       font-medium text-gray-700">

                                {{ number_format($hasil->standar_nilai, 2) }}

                            </td>


                            {{-- STATUS --}}
                            <td class="px-4 py-4 text-center">

                                @if ($isLulus)

                                    <span class="inline-flex items-center
                                                 justify-center rounded-md
                                                 bg-[#4c7b58] px-3 py-1
                                                 text-xs font-semibold
                                                 text-white">

                                        Lulus

                                    </span>

                                @else

                                    <span class="inline-flex items-center
                                                 justify-center rounded-md
                                                 border border-red-200
                                                 bg-red-50 px-3 py-1
                                                 text-xs font-semibold
                                                 text-red-700">

                                        Tidak Lulus

                                    </span>

                                @endif

                            </td>


                            {{-- TANGGAL UJIAN --}}
                            <td class="whitespace-nowrap
                                       px-4 py-4 text-gray-600">

                                {{ $hasil->tanggal_ujian?->format('d M Y H:i') ?? '-' }}

                            </td>


                            {{-- TOMBOL DETAIL --}}
                            <td class="px-4 py-4 text-center">

                                <a
                                    href="{{ route('hrd.hasil-assessment.show', $hasil) }}"
                                    class="inline-flex items-center
                                           justify-center rounded-md
                                           border border-[#d9dfda]
                                           bg-[#f4f6f3] px-3 py-2
                                           text-xs font-semibold
                                           text-[#304c3f]
                                           transition-colors
                                           hover:bg-[#e8ede7]"
                                >

                                    Detail

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="9"
                                class="px-6 py-12 text-center">

                                <p class="text-sm font-semibold
                                          text-gray-600">
                                    Belum ada hasil penilaian.
                                </p>

                                <p class="mt-1 text-xs text-gray-400">
                                    Data hasil assessment akan muncul di sini.
                                </p>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if ($hasilAssessments->hasPages())

            <div class="border-t border-[#e0e5df]
                        px-5 py-4">

                {{ $hasilAssessments->withQueryString()->links() }}

            </div>

        @endif

    </section>

</div>

@endsection
