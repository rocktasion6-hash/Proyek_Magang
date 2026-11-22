@extends('layouts.hrd')
@section('title', 'Bank Soal')
@section('page_title', 'Bank Soal')
@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-7">
    {{-- Pesan sukses --}}
    @if (session('success'))
        <div class="mb-6 flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-sm text-green-800">
            <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M12 22a10 10 0 110-20 10 10 0 010 20z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Header --}}
    <div class="relative mb-6 overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="pointer-events-none absolute -right-10 -bottom-20 h-48 w-72 rounded-full bg-[#e8f1ea] opacity-60"></div>
        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-[#e8f1ea] text-[#2c4a3e]">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5.5A2.5 2.5 0 016.5 3H20v17H6.5A2.5 2.5 0 014 17.5v-12zM4 17.5A2.5 2.5 0 016.5 15H20M9 8h7M9 11h5"/></svg>
                </div>
                <div>
                    <p class="mb-1 text-xs font-bold uppercase tracking-wider text-[#4a7c59]">Manajemen Assessment</p>
                    <h1 class="text-2xl font-bold tracking-tight text-[#2c4a3e]">Bank Soal</h1>
                    <p class="mt-1 text-sm text-gray-500">Kelola kumpulan soal berdasarkan departemen dan jabatan.</p>
                </div>
            </div>
            <a href="{{ route('hrd.soal.upload') }}" class="inline-flex h-11 shrink-0 items-center justify-center gap-2 rounded-xl bg-[#2c4a3e] px-5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-[#4a7c59] focus:outline-none focus:ring-2 focus:ring-[#4a7c59] focus:ring-offset-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0L7 9m5-5l5 5M4 16v3a2 2 0 002 2h12a2 2 0 002-2v-3"/></svg>
                Upload Excel
            </a>
        </div>
    </div>

    {{-- Tabel daftar bank soal --}}
    <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-2 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#e8f1ea] text-[#2c4a3e]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M8 6h13M8 12h13M8 18h13"/><circle cx="3" cy="6" r="1"/><circle cx="3" cy="12" r="1"/><circle cx="3" cy="18" r="1"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-[#2c4a3e]">Daftar Bank Soal</h2>
                    <p class="mt-0.5 text-xs text-gray-500">File soal Excel yang sudah diunggah ke sistem</p>
                </div>
            </div>
            <span class="inline-flex w-fit items-center gap-2 rounded-lg bg-[#e8f1ea] px-3 py-2 text-xs font-semibold text-[#2c4a3e]">
                <span class="h-2 w-2 rounded-full bg-[#4a7c59]"></span>
                {{ $uploads->total() }} File Bank Soal
            </span>
        </div>

        <div class="px-6 pb-5">
            <div class="overflow-hidden rounded-xl border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[990px] text-sm">
                        <thead class="bg-[#e8f1ea] text-[#2c4a3e]">
                            <tr>
                                <th scope="col" class="px-4 py-4 text-left font-bold">No</th>
                                <th scope="col" class="px-4 py-4 text-left font-bold">Nama File</th>
                                <th scope="col" class="px-4 py-4 text-left font-bold">Departemen</th>
                                <th scope="col" class="px-4 py-4 text-left font-bold">Jabatan</th>
                                <th scope="col" class="px-4 py-4 text-center font-bold">Jumlah Soal</th>
                                <th scope="col" class="px-4 py-4 text-left font-bold">Tanggal Upload</th>
                                <th scope="col" class="px-4 py-4 text-center font-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($uploads as $upload)
                                <tr class="transition-colors hover:bg-[#f6faf7]">
                                    <td class="px-4 py-4 text-gray-500">{{ $uploads->firstItem() + $loop->index }}</td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-green-50 text-green-700">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h8"/></svg>
                                            </div>
                                            <span class="max-w-[245px] break-words font-semibold text-gray-800">{{ $upload->nama_file_tampilan }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-gray-600">{{ $upload->departemen->nama_departemen ?? 'Belum ditentukan' }}</td>
                                    <td class="px-4 py-4 text-gray-600">
                                        <span>{{ $upload->jabatan->nama_jabatan ?? '-' }}</span>
                                        @if ($upload->jabatan?->tipe_jabatan === 'operasional')
                                            <span class="mt-1 block text-xs text-gray-400">Level {{ $upload->jabatan->level_jabatan }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex min-w-10 justify-center rounded-lg bg-[#e8f1ea] px-3 py-1.5 font-bold text-[#2c4a3e]">{{ $upload->soals_count }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-gray-600">
                                        <span class="block">{{ $upload->created_at->format('d M Y') }}</span>
                                        <span class="mt-1 block text-xs text-gray-400">{{ $upload->created_at->format('H:i') }}</span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('hrd.soal.upload.show', $upload) }}" class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg border border-[#4a7c59] px-3 text-xs font-semibold text-[#2c4a3e] transition-colors hover:bg-[#2c4a3e] hover:text-white">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                                Detail
                                            </a>
                                            <form method="POST" action="{{ route('hrd.soal.upload.destroy', $upload) }}" onsubmit="return confirm('Hapus file ini beserta {{ $upload->soals_count }} soal di dalamnya?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 text-xs font-semibold text-red-700 transition-colors hover:bg-red-100">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M10 11v6m4-6v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-14 text-center">
                                        <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 text-gray-400">
                                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M4 5.5A2.5 2.5 0 016.5 3H20v17H6.5A2.5 2.5 0 014 17.5v-12zM4 17.5A2.5 2.5 0 016.5 15H20"/></svg>
                                        </div>
                                        <p class="font-semibold text-gray-700">Belum ada bank soal</p>
                                        <p class="mt-1 text-sm text-gray-500">Unggah file Excel untuk mulai menambahkan kumpulan soal.</p>
                                        <a href="{{ route('hrd.soal.upload') }}" class="mt-4 inline-flex items-center rounded-lg bg-[#2c4a3e] px-4 py-2 text-sm font-semibold text-white hover:bg-[#4a7c59]">Upload Excel</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($uploads->hasPages())
                <div class="mt-5">{{ $uploads->links() }}</div>
            @endif
        </div>
    </section>
</div>

@endsection
