@extends('layouts.hrd')
@section('title', 'Manajemen Skill')
@section('page_title', 'Manajemen Skill')
@section('content')

<div class="card">

    <div style="display:flex;justify-content:space-between;align-items:center;gap:15px;flex-wrap:wrap;margin-bottom:20px;">

        <div>
            <h1 style="margin:0;">Manajemen Skill</h1>
            <p>Jalur peningkatan skill karyawan operasional (Level 2 – Level 5).</p>
        </div>

    </div>

    <div style="background:#eff6ff;color:#1e40af;padding:14px 16px;border-radius:8px;margin-bottom:20px;font-size:14px;">
        <strong>Alur Peningkatan Skill:</strong><br>
        Crew → <em>Pemindahan Jabatan</em> → Operator Loader <strong>Level 1</strong>
        → <em>Lulus Assessment</em> → Operator Loader <strong>Level 2</strong>
        → <em>Lulus Assessment</em> → ... → <strong>Level 5</strong> (Maks)
        <br><small>Setiap kenaikan level harus lulus assessment jabatan tujuan terlebih dahulu.</small>
    </div>

    <form method="GET" style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Cari nama jabatan..."
            style="padding:10px;border:1px solid #d1d5db;border-radius:6px;min-width:280px;"
        >
        <button type="submit" class="btn" style="background:#2563eb;color:white;">Cari</button>
    </form>

    <table style="width:100%;border-collapse:collapse;">

        <thead>
            <tr>
                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">No</th>
                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">Nama Jabatan</th>
                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">Level Skill</th>
                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">Standar Nilai</th>
                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">Karyawan</th>
                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">Aksi</th>
            </tr>
        </thead>

        <tbody>

        @forelse($jabatans as $jabatan)

            <tr>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                    {{ $jabatans->firstItem() + $loop->index }}
                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                    <strong>{{ $jabatan->nama_jabatan }}</strong>
                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                    <span style="background:#dcfce7;color:#166534;padding:3px 10px;border-radius:12px;font-size:13px;font-weight:bold;">
                        Level {{ $jabatan->level_jabatan }}
                    </span>
                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                    {{ number_format($jabatan->standar_nilai, 2) }}
                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                    {{ $jabatan->karyawans_count }} orang
                </td>

                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                    <a
                        href="{{ route('hrd.jabatan.show', $jabatan) }}"
                        class="btn"
                        style="background:#111827;color:white;"
                    >
                        Detail Jabatan
                    </a>
                </td>

            </tr>

        @empty

            <tr>
                <td colspan="6" style="padding:25px;text-align:center;color:#6b7280;">
                    Belum ada jabatan operasional level 2–5.
                    Tambahkan melalui <a href="{{ route('hrd.jabatan.create') }}">Manajemen Jabatan</a>.
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
