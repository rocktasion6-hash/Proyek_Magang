@extends('layouts.hrd')
@section('title', 'Detail Assessment')
@section('page_title', 'Detail Assessment')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-7 space-y-6">
    {{-- Navigasi dan hero --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('hrd.assessment.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-[#2c4a3e] transition-colors">
            <span aria-hidden="true">←</span> Kembali ke Daftar Assessment
        </a>
        <a href="{{ route('hrd.assessment.edit', $assessment) }}" class="inline-flex items-center gap-2 rounded-xl bg-[#2c4a3e] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#4a7c59] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L9 17l-4 1 1-4 10.5-10.5Z"/></svg>
            Edit Assessment
        </a>
    </div>

    <section class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 sm:p-7">
        <div class="flex items-start gap-4">
            <div class="w-14 h-14 bg-[#e8f1ea] rounded-2xl flex items-center justify-center shrink-0">
                <svg class="w-7 h-7 text-[#2c4a3e]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4h6M9 10h6M9 14h6M9 18h4"/></svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-bold tracking-widest uppercase text-[#4a7c59]">Manajemen Assessment / Detail</p>
                <h1 class="text-xl sm:text-2xl font-bold text-[#2c4a3e] mt-1 break-words">{{ $assessment->nama_assessment }}</h1>
                <p class="text-sm text-gray-500 mt-2">Lihat informasi, komposisi soal, dan peserta assessment.</p>
                <span class="inline-flex mt-3 px-3 py-1 rounded-full text-xs font-semibold {{ $assessment->status === 'aktif' ? 'bg-green-100 text-green-700' : ($assessment->status === 'draft' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700') }}">
                    {{ $assessment->status === 'draft' ? 'Tidak Aktif' : ucfirst(str_replace('_', ' ', $assessment->status)) }}
                </span>
            </div>
        </div>
    </section>

    {{-- Informasi utama --}}
    <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <span class="w-1 h-7 bg-[#4a7c59] rounded-full"></span>
            <div><h2 class="text-lg font-bold text-[#2c4a3e]">Informasi Assessment</h2><p class="text-xs text-gray-500 mt-0.5">Ringkasan pengaturan ujian</p></div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5 p-6">
            @foreach ([
                ['Departemen', $assessment->departemen?->nama_departemen ?? '-'],
                ['Jabatan', $assessment->jabatan?->nama_jabatan ?? '-'],
                ['Standar Nilai', number_format($assessment->standar_nilai, 0)],
                ['Durasi Ujian', $assessment->durasi . ' menit'],
                ['Tanggal Mulai', $assessment->tanggal_mulai?->format('d M Y H:i') ?? '-'],
                ['Tanggal Selesai', $assessment->tanggal_selesai?->format('d M Y H:i') ?? '-'],
            ] as [$label, $value])
                <div class="bg-[#f8faf8] border border-[#edf1ed] rounded-xl p-4">
                    <p class="text-xs font-semibold text-gray-500">{{ $label }}</p>
                    <p class="font-bold text-gray-800 mt-2 break-words">{{ $value }}</p>
                </div>
            @endforeach
            <div class="sm:col-span-2 lg:col-span-3 bg-[#f8faf8] border border-[#edf1ed] rounded-xl p-4">
                <p class="text-xs font-semibold text-gray-500">File Bank Soal</p>
                <p class="font-bold text-gray-800 mt-2 break-words">{{ $assessment->soalUpload?->nama_file_tampilan ?? '-' }}</p>
                <p class="text-xs text-gray-500 mt-1">Kumpulan soal: Level 1 sampai Level {{ $assessment->jabatan?->level_jabatan ?? '-' }}</p>
            </div>
        </div>
    </section>

    {{-- Konfigurasi soal --}}
    @php
        $labelProsedur = [
            'pengoperasian_jabatan' => 'Prosedur Pengoperasian Jabatan',
            'keselamatan_kerja' => 'Prosedur Pengoperasian Keselamatan',
            'peraturan_perusahaan' => 'Peraturan Perusahaan',
        ];
        $labelTipeSoal = [
            'pilihan_ganda' => 'Pilihan Ganda / Pilihan Tunggal',
            'multi_jawaban' => 'Multi Jawaban',
            'benar_salah' => 'Benar / Salah',
        ];
    @endphp
    <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3"><span class="w-1 h-7 bg-[#4a7c59] rounded-full"></span><div><h2 class="text-lg font-bold text-[#2c4a3e]">Konfigurasi Jumlah Soal</h2><p class="text-xs text-gray-500 mt-0.5">Pembagian berdasarkan level, prosedur, dan tipe soal</p></div></div>
            <span class="text-xs font-semibold text-[#2c4a3e] bg-[#e8f1ea] rounded-full px-3 py-1.5">{{ $assessment->konfigurasiSoals->sum('jumlah_soal') }} soal dikonfigurasi</span>
        </div>
        <div class="p-5 sm:p-6"><div class="border border-gray-200 rounded-xl overflow-hidden"><div class="overflow-x-auto"><table class="w-full min-w-[650px] text-sm">
            <thead class="bg-[#e8f1ea] text-[#2c4a3e]"><tr><th class="px-4 py-3.5 text-left font-bold">Level / SOP</th><th class="px-4 py-3.5 text-left font-bold">Prosedur</th><th class="px-4 py-3.5 text-left font-bold">Tipe Soal</th><th class="px-4 py-3.5 text-right font-bold">Jumlah</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($assessment->konfigurasiSoals as $konfigurasi)
                    <tr class="hover:bg-[#f7faf8]"><td class="px-4 py-4 text-gray-700">{{ $konfigurasi->level_soal ? 'Level '.$konfigurasi->level_soal : 'Gabungan (data lama)' }}</td><td class="px-4 py-4 text-gray-700">{{ $labelProsedur[$konfigurasi->tipe_prosedur] ?? $konfigurasi->tipe_prosedur }}</td><td class="px-4 py-4 text-gray-700">{{ $labelTipeSoal[$konfigurasi->tipe_soal] ?? $konfigurasi->tipe_soal }}</td><td class="px-4 py-4 text-right font-bold text-[#2c4a3e]">{{ $konfigurasi->jumlah_soal }}</td></tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-10 text-center text-gray-500">Tidak ada konfigurasi soal.</td></tr>
                @endforelse
            </tbody>
        </table></div></div></div>
    </section>

    {{-- Daftar soal --}}
    <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3"><span class="w-1 h-7 bg-[#4a7c59] rounded-full"></span><div><h2 class="text-lg font-bold text-[#2c4a3e]">Soal Assessment</h2><p class="text-xs text-gray-500 mt-0.5">Daftar pertanyaan yang terhubung ke assessment</p></div></div>
            <span class="text-xs font-semibold bg-[#e8f1ea] text-[#2c4a3e] px-3 py-1.5 rounded-full">{{ $assessment->soals->count() }} soal</span>
        </div>
        <div class="p-5 sm:p-6 space-y-3">
            @forelse ($assessment->soals as $index => $soal)
                <div class="flex items-start gap-4 rounded-xl border border-gray-200 bg-white p-4 hover:border-[#b7cdbb] transition-colors">
                    <span class="w-9 h-9 flex items-center justify-center shrink-0 rounded-lg bg-[#e8f1ea] text-[#2c4a3e] text-sm font-bold">{{ $index + 1 }}</span>
                    <div class="min-w-0"><p class="font-semibold text-sm text-gray-800 break-words">{{ $soal->pertanyaan }}</p><div class="flex flex-wrap items-center gap-2 mt-2"><span class="text-xs font-medium text-[#4a7c59] bg-[#f1f6f2] px-2.5 py-1 rounded-md">{{ $labelTipeSoal[$soal->tipe_soal] ?? ucwords(str_replace('_', ' ', $soal->tipe_soal)) }}</span>@if ($soal->nama_prosedur)<span class="text-xs text-gray-500">{{ $soal->nama_prosedur }}</span>@endif</div></div>
                </div>
            @empty
                <div class="text-center py-10 text-sm text-gray-500">Belum ada soal assessment.</div>
            @endforelse
        </div>
    </section>

    {{-- Peserta --}}
    <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3"><span class="w-1 h-7 bg-[#4a7c59] rounded-full"></span><div><h2 class="text-lg font-bold text-[#2c4a3e]">Peserta Assessment</h2><p class="text-xs text-gray-500 mt-0.5">Karyawan yang ditugaskan mengikuti ujian</p></div></div>
            <span class="text-xs font-semibold bg-[#e8f1ea] text-[#2c4a3e] px-3 py-1.5 rounded-full">{{ $assessment->peserta->count() }} peserta</span>
        </div>
        <div class="p-5 sm:p-6"><div class="border border-gray-200 rounded-xl overflow-hidden"><div class="overflow-x-auto"><table class="w-full min-w-[620px] text-sm">
            <thead class="bg-[#e8f1ea] text-[#2c4a3e]"><tr><th class="px-4 py-3.5 text-left font-bold">No</th><th class="px-4 py-3.5 text-left font-bold">NIK</th><th class="px-4 py-3.5 text-left font-bold">Nama Karyawan</th><th class="px-4 py-3.5 text-left font-bold">Jabatan</th><th class="px-4 py-3.5 text-left font-bold">Status</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($assessment->peserta as $index => $peserta)
                    <tr class="hover:bg-[#f7faf8]"><td class="px-4 py-4 text-gray-500">{{ $index + 1 }}</td><td class="px-4 py-4 text-gray-600">{{ $peserta->karyawan->nik }}</td><td class="px-4 py-4 font-semibold text-gray-800">{{ $peserta->karyawan->nama }}</td><td class="px-4 py-4 text-gray-600">{{ $peserta->karyawan->jabatan?->nama_jabatan ?? '-' }}</td><td class="px-4 py-4">
                    @if ($peserta->status === 'ditugaskan')<span class="inline-flex rounded-lg bg-yellow-100 text-yellow-700 text-xs font-semibold px-3 py-1.5">Ditugaskan</span>
                    @elseif ($peserta->status === 'sedang_mengerjakan')<span class="inline-flex rounded-lg bg-blue-100 text-blue-700 text-xs font-semibold px-3 py-1.5">Sedang Mengerjakan</span>
                    @elseif ($peserta->status === 'selesai')<span class="inline-flex rounded-lg bg-green-100 text-green-700 text-xs font-semibold px-3 py-1.5">Selesai</span>
                    @else <span class="inline-flex rounded-lg bg-gray-100 text-gray-700 text-xs font-semibold px-3 py-1.5">{{ ucwords(str_replace('_', ' ', $peserta->status)) }}</span>
                    @endif
                    </td></tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-gray-500">Belum ada peserta.</td></tr>
                @endforelse
            </tbody>
        </table></div></div></div>
    </section>

    {{-- Penugasan ulang --}}
    <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3"><span class="w-1 h-7 bg-[#4a7c59] rounded-full"></span><div><h2 class="text-lg font-bold text-[#2c4a3e]">Tugaskan Ulang Assessment</h2><p class="text-xs text-gray-500 mt-0.5">Berikan kesempatan ujian baru tanpa mengubah hasil sebelumnya</p></div></div>
        <form action="{{ route('hrd.assessment.tugaskan-ulang', $assessment) }}" method="POST" class="p-6">
            @csrf
            <label for="karyawan_ids" class="block text-sm font-semibold text-gray-700 mb-2">Pilih Karyawan</label>
            <select id="karyawan_ids" name="karyawan_ids[]" multiple required class="w-full min-h-[145px] rounded-xl border border-gray-300 px-3 py-2 text-sm text-gray-700 outline-none focus:border-[#4a7c59] focus:ring-2 focus:ring-green-100">
                @foreach ($karyawans as $karyawan)
                    <option value="{{ $karyawan->id }}" @selected(in_array($karyawan->id, old('karyawan_ids', [])))>{{ $karyawan->nama }} - {{ $karyawan->nik }}</option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-2">Tahan Ctrl (Windows) atau Command (Mac) untuk memilih beberapa karyawan.</p>
            @error('karyawan_ids')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
            @error('karyawan_ids.*')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
            <button type="submit" onclick="return confirm('Yakin ingin menugaskan kembali assessment ini?')" class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-[#2c4a3e] hover:bg-[#4a7c59] px-5 py-3 text-white text-sm font-semibold transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                Tugaskan Ulang
            </button>
        </form>
    </section>
</div>
@endsection
