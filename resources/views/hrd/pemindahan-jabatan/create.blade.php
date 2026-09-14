<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Tambah Pemindahan Jabatan</title>

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

<div class="container">

    <div style="margin-bottom:20px;">

        <a
            href="{{ route('hrd.pemindahan-jabatan.index') }}"
            class="btn btn-secondary"
        >
            ← Kembali
        </a>

    </div>

    <div class="card">

        <h1>Tambah Pengajuan Pemindahan Jabatan</h1>

        <p>
            Pengajuan hanya dapat dibuat menggunakan hasil assessment
            yang berstatus <strong>LULUS</strong>.
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

            <strong>Aturan:</strong>

            Jabatan tujuan tidak boleh sama dengan jabatan
            karyawan saat ini.

        </div>

        <form
            method="POST"
            action="{{ route('hrd.pemindahan-jabatan.store') }}"
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

                            |
                            Nilai:
                            {{ number_format(
                                $hasil->nilai_akhir,
                                2
                            ) }}

                            |
                            Lulus

                        </option>

                    @endforeach

                </select>

            </div>

            <div class="form-group">

                <label>
                    Jabatan Tujuan
                </label>

                <select
                    name="jabatan_tujuan_id"
                    required
                >

                    <option value="">
                        -- Pilih Jabatan Tujuan --
                    </option>

                    @foreach($jabatans as $jabatan)

                        <option
                            value="{{ $jabatan->id }}"
                            {{ old('jabatan_tujuan_id') == $jabatan->id ? 'selected' : '' }}
                        >

                            {{ $jabatan->nama_jabatan }}

                            —
                            Level {{ $jabatan->level_jabatan }}

                            —
                            Standar {{ $jabatan->standar_nilai }}

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
            >
                Simpan Pengajuan
            </button>

        </form>

    </div>

</div>

</body>

</html>