@extends('layouts.hrd')

@section('title', 'Laporan & Analitik')

@section('page_title', 'Laporan & Analitik')

@section('content')

{{-- ========================================================== --}}
{{-- FILTER TAHUN --}}
{{-- ========================================================== --}}

<div class="card">

    <div
        style="
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:15px;
            flex-wrap:wrap;
        "
    >

        <div>

            <h1 style="margin:0;">
                Dashboard Analitik
            </h1>

            <p style="margin-bottom:0;">
                Ringkasan data karyawan, assessment,
                dan pengembangan untuk tahun {{ $tahun }}.
            </p>

        </div>


        <form
            method="GET"
            style="
                display:flex;
                gap:10px;
                align-items:center;
            "
        >

            <label>
                Tahun:
            </label>

            <select
                name="tahun"
                onchange="this.form.submit()"
                style="
                    padding:10px;
                    border:1px solid #d1d5db;
                    border-radius:6px;
                "
            >

                @foreach($tahunTersedia as $tahunItem)

                    <option
                        value="{{ $tahunItem }}"
                        {{ $tahun == $tahunItem
                            ? 'selected'
                            : '' }}
                    >
                        {{ $tahunItem }}
                    </option>

                @endforeach

            </select>

        </form>

    </div>

</div>


{{-- ========================================================== --}}
{{-- KPI MASTER --}}
{{-- ========================================================== --}}

<div
    style="
        display:grid;
        grid-template-columns:repeat(4, 1fr);
        gap:20px;
        margin-bottom:20px;
    "
>

    <div class="card" style="margin:0;">

        <small>
            Karyawan Aktif
        </small>

        <h2 style="margin:10px 0 0;">
            {{ $totalKaryawan }}
        </h2>

        <small>
            orang
        </small>

    </div>


    <div class="card" style="margin:0;">

        <small>
            Departemen
        </small>

        <h2 style="margin:10px 0 0;">
            {{ $totalDepartemen }}
        </h2>

        <small>
            departemen
        </small>

    </div>


    <div class="card" style="margin:0;">

        <small>
            Jabatan
        </small>

        <h2 style="margin:10px 0 0;">
            {{ $totalJabatan }}
        </h2>

        <small>
            jabatan
        </small>

    </div>


    <div class="card" style="margin:0;">

        <small>
            Skill
        </small>

        <h2 style="margin:10px 0 0;">
            {{ $totalSkill }}
        </h2>

        <small>
            skill
        </small>

    </div>

</div>


{{-- ========================================================== --}}
{{-- KPI ASSESSMENT --}}
{{-- ========================================================== --}}

<div
    style="
        display:grid;
        grid-template-columns:repeat(4, 1fr);
        gap:20px;
        margin-bottom:20px;
    "
>

    <div class="card" style="margin:0;">

        <small>
            Hasil Assessment
        </small>

        <h2 style="margin:10px 0 0;">
            {{ $totalHasilAssessment }}
        </h2>

        <small>
            tahun {{ $tahun }}
        </small>

    </div>


    <div class="card" style="margin:0;">

        <small>
            Lulus
        </small>

        <h2 style="margin:10px 0 0;">
            {{ $totalLulus }}
        </h2>

        <small>
            peserta
        </small>

    </div>


    <div class="card" style="margin:0;">

        <small>
            Tidak Lulus
        </small>

        <h2 style="margin:10px 0 0;">
            {{ $totalTidakLulus }}
        </h2>

        <small>
            peserta
        </small>

    </div>


    <div class="card" style="margin:0;">

        <small>
            Tingkat Kelulusan
        </small>

        <h2 style="margin:10px 0 0;">
            {{ number_format(
                $tingkatKelulusan,
                2
            ) }}%
        </h2>

        <small>
            rata-rata nilai:
            {{ number_format(
                $rataRataNilai,
                2
            ) }}
        </small>

    </div>

</div>


{{-- ========================================================== --}}
{{-- KPI PENGEMBANGAN --}}
{{-- ========================================================== --}}

<div
    style="
        display:grid;
        grid-template-columns:repeat(5, 1fr);
        gap:20px;
        margin-bottom:25px;
    "
