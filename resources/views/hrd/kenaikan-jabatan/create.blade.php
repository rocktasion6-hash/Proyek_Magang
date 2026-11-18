<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Tambah Kenaikan Jabatan</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 850px;
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

        select,
        input,
        textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 11px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .btn {
            padding: 10px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
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
@section('title', 'Tambah Kenaikan Jabatan')
@section('page_title', 'Tambah Kenaikan Jabatan')
@section('content')
<div class="container">

    <div style="margin-bottom:20px;">

        <a
            href="{{ route('hrd.kenaikan-jabatan.index') }}"
            class="btn btn-secondary"
        >
            ← Kembali
        </a>

    </div>

    <div class="card">

        <h1>Tambah Pengajuan Kenaikan Jabatan</h1>

        <p>
            Pengajuan hanya dapat menggunakan hasil assessment yang sudah selesai
            dan berstatus <strong>LULUS</strong> untuk tahap struktural berikutnya.
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

            <strong>Alur kenaikan:</strong>
            Karyawan operasional → assessment struktural Urutan 1 (Wakil Foreman).
            Karyawan struktural harus naik satu urutan setiap kali melalui
            assessment jabatan tujuan berikutnya, sampai Urutan 6 (Manager).
            Jabatan tujuan mengikuti assessment yang lulus.

        </div>

        @if($hasilAssessments->isEmpty())
            <div class="info">
                Belum ada hasil assessment lulus yang sesuai dengan tahap kenaikan jabatan karyawan.
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('hrd.kenaikan-jabatan.store') }}"
        >

            @csrf

            <div class="form-group">

                <label>
                    Hasil Assessment
                </label>

                <select
                    name="hasil_assessment_id"
                    required
                >

                    <option value="">
                        -- Pilih Hasil Assessment --
                    </option>

                    @foreach($hasilAssessments as $hasil)

                        @php
                            $peserta = $hasil->assessmentPeserta;
                            $karyawan = $peserta->karyawan;
                            $jabatan = $karyawan->jabatan;
                            $jabatanTujuan = $peserta->assessment->jabatan;
                        @endphp

                        <option
                            value="{{ $hasil->id }}"
                            {{ old('hasil_assessment_id') == $hasil->id ? 'selected' : '' }}
                        >

                            {{ $karyawan->nama }}

                            |
                            {{ $karyawan->nik }}

                            |
                            {{ $jabatan->nama_jabatan }}
                            @if($jabatan->tipe_jabatan === 'struktural')
                                (Urutan {{ $jabatan->urutan_struktural }})
                            @endif

                            →

                            {{ $jabatanTujuan->nama_jabatan }}
                            (Urutan {{ $jabatanTujuan->urutan_struktural }})

                            | Nilai:
                            {{ number_format($hasil->nilai_akhir, 2) }}

                            |
                            Lulus

                        </option>

                    @endforeach

                </select>

            </div>

            <div class="form-group">

                <label>
                    Tanggal Pengajuan
                </label>

                <input
                    type="date"
                    name="tanggal_pengajuan"
                    value="{{ old(
                        'tanggal_pengajuan',
                        now()->toDateString()
                    ) }}"
                    required
                >

            </div>

            <div class="form-group">

                <label>
                    Catatan
                </label>

                <textarea
                    name="catatan"
                    placeholder="Masukkan catatan pengajuan..."
                >{{ old('catatan') }}</textarea>

            </div>

            <button
                type="submit"
                class="btn btn-primary"
                @disabled($hasilAssessments->isEmpty())
            >
                Simpan Pengajuan
            </button>

        </form>

    </div>

</div>
@endsection
</body>

</html>