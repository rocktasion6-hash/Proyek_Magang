@extends('layouts.hrd')
@section('title', 'Detail Bank Soal')
@section('page_title', 'Detail Bank Soal')
@section('content')

<div class="card">

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:25px;flex-wrap:wrap;gap:15px;">
        <div>
            <h1 style="margin:0;">{{ $soalUpload->nama_file_tampilan }}</h1>
            <p style="margin:5px 0 0;color:#6b7280;">
                {{ $soalUpload->jabatan->nama_jabatan ?? '-' }}
                @if($soalUpload->jabatan?->tipe_jabatan === 'operasional')
                    (Level {{ $soalUpload->jabatan->level_jabatan }})
                @endif
                &nbsp;·&nbsp;
                {{ $soalUpload->departemen->nama_departemen ?? 'Departemen belum ditentukan' }} &nbsp;·&nbsp;
                {{ $soalUpload->soals->count() }} soal &nbsp;·&nbsp;
                Diupload {{ $soalUpload->created_at->format('d/m/Y H:i') }}
            </p>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="{{ route('hrd.soal.index') }}" class="btn" style="background:#6b7280;color:white;">← Kembali</a>
            <form method="POST" action="{{ route('hrd.soal.upload.destroy', $soalUpload) }}"
                  onsubmit="return confirm('Hapus file ini beserta semua soalnya?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn" style="background:#dc2626;color:white;">Hapus File</button>
            </form>
        </div>
    </div>

    {{-- Daftar Soal --}}
    @foreach($soalUpload->soals as $i => $soal)
    <div style="border:1px solid #e5e7eb;border-radius:8px;margin-bottom:16px;overflow:hidden;">

        {{-- Header soal --}}
        <div style="background:#f9fafb;padding:12px 16px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="background:#1e40af;color:white;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0;">
                    {{ $i + 1 }}
                </span>
                <div>
                    <span style="background:#eff6ff;color:#1e40af;padding:2px 8px;border-radius:4px;font-size:12px;">
                        {{ $labelTipe[$soal->tipe_soal] ?? $soal->tipe_soal }}
                    </span>
                    @if($soal->nama_prosedur)
                        <span style="color:#6b7280;font-size:12px;margin-left:8px;">{{ $soal->nama_prosedur }}</span>
                    @endif
                    @if($soal->nilai_point)
                        <span style="color:#6b7280;font-size:12px;margin-left:8px;">· {{ $soal->nilai_point }} poin</span>
                    @endif
                </div>
            </div>
            <div style="display:flex;gap:8px;">
                <a href="{{ route('hrd.soal.edit', $soal) }}" class="btn" style="background:#f59e0b;color:white;padding:5px 12px;font-size:13px;">Edit</a>
                <form method="POST" action="{{ route('hrd.soal.destroy', $soal) }}"
                      onsubmit="return confirm('Hapus soal ini?')" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn" style="background:#dc2626;color:white;padding:5px 12px;font-size:13px;">Hapus</button>
                </form>
            </div>
        </div>

        {{-- Pertanyaan --}}
        <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;">
            <p style="margin:0;font-weight:600;">{{ $soal->pertanyaan }}</p>
        </div>

        {{-- Pilihan jawaban --}}
        <div style="padding:12px 16px;display:flex;flex-direction:column;gap:6px;">
            @foreach($soal->pilihanJawabans as $pilihan)
            <div style="padding:8px 12px;border-radius:5px;border:1px solid {{ $pilihan->is_benar ? '#86efac' : '#e5e7eb' }};background:{{ $pilihan->is_benar ? '#f0fdf4' : '#fff' }};display:flex;justify-content:space-between;align-items:center;">
                <span>
                    <strong>{{ $pilihan->kode }}.</strong> {{ $pilihan->teks_jawaban }}
                </span>
                @if($pilihan->is_benar)
                    <span style="background:#dcfce7;color:#15803d;padding:1px 8px;border-radius:4px;font-size:12px;font-weight:600;">✓ Benar</span>
                @endif
            </div>
            @endforeach
        </div>

    </div>
    @endforeach

    @if($soalUpload->soals->isEmpty())
        <p style="text-align:center;color:#6b7280;padding:30px;">Tidak ada soal dalam file ini.</p>
    @endif

</div>

@endsection
