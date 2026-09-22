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

    <div
        style="
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:10px;
            flex-wrap:wrap;
        "
    >

        <div>

            <h2>
                Skill yang Dibutuhkan
            </h2>

            <p>
                Tentukan skill dan level minimum
                yang dibutuhkan untuk jabatan ini.
            </p>

        </div>

    </div>


    @if($availableSkills->count() > 0)

        <form
            method="POST"
            action="{{ route(
                'hrd.jabatan.skill.store',
                $jabatan
            ) }}"
            style="
                background:#f9fafb;
                padding:20px;
                border-radius:8px;
                margin-bottom:20px;
            "
        >

            @csrf

            <div
                style="
                    display:grid;
                    grid-template-columns:1fr 200px auto;
                    gap:10px;
                    align-items:end;
                "
            >

                <div>

                    <label
                        style="
                            display:block;
                            margin-bottom:7px;
                            font-weight:bold;
                        "
                    >
                        Skill
                    </label>

                    <select
                        name="skill_id"
                        required
                        style="
                            width:100%;
                            padding:10px;
                            border:1px solid #d1d5db;
                            border-radius:6px;
                        "
                    >

                        <option value="">
                            -- Pilih Skill --
                        </option>

                        @foreach($availableSkills as $skill)

                            <option
                                value="{{ $skill->id }}"
                            >
                                {{ $skill->nama_skill }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div>

                    <label
                        style="
                            display:block;
                            margin-bottom:7px;
                            font-weight:bold;
                        "
                    >
                        Level Dibutuhkan
                    </label>

                    <select
                        name="level_dibutuhkan"
                        required
                        style="
                            width:100%;
                            padding:10px;
                            border:1px solid #d1d5db;
                            border-radius:6px;
                        "
                    >

                        @for($level = 1; $level <= 5; $level++)

                            <option
                                value="{{ $level }}"
                            >
                                Level {{ $level }}
                            </option>

                        @endfor

                    </select>

                </div>


                <button
                    type="submit"
                    class="btn"
                    style="
                        background:#2563eb;
                        color:white;
                    "
                >
                    + Tambah
                </button>

            </div>

        </form>

    @endif


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
                    Skill
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Level Dibutuhkan
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Aksi
                </th>

            </tr>

        </thead>

        <tbody>

        @forelse($jabatan->jabatanSkills as $jabatanSkill)

            <tr>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                    {{ $loop->iteration }}
                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">

                    <strong>
                        {{ $jabatanSkill->skill->nama_skill }}
                    </strong>

                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">

                    Level
                    {{ $jabatanSkill->level_dibutuhkan }}

                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">

                    <form
                        method="POST"
                        action="{{ route(
                            'hrd.jabatan.skill.destroy',
                            [
                                'jabatan' => $jabatan,
                                'jabatanSkill' => $jabatanSkill
                            ]
                        ) }}"
                        onsubmit="return confirm(
                            'Hapus skill ini dari jabatan?'
                        )"
                        style="display:inline;"
                    >

                        @csrf

                        @method('DELETE')

                        <button
                            type="submit"
                            class="btn"
                            style="
                                background:#dc2626;
                                color:white;
                            "
                        >
                            Hapus
                        </button>

                    </form>

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
                    Belum ada skill yang dibutuhkan
                    untuk jabatan ini.
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