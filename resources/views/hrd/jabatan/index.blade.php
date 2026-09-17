@extends('layouts.hrd')
@section('title', 'Manajemen Jabatan')
@section('page_title', 'Manajemen Jabatan')
@section('content')

<div class="card">

    <div
        style="
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:15px;
            flex-wrap:wrap;
            margin-bottom:20px;
        "
    >

        <div>

            <h1 style="margin:0;">
                Manajemen Jabatan
            </h1>

            <p>
                Mengelola jabatan, level, dan standar nilai assessment.
            </p>

        </div>

        <a
            href="{{ route('hrd.jabatan.create') }}"
            class="btn"
            style="
                background:#2563eb;
                color:white;
            "
        >
            + Tambah Jabatan
        </a>

    </div>


    <form
        method="GET"
        style="
            display:flex;
            gap:10px;
            margin-bottom:20px;
            flex-wrap:wrap;
        "
    >

        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Cari jabatan..."
            style="
                padding:10px;
                border:1px solid #d1d5db;
                border-radius:6px;
                min-width:250px;
            "
        >

        <select
            name="level_jabatan"
            style="
                padding:10px;
                border:1px solid #d1d5db;
                border-radius:6px;
            "
        >

            <option value="">
                Semua Level
            </option>

            @foreach($levels as $level)

                <option
                    value="{{ $level }}"
                    {{ request('level_jabatan') == $level
                        ? 'selected'
                        : '' }}
                >
                    Level {{ $level }}
                </option>

            @endforeach

        </select>

        <button
            type="submit"
            class="btn"
            style="
                background:#2563eb;
                color:white;
            "
        >
            Filter
        </button>

    </form>


    <table
        style="
            width:100%;
            border-collapse:collapse;
        "
    >

        <thead>

            <tr>

                <th style="padding:12px; text-align:left; border-bottom:1px solid #e5e7eb;">
                    No
                </th>

                <th style="padding:12px; text-align:left; border-bottom:1px solid #e5e7eb;">
                    Jabatan
                </th>

                <th style="padding:12px; text-align:left; border-bottom:1px solid #e5e7eb;">
                    Level
                </th>

                <th style="padding:12px; text-align:left; border-bottom:1px solid #e5e7eb;">
                    Standar Nilai
                </th>

                <th style="padding:12px; text-align:left; border-bottom:1px solid #e5e7eb;">
                    Karyawan
                </th>

                <th style="padding:12px; text-align:left; border-bottom:1px solid #e5e7eb;">
                    Skill
                </th>

                <th style="padding:12px; text-align:left; border-bottom:1px solid #e5e7eb;">
                    Aksi
                </th>

            </tr>

        </thead>

        <tbody>

        @forelse($jabatans as $jabatan)

            <tr>

                <td style="padding:12px; border-bottom:1px solid #e5e7eb;">

                    {{ $jabatans->firstItem() + $loop->index }}

                </td>

                <td style="padding:12px; border-bottom:1px solid #e5e7eb;">

                    <strong>
                        {{ $jabatan->nama_jabatan }}
                    </strong>

                </td>

                <td style="padding:12px; border-bottom:1px solid #e5e7eb;">

                    Level {{ $jabatan->level_jabatan }}

                </td>

                <td style="padding:12px; border-bottom:1px solid #e5e7eb;">

                    <strong>
                        {{ number_format(
                            $jabatan->standar_nilai,
                            2
                        ) }}
                    </strong>

                </td>

                <td style="padding:12px; border-bottom:1px solid #e5e7eb;">

                    {{ $jabatan->karyawans_count }}
                    orang

                </td>

                <td style="padding:12px; border-bottom:1px solid #e5e7eb;">

                    {{ $jabatan->skills_count }}
                    skill

                </td>

                <td style="padding:12px; border-bottom:1px solid #e5e7eb;">

                    <a
                        href="{{ route(
                            'hrd.jabatan.show',
                            $jabatan
                        ) }}"
                        class="btn"
                        style="
                            background:#111827;
                            color:white;
                        "
                    >
                        Detail
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

                </td>

            </tr>

        @empty

            <tr>

                <td
                    colspan="7"
                    style="
                        padding:25px;
                        text-align:center;
                    "
                >
                    Belum ada data jabatan.
                </td>

            </tr>

        @endforelse

        </tbody>

    </table>


    <div style="margin-top:20px;">
        {{ $jabatans->links() }}
    </div>

</div>

@endsection