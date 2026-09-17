<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Detail Kenaikan Jabatan</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .row {
            display: grid;
            grid-template-columns: 200px 1fr;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .label {
            font-weight: bold;
            color: #374151;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-back {
            background: #6b7280;
            color: white;
        }

        .btn-process {
            background: #2563eb;
            color: white;
        }

        .btn-approve {
            background: #16a34a;
            color: white;
        }

        .btn-reject {
            background: #dc2626;
            color: white;
        }

        textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            min-height: 100px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 13px;
        }

        .diajukan {
            background: #fef3c7;
            color: #92400e;
        }

        .diproses {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .disetujui {
            background: #dcfce7;
            color: #166534;
        }

        .ditolak {
            background: #fee2e2;
            color: #991b1b;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

    </style>

</head>

<body>
@extends('layouts.hrd')
@section('title', 'Detail Kenaikan Jabatan')
@section('page_title', 'Detail Kenaikan Jabatan')
@section('content')
<div class="container">

    <div style="margin-bottom:20px;">

        <a
            href="{{ route('hrd.kenaikan-jabatan.index') }}"
            class="btn btn-back"
        >
            ← Kembali
        </a>

    </div>

    @if(session('success'))

        <div class="success">
            {{ session('success') }}
        </div>

    @endif

    @if(session('error'))

        <div class="error">
            {{ session('error') }}
        </div>

    @endif

    <div class="card">

        <h1>Detail Kenaikan Jabatan</h1>

        <div class="row">

            <div class="label">
                Status
            </div>

            <div>

                <span class="badge {{ $pengajuan->status }}">
                    {{ ucfirst($pengajuan->status) }}
                </span>

            </div>

        </div>

        <div class="row">

            <div class="label">
                Karyawan
            </div>

            <div>
                {{ $pengajuan->karyawan->nama }}
            </div>

        </div>

        <div class="row">

            <div class="label">
                NIK
            </div>

            <div>
                {{ $pengajuan->karyawan->nik }}
            </div>

        </div>

        <div class="row">

            <div class="label">
                Departemen
            </div>

            <div>
                {{ $pengajuan->karyawan->departemen->nama_departemen }}
            </div>

        </div>

        <div class="row">

            <div class="label">
                Jabatan Saat Ini
            </div>

            <div>
                {{ $pengajuan->jabatanAsal->nama_jabatan }}
                (Level {{ $pengajuan->jabatanAsal->level_jabatan }})
            </div>

        </div>

        <div class="row">

            <div class="label">
                Jabatan Tujuan
            </div>

            <div>
                {{ $pengajuan->jabatanTujuan->nama_jabatan }}
                (Level {{ $pengajuan->jabatanTujuan->level_jabatan }})
            </div>

        </div>

        <div class="row">

            <div class="label">
                Nilai Assessment
            </div>

            <div>

                <strong>
                    {{ number_format(
                        $pengajuan->hasilAssessment->nilai_akhir,
                        2
                    ) }}
                </strong>

            </div>

        </div>

        <div class="row">

            <div class="label">
                Standar Nilai
            </div>

            <div>
                {{ number_format(
                    $pengajuan->hasilAssessment->standar_nilai,
                    2
                ) }}
            </div>

        </div>

        <div class="row">

            <div class="label">
                Status Assessment
            </div>

            <div>
                {{ ucfirst(
                    str_replace(
                        '_',
                        ' ',
                        $pengajuan->hasilAssessment->status
                    )
                ) }}
            </div>

        </div>

        <div class="row">

            <div class="label">
                Tanggal Ujian
            </div>

            <div>
                {{ \Carbon\Carbon::parse(
                    $pengajuan->hasilAssessment->tanggal_ujian
                )->format('d-m-Y H:i') }}
            </div>

        </div>

        <div class="row">

            <div class="label">
                Tanggal Pengajuan
            </div>

            <div>
                {{ \Carbon\Carbon::parse(
                    $pengajuan->tanggal_pengajuan
                )->format('d-m-Y') }}
            </div>

        </div>

        @if($pengajuan->tanggal_keputusan)

            <div class="row">

                <div class="label">
                    Tanggal Keputusan
                </div>

                <div>
                    {{ \Carbon\Carbon::parse(
                        $pengajuan->tanggal_keputusan
                    )->format('d-m-Y') }}
                </div>

            </div>

        @endif

        <div class="row">

            <div class="label">
                Catatan
            </div>

            <div>
                {{ $pengajuan->catatan ?: '-' }}
            </div>

        </div>

        <div class="actions">

            @if($pengajuan->status === 'diajukan')

                <form
                    method="POST"
                    action="{{ route(
                        'hrd.kenaikan-jabatan.process',
                        $pengajuan
                    ) }}"
                >

                    @csrf

                    <button
                        type="submit"
                        class="btn btn-process"
                    >
                        Proses Pengajuan
                    </button>

                </form>

            @endif

            @if(in_array($pengajuan->status, ['diajukan', 'diproses']))

                <form
                    method="POST"
                    action="{{ route(
                        'hrd.kenaikan-jabatan.approve',
                        $pengajuan
                    ) }}"
                    onsubmit="return confirm(
                        'Yakin ingin menyetujui kenaikan jabatan ini?'
                    )"
                >

                    @csrf

                    <button
                        type="submit"
                        class="btn btn-approve"
                    >
                        ✓ Setujui
                    </button>

                </form>

            @endif

        </div>

    </div>

    @if(in_array($pengajuan->status, ['diajukan', 'diproses']))

        <div class="card">

            <h2>Tolak Pengajuan</h2>

            <form
                method="POST"
                action="{{ route(
                    'hrd.kenaikan-jabatan.reject',
                    $pengajuan
                ) }}"
            >

                @csrf

                <p>
                    Masukkan alasan penolakan:
                </p>

                <textarea
                    name="catatan"
                    required
                    placeholder="Contoh: Belum memenuhi kebutuhan jabatan."
                ></textarea>

                <br><br>

                <button
                    type="submit"
                    class="btn btn-reject"
                    onclick="return confirm(
                        'Yakin ingin menolak pengajuan ini?'
                    )"
                >
                    ✕ Tolak Pengajuan
                </button>

            </form>

        </div>

    @endif

</div>
@endsection
</body>
</html>