<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Detail Karyawan</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 10px;
            flex-wrap: wrap;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        h1,
        h2 {
            margin-top: 0;
        }

        .profile {
            display: grid;
            grid-template-columns: 220px 1fr;
        }

        .profile div {
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .label {
            font-weight: bold;
            color: #374151;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 6px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .btn-back {
            background: #6b7280;
            color: white;
        }

        .btn-edit {
            background: #f59e0b;
            color: white;
        }

        .btn-history {
            background: #111827;
            color: white;
        }

        .badge {
            display: inline-block;
            padding: 6px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .aktif {
            background: #dcfce7;
            color: #166534;
        }

        .nonaktif {
            background: #fee2e2;
            color: #991b1b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f9fafb;
        }

        .empty {
            text-align: center;
            color: #6b7280;
            padding: 25px;
        }

    </style>

</head>

<body>
@extends('layouts.hrd')
@section('title', 'Detail Karyawan')
@section('page_title', 'Detail Karyawan')
@section('content')
<div class="container">

    <div class="topbar">

        <a
            href="{{ route('hrd.karyawan.index') }}"
            class="btn btn-back"
        >
            ← Kembali
        </a>

        <div>

            <a
                href="{{ route(
                    'hrd.karyawan.edit',
                    $karyawan
                ) }}"
                class="btn btn-edit"
            >
                Edit Data
            </a>

            <a
                href="{{ route(
                    'hrd.riwayat.show',
                    $karyawan
                ) }}"
                class="btn btn-history"
            >
                Lihat Riwayat Lengkap
            </a>

        </div>

    </div>


    {{-- ===================================================== --}}
    {{-- IDENTITAS KARYAWAN --}}
    {{-- ===================================================== --}}

    <div class="card">

        <h1>Detail Karyawan</h1>

        <div class="profile">

            <div class="label">
                NIK
            </div>

            <div>
                {{ $karyawan->nik }}
            </div>

            <div class="label">
                Nama Karyawan
            </div>

            <div>
                {{ $karyawan->nama }}
            </div>

            <div class="label">
                Email
            </div>

            <div>
                {{ $karyawan->user->email ?? '-' }}
            </div>

            <div class="label">
                Username
            </div>

            <div>
                {{ $karyawan->user->username ?? '-' }}
            </div>

            <div class="label">
                Departemen
            </div>

            <div>
                {{ $karyawan->departemen->nama_departemen }}
            </div>

            <div class="label">
                Jabatan
            </div>

            <div>

                <strong>
                    {{ $karyawan->jabatan->nama_jabatan }}
                </strong>

                <br>

                <small>
                    Level {{ $karyawan->jabatan->level_jabatan }}
                </small>

            </div>

            <div class="label">
                Tanggal Masuk
            </div>

            <div>
                {{ \Carbon\Carbon::parse(
                    $karyawan->tanggal_masuk
                )->format('d-m-Y') }}
            </div>

            <div class="label">
                Status
            </div>

            <div>

                <span class="badge {{ $karyawan->status }}">
                    {{ ucfirst($karyawan->status) }}
                </span>

            </div>

        </div>

    </div>


    {{-- ===================================================== --}}
    {{-- SKILL SAAT INI --}}
    {{-- ===================================================== --}}

