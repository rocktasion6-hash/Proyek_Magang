
@extends('layouts.hrd')

@section('title', 'Manajemen Karyawan')
@section('page_title', 'Manajemen Karyawan')

@push('styles')
<style>
    .mk-page {
        width: 100%;
        max-width: 100%;
    }

    .mk-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .mk-top h1 {
        margin: 0 0 4px;
        font-size: 24px;
        font-weight: 700;
        color: #2c4a3e;
    }

    .mk-top p {
        margin: 0;
        font-size: 14.5px;
        color: #4b5563;
    }

    .mk-btn {
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

    .mk-btn-primary {
        background: #4a7c59;
        color: #fdfdfc;
    }

    .mk-btn-primary:hover,
    .mk-btn-primary:active {
        background: #3b6649;
    }

    .mk-btn-reset {
        background: #ffffff;
        color: #374151;
        border-color: #cbd5e1;
    }

    .mk-btn-reset:hover,
    .mk-btn-reset:active {
        background: #f4f6f4;
        color: #111827;
        border-color: #4a7c59;
    }

    .mk-btn-sm {
        padding: 7px 13px;
        font-size: 13.5px;
        font-weight: 700;
        border-radius: 7px;
    }

    .mk-btn-detail {
        background: #f4f6f4;
        color: #2c4a3e;
        border-color: #cbd5e1;
    }

    .mk-btn-detail:hover,
    .mk-btn-detail:active {
        background: #4a7c59;
        color: #fdfdfc;
        border-color: #4a7c59;
    }

    .mk-btn-edit {
        background: #e3b535;
        color: #111827;
        border-color: #e3b535;
    }

    .mk-btn-edit:hover,
    .mk-btn-edit:active {
        background: #cca024;
        border-color: #cca024;
        color: #111827;
    }

    .mk-btn-delete {
        background: #ffffff;
        color: #991b1b;
        border-color: #fecaca;
    }

    .mk-btn-delete:hover,
    .mk-btn-delete:active {
        background: #991b1b;
        color: #ffffff;
        border-color: #991b1b;
    }

    .mk-flash {
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

    .mk-filter-box {
        background: #ffffff;
        border: 1px solid #e2e8e4;
        border-radius: 10px;
        padding: 18px 22px;
        margin-bottom: 20px;
    }

    .mk-filter-row {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
    }

    .mk-filter-row input[type="text"],
    .mk-filter-row select {
        height: 44px;
        padding: 0 14px;
        font-size: 14.5px;
        color: #111827;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        box-sizing: border-box;
    }

    .mk-filter-row input[type="text"] {
        min-width: 280px;
        flex: 1 1 280px;
    }

    .mk-filter-row select {
        min-width: 200px;
    }

    .mk-filter-row input[type="text"]:focus,
    .mk-filter-row select:focus {
        outline: none;
        border-color: #4a7c59;
    }

    .mk-select-status.is-aktif {
        color: #2c4a3e;
        font-weight: 600;
        border-color: #4a7c59;
        background-color: #f4f6f4;
    }

    .mk-select-status.is-nonaktif {
        color: #991b1b;
        font-weight: 600;
        border-color: #fecaca;
        background-color: #fef2f2;
    }

    .mk-table-wrap {
        background: #ffffff;
        border: 1px solid #e2e8e4;
        border-radius: 10px;
        overflow-x: auto;
    }

    .mk-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 960px;
    }

    .mk-table th {
        background: #f4f6f4;
        color: #2c4a3e;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 14px 16px;
        border-bottom: 1.5px solid #d8e0da;
        text-align: left;
    }

    .mk-table td {
        padding: 14px 16px;
        font-size: 14.5px;
        color: #1f2937;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    .mk-table tbody tr:hover {
        background: #fdfdfc;
    }

    .mk-table tbody tr:last-child td {
        border-bottom: none;
    }

    .mk-status {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 12.5px;
        font-weight: 700;
        line-height: 1.2;
    }

    .mk-status.aktif {
        background: #4a7c59;
        color: #fdfdfc;
        border: 1px solid #4a7c59;
    }

    .mk-status.nonaktif {
        background: #991b1b;
        color: #ffffff;
        border: 1px solid #991b1b;
    }

    .mk-aksi-group {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .mk-empty-row {
        text-align: center;
        padding: 34px 16px;
        color: #6b7280;
        font-size: 14.5px;
    }

    .mk-pager {
        margin-top: 18px;
    }
</style>
@endpush

@section('content')
<div class="mk-page">

    <div class="mk-top">
        <div>
            <h1>Manajemen Karyawan</h1>
            <p>Mengelola data karyawan perusahaan.</p>
        </div>

        <a href="{{ route('hrd.karyawan.create') }}" class="mk-btn mk-btn-primary">
            + Tambah Karyawan
        </a>
    </div>

    @if(session('success'))
        <div class="mk-flash">
            {{ session('success') }}
        </div>
    @endif

    <div class="mk-filter-box">
        <form method="GET" action="{{ route('hrd.karyawan.index') }}" class="mk-filter-row">
            <input
                type="text"
                name="search"
                placeholder="Cari nama atau NIK..."
                value="{{ request('search') }}"
            >

            <select name="departemen_id">
                <option value="">Semua Departemen</option>
                @foreach($departemens as $departemen)
                    <option
                        value="{{ $departemen->id }}"
                        {{ request('departemen_id') == $departemen->id ? 'selected' : '' }}
                    >
                        {{ $departemen->nama_departemen }}
                    </option>
                @endforeach
            </select>

            <select
                name="status"
                id="filterStatus"
                class="mk-select-status {{ request('status') === 'aktif' ? 'is-aktif' : (request('status') === 'nonaktif' ? 'is-nonaktif' : '') }}"
            >
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>
                    Aktif
                </option>
                <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>
                    Nonaktif
                </option>
            </select>

            <button type="submit" class="mk-btn mk-btn-primary">
                Filter
            </button>

            @if(request()->filled('search') || request()->filled('departemen_id') || request()->filled('status'))
                <a href="{{ route('hrd.karyawan.index') }}" class="mk-btn mk-btn-reset">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <div class="mk-table-wrap">
        <table class="mk-table">
            <thead>
                <tr>
                    <th style="width: 64px;">No</th>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>PT/Perusahaan</th>
                    <th>Departemen</th>
                    <th>Jabatan</th>
                    <th>Status</th>
                    <th style="width: 220px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($karyawans as $karyawan)
                    <tr>
                        <td>{{ $karyawans->firstItem() + $loop->index }}</td>
                        <td>{{ $karyawan->nik }}</td>
                        <td><strong>{{ $karyawan->nama }}</strong></td>
                        <td>{{ $karyawan->pt ?: '-' }}</td>
                        <td>{{ $karyawan->departemen->nama_departemen }}</td>
                        <td>{{ $karyawan->jabatan->nama_jabatan }}</td>
                        <td>
                            <span class="mk-status {{ $karyawan->status }}">
                                {{ ucfirst($karyawan->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="mk-aksi-group">
                                <a
                                    href="{{ route('hrd.karyawan.show', $karyawan) }}"
                                    class="mk-btn mk-btn-sm mk-btn-detail"
                                >
                                    Detail
                                </a>

                                <a
                                    href="{{ route('hrd.karyawan.edit', $karyawan) }}"
                                    class="mk-btn mk-btn-sm mk-btn-edit"
                                >
                                    Edit
                                </a>

                                <form
                                    action="{{ route('hrd.karyawan.destroy', $karyawan) }}"
                                    method="POST"
                                    style="display:inline;"
                                    onsubmit="return confirm('Hapus karyawan ini beserta akun login, hasil assessment, dan seluruh riwayat terkait? Tindakan ini tidak dapat dibatalkan.')"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="mk-btn mk-btn-sm mk-btn-delete">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="mk-empty-row">
                            Belum ada data karyawan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mk-pager">
        {{ $karyawans->links() }}
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('filterStatus');
        if (!el) return;

        function syncStatusClass() {
            el.classList.remove('is-aktif', 'is-nonaktif');

            if (el.value === 'aktif') {
                el.classList.add('is-aktif');
            } else if (el.value === 'nonaktif') {
                el.classList.add('is-nonaktif');
            }
        }

        el.addEventListener('change', syncStatusClass);
    });
</script>
@endsection
