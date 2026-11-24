
@extends('layouts.hrd')

@section('title', 'Laporan & Analitik')
@section('page_title', 'Laporan & Analitik')

@section('content')

@php
    $statistikMaster = [
        ['label' => 'Karyawan Aktif', 'nilai' => $totalKaryawan, 'satuan' => 'orang'],
        ['label' => 'Departemen', 'nilai' => $totalDepartemen, 'satuan' => 'departemen'],
        ['label' => 'Jabatan', 'nilai' => $totalJabatan, 'satuan' => 'jabatan'],
        ['label' => 'Skill', 'nilai' => $totalSkill, 'satuan' => 'skill'],
    ];

    $statistikAssessment = [
        ['label' => 'Hasil Assessment', 'nilai' => $totalHasilAssessment, 'satuan' => $labelTahun],
        ['label' => 'Lulus', 'nilai' => $totalLulus, 'satuan' => 'peserta'],
        ['label' => 'Tidak Lulus', 'nilai' => $totalTidakLulus, 'satuan' => 'peserta'],
        [
            'label' => 'Tingkat Kelulusan',
            'nilai' => number_format($tingkatKelulusan, 2) . '%',
            'satuan' => 'Rata-rata nilai: ' . number_format($rataRataNilai, 2),
        ],
    ];

    $statistikPengembangan = [
        ['label' => 'Total Pengembangan', 'nilai' => $totalPengembangan],
        ['label' => 'Diajukan', 'nilai' => $pengembanganDiajukan],
        ['label' => 'Diproses', 'nilai' => $pengembanganDiproses],
        ['label' => 'Disetujui', 'nilai' => $pengembanganDisetujui],
        ['label' => 'Ditolak', 'nilai' => $pengembanganDitolak],
    ];

    $statistikRealisasi = [
        [
            'label' => 'Perubahan Jabatan',
            'nilai' => $totalPerubahanJabatan,
            'keterangan' => 'Perubahan terealisasi',
        ],
        [
            'label' => 'Promosi Struktural',
            'nilai' => $totalPromosiStruktural,
            'keterangan' => 'Promosi terealisasi',
        ],
        [
            'label' => 'Pemindahan Jabatan',
            'nilai' => $totalPemindahanAktual,
            'keterangan' => 'Pemindahan terealisasi',
        ],
        [
            'label' => 'Peningkatan Skill',
            'nilai' => $totalPeningkatanSkill,
            'keterangan' => 'Pengajuan disetujui',
        ],
    ];
@endphp