<div class="card">

    <div>

        <h2>
            Skill Saat Ini
        </h2>

        <p>
            Kelola skill yang dimiliki karyawan
            beserta level kompetensinya.
        </p>

    </div>


    <form
        method="POST"
        action="{{ route(
            'hrd.karyawan.skill.store',
            $karyawan
        ) }}"
        style="
            background:#f9fafb;
            padding:20px;
            border-radius:8px;
            margin-bottom:20px;
        "
    >

        @csrf

        <div
            style="
                display:grid;
                grid-template-columns:1fr 180px 180px auto;
                gap:10px;
                align-items:end;
            "
        >

            <div>

                <label
                    style="
                        display:block;
                        margin-bottom:7px;
                        font-weight:bold;
                    "
                >
                    Skill
                </label>

                <select
                    name="skill_id"
                    required
                    style="
                        width:100%;
                        padding:10px;
                        border:1px solid #d1d5db;
                        border-radius:6px;
                    "
                >

                    <option value="">
                        -- Pilih Skill --
                    </option>

                    @foreach($availableSkills as $skill)

                        <option
                            value="{{ $skill->id }}"
                        >
                            {{ $skill->nama_skill }}
                        </option>

                    @endforeach

                </select>

            </div>


            <div>

                <label
                    style="
                        display:block;
                        margin-bottom:7px;
                        font-weight:bold;
                    "
                >
                    Level Skill
                </label>

                <select
                    name="level_skill"
                    required
                    style="
                        width:100%;
                        padding:10px;
                        border:1px solid #d1d5db;
                        border-radius:6px;
                    "
                >

                    @for($level = 1; $level <= 5; $level++)

                        <option
                            value="{{ $level }}"
                        >
                            Level {{ $level }}
                        </option>

                    @endfor

                </select>

            </div>


            <div>

                <label
                    style="
                        display:block;
                        margin-bottom:7px;
                        font-weight:bold;
                    "
                >
                    Tanggal Penilaian
                </label>

                <input
                    type="date"
                    name="tanggal_penilaian"
                    value="{{ now()->toDateString() }}"
                    required
                    style="
                        width:100%;
                        padding:10px;
                        border:1px solid #d1d5db;
                        border-radius:6px;
                    "
                >

            </div>


            <button
                type="submit"
                class="btn"
                style="
                    background:#2563eb;
                    color:white;
                "
            >
                + Simpan
            </button>

        </div>

    </form>


    <table
        style="
            width:100%;
            border-collapse:collapse;
        "
    >

        <thead>

            <tr>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    No
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Skill
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Level
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Tanggal Penilaian
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Aksi
                </th>

            </tr>

        </thead>

        <tbody>

        @forelse($karyawan->karyawanSkills as $karyawanSkill)

            <tr>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                    {{ $loop->iteration }}
                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">

                    <strong>
                        {{ $karyawanSkill->skill->nama_skill }}
                    </strong>

                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">

                    Level
                    {{ $karyawanSkill->level_skill }}

                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">

                    {{ $karyawanSkill->tanggal_penilaian
                        ? \Carbon\Carbon::parse(
                            $karyawanSkill->tanggal_penilaian
                        )->format('d-m-Y')
                        : '-' }}

                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">

                    <form
                        method="POST"
                        action="{{ route(
                            'hrd.karyawan.skill.destroy',
                            [
                                'karyawan' => $karyawan,
                                'karyawanSkill' => $karyawanSkill
                            ]
                        ) }}"
                        onsubmit="return confirm(
                            'Hapus skill ini dari karyawan?'
                        )"
                    >

                        @csrf

                        @method('DELETE')

                        <button
                            type="submit"
                            class="btn"
                            style="
                                background:#dc2626;
                                color:white;
                            "
                        >
                            Hapus
                        </button>

                    </form>

                </td>

            </tr>

        @empty

            <tr>

                <td
                    colspan="5"
                    style="
                        padding:25px;
                        text-align:center;
                    "
                >
                    Karyawan belum memiliki skill.
                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

</div>


    {{-- ===================================================== --}}
    {{-- RIWAYAT JABATAN SINGKAT --}}
    {{-- ===================================================== --}}

    <div class="card">

        <h2>Riwayat Jabatan</h2>

        <table>

            <thead>

                <tr>
                    <th>No</th>
                    <th>Jabatan</th>
                    <th>Jenis</th>
                    <th>Mulai</th>
                    <th>Selesai</th>
                </tr>

            </thead>

            <tbody>

            @forelse($karyawan->riwayatJabatans as $riwayat)

                <tr>

                    <td>
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $riwayat->jabatan->nama_jabatan }}
                    </td>

                    <td>

                        {{ ucfirst(
                            $riwayat->jenis_perubahan
                        ) }}

                    </td>

                    <td>

                        {{ \Carbon\Carbon::parse(
                            $riwayat->tanggal_mulai
                        )->format('d-m-Y') }}

                    </td>

                    <td>

                        @if($riwayat->tanggal_selesai)

                            {{ \Carbon\Carbon::parse(
                                $riwayat->tanggal_selesai
                            )->format('d-m-Y') }}

                        @else

                            <strong>
                                Masih aktif
                            </strong>

                        @endif

                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="5"
                        class="empty"
                    >
                        Belum ada riwayat jabatan.
                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

</div>
@endsection
</body>
</html>