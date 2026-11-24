
@extends('layouts.hrd')

@section('title', 'Detail Riwayat Karyawan')
@section('page_title', 'Detail Riwayat Karyawan')

@section('content')

<div class="w-full min-w-0 space-y-5">

    {{-- ============================================== --}}
    {{-- HEADER HALAMAN --}}
    {{-- ============================================== --}}

    <div class="flex flex-col gap-3 sm:flex-row
                sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-[#304c3f]">
                Detail Riwayat Karyawan
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Informasi perjalanan jabatan dan perkembangan
                keterampilan karyawan.
            </p>
        </div>

        <a href="{{ route('hrd.riwayat.index') }}"
           class="inline-flex h-10 items-center justify-center
                  gap-2 self-start rounded-lg
                  border border-[#dfe5df] bg-white
                  px-4 text-sm font-semibold text-[#304c3f]
                  transition-colors hover:bg-[#f4f6f3]
                  sm:self-auto">

            <svg class="h-4 w-4"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 viewBox="0 0 24 24">

                <path d="M15 18l-6-6 6-6"/>

            </svg>

            Kembali
        </a>

    </div>


    {{-- ============================================== --}}
    {{-- INFORMASI KARYAWAN --}}
    {{-- ============================================== --}}

    <section class="w-full min-w-0 rounded-xl
                    border border-[#e0e5df] bg-white p-5">

        {{-- JUDUL SECTION --}}
        <div class="mb-5 overflow-hidden rounded-lg
                    bg-[#f4f6f3]">

            <div class="rounded-l-md border-l-[5px]
                        border-[#4c7b58] px-5 py-3">

                <h2 class="text-base font-bold text-[#304c3f]">
                    Informasi Karyawan
                </h2>

            </div>

        </div>


        {{-- DATA KARYAWAN --}}
        <div class="grid grid-cols-1 gap-x-8
                    lg:grid-cols-2">

            {{-- NIK --}}
            <div class="flex flex-col gap-1
                        border-b border-[#e8ece8]
                        py-4 sm:flex-row sm:items-start">

                <p class="w-full flex-shrink-0 text-sm
                          font-medium text-gray-500
                          sm:w-40">
                    NIK
                </p>

                <p class="min-w-0 flex-1 break-words
                          text-sm font-semibold text-gray-800">
                    {{ $karyawan->nik }}
                </p>

            </div>


            {{-- NAMA --}}
            <div class="flex flex-col gap-1
                        border-b border-[#e8ece8]
                        py-4 sm:flex-row sm:items-start">

                <p class="w-full flex-shrink-0 text-sm
                          font-medium text-gray-500
                          sm:w-40">
                    Nama Karyawan
                </p>

                <p class="min-w-0 flex-1 break-words
                          text-sm font-semibold text-gray-800">
                    {{ $karyawan->nama }}
                </p>

            </div>


            {{-- DEPARTEMEN --}}
            <div class="flex flex-col gap-1
                        border-b border-[#e8ece8]
                        py-4 sm:flex-row sm:items-start">

                <p class="w-full flex-shrink-0 text-sm
                          font-medium text-gray-500
                          sm:w-40">
                    Departemen
                </p>

                <p class="min-w-0 flex-1 break-words
                          text-sm font-semibold text-gray-800">
                    {{ $karyawan->departemen?->nama_departemen ?? '-' }}
                </p>

            </div>


            {{-- JABATAN --}}
            <div class="flex flex-col gap-1
                        border-b border-[#e8ece8]
                        py-4 sm:flex-row sm:items-start">

                <p class="w-full flex-shrink-0 text-sm
                          font-medium text-gray-500
                          sm:w-40">
                    Jabatan Saat Ini
                </p>

                <p class="min-w-0 flex-1 break-words
                          text-sm font-semibold text-gray-800">
                    {{ $karyawan->jabatan?->nama_jabatan ?? '-' }}
                </p>

            </div>


            {{-- LEVEL --}}
            <div class="flex flex-col gap-1
                        border-b border-[#e8ece8]
                        py-4 sm:flex-row sm:items-start">

                <p class="w-full flex-shrink-0 text-sm
                          font-medium text-gray-500
                          sm:w-40">
                    Level Jabatan
                </p>

                <p class="min-w-0 flex-1 text-sm
                          font-semibold text-gray-800">

                    @if ($karyawan->jabatan?->level_jabatan)
                        Level {{ $karyawan->jabatan->level_jabatan }}
                    @else
                        -
                    @endif

                </p>

            </div>


            {{-- TANGGAL MASUK --}}
            <div class="flex flex-col gap-1
                        border-b border-[#e8ece8]
                        py-4 sm:flex-row sm:items-start">

                <p class="w-full flex-shrink-0 text-sm
                          font-medium text-gray-500
                          sm:w-40">
                    Tanggal Masuk
                </p>

                <p class="min-w-0 flex-1 text-sm
                          font-semibold text-gray-800">

                    {{ $karyawan->tanggal_masuk
                        ? \Carbon\Carbon::parse($karyawan->tanggal_masuk)->format('d M Y')
                        : '-' }}

                </p>

            </div>

        </div>

    </section>


    {{-- ============================================== --}}
    {{-- RIWAYAT JABATAN --}}
    {{-- ============================================== --}}

    <section class="w-full min-w-0 overflow-hidden
                    rounded-xl border border-[#e0e5df]
                    bg-white">

        <div class="p-5 pb-4">

            {{-- JUDUL SECTION --}}
            <div class="mb-3 overflow-hidden rounded-lg
                        bg-[#f4f6f3]">

                <div class="rounded-l-md border-l-[5px]
                            border-[#4c7b58] px-5 py-3">

                    <h2 class="text-base font-bold text-[#304c3f]">
                        Riwayat Jabatan
                    </h2>

                </div>

            </div>

            <p class="text-sm text-gray-500">
                Daftar perubahan jabatan, kenaikan jabatan, dan
                pemindahan jabatan selama masa kerja.
            </p>

        </div>


        {{-- TABEL RIWAYAT JABATAN --}}
        <div class="w-full min-w-0 overflow-x-auto">

            <table class="w-full min-w-[1050px]
                          border-collapse text-sm">

                <thead class="bg-[#f4f6f3]">

                    <tr class="text-xs uppercase
                               tracking-wide text-[#304c3f]">

                        <th class="px-4 py-4 text-left font-bold">
                            No
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            Jabatan
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            Jenis Perubahan
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            Tanggal Mulai
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            Tanggal Selesai
                        </th>

                        <th class="px-4 py-4 text-center font-bold">
                            Nilai Assessment
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            Keterangan
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-[#e8ece8]">

                    @forelse ($riwayatJabatans as $riwayat)

                        @php
                            $jenisPerubahan = $riwayat->jenis_perubahan;

                            $warnaJenis = match ($jenisPerubahan) {
                                'awal' => 'bg-gray-100 text-gray-600 border-gray-200',
                                'kenaikan' => 'bg-[#e8f1ea] text-[#304c3f] border-[#d3e3d6]',
                                'pemindahan' => 'bg-[#fff8e6] text-[#927018] border-[#f2e3ab]',
                                default => 'bg-gray-100 text-gray-600 border-gray-200',
                            };

                            $namaJenis = match ($jenisPerubahan) {
                                'awal' => 'Jabatan Awal',
                                'kenaikan' => 'Kenaikan Jabatan',
                                'pemindahan' => 'Pemindahan Jabatan',
                                default => ucfirst(
                                    str_replace('_', ' ', $jenisPerubahan ?? '-')
                                ),
                            };
                        @endphp

                        <tr class="transition-colors
                                   hover:bg-[#fafbf9]">

                            {{-- NOMOR --}}
                            <td class="whitespace-nowrap
                                       px-4 py-4 text-gray-600">

                                {{ $loop->iteration }}

                            </td>


                            {{-- JABATAN --}}
                            <td class="px-4 py-4">

                                <p class="font-semibold text-gray-800">
                                    {{ $riwayat->jabatan?->nama_jabatan ?? '-' }}
                                </p>

                                <p class="mt-1 text-xs text-gray-500">

                                    @if ($riwayat->jabatan?->level_jabatan)
                                        Level {{ $riwayat->jabatan->level_jabatan }}
                                    @else
                                        -
                                    @endif

                                </p>

                            </td>


                            {{-- JENIS PERUBAHAN --}}
                            <td class="px-4 py-4">

                                <span class="inline-flex items-center
                                             justify-center whitespace-nowrap
                                             rounded-md border
                                             px-3 py-1.5
                                             text-xs font-semibold
                                             {{ $warnaJenis }}">

                                    {{ $namaJenis }}

                                </span>

                            </td>


                            {{-- TANGGAL MULAI --}}
                            <td class="whitespace-nowrap
                                       px-4 py-4 text-gray-700">

                                {{ $riwayat->tanggal_mulai
                                    ? \Carbon\Carbon::parse($riwayat->tanggal_mulai)->format('d M Y')
                                    : '-' }}

                            </td>


                            {{-- TANGGAL SELESAI --}}
                            <td class="whitespace-nowrap
                                       px-4 py-4">

                                @if ($riwayat->tanggal_selesai)

                                    <span class="text-gray-700">

                                        {{ \Carbon\Carbon::parse(
                                            $riwayat->tanggal_selesai
                                        )->format('d M Y') }}

                                    </span>

                                @else

                                    <span class="inline-flex items-center
                                                 gap-2 text-xs font-semibold
                                                 text-[#4c7b58]">

                                        <span class="h-2 w-2 rounded-full
                                                     bg-[#4c7b58]"></span>

                                        Masih Aktif

                                    </span>

                                @endif

                            </td>


                            {{-- NILAI ASSESSMENT --}}
                            <td class="px-4 py-4 text-center">

                                @if ($riwayat->hasilAssessment)

                                    <span class="font-bold text-[#304c3f]">

                                        {{ number_format(
                                            $riwayat->hasilAssessment->nilai_akhir,
                                            2
                                        ) }}

                                    </span>

                                @else

                                    <span class="text-gray-400">
                                        -
                                    </span>

                                @endif

                            </td>


                            {{-- KETERANGAN --}}
                            <td class="px-4 py-4 text-gray-600">

                                {{ $riwayat->keterangan ?: '-' }}

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7"
                                class="px-6 py-12 text-center">

                                <div class="flex flex-col
                                            items-center gap-2">

                                    <div class="flex h-12 w-12
                                                items-center justify-center
                                                rounded-xl bg-[#f4f6f3]">

                                        <svg class="h-6 w-6 text-gray-400"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="1.5"
                                             viewBox="0 0 24 24">

                                            <rect x="5" y="4"
                                                  width="14" height="17"
                                                  rx="2"/>

                                            <path d="M9 9h6M9 13h6M9 17h4"/>

                                        </svg>

                                    </div>

                                    <p class="text-sm font-semibold
                                              text-gray-600">
                                        Belum ada riwayat jabatan.
                                    </p>

                                    <p class="text-xs text-gray-400">
                                        Perubahan jabatan akan ditampilkan di sini.
                                    </p>

                                </div>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>


    {{-- ============================================== --}}
    {{-- RIWAYAT SKILL --}}
    {{-- ============================================== --}}

    <section class="w-full min-w-0 overflow-hidden
                    rounded-xl border border-[#e0e5df]
                    bg-white">

        <div class="p-5 pb-4">

            {{-- JUDUL SECTION --}}
            <div class="mb-3 overflow-hidden rounded-lg
                        bg-[#f4f6f3]">

                <div class="rounded-l-md border-l-[5px]
                            border-[#4c7b58] px-5 py-3">

                    <h2 class="text-base font-bold text-[#304c3f]">
                        Riwayat Skill
                    </h2>

                </div>

            </div>

            <p class="text-sm text-gray-500">
                Perkembangan keterampilan dan peningkatan level
                kompetensi karyawan.
            </p>

        </div>


        {{-- TABEL RIWAYAT SKILL --}}
        <div class="w-full min-w-0 overflow-x-auto">

            <table class="w-full min-w-[1050px]
                          border-collapse text-sm">

                <thead class="bg-[#f4f6f3]">

                    <tr class="text-xs uppercase
                               tracking-wide text-[#304c3f]">

                        <th class="px-4 py-4 text-left font-bold">
                            No
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            Skill
                        </th>

                        <th class="px-4 py-4 text-center font-bold">
                            Level Sebelum
                        </th>

                        <th class="px-4 py-4 text-center font-bold">
                            Level Sesudah
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            Tanggal Perubahan
                        </th>

                        <th class="px-4 py-4 text-center font-bold">
                            Nilai Assessment
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            Keterangan
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-[#e8ece8]">

                    @forelse ($riwayatSkills as $riwayat)

                        <tr class="transition-colors
                                   hover:bg-[#fafbf9]">

                            {{-- NOMOR --}}
                            <td class="whitespace-nowrap
                                       px-4 py-4 text-gray-600">

                                {{ $loop->iteration }}

                            </td>


                            {{-- SKILL --}}
                            <td class="px-4 py-4">

                                <p class="font-semibold text-gray-800">
                                    {{ $riwayat->skill?->nama_skill ?? '-' }}
                                </p>

                            </td>


                            {{-- LEVEL SEBELUM --}}
                            <td class="px-4 py-4 text-center">

                                <span class="inline-flex items-center
                                             justify-center rounded-md
                                             border border-gray-200
                                             bg-gray-100 px-3 py-1.5
                                             text-xs font-semibold
                                             text-gray-600">

                                    Level {{ $riwayat->level_sebelum }}

                                </span>

                            </td>


                            {{-- LEVEL SESUDAH --}}
                            <td class="px-4 py-4 text-center">

                                <span class="inline-flex items-center
                                             justify-center rounded-md
                                             border border-[#d3e3d6]
                                             bg-[#e8f1ea] px-3 py-1.5
                                             text-xs font-semibold
                                             text-[#304c3f]">

                                    Level {{ $riwayat->level_sesudah }}

                                </span>

                            </td>


                            {{-- TANGGAL PERUBAHAN --}}
                            <td class="whitespace-nowrap
                                       px-4 py-4 text-gray-700">

                                {{ $riwayat->tanggal_perubahan
                                    ? \Carbon\Carbon::parse($riwayat->tanggal_perubahan)->format('d M Y')
                                    : '-' }}

                            </td>


                            {{-- NILAI ASSESSMENT --}}
                            <td class="px-4 py-4 text-center">

                                @if ($riwayat->hasilAssessment)

                                    <span class="font-bold text-[#304c3f]">

                                        {{ number_format(
                                            $riwayat->hasilAssessment->nilai_akhir,
                                            2
                                        ) }}

                                    </span>

                                @else

                                    <span class="text-gray-400">
                                        -
                                    </span>

                                @endif

                            </td>


                            {{-- KETERANGAN --}}
                            <td class="px-4 py-4 text-gray-600">

                                {{ $riwayat->keterangan ?: '-' }}

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7"
                                class="px-6 py-12 text-center">

                                <div class="flex flex-col
                                            items-center gap-2">

                                    <div class="flex h-12 w-12
                                                items-center justify-center
                                                rounded-xl bg-[#f4f6f3]">

                                        <svg class="h-6 w-6 text-gray-400"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="1.5"
                                             viewBox="0 0 24 24">

                                            <rect x="5" y="4"
                                                  width="14" height="17"
                                                  rx="2"/>

                                            <path d="M9 9h6M9 13h6M9 17h4"/>

                                        </svg>

                                    </div>

                                    <p class="text-sm font-semibold
                                              text-gray-600">
                                        Belum ada riwayat skill.
                                    </p>

                                    <p class="text-xs text-gray-400">
                                        Perkembangan skill akan ditampilkan di sini.
                                    </p>

                                </div>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>


    {{-- ============================================== --}}
    {{-- TOMBOL KEMBALI BAWAH --}}
    {{-- ============================================== --}}

    <div class="flex justify-end">

        <a href="{{ route('hrd.riwayat.index') }}"
           class="inline-flex h-10 items-center justify-center
                  gap-2 rounded-lg border border-[#dfe5df]
                  bg-white px-5 text-sm font-semibold
                  text-[#304c3f] transition-colors
                  hover:bg-[#f4f6f3]">

            <svg class="h-4 w-4"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 viewBox="0 0 24 24">

                <path d="M15 18l-6-6 6-6"/>

            </svg>

            Kembali ke Riwayat Karyawan

        </a>

    </div>

</div>

@endsection