>

    <div class="card" style="margin:0;">

        <small>
            Total Pengembangan
        </small>

        <h2 style="margin:10px 0 0;">
            {{ $totalPengembangan }}
        </h2>

    </div>


    <div class="card" style="margin:0;">

        <small>
            Diajukan
        </small>

        <h2 style="margin:10px 0 0;">
            {{ $pengembanganDiajukan }}
        </h2>

    </div>


    <div class="card" style="margin:0;">

        <small>
            Diproses
        </small>

        <h2 style="margin:10px 0 0;">
            {{ $pengembanganDiproses }}
        </h2>

    </div>


    <div class="card" style="margin:0;">

        <small>
            Disetujui
        </small>

        <h2 style="margin:10px 0 0;">
            {{ $pengembanganDisetujui }}
        </h2>

    </div>


    <div class="card" style="margin:0;">

        <small>
            Ditolak
        </small>

        <h2 style="margin:10px 0 0;">
            {{ $pengembanganDitolak }}
        </h2>

    </div>

</div>


{{-- ========================================================== --}}
{{-- CHART ASSESSMENT --}}
{{-- ========================================================== --}}

<div
    style="
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:20px;
        margin-bottom:20px;
    "
>

    <div class="card">

        <h2>
            Status Hasil Assessment
        </h2>

        <div style="height:300px;">

            <canvas id="assessmentStatusChart"></canvas>

        </div>

    </div>


    <div class="card">

        <h2>
            Pengembangan Berdasarkan Jenis
        </h2>

        <div style="height:300px;">

            <canvas id="pengembanganJenisChart"></canvas>

        </div>

    </div>

</div>


{{-- ========================================================== --}}
{{-- CHART DISTRIBUSI --}}
{{-- ========================================================== --}}

<div
    style="
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:20px;
        margin-bottom:20px;
    "
>

    <div class="card">

        <h2>
            Karyawan per Jabatan
        </h2>

        <div style="height:350px;">

            <canvas id="jabatanChart"></canvas>

        </div>

    </div>


    <div class="card">

        <h2>
            Karyawan per Departemen
        </h2>

        <div style="height:350px;">

            <canvas id="departemenChart"></canvas>

        </div>

    </div>

</div>


{{-- ========================================================== --}}
{{-- TREND ASSESSMENT --}}
{{-- ========================================================== --}}

<div class="card">

    <h2>
        Tren Hasil Assessment {{ $tahun }}
    </h2>

    <div style="height:350px;">

        <canvas id="assessmentTrendChart"></canvas>

    </div>

</div>


{{-- ========================================================== --}}
{{-- TABEL PENGEMBANGAN TERBARU --}}
{{-- ========================================================== --}}

<div class="card">

    <h2>
        Pengembangan Terbaru
    </h2>

    <table
        style="
            width:100%;
            border-collapse:collapse;
        "
    >

        <thead>

            <tr>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    No
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Karyawan
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Jenis
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Detail
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Status
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Tanggal
                </th>

            </tr>

        </thead>

        <tbody>

        @forelse($pengembanganTerbaru as $pengajuan)

            <tr>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                    {{ $loop->iteration }}
                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">

                    <strong>
                        {{ $pengajuan->nama_karyawan }}
                    </strong>

                    <br>

                    <small>
                        {{ $pengajuan->nik }}
                    </small>

                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">

                    {{ match($pengajuan->jenis_pengajuan) {
                        'kenaikan_jabatan' => 'Kenaikan Jabatan',
                        'pemindahan_jabatan' => 'Pemindahan Jabatan',
                        'peningkatan_skill' => 'Peningkatan Skill',
                        default => ucfirst(
                            $pengajuan->jenis_pengajuan
                        ),
                    } }}

                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">

                    @if($pengajuan->jenis_pengajuan === 'peningkatan_skill')

                        {{ $pengajuan->nama_skill ?? '-' }}

                        <br>

                        <small>
                            Level
                            {{ $pengajuan->level_sebelum ?? '-' }}
                            →
                            {{ $pengajuan->level_sesudah ?? '-' }}
                        </small>

                    @else

                        {{ $pengajuan->jabatan_asal ?? '-' }}

                        →

                        {{ $pengajuan->jabatan_tujuan ?? '-' }}

                    @endif

                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">

                    {{ ucfirst(
                        $pengajuan->status
                    ) }}

                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">

                    {{ \Carbon\Carbon::parse(
                        $pengajuan->tanggal
                    )->format('d-m-Y') }}

                </td>

            </tr>

        @empty

            <tr>

                <td
                    colspan="6"
                    style="
                        padding:25px;
                        text-align:center;
                    "
                >
                    Belum ada data pengembangan
                    pada tahun {{ $tahun }}.
                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

