<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Assessment</title>
    <style>
        @page { size: A4 portrait; margin: 18mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        h1 { text-align: center; color: #2c4a3e; font-size: 18px; margin-bottom: 5px; }
        .subtitle { text-align: center; color: #6b7280; margin-bottom: 25px; }
        .section-title { color: #2c4a3e; border-bottom: 2px solid #4a7c59; padding-bottom: 7px; margin-top: 22px; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        td, th { border: 1px solid #e5e7eb; padding: 9px; text-align: left; }
        th { background-color: #e8f1ea; color: #2c4a3e; }
        .nilai { text-align: center; font-size: 32px; font-weight: bold; color: #2c4a3e; margin: 15px 0; }
        tr { page-break-inside: avoid; }
    </style>
</head>
<body>

{{-- Semua perhitungan dilakukan dahulu, sebelum tabel ditampilkan. --}}
@php
    $peserta = $hasilAssessment->assessmentPeserta;
    $assessment = $peserta->assessment;
    $jawabans = $peserta->jawabans;

    $kategoriPengetahuan = [
        'peraturan_perusahaan' => 'PP - Peraturan Perusahaan',
        'pengoperasian_jabatan' => 'SOP - Prosedur Pengoperasian Jabatan',
        'keselamatan_kerja' => 'K3 - Prosedur Pengoperasian Keselamatan Kerja',
    ];

    $kategoriAnalisis = [
        'peraturan_perusahaan' => 'PP',
        'pengoperasian_jabatan' => 'SOP',
        'keselamatan_kerja' => 'K3',
    ];

    $tipeAnalisis = [
        'pilihan_ganda' => 'PG',
        'multi_jawaban' => 'MJ',
        'benar_salah' => 'Benar/Salah',
    ];

    $analisisPengetahuan = [];

    foreach ($kategoriPengetahuan as $kode => $nama) {
        $soals = $assessment->soals->where('tipe_prosedur', $kode);
        $poinMaksimal = 0;
        $poinPeserta = 0;

        foreach ($soals as $soal) {
            $bobot = $assessment->nilaiPointUntukSoal($soal);
            $poinMaksimal += $bobot;

            $dipilih = $jawabans->where('soal_id', $soal->id)
                ->pluck('pilihan_jawaban_id')
                ->filter(fn ($id) => $id !== null)
                ->map(fn ($id) => (string) $id)
                ->sort()->values()->all();

            $kunci = $soal->pilihanJawabans->where('is_benar', true)
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->sort()->values()->all();

            if (count($dipilih) > 0 && $dipilih === $kunci) {
                $poinPeserta += $bobot;
            }
        }

        $analisisPengetahuan[] = [
            'nama' => $nama,
            'maksimal' => $poinMaksimal,
            'peserta' => $poinPeserta,
            'persentase' => $poinMaksimal > 0
                ? ($poinPeserta / $poinMaksimal) * 100
                : null,
        ];
    }

    $rekapKesalahan = [];
    foreach ($kategoriAnalisis as $kode => $nama) {
        $rekapKesalahan[$kode] = ['nama' => $nama, 'tipe' => []];
        foreach ($tipeAnalisis as $tipe => $label) {
            $rekapKesalahan[$kode]['tipe'][$tipe] = ['benar' => 0, 'salah' => 0];
        }
    }

    foreach ($assessment->soals as $soal) {
        $kategori = $soal->tipe_prosedur;
        $tipe = $soal->tipe_soal === 'pilihan_tunggal'
            ? 'pilihan_ganda' : $soal->tipe_soal;

        if (!isset($rekapKesalahan[$kategori]) || !isset($tipeAnalisis[$tipe])) {
            continue;
        }

        $dipilih = $jawabans->where('soal_id', $soal->id)
            ->pluck('pilihan_jawaban_id')
            ->filter(fn ($id) => $id !== null)
            ->map(fn ($id) => (string) $id)
            ->unique()->sort()->values()->all();

        $kunci = $soal->pilihanJawabans->where('is_benar', true)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->unique()->sort()->values()->all();

        $benar = count($dipilih) > 0 && $dipilih === $kunci;
        $status = $benar ? 'benar' : 'salah';
        $rekapKesalahan[$kategori]['tipe'][$tipe][$status]++;
    }

    $totalBenar = 0;
    $totalSalah = 0;
    foreach ($rekapKesalahan as $rekap) {
        foreach ($rekap['tipe'] as $hasilTipe) {
            $totalBenar += $hasilTipe['benar'];
            $totalSalah += $hasilTipe['salah'];
        }
    }
    $totalSoal = $totalBenar + $totalSalah;
@endphp

<h1>LAPORAN HASIL ASSESSMENT</h1>
<div class="subtitle">Sistem Penilaian Jabatan Karyawan</div>

<h3 class="section-title">Informasi Karyawan</h3>
<table>
    <tr><th>Nama</th><td>{{ $peserta->karyawan->nama }}</td></tr>
    <tr><th>NIK</th><td>{{ $peserta->karyawan->nik }}</td></tr>
    <tr><th>Assessment</th><td>{{ $assessment->nama_assessment }}</td></tr>
    <tr><th>Jabatan</th><td>{{ $peserta->karyawan->jabatan?->nama_jabatan ?? '-' }}</td></tr>
</table>

<h3 class="section-title">Ringkasan Nilai Tes</h3>
<div class="nilai">{{ number_format($hasilAssessment->nilai_akhir, 2) }}</div>
<table>
    <tr><th>Standar Kelulusan</th><td>{{ number_format($hasilAssessment->standar_nilai, 2) }}</td></tr>
    <tr><th>Status</th><td>{{ $hasilAssessment->status === 'lulus' ? 'LULUS' : 'TIDAK LULUS' }}</td></tr>
    <tr><th>Tanggal Ujian</th><td>{{ $hasilAssessment->tanggal_ujian?->format('d/m/Y H:i') ?? '-' }}</td></tr>
</table>

<h3 class="section-title">Ringkasan Hasil Pengerjaan</h3>
<table>
    <thead><tr><th>Total Soal</th><th>Jawaban Benar</th><th>Jawaban Salah</th></tr></thead>
    <tbody>
        <tr>
            <td style="text-align:center;font-weight:bold;">{{ $totalSoal }}</td>
            <td style="text-align:center;font-weight:bold;color:#166534;">{{ $totalBenar }}</td>
            <td style="text-align:center;font-weight:bold;color:#b91c1c;">{{ $totalSalah }}</td>
        </tr>
    </tbody>
</table>

<h3 class="section-title">Analisis Poin Pengetahuan</h3>
<table>
    <thead>
        <tr><th>Kategori Pengetahuan</th><th>Poin Maksimal</th><th>Poin Peserta</th><th>Persentase</th></tr>
    </thead>
    <tbody>
        @foreach ($analisisPengetahuan as $item)
            <tr>
                <td>{{ $item['nama'] }}</td>
                <td>{{ number_format($item['maksimal'], 2) }}</td>
                <td>{{ number_format($item['peserta'], 2) }}</td>
                <td>{{ $item['persentase'] === null ? '-' : number_format($item['persentase'], 2) . '%' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h3 class="section-title">Analisis Kesalahan Jawaban</h3>
<table>
    <thead><tr><th>Kategori</th><th>PG</th><th>MJ</th><th>Benar/Salah</th></tr></thead>
    <tbody>
        @foreach ($rekapKesalahan as $rekap)
            <tr>
                <td><strong>{{ $rekap['nama'] }}</strong></td>
                @foreach ($tipeAnalisis as $kodeTipe => $label)
                    @php $hasilTipe = $rekap['tipe'][$kodeTipe]; @endphp
                    <td>
                        <span style="color:#166534;">{{ $hasilTipe['benar'] }} Benar</span><br>
                        <span style="color:#b91c1c;">{{ $hasilTipe['salah'] }} Salah</span>
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
