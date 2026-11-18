@extends('layouts.hrd')
@section('title', 'Detail Soal')
@section('page_title', 'Detail Soal')
@section('content')

<div class="card" style="max-width:800px;">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;">
        <h1 style="margin:0;">Soal #{{ $soal->id }}</h1>
        <div style="display:flex;gap:10px;">
            <a href="{{ route('hrd.soal.edit', $soal) }}" class="btn" style="background:#f59e0b;color:white;">Edit</a>
            <form method="POST" action="{{ route('hrd.soal.destroy', $soal) }}" onsubmit="return confirm('Hapus soal ini?')" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" class="btn" style="background:#dc2626;color:white;">Hapus</button>
            </form>
        </div>
    </div>

    <table style="width:100%;border-collapse:collapse;margin-bottom:25px;">
        <tr>
            <td style="padding:10px;color:#6b7280;width:180px;border-bottom:1px solid #f3f4f6;">Jabatan</td>
            <td style="padding:10px;border-bottom:1px solid #f3f4f6;font-weight:600;">
                {{ $soal->jabatan->nama_jabatan ?? '-' }}
                @if($soal->jabatan?->tipe_jabatan === 'operasional')
                    (Level {{ $soal->jabatan->level_jabatan }})
                @endif
            </td>
        </tr>
        <tr>
            <td style="padding:10px;color:#6b7280;border-bottom:1px solid #f3f4f6;">Prosedur</td>
            <td style="padding:10px;border-bottom:1px solid #f3f4f6;">{{ $soal->nama_prosedur ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:10px;color:#6b7280;border-bottom:1px solid #f3f4f6;">Nilai Point</td>
            <td style="padding:10px;border-bottom:1px solid #f3f4f6;">{{ $soal->nilai_point ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:10px;color:#6b7280;border-bottom:1px solid #f3f4f6;">Tipe Soal</td>
            <td style="padding:10px;border-bottom:1px solid #f3f4f6;">
                <span style="background:#eff6ff;color:#1e40af;padding:2px 10px;border-radius:4px;font-size:13px;">
                    {{ ['pilihan_ganda'=>'Pilihan Ganda','multi_jawaban'=>'Multi Jawaban','benar_salah'=>'Benar/Salah'][$soal->tipe_soal] ?? $soal->tipe_soal }}
                </span>
            </td>
        </tr>
        <tr>
            <td style="padding:10px;color:#6b7280;border-bottom:1px solid #f3f4f6;">Pertanyaan</td>
            <td style="padding:10px;border-bottom:1px solid #f3f4f6;">{{ $soal->pertanyaan }}</td>
        </tr>
    </table>

    <h2 style="font-size:15px;margin-bottom:12px;">Pilihan Jawaban</h2>
    <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:25px;">
        @foreach($soal->pilihanJawabans as $pilihan)
        <div style="padding:12px 15px;border-radius:6px;border:1px solid {{ $pilihan->is_benar ? '#86efac' : '#e5e7eb' }};background:{{ $pilihan->is_benar ? '#f0fdf4' : '#fff' }};display:flex;justify-content:space-between;align-items:center;">
            <span>
                <strong>{{ $pilihan->kode }}.</strong> {{ $pilihan->teks_jawaban }}
            </span>
            @if($pilihan->is_benar)
                <span style="background:#dcfce7;color:#15803d;padding:2px 10px;border-radius:4px;font-size:12px;font-weight:600;">✓ Benar</span>
            @endif
        </div>
        @endforeach
    </div>

    <a href="{{ route('hrd.soal.index') }}" class="btn" style="background:#6b7280;color:white;">← Kembali</a>

</div>

@endsection
