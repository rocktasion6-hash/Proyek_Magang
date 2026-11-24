
@extends('layouts.hrd')

@section('title', 'Riwayat Karyawan')
@section('page_title', 'Riwayat Karyawan')

@section('content')

<div class="w-full min-w-0 space-y-5">

    {{-- ================================================= --}}
    {{-- HEADER HALAMAN --}}
    {{-- ================================================= --}}

    <div class="flex flex-col gap-3 sm:flex-row
                sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-[#304c3f]">
                Riwayat Karyawan
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Melihat riwayat perubahan jabatan dan perkembangan skill karyawan.
            </p>
        </div>

    </div>


    {{-- ================================================= --}}
    {{-- FILTER PENCARIAN --}}
    {{-- ================================================= --}}

    <section class="w-full min-w-0 rounded-xl
                    border border-[#e0e5df] bg-white">

        <div class="p-5">

            <form
                method="GET"
                action="{{ route('hrd.riwayat.index') }}"
                class="flex flex-col gap-3 sm:flex-row sm:items-center"
            >

                {{-- INPUT PENCARIAN --}}
                <div class="min-w-0 flex-1">

                    <label for="search"
                           class="sr-only">
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
                               bg-white px-4 text-sm text-gray-800
                               placeholder:text-gray-400
                               outline-none transition
                               focus:border-[#4c7b58]
                               focus:ring-2 focus:ring-[#dce8df]"
                    >

                </div>


                {{-- TOMBOL --}}
                <div class="flex shrink-0 items-center gap-2">

                    {{-- CARI --}}
                    <button
                        type="submit"
                        class="inline-flex h-11 min-w-[100px]
                               items-center justify-center gap-2
                               rounded-lg bg-[#4c7b58]
                               px-4 text-sm font-semibold text-white
                               transition-colors hover:bg-[#3e6749]"
                    >

                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <circle cx="11" cy="11" r="7"/>
                            <path d="m20 20-4-4"/>
                        </svg>

                        Cari

                    </button>


                    {{-- RESET --}}
                    <a
                        href="{{ route('hrd.riwayat.index') }}"
                        class="inline-flex h-11 min-w-[100px]
                               items-center justify-center
                               rounded-lg border border-[#dfe5df]
                               bg-white px-4
                               text-sm font-semibold text-[#304c3f]
                               transition-colors hover:bg-[#f4f6f3]"
                    >
                        Reset
                    </a>

                </div>

            </form>

        </div>

    </section>


    {{-- ================================================= --}}
    {{-- DAFTAR RIWAYAT KARYAWAN --}}
    {{-- ================================================= --}}

    <section class="w-full min-w-0 overflow-hidden
                    rounded-xl border border-[#e0e5df] bg-white">

        {{-- HEADER BAGIAN --}}
        <div class="border-b border-[#e0e5df] px-5 py-4">

            <div class="relative overflow-hidden rounded-lg
                        bg-[#f4f6f3]">

                {{-- AKSEN HIJAU MELENGKUNG --}}
                <span class="absolute inset-y-0 left-0
                             w-[5px] rounded-l-md
                             bg-[#4c7b58]"></span>

                <div class="px-5 py-3">

                    <h2 class="text-base font-bold text-[#304c3f]">
                        Daftar Riwayat Karyawan
                    </h2>

                </div>

            </div>

            <p class="mt-3 text-xs text-gray-500">
                Pilih karyawan untuk melihat riwayat perubahan jabatan
                dan perkembangan skill secara lengkap.
            </p>

        </div>


        {{-- TABEL --}}
        <div class="w-full min-w-0 overflow-x-auto">

            <table class="w-full min-w-[900px]
                          border-collapse text-sm">

                {{-- HEADER TABEL --}}
                <thead class="bg-[#f4f6f3]">

                    <tr class="text-xs uppercase
                               tracking-wide text-[#304c3f]">

                        <th class="px-4 py-4 text-left font-bold">
                            No
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            NIK
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            Nama Karyawan
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            Departemen
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            Jabatan Saat Ini
                        </th>

                        <th class="px-4 py-4 text-center font-bold">
                            Status
                        </th>

                        <th class="px-4 py-4 text-center font-bold">
                            Aksi
                        </th>

                    </tr>

                </thead>


                {{-- ISI TABEL --}}
                <tbody class="divide-y divide-[#e8ece8]">

                    @forelse ($karyawans as $karyawan)

                        <tr class="transition-colors hover:bg-[#fafbf9]">

                            {{-- NOMOR --}}
                            <td class="whitespace-nowrap px-4 py-4
                                       text-gray-600">

                                {{ $karyawans->firstItem() + $loop->index }}

                            </td>


                            {{-- NIK --}}
                            <td class="whitespace-nowrap px-4 py-4
                                       text-gray-700">

                                {{ $karyawan->nik }}

                            </td>


                            {{-- NAMA --}}
                            <td class="px-4 py-4">

                                <span class="font-semibold text-gray-900">
                                    {{ $karyawan->nama }}
                                </span>

                            </td>


                            {{-- DEPARTEMEN --}}
                            <td class="px-4 py-4 text-gray-700">

                                {{ $karyawan->departemen?->nama_departemen ?? '-' }}

                            </td>


                            {{-- JABATAN --}}
                            <td class="px-4 py-4 text-gray-700">

                                {{ $karyawan->jabatan?->nama_jabatan ?? '-' }}

                            </td>


                            {{-- STATUS --}}
                            <td class="px-4 py-4 text-center">

                                @if (strtolower($karyawan->status) === 'aktif')

                                    <span class="inline-flex items-center
                                                 justify-center rounded-md
                                                 bg-[#4c7b58] px-3 py-1
                                                 text-xs font-semibold text-white">

                                        Aktif

                                    </span>

                                @elseif (strtolower($karyawan->status) === 'nonaktif')

                                    <span class="inline-flex items-center
                                                 justify-center rounded-md
                                                 border border-gray-200
                                                 bg-gray-100 px-3 py-1
                                                 text-xs font-semibold
                                                 text-gray-600">

                                        Nonaktif

                                    </span>

                                @else

                                    <span class="inline-flex items-center
                                                 justify-center rounded-md
                                                 border border-[#e0e5df]
                                                 bg-[#f4f6f3] px-3 py-1
                                                 text-xs font-semibold
                                                 text-[#304c3f]">

                                        {{ ucfirst($karyawan->status) }}

                                    </span>

                                @endif

                            </td>


                            {{-- AKSI --}}
                            <td class="px-4 py-4 text-center">

                                <a
                                    href="{{ route('hrd.riwayat.show', $karyawan) }}"
                                    class="inline-flex h-9 items-center
                                           justify-center whitespace-nowrap
                                           rounded-md border border-[#d9dfda]
                                           bg-[#f4f6f3] px-4
                                           text-xs font-semibold
                                           text-[#304c3f]
                                           transition-colors
                                           hover:bg-[#e8ede7]"
                                >
                                    Lihat Riwayat
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="px-6 py-12 text-center"
                            >

                                <div class="flex flex-col items-center gap-2">

                                    <div class="flex h-12 w-12
                                                items-center justify-center
                                                rounded-xl bg-[#f4f6f3]">

                                        <svg
                                            class="h-6 w-6 text-gray-400"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                            viewBox="0 0 24 24"
                                        >
                                            <circle cx="12" cy="8" r="4"/>
                                            <path d="M4 21v-2a8 8 0 0116 0v2"/>
                                        </svg>

                                    </div>

                                    <p class="text-sm font-semibold text-gray-600">
                                        Data karyawan tidak ditemukan.
                                    </p>

                                    <p class="text-xs text-gray-400">
                                        Coba gunakan nama atau NIK yang berbeda.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if ($karyawans->hasPages())

            <div class="border-t border-[#e0e5df] px-5 py-4">

                {{ $karyawans->withQueryString()->links() }}

            </div>

        @endif

    </section>

</div>

@endsection
