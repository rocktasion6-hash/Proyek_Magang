
@extends('layouts.hrd')

@section('title', 'Manajemen Assessment')
@section('page_title', 'Manajemen Assessment')

@section('content')

@php
    $kategoriAssessment = [
        'semua' => 'Semua Assessment',
        'pemindahan_jabatan' => 'Pemindahan Jabatan',
        'peningkatan_skill' => 'Peningkatan Skill',
        'kenaikan_jabatan' => 'Kenaikan Jabatan',
    ];
@endphp

<div class="w-full">

    {{-- HEADER HALAMAN --}}
    <div class="flex flex-col sm:flex-row
                sm:items-center sm:justify-between
                gap-4 mb-5">

        <div>
            <h1 class="text-2xl font-bold text-[#304c3f]">
                Manajemen Assessment
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Mengelola data assessment dan peserta ujian karyawan.
            </p>
        </div>

        <a href="{{ route('hrd.assessment.create') }}"
           class="inline-flex items-center justify-center
                  gap-2 px-5 py-3 rounded-lg
                  bg-[#4c7b58] hover:bg-[#3e6749]
                  text-white text-sm font-semibold
                  transition-colors">

            <span class="text-lg leading-none">+</span>
            Buat Assessment

        </a>
    </div>


    


    {{-- FILTER KATEGORI --}}
    <div class="bg-white border border-[#e0e5df]
                rounded-xl px-5 py-4 mb-5">

        <div class="flex flex-col lg:flex-row
                    lg:items-center gap-3">

            <div class="flex-shrink-0">
                <label class="text-sm font-semibold text-[#304c3f]">
                    Kategori Assessment
                </label>
            </div>

            <nav aria-label="Kategori assessment"
                 class="flex flex-wrap items-center gap-2 lg:ml-auto">

                @foreach ($kategoriAssessment as $nilaiKategori => $labelKategori)

                    <a href="{{ route('hrd.assessment.index', [
                            'kategori' => $nilaiKategori
                        ]) }}"
                       @if ($kategori === $nilaiKategori)
                           aria-current="page"
                       @endif
                       class="inline-flex items-center justify-center
                              px-4 py-2 rounded-lg border text-sm
                              font-medium transition-colors
                              {{ $kategori === $nilaiKategori
                                  ? 'bg-[#4c7b58] border-[#4c7b58] text-white'
                                  : 'bg-white border-[#e0e5df] text-gray-600 hover:bg-[#f4f6f3] hover:text-[#304c3f]' }}">

                        {{ $labelKategori }}

                    </a>

                @endforeach

            </nav>
        </div>

    </div>


    {{-- TABEL ASSESSMENT --}}
    <div class="bg-white border border-[#e0e5df]
                rounded-xl overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full min-w-[1040px] text-sm">

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
                            Assessment
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            Jabatan
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            Departemen
                        </th>

                        <th class="px-4 py-4 text-center font-bold">
                            Standar
                        </th>

                        <th class="px-4 py-4 text-center font-bold">
                            Durasi
                        </th>

                        <th class="px-4 py-4 text-center font-bold">
                            Peserta
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

                    @forelse ($assessments as $index => $assessment)

                        <tr class="hover:bg-[#fafbf9]
                                   transition-colors">

                            {{-- NOMOR --}}
                            <td class="px-4 py-4 text-gray-600">
                                {{ $assessments->firstItem() + $index }}
                            </td>


                            {{-- ASSESSMENT --}}
                            <td class="px-4 py-4">

                                <p class="font-semibold text-gray-800">
                                    {{ $assessment->nama_assessment }}
                                </p>

                                <p class="text-xs text-gray-500 mt-1">
                                    Bank Soal:
                                    {{ $assessment->soalUpload?->nama_file_tampilan ?? '-' }}
                                </p>

                            </td>


                            {{-- JABATAN --}}
                            <td class="px-4 py-4 text-gray-700">
                                {{ $assessment->jabatan?->nama_jabatan ?? '-' }}
                            </td>


                            {{-- DEPARTEMEN --}}
                            <td class="px-4 py-4 text-gray-700">
                                {{ $assessment->departemen?->nama_departemen ?? '-' }}
                            </td>


                            {{-- STANDAR --}}
                            <td class="px-4 py-4 text-center
                                       font-semibold text-gray-800">
                                {{ number_format($assessment->standar_nilai, 0) }}
                            </td>


                            {{-- DURASI --}}
                            <td class="px-4 py-4 text-center text-gray-700">
                                {{ $assessment->durasi }} menit
                            </td>


                            {{-- PESERTA --}}
                            <td class="px-4 py-4 text-center text-gray-700">
                                {{ $assessment->peserta_count }} orang
                            </td>


                            {{-- STATUS --}}
                            <td class="px-4 py-4 text-center">

                                @if (in_array($assessment->status, ['aktif', 'draft'], true))

                                    @php
                                        $isAssessmentActive =
                                            $assessment->status === 'aktif';
                                    @endphp

                                    <form method="POST"
                                          action="{{ route('hrd.assessment.toggle-status', $assessment) }}">

                                        @csrf

                                        <button type="submit"
                                                role="switch"
                                                aria-checked="{{ $isAssessmentActive ? 'true' : 'false' }}"
                                                aria-label="{{ $isAssessmentActive ? 'Nonaktifkan' : 'Aktifkan' }} {{ $assessment->nama_assessment }}"
                                                class="inline-flex items-center
                                                       justify-center gap-2
                                                       focus:outline-none
                                                       focus-visible:ring-2
                                                       focus-visible:ring-[#4c7b58]
                                                       focus-visible:ring-offset-2">

                                            <span aria-hidden="true"
                                                  class="relative inline-block
                                                         w-10 h-5 rounded-full
                                                         transition-colors
                                                         {{ $isAssessmentActive
                                                             ? 'bg-[#4c7b58]'
                                                             : 'bg-gray-300' }}">

                                                <span class="absolute
                                                             top-0.5 left-0.5
                                                             w-4 h-4 bg-white
                                                             rounded-full shadow-sm
                                                             transition-transform
                                                             {{ $isAssessmentActive
                                                                 ? 'translate-x-5'
                                                                 : 'translate-x-0' }}">
                                                </span>

                                            </span>

                                            <span class="text-xs font-semibold
                                                {{ $isAssessmentActive
                                                    ? 'text-[#4c7b58]'
                                                    : 'text-gray-500' }}">

                                                {{ $isAssessmentActive
                                                    ? 'Aktif'
                                                    : 'Tidak Aktif' }}

                                            </span>

                                        </button>

                                    </form>

                                @elseif ($assessment->status === 'selesai')

                                    <span class="inline-flex px-3 py-1
                                                 rounded-md
                                                 bg-[#e9eeea]
                                                 text-[#304c3f]
                                                 text-xs font-semibold">
                                        Selesai
                                    </span>

                                @else

                                    <span class="inline-flex px-3 py-1
                                                 rounded-md
                                                 bg-red-50 text-red-700
                                                 text-xs font-semibold">
                                        Dibatalkan
                                    </span>

                                @endif

                            </td>


                            {{-- TOMBOL AKSI --}}
                            <td class="px-4 py-4">

                                <div class="flex items-center
                                            justify-center gap-2">

                                    {{-- DETAIL --}}
                                    <a href="{{ route('hrd.assessment.show', $assessment) }}"
                                       class="inline-flex items-center
                                              justify-center px-3 py-2
                                              bg-[#f4f6f3]
                                              border border-[#d9dfda]
                                              rounded-md text-xs
                                              font-semibold text-[#304c3f]
                                              hover:bg-[#e8ede7]
                                              transition-colors">
                                        Detail
                                    </a>


                                    {{-- EDIT --}}
                                    <a href="{{ route('hrd.assessment.edit', $assessment) }}"
                                       class="inline-flex items-center
                                              justify-center px-3 py-2
                                              bg-[#e6b93d]
                                              border border-[#e6b93d]
                                              rounded-md text-xs
                                              font-semibold text-gray-900
                                              hover:bg-[#d8a92c]
                                              transition-colors">
                                        Edit
                                    </a>


                                    {{-- HAPUS --}}
                                    <form method="POST"
                                          action="{{ route('hrd.assessment.destroy', $assessment) }}"
                                          onsubmit="return confirm('Hapus assessment ini? Assessment yang sudah mulai dikerjakan atau memiliki hasil tidak dapat dihapus.')">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="inline-flex items-center
                                                       justify-center px-3 py-2
                                                       bg-white border
                                                       border-red-200
                                                       rounded-md text-xs
                                                       font-semibold text-red-700
                                                       hover:bg-red-50
                                                       transition-colors">
                                            Hapus
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="9"
                                class="px-6 py-12 text-center">

                                <div class="flex flex-col
                                            items-center gap-2">

                                    <svg class="w-10 h-10 text-gray-300"
                                         fill="none"
                                         stroke="currentColor"
                                         stroke-width="1.5"
                                         viewBox="0 0 24 24">

                                        <rect x="5" y="4"
                                              width="14" height="17"
                                              rx="2"/>

                                        <path d="M9 10h6M9 14h6M9 18h4"/>

                                    </svg>

                                    <p class="text-sm font-medium
                                              text-gray-600">
                                        Belum ada assessment.
                                    </p>

                                    <p class="text-xs text-gray-400">
                                        Data assessment akan ditampilkan di sini.
                                    </p>

                                </div>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if ($assessments->hasPages())

            <div class="px-5 py-4 border-t
                        border-[#e0e5df]">

                {{ $assessments->links() }}

            </div>

        @endif

    </div>

</div>

@endsection
