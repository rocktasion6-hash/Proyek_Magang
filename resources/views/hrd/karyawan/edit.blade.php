<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Karyawan</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 800px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            box-sizing: border-box;
            padding: 11px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
        }

        input[readonly] {
            background: #f3f4f6;
        }

        .hint {
            display: block;
            margin-top: 6px;
            color: #6b7280;
            font-size: 13px;
        }

        .section {
            margin-top: 30px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .section h2 {
            margin-bottom: 5px;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .alert {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .info {
            background: #eff6ff;
            color: #1e40af;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

    </style>

</head>

<body>
@extends('layouts.hrd')
@section('title', 'Edit Karyawan')
@section('page_title', 'Edit Karyawan')
@section('content')
<div class="container">

    <div style="margin-bottom:20px;">

        <a
            href="{{ route(
                'hrd.karyawan.show',
                $karyawan
            ) }}"
            class="btn btn-secondary"
        >
            ← Kembali
        </a>

    </div>

    <div class="card">

        <h1>Edit Karyawan</h1>

        <p>
            Perbarui informasi karyawan.
        </p>

        @if($errors->any())

            <div class="alert">

                <strong>Terjadi kesalahan:</strong>

                <ul>

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif

        <div class="info">

            <strong>Catatan:</strong>

            Perubahan jabatan tidak dilakukan melalui halaman ini.
            Gunakan Modul Kenaikan Jabatan atau Pemindahan Jabatan
            agar riwayat jabatan tetap tercatat.

        </div>

        <form
            method="POST"
            action="{{ route(
                'hrd.karyawan.update',
                $karyawan
            ) }}"
        >

            @csrf

            @method('PUT')


            {{-- ================================================= --}}
            {{-- DATA KARYAWAN --}}
            {{-- ================================================= --}}

            <div class="section">

                <h2>Data Karyawan</h2>

            </div>

            <div class="form-group">

                <label>
                    NIK
                </label>

                <input
                    type="text"
                    name="nik"
                    value="{{ old(
                        'nik',
                        $karyawan->nik
                    ) }}"
                    required
                >

            </div>

            <div class="form-group">

                <label>
                    Nama Karyawan
                </label>

                <input
                    type="text"
                    name="nama"
                    value="{{ old(
                        'nama',
                        $karyawan->nama
                    ) }}"
                    required
                >

            </div>


            {{-- ================================================= --}}
            {{-- AKUN --}}
            {{-- ================================================= --}}

            <div class="section">

                <h2>Akun Login</h2>

            </div>

            <div class="form-group">

                <label>
                    Username
                </label>

                <input
                    type="text"
                    value="{{ $karyawan->user->username ?? '-' }}"
                    readonly
                >

                <small class="hint">
                    Username tidak diubah dari halaman ini.
                </small>

            </div>

            <div class="form-group">

                <label>
                    Email
                </label>

                <input
                    type="email"
                    value="{{ $karyawan->user->email ?? '-' }}"
                    readonly
                >

                <small class="hint">
                    Email login juga tidak diubah dari halaman ini.
                </small>

            </div>


            {{-- ================================================= --}}
            {{-- PEKERJAAN --}}
            {{-- ================================================= --}}

            <div class="section">

                <h2>Data Pekerjaan</h2>

            </div>

            <div class="form-group">

                <label>
                    Departemen
                </label>

                <select
                    name="departemen_id"
                    required
                >

                    @foreach($departemens as $departemen)

                        <option
                            value="{{ $departemen->id }}"
                            {{ old(
                                'departemen_id',
                                $karyawan->departemen_id
                            ) == $departemen->id
                                ? 'selected'
                                : '' }}
                        >

                            {{ $departemen->nama_departemen }}

                        </option>

                    @endforeach

                </select>

            </div>

            <div class="form-group">

                <label>
                    Jabatan Saat Ini
                </label>

                <select
                    disabled
                >

                    @foreach($jabatans as $jabatan)

                        @if(
                            $jabatan->id ===
                            $karyawan->jabatan_id
                        )

                            <option selected>

                                {{ $jabatan->nama_jabatan }}
                                —
                                Level {{ $jabatan->level_jabatan }}

                            </option>

                        @endif

                    @endforeach

                </select>

                <small class="hint">

                    Jabatan tidak dapat diubah melalui Edit Karyawan.
                    Gunakan modul Kenaikan Jabatan atau Pemindahan Jabatan.

                </small>

            </div>

            <div class="form-group">

                <label>
                    Tanggal Masuk
                </label>

                <input
                    type="date"
                    name="tanggal_masuk"
                    value="{{ old(
                        'tanggal_masuk',
                        $karyawan->tanggal_masuk
                    ) }}"
                    required
                >

            </div>

            <div class="form-group">

                <label>
                    Status
                </label>

                <select
                    name="status"
                    required
                >

                    <option
                        value="aktif"
                        {{ old(
                            'status',
                            $karyawan->status
                        ) === 'aktif'
                            ? 'selected'
                            : '' }}
                    >
                        Aktif
                    </option>

                    <option
                        value="nonaktif"
                        {{ old(
                            'status',
                            $karyawan->status
                        ) === 'nonaktif'
                            ? 'selected'
                            : '' }}
                    >
                        Nonaktif
                    </option>

                </select>

            </div>

            <button
                type="submit"
                class="btn btn-primary"
            >
                Simpan Perubahan
            </button>

        </form>

    </div>

</div>
@endsection
</body>

</html>