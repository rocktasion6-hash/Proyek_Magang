@extends('layouts.hrd')
@section('title', 'Detail Jabatan')
@section('page_title', 'Detail Jabatan')
@section('content')

<div class="card">

    <div
        style="
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:10px;
            flex-wrap:wrap;
            margin-bottom:20px;
        "
    >

        <h1 style="margin:0;">
            Detail Jabatan
        </h1>

        <div>

            <a
                href="{{ route('hrd.jabatan.index') }}"
                class="btn"
                style="
                    background:#6b7280;
                    color:white;
                "
            >
                ← Kembali
            </a>

            <a
                href="{{ route(
                    'hrd.jabatan.edit',
                    $jabatan
                ) }}"
                class="btn"
                style="
                    background:#f59e0b;
                    color:white;
                "
            >
                Edit
            </a>

        </div>

    </div>


    <div
        style="
            display:grid;
            grid-template-columns:220px 1fr;
        "
    >

        <div
            style="
                padding:12px 0;
                font-weight:bold;
                border-bottom:1px solid #e5e7eb;
            "
        >
            Nama Jabatan
        </div>

        <div
            style="
                padding:12px 0;
                border-bottom:1px solid #e5e7eb;
            "
        >
            {{ $jabatan->nama_jabatan }}
        </div>


        <div
            style="
                padding:12px 0;
                font-weight:bold;
                border-bottom:1px solid #e5e7eb;
            "
        >
            Level Jabatan
        </div>

        <div
            style="
                padding:12px 0;
                border-bottom:1px solid #e5e7eb;
            "
        >
            Level {{ $jabatan->level_jabatan }}
        </div>


        <div
            style="
                padding:12px 0;
                font-weight:bold;
                border-bottom:1px solid #e5e7eb;
            "
        >
            Standar Nilai
        </div>

        <div
            style="
                padding:12px 0;
                border-bottom:1px solid #e5e7eb;
            "
        >
            {{ number_format(
                $jabatan->standar_nilai,
                2
            ) }}
        </div>


        <div
            style="
                padding:12px 0;
                font-weight:bold;
                border-bottom:1px solid #e5e7eb;
            "
        >
            Jumlah Karyawan
        </div>

        <div
            style="
                padding:12px 0;
                border-bottom:1px solid #e5e7eb;
            "
        >
            {{ $jabatan->karyawans->count() }}
            orang
        </div>


        <div
            style="
                padding:12px 0;
                font-weight:bold;
                border-bottom:1px solid #e5e7eb;
            "
        >
            Deskripsi
        </div>

        <div
            style="
                padding:12px 0;
                border-bottom:1px solid #e5e7eb;
            "
        >
            {{ $jabatan->deskripsi ?: '-' }}
        </div>

    </div>

</div>


<div class="card">

    <h2>
        Skill yang Dibutuhkan
    </h2>

    <table style="width:100%; border-collapse:collapse;">

        <thead>

            <tr>

                <th
                    style="
                        padding:12px;
                        text-align:left;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    No
                </th>

                <th
                    style="
                        padding:12px;
                        text-align:left;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    Skill
                </th>

                <th
                    style="
                        padding:12px;
                        text-align:left;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    Level Dibutuhkan
                </th>

            </tr>

        </thead>

        <tbody>

        @forelse($jabatan->skills as $skill)

            <tr>

                <td
                    style="
                        padding:12px;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    {{ $loop->iteration }}
                </td>

                <td
                    style="
                        padding:12px;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    {{ $skill->nama_skill }}
                </td>

                <td
                    style="
                        padding:12px;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    Level
                    {{ $skill->pivot->level_dibutuhkan }}
                </td>

            </tr>

        @empty

            <tr>

                <td
                    colspan="3"
                    style="
                        padding:25px;
                        text-align:center;
                    "
                >
                    Belum ada skill yang dikaitkan dengan jabatan ini.
                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

</div>


<div class="card">

    <h2>
        Karyawan pada Jabatan Ini
    </h2>

    <table style="width:100%; border-collapse:collapse;">

        <thead>

            <tr>

                <th
                    style="
                        padding:12px;
                        text-align:left;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    No
                </th>

                <th
                    style="
                        padding:12px;
                        text-align:left;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    NIK
                </th>

                <th
                    style="
                        padding:12px;
                        text-align:left;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    Nama
                </th>

                <th
                    style="
                        padding:12px;
                        text-align:left;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    Departemen
                </th>

            </tr>

        </thead>

        <tbody>

        @forelse($jabatan->karyawans as $karyawan)

            <tr>

                <td
                    style="
                        padding:12px;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    {{ $loop->iteration }}
                </td>

                <td
                    style="
                        padding:12px;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    {{ $karyawan->nik }}
                </td>

                <td
                    style="
                        padding:12px;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    {{ $karyawan->nama }}
                </td>

                <td
                    style="
                        padding:12px;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    {{ $karyawan->departemen->nama_departemen }}
                </td>

            </tr>

        @empty

            <tr>

                <td
                    colspan="4"
                    style="
                        padding:25px;
                        text-align:center;
                    "
                >
                    Belum ada karyawan pada jabatan ini.
                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

</div>


<div class="card">

    <h2>
        Assessment yang Menggunakan Jabatan Ini
    </h2>

    <table style="width:100%; border-collapse:collapse;">

        <thead>

            <tr>

                <th
                    style="
                        padding:12px;
                        text-align:left;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    No
                </th>

                <th
                    style="
                        padding:12px;
                        text-align:left;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    Nama Assessment
                </th>

                <th
                    style="
                        padding:12px;
                        text-align:left;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    Standar
                </th>

                <th
                    style="
                        padding:12px;
                        text-align:left;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    Status
                </th>

            </tr>

        </thead>

        <tbody>

        @forelse($jabatan->assessments as $assessment)

            <tr>

                <td
                    style="
                        padding:12px;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    {{ $loop->iteration }}
                </td>

                <td
                    style="
                        padding:12px;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    {{ $assessment->nama_assessment }}
                </td>

                <td
                    style="
                        padding:12px;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    {{ number_format(
                        $assessment->standar_nilai,
                        2
                    ) }}
                </td>

                <td
                    style="
                        padding:12px;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    {{ ucfirst($assessment->status) }}
                </td>

            </tr>

        @empty

            <tr>

                <td
                    colspan="4"
                    style="
                        padding:25px;
                        text-align:center;
                    "
                >
                    Belum ada assessment untuk jabatan ini.
                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

</div>

@endsection