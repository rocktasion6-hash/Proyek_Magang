@extends('layouts.hrd')

@section('title', 'Detail Skill')
@section('page_title', 'Detail Skill')

@push('styles')
<style>
    #dsSkillPage.ds-wrap {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        box-sizing: border-box;
    }

    #dsSkillPage .ds-panel {
        width: 100% !important;
        max-width: none !important;
        background: #ffffff;
        border: 1px solid #d8e0da;
        border-radius: 10px;
        padding: 28px 36px;
        margin-bottom: 20px;
        box-sizing: border-box;
    }

    #dsSkillPage .ds-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        padding-bottom: 16px;
        margin-bottom: 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    #dsSkillPage .ds-head h1 {
        margin: 0 0 4px;
        font-size: 24px;
        font-weight: 700;
        color: #2c4a3e;
        line-height: 1.25;
    }

    #dsSkillPage .ds-head h2 {
        margin: 0 0 4px;
        font-size: 18px;
        font-weight: 700;
        color: #2c4a3e;
        line-height: 1.3;
    }

    #dsSkillPage .ds-head p {
        margin: 0;
        font-size: 14px;
        color: #4b5563;
        line-height: 1.45;
    }

    #dsSkillPage .ds-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    #dsSkillPage .ds-btn {
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

    #dsSkillPage .ds-btn-back {
        background: #f4f6f4;
        color: #2c4a3e;
        border-color: #c6d2c9;
    }

    #dsSkillPage .ds-btn-back:hover,
    #dsSkillPage .ds-btn-back:active {
        background: #2c4a3e;
        color: #fdfdfc;
        border-color: #2c4a3e;
    }

    #dsSkillPage .ds-btn-edit {
        background: #e3b535;
        color: #111827;
        border-color: #e3b535;
        font-weight: 700;
    }

    #dsSkillPage .ds-btn-edit:hover,
    #dsSkillPage .ds-btn-edit:active {
        background: #cca024;
        color: #111827;
        border-color: #cca024;
    }

    #dsSkillPage .ds-info-grid {
        display: grid;
        grid-template-columns: 220px 1fr;
        border-top: 1px solid #edf0ee;
    }

    #dsSkillPage .ds-info-key {
        padding: 13px 16px 13px 0;
        font-size: 14px;
        font-weight: 700;
        color: #4b5563;
        border-bottom: 1px solid #edf0ee;
        vertical-align: top;
    }

    #dsSkillPage .ds-info-val {
        padding: 13px 0;
        font-size: 14.5px;
        font-weight: 600;
        color: #111827;
        border-bottom: 1px solid #edf0ee;
        vertical-align: top;
        word-break: break-word;
    }

    #dsSkillPage .ds-table-wrap {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #e2e8e4;
        border-radius: 8px;
    }

    #dsSkillPage .ds-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 720px;
    }

    #dsSkillPage .ds-table th {
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

    #dsSkillPage .ds-table th.ds-col-center,
    #dsSkillPage .ds-table td.ds-col-center {
        text-align: center;
    }

    #dsSkillPage .ds-table td {
        padding: 13px 16px;
        font-size: 14.5px;
        color: #1f2937;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    #dsSkillPage .ds-table tbody tr:hover {
        background: #fdfdfc;
    }

    #dsSkillPage .ds-table tbody tr:last-child td {
        border-bottom: none;
    }

    #dsSkillPage .ds-level-badge {
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

    #dsSkillPage .ds-note {
        background: #f4f6f4;
        color: #1f2937;
        border: 1px solid #d8e0da;
        border-left: 4px solid #e3b535;
        border-radius: 8px;
        padding: 13px 16px;
        margin-top: 16px;
        font-size: 14px;
        line-height: 1.55;
    }

    #dsSkillPage .ds-note strong {
        color: #2c4a3e;
    }

    #dsSkillPage .ds-empty {
        text-align: center;
        color: #6b7280;
        padding: 26px 16px;
        font-size: 14px;
    }

    @media (max-width: 768px) {
        #dsSkillPage .ds-panel {
            padding: 20px;
        }

        #dsSkillPage .ds-info-grid {
            grid-template-columns: 1fr;
            border-top: none;
        }

        #dsSkillPage .ds-info-key {
            padding: 12px 0 4px;
            border-bottom: none;
        }

        #dsSkillPage .ds-info-val {
            padding: 0 0 12px;
        }
    }
</style>
@endpush

