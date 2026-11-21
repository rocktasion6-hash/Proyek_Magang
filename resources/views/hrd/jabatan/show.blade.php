@extends('layouts.hrd')

@section('title', 'Detail Jabatan')
@section('page_title', 'Detail Jabatan')

@push('styles')
<style>
    #djJabatanPage.dj-wrap {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        box-sizing: border-box;
    }

    #djJabatanPage .dj-panel {
        width: 100% !important;
        max-width: none !important;
        background: #ffffff;
        border: 1px solid #d8e0da;
        border-radius: 10px;
        padding: 28px 36px;
        margin-bottom: 20px;
        box-sizing: border-box;
    }

    #djJabatanPage .dj-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        padding-bottom: 16px;
        margin-bottom: 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    #djJabatanPage .dj-head h1 {
        margin: 0 0 4px;
        font-size: 24px;
        font-weight: 700;
        color: #2c4a3e;
        line-height: 1.25;
    }

    #djJabatanPage .dj-head h2 {
        margin: 0 0 4px;
        font-size: 18px;
        font-weight: 700;
        color: #2c4a3e;
        line-height: 1.3;
    }

    #djJabatanPage .dj-head p {
        margin: 0;
        font-size: 14px;
        color: #4b5563;
        line-height: 1.45;
    }

    #djJabatanPage .dj-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    #djJabatanPage .dj-btn {
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

    #djJabatanPage .dj-btn-sm {
        padding: 7px 13px;
        font-size: 13px;
        font-weight: 700;
        border-radius: 7px;
    }

    #djJabatanPage .dj-btn-back {
        background: #f4f6f4;
        color: #2c4a3e;
        border-color: #c6d2c9;
    }

    #djJabatanPage .dj-btn-back:hover,
    #djJabatanPage .dj-btn-back:active {
        background: #2c4a3e;
        color: #fdfdfc;
        border-color: #2c4a3e;
    }

    #djJabatanPage .dj-btn-edit {
        background: #e3b535;
        color: #111827;
        border-color: #e3b535;
        font-weight: 700;
    }

    #djJabatanPage .dj-btn-edit:hover,
    #djJabatanPage .dj-btn-edit:active {
        background: #cca024;
        color: #111827;
        border-color: #cca024;
    }

    #djJabatanPage .dj-btn-save {
        height: 42px;
        padding: 0 18px;
        background: #2c4a3e;
        color: #fdfdfc;
        border-color: #2c4a3e;
        font-weight: 700;
    }

    #djJabatanPage .dj-btn-save:hover,
    #djJabatanPage .dj-btn-save:active {
        background: #4a7c59;
        color: #fdfdfc;
        border-color: #4a7c59;
    }

    #djJabatanPage .dj-btn-delete {
        background: #ffffff;
        color: #991b1b;
        border-color: #fecaca;
    }

    #djJabatanPage .dj-btn-delete:hover,
    #djJabatanPage .dj-btn-delete:active {
        background: #991b1b;
        color: #ffffff;
        border-color: #991b1b;
    }

    #djJabatanPage .dj-info-grid {
        display: grid;
        grid-template-columns: 220px 1fr;
        border-top: 1px solid #edf0ee;
    }

    #djJabatanPage .dj-info-key {
        padding: 13px 16px 13px 0;
        font-size: 14px;
        font-weight: 700;
        color: #4b5563;
        border-bottom: 1px solid #edf0ee;
        vertical-align: top;
    }

    #djJabatanPage .dj-info-val {
        padding: 13px 0;
        font-size: 14.5px;
        font-weight: 600;
        color: #111827;
        border-bottom: 1px solid #edf0ee;
        vertical-align: top;
        word-break: break-word;
    }

    #djJabatanPage .dj-tipe-badge {
        display: inline-block;
        padding: 4px 11px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.2;
        white-space: nowrap;
    }

    #djJabatanPage .dj-tipe-badge.struktural {
        background: #fdf6df;
        color: #2c4a3e;
        border: 1px solid #e3b535;
    }

    #djJabatanPage .dj-tipe-badge.operasional {
        background: #f4f6f4;
        color: #2c4a3e;
        border: 1px solid #4a7c59;
    }

    #djJabatanPage .dj-count-badge {
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

    #djJabatanPage .dj-status-badge {
        display: inline-block;
        padding: 4px 11px;
        border-radius: 6px;
        font-size: 12.5px;
        font-weight: 700;
        line-height: 1.2;
        white-space: nowrap;
    }

    #djJabatanPage .dj-status-badge.aktif {
        background: #2c4a3e;
        color: #fdfdfc;
    }

    #djJabatanPage .dj-status-badge.nonaktif {
        background: #991b1b;
        color: #ffffff;
    }

    #djJabatanPage .dj-form-skill {
        background: #f4f6f4;
        border: 1px solid #e2e8e4;
        padding: 18px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    #djJabatanPage .dj-form-grid {
        display: grid;
        grid-template-columns: 1fr 220px auto;
        gap: 14px;
        align-items: end;
    }

    #djJabatanPage .dj-input-group label {
        display: block;
        margin-bottom: 7px;
        font-size: 13.5px;
        font-weight: 700;
        color: #111827;
    }

    #djJabatanPage .dj-input-group select {
        width: 100%;
        height: 42px;
        padding: 0 12px;
        font-size: 14px;
        color: #111827;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        box-sizing: border-box;
    }

    #djJabatanPage .dj-input-group select:focus {
        outline: none;
        border-color: #2c4a3e;
    }

    #djJabatanPage .dj-table-wrap {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #e2e8e4;
        border-radius: 8px;
    }

    #djJabatanPage .dj-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 640px;
    }

    #djJabatanPage .dj-table th {
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

    #djJabatanPage .dj-table th.dj-col-center,
    #djJabatanPage .dj-table td.dj-col-center {
        text-align: center;
    }

    #djJabatanPage .dj-table td {
        padding: 13px 16px;
        font-size: 14.5px;
        color: #1f2937;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    #djJabatanPage .dj-table tbody tr:hover {
        background: #fdfdfc;
    }

    #djJabatanPage .dj-table tbody tr:last-child td {
        border-bottom: none;
    }

    #djJabatanPage .dj-empty {
        text-align: center;
        color: #6b7280;
        padding: 26px 16px;
        font-size: 14px;
    }

    @media (max-width: 820px) {
        #djJabatanPage .dj-form-grid {
            grid-template-columns: 1fr;
        }

        #djJabatanPage .dj-btn-save {
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        #djJabatanPage .dj-panel {
            padding: 20px;
        }

        #djJabatanPage .dj-info-grid {
            grid-template-columns: 1fr;
            border-top: none;
        }

        #djJabatanPage .dj-info-key {
            padding: 12px 0 4px;
            border-bottom: none;
        }

        #djJabatanPage .dj-info-val {
            padding: 0 0 12px;
        }
    }
