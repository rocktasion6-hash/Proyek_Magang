@extends('layouts.hrd')

@section('title', 'Detail Skill')

@section('page_title', 'Detail Skill')

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
            Detail Skill
        </h1>

        <div>

            <a
                href="{{ route('hrd.skill.index') }}"
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
                    'hrd.skill.edit',
                    $skill
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

        <div style="padding:12px 0;font-weight:bold;border-bottom:1px solid #e5e7eb;">
            Nama Skill
        </div>

        <div style="padding:12px 0;border-bottom:1px solid #e5e7eb;">
            {{ $skill->nama_skill }}
        </div>


        <div style="padding:12px 0;font-weight:bold;border-bottom:1px solid #e5e7eb;">
            Deskripsi
        </div>

        <div style="padding:12px 0;border-bottom:1px solid #e5e7eb;">
            {{ $skill->deskripsi ?: '-' }}
        </div>

    </div>

</div>


{{-- ================================================= --}}
{{-- JABATAN YANG MEMBUTUHKAN SKILL --}}
{{-- ================================================= --}}

<div class="card">

    <h2>
        Jabatan yang Membutuhkan Skill
    </h2>

    <table style="width:100%;border-collapse:collapse;">

        <thead>

            <tr>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    No
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Jabatan
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Level Jabatan
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Level Skill Dibutuhkan
                </th>

            </tr>

        </thead>

        <tbody>

        @forelse($skill->jabatans as $jabatan)

            <tr>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                    {{ $loop->iteration }}
                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">

                    <strong>
                        {{ $jabatan->nama_jabatan }}
                    </strong>

                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">

                    Level {{ $jabatan->level_jabatan }}

                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">

                    Level {{ $jabatan->pivot->level_dibutuhkan }}

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
                    Belum ada jabatan yang membutuhkan skill ini.
                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

</div>


{{-- ================================================= --}}
{{-- KARYAWAN YANG MEMILIKI SKILL --}}
{{-- ================================================= --}}

<div class="card">

    <h2>
        Karyawan yang Memiliki Skill
    </h2>

    <table style="width:100%;border-collapse:collapse;">

        <thead>

            <tr>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    No
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    NIK
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Nama
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Departemen
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Jabatan
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Level Skill
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Tanggal Penilaian
                </th>

            </tr>

        </thead>

        <tbody>

        @forelse($skill->karyawans as $karyawan)

            <tr>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                    {{ $loop->iteration }}
                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                    {{ $karyawan->nik }}
                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">

                    <strong>
                        {{ $karyawan->nama }}
                    </strong>

                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                    {{ $karyawan->departemen->nama_departemen }}
                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                    {{ $karyawan->jabatan->nama_jabatan }}
                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                    Level {{ $karyawan->pivot->level_skill }}
                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">

                    @if($karyawan->pivot->tanggal_penilaian)

                        {{ \Carbon\Carbon::parse(
                            $karyawan->pivot->tanggal_penilaian
                        )->format('d-m-Y') }}

                    @else

                        -

                    @endif

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
                    Belum ada karyawan yang memiliki skill ini.
                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

</div>


{{-- ================================================= --}}
{{-- BANK SOAL --}}
{{-- ================================================= --}}

<div class="card">

    <h2>
        Bank Soal Menggunakan Skill Ini
    </h2>

    <table style="width:100%;border-collapse:collapse;">

        <thead>

            <tr>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    No
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Pertanyaan
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Jabatan
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Tipe
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Tingkat
                </th>

            </tr>

        </thead>

        <tbody>

        @forelse($skill->soals as $soal)

            <tr>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                    {{ $loop->iteration }}
                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                    {{ $soal->pertanyaan }}
                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                    {{ $soal->jabatan->nama_jabatan }}
                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                    {{ str_replace(
                        '_',
                        ' ',
                        ucfirst($soal->tipe_soal)
                    ) }}
                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                    {{ ucfirst($soal->tingkat_kesulitan) }}
                </td>

            </tr>

        @empty

            <tr>

                <td
                    colspan="5"
                    style="
                        padding:25px;
                        text-align:center;
                    "
                >
                    Belum ada soal untuk skill ini.
                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

</div>

@endsection