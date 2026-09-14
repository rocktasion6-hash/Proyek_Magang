<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kenaikan Jabatan</title>

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
            margin: 0;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-detail {
            background: #111827;
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

        .diajukan {
            background: #fef3c7;
            color: #92400e;
        }

        .diproses {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .disetujui {
            background: #dcfce7;
            color: #166534;
        }

        .ditolak {
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

        .error {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="header">
        <div>
            <h1>Kenaikan Jabatan</h1>
            <p>Manajemen proses kenaikan jabatan karyawan.</p>
        </div>

        <a
            href="{{ route('hrd.kenaikan-jabatan.create') }}"
            class="btn btn-primary"
        >
            + Buat Pengajuan
        </a>
    </div>

    @if(session('success'))
        <div class="alert success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert error">
            {{ session('error') }}
        </div>
    @endif

    <div class="filter">

        <form method="GET">

            <input
                type="text"
                name="search"
                placeholder="Cari nama / NIK..."
                value="{{ request('search') }}"
            >

            <select name="status">
                <option value="">Semua Status</option>
                <option
                    value="diajukan"
                    {{ request('status') === 'diajukan' ? 'selected' : '' }}
                >
                    Diajukan
                </option>

                <option
                    value="diproses"
                    {{ request('status') === 'diproses' ? 'selected' : '' }}
                >
                    Diproses
                </option>

                <option
                    value="disetujui"
                    {{ request('status') === 'disetujui' ? 'selected' : '' }}
                >
                    Disetujui
                </option>

                <option
                    value="ditolak"
                    {{ request('status') === 'ditolak' ? 'selected' : '' }}
                >
                    Ditolak
                </option>
            </select>

            <button class="btn btn-primary">
                Filter
            </button>

        </form>

    </div>

    <table>

        <thead>
            <tr>
                <th>No</th>
                <th>Karyawan</th>
                <th>Jabatan Asal</th>
                <th>Jabatan Tujuan</th>
                <th>Nilai Assessment</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>

            @forelse($pengajuans as $pengajuan)

                <tr>

                    <td>
                        {{ $pengajuans->firstItem() + $loop->index }}
                    </td>

                    <td>
                        <strong>
                            {{ $pengajuan->karyawan->nama }}
                        </strong>
                        <br>
                        <small>
                            {{ $pengajuan->karyawan->nik }}
                        </small>
                    </td>

                    <td>
                        {{ $pengajuan->jabatanAsal->nama_jabatan }}
                    </td>

                    <td>
                        {{ $pengajuan->jabatanTujuan->nama_jabatan }}
                    </td>

                    <td>
                        {{ number_format($pengajuan->hasilAssessment->nilai_akhir, 2) }}
                    </td>

                    <td>

                        <span class="badge {{ $pengajuan->status }}">
                            {{ ucfirst($pengajuan->status) }}
                        </span>

                    </td>

                    <td>
                        {{ \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->format('d-m-Y') }}
                    </td>

                    <td>

                        <a
                            href="{{ route(
                                'hrd.kenaikan-jabatan.show',
                                $pengajuan
                            ) }}"
                            class="btn btn-detail"
                        >
                            Detail
                        </a>

                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="8" style="text-align:center;">
                        Belum ada pengajuan kenaikan jabatan.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

    <div style="margin-top:20px;">
        {{ $pengajuans->links() }}
    </div>

</div>

</body>
</html>