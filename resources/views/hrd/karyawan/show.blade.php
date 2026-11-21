@extends('layouts.hrd')

@section('title', 'Detail Karyawan')
@section('page_title', 'Detail Karyawan')

@push('styles')
<style>
    .dk-wrap {
        width: 100%;
        max-width: 100%;
    }

    .dk-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .dk-bar-right {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .dk-panel {
        background: #ffffff;
        border: 1px solid #e2e8e4;
        border-radius: 10px;
        padding: 24px 28px;
        margin-bottom: 20px;
    }

    .dk-panel-head {
        padding-bottom: 14px;
        margin-bottom: 18px;
        border-bottom: 1px solid #e5e7eb;
    }

    .dk-panel-head h1 {
        margin: 0 0 4px;
        font-size: 22px;
        font-weight: 700;
        color: #2c4a3e;
    }

    .dk-panel-head h2 {
        margin: 0 0 4px;
        font-size: 18px;
        font-weight: 700;
        color: #2c4a3e;
    }

    .dk-panel-head p {
        margin: 0;
        font-size: 13.5px;
        color: #6b7280;
    }

    .dk-sections-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 24px;
        align-items: start;
    }

    .dk-box {
        background: #fdfdfc;
        border: 1px solid #e2e8e4;
        border-radius: 8px;
        padding: 18px 20px;
    }

    .dk-box-title {
        background: #f4f6f4;
        border-left: 4px solid #4a7c59;
        border-radius: 6px;
        padding: 9px 14px;
        margin: 0 0 14px;
        font-size: 14px;
        font-weight: 700;
        color: #2c4a3e;
    }

    .dk-kv-table {
        width: 100%;
        border-collapse: collapse;
    }

    .dk-kv-table tr {
        border-bottom: 1px solid #edf0ee;
    }

    .dk-kv-table tr:last-child {
        border-bottom: none;
    }

    .dk-kv-table th {
        width: 155px;
        padding: 11px 14px 11px 0;
        font-size: 13.5px;
        font-weight: 700;
        color: #4b5563;
        text-align: left;
        vertical-align: top;
    }

    .dk-kv-table td {
        padding: 11px 0;
        font-size: 14px;
        font-weight: 600;
        color: #111827;
        text-align: left;
        vertical-align: top;
        word-break: break-word;
    }

    .dk-jabatan-sub {
        display: block;
        margin-top: 3px;
        font-size: 12.5px;
        font-weight: 500;
        color: #6b7280;
    }

    .dk-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 16px;
        border-radius: 8px;
        border: 1.5px solid transparent;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        line-height: 1.2;
        white-space: nowrap;
    }

    .dk-btn-sm {
        padding: 7px 13px;
        font-size: 13px;
        border-radius: 7px;
    }

    .dk-btn-back {
        background: #f4f6f4;
        color: #2c4a3e;
        border-color: #cbd5e1;
    }

    .dk-btn-back:hover {
        background: #e2e8e4;
    }

    .dk-btn-edit {
        background: #e3b535;
        color: #111827;
        border-color: #e3b535;
    }

    .dk-btn-edit:hover {
        background: #cca024;
        border-color: #cca024;
    }

    .dk-btn-history {
        background: #2c4a3e;
        color: #fdfdfc;
        border-color: #2c4a3e;
    }

    .dk-btn-history:hover {
        background: #4a7c59;
        border-color: #4a7c59;
    }

    .dk-btn-save {
        height: 42px;
        padding: 0 18px;
        background: #2c4a3e;
        color: #fdfdfc;
        border-color: #2c4a3e;
    }

    .dk-btn-save:hover {
        background: #4a7c59;
        border-color: #4a7c59;
    }

    .dk-btn-delete {
        background: #991b1b;
        color: #ffffff;
        border-color: #991b1b;
    }

    .dk-btn-delete:hover {
        background: #7f1d1d;
        border-color: #7f1d1d;
    }

    .dk-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 12.5px;
        font-weight: 700;
        line-height: 1.2;
    }

    .dk-badge.aktif {
        background: #2c4a3e;
        color: #fdfdfc;
    }

    .dk-badge.nonaktif {
        background: #991b1b;
        color: #ffffff;
    }

    .dk-form-skill {
        background: #f4f6f4;
        border: 1px solid #e2e8e4;
        padding: 18px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .dk-form-grid {
        display: grid;
        grid-template-columns: 1fr 180px 180px auto;
        gap: 14px;
        align-items: end;
    }

    .dk-input-group label {
        display: block;
        margin-bottom: 7px;
        font-size: 13.5px;
        font-weight: 700;
        color: #111827;
    }

    .dk-input-group select,
    .dk-input-group input[type="date"] {
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

    .dk-input-group select:focus,
    .dk-input-group input[type="date"]:focus {
        outline: none;
        border-color: #2c4a3e;
    }

    .dk-table-box {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #e2e8e4;
        border-radius: 8px;
    }

    .dk-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 640px;
    }

    .dk-table th {
        background: #f4f6f4;
        color: #2c4a3e;
        font-size: 12.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 12px 14px;
        border-bottom: 1.5px solid #d8e0da;
        text-align: left;
    }

    .dk-table td {
        padding: 13px 14px;
        font-size: 14px;
        color: #1f2937;
        border-bottom: 1px solid #e5e7eb;
        text-align: left;
        vertical-align: middle;
    }

    .dk-table tbody tr:hover {
        background: #fdfdfc;
    }

    .dk-table tbody tr:last-child td {
        border-bottom: none;
    }

    .dk-empty {
        text-align: center;
        color: #6b7280;
        padding: 26px 16px;
        font-size: 14px;
    }

    @media (max-width: 960px) {
        .dk-sections-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 820px) {
        .dk-form-grid {
            grid-template-columns: 1fr;
        }

        .dk-btn-save {
            width: 100%;
        }
    }

    @media (max-width: 540px) {
        .dk-kv-table th,
        .dk-kv-table td {
            display: block;
            width: 100%;
            padding: 4px 0;
        }

        .dk-kv-table tr {
            padding: 8px 0;
            display: block;
        }

        .dk-panel {
            padding: 18px;
        }
    }
</style>
@endpush

@section('content')
<div class="dk-wrap">

    <div class="dk-bar">
        <a href="{{ route('hrd.karyawan.index') }}" class="dk-btn dk-btn-back">
            Kembali
        </a>

        <div class="dk-bar-right">
            <a href="{{ route('hrd.karyawan.edit', $karyawan) }}" class="dk-btn dk-btn-edit">
                Edit Data
            </a>

            <a href="{{ route('hrd.riwayat.show', $karyawan) }}" class="dk-btn dk-btn-history">
                Lihat Riwayat Lengkap
            </a>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- IDENTITAS KARYAWAN --}}
    {{-- ===================================================== --}}
    <div class="dk-panel">
        <div class="dk-panel-head">
            <h1>Detail Karyawan</h1>
            <p>Informasi profil, akun login dan posisi kepegawaian karyawan.</p>
        </div>

        <div class="dk-sections-grid">
            <div class="dk-box">
                <div class="dk-box-title">Profil &amp; Akun</div>
                <table class="dk-kv-table">
                    <tr>
                        <th>NIK</th>
                        <td>{{ $karyawan->nik }}</td>
                    </tr>
                    <tr>
                        <th>Nama Karyawan</th>
                        <td>{{ $karyawan->nama }}</td>
                    </tr>
                    <tr>
                        <th>Username</th>
                        <td>{{ $karyawan->user->username ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $karyawan->user->email ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            <div class="dk-box">
                <div class="dk-box-title">Data Pekerjaan</div>
                <table class="dk-kv-table">
                    <tr>
                        <th>PT/Perusahaan</th>
                        <td>{{ $karyawan->pt ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Departemen</th>
                        <td>{{ $karyawan->departemen->nama_departemen }}</td>
                    </tr>
                    <tr>
                        <th>Jabatan</th>
                        <td>
                            {{ $karyawan->jabatan->nama_jabatan }}
                            <span class="dk-jabatan-sub">
                                @if(mb_strtolower(trim($karyawan->jabatan->nama_jabatan)) === 'crew')
                                    Jabatan Awal
                                @else
                                    Level {{ $karyawan->jabatan->level_jabatan }}
                                @endif
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal Masuk</th>
                        <td>{{ \Carbon\Carbon::parse($karyawan->tanggal_masuk)->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="dk-badge {{ $karyawan->status }}">
                                {{ ucfirst($karyawan->status) }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- SKILL SAAT INI --}}
    {{-- ===================================================== --}}
    @unless($isCrew)
        <div class="dk-panel">
            <div class="dk-panel-head">
                <h2>Skill Saat Ini</h2>
                <p>Kelola skill yang dimiliki karyawan beserta level kompetensinya.</p>
            </div>

            <form
                method="POST"
                action="{{ route('hrd.karyawan.skill.store', $karyawan) }}"
                class="dk-form-skill"
            >
                @csrf

                <div class="dk-form-grid">
                    <div class="dk-input-group">
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

                    <div class="dk-input-group">
                        <label for="level_skill">Level Skill</label>
                        <select id="level_skill" name="level_skill" required>
                            @for($level = 1; $level <= 5; $level++)
                                <option value="{{ $level }}">
                                    Level {{ $level }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="dk-input-group">
                        <label for="tanggal_penilaian">Tanggal Penilaian</label>
                        <input
                            type="date"
                            id="tanggal_penilaian"
                            name="tanggal_penilaian"
                            value="{{ now()->toDateString() }}"
                            required
                        >
                    </div>

                    <button type="submit" class="dk-btn dk-btn-save">
                        + Simpan
                    </button>
                </div>
            </form>

            <div class="dk-table-box">
                <table class="dk-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>Skill</th>
                            <th>Level</th>
                            <th>Tanggal Penilaian</th>
                            <th style="width: 110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($karyawan->karyawanSkills as $karyawanSkill)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $karyawanSkill->skill->nama_skill }}</strong></td>
                                <td>Level {{ $karyawanSkill->level_skill }}</td>
                                <td>
                                    {{ $karyawanSkill->tanggal_penilaian
                                        ? \Carbon\Carbon::parse($karyawanSkill->tanggal_penilaian)->format('d-m-Y')
                                        : '-' }}
                                </td>
                                <td>
                                    <form
                                        method="POST"
                                        action="{{ route('hrd.karyawan.skill.destroy', [
                                            'karyawan' => $karyawan,
                                            'karyawanSkill' => $karyawanSkill
                                        ]) }}"
                                        onsubmit="return confirm('Hapus skill ini dari karyawan?')"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dk-btn dk-btn-sm dk-btn-delete">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="dk-empty">
                                    Karyawan belum memiliki skill.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endunless

    {{-- ===================================================== --}}
    {{-- RIWAYAT JABATAN SINGKAT --}}
    {{-- ===================================================== --}}
    <div class="dk-panel">
        <div class="dk-panel-head">
            <h2>Riwayat Jabatan</h2>
            <p>Catatan perubahan jabatan karyawan.</p>
        </div>

        <div class="dk-table-box">
            <table class="dk-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Jabatan</th>
                        <th>Jenis</th>
                        <th>Mulai</th>
                        <th>Selesai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($karyawan->riwayatJabatans as $riwayat)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $riwayat->jabatan->nama_jabatan }}</strong></td>
                            <td>{{ ucfirst($riwayat->jenis_perubahan) }}</td>
                            <td>{{ \Carbon\Carbon::parse($riwayat->tanggal_mulai)->format('d-m-Y') }}</td>
                            <td>
                                @if($riwayat->tanggal_selesai)
                                    {{ \Carbon\Carbon::parse($riwayat->tanggal_selesai)->format('d-m-Y') }}
                                @else
                                    <span class="dk-badge aktif">Masih aktif</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="dk-empty">
                                Belum ada riwayat jabatan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection