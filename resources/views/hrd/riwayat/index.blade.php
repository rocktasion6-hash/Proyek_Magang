<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Riwayat Karyawan</title>

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
            margin-bottom: 25px;
        }

        h1 {
            margin-bottom: 5px;
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

        input {
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            min-width: 280px;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            text-decoration: none;
            border: none;
            border-radius: 6px;
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

        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            background: #dcfce7;
            color: #166534;
        }

    </style>

</head>

<body>

<div class="container">

    <div class="header">

        <h1>Riwayat Karyawan</h1>

        <p>
            Melihat riwayat perubahan jabatan dan perkembangan skill karyawan.
        </p>

    </div>

    <div class="filter">

        <form method="GET">

            <input
                type="text"
                name="search"
                placeholder="Cari nama atau NIK..."
                value="{{ request('search') }}"
            >

            <button
                type="submit"
                class="btn btn-primary"
            >
                Cari
            </button>

        </form>

    </div>

    <table>

        <thead>

            <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama Karyawan</th>
                <th>Departemen</th>
                <th>Jabatan Saat Ini</th>
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

                    <span class="status">
                        {{ ucfirst($karyawan->status) }}
                    </span>

                </td>

                <td>

                    <a
                        href="{{ route(
                            'hrd.riwayat.show',
                            $karyawan
                        ) }}"
                        class="btn btn-detail"
                    >
                        Lihat Riwayat
                    </a>

                </td>

            </tr>

        @empty

            <tr>

                <td
                    colspan="7"
                    style="text-align:center;"
                >
                    Data karyawan tidak ditemukan.
                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

    <div style="margin-top:20px;">
        {{ $karyawans->links() }}
    </div>

</div>

</body>

</html>