<div class="w-full min-w-0 space-y-6">

    {{-- ================================================= --}}
    {{-- HEADER DAN FILTER TAHUN --}}
    {{-- ================================================= --}}

    <div class="flex flex-col gap-4
                lg:flex-row lg:items-end lg:justify-between">

        <div class="min-w-0">
            <h1 class="text-2xl font-bold text-[#304c3f]">
                Dashboard Analitik
            </h1>

            <p class="mt-1 max-w-3xl text-sm leading-6 text-gray-500">
                Ringkasan karyawan, assessment, dan pengembangan
                {{ $labelTahun }}.
            </p>

            <p class="text-xs text-gray-400">
                Distribusi karyawan menggunakan data warehouse
                pada pembaruan terakhir.
            </p>
        </div>

        <form
            method="GET"
            action="{{ route('hrd.laporan.index') }}"
            class="flex shrink-0 items-end gap-3"
        >
            <div class="min-w-0">
                <label for="filter-tahun"
                       class="mb-2 block text-xs font-semibold
                              text-[#304c3f]">
                    Filter Tahun
                </label>

                <select
                    id="filter-tahun"
                    name="tahun"
                    onchange="this.form.submit()"
                    class="h-11 min-w-[170px] rounded-lg
                           border border-[#dfe5df] bg-white
                           px-3 text-sm font-medium text-gray-700
                           outline-none transition
                           focus:border-[#4c7b58]
                           focus:ring-2 focus:ring-[#dce8df]"
                >
                    @foreach ($tahunTersedia as $tahunItem)
                        <option
                            value="{{ $tahunItem }}"
                            @selected((string) $tahun === (string) $tahunItem)
                        >
                            {{ $tahunItem === 'semua'
                                ? 'Semua Tahun'
                                : $tahunItem }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button
                type="submit"
                class="inline-flex h-11 items-center justify-center
                       rounded-lg bg-[#4c7b58] px-5
                       text-sm font-semibold text-white
                       transition hover:bg-[#3e6749]"
            >
                Terapkan
            </button>
        </form>
    </div>


    {{-- ================================================= --}}
    {{-- RINGKASAN DATA MASTER --}}
    {{-- ================================================= --}}

    <section class="min-w-0 rounded-xl border
                    border-[#e0e5df] bg-white p-5">

        <div class="mb-5 overflow-hidden rounded-lg
                    bg-[#f4f6f3]">

            <div class="rounded-l-md border-l-[5px]
                        border-[#4c7b58] px-5 py-3">

                <h2 class="text-base font-bold text-[#304c3f]">
                    Ringkasan Data Karyawan
                </h2>

            </div>

        </div>

        <div class="grid grid-cols-1 gap-4
                    sm:grid-cols-2 xl:grid-cols-4">

            @foreach ($statistikMaster as $item)

                <div class="min-w-0 rounded-xl border
                            border-[#e1e7e1] bg-[#fafbf9]
                            p-5">

                    <p class="text-xs font-semibold uppercase
                              tracking-wide text-gray-500">
                        {{ $item['label'] }}
                    </p>

                    <p class="mt-3 text-3xl font-bold text-[#304c3f]">
                        {{ number_format($item['nilai']) }}
                    </p>

                    <p class="mt-2 text-xs text-gray-500">
                        {{ $item['satuan'] }}
                    </p>

                    <div class="mt-4 h-1 w-10 rounded-full
                                bg-[#4c7b58]"></div>

                </div>

            @endforeach

        </div>

    </section>


    {{-- ================================================= --}}
    {{-- RINGKASAN HASIL ASSESSMENT --}}
    {{-- ================================================= --}}

    <section class="min-w-0 rounded-xl border
                    border-[#e0e5df] bg-white p-5">

        <div class="mb-5 overflow-hidden rounded-lg
                    bg-[#f4f6f3]">

            <div class="rounded-l-md border-l-[5px]
                        border-[#4c7b58] px-5 py-3">

                <h2 class="text-base font-bold text-[#304c3f]">
                    Ringkasan Hasil Assessment
                </h2>

            </div>

        </div>

        <div class="grid grid-cols-1 gap-4
                    sm:grid-cols-2 xl:grid-cols-4">

            @foreach ($statistikAssessment as $item)

                <div class="min-w-0 rounded-xl border
                            border-[#e1e7e1] bg-[#fafbf9]
                            p-5">

                    <p class="text-xs font-semibold uppercase
                              tracking-wide text-gray-500">
                        {{ $item['label'] }}
                    </p>

                    <p class="mt-3 text-3xl font-bold
                        {{ $item['label'] === 'Tidak Lulus'
                            ? 'text-red-600'
                            : 'text-[#304c3f]' }}">
                        {{ $item['nilai'] }}
                    </p>

                    <p class="mt-2 text-xs text-gray-500">
                        {{ $item['satuan'] }}
                    </p>

                    <div class="mt-4 h-1 w-10 rounded-full
                        {{ $item['label'] === 'Tidak Lulus'
                            ? 'bg-red-400'
                            : 'bg-[#4c7b58]' }}">
                    </div>

                </div>

            @endforeach

        </div>

    </section>


    {{-- ================================================= --}}
    {{-- RINGKASAN PENGEMBANGAN --}}
    {{-- ================================================= --}}

    <section class="min-w-0 rounded-xl border
                    border-[#e0e5df] bg-white p-5">

        <div class="mb-5 overflow-hidden rounded-lg
                    bg-[#f4f6f3]">

            <div class="rounded-l-md border-l-[5px]
                        border-[#4c7b58] px-5 py-3">

                <h2 class="text-base font-bold text-[#304c3f]">
                    Ringkasan Pengembangan Karyawan
                </h2>

            </div>

        </div>

        <div class="grid grid-cols-1 gap-4
                    sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">

            @foreach ($statistikPengembangan as $item)

                <div class="min-w-0 rounded-xl border
                            border-[#e1e7e1] bg-[#fafbf9]
                            p-4">

                    <p class="min-h-[32px] text-xs font-semibold
                              uppercase tracking-wide text-gray-500">
                        {{ $item['label'] }}
                    </p>

                    <p class="mt-2 text-3xl font-bold
                        {{ $item['label'] === 'Ditolak'
                            ? 'text-red-600'
                            : 'text-[#304c3f]' }}">
                        {{ number_format($item['nilai']) }}
                    </p>

                    <div class="mt-4 h-1 w-10 rounded-full
                        {{ $item['label'] === 'Ditolak'
                            ? 'bg-red-400'
                            : 'bg-[#4c7b58]' }}">
                    </div>

                </div>

            @endforeach

        </div>

        <p class="mt-4 text-xs leading-5 text-gray-500">
            Status mengikuti kondisi terakhir pengajuan yang dibuat
            {{ $labelTahun }}. Tren keputusan menggunakan
            tanggal keputusan.
        </p>

    </section>


    {{-- ================================================= --}}
    {{-- REALISASI PENGEMBANGAN --}}
    {{-- ================================================= --}}

    <section class="min-w-0 rounded-xl border
                    border-[#e0e5df] bg-white p-5">

        <div class="mb-5 overflow-hidden rounded-lg
                    bg-[#f4f6f3]">

            <div class="rounded-l-md border-l-[5px]
                        border-[#4c7b58] px-5 py-3">

                <h2 class="text-base font-bold text-[#304c3f]">
                    Realisasi Pengembangan
                </h2>

            </div>

        </div>

        <div class="grid grid-cols-1 gap-4
                    sm:grid-cols-2 xl:grid-cols-4">

            @foreach ($statistikRealisasi as $item)

                <div class="min-w-0 rounded-xl border
                            border-[#e1e7e1] bg-[#fafbf9]
                            p-5">

                    <p class="min-h-[32px] text-xs font-semibold
                              uppercase tracking-wide text-gray-500">
                        {{ $item['label'] }}
                    </p>

                    <p class="mt-2 text-3xl font-bold text-[#304c3f]">
                        {{ number_format($item['nilai']) }}
                    </p>

                    <p class="mt-2 text-xs text-gray-500">
                        {{ $item['keterangan'] }} {{ $labelTahun }}
                    </p>

                    <div class="mt-4 h-1 w-10 rounded-full
                                bg-[#e6b93d]"></div>

                </div>

            @endforeach

        </div>

    </section>


    {{-- ================================================= --}}
    {{-- GRAFIK STATUS ASSESSMENT DAN PENGEMBANGAN --}}
    {{-- ================================================= --}}

    <div class="grid min-w-0 grid-cols-1 gap-5 xl:grid-cols-2">

        {{-- STATUS ASSESSMENT --}}
        <section class="min-w-0 rounded-xl border
                        border-[#e0e5df] bg-white p-5">

            <div class="mb-4 overflow-hidden rounded-lg
                        bg-[#f4f6f3]">

                <div class="rounded-l-md border-l-[5px]
                            border-[#4c7b58] px-5 py-3">

                    <h2 class="text-base font-bold text-[#304c3f]">
                        Status Hasil Assessment
                    </h2>

                </div>

            </div>

            <p class="mb-5 text-xs text-gray-500">
                Perbandingan jumlah peserta lulus dan tidak lulus
                {{ $labelTahun }}.
            </p>

            <div class="relative h-[300px] w-full min-w-0">
                <canvas id="assessmentStatusChart"></canvas>
            </div>

        </section>


        {{-- PENGEMBANGAN BERDASARKAN JENIS --}}
        <section class="min-w-0 rounded-xl border
                        border-[#e0e5df] bg-white p-5">

            <div class="mb-4 overflow-hidden rounded-lg
                        bg-[#f4f6f3]">

                <div class="rounded-l-md border-l-[5px]
                            border-[#4c7b58] px-5 py-3">

                    <h2 class="text-base font-bold text-[#304c3f]">
                        Pengembangan Berdasarkan Jenis
                    </h2>

                </div>

            </div>

            <p class="mb-5 text-xs text-gray-500">
                Distribusi pengajuan kenaikan jabatan,
                pemindahan jabatan, dan peningkatan skill.
            </p>

            <div class="relative h-[300px] w-full min-w-0">
                <canvas id="pengembanganJenisChart"></canvas>
            </div>

        </section>

    </div>


    {{-- ================================================= --}}
    {{-- GRAFIK DISTRIBUSI KARYAWAN --}}
    {{-- ================================================= --}}

    <div class="grid min-w-0 grid-cols-1 gap-5 xl:grid-cols-2">

        {{-- PER JABATAN --}}
        <section class="min-w-0 rounded-xl border
                        border-[#e0e5df] bg-white p-5">

            <div class="mb-4 overflow-hidden rounded-lg
                        bg-[#f4f6f3]">

                <div class="rounded-l-md border-l-[5px]
                            border-[#4c7b58] px-5 py-3">

                    <h2 class="text-base font-bold text-[#304c3f]">
                        Karyawan Aktif per Jabatan
                    </h2>

                </div>

            </div>

            <p class="mb-5 text-xs text-gray-500">
                Distribusi karyawan aktif berdasarkan jabatan saat ini.
            </p>

            <div class="relative h-[350px] w-full min-w-0">
                <canvas id="jabatanChart"></canvas>
            </div>

        </section>


        {{-- PER DEPARTEMEN --}}
        <section class="min-w-0 rounded-xl border
                        border-[#e0e5df] bg-white p-5">

            <div class="mb-4 overflow-hidden rounded-lg
                        bg-[#f4f6f3]">

                <div class="rounded-l-md border-l-[5px]
                            border-[#4c7b58] px-5 py-3">

                    <h2 class="text-base font-bold text-[#304c3f]">
                        Karyawan Aktif per Departemen
                    </h2>

                </div>

            </div>

            <p class="mb-5 text-xs text-gray-500">
                Distribusi jumlah karyawan aktif berdasarkan departemen.
            </p>

            <div class="relative h-[350px] w-full min-w-0">
                <canvas id="departemenChart"></canvas>
            </div>

        </section>

    </div>


    {{-- ================================================= --}}
    {{-- GRAFIK TREN ASSESSMENT --}}
    {{-- ================================================= --}}

    <section class="min-w-0 rounded-xl border
                    border-[#e0e5df] bg-white p-5">

        <div class="mb-4 overflow-hidden rounded-lg
                    bg-[#f4f6f3]">

            <div class="rounded-l-md border-l-[5px]
                        border-[#4c7b58] px-5 py-3">

                <h2 class="text-base font-bold text-[#304c3f]">
                    Tren Hasil Assessment {{ $labelTahun }}
                </h2>

            </div>

        </div>

        <p class="mb-5 text-xs text-gray-500">
            Perkembangan jumlah peserta lulus dan tidak lulus
            dari Januari hingga Desember.
        </p>

        <div class="relative h-[350px] w-full min-w-0">
            <canvas id="assessmentTrendChart"></canvas>
        </div>

    </section>


    {{-- ================================================= --}}
    {{-- PROMOSI DAN KEPUTUSAN PENGEMBANGAN --}}
    {{-- ================================================= --}}

    <div class="grid min-w-0 grid-cols-1 gap-5 xl:grid-cols-2">

        {{-- PROMOSI STRUKTURAL --}}
        <section class="min-w-0 rounded-xl border
                        border-[#e0e5df] bg-white p-5">

            <div class="mb-4 overflow-hidden rounded-lg
                        bg-[#f4f6f3]">

                <div class="rounded-l-md border-l-[5px]
                            border-[#4c7b58] px-5 py-3">

                    <h2 class="text-base font-bold text-[#304c3f]">
                        Promosi Struktural per Urutan
                    </h2>

                </div>

            </div>

            <p class="mb-5 text-xs text-gray-500">
                Jumlah promosi jabatan struktural terealisasi
                {{ $labelTahun }}.
            </p>

            <div class="relative h-[350px] w-full min-w-0">
                <canvas id="promotionSequenceChart"></canvas>
            </div>

        </section>


        {{-- KEPUTUSAN PENGAJUAN --}}
        <section class="min-w-0 rounded-xl border
                        border-[#e0e5df] bg-white p-5">

            <div class="mb-4 overflow-hidden rounded-lg
                        bg-[#f4f6f3]">

                <div class="rounded-l-md border-l-[5px]
                            border-[#4c7b58] px-5 py-3">

                    <h2 class="text-base font-bold text-[#304c3f]">
                        Keputusan Pengajuan per Bulan
                    </h2>

                </div>

            </div>

            <p class="mb-5 text-xs text-gray-500">
                Tren jumlah pengajuan yang disetujui
                dan ditolak {{ $labelTahun }}.
            </p>

            <div class="relative h-[350px] w-full min-w-0">
                <canvas id="developmentDecisionChart"></canvas>
            </div>

        </section>

    </div>


    {{-- ================================================= --}}
    {{-- TABEL PENGEMBANGAN TERBARU --}}
    {{-- ================================================= --}}

    <section class="min-w-0 overflow-hidden rounded-xl
                    border border-[#e0e5df] bg-white">

        <div class="p-5 pb-4">

            <div class="mb-3 overflow-hidden rounded-lg
                        bg-[#f4f6f3]">

                <div class="rounded-l-md border-l-[5px]
                            border-[#4c7b58] px-5 py-3">

                    <h2 class="text-base font-bold text-[#304c3f]">
                        Pengembangan Terbaru
                    </h2>

                </div>

            </div>

            <p class="text-xs text-gray-500">
                Daftar 10 aktivitas pengajuan pengembangan terbaru
                {{ $labelTahun }}.
            </p>

        </div>

        <div class="w-full min-w-0 overflow-x-auto">

            <table class="w-full min-w-[1100px]
                          border-collapse text-sm">

                <thead class="bg-[#f4f6f3]">

                    <tr class="text-xs uppercase
                               tracking-wide text-[#304c3f]">

                        <th class="px-4 py-4 text-left font-bold">
                            No
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            Karyawan
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            Jenis
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            Detail
                        </th>

                        <th class="px-4 py-4 text-center font-bold">
                            Status
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            Tanggal Pengajuan
                        </th>

                        <th class="px-4 py-4 text-left font-bold">
                            Tanggal Keputusan
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-[#e8ece8]">

                    @forelse ($pengembanganTerbaru as $pengajuan)

                        @php
                            $jenisPengajuan = match ($pengajuan->jenis_pengajuan) {
                                'kenaikan_jabatan' => 'Kenaikan Jabatan',
                                'pemindahan_jabatan' => 'Pemindahan Jabatan',
                                'peningkatan_skill' => 'Peningkatan Skill',
                                default => ucwords(str_replace(
                                    '_', ' ', $pengajuan->jenis_pengajuan ?? '-'
                                )),
                            };

                            $warnaStatus = match ($pengajuan->status) {
                                'disetujui' => 'bg-[#e8f1ea] text-[#304c3f] border-[#d3e3d6]',
                                'ditolak' => 'bg-red-50 text-red-700 border-red-200',
                                'diproses' => 'bg-[#fff8e6] text-[#927018] border-[#f2e3ab]',
                                'diajukan' => 'bg-gray-100 text-gray-600 border-gray-200',
                                default => 'bg-gray-100 text-gray-600 border-gray-200',
                            };
                        @endphp

                        <tr class="transition-colors hover:bg-[#fafbf9]">

                            {{-- NOMOR --}}
                            <td class="whitespace-nowrap px-4 py-4
                                       text-gray-600">
                                {{ $loop->iteration }}
                            </td>


                            {{-- KARYAWAN --}}
                            <td class="px-4 py-4">

                                <p class="font-semibold text-gray-800">
                                    {{ $pengajuan->nama_karyawan }}
                                </p>

                                <p class="mt-1 text-xs text-gray-500">
                                    NIK: {{ $pengajuan->nik }}
                                </p>

                            </td>


                            {{-- JENIS --}}
                            <td class="px-4 py-4">

                                <span class="font-medium text-gray-700">
                                    {{ $jenisPengajuan }}
                                </span>

                            </td>


                            {{-- DETAIL --}}
                            <td class="min-w-[260px] px-4 py-4
                                       text-gray-700">

                                @if ($pengajuan->jenis_pengajuan === 'peningkatan_skill')

                                    @if (
                                        isset($pengajuan->skill_progressions) &&
                                        $pengajuan->skill_progressions->isNotEmpty()
                                    )

                                        <div class="space-y-2">

                                            @foreach (
                                                $pengajuan->skill_progressions
                                                as $skillProgression
                                            )

                                                <div class="text-xs leading-5">

                                                    <span class="font-medium">
                                                        {{ $skillProgression->nama_skill }}
                                                    </span>

                                                    <span class="text-gray-500">
                                                        Level
                                                        {{ $skillProgression->level_sebelum ?? '-' }}
                                                    </span>

                                                    <span class="mx-1 text-[#4c7b58]">
                                                        →
                                                    </span>

                                                    <span class="font-semibold
                                                                 text-[#304c3f]">
                                                        Level
                                                        {{ $skillProgression->level_sesudah ?? '-' }}
                                                    </span>

                                                </div>

                                            @endforeach

                                        </div>

                                    @else

                                        <p class="text-xs leading-5">

                                            {{ $pengajuan->jabatan_asal
                                                ?? $pengajuan->nama_skill
                                                ?? '-' }}

                                            <span class="mx-1 text-[#4c7b58]">
                                                →
                                            </span>

                                            {{ $pengajuan->jabatan_tujuan
                                                ?? $pengajuan->nama_skill
                                                ?? '-' }}

                                        </p>

                                        @if (
                                            $pengajuan->level_sebelum !== null ||
                                            $pengajuan->level_sesudah !== null
                                        )

                                            <p class="mt-1 text-xs text-gray-500">
                                                Level
                                                {{ $pengajuan->level_sebelum ?? '-' }}
                                                →
                                                {{ $pengajuan->level_sesudah ?? '-' }}
                                            </p>

                                        @endif

                                    @endif

                                @else

                                    <p class="text-xs leading-5">

                                        <span class="font-medium">
                                            {{ $pengajuan->jabatan_asal ?? '-' }}
                                        </span>

                                        @if (
                                            $pengajuan->tipe_jabatan_asal === 'struktural' &&
                                            $pengajuan->urutan_asal !== null
                                        )
                                            (Urutan {{ $pengajuan->urutan_asal }})
                                        @endif

                                        <span class="mx-1 text-[#4c7b58]">
                                            →
                                        </span>

                                        <span class="font-semibold text-[#304c3f]">
                                            {{ $pengajuan->jabatan_tujuan ?? '-' }}
                                        </span>

                                        @if (
                                            $pengajuan->tipe_jabatan_tujuan === 'struktural' &&
                                            $pengajuan->urutan_tujuan !== null
                                        )
                                            (Urutan {{ $pengajuan->urutan_tujuan }})
                                        @endif

                                    </p>

                                @endif

                            </td>


                            {{-- STATUS --}}
                            <td class="px-4 py-4 text-center">

                                <span class="inline-flex items-center
                                             justify-center whitespace-nowrap
                                             rounded-md border px-3 py-1.5
                                             text-xs font-semibold
                                             {{ $warnaStatus }}">

                                    {{ ucfirst($pengajuan->status) }}

                                </span>

                            </td>


                            {{-- TANGGAL PENGAJUAN --}}
                            <td class="whitespace-nowrap px-4 py-4
                                       text-gray-600">

                                {{ $pengajuan->tanggal
                                    ? \Carbon\Carbon::parse($pengajuan->tanggal)->format('d M Y')
                                    : '-' }}

                            </td>


                            {{-- TANGGAL KEPUTUSAN --}}
                            <td class="whitespace-nowrap px-4 py-4
                                       text-gray-600">

                                {{ $pengajuan->tanggal_keputusan
                                    ? \Carbon\Carbon::parse($pengajuan->tanggal_keputusan)->format('d M Y')
                                    : '-' }}

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7"
                                class="px-6 py-12 text-center">

                                <p class="text-sm font-semibold text-gray-600">
                                    Belum ada data pengembangan.
                                </p>

                                <p class="mt-1 text-xs text-gray-400">
                                    Data pengembangan {{ $labelTahun }}
                                    akan ditampilkan di sini.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>

</div>


{{-- ================================================= --}}
{{-- CHART.JS --}}
{{-- ================================================= --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ---------------------------------------------
    // DATA DARI LARAVEL
    // ---------------------------------------------

    const assessmentLulus = {{ Js::from($totalLulus) }};
    const assessmentTidakLulus = {{ Js::from($totalTidakLulus) }};

    const jabatanLabels = {{ Js::from($karyawanPerJabatan->pluck('label_jabatan')->values()) }};
    const jabatanData = {{ Js::from($karyawanPerJabatan->pluck('jumlah')->values()) }};

    const departemenLabels = {{ Js::from($karyawanPerDepartemen->pluck('nama_departemen')->values()) }};
    const departemenData = {{ Js::from($karyawanPerDepartemen->pluck('jumlah')->values()) }};

    const jenisLabels = {{ Js::from($labelPengembanganJenis) }};
    const jenisData = {{ Js::from($dataPengembanganJenis) }};

    const bulanLabels = {{ Js::from($labelBulan) }};
    const lulusBulanan = {{ Js::from($dataLulusBulanan) }};
    const tidakLulusBulanan = {{ Js::from($dataTidakLulusBulanan) }};

    const promosiUrutanLabels = {{ Js::from($labelPromosiPerUrutan) }};
    const promosiUrutanData = {{ Js::from($dataPromosiPerUrutan) }};

    const keputusanDisetujuiData = {{ Js::from($dataDisetujuiBulanan) }};
    const keputusanDitolakData = {{ Js::from($dataDitolakBulanan) }};


    // ---------------------------------------------
    // PALET WARNA GRAFIK
    // ---------------------------------------------

    const hijau = '#4c7b58';
    const hijauGelap = '#304c3f';
    const hijauMuda = '#91b59a';
    const kuning = '#e6b93d';
    const merah = '#d87676';

    const warnaGrafik = [
        hijau,
        kuning,
        hijauMuda,
        hijauGelap,
        '#b8c9b9',
        '#d3a96a',
        '#789b83',
        '#aab8ad'
    ];

    Chart.defaults.font.family = 'Arial, sans-serif';
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#6b7280';
    Chart.defaults.plugins.legend.labels.usePointStyle = true;


    // ---------------------------------------------
    // PENGATURAN GRAFIK
    // ---------------------------------------------

    const opsiLingkaran = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 18
                }
            }
        }
    };

    const opsiBatang = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    maxRotation: 45,
                    minRotation: 0
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                },
                grid: {
                    color: '#edf1ed'
                }
            }
        }
    };

    const opsiGaris = {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            intersect: false,
            mode: 'index'
        },
        plugins: {
            legend: {
                position: 'bottom'
            }
        },
        scales: {
            x: {
                grid: {
                    display: false
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                },
                grid: {
                    color: '#edf1ed'
                }
            }
        }
    };


    // ---------------------------------------------
    // 1. STATUS HASIL ASSESSMENT
    // ---------------------------------------------

    new Chart(
        document.getElementById('assessmentStatusChart'),
        {
            type: 'doughnut',
            data: {
                labels: ['Lulus', 'Tidak Lulus'],
                datasets: [{
                    data: [
                        assessmentLulus,
                        assessmentTidakLulus
                    ],
                    backgroundColor: [
                        hijau,
                        merah
                    ],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 5
                }]
            },
            options: {
                ...opsiLingkaran,
                cutout: '68%'
            }
        }
    );


    // ---------------------------------------------
    // 2. PENGEMBANGAN BERDASARKAN JENIS
    // ---------------------------------------------

    new Chart(
        document.getElementById('pengembanganJenisChart'),
        {
            type: 'pie',
            data: {
                labels: jenisLabels,
                datasets: [{
                    data: jenisData,
                    backgroundColor: warnaGrafik,
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 5
                }]
            },
            options: opsiLingkaran
        }
    );


    // ---------------------------------------------
    // 3. KARYAWAN PER JABATAN
    // ---------------------------------------------

    new Chart(
        document.getElementById('jabatanChart'),
        {
            type: 'bar',
            data: {
                labels: jabatanLabels,
                datasets: [{
                    label: 'Karyawan',
                    data: jabatanData,
                    backgroundColor: hijau,
                    borderRadius: 5,
                    maxBarThickness: 45
                }]
            },
            options: opsiBatang
        }
    );


    // ---------------------------------------------
    // 4. KARYAWAN PER DEPARTEMEN
    // ---------------------------------------------

    new Chart(
        document.getElementById('departemenChart'),
        {
            type: 'bar',
            data: {
                labels: departemenLabels,
                datasets: [{
                    label: 'Karyawan',
                    data: departemenData,
                    backgroundColor: hijauMuda,
                    borderRadius: 5,
                    maxBarThickness: 45
                }]
            },
            options: opsiBatang
        }
    );


    // ---------------------------------------------
    // 5. TREN HASIL ASSESSMENT
    // ---------------------------------------------

    new Chart(
        document.getElementById('assessmentTrendChart'),
        {
            type: 'line',
            data: {
                labels: bulanLabels,
                datasets: [
                    {
                        label: 'Lulus',
                        data: lulusBulanan,
                        borderColor: hijau,
                        backgroundColor: '#4c7b581a',
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    },
                    {
                        label: 'Tidak Lulus',
                        data: tidakLulusBulanan,
                        borderColor: merah,
                        backgroundColor: '#d876761a',
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    }
                ]
            },
            options: opsiGaris
        }
    );


    // ---------------------------------------------
    // 6. PROMOSI STRUKTURAL
    // ---------------------------------------------

    new Chart(
        document.getElementById('promotionSequenceChart'),
        {
            type: 'bar',
            data: {
                labels: promosiUrutanLabels,
                datasets: [{
                    label: 'Promosi Terealisasi',
                    data: promosiUrutanData,
                    backgroundColor: kuning,
                    borderRadius: 5,
                    maxBarThickness: 45
                }]
            },
            options: opsiBatang
        }
    );


    // ---------------------------------------------
    // 7. KEPUTUSAN PENGAJUAN
    // ---------------------------------------------

    new Chart(
        document.getElementById('developmentDecisionChart'),
        {
            type: 'line',
            data: {
                labels: bulanLabels,
                datasets: [
                    {
                        label: 'Disetujui',
                        data: keputusanDisetujuiData,
                        borderColor: hijau,
                        backgroundColor: '#4c7b581a',
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    },
                    {
                        label: 'Ditolak',
                        data: keputusanDitolakData,
                        borderColor: merah,
                        backgroundColor: '#d876761a',
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    }
                ]
            },
            options: opsiGaris
        }
    );

});
</script>

@endsection
