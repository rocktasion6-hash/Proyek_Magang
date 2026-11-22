
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Assessment</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #374151;
            margin: 0;
            padding: 0;
        }

        .header {
            border: 1px solid #dce8df;
            background-color: #f1f7f2;
            padding: 20px;
            margin-bottom: 15px;
        }

        .eyebrow {
            color: #4a7c59;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 7px 0;
        }

        .assessment-title {
            color: #2c4a3e;
            font-size: 19px;
            font-weight: bold;
            line-height: 1.4;
            margin: 0 0 9px 0;
        }

        .date {
            color: #6b7280;
            font-size: 10px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            border: 0;
            padding: 0;
            vertical-align: middle;
        }

        .score-box {
            width: 125px;
            padding: 13px;
            border: 1px solid;
            text-align: center;
        }

        .score-pass {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .score-fail {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .score-label {
            font-size: 9px;
            color: #6b7280;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .score-value {
            font-size: 28px;
            font-weight: bold;
            line-height: 1.2;
        }

        .score-pass .score-value {
            color: #15803d;
        }

        .score-fail .score-value {
            color: #b91c1c;
        }

        .score-standard {
            font-size: 9px;
            color: #6b7280;
            margin-top: 5px;
        }

        .cards {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin-left: -8px;
        }

        .cards > tbody > tr > td {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }

        .card {
            border: 1px solid #e5e7eb;
            padding: 14px;
            min-height: 125px;
        }

        .section-heading {
            color: #2c4a3e;
            background: #edf5ef;
            border-left: 4px solid #4a7c59;
            font-size: 12px;
            font-weight: bold;
            padding: 9px 10px;
            margin-bottom: 13px;
        }

        .field-label {
            font-size: 9px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .field-value {
            font-size: 11px;
            font-weight: bold;
            color: #1f2937;
            line-height: 1.5;
        }

        .separator {
            border: 0;
            border-top: 1px solid #e5e7eb;
            margin: 11px 0;
        }
    </style>
</head>

<body>

@php
    $peserta = $hasilAssessment->assessmentPeserta;
    $karyawan = $peserta->karyawan;
    $assessment = $peserta->assessment;

    $isLulus = $hasilAssessment->status === 'lulus';

    $jabatanAwal = $karyawan->jabatan?->nama_jabatan ?? '-';
    $jabatanTujuan = $assessment->jabatan?->nama_jabatan ?? '-';
@endphp

{{-- HEADER LAPORAN --}}
<div class="header">

    <table class="header-table">
        <tr>
            <td style="padding-right: 14px;">

                <p class="eyebrow">
                    Laporan Hasil Assessment
                </p>

                <h1 class="assessment-title">
                    {{ $assessment->nama_assessment }}
                </h1>

                <div class="date">
                    Tanggal Ujian:
                    <strong>
                        {{ $hasilAssessment->tanggal_ujian?->format('d M Y H:i') ?? '-' }}
                    </strong>
                </div>

            </td>

            <td style="width: 155px;">

                <div class="score-box {{ $isLulus ? 'score-pass' : 'score-fail' }}">

                    <div class="score-label">
                        NILAI TES
                    </div>

                    <div class="score-value">
                        {{ number_format($hasilAssessment->nilai_akhir, 2) }}
                    </div>

                    <div class="score-standard">
                        Standar:
                        {{ number_format($hasilAssessment->standar_nilai, 2) }}
                    </div>

                </div>

            </td>
        </tr>
    </table>

</div>


{{-- INFORMASI KARYAWAN DAN JABATAN --}}
<table class="cards">

    <tr>

        {{-- INFORMASI KARYAWAN --}}
        <td>
            <div class="card">

                <div class="section-heading">
                    Informasi Karyawan
                </div>

                <div class="field-label">
                    Nama Karyawan
                </div>

                <div class="field-value">
                    {{ $karyawan->nama }}
                </div>

                <hr class="separator">

                <div class="field-label">
                    NIK
                </div>

                <div class="field-value">
                    {{ $karyawan->nik }}
                </div>

            </div>
        </td>


        {{-- INFORMASI JABATAN --}}
        <td>
            <div class="card">

                <div class="section-heading">
                    Informasi Jabatan
                </div>

                <div class="field-label">
                    Jabatan Awal
                </div>

                <div class="field-value">
                    {{ $jabatanAwal }}
                </div>

                <hr class="separator">

                <div class="field-label">
                    Jabatan Tujuan
                </div>

                <div class="field-value" style="color: #2c4a3e;">
                    {{ $jabatanTujuan }}
                </div>

            </div>
        </td>

    </tr>

</table>

</body>
</html>
