
@extends('layouts.hrd')

@section('title', 'Manajemen Departemen')
@section('page_title', 'Manajemen Departemen')

@push('styles')
<style>
    .md-page {
        width: 100%;
        max-width: 100%;
    }

    .md-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .md-top h1 {
        margin: 0 0 4px;
        font-size: 24px;
        font-weight: 700;
        color: #2c4a3e;
    }

    .md-top p {
        margin: 0;
        font-size: 14.5px;
        color: #4b5563;
    }

    .md-btn {
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
    }

    .md-btn-primary {
        background: #4a7c59;
        color: #fdfdfc;
    }

    .md-btn-primary:hover,
    .md-btn-primary:active {
        background: #3b6649;
    }

    .md-btn-reset {
        background: #ffffff;
        color: #374151;
        border-color: #cbd5e1;
    }

    .md-btn-reset:hover,
    .md-btn-reset:active {
        background: #f4f6f4;
        color: #111827;
        border-color: #4a7c59;
    }

    .md-btn-sm {
        padding: 7px 13px;
        font-size: 13.5px;
        font-weight: 700;
        border-radius: 7px;
    }

    .md-btn-detail {
        background: #f4f6f4;
        color: #2c4a3e;
        border-color: #cbd5e1;
    }

    .md-btn-detail:hover,
    .md-btn-detail:active {
        background: #4a7c59;
        color: #fdfdfc;
        border-color: #4a7c59;
    }

    .md-btn-edit {
        background: #e3b535;
        color: #111827;
        border-color: #e3b535;
    }

    .md-btn-edit:hover,
    .md-btn-edit:active {
        background: #cca024;
        border-color: #cca024;
        color: #111827;
    }

    .md-btn-delete {
        background: #ffffff;
        color: #991b1b;
        border-color: #fecaca;
    }

    .md-btn-delete:hover,
    .md-btn-delete:active {
        background: #991b1b;
        color: #ffffff;
        border-color: #991b1b;
    }

    .md-flash {
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

    .md-filter-box {
        background: #ffffff;
        border: 1px solid #e2e8e4;
        border-radius: 10px;
        padding: 18px 22px;
        margin-bottom: 20px;
    }

    .md-filter-row {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
    }

    .md-filter-row input[type="text"] {
        height: 44px;
        padding: 0 14px;
        font-size: 14.5px;
        color: #111827;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        box-sizing: border-box;
        min-width: 280px;
        flex: 1 1 320px;
    }

    .md-filter-row input[type="text"]:focus {
        outline: none;
        border-color: #4a7c59;
    }

    .md-table-wrap {
        background: #ffffff;
        border: 1px solid #e2e8e4;
        border-radius: 10px;
        overflow-x: auto;
    }

    .md-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }

    .md-table th {
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

    .md-table th.md-col-center,
    .md-table td.md-col-center {
        text-align: center;
    }

    .md-table td {
        padding: 14px 16px;
        font-size: 14.5px;
        color: #1f2937;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    .md-table tbody tr:hover {
        background: #fdfdfc;
    }

    .md-table tbody tr:last-child td {
        border-bottom: none;
    }

    .md-count-badge {
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

    .md-aksi-group {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .md-empty-row {
        text-align: center;
        padding: 34px 16px;
        color: #6b7280;
        font-size: 14.5px;
    }

    .md-pager-bar {
        margin-top: 18px;
        padding: 14px 18px;
        background: #ffffff;
        border: 1px solid #e2e8e4;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .md-pager-info {
        font-size: 13.5px;
        color: #4b5563;
    }

    .md-pager-info strong {
        color: #2c4a3e;
    }

    .md-pager-links nav {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .md-pager-links a,
    .md-pager-links span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 10px;
        border-radius: 7px;
        border: 1px solid #d1d5db;
        background: #ffffff;
        color: #1f2937;
        font-size: 13.5px;
        font-weight: 600;
        text-decoration: none;
        box-sizing: border-box;
    }

    .md-pager-links a:hover {
        background: #f4f6f4;
        border-color: #4a7c59;
        color: #2c4a3e;
    }

    .md-pager-links [aria-current="page"] span,
    .md-pager-links span.active {
        background: #4a7c59;
        border-color: #4a7c59;
        color: #fdfdfc;
    }

    .md-pager-links [aria-disabled="true"] span,
    .md-pager-links span.disabled {
        background: #f4f6f4;
        color: #9ca3af;
        border-color: #e5e7eb;
        cursor: not-allowed;
    }
</style>
@endpush

@section('content')
@php
    $isPaginator = method_exists($departemens, 'firstItem');
    $totalData = $isPaginator ? $departemens->total() : $departemens->count();
    $firstNum = $isPaginator ? ($departemens->firstItem() ?? 0) : ($totalData > 0 ? 1 : 0);
    $lastNum = $isPaginator ? ($departemens->lastItem() ?? 0) : $totalData;
@endphp

<div class="md-page">

    <div class="md-top">
        <div>
            <h1>Manajemen Departemen</h1>
            <p>Mengelola data departemen perusahaan.</p>
        </div>

        <a href="{{ route('hrd.departemen.create') }}" class="md-btn md-btn-primary">
            + Tambah Departemen
        </a>
    </div>

    @if(session('success'))
        <div class="md-flash">
            {{ session('success') }}
        </div>
    @endif

    <div class="md-filter-box">
        <form method="GET" action="{{ route('hrd.departemen.index') }}" class="md-filter-row">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama atau deskripsi departemen..."
            >

            <button type="submit" class="md-btn md-btn-primary">
                Cari
            </button>

            @if(request()->filled('search'))
                <a href="{{ route('hrd.departemen.index') }}" class="md-btn md-btn-reset">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <div class="md-table-wrap">
        <table class="md-table">
            <thead>
                <tr>
                    <th style="width: 64px;">No</th>
                    <th style="width: 220px;">Nama Departemen</th>
                    <th>Deskripsi</th>
                    <th style="width: 195px;" class="md-col-center">Jumlah Karyawan</th>
                    <th style="width: 240px;" class="md-col-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departemens as $departemen)
                    <tr>
                        <td>{{ $isPaginator ? ($departemens->firstItem() + $loop->index) : $loop->iteration }}</td>
                        <td><strong>{{ $departemen->nama_departemen }}</strong></td>
                        <td>{{ $departemen->deskripsi ?: '-' }}</td>
                        <td class="md-col-center">
                            <span class="md-count-badge">
                                {{ $departemen->karyawans_count }} orang
                            </span>
                        </td>
                        <td class="md-col-center">
                            <div class="md-aksi-group">
                                <a
                                    href="{{ route('hrd.departemen.show', $departemen) }}"
                                    class="md-btn md-btn-sm md-btn-detail"
                                >
                                    Detail
                                </a>

                                <a
                                    href="{{ route('hrd.departemen.edit', $departemen) }}"
                                    class="md-btn md-btn-sm md-btn-edit"
                                >
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('hrd.departemen.destroy', $departemen) }}"
                                    style="display:inline;"
                                    onsubmit="return confirm('Yakin ingin menghapus departemen ini?')"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="md-btn md-btn-sm md-btn-delete">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="md-empty-row">
                            Belum ada data departemen.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($totalData > 0)
        <div class="md-pager-bar">
            <div class="md-pager-info">
                Menampilkan
                <strong>{{ $firstNum }}</strong>
                –
                <strong>{{ $lastNum }}</strong>
                dari
                <strong>{{ $totalData }}</strong>
                data departemen
            </div>

            @if($isPaginator && $departemens->hasPages())
                <div class="md-pager-links">
                    {{ method_exists($departemens, 'withQueryString') ? $departemens->withQueryString()->links() : $departemens->links() }}
                </div>
            @endif
        </div>
    @endif

</div>
@endsection
