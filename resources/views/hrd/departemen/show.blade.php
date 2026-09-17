@extends('layouts.hrd')

@section('title', 'Detail Departemen')

@section('page_title', 'Detail Departemen')

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
            Detail Departemen
        </h1>

        <div>

            <a
                href="{{ route('hrd.departemen.index') }}"
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
                    'hrd.departemen.edit',
                    $departemen
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
            grid-template-columns:200px 1fr;
        "
    >

        <div
            style="
                padding:12px 0;
                font-weight:bold;
                border-bottom:1px solid #e5e7eb;
            "
        >
            Nama Departemen
        </div>

        <div
            style="
                padding:12px 0;
                border-bottom:1px solid #e5e7eb;
            "
        >
            {{ $departemen->nama_departemen }}
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
            {{ $departemen->karyawans->count() }}
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
            {{ $departemen->deskripsi ?: '-' }}
        </div>

    </div>

</div>


<div class="card">

    <h2>
        Karyawan dalam Departemen
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
                    Jabatan
                </th>

            </tr>

        </thead>

        <tbody>

        @forelse($departemen->karyawans as $karyawan)

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
                    {{ $karyawan->jabatan->nama_jabatan }}
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
                    Belum ada karyawan di departemen ini.
                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

</div>

@endsection