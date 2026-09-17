<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Manajemen Karyawan</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        h1 {
            margin-bottom: 5px;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-detail {
            background: #111827;
            color: white;
        }

        .btn-edit {
            background: #f59e0b;
            color: white;
        }

        .filter {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .filter form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        input,
        select {
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
        }

        input {
            min-width: 250px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        th {
            background: #f9fafb;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .aktif {
            background: #dcfce7;
            color: #166534;
        }

        .nonaktif {
            background: #fee2e2;
            color: #991b1b;
        }

        .alert {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .success {
            background: #dcfce7;
            color: #166534;
        }

    </style>

</head>

<body>
@extends('layouts.hrd')
@section('title', 'Manajemen Karyawan')
@section('page_title', 'Manajemen Karyawan')
@section('content')
<div class="container">

    <div class="header">

        <div>

            <h1>Manajemen Karyawan</h1>

            <p>
                Mengelola data karyawan perusahaan.
            </p>

        </div>

        <a
            href="{{ route('hrd.karyawan.create') }}"
            class="btn btn-primary"
        >
            + Tambah Karyawan
        </a>

    </div>

    @if(session('success'))

        <div class="alert success">
            {{ session('success') }}
        </div>

    @endif

    <div class="filter">

        <form method="GET">

            <input
                type="text"
                name="search"
                placeholder="Cari nama atau NIK..."
                value="{{ request('search') }}"
            >

            <select name="departemen_id">

                <option value="">
                    Semua Departemen
                </option>

                @foreach($departemens as $departemen)

                    <option
                        value="{{ $departemen->id }}"
                        {{ request('departemen_id') == $departemen->id ? 'selected' : '' }}
                    >
                        {{ $departemen->nama_departemen }}
                    </option>

                @endforeach

            </select>

            <select name="status">

                <option value="">
                    Semua Status
                </option>

                <option
                    value="aktif"
                    {{ request('status') === 'aktif' ? 'selected' : '' }}
                >
                    Aktif
                </option>

                <option
                    value="nonaktif"
                    {{ request('status') === 'nonaktif' ? 'selected' : '' }}
                >
                    Nonaktif
                </option>

            </select>

            <button
                type="submit"
                class="btn btn-primary"
            >
                Filter
            </button>

        </form>

    </div>

    <table>

        <thead>

            <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama</th>
                <th>Departemen</th>
                <th>Jabatan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>

        </thead>

        <tbody>

        @forelse($karyawans as $karyawan)

            <tr>

                <td>
                    {{ $karyawans->firstItem() + $loop->index }}
                </td>

                <td>
                    {{ $karyawan->nik }}
                </td>

                <td>
                    <strong>
                        {{ $karyawan->nama }}
                    </strong>
                </td>

                <td>
                    {{ $karyawan->departemen->nama_departemen }}
                </td>

                <td>
                    {{ $karyawan->jabatan->nama_jabatan }}
                </td>

                <td>

                    <span class="badge {{ $karyawan->status }}">
                        {{ ucfirst($karyawan->status) }}
                    </span>

                </td>

                <td>

                    <a
                        href="{{ route(
                            'hrd.karyawan.show',
                            $karyawan
                        ) }}"
                        class="btn btn-detail"
                    >
                        Detail
                    </a>

                    <a
                        href="{{ route(
                            'hrd.karyawan.edit',
                            $karyawan
                        ) }}"
                        class="btn btn-edit"
                    >
                        Edit
                    </a>

                </td>

            </tr>

        @empty

            <tr>

                <td
                    colspan="7"
                    style="text-align:center;"
                >
                    Belum ada data karyawan.
                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

    <div style="margin-top:20px;">
        {{ $karyawans->links() }}
    </div>

</div>

@endsection
</body>
</html>