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

        <h2>Skill Saat Ini</h2>

        <table>

            <thead>

                <tr>
                    <th>No</th>
                    <th>Skill</th>
                    <th>Level</th>
                    <th>Tanggal Penilaian</th>
                </tr>

            </thead>

            <tbody>

            @forelse($karyawan->skills as $skill)

                <tr>

                    <td>
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        <strong>
                            {{ $skill->nama_skill }}
                        </strong>
                    </td>

                    <td>
                        Level {{ $skill->pivot->level_skill }}
                    </td>

                    <td>

                        @if($skill->pivot->tanggal_penilaian)

                            {{ \Carbon\Carbon::parse(
                                $skill->pivot->tanggal_penilaian
                            )->format('d-m-Y') }}

                        @else

                            -

                        @endif

                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="4"
                        class="empty"
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