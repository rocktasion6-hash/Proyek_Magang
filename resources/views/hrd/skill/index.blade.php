
@extends('layouts.hrd')

@section('title', 'Manajemen Skill')
@section('page_title', 'Manajemen Skill')

@push('styles')
<style>
    #msSkillPage.ms-page {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        box-sizing: border-box;
    }

    #msSkillPage .ms-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    #msSkillPage .ms-top h1 {
        margin: 0 0 4px;
        font-size: 24px;
        font-weight: 700;
        color: #2c4a3e;
    }

    #msSkillPage .ms-top p {
        margin: 0;
        font-size: 14.5px;
        color: #4b5563;
    }

    /* INFORMASI ALUR SKILL */
    #msSkillPage .ms-flow-box {
        background: #f4f6f4;
        color: #1f2937;
        border: 1px solid #d8e0da;
        border-left: 4px solid #e3b535;
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 20px;
        font-size: 14px;
        line-height: 1.65;
    }

    #msSkillPage .ms-flow-box strong {
        color: #2c4a3e;
    }

    #msSkillPage .ms-flow-box small {
        display: block;
        margin-top: 4px;
        font-size: 13px;
        color: #4b5563;
    }

    /* FILTER */
    #msSkillPage .ms-filter-box {
        background: #ffffff;
        border: 1px solid #e2e8e4;
        border-radius: 10px;
        padding: 18px 22px;
        margin-bottom: 20px;
    }

    #msSkillPage .ms-filter-row {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
    }

    #msSkillPage .ms-filter-row input[type="text"] {
        height: 44px;
        padding: 0 14px;
        font-size: 14.5px;
        color: #111827;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        box-sizing: border-box;
        min-width: 280px;
        flex: 1 1 280px;
    }

    #msSkillPage .ms-filter-row input[type="text"]:focus {
        outline: none;
        border-color: #4a7c59;
    }

    /* TOMBOL UMUM */
    #msSkillPage .ms-btn {
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
        transition: background 0.2s ease,
                    border-color 0.2s ease,
                    color 0.2s ease;
    }

    /* TOMBOL CARI */
    #msSkillPage .ms-btn-primary {
        background: #4a7c59;
        color: #fdfdfc;
        border-color: #4a7c59;
    }

    #msSkillPage .ms-btn-primary:hover,
    #msSkillPage .ms-btn-primary:active {
        background: #3b6649;
        color: #fdfdfc;
        border-color: #3b6649;
    }

    /* TOMBOL RESET */
    #msSkillPage .ms-btn-reset {
        background: #ffffff;
        color: #374151;
        border-color: #cbd5e1;
    }

    #msSkillPage .ms-btn-reset:hover,
    #msSkillPage .ms-btn-reset:active {
        background: #f4f6f4;
        color: #111827;
        border-color: #4a7c59;
    }

    #msSkillPage .ms-btn-sm {
        padding: 7px 14px;
        font-size: 13.5px;
        font-weight: 700;
        border-radius: 7px;
    }

    /* TOMBOL DETAIL JABATAN */
    #msSkillPage .ms-btn-detail {
        background: #f4f6f4;
        color: #2c4a3e;
        border-color: #cbd5e1;
    }

    #msSkillPage .ms-btn-detail:hover,
    #msSkillPage .ms-btn-detail:active {
        background: #4a7c59;
        color: #fdfdfc;
        border-color: #4a7c59;
    }

    /* TABEL */
    #msSkillPage .ms-table-wrap {
        background: #ffffff;
        border: 1px solid #e2e8e4;
        border-radius: 10px;
        overflow-x: auto;
    }

    #msSkillPage .ms-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 860px;
    }

    #msSkillPage .ms-table th {
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

    #msSkillPage .ms-table th.ms-col-center,
    #msSkillPage .ms-table td.ms-col-center {
        text-align: center;
    }

    #msSkillPage .ms-table td {
        padding: 14px 16px;
        font-size: 14.5px;
        color: #1f2937;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    #msSkillPage .ms-table tbody tr:hover {
        background: #fdfdfc;
    }

    #msSkillPage .ms-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* BADGE LEVEL SKILL */
    #msSkillPage .ms-level-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 6px;
        background: #f4f6f4;
        color: #2c4a3e;
        border: 1px solid #4a7c59;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.2;
        white-space: nowrap;
    }

    /* BADGE JUMLAH KARYAWAN */
    #msSkillPage .ms-count-badge {
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

    /* DATA KOSONG */
    #msSkillPage .ms-empty-row {
        text-align: center;
        padding: 34px 16px;
        color: #6b7280;
        font-size: 14.5px;
    }

    #msSkillPage .ms-empty-row a {
        color: #2c4a3e;
        font-weight: 700;
        text-decoration: underline;
    }

    /* PAGINATION */
    #msSkillPage .ms-pager {
        margin-top: 18px;
    }
</style>
@endpush

@section('content')
<div id="msSkillPage" class="ms-page">

    <div class="ms-top">
        <div>
            <h1>Manajemen Skill</h1>
            <p>Jalur peningkatan skill karyawan operasional (Level 2 – Level 5).</p>
        </div>
    </div>

    <div class="ms-flow-box">
        <strong>Alur Peningkatan Skill:</strong><br>
        Crew &rarr; <em>Pemindahan Jabatan</em> &rarr; Operator Loader <strong>Level 1</strong>
        &rarr; <em>Lulus Assessment</em> &rarr; Operator Loader <strong>Level 2</strong>
        &rarr; <em>Lulus Assessment</em> &rarr; ... &rarr; <strong>Level 5</strong> (Maks)
        <small>Setiap kenaikan level harus lulus assessment jabatan tujuan terlebih dahulu.</small>
    </div>

    <div class="ms-filter-box">
        <form method="GET" action="{{ route('hrd.skill.index') }}" class="ms-filter-row">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama jabatan..."
                autocomplete="off"
            >

            <button type="submit" class="ms-btn ms-btn-primary">
                Cari
            </button>

            @if(request()->filled('search'))
                <a href="{{ route('hrd.skill.index') }}" class="ms-btn ms-btn-reset">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <div class="ms-table-wrap">
        <table class="ms-table">
            <thead>
                <tr>
                    <th style="width: 64px;">No</th>
                    <th>Nama Jabatan</th>
                    <th style="width: 150px;" class="ms-col-center">Level Skill</th>
                    <th style="width: 160px;" class="ms-col-center">Standar Nilai</th>
                    <th style="width: 150px;" class="ms-col-center">Karyawan</th>
                    <th style="width: 160px;" class="ms-col-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jabatans as $jabatan)
                    <tr>
                        <td>{{ $jabatans->firstItem() + $loop->index }}</td>
                        <td><strong>{{ $jabatan->nama_jabatan }}</strong></td>
                        <td class="ms-col-center">
                            <span class="ms-level-badge">
                                Level {{ $jabatan->level_jabatan }}
                            </span>
                        </td>
                        <td class="ms-col-center">
                            <strong>{{ number_format($jabatan->standar_nilai, 2) }}</strong>
                        </td>
                        <td class="ms-col-center">
                            <span class="ms-count-badge">
                                {{ $jabatan->karyawans_count }} orang
                            </span>
                        </td>
                        <td class="ms-col-center">
                            <a
                                href="{{ route('hrd.jabatan.show', $jabatan) }}"
                                class="ms-btn ms-btn-sm ms-btn-detail"
                            >
                                Detail Jabatan
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="ms-empty-row">
                            Belum ada jabatan operasional level 2–5.
                            Tambahkan melalui <a href="{{ route('hrd.jabatan.create') }}">Manajemen Jabatan</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="ms-pager">
        {{ $jabatans->links() }}
    </div>

</div>
@endsection
