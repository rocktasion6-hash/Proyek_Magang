@extends('layouts.hrd')

@section('title', 'Manajemen Jabatan')
@section('page_title', 'Manajemen Jabatan')

@push('styles')
<style>
    #mjJabatanPage.mj-page {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        box-sizing: border-box;
    }

    #mjJabatanPage .mj-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    #mjJabatanPage .mj-top h1 {
        margin: 0 0 4px;
        font-size: 24px;
        font-weight: 700;
        color: #2c4a3e;
    }

    #mjJabatanPage .mj-top p {
        margin: 0;
        font-size: 14.5px;
        color: #4b5563;
    }

    #mjJabatanPage .mj-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 11px 18px;
        border-radius: 8px;
        border: 1.5px solid transparent;
        font-size: 14.5px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        line-height: 1.2;
        white-space: nowrap;
        box-sizing: border-box;
    }

    #mjJabatanPage .mj-btn-primary {
        background: #2c4a3e;
        color: #fdfdfc;
        border-color: #2c4a3e;
    }

    #mjJabatanPage .mj-btn-primary:hover,
    #mjJabatanPage .mj-btn-primary:active {
        background: #4a7c59;
        color: #fdfdfc;
        border-color: #4a7c59;
    }

    #mjJabatanPage .mj-btn-reset {
        background: #ffffff;
        color: #374151;
        border-color: #cbd5e1;
    }

    #mjJabatanPage .mj-btn-reset:hover,
    #mjJabatanPage .mj-btn-reset:active {
        background: #f4f6f4;
        color: #111827;
        border-color: #2c4a3e;
    }

    #mjJabatanPage .mj-btn-sm {
        padding: 7px 13px;
        font-size: 13.5px;
        font-weight: 700;
        border-radius: 7px;
    }

    #mjJabatanPage .mj-btn-detail {
        background: #f4f6f4;
        color: #2c4a3e;
        border-color: #cbd5e1;
    }

    #mjJabatanPage .mj-btn-detail:hover,
    #mjJabatanPage .mj-btn-detail:active {
        background: #2c4a3e;
        color: #fdfdfc;
        border-color: #2c4a3e;
    }

    #mjJabatanPage .mj-btn-edit {
        background: #e3b535;
        color: #111827;
        border-color: #e3b535;
    }

    #mjJabatanPage .mj-btn-edit:hover,
    #mjJabatanPage .mj-btn-edit:active {
        background: #cca024;
        color: #111827;
        border-color: #cca024;
    }

    #mjJabatanPage .mj-btn-delete {
        background: #ffffff;
        color: #991b1b;
        border-color: #fecaca;
    }

    #mjJabatanPage .mj-btn-delete:hover,
    #mjJabatanPage .mj-btn-delete:active {
        background: #991b1b;
        color: #ffffff;
        border-color: #991b1b;
    }

    #mjJabatanPage .mj-flash {
        background: #f4f6f4;
        color: #2c4a3e;
        border: 1px solid #d1dbd4;
        border-left: 4px solid #4a7c59;
        border-radius: 8px;
        padding: 13px 16px;
        margin-bottom: 20px;
        font-size: 14.5px;
        font-weight: 500;
    }

    #mjJabatanPage .mj-filter-box {
        background: #ffffff;
        border: 1px solid #e2e8e4;
        border-radius: 10px;
        padding: 18px 22px;
        margin-bottom: 20px;
    }

    #mjJabatanPage .mj-filter-row {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
    }

    #mjJabatanPage .mj-filter-row input[type="text"],
    #mjJabatanPage .mj-filter-row select {
        height: 44px;
        padding: 0 14px;
        font-size: 14.5px;
        color: #111827;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        box-sizing: border-box;
    }

    #mjJabatanPage .mj-filter-row input[type="text"] {
        min-width: 260px;
        flex: 1 1 280px;
    }

    #mjJabatanPage .mj-filter-row select {
        min-width: 200px;
    }

    #mjJabatanPage .mj-filter-row input[type="text"]:focus,
    #mjJabatanPage .mj-filter-row select:focus {
        outline: none;
        border-color: #2c4a3e;
    }

    #mjJabatanPage .mj-table-wrap {
        background: #ffffff;
        border: 1px solid #e2e8e4;
        border-radius: 10px;
        overflow-x: auto;
    }

    #mjJabatanPage .mj-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 980px;
    }

    #mjJabatanPage .mj-table th {
        background: #f4f6f4;
        color: #2c4a3e;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 14px 16px;
        border-bottom: 1.5px solid #d8e0da;
        text-align: left;
        white-space: nowrap;
    }

    #mjJabatanPage .mj-table th.mj-col-center,
    #mjJabatanPage .mj-table td.mj-col-center {
        text-align: center;
    }

    #mjJabatanPage .mj-table td {
        padding: 14px 16px;
        font-size: 14.5px;
        color: #1f2937;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    #mjJabatanPage .mj-table tbody tr:hover {
        background: #fdfdfc;
    }

    #mjJabatanPage .mj-table tbody tr:last-child td {
        border-bottom: none;
    }

    #mjJabatanPage .mj-tipe-badge {
        display: inline-block;
        padding: 4px 11px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.2;
        white-space: nowrap;
    }

    #mjJabatanPage .mj-tipe-badge.struktural {
        background: #fdf6df;
        color: #2c4a3e;
        border: 1px solid #e3b535;
    }

    #mjJabatanPage .mj-tipe-badge.operasional {
        background: #f4f6f4;
        color: #2c4a3e;
        border: 1px solid #4a7c59;
    }

    #mjJabatanPage .mj-count-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 6px;
        background: #f4f6f4;
        color: #2c4a3e;
        border: 1px solid #d1dbd4;
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
    }

    #mjJabatanPage .mj-aksi-group {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    #mjJabatanPage .mj-empty-row {
        text-align: center;
        padding: 34px 16px;
        color: #6b7280;
        font-size: 14.5px;
    }

    #mjJabatanPage .mj-pager {
        margin-top: 18px;
    }
</style>
@endpush

@section('content')
<div id="mjJabatanPage" class="mj-page">

    <div class="mj-top">
        <div>
            <h1>Manajemen Jabatan</h1>
            <p>Mengelola jabatan, level, dan standar nilai assessment.</p>
        </div>

        <a href="{{ route('hrd.jabatan.create') }}" class="mj-btn mj-btn-primary">
            + Tambah Jabatan
        </a>
    </div>

    @if(session('success'))
        <div class="mj-flash">
            {{ session('success') }}
        </div>
    @endif

    <div class="mj-filter-box">
        <form method="GET" action="{{ route('hrd.jabatan.index') }}" class="mj-filter-row">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari jabatan..."
            >

            <select name="tipe_jabatan">
                <option value="">Semua Tipe</option>
                <option value="operasional" {{ request('tipe_jabatan') === 'operasional' ? 'selected' : '' }}>
                    Operasional
                </option>
                <option value="struktural" {{ request('tipe_jabatan') === 'struktural' ? 'selected' : '' }}>
                    Struktural
                </option>
            </select>

            <button type="submit" class="mj-btn mj-btn-primary">
                Filter
            </button>

            @if(request()->filled('search') || request()->filled('tipe_jabatan'))
                <a href="{{ route('hrd.jabatan.index') }}" class="mj-btn mj-btn-reset">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <div class="mj-table-wrap">
        <table class="mj-table">
            <thead>
                <tr>
                    <th style="width: 64px;">No</th>
                    <th>Jabatan</th>
                    <th style="width: 175px;" class="mj-col-center">Tipe</th>
                    <th style="width: 180px;">Level</th>
                    <th style="width: 155px;" class="mj-col-center">Standar Nilai</th>
                    <th style="width: 150px;" class="mj-col-center">Karyawan</th>
                    <th style="width: 240px;" class="mj-col-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jabatans as $jabatan)
                    <tr>
                        <td>{{ $jabatans->firstItem() + $loop->index }}</td>
                        <td><strong>{{ $jabatan->nama_jabatan }}</strong></td>
                        <td class="mj-col-center">
                            @if($jabatan->tipe_jabatan === 'struktural')
                                <span class="mj-tipe-badge struktural">
                                    Struktural
                                    @if($jabatan->urutan_struktural)
                                        ({{ $jabatan->urutan_struktural }})
                                    @endif
                                </span>
                            @else
                                <span class="mj-tipe-badge operasional">
                                    Operasional
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($jabatan->tipe_jabatan === 'struktural')
                                @php
                                    $labelUrutan = [
                                        1 => 'Wakil Foreman',
                                        2 => 'Foreman',
                                        3 => 'Wakil Supervisor',
                                        4 => 'Supervisor',
                                        5 => 'Wakil General Manager',
                                        6 => 'Manager',
                                    ];
                                @endphp
                                {{ $labelUrutan[$jabatan->urutan_struktural] ?? '-' }}
                            @elseif(mb_strtolower(trim($jabatan->nama_jabatan)) === 'crew')
                                Crew
                            @else
                                Level {{ $jabatan->level_jabatan }}
                            @endif
                        </td>
                        <td class="mj-col-center">
                            <strong>{{ number_format($jabatan->standar_nilai, 2) }}</strong>
                        </td>
                        <td class="mj-col-center">
                            <span class="mj-count-badge">
                                {{ $jabatan->karyawans_count }} orang
                            </span>
                        </td>
                        <td class="mj-col-center">
                            <div class="mj-aksi-group">
                                <a
                                    href="{{ route('hrd.jabatan.show', $jabatan) }}"
                                    class="mj-btn mj-btn-sm mj-btn-detail"
                                >
                                    Detail
                                </a>

                                <a
                                    href="{{ route('hrd.jabatan.edit', $jabatan) }}"
                                    class="mj-btn mj-btn-sm mj-btn-edit"
                                >
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('hrd.jabatan.destroy', $jabatan) }}"
                                    style="display:inline;"
                                    onsubmit="return confirm('Yakin ingin menghapus jabatan ini?')"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="mj-btn mj-btn-sm mj-btn-delete">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="mj-empty-row">
                            Belum ada data jabatan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mj-pager">
        {{ $jabatans->links() }}
    </div>

</div>
@endsection