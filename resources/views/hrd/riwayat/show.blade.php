<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Detail Riwayat Karyawan</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .profile {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 10px;
        }

        .profile div {
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .label {
            font-weight: bold;
            color: #374151;
        }

        h1,
        h2 {
            margin-top: 0;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 6px;
        }

        .btn-back {
            background: #6b7280;
            color: white;
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

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .awal {
            background: #e5e7eb;
            color: #374151;
        }

        .kenaikan {
            background: #dcfce7;
            color: #166534;
        }

        .pemindahan {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .skill {
            background: #fef3c7;
            color: #92400e;
        }

        .empty {
            text-align: center;
            padding: 25px;
            color: #6b7280;
        }

    </style>

</head>

<body>
@extends('layouts.hrd')
@section('title', 'Detail Riwayat')
@section('page_title', 'Detail Riwayat')
@section('content')
<div class="container">

    <div style="margin-bottom:20px;">

        <a
            href="{{ route('hrd.riwayat.index') }}"
            class="btn btn-back"
        >
            ← Kembali
        </a>

    </div>

    <!-- PROFIL KARYAWAN -->

    <div class="card">

        <h1>Riwayat Karyawan</h1>

        <div class="profile">

            <div class="label">
                NIK
            </div>

            <div>
                {{ $karyawan->nik }}
            </div>

            <div class="label">
                Nama
            </div>

            <div>
                {{ $karyawan->nama }}
            </div>

            <div class="label">
                Departemen
            </div>

            <div>
                {{ $karyawan->departemen->nama_departemen }}
            </div>

            <div class="label">
                Jabatan Saat Ini
            </div>

            <div>
                {{ $karyawan->jabatan->nama_jabatan }}
            </div>

            <div class="label">
                Level
            </div>

            <div>
                {{ $karyawan->jabatan->level_jabatan }}
            </div>

            <div class="label">
                Tanggal Masuk
            </div>

            <div>
                {{ \Carbon\Carbon::parse(
                    $karyawan->tanggal_masuk
                )->format('d-m-Y') }}
            </div>

        </div>

    </div>


    <!-- RIWAYAT JABATAN -->

    <div class="card">

        <h2>Riwayat Jabatan</h2>

        <table>

            <thead>

                <tr>
                    <th>No</th>
                    <th>Jabatan</th>
                    <th>Jenis Perubahan</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Nilai Assessment</th>
                    <th>Keterangan</th>
                </tr>

            </thead>

            <tbody>

            @forelse($riwayatJabatans as $riwayat)

                <tr>

                    <td>
                        {{ $loop->iteration }}
                    </td>

                    <td>

                        <strong>
                            {{ $riwayat->jabatan->nama_jabatan }}
                        </strong>

                        <br>

                        <small>
                            Level {{ $riwayat->jabatan->level_jabatan }}
                        </small>

                    </td>

                    <td>

                        @php
                            $class = match($riwayat->jenis_perubahan) {
                                'awal' => 'awal',
                                'kenaikan' => 'kenaikan',
                                'pemindahan' => 'pemindahan',
                                default => 'awal',
                            };
                        @endphp

                        <span class="badge {{ $class }}">
                            {{ ucfirst($riwayat->jenis_perubahan) }}
                        </span>

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

                            <strong>Masih aktif</strong>

                        @endif

                    </td>

                    <td>

                        @if($riwayat->hasilAssessment)

                            {{ number_format(
                                $riwayat->hasilAssessment->nilai_akhir,
                                2
                            ) }}

                        @else

                            -

                        @endif

                    </td>

                    <td>
                        {{ $riwayat->keterangan ?: '-' }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="7"
                        class="empty"
                    >
                        Belum ada riwayat jabatan.
                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>


    <!-- RIWAYAT SKILL -->

    <div class="card">

        <h2>Riwayat Skill</h2>

        <table>

            <thead>

                <tr>
                    <th>No</th>
                    <th>Skill</th>
                    <th>Level Sebelum</th>
                    <th>Level Sesudah</th>
                    <th>Tanggal Perubahan</th>
                    <th>Nilai Assessment</th>
                    <th>Keterangan</th>
                </tr>

            </thead>

            <tbody>

            @forelse($riwayatSkills as $riwayat)

                <tr>

                    <td>
                        {{ $loop->iteration }}
                    </td>

                    <td>

                        <strong>
                            {{ $riwayat->skill->nama_skill }}
                        </strong>

                    </td>

                    <td>
                        Level {{ $riwayat->level_sebelum }}
                    </td>

                    <td>

                        <span class="badge skill">
                            Level {{ $riwayat->level_sesudah }}
                        </span>

                    </td>

                    <td>

                        {{ \Carbon\Carbon::parse(
                            $riwayat->tanggal_perubahan
                        )->format('d-m-Y') }}

                    </td>

                    <td>

                        @if($riwayat->hasilAssessment)

                            {{ number_format(
                                $riwayat->hasilAssessment->nilai_akhir,
                                2
                            ) }}

                        @else

                            -

                        @endif

                    </td>

                    <td>
                        {{ $riwayat->keterangan ?: '-' }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="7"
                        class="empty"
                    >
                        Belum ada riwayat skill.
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