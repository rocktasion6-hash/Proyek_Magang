<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Tambah Karyawan</title>

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

        .section {
            margin-top: 30px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .section h2 {
            margin-bottom: 5px;
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

        .hint {
            display: block;
            margin-top: 5px;
            color: #6b7280;
            font-size: 13px;
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
@section('title', 'Tambah Karyawan')
@section('page_title', 'Tambah Karyawan')
@section('content')
<div class="container">

    <div style="margin-bottom:20px;">

        <a
            href="{{ route('hrd.karyawan.index') }}"
            class="btn btn-secondary"
        >
            ← Kembali
        </a>

    </div>

    <div class="card">

        <h1>Tambah Karyawan</h1>

        <p>
            Tambahkan data karyawan sekaligus membuat akun login.
        </p>

        @if($errors->any())

            <div class="alert">

                <strong>Terjadi kesalahan:</strong>

                <ul>

                    @foreach($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif

        <div class="info">

            Jabatan yang dipilih akan menentukan
            <strong>level jabatan</strong> karyawan.
            Tidak perlu mengisi level karyawan secara terpisah.

        </div>

        <form
            method="POST"
            action="{{ route('hrd.karyawan.store') }}"
        >

            @csrf


            {{-- ===================================================== --}}
            {{-- DATA KARYAWAN --}}
            {{-- ===================================================== --}}

            <div class="section">

                <h2>Data Karyawan</h2>

                <p>
                    Informasi dasar karyawan.
                </p>

            </div>

            <div class="form-group">

                <label>
                    NIK
                </label>

                <input
                    type="text"
                    name="nik"
                    value="{{ old('nik') }}"
                    required
                    placeholder="Contoh: KRY003"
                >

            </div>

            <div class="form-group">

                <label>
                    Nama Karyawan
                </label>

                <input
                    type="text"
                    name="nama"
                    value="{{ old('nama') }}"
                    required
                    placeholder="Nama lengkap karyawan"
                >

            </div>


            {{-- ===================================================== --}}
            {{-- AKUN LOGIN --}}
            {{-- ===================================================== --}}

            <div class="section">

                <h2>Akun Login</h2>

                <p>
                    Akun ini digunakan karyawan untuk masuk ke sistem.
                </p>

            </div>

            <div class="form-group">

                <label>
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    placeholder="Contoh: karyawan@gmail.com"
                >

            </div>

            <div class="form-group">

                <label>
                    Username
                </label>

                <input
                    type="text"
                    name="username"
                    value="{{ old('username') }}"
                    required
                    placeholder="Contoh: karyawan003"
                >

                <small class="hint">
                    Username digunakan saat login.
                </small>

            </div>

            <div class="form-group">

                <label>
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    required
                    minlength="8"
                    placeholder="Minimal 8 karakter"
                >

            </div>

            <div class="form-group">

                <label>
                    Konfirmasi Password
                </label>

                <input
                    type="password"
                    name="password_confirmation"
                    required
                    minlength="8"
                    placeholder="Ulangi password"
                >

            </div>


            {{-- ===================================================== --}}
            {{-- DATA PEKERJAAN --}}
            {{-- ===================================================== --}}

            <div class="section">

                <h2>Data Pekerjaan</h2>

                <p>
                    Tentukan departemen dan jabatan awal karyawan.
                </p>

            </div>

            <div class="form-group">

                <label>
                    Departemen
                </label>

                <select
                    name="departemen_id"
                    required
                >

                    <option value="">
                        -- Pilih Departemen --
                    </option>

                    @foreach($departemens as $departemen)

                        <option
                            value="{{ $departemen->id }}"
                            {{ old('departemen_id') == $departemen->id ? 'selected' : '' }}
                        >

                            {{ $departemen->nama_departemen }}

                        </option>

                    @endforeach

                </select>

            </div>

            <div class="form-group">

                <label>
                    Jabatan
                </label>

                <select
                    name="jabatan_id"
                    required
                >

                    <option value="">
                        -- Pilih Jabatan --
                    </option>

                    @foreach($jabatans as $jabatan)

                        <option
                            value="{{ $jabatan->id }}"
                            {{ old('jabatan_id') == $jabatan->id ? 'selected' : '' }}
                        >

                            {{ $jabatan->nama_jabatan }}
                            —
                            Level {{ $jabatan->level_jabatan }}

                        </option>

                    @endforeach

                </select>

                <small class="hint">
                    Level karyawan mengikuti level jabatan yang dipilih.
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
                        now()->toDateString()
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
                        {{ old('status', 'aktif') === 'aktif'
                            ? 'selected'
                            : '' }}
                    >
                        Aktif
                    </option>

                    <option
                        value="nonaktif"
                        {{ old('status') === 'nonaktif'
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
                Simpan Karyawan
            </button>

        </form>

    </div>

</div>
@endsection
</body>

</html>