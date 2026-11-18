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
        style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;"
    >

        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Cari jabatan..."
            style="padding:10px;border:1px solid #d1d5db;border-radius:6px;min-width:250px;"
        >

        <select
            name="tipe_jabatan"
            style="padding:10px;border:1px solid #d1d5db;border-radius:6px;"
        >
            <option value="">Semua Tipe</option>
            <option value="operasional" {{ request('tipe_jabatan') === 'operasional' ? 'selected' : '' }}>Operasional</option>
            <option value="struktural" {{ request('tipe_jabatan') === 'struktural' ? 'selected' : '' }}>Struktural</option>
        </select>

        <button type="submit" class="btn" style="background:#2563eb;color:white;">
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
                    Tipe
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

                    @if($jabatan->tipe_jabatan === 'struktural')
                        <span style="background:#fef3c7;color:#92400e;padding:3px 8px;border-radius:4px;font-size:13px;">
                            Struktural
                            @if($jabatan->urutan_struktural)
                                ({{ $jabatan->urutan_struktural }})
                            @endif
                        </span>
                    @else
                        <span style="background:#eff6ff;color:#1e40af;padding:3px 8px;border-radius:4px;font-size:13px;">
                            Operasional
                        </span>
                    @endif

                </td>

                <td style="padding:12px; border-bottom:1px solid #e5e7eb;">

                    @if($jabatan->tipe_jabatan === 'struktural')
                        @php
                            $labelUrutan = [
                                1 => 'Wakil Foreman',
                                2 => 'Foreman',
                                3 => 'Wakil Supervisor',
                                4 => 'Supervisor',
                                5 => 'Wakil General Manager',
                                6 => 'Manager',
                            ];
                        @endphp
                        {{ $labelUrutan[$jabatan->urutan_struktural] ?? '-' }}
                    @elseif(mb_strtolower(trim($jabatan->nama_jabatan)) === 'crew')
                        Crew
                    @else
                        Level {{ $jabatan->level_jabatan }}
                    @endif

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

                    <div
                        style="
                            display:flex;
                            align-items:center;
                            gap:8px;
                            flex-wrap:wrap;
                        "
                    >

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

                        <form
                            method="POST"
                            action="{{ route(
                                'hrd.jabatan.destroy',
                                $jabatan
                            ) }}"
                            onsubmit="return confirm('Yakin ingin menghapus jabatan ini?')"
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
                    </div>

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