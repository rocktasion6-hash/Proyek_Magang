@extends('layouts.hrd')

@section('title', 'Assessment')
@section('page_title', 'Assessment')

@section('content')
<div class="w-full max-w-7xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-[#e8f1ea] flex-shrink-0 flex items-center justify-center">
                    <svg class="w-6 h-6 text-[#2c4a3e]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="5" y="4" width="14" height="17" rx="2" />
                        <path d="M9 10h6M9 14h6M9 18h4" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-[#4a7c59]">Manajemen Assessment</p>
                    <h1 class="text-2xl font-bold text-[#2c4a3e] mt-1">Assessment</h1>
                    <p class="text-sm text-gray-500 mt-1">Kelola assessment dan peserta ujian karyawan.</p>
                </div>
            </div>
            <a href="{{ route('hrd.assessment.create') }}"
               class="inline-flex items-center justify-center gap-2 bg-[#2c4a3e] hover:bg-[#4a7c59] text-white px-5 py-3 rounded-xl text-sm font-semibold transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Buat Assessment
            </a>
        </div>
    </div>

        @php
            $kategoriAssessment = [
                'semua' => 'Semua',
                'pemindahan_jabatan' => 'Pemindahan Jabatan',
                'peningkatan_skill' => 'Peningkatan Skill',
                'kenaikan_jabatan' => 'Kenaikan Jabatan',
            ];
        @endphp

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 pt-6 pb-2">
                <h2 class="text-lg font-bold text-[#2c4a3e]">Daftar Assessment</h2>
                <p class="text-sm text-gray-500 mt-1">Pilih kategori untuk melihat assessment yang tersedia.</p>
            </div>

        <nav
            aria-label="Kategori assessment"
            class="flex gap-2 overflow-x-auto border-b border-gray-100 px-5 pt-3"
        >
            @foreach ($kategoriAssessment as $nilaiKategori => $labelKategori)
                <a
                    href="{{ route('hrd.assessment.index', ['kategori' => $nilaiKategori]) }}"
                    @if ($kategori === $nilaiKategori) aria-current="page" @endif
                    class="shrink-0 border-b-2 px-4 py-3 text-sm font-medium {{ $kategori === $nilaiKategori ? 'border-[#2c4a3e] text-[#2c4a3e] font-semibold' : 'border-transparent text-gray-500 hover:border-[#a4c4ad] hover:text-[#2c4a3e]' }}"
                >
                    {{ $labelKategori }}
                </a>
            @endforeach
        </nav>

        <div class="overflow-hidden">

            <div class="overflow-x-auto px-5 pb-3">

                <table class="w-full min-w-[1120px] text-sm border-separate border-spacing-0">

                    <thead class="bg-[#e8f1ea] text-[#2c4a3e]">

                        <tr>

                            <th class="px-4 py-4 text-left text-xs font-bold tracking-wide">
                                #
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-bold tracking-wide">
                                Assessment
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-bold tracking-wide">
                                Jabatan
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-bold tracking-wide">
                                Departemen
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-bold tracking-wide">
                                Standar
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-bold tracking-wide">
                                Durasi
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-bold tracking-wide">
                                Peserta
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-bold tracking-wide">
                                Status
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-bold tracking-wide">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse ($assessments as $index => $assessment)

                            <tr class="hover:bg-[#f6faf7] transition-colors">

                                <td class="px-4 py-4">
                                    {{ $assessments->firstItem() + $index }}
                                </td>


                                <td class="px-4 py-4">

                                    <p class="font-semibold text-gray-800">
                                        {{ $assessment->nama_assessment }}
                                    </p>

                                    <p class="text-xs text-gray-500 mt-1">
                                        Bank soal: {{ $assessment->soalUpload?->nama_file_tampilan ?? '-' }}
                                    </p>

                                </td>


                                <td class="px-4 py-4 text-gray-600">
                                    {{ $assessment->jabatan?->nama_jabatan ?? '-' }}
                                </td>

                                <td class="px-4 py-4 text-gray-600">
                                    {{ $assessment->departemen?->nama_departemen ?? '-' }}
                                </td>


                                <td class="px-4 py-4 text-gray-600">
                                    {{ number_format($assessment->standar_nilai, 0) }}
                                </td>


                                <td class="px-4 py-4 text-gray-600">
                                    {{ $assessment->durasi }} menit
                                </td>


                                <td class="px-4 py-4 text-gray-600">
                                    {{ $assessment->peserta_count }} orang
                                </td>


                                <td class="px-4 py-4">

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
                                                class="inline-flex items-center gap-2 rounded-full focus:outline-none focus-visible:ring-2 focus-visible:ring-[#4a7c59] focus-visible:ring-offset-2"
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

                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-[#e8f1ea] text-[#2c4a3e]">
                                            Selesai
                                        </span>

                                    @else

                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                            Dibatalkan
                                        </span>

                                    @endif

                                </td>


                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a
                                            href="{{ route('hrd.assessment.show', $assessment) }}"
                                            class="inline-flex items-center rounded-lg border border-[#c5ddcc] px-3 py-1.5 text-xs font-semibold text-[#2c4a3e] hover:bg-[#e8f1ea] transition-colors"
                                        >
                                            Detail
                                        </a>

                                        <a
                                            href="{{ route('hrd.assessment.edit', $assessment) }}"
                                            class="inline-flex items-center rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-100 transition-colors"
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
                                                class="inline-flex items-center rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-50 transition-colors"
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

                <div class="px-5 py-4 border-t border-gray-100">

                    {{ $assessments->links() }}

                </div>

            @endif

        </div>
        </div>

</div>
@endsection
