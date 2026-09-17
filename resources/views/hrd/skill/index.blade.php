@extends('layouts.hrd')

@section('title', 'Manajemen Skill')

@section('page_title', 'Manajemen Skill')

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
                Manajemen Skill
            </h1>

            <p>
                Mengelola kompetensi dan skill yang digunakan dalam sistem.
            </p>

        </div>

        <a
            href="{{ route('hrd.skill.create') }}"
            class="btn"
            style="
                background:#2563eb;
                color:white;
            "
        >
            + Tambah Skill
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
            placeholder="Cari skill..."
            style="
                padding:10px;
                border:1px solid #d1d5db;
                border-radius:6px;
                min-width:280px;
            "
        >

        <button
            type="submit"
            class="btn"
            style="
                background:#2563eb;
                color:white;
            "
        >
            Cari
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

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    No
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Skill
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Deskripsi
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Jabatan
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Karyawan
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Soal
                </th>

                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">
                    Aksi
                </th>

            </tr>

        </thead>

        <tbody>

        @forelse($skills as $skill)

            <tr>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">

                    {{ $skills->firstItem() + $loop->index }}

                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">

                    <strong>
                        {{ $skill->nama_skill }}
                    </strong>

                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">

                    {{ $skill->deskripsi ?: '-' }}

                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">

                    {{ $skill->jabatan_skills_count }}

                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">

                    {{ $skill->karyawan_skills_count }}

                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">

                    {{ $skill->soals_count }}

                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">

                    <a
                        href="{{ route(
                            'hrd.skill.show',
                            $skill
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
                    Belum ada data skill.
                </td>

            </tr>

        @endforelse

        </tbody>

    </table>


    <div style="margin-top:20px;">

        {{ $skills->links() }}

    </div>

</div>

@endsection