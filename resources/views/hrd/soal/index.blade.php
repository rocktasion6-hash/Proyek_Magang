@extends('layouts.hrd')
@section('title', 'Bank Soal')
@section('page_title', 'Bank Soal')
@section('content')

<div class="card">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;">
        <div>
            <h1 style="margin:0;">Bank Soal</h1>
            <p style="margin:4px 0 0;color:#6b7280;">Daftar bank soal yang telah diupload per departemen dan jabatan.</p>
        </div>
        <a href="{{ route('hrd.soal.upload') }}" class="btn" style="background:#16a34a;color:white;">↑ Upload Excel</a>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;color:#15803d;padding:12px 15px;border-radius:6px;margin-bottom:20px;">
            {{ session('success') }}
        </div>
    @endif

    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr style="background:#f9fafb;">
                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">No</th>
                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">Nama File</th>
                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">Departemen</th>
                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">Jabatan</th>
                <th style="padding:12px;text-align:center;border-bottom:1px solid #e5e7eb;">Jumlah Soal</th>
                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">Tanggal Upload</th>
                <th style="padding:12px;text-align:left;border-bottom:1px solid #e5e7eb;">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($uploads as $upload)
            <tr>
                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">{{ $uploads->firstItem() + $loop->index }}</td>
                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                    <span style="font-weight:600;">{{ $upload->nama_file_tampilan }}</span>
                </td>
                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                    {{ $upload->departemen->nama_departemen ?? 'Belum ditentukan' }}
                </td>
                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                    {{ $upload->jabatan->nama_jabatan ?? '-' }}
                    @if($upload->jabatan?->tipe_jabatan === 'operasional')
                        <span style="color:#6b7280;font-size:12px;">(Level {{ $upload->jabatan->level_jabatan }})</span>
                    @endif
                </td>
                <td style="padding:12px;border-bottom:1px solid #e5e7eb;text-align:center;">
                    <span style="background:#eff6ff;color:#1e40af;padding:2px 10px;border-radius:4px;font-weight:600;">
                        {{ $upload->soals_count }}
                    </span>
                </td>
                <td style="padding:12px;border-bottom:1px solid #e5e7eb;color:#6b7280;font-size:13px;">
                    {{ $upload->created_at->format('d/m/Y H:i') }}
                </td>
                <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                    <div style="display:flex;gap:8px;">
                        <a href="{{ route('hrd.soal.upload.show', $upload) }}" class="btn" style="background:#111827;color:white;">Detail</a>
                        <form method="POST" action="{{ route('hrd.soal.upload.destroy', $upload) }}"
                              onsubmit="return confirm('Hapus file ini beserta {{ $upload->soals_count }} soal di dalamnya?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn" style="background:#dc2626;color:white;">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" style="padding:30px;text-align:center;color:#6b7280;">
                    Belum ada soal yang diupload. <a href="{{ route('hrd.soal.upload') }}">Upload sekarang</a>.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top:20px;">{{ $uploads->links() }}</div>

</div>

@endsection
