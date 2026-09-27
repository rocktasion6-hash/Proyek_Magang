@extends('layouts.hrd')

@section('title', 'Blockchain Explorer')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">
                Blockchain Explorer
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Memantau block dan memverifikasi integritas blockchain sistem.
            </p>
        </div>

        <form
            action="{{ route('hrd.blockchain.verify') }}"
            method="POST"
        >
            @csrf

            <button
                type="submit"
                class="inline-flex items-center gap-2 px-5 py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold text-sm transition"
            >
                <svg
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 12c0 5.591 3.824 10.291 9 11.622C17.176 22.291 21 17.591 21 12c0-1.032-.13-2.034-.373-2.984"
                    />
                </svg>

                Verifikasi Blockchain
            </button>
        </form>
    </div>

    <form
        action="{{ route('hrd.blockchain.verify-data') }}"
        method="POST"
    >
    @csrf

    <button
        type="submit"
        class="inline-flex items-center gap-2 px-5 py-3 bg-white hover:bg-slate-50 border border-slate-200 text-slate-800 rounded-xl font-bold text-sm transition"
    >
        <svg
            class="w-5 h-5"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 12c0 5.591 3.824 10.291 9 11.622C17.176 22.291 21 17.591 21 12c0-1.032-.13-2.034-.373-2.984"
            />
            </svg>

            Verifikasi Data
        </button>
    </form>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <p class="text-xs uppercase tracking-widest font-bold text-slate-400">
                Total Block
            </p>

            <p class="text-3xl font-black text-slate-900 mt-2">
                {{ $totalBlocks }}
            </p>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <p class="text-xs uppercase tracking-widest font-bold text-slate-400">
                Genesis Block
            </p>

            <p class="text-3xl font-black text-slate-900 mt-2">
                {{ $genesisBlock?->block_number ?? '-' }}
            </p>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <p class="text-xs uppercase tracking-widest font-bold text-slate-400">
                Block Terakhir
            </p>

            <p class="text-3xl font-black text-slate-900 mt-2">
                {{ $lastBlock?->block_number ?? '-' }}
            </p>
        </div>

    </div>

    {{-- Status Blockchain --}}
    @if($verification['valid'])
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5">
            <div class="flex items-start gap-3">

                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <svg
                        class="w-5 h-5 text-emerald-600"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7"
                        />
                    </svg>
                </div>

                <div>
                    <h3 class="font-black text-emerald-800">
                        Blockchain Valid
                    </h3>

                    <p class="text-sm text-emerald-700 mt-1">
                        {{ $verification['message'] }}
                    </p>
                </div>

            </div>
        </div>
    @else
        <div class="bg-red-50 border border-red-200 rounded-2xl p-5">
            <div class="flex items-start gap-3">

                <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                    <svg
                        class="w-5 h-5 text-red-600"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </div>

                <div>
                    <h3 class="font-black text-red-800">
                        Blockchain Tidak Valid
                    </h3>

                    <p class="text-sm text-red-700 mt-1">
                        {{ $verification['message'] }}
                    </p>

                    @if($verification['invalid_block'])
                        <p class="text-sm font-bold text-red-800 mt-2">
                            Block bermasalah:
                            #{{ $verification['invalid_block'] }}
                        </p>
                    @endif
                </div>

            </div>
        </div>
    @endif

    {{-- Filter --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">

        <form
            action="{{ route('hrd.blockchain.index') }}"
            method="GET"
            class="grid grid-cols-1 md:grid-cols-4 gap-4"
        >

            {{-- Search --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Cari Block
                </label>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Nomor block atau entity ID..."
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-slate-300 focus:border-slate-400"
                >
            </div>

            {{-- Entity --}}
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Jenis Data
                </label>

                <select
                    name="entity_type"
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-slate-300 focus:border-slate-400"
                >
                    <option value="">
                        Semua Jenis
                    </option>

                    @foreach($entityTypes as $type)
                        <option
                            value="{{ $type }}"
                            @selected(request('entity_type') === $type)
                        >
                            {{ ucwords(str_replace('_', ' ', $type)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Button --}}
            <div class="flex items-end gap-2">

                <button
                    type="submit"
                    class="flex-1 px-4 py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-bold transition"
                >
                    Filter
                </button>

                <a
                    href="{{ route('hrd.blockchain.index') }}"
                    class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold transition"
                >
                    Reset
                </a>

            </div>

        </form>
    </div>

    {{-- Blockchain Table --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

        <div class="p-5 border-b border-slate-200">
            <h2 class="font-black text-slate-900">
                Daftar Block
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Setiap block terhubung dengan hash block sebelumnya.
            </p>
        </div>

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>

                        <th class="px-5 py-4 text-left font-black text-slate-600">
                            Block
                        </th>

                        <th class="px-5 py-4 text-left font-black text-slate-600">
                            Jenis Data
                        </th>

                        <th class="px-5 py-4 text-left font-black text-slate-600">
                            Entity ID
                        </th>

                        <th class="px-5 py-4 text-left font-black text-slate-600">
                            Data Hash
                        </th>

                        <th class="px-5 py-4 text-left font-black text-slate-600">
                            Previous Hash
                        </th>

                        <th class="px-5 py-4 text-left font-black text-slate-600">
                            Block Hash
                        </th>

                        <th class="px-5 py-4 text-left font-black text-slate-600">
                            Waktu
                        </th>

                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse($blocks as $block)

                        <tr class="hover:bg-slate-50 transition">

                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-lg bg-slate-100 text-slate-800 font-black">
                                    #{{ $block->block_number }}
                                </span>
                            </td>

                            <td class="px-5 py-4">

                                @php
                                    $entityLabel = match ($block->entity_type) {
                                        'genesis' => 'Genesis Block',
                                        'hasil_assessment' => 'Hasil Assessment',
                                        'pengajuan_pengembangan' => 'Pengajuan Pengembangan',
                                        'keputusan_kenaikan_jabatan' => 'Keputusan Kenaikan Jabatan',
                                        'keputusan_pemindahan_jabatan' => 'Keputusan Pemindahan Jabatan',
                                        'keputusan_peningkatan_skill' => 'Keputusan Peningkatan Skill',
                                        default => ucwords(str_replace('_', ' ', $block->entity_type)),
                                    };
                                @endphp

                                <span class="font-bold text-slate-800">
                                    {{ $entityLabel }}
                                </span>

                            </td>

                            <td class="px-5 py-4 text-slate-600 font-semibold">
                                {{ $block->entity_id }}
                            </td>

                            <td class="px-5 py-4">

                                <code class="text-xs text-slate-600">
                                    {{ \Illuminate\Support\Str::limit($block->data_hash, 20) }}
                                </code>

                            </td>

                            <td class="px-5 py-4">

                                @if($block->previous_hash)
                                    <code class="text-xs text-slate-600">
                                        {{ \Illuminate\Support\Str::limit($block->previous_hash, 20) }}
                                    </code>
                                @else
                                    <span class="text-xs text-slate-400 font-semibold">
                                        NULL
                                    </span>
                                @endif

                            </td>

                            <td class="px-5 py-4">

                                <code class="text-xs text-slate-600">
                                    {{ \Illuminate\Support\Str::limit($block->block_hash, 20) }}
                                </code>

                            </td>

                            <td class="px-5 py-4 text-xs text-slate-500 whitespace-nowrap">
                                {{ $block->created_at?->format('d-m-Y H:i:s') }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="7"
                                class="px-5 py-12 text-center text-slate-400"
                            >
                                Belum ada block blockchain.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        @if($blocks->hasPages())
            <div class="p-5 border-t border-slate-200">
                {{ $blocks->links() }}
            </div>
        @endif

    </div>

</div>

@endsection