</style>
@endpush

@section('content')
<div id="djJabatanPage" class="dj-wrap">

    {{-- ===================================================== --}}
    {{-- INFORMASI JABATAN --}}
    {{-- ===================================================== --}}
    <div class="dj-panel">
        <div class="dj-head">
            <div>
                <h1>Detail Jabatan</h1>
                <p>Informasi lengkap tipe, hierarki, standar nilai, dan kebutuhan jabatan.</p>
            </div>

            <div class="dj-actions">
                <a href="{{ route('hrd.jabatan.index') }}" class="dj-btn dj-btn-back">
                    Kembali
                </a>

                <a href="{{ route('hrd.jabatan.edit', $jabatan) }}" class="dj-btn dj-btn-edit">
                    Edit
                </a>
            </div>
        </div>

        <div class="dj-info-grid">
            <div class="dj-info-key">Nama Jabatan</div>
            <div class="dj-info-val">{{ $jabatan->nama_jabatan }}</div>

            <div class="dj-info-key">Tipe Jabatan</div>
            <div class="dj-info-val">
                @if($jabatan->tipe_jabatan === 'struktural')
                    <span class="dj-tipe-badge struktural">Struktural</span>
                @else
                    <span class="dj-tipe-badge operasional">Operasional</span>
                @endif
            </div>

            @if($jabatan->tipe_jabatan === 'struktural')
                <div class="dj-info-key">Urutan Struktural</div>
                <div class="dj-info-val">
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
                    {{ $jabatan->urutan_struktural }}
                    &mdash;
                    {{ $labelUrutan[$jabatan->urutan_struktural] ?? '-' }}
                </div>
            @endif

            @if($jabatan->tipe_jabatan !== 'struktural')
                <div class="dj-info-key">Level Jabatan</div>
                <div class="dj-info-val">
                    @if(mb_strtolower(trim($jabatan->nama_jabatan)) === 'crew')
                        Crew (Jabatan Awal)
                    @else
                        Level {{ $jabatan->level_jabatan }}
                    @endif
                </div>
            @endif

            <div class="dj-info-key">Standar Nilai</div>
            <div class="dj-info-val">{{ number_format($jabatan->standar_nilai, 2) }}</div>

            <div class="dj-info-key">Jumlah Karyawan</div>
            <div class="dj-info-val">
                <span class="dj-count-badge">
                    {{ $jabatan->karyawans->count() }} orang
                </span>
            </div>

            <div class="dj-info-key">Deskripsi</div>
            <div class="dj-info-val">{{ $jabatan->deskripsi ?: '-' }}</div>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- SKILL YANG DIBUTUHKAN --}}
    {{-- ===================================================== --}}
    @unless(mb_strtolower(trim($jabatan->nama_jabatan)) === 'crew')
        <div class="dj-panel">
            <div class="dj-head">
                <div>
                    <h2>Skill yang Dibutuhkan</h2>
                    <p>Tentukan skill dan level minimum yang dibutuhkan untuk jabatan ini.</p>
                </div>
            </div>

            @if($availableSkills->count() > 0)
                <form
                    method="POST"
                    action="{{ route('hrd.jabatan.skill.store', $jabatan) }}"
                    class="dj-form-skill"
                >
                    @csrf

                    <div class="dj-form-grid">
                        <div class="dj-input-group">
                            <label for="skill_id">Skill</label>
                            <select id="skill_id" name="skill_id" required>
                                <option value="">-- Pilih Skill --</option>
                                @foreach($availableSkills as $skill)
                                    <option value="{{ $skill->id }}">
                                        {{ $skill->nama_skill }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="dj-input-group">
                            <label for="level_dibutuhkan">Level Dibutuhkan</label>
                            <select id="level_dibutuhkan" name="level_dibutuhkan" required>
                                @for($level = 1; $level <= 5; $level++)
                                    <option value="{{ $level }}">
                                        Level {{ $level }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <button type="submit" class="dj-btn dj-btn-save">
                            + Tambah
                        </button>
                    </div>
                </form>
            @endif

            <div class="dj-table-wrap">
                <table class="dj-table">
                    <thead>
                        <tr>
                            <th style="width: 64px;">No</th>
                            <th>Skill</th>
                            <th style="width: 190px;">Level Dibutuhkan</th>
                            <th style="width: 130px;" class="dj-col-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jabatan->jabatanSkills as $jabatanSkill)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $jabatanSkill->skill->nama_skill }}</strong></td>
                                <td>Level {{ $jabatanSkill->level_dibutuhkan }}</td>
                                <td class="dj-col-center">
                                    <form
                                        method="POST"
                                        action="{{ route('hrd.jabatan.skill.destroy', [
                                            'jabatan' => $jabatan,
                                            'jabatanSkill' => $jabatanSkill
                                        ]) }}"
                                        onsubmit="return confirm('Hapus skill ini dari jabatan?')"
                                        style="display:inline;"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dj-btn dj-btn-sm dj-btn-delete">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="dj-empty">
                                    Belum ada skill yang dibutuhkan untuk jabatan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endunless

    {{-- ===================================================== --}}
    {{-- KARYAWAN PADA JABATAN INI --}}
    {{-- ===================================================== --}}
    <div class="dj-panel">
        <div class="dj-head">
            <div>
                <h2>Karyawan pada Jabatan Ini</h2>
                <p>Daftar karyawan yang saat ini menempati jabatan ini.</p>
            </div>
        </div>

        <div class="dj-table-wrap">
            <table class="dj-table">
                <thead>
                    <tr>
                        <th style="width: 64px;">No</th>
                        <th style="width: 160px;">NIK</th>
                        <th>Nama</th>
                        <th>Departemen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jabatan->karyawans as $karyawan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $karyawan->nik }}</td>
                            <td><strong>{{ $karyawan->nama }}</strong></td>
                            <td>{{ $karyawan->departemen->nama_departemen }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="dj-empty">
                                Belum ada karyawan pada jabatan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- ASSESSMENT YANG MENGGUNAKAN JABATAN INI --}}
    {{-- ===================================================== --}}
    <div class="dj-panel">
        <div class="dj-head">
            <div>
                <h2>Assessment yang Menggunakan Jabatan Ini</h2>
                <p>Daftar assessment yang mengacu pada jabatan ini.</p>
            </div>
        </div>

        <div class="dj-table-wrap">
            <table class="dj-table">
                <thead>
                    <tr>
                        <th style="width: 64px;">No</th>
                        <th>Nama Assessment</th>
                        <th style="width: 150px;" class="dj-col-center">Standar</th>
                        <th style="width: 150px;" class="dj-col-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jabatan->assessments as $assessment)
                        @php
                            $stLower = mb_strtolower(trim($assessment->status));
                            $stClass = in_array($stLower, ['nonaktif', 'non-aktif', 'tidak aktif', 'inactive']) ? 'nonaktif' : 'aktif';
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $assessment->nama_assessment }}</strong></td>
                            <td class="dj-col-center">{{ number_format($assessment->standar_nilai, 2) }}</td>
                            <td class="dj-col-center">
                                <span class="dj-status-badge {{ $stClass }}">
                                    {{ ucfirst($assessment->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="dj-empty">
                                Belum ada assessment untuk jabatan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection