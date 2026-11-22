@extends('layouts.hrd')

@section('title', 'Detail Departemen')
@section('page_title', 'Detail Departemen')

@push('styles')
<style>
    #ddDepartemenPage.dd-wrap {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        box-sizing: border-box;
    }

    #ddDepartemenPage .dd-panel {
        width: 100% !important;
        max-width: none !important;
        background: #ffffff;
        border: 1px solid #d8e0da;
        border-radius: 10px;
        padding: 28px 36px;
        margin-bottom: 20px;
        box-sizing: border-box;
    }

    #ddDepartemenPage .dd-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        padding-bottom: 16px;
        margin-bottom: 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    #ddDepartemenPage .dd-head h1 {
        margin: 0 0 4px;
        font-size: 24px;
        font-weight: 700;
        color: #2c4a3e;
        line-height: 1.25;
    }

    #ddDepartemenPage .dd-head h2 {
        margin: 0 0 4px;
        font-size: 18px;
        font-weight: 700;
        color: #2c4a3e;
        line-height: 1.3;
    }

    #ddDepartemenPage .dd-head p {
        margin: 0;
        font-size: 14px;
        color: #4b5563;
        line-height: 1.45;
    }

    #ddDepartemenPage .dd-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    #ddDepartemenPage .dd-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 18px;
        border-radius: 8px;
        border: 1.5px solid transparent;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        line-height: 1.2;
        white-space: nowrap;
        box-sizing: border-box;
    }

    #ddDepartemenPage .dd-btn-back {
        background: #f4f6f4;
        color: #2c4a3e;
        border-color: #c6d2c9;
    }

    #ddDepartemenPage .dd-btn-back:hover,
    #ddDepartemenPage .dd-btn-back:active {
        background: #3b6649;
        color: #fdfdfc;
        border-color: #3b6649;
    }

    #ddDepartemenPage .dd-btn-edit {
        background: #e3b535;
        color: #111827;
        border-color: #e3b535;
        font-weight: 700;
    }

    #ddDepartemenPage .dd-btn-edit:hover,
    #ddDepartemenPage .dd-btn-edit:active {
        background: #cca024;
        border-color: #cca024;
        color: #111827;
    }

    #ddDepartemenPage .dd-info-grid {
        display: grid;
        grid-template-columns: 220px 1fr;
        border-top: 1px solid #edf0ee;
    }

    #ddDepartemenPage .dd-info-key {
        padding: 13px 16px 13px 0;
        font-size: 14px;
        font-weight: 700;
        color: #4b5563;
        border-bottom: 1px solid #edf0ee;
        vertical-align: top;
    }

    #ddDepartemenPage .dd-info-val {
        padding: 13px 0;
        font-size: 14.5px;
        font-weight: 600;
        color: #111827;
        border-bottom: 1px solid #edf0ee;
        vertical-align: top;
        word-break: break-word;
    }

    #ddDepartemenPage .dd-count-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 6px;
        background: #f4f6f4;
        color: #2c4a3e;
        border: 1px solid #d1dbd4;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
    }

    #ddDepartemenPage .dd-table-wrap {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #e2e8e4;
        border-radius: 8px;
    }

    #ddDepartemenPage .dd-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 640px;
    }

    #ddDepartemenPage .dd-table th {
        background: #f4f6f4;
        color: #2c4a3e;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 13px 16px;
        border-bottom: 1.5px solid #d8e0da;
        text-align: left;
        white-space: nowrap;
    }

    #ddDepartemenPage .dd-table td {
        padding: 13px 16px;
        font-size: 14.5px;
        color: #1f2937;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    #ddDepartemenPage .dd-table tbody tr:hover {
        background: #fdfdfc;
    }

    #ddDepartemenPage .dd-table tbody tr:last-child td {
        border-bottom: none;
    }

    #ddDepartemenPage .dd-empty {
        text-align: center;
        padding: 28px 16px;
        color: #6b7280;
        font-size: 14.5px;
    }

    @media (max-width: 768px) {
        #ddDepartemenPage .dd-panel {
            padding: 20px;
        }

        #ddDepartemenPage .dd-info-grid {
            grid-template-columns: 1fr;
            border-top: none;
        }

        #ddDepartemenPage .dd-info-key {
            padding: 12px 0 4px;
            border-bottom: none;
        }

        #ddDepartemenPage .dd-info-val {
            padding: 0 0 12px;
        }
    }
</style>
@endpush

@section('content')
<div id="ddDepartemenPage" class="dd-wrap">

    <div class="dd-panel">
        <div class="dd-head">
            <div>
                <h1>Detail Departemen</h1>
                <p>Informasi lengkap departemen dan jumlah karyawan aktif di dalamnya.</p>
            </div>

            <div class="dd-actions">
                <a href="{{ route('hrd.departemen.index') }}" class="dd-btn dd-btn-back">
                    Kembali
                </a>

                <a href="{{ route('hrd.departemen.edit', $departemen) }}" class="dd-btn dd-btn-edit">
                    Edit
                </a>
            </div>
        </div>

        <div class="dd-info-grid">
            <div class="dd-info-key">Nama Departemen</div>
            <div class="dd-info-val">{{ $departemen->nama_departemen }}</div>

            <div class="dd-info-key">Jumlah Karyawan</div>
            <div class="dd-info-val">
                <span class="dd-count-badge">
                    {{ $departemen->karyawans->count() }} orang
                </span>
            </div>

            <div class="dd-info-key">Deskripsi</div>
            <div class="dd-info-val">{{ $departemen->deskripsi ?: '-' }}</div>
        </div>
    </div>

    <div class="dd-panel">
        <div class="dd-head">
            <div>
                <h2>Karyawan dalam Departemen</h2>
                <p>Daftar karyawan yang terdaftar pada departemen ini.</p>
            </div>
        </div>

        <div class="dd-table-wrap">
            <table class="dd-table">
                <thead>
                    <tr>
                        <th style="width: 64px;">No</th>
                        <th style="width: 160px;">NIK</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departemen->karyawans as $karyawan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $karyawan->nik }}</td>
                            <td><strong>{{ $karyawan->nama }}</strong></td>
                            <td>{{ $karyawan->jabatan->nama_jabatan }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="dd-empty">
                                Belum ada karyawan di departemen ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection