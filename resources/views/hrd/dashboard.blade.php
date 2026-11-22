
@extends('layouts.hrd')

@section('title', 'Dashboard HRD')
@section('page_title', 'Dashboard')

@push('styles')
<style>
    /* =========================================
       DASHBOARD UTAMA
    ========================================= */
    #hrdDashboard {
        --primary: #438452;
        --primary-hover: #356c42;
        --dark-green: #214b38;
        --yellow: #e8c94e;
        --cream: #f5f6ef;
        --border: #dde5dc;
        --text: #26352c;
        --muted: #6b756d;

        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        box-sizing: border-box;
        color: var(--text);
    }

    #hrdDashboard *,
    #hrdDashboard *::before,
    #hrdDashboard *::after {
        box-sizing: border-box;
    }

    /* =========================================
       HEADER
    ========================================= */
    #hrdDashboard .db-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 24px;
        padding: 23px 28px;
        background: #edf3eb;
        border: 1px solid #d5e2d4;
        border-left: 5px solid var(--primary);
        border-radius: 10px;
    }

    #hrdDashboard .db-header h1 {
        margin: 0 0 6px;
        color: var(--dark-green);
        font-size: 24px;
        font-weight: 700;
        line-height: 1.3;
    }

    #hrdDashboard .db-header p {
        margin: 0;
        color: #526557;
        font-size: 14px;
        line-height: 1.5;
    }

    #hrdDashboard .db-date {
        padding: 10px 15px;
        background: #ffffff;
        border: 1px solid #ceddcc;
        border-radius: 7px;
        color: var(--dark-green);
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
    }

    /* =========================================
       SECTION
    ========================================= */
    #hrdDashboard .db-section {
        margin-bottom: 24px;
    }

    #hrdDashboard .db-section-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        margin-bottom: 15px;
        padding-left: 13px;
        border-left: 4px solid var(--primary);
    }

    #hrdDashboard .db-section-heading h2 {
        margin: 0 0 4px;
        color: var(--dark-green);
        font-size: 17px;
        font-weight: 700;
    }

    #hrdDashboard .db-section-heading p {
        margin: 0;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.5;
    }

    /* =========================================
       KARTU STATISTIK
    ========================================= */
    #hrdDashboard .db-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    #hrdDashboard .db-stat {
        min-width: 0;
        min-height: 146px;
        padding: 22px 24px;
        border: 1px solid;
        border-radius: 10px;
        position: relative;
        overflow: hidden;
    }

    #hrdDashboard .db-stat::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }

    /* TOTAL KARYAWAN */
    #hrdDashboard .db-stat-karyawan {
        background: #eaf3e9;
        border-color: #cce0cb;
    }

    #hrdDashboard .db-stat-karyawan::before {
        background: #438452;
    }

    /* TOTAL JABATAN */
    #hrdDashboard .db-stat-jabatan {
        background: #f0f4ed;
        border-color: #d9e3d4;
    }

    #hrdDashboard .db-stat-jabatan::before {
        background: #214b38;
    }

    /* TOTAL SKILL */
    #hrdDashboard .db-stat-skill {
        background: #fdf7df;
        border-color: #f0e4af;
    }

    #hrdDashboard .db-stat-skill::before {
        background: #e8c94e;
    }

    /* ASSESSMENT AKTIF */
    #hrdDashboard .db-stat-assessment {
        background: #f5f6ef;
        border-color: #dfe4d7;
    }

    #hrdDashboard .db-stat-assessment::before {
        background: #438452;
    }

    #hrdDashboard .db-stat-label {
        margin: 0 0 15px;
        color: #455b49;
        font-size: 13px;
        font-weight: 700;
    }

    #hrdDashboard .db-stat-value {
        margin: 0 0 9px;
        color: var(--dark-green);
        font-size: 34px;
        font-weight: 800;
        line-height: 1.1;
        font-variant-numeric: tabular-nums;
    }

    #hrdDashboard .db-stat-description {
        margin: 0;
        color: #66766a;
        font-size: 12px;
        line-height: 1.5;
    }

    /* =========================================
       PANEL MONITORING
    ========================================= */
    #hrdDashboard .db-panels {
        display: grid;
        grid-template-columns: minmax(0, 1.3fr) minmax(0, 1fr);
        gap: 16px;
    }

    #hrdDashboard .db-panel {
        min-width: 0;
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 10px;
        overflow: hidden;
    }

    #hrdDashboard .db-panel-header {
        padding: 17px 22px;
        background: var(--cream);
        border-bottom: 1px solid var(--border);
    }

    #hrdDashboard .db-panel-header h3 {
        margin: 0 0 5px;
        color: var(--dark-green);
        font-size: 15px;
        font-weight: 700;
    }

    #hrdDashboard .db-panel-header p {
        margin: 0;
        color: #657367;
        font-size: 12.5px;
        line-height: 1.5;
    }

    #hrdDashboard .db-panel-body {
        padding: 24px;
    }

    /* =========================================
       PROGRESS ASSESSMENT
    ========================================= */
    #hrdDashboard .db-progress-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 14px;
        margin-bottom: 18px;
    }

    #hrdDashboard .db-progress-label {
        display: block;
        margin-bottom: 8px;
        color: var(--muted);
        font-size: 13px;
    }

    #hrdDashboard .db-progress-value {
        display: block;
        color: var(--dark-green);
        font-size: 32px;
        font-weight: 800;
        line-height: 1.2;
    }

    #hrdDashboard .db-progress-meta {
        color: var(--primary);
        font-size: 13px;
        font-weight: 700;
        text-align: right;
    }

    #hrdDashboard .db-progress-track {
        width: 100%;
        height: 11px;
        background: #e6ede4;
        border-radius: 20px;
        overflow: hidden;
    }

    #hrdDashboard .db-progress-fill {
        height: 100%;
        background: var(--primary);
        border-radius: 20px;
    }

    #hrdDashboard .db-progress-note {
        margin: 11px 0 0;
        color: var(--muted);
        font-size: 12px;
        line-height: 1.5;
    }

    /* RINGKASAN TAMBAHAN */
    #hrdDashboard .db-summary {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #e5e9e3;
    }

    #hrdDashboard .db-summary-item {
        padding: 17px 18px;
        border: 1px solid #d5e4d3;
        border-radius: 8px;
        background: #edf5ec;
    }

    #hrdDashboard .db-summary-item:nth-child(2) {
        background: #f5f6ef;
        border-color: #e1e4d8;
    }

    #hrdDashboard .db-summary-item span {
        display: block;
        margin-bottom: 8px;
        color: #607165;
        font-size: 12px;
    }

    #hrdDashboard .db-summary-item strong {
        color: var(--dark-green);
        font-size: 23px;
        font-weight: 800;
    }

    /* =========================================
       PENGAJUAN MENUNGGU
    ========================================= */
    #hrdDashboard .db-pending {
        border-color: #eadb99;
        background: #fffdf4;
    }

    #hrdDashboard .db-pending .db-panel-header {
        background: #f9efc4;
        border-bottom-color: #eadb99;
    }

    #hrdDashboard .db-pending .db-panel-header h3 {
        color: #635018;
    }

    #hrdDashboard .db-pending .db-panel-header p {
        color: #78692e;
    }

    #hrdDashboard .db-pending-label {
        display: block;
        margin-bottom: 10px;
        color: #7d703b;
        font-size: 13px;
        font-weight: 600;
    }

    #hrdDashboard .db-pending-value {
        margin: 0 0 14px;
        color: #8b7026;
        font-size: 40px;
        font-weight: 800;
        line-height: 1.2;
    }

    #hrdDashboard .db-pending-value small {
        color: #857a53;
        font-size: 13px;
        font-weight: 400;
    }

    #hrdDashboard .db-pending-description {
        margin: 0;
        color: #76705d;
        font-size: 13px;
        line-height: 1.7;
    }

    #hrdDashboard .db-pending-footer {
        margin-top: 24px;
        padding-top: 18px;
        border-top: 1px solid #e9dfba;
    }

    /* =========================================
       TOMBOL
    ========================================= */
    #hrdDashboard .db-btn {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        padding: 10px 17px;
        background: var(--primary);
        color: #ffffff;
        border: 1px solid var(--primary);
        border-radius: 7px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.2s ease,
                    border-color 0.2s ease;
    }

    #hrdDashboard .db-btn:hover {
        background: var(--primary-hover);
        border-color: var(--primary-hover);
        color: #ffffff;
    }

    /* =========================================
       AKSES CEPAT
    ========================================= */
    #hrdDashboard .db-shortcuts {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    #hrdDashboard .db-shortcut {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        padding: 19px 21px;
        background: #ffffff;
        border: 1px solid var(--border);
        border-left: 4px solid var(--primary);
        border-radius: 9px;
        text-decoration: none;
        transition: background 0.2s ease,
                    border-color 0.2s ease;
    }

    #hrdDashboard .db-shortcut:nth-child(2) {
        border-left-color: #214b38;
    }

    #hrdDashboard .db-shortcut:nth-child(3) {
        border-left-color: #e8c94e;
    }

    #hrdDashboard .db-shortcut:hover {
        background: var(--cream);
        border-color: #bdd3bd;
    }

    #hrdDashboard .db-shortcut strong {
        display: block;
        margin-bottom: 6px;
        color: var(--dark-green);
        font-size: 14px;
        font-weight: 700;
    }

    #hrdDashboard .db-shortcut small {
        display: block;
        color: var(--muted);
        font-size: 12px;
        line-height: 1.5;
    }

    #hrdDashboard .db-shortcut-arrow {
        color: var(--primary);
        font-size: 20px;
        font-weight: 700;
        flex-shrink: 0;
    }

    /* =========================================
       TABEL ASSESSMENT
    ========================================= */
    #hrdDashboard .db-table-panel {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 10px;
        overflow: hidden;
    }

    #hrdDashboard .db-table-panel-header {
        padding: 18px 22px;
        background: var(--cream);
        border-bottom: 1px solid var(--border);
    }

    #hrdDashboard .db-table-panel-header h3 {
        margin: 0 0 5px;
        color: var(--dark-green);
        font-size: 15px;
        font-weight: 700;
    }

    #hrdDashboard .db-table-panel-header p {
        margin: 0;
        color: #657367;
        font-size: 12.5px;
    }

    #hrdDashboard .db-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    #hrdDashboard .db-table {
        width: 100%;
        min-width: 830px;
        border-collapse: collapse;
    }

    #hrdDashboard .db-table th {
        padding: 14px 18px;
        background: #f5f8f3;
        color: var(--dark-green);
        border-bottom: 1.5px solid var(--border);
        text-align: left;
        font-size: 12.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        white-space: nowrap;
    }

    #hrdDashboard .db-table td {
        padding: 16px 18px;
        color: #374151;
        border-bottom: 1px solid #e7ece6;
        font-size: 13.5px;
        line-height: 1.5;
        vertical-align: middle;
    }

    #hrdDashboard .db-table tbody tr:nth-child(even) {
        background: #fcfdfb;
    }

    #hrdDashboard .db-table tbody tr:hover {
        background: #f0f6ef;
    }

    #hrdDashboard .db-table tbody tr:last-child td {
        border-bottom: none;
    }

    #hrdDashboard .db-table-name {
        color: var(--dark-green);
        font-weight: 700;
    }

    /* =========================================
       STATUS
    ========================================= */
    #hrdDashboard .db-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    #hrdDashboard .db-badge.aktif {
        background: #e9f3e7;
        color: #214b38;
        border: 1px solid #c7dec9;
    }

    #hrdDashboard .db-badge.draft {
        background: #f1f2ed;
        color: #606a61;
        border: 1px solid #dfe4db;
    }

    #hrdDashboard .db-badge.selesai {
        background: #fbf3cf;
        color: #82671f;
        border: 1px solid #e9da94;
    }

    #hrdDashboard .db-badge.dibatalkan {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    #hrdDashboard .db-empty {
        padding: 35px 18px !important;
        color: var(--muted) !important;
        text-align: center;
    }

    /* =========================================
       RESPONSIVE
    ========================================= */
    @media (max-width: 1100px) {
        #hrdDashboard .db-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        #hrdDashboard .db-panels {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        #hrdDashboard .db-shortcuts {
            grid-template-columns: 1fr;
        }

        #hrdDashboard .db-header {
            padding: 20px;
        }
    }

    @media (max-width: 540px) {
        #hrdDashboard .db-stats {
            grid-template-columns: 1fr;
        }

        #hrdDashboard .db-stat {
            min-height: auto;
            padding: 19px;
        }

        #hrdDashboard .db-panel-body {
            padding: 18px;
        }

        #hrdDashboard .db-summary {
            gap: 10px;
        }

        #hrdDashboard .db-summary-item {
            padding: 13px;
        }

        #hrdDashboard .db-progress-heading {
            flex-direction: column;
            align-items: flex-start;
        }

        #hrdDashboard .db-progress-meta {
            text-align: left;
        }
    }
</style>
@endpush

@section('content')

@php
    $totalKaryawanAman = max((int) $totalKaryawan, 0);
    $jumlahLulusAman = max((int) $karyawanLulus, 0);

    $persentaseLulus = $totalKaryawanAman > 0
        ? min(
            round(($jumlahLulusAman / $totalKaryawanAman) * 100, 1),
            100
        )
        : 0;
@endphp

<div id="hrdDashboard">

    {{-- HEADER DASHBOARD --}}
    <div class="db-header">
        <div>
            <h1>Dashboard HRD</h1>
            <p>
                Ringkasan data karyawan, jabatan, skill,
                dan aktivitas penilaian.
            </p>
        </div>

        <div class="db-date">
            {{ now()->translatedFormat('d F Y') }}
        </div>
    </div>

    {{-- RINGKASAN DATA --}}
    <section class="db-section">

        <div class="db-section-heading">
            <div>
                <h2>Ringkasan Data</h2>
                <p>Informasi utama yang tercatat dalam sistem HRD.</p>
            </div>
        </div>

        <div class="db-stats">

            <div class="db-stat db-stat-karyawan">
                <p class="db-stat-label">Total Karyawan</p>
                <p class="db-stat-value">
                    {{ number_format($totalKaryawan, 0, ',', '.') }}
                </p>
                <p class="db-stat-description">
                    Karyawan terdaftar dalam sistem
                </p>
            </div>

            <div class="db-stat db-stat-jabatan">
                <p class="db-stat-label">Total Jabatan</p>
                <p class="db-stat-value">
                    {{ number_format($totalJabatan, 0, ',', '.') }}
                </p>
                <p class="db-stat-description">
                    Jabatan yang tersedia
                </p>
            </div>

            <div class="db-stat db-stat-skill">
                <p class="db-stat-label">Total Skill</p>
                <p class="db-stat-value">
                    {{ number_format($totalSkill, 0, ',', '.') }}
                </p>
                <p class="db-stat-description">
                    Kompetensi yang tercatat
                </p>
            </div>

            <div class="db-stat db-stat-assessment">
                <p class="db-stat-label">Assessment Aktif</p>
                <p class="db-stat-value">
                    {{ number_format($assessmentAktif, 0, ',', '.') }}
                </p>
                <p class="db-stat-description">
                    Assessment berstatus aktif
                </p>
            </div>

        </div>
    </section>

    {{-- MONITORING --}}
    <section class="db-section">

        <div class="db-section-heading">
            <div>
                <h2>Monitoring & Evaluasi</h2>
                <p>
                    Ringkasan hasil assessment dan pengajuan karyawan.
                </p>
            </div>
        </div>

        <div class="db-panels">

            {{-- HASIL ASSESSMENT --}}
            <div class="db-panel">

                <div class="db-panel-header">
                    <h3>Hasil Assessment Karyawan</h3>
                    <p>
                        Perbandingan karyawan lulus
                        dengan total karyawan terdaftar.
                    </p>
                </div>

                <div class="db-panel-body">

                    <div class="db-progress-heading">
                        <div>
                            <span class="db-progress-label">
                                Karyawan Lulus Assessment
                            </span>

                            <strong class="db-progress-value">
                                {{ number_format($persentaseLulus, 1, ',', '.') }}%
                            </strong>
                        </div>

                        <div class="db-progress-meta">
                            {{ number_format($karyawanLulus, 0, ',', '.') }}
                            dari
                            {{ number_format($totalKaryawan, 0, ',', '.') }}
                            karyawan
                        </div>
                    </div>

                    <div
                        class="db-progress-track"
                        role="progressbar"
                        aria-label="Persentase karyawan lulus assessment"
                        aria-valuemin="0"
                        aria-valuemax="100"
                        aria-valuenow="{{ $persentaseLulus }}"
                    >
                        <div
                            class="db-progress-fill"
                            style="width: {{ $persentaseLulus }}%;"
                        ></div>
                    </div>

                    <p class="db-progress-note">
                        Persentase berdasarkan jumlah karyawan
                        yang tercatat lulus assessment.
                    </p>

                    <div class="db-summary">

                        <div class="db-summary-item">
                            <span>Karyawan Lulus</span>
                            <strong>
                                {{ number_format($karyawanLulus, 0, ',', '.') }}
                            </strong>
                        </div>

                        <div class="db-summary-item">
                            <span>Total Departemen</span>
                            <strong>
                                {{ number_format($totalDepartemen, 0, ',', '.') }}
                            </strong>
                        </div>

                    </div>

                </div>
            </div>

            {{-- PENGAJUAN MENUNGGU --}}
            <div class="db-panel db-pending">

                <div class="db-panel-header">
                    <h3>Pengajuan Menunggu Verifikasi</h3>
                    <p>Pengajuan yang memerlukan tindak lanjut HRD.</p>
                </div>

                <div class="db-panel-body">

                    <span class="db-pending-label">
                        Jumlah Pengajuan Menunggu
                    </span>

                    <p class="db-pending-value">
                        {{ number_format($pengajuanMenunggu, 0, ',', '.') }}
                        <small>pengajuan</small>
                    </p>

                    <p class="db-pending-description">
                        Pengajuan yang masih memerlukan
                        pemeriksaan atau keputusan lebih lanjut
                        dari tim HRD.
                    </p>

                    <div class="db-pending-footer">
                        <a
                            href="{{ route('hrd.peningkatan-skill.index') }}"
                            class="db-btn"
                        >
                            Lihat Pengajuan
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </section>

    {{-- AKSES CEPAT --}}
    <section class="db-section">

        <div class="db-section-heading">
            <div>
                <h2>Akses Cepat</h2>
                <p>Navigasi ke fitur manajemen utama.</p>
            </div>
        </div>

        <div class="db-shortcuts">

            <a
                href="{{ route('hrd.jabatan.index') }}"
                class="db-shortcut"
            >
                <div>
                    <strong>Manajemen Jabatan</strong>
                    <small>Kelola struktur dan level jabatan.</small>
                </div>
                <span class="db-shortcut-arrow">&rarr;</span>
            </a>

            <a
                href="{{ route('hrd.skill.index') }}"
                class="db-shortcut"
            >
                <div>
                    <strong>Manajemen Skill</strong>
                    <small>Kelola kompetensi karyawan.</small>
                </div>
                <span class="db-shortcut-arrow">&rarr;</span>
            </a>

            <a
                href="{{ route('hrd.peningkatan-skill.index') }}"
                class="db-shortcut"
            >
                <div>
                    <strong>Peningkatan Skill</strong>
                    <small>Kelola pengajuan peningkatan skill.</small>
                </div>
                <span class="db-shortcut-arrow">&rarr;</span>
            </a>

        </div>
    </section>

    {{-- ASSESSMENT TERBARU --}}
    <section class="db-section">

        <div class="db-table-panel">

            <div class="db-table-panel-header">
                <h3>Assessment Terbaru</h3>
                <p>
                    Daftar assessment terbaru yang tercatat
                    dalam sistem.
                </p>
            </div>

            <div class="db-table-wrap">

                <table class="db-table">

                    <thead>
                        <tr>
                            <th>Nama Assessment</th>
                            <th>Jabatan</th>
                            <th>Bank Soal</th>
                            <th>Standar Nilai</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($assessmentTerbaru as $assessment)
                            <tr>
                                <td>
                                    <span class="db-table-name">
                                        {{ $assessment->nama_assessment }}
                                    </span>
                                </td>

                                <td>
                                    {{ $assessment->jabatan?->nama_jabatan ?? '-' }}
                                </td>

                                <td>
                                    {{ $assessment->soalUpload?->nama_file_tampilan ?? '-' }}
                                </td>

                                <td>
                                    <strong>
                                        {{ number_format($assessment->standar_nilai, 0) }}
                                    </strong>
                                </td>

                                <td>
                                    @if($assessment->status === 'aktif')
                                        <span class="db-badge aktif">
                                            Aktif
                                        </span>
                                    @elseif($assessment->status === 'draft')
                                        <span class="db-badge draft">
                                            Draft
                                        </span>
                                    @elseif($assessment->status === 'selesai')
                                        <span class="db-badge selesai">
                                            Selesai
                                        </span>
                                    @else
                                        <span class="db-badge dibatalkan">
                                            Dibatalkan
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="db-empty">
                                    Belum ada data assessment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>

            </div>
        </div>

    </section>

</div>

@endsection
