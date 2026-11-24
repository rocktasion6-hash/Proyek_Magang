
@extends('layouts.hrd')

@section('title', 'Bank Soal')
@section('page_title', 'Bank Soal')

@section('content')

<div class="w-full">

    {{-- HEADER HALAMAN --}}
    <div class="flex flex-col sm:flex-row
                sm:items-center sm:justify-between
                gap-4 mb-5">

        <div>
            <h1 class="text-2xl font-bold text-[#304c3f]">
                Bank Soal
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Mengelola bank soal assessment berdasarkan
                departemen dan jabatan.
            </p>
        </div>

        <a href="{{ route('hrd.soal.upload') }}"
           class="inline-flex items-center justify-center
                  gap-2 px-5 py-3 rounded-lg
                  bg-[#4c7b58] hover:bg-[#3e6749]
                  text-white text-sm font-semibold
                  transition-colors">

            <svg class="w-4 h-4"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 viewBox="0 0 24 24">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M12 16V4m0 0-4 4m4-4 4 4
                         M4 16v4h16v-4"/>

            </svg>

            Upload Excel
        </a>

    </div>


    {{-- NOTIFIKASI --}}
    @if (session('success'))

        <div class="mb-4 rounded-lg border border-green-200
                    bg-green-50 px-4 py-3
                    text-sm text-green-800">

            {{ session('success') }}

        </div>

    @endif


    @if (session('error'))

        <div class="mb-4 rounded-lg border border-red-200
                    bg-red-50 px-4 py-3
                    text-sm text-red-700">

            {{ session('error') }}

        </div>

    @endif


    {{-- INFORMASI DAFTAR --}}
    <div class="bg-white border border-[#e0e5df]
                rounded-xl px-5 py-4 mb-5">

        <div class="flex flex-col sm:flex-row
                    sm:items-center sm:justify-between
                    gap-3">

            <div>
                <p class="text-sm font-semibold text-[#304c3f]">
                    Daftar Bank Soal
                </p>

                <p class="text-xs text-gray-500 mt-1">
                    File soal yang telah diunggah ke sistem.
                </p>
            </div>

            <div class="inline-flex items-center
                        gap-2 rounded-lg
                        bg-[#f4f6f3]
                        border border-[#e0e5df]
                        px-3 py-2">

                <span class="text-xs font-medium text-gray-500">
                    Total Bank Soal
                </span>

                <span class="text-sm font-bold text-[#304c3f]">
                    {{ $uploads->total() }}
                </span>

            </div>

        </div>

    </div>


    {{-- TABEL BANK SOAL --}}
    <div class="bg-white border border-[#e0e5df]
                rounded-xl overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full min-w-[920px] text-sm">

                {{-- HEADER TABEL --}}
                <thead class="bg-[#f4f6f3]
                              text-[#304c3f]
                              uppercase text-xs
                              tracking-wide">

                    <tr>

                        <th class="px-4 py-4 text-left font-bold">
                            No
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            Nama File
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            Departemen
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            Jabatan
                        </th>

                        <th class="px-4 py-4 text-center font-bold">
                            Jumlah Soal
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            Tanggal Upload
                        </th>

                        <th class="px-4 py-4 text-center font-bold">
                            Aksi
                        </th>

                    </tr>

                </thead>


                {{-- ISI TABEL --}}
                <tbody class="divide-y divide-[#e8ece8]">

                    @forelse ($uploads as $upload)

                        <tr class="hover:bg-[#fafbf9]
                                   transition-colors">

                            {{-- NOMOR --}}
                            <td class="px-4 py-4 text-gray-600">
                                {{ $uploads->firstItem() + $loop->index }}
                            </td>


                            {{-- NAMA FILE --}}
                            <td class="px-4 py-4">

                                <div class="flex items-center gap-3">

                                    <div class="w-9 h-9
                                                flex-shrink-0
                                                rounded-lg
                                                bg-[#edf3ee]
                                                flex items-center
                                                justify-center">

                                        <svg class="w-5 h-5 text-[#4c7b58]"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="1.8"
                                             viewBox="0 0 24 24">

                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  d="M14 2H6a2 2 0 00-2 2v16
                                                     a2 2 0 002 2h12
                                                     a2 2 0 002-2V8z"/>

                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  d="M14 2v6h6M8 13h8M8 17h8"/>

                                        </svg>

                                    </div>

                                    <span class="font-semibold text-gray-800 break-all">
                                        {{ $upload->nama_file_tampilan }}
                                    </span>

                                </div>

                            </td>


                            {{-- DEPARTEMEN --}}
                            <td class="px-4 py-4 text-gray-700">

                                {{ $upload->departemen?->nama_departemen ?? 'Belum ditentukan' }}

                            </td>


                            {{-- JABATAN --}}
                            <td class="px-4 py-4 text-gray-700">

                                <p>
                                    {{ $upload->jabatan?->nama_jabatan ?? '-' }}
                                </p>

                                @if ($upload->jabatan?->tipe_jabatan === 'operasional')

                                    <p class="text-xs text-gray-500 mt-1">
                                        Level {{ $upload->jabatan->level_jabatan }}
                                    </p>

                                @endif

                            </td>


                            {{-- JUMLAH SOAL --}}
                            <td class="px-4 py-4 text-center">

                                <span class="inline-flex items-center
                                             justify-center
                                             px-3 py-1 rounded-md
                                             bg-[#e8eee9]
                                             text-[#304c3f]
                                             text-xs font-semibold">

                                    {{ $upload->soals_count }} Soal

                                </span>

                            </td>


                            {{-- TANGGAL UPLOAD --}}
                            <td class="px-4 py-4 text-gray-600">

                                {{ $upload->created_at?->format('d/m/Y H:i') ?? '-' }}

                            </td>


                            {{-- TOMBOL AKSI --}}
                            <td class="px-4 py-4">

                                <div class="flex items-center
                                            justify-center gap-2">

                                    {{-- DETAIL --}}
                                    <a href="{{ route('hrd.soal.upload.show', $upload) }}"
                                       class="inline-flex items-center
                                              justify-center
                                              px-3 py-2
                                              bg-[#f4f6f3]
                                              border border-[#d9dfda]
                                              rounded-md
                                              text-xs font-semibold
                                              text-[#304c3f]
                                              hover:bg-[#e8ede7]
                                              transition-colors">

                                        Detail

                                    </a>


                                    {{-- HAPUS --}}
                                    <form method="POST"
                                          action="{{ route('hrd.soal.upload.destroy', $upload) }}"
                                          onsubmit="return confirm('Hapus file ini beserta {{ $upload->soals_count }} soal di dalamnya?')">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="inline-flex items-center
                                                       justify-center
                                                       px-3 py-2
                                                       bg-white
                                                       border border-red-200
                                                       rounded-md
                                                       text-xs font-semibold
                                                       text-red-700
                                                       hover:bg-red-50
                                                       transition-colors">

                                            Hapus

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        {{-- KETIKA BELUM ADA DATA --}}
                        <tr>

                            <td colspan="7"
                                class="px-6 py-12 text-center">

                                <div class="flex flex-col items-center gap-3">

                                    <div class="w-12 h-12
                                                rounded-xl
                                                bg-[#f4f6f3]
                                                flex items-center
                                                justify-center">

                                        <svg class="w-7 h-7 text-gray-400"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="1.5"
                                             viewBox="0 0 24 24">

                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  d="M14 2H6a2 2 0 00-2 2v16
                                                     a2 2 0 002 2h12
                                                     a2 2 0 002-2V8z"/>

                                            <path d="M14 2v6h6M8 13h8M8 17h8"/>

                                        </svg>

                                    </div>

                                    <p class="text-sm font-medium text-gray-600">
                                        Belum ada bank soal yang diunggah.
                                    </p>

                                    <a href="{{ route('hrd.soal.upload') }}"
                                       class="text-sm font-semibold
                                              text-[#4c7b58]
                                              hover:underline">

                                        Upload bank soal sekarang

                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if ($uploads->hasPages())

            <div class="px-5 py-4 border-t border-[#e0e5df]">

                {{ $uploads->links() }}

            </div>

        @endif

    </div>

</div>

@endsection
