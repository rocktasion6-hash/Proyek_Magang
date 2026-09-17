@extends('layouts.hrd')

@section('title', 'Manajemen Departemen')

@section('page_title', 'Manajemen Departemen')

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
                Manajemen Departemen
            </h1>

            <p>
                Mengelola data departemen perusahaan.
            </p>

        </div>

        <a
            href="{{ route('hrd.departemen.create') }}"
            class="btn"
            style="
                background:#2563eb;
                color:white;
            "
        >
            + Tambah Departemen
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
            placeholder="Cari departemen..."
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
                    Nama Departemen
                </th>

                <th
                    style="
                        padding:12px;
                        text-align:left;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    Deskripsi
                </th>

                <th
                    style="
                        padding:12px;
                        text-align:left;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    Jumlah Karyawan
                </th>

                <th
                    style="
                        padding:12px;
                        text-align:left;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    Aksi
                </th>

            </tr>

        </thead>

        <tbody>

        @forelse($departemens as $departemen)

            <tr>

                <td
                    style="
                        padding:12px;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    {{ $departemens->firstItem() + $loop->index }}
                </td>

                <td
                    style="
                        padding:12px;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    <strong>
                        {{ $departemen->nama_departemen }}
                    </strong>
                </td>

                <td
                    style="
                        padding:12px;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    {{ $departemen->deskripsi ?: '-' }}
                </td>

                <td
                    style="
                        padding:12px;
                        border-bottom:1px solid #e5e7eb;
                    "
                >
                    {{ $departemen->karyawans_count }}
                    orang
                </td>

                <td
                    style="
                        padding:12px;
                        border-bottom:1px solid #e5e7eb;
                    "
                >

                    <a
                        href="{{ route(
                            'hrd.departemen.show',
                            $departemen
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
                    Belum ada data departemen.
                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

    <div style="margin-top:20px;">
        {{ $departemens->links() }}
    </div>

</div>

@endsection