</div>


{{-- ========================================================== --}}
{{-- CHART.JS --}}
{{-- ========================================================== --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

    /*
    |--------------------------------------------------------------------------
    | Data dari Laravel
    |--------------------------------------------------------------------------
    */

    const assessmentLulus = {{ Js::from($totalLulus) }};
    const assessmentTidakLulus = {{ Js::from($totalTidakLulus) }};
    const jabatanLabels = {{ Js::from($karyawanPerJabatan->pluck('nama_jabatan')->values()) }};
    const jabatanData = {{ Js::from($karyawanPerJabatan->pluck('jumlah')->values()) }};
    const departemenLabels = {{ Js::from($karyawanPerDepartemen->pluck('nama_departemen')->values()) }};
    const departemenData = {{ Js::from($karyawanPerDepartemen->pluck('jumlah')->values()) }};
    const jenisLabels = {{ Js::from($labelPengembanganJenis) }};
    const jenisData = {{ Js::from($dataPengembanganJenis) }};
    const bulanLabels = {{ Js::from($labelBulan) }};
    const lulusBulanan = {{ Js::from($dataLulusBulanan) }};
    const tidakLulusBulanan = {{ Js::from($dataTidakLulusBulanan) }};
    const labelPengembanganJenis = {{ Js::from($labelPengembanganJenis) }};
    const dataPengembanganJenis = {{ Js::from($dataPengembanganJenis) }};


    /*
    |--------------------------------------------------------------------------
    | Chart status assessment
    |--------------------------------------------------------------------------
    */

    new Chart(
        document.getElementById(
            'assessmentStatusChart'
        ),
        {
            type: 'doughnut',

            data: {
                labels: [
                    'Lulus',
                    'Tidak Lulus'
                ],

                datasets: [
                    {
                        data: [
                            assessmentLulus,
                            assessmentTidakLulus
                        ]
                    }
                ]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Chart pengembangan berdasarkan jenis
    |--------------------------------------------------------------------------
    */

    new Chart(
        document.getElementById(
            'pengembanganJenisChart'
        ),
        {
            type: 'pie',

            data: {
                labels: jenisLabels,

                datasets: [
                    {
                        data: jenisData
                    }
                ]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Chart karyawan per jabatan
    |--------------------------------------------------------------------------
    */

    new Chart(
        document.getElementById(
            'jabatanChart'
        ),
        {
            type: 'bar',

            data: {
                labels: jabatanLabels,

                datasets: [
                    {
                        label: 'Karyawan',
                        data: jabatanData
                    }
                ]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Chart karyawan per departemen
    |--------------------------------------------------------------------------
    */

    new Chart(
        document.getElementById(
            'departemenChart'
        ),
        {
            type: 'bar',

            data: {
                labels: departemenLabels,

                datasets: [
                    {
                        label: 'Karyawan',
                        data: departemenData
                    }
                ]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Trend assessment bulanan
    |--------------------------------------------------------------------------
    */

    new Chart(
        document.getElementById(
            'assessmentTrendChart'
        ),
        {
            type: 'line',

            data: {
                labels: bulanLabels,

                datasets: [

                    {
                        label: 'Lulus',
                        data: lulusBulanan,
                        tension: 0.3
                    },

                    {
                        label: 'Tidak Lulus',
                        data: tidakLulusBulanan,
                        tension: 0.3
                    }

                ]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        }
    );

</script>

@endsection