@section('content')
<div id="dsSkillPage" class="ds-wrap">

    {{-- ===================================================== --}}
    {{-- INFORMASI SKILL --}}
    {{-- ===================================================== --}}
    <div class="ds-panel">
        <div class="ds-head">
            <div>
                <h1>Detail Skill</h1>
                <p>Informasi kompetensi, jabatan terkait, pemegang skill, dan bank soal.</p>
            </div>

            <div class="ds-actions">
                <a href="{{ route('hrd.skill.index') }}" class="ds-btn ds-btn-back">
                    Kembali
                </a>

                <a href="{{ route('hrd.skill.edit', $skill) }}" class="ds-btn ds-btn-edit">
                    Edit
                </a>
            </div>
        </div>

        <div class="ds-info-grid">
            <div class="ds-info-key">Nama Skill</div>
            <div class="ds-info-val">{{ $skill->nama_skill }}</div>

            <div class="ds-info-key">Deskripsi</div>
            <div class="ds-info-val">{{ $skill->deskripsi ?: '-' }}</div>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- JABATAN YANG MEMBUTUHKAN SKILL --}}
    {{-- ===================================================== --}}
    <div class="ds-panel">
        <div class="ds-head">
            <div>
                <h2>Jabatan yang Membutuhkan Skill</h2>
                <p>Daftar jabatan beserta level minimum skill yang dipersyaratkan.</p>
            </div>
        </div>

        <div class="ds-table-wrap">
            <table class="ds-table">
                <thead>
                    <tr>
                        <th style="width: 64px;">No</th>
                        <th>Jabatan</th>
                        <th style="width: 220px;" class="ds-col-center">Level Skill Dibutuhkan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($skill->jabatans as $jabatan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $jabatan->nama_jabatan }}</strong></td>
                            <td class="ds-col-center">
                                <span class="ds-level-badge">
                                    Level {{ $jabatan->pivot->level_dibutuhkan }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="ds-empty">
                                Belum ada jabatan yang membutuhkan skill ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="ds-note">
            Skill ini dibutuhkan oleh jabatan-jabatan di atas. Karyawan harus berada di jabatan tersebut untuk dapat memiliki skill ini.
            Contoh alur: <strong>Crew &rarr; Operator Loader Level 1</strong> (pemindahan jabatan) &rarr; peningkatan skill &rarr; <strong>Operator Loader Level 2</strong>.
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- KARYAWAN YANG MEMILIKI SKILL --}}
    {{-- ===================================================== --}}
    <div class="ds-panel">
        <div class="ds-head">
            <div>
                <h2>Karyawan yang Memiliki Skill</h2>
                <p>Daftar karyawan yang tercatat memiliki skill ini beserta levelnya.</p>
            </div>
        </div>

        <div class="ds-table-wrap">
            <table class="ds-table">
                <thead>
                    <tr>
                        <th style="width: 64px;">No</th>
                        <th style="width: 150px;">NIK</th>
                        <th>Nama</th>
                        <th>Departemen</th>
                        <th>Jabatan</th>
                        <th style="width: 140px;" class="ds-col-center">Level Skill</th>
                        <th style="width: 170px;" class="ds-col-center">Tanggal Penilaian</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($skill->karyawans as $karyawan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $karyawan->nik }}</td>
                            <td><strong>{{ $karyawan->nama }}</strong></td>
                            <td>{{ $karyawan->departemen->nama_departemen }}</td>
                            <td>{{ $karyawan->jabatan->nama_jabatan }}</td>
                            <td class="ds-col-center">
                                <span class="ds-level-badge">
                                    Level {{ $karyawan->pivot->level_skill }}
                                </span>
                            </td>
                            <td class="ds-col-center">
                                @if($karyawan->pivot->tanggal_penilaian)
                                    {{ \Carbon\Carbon::parse($karyawan->pivot->tanggal_penilaian)->format('d-m-Y') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="ds-empty">
                                Belum ada karyawan yang memiliki skill ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- BANK SOAL --}}
    {{-- ===================================================== --}}
    <div class="ds-panel">
        <div class="ds-head">
            <div>
                <h2>Bank Soal Menggunakan Skill Ini</h2>
                <p>Daftar soal assessment yang menguji skill ini.</p>
            </div>
        </div>

        <div class="ds-table-wrap">
            <table class="ds-table">
                <thead>
                    <tr>
                        <th style="width: 64px;">No</th>
                        <th>Pertanyaan</th>
                        <th>Jabatan</th>
                        <th style="width: 160px;" class="ds-col-center">Tipe</th>
                        <th style="width: 150px;" class="ds-col-center">Tingkat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($skill->soals as $soal)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $soal->pertanyaan }}</td>
                            <td><strong>{{ $soal->jabatan->nama_jabatan }}</strong></td>
                            <td class="ds-col-center">
                                {{ str_replace('_', ' ', ucfirst($soal->tipe_soal)) }}
                            </td>
                            <td class="ds-col-center">
                                {{ ucfirst($soal->tingkat_kesulitan) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="ds-empty">
                                Belum ada soal untuk skill ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection