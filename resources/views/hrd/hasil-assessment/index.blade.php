@extends('layouts.hrd')
@section('title', 'Hasil Penilaian')
@section('page_title', 'Hasil Penilaian')
@section('content')
    <main class="w-full max-w-7xl mx-auto px-3 sm:px-5 lg:px-6 py-5">


        @if (session('success'))

            <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 mb-6">
                {{ session('success') }}
            </div>

        @endif


        <!-- Header -->
        
<!-- Header Hasil Penilaian -->
<div class="bg-white border border-gray-100
            rounded-2xl p-6 mb-6 shadow-sm">

    <div class="flex items-center gap-4">

        <div class="w-14 h-14 bg-[#e8f1ea]
                    rounded-xl flex items-center
                    justify-center flex-shrink-0">

            <svg class="w-7 h-7 text-[#2c4a3e]"
                 fill="none" stroke="currentColor"
                 stroke-width="1.8" viewBox="0 0 24 24">
                <rect x="5" y="4" width="14"
                      height="17" rx="2"/>
                <path d="M9 4.5h6M9 11l2 2 4-4M9 17h6"/>
            </svg>

        </div>

        <div>
            <p class="text-xs font-bold uppercase
                      tracking-wider text-[#4a7c59] mb-1">
                Manajemen Assessment
            </p>

            <h1 class="text-2xl font-bold text-[#2c4a3e]">
                Hasil Penilaian
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Pantau hasil ujian, nilai, dan
                status kelulusan karyawan.
            </p>
        </div>

    </div>
</div>


{{-- Statistik Hasil Penilaian --}}
@php
    $dataHalaman = $hasilAssessments->getCollection();

    $totalHasil = $dataHalaman->count();

    $totalLulus = $dataHalaman
        ->where('status', 'lulus')
        ->count();

    $totalTidakLulus = $dataHalaman
        ->where('status', 'tidak_lulus')
        ->count();
@endphp


{{-- Kartu Statistik Modern Corporate --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

    {{-- Total Hasil --}}
    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-[#e8f1ea] rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-7 h-7 text-[#2c4a3e]" fill="none"
                     stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <rect x="5" y="4" width="14" height="17" rx="2"/>
                    <path d="M9 9h6M9 13h6M9 17h4"/>
                </svg>
            </div>

            <div>
                <p class="text-3xl font-bold text-[#2c4a3e]">
                    {{ $totalHasil }}
                </p>
                <p class="text-sm text-gray-500 mt-1">
                    Hasil Ditampilkan
                </p>
            </div>
        </div>
    </div>

    {{-- Lulus --}}
    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-7 h-7 text-green-700" fill="none"
                     stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="m8 12 3 3 5-6"/>
                </svg>
            </div>

            <div>
                <p class="text-3xl font-bold text-green-700">
                    {{ $totalLulus }}
                </p>
                <p class="text-sm text-gray-500 mt-1">
                    Lulus
                </p>
            </div>
        </div>
    </div>

    {{-- Tidak Lulus --}}
    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-7 h-7 text-red-700" fill="none"
                     stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="m9 9 6 6m0-6-6 6"/>
                </svg>
            </div>

            <div>
                <p class="text-3xl font-bold text-red-700">
                    {{ $totalTidakLulus }}
                </p>
                <p class="text-sm text-gray-500 mt-1">
                    Tidak Lulus
                </p>
            </div>
        </div>
    </div>

</div>


        {{-- Panel Daftar Hasil Assessment: filter dan tabel dalam satu kartu --}}
        <section class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-6">
            <div class="px-5 sm:px-6 pt-6 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#e8f1ea] rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-[#2c4a3e]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" d="M8 6h13M8 12h13M8 18h13" />
                            <circle cx="3" cy="6" r="1"/><circle cx="3" cy="12" r="1"/><circle cx="3" cy="18" r="1"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-[#2c4a3e]">Daftar Hasil Assessment</h2>
                        <p class="text-xs text-gray-500 mt-1">Data hasil ujian dan kelulusan karyawan</p>
                    </div>
                </div>
            </div>

            <div class="px-5 sm:px-6 pb-5">
                <form method="GET" action="{{ route('hrd.hasil-assessment.index') }}">
                    <div class="flex flex-col lg:flex-row lg:items-end gap-3">
                        <div class="flex-1 min-w-0">
                            <label for="hasil-search" class="block text-xs font-semibold text-gray-600 mb-2">Cari Karyawan</label>
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle cx="11" cy="11" r="7"/><path d="m16 16 4 4"/>
                                </svg>
                                <input id="hasil-search" type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Cari nama atau NIK karyawan..."
                                    class="w-full h-11 pl-11 pr-4 rounded-lg border border-gray-300 text-sm focus:border-[#4a7c59] focus:ring-2 focus:ring-green-100 outline-none transition" />
                            </div>
                        </div>
                        <div class="w-full lg:w-56">
                            <label for="hasil-status" class="block text-xs font-semibold text-gray-600 mb-2">Status Kelulusan</label>
                            <select id="hasil-status" name="status" class="w-full h-11 px-3 border border-gray-300 rounded-lg bg-white text-sm focus:border-[#4a7c59] focus:ring-2 focus:ring-green-100 outline-none">
                                <option value="">Semua Status</option>
                                <option value="lulus" @selected(request('status') === 'lulus')>Lulus</option>
                                <option value="tidak_lulus" @selected(request('status') === 'tidak_lulus')>Tidak Lulus</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="submit" class="h-11 px-5 rounded-lg bg-[#2c4a3e] hover:bg-[#4a7c59] text-white text-sm font-semibold inline-flex items-center justify-center gap-2 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16l-6 7v6l-4 2v-8z"/></svg>
                                Terapkan Filter
                            </button>
                            <a href="{{ route('hrd.hasil-assessment.index') }}" class="h-11 px-4 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-gray-600 text-sm font-semibold transition-colors">Reset</a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="px-5 sm:px-6 pb-5">
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[1050px] text-sm">
                            <thead class="bg-[#e8f1ea] text-[#2c4a3e]">
                                <tr>
                                    <th class="px-4 py-4 text-left font-bold">No</th>
                                    <th class="px-4 py-4 text-left font-bold">Karyawan</th>
                                    <th class="px-4 py-4 text-left font-bold">Assessment</th>
                                    <th class="px-4 py-4 text-left font-bold">Jabatan</th>
                                    <th class="px-4 py-4 text-center font-bold">Nilai</th>
                                    <th class="px-4 py-4 text-center font-bold">Standar</th>
                                    <th class="px-4 py-4 text-center font-bold">Status</th>
                                    <th class="px-4 py-4 text-left font-bold">Tanggal Ujian</th>
                                    <th class="px-4 py-4 text-center font-bold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($hasilAssessments as $index => $hasil)
                                    @php
                                        $peserta = $hasil->assessmentPeserta;
                                    @endphp
                                    <tr class="hover:bg-[#f6faf7] transition-colors">
                                        <td class="px-4 py-4 text-gray-500">{{ $hasilAssessments->firstItem() + $index }}</td>
                                        <td class="px-4 py-4">
                                            <p class="font-bold text-gray-800">{{ $peserta->karyawan->nama }}</p>
                                            <p class="text-xs text-gray-500 mt-1">{{ $peserta->karyawan->nik }}</p>
                                        </td>
                                        <td class="px-4 py-4"><p class="font-medium text-gray-800 leading-relaxed">{{ $peserta->assessment->nama_assessment }}</p></td>
                                        <td class="px-4 py-4 text-gray-600">{{ $peserta->assessment->jabatan?->nama_jabatan ?? '-' }}</td>
                                        <td class="px-4 py-4 text-center"><span class="font-bold text-[#2c4a3e]">{{ number_format($hasil->nilai_akhir, 2) }}</span></td>
                                        <td class="px-4 py-4 text-center text-gray-600">{{ number_format($hasil->standar_nilai, 2) }}</td>
                                        <td class="px-4 py-4 text-center">
                                            @if ($hasil->status === 'lulus')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-100 text-green-700 text-xs font-semibold">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span>Lulus
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-100 text-red-700 text-xs font-semibold">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>Tidak Lulus
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-gray-600">
                                            {{ $hasil->tanggal_ujian?->format('d M Y') ?? '-' }}
                                            <p class="text-xs text-gray-400 mt-1">{{ $hasil->tanggal_ujian?->format('H:i') }}</p>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <a href="{{ route('hrd.hasil-assessment.show', $hasil) }}"
                                               class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg border border-[#4a7c59] text-[#2c4a3e] hover:bg-[#2c4a3e] hover:text-white text-xs font-semibold transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="9" class="px-6 py-12 text-center text-gray-500">Belum ada hasil assessment.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($hasilAssessments->hasPages())
                    <div class="mt-5">{{ $hasilAssessments->links() }}</div>
                @endif
            </div>
        </section>

    </main>
@endsection
