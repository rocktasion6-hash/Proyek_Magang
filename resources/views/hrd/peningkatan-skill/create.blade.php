<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Tambah Peningkatan Skill</title>

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
@section('title', 'Tambah Peningkatan Skill')
@section('page_title', 'Tambah Peningkatan Skill')
@section('content')
<div class="container">

    <div style="margin-bottom:20px;">

        <a
            href="{{ route('hrd.peningkatan-skill.index') }}"
            class="btn btn-secondary"
        >
            ← Kembali
        </a>

    </div>

    <div class="card">

        <h1>Tambah Pengajuan Peningkatan Skill</h1>

        <p>
            Pengajuan hanya dapat menggunakan hasil assessment yang sudah selesai
            dan berstatus <strong>LULUS</strong> untuk jabatan satu level di atas
            jabatan karyawan saat ini.
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
            <strong>Alur Pengembangan:</strong><br>
            Crew → <em>Pemindahan Jabatan</em> → Operator Loader Level 1
            → <em>Assessment Level 2</em> → Pengajuan Peningkatan Skill
            → Level 2 → assessment dan pengajuan berikutnya sampai Level 5 (maks).
            Jabatan tujuan mengikuti assessment untuk level berikutnya dan harus
            berasal dari jalur jabatan yang sama.
        </div>

        @if($hasilAssessments->isEmpty())
            <div class="info">
                Belum ada hasil assessment lulus yang sesuai dengan jabatan karyawan saat ini dan level berikutnya.
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('hrd.peningkatan-skill.store') }}"
        >

            @csrf

            <div class="form-group">

                <label>
                    Hasil Assessment (Lulus)
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
                            $peserta  = $hasil->assessmentPeserta;
                            $karyawan = $peserta->karyawan;
                            $jabatan  = $karyawan->jabatan;
                            $jabatanTujuan = $peserta->assessment->jabatan;
                        @endphp

                        <option
                            value="{{ $hasil->id }}"
                            {{ old('hasil_assessment_id') == $hasil->id ? 'selected' : '' }}
                        >
                            {{ $karyawan->nama }} ({{ $karyawan->nik }})
                            | {{ $jabatan->nama_jabatan }}
                            → {{ $jabatanTujuan->nama_jabatan }}
                            (Level {{ $jabatanTujuan->level_jabatan }})
                            | Nilai: {{ number_format($hasil->nilai_akhir, 2) }}
                        </option>

                    @endforeach

                </select>

                <small style="color:#6b7280;">
                    Hanya menampilkan karyawan aktif level 1–4 dengan assessment level berikutnya yang sudah selesai dan lulus.
                </small>

            </div>

            <div class="form-group">

                <label>
                    Tanggal Pengajuan
                </label>

                <input
                    type="date"
                    name="tanggal_pengajuan"
                    value="{{ old('tanggal_pengajuan', now()->toDateString()) }}"
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