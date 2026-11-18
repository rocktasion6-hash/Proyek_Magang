@extends('layouts.hrd')
@section('title', 'Tambah Soal')
@section('page_title', 'Tambah Soal')
@section('content')

<div class="card" style="max-width:800px;">

    <h1 style="margin:0 0 20px;">Tambah Soal Manual</h1>

    @if($errors->any())
        <div style="background:#fee2e2;color:#991b1b;padding:12px 15px;border-radius:6px;margin-bottom:20px;">
            <ul style="margin:0;padding-left:18px;">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('hrd.soal.store') }}">
        @csrf

        <div style="margin-bottom:18px;">
            <label style="display:block;font-weight:600;margin-bottom:6px;">Jabatan</label>
            <select name="jabatan_id" required style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px;">
                <option value="">-- Pilih Jabatan --</option>
                @foreach($jabatans as $j)
                    <option value="{{ $j->id }}" {{ old('jabatan_id') == $j->id ? 'selected' : '' }}>
                        {{ $j->nama_jabatan }}{{ $j->tipe_jabatan === 'struktural' ? ' (Jabatan Struktural)' : ' (Level '.$j->level_jabatan.')' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom:18px;">
            <label style="display:block;font-weight:600;margin-bottom:6px;">Nama Prosedur</label>
            <input type="text" name="nama_prosedur" value="{{ old('nama_prosedur') }}"
                   placeholder="Contoh: Prosedur Pengoperasian Excavator"
                   style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px;">
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-bottom:18px;">
            <div>
                <label style="display:block;font-weight:600;margin-bottom:6px;">Tipe Soal</label>
                <select name="tipe_soal" id="tipe_soal" required onchange="buatPilihan()"
                        style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px;">
                    <option value="">-- Pilih Tipe --</option>
                    <option value="pilihan_ganda" {{ old('tipe_soal') === 'pilihan_ganda' ? 'selected' : '' }}>Pilihan Ganda</option>
                    <option value="multi_jawaban" {{ old('tipe_soal') === 'multi_jawaban' ? 'selected' : '' }}>Multi Jawaban</option>
                    <option value="benar_salah" {{ old('tipe_soal') === 'benar_salah' ? 'selected' : '' }}>Benar / Salah</option>
                </select>
            </div>
            <div>
                <label style="display:block;font-weight:600;margin-bottom:6px;">Nilai Point</label>
                <input type="number" name="nilai_point" value="{{ old('nilai_point') }}" step="0.01" min="0"
                       placeholder="Contoh: 1"
                       style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px;">
            </div>
        </div>

        <div style="margin-bottom:18px;">
            <label style="display:block;font-weight:600;margin-bottom:6px;">Pertanyaan</label>
            <textarea name="pertanyaan" rows="3" required
                      style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px;">{{ old('pertanyaan') }}</textarea>
        </div>

        <div id="pilihanContainer" style="margin-bottom:18px;"></div>

        <div style="display:flex;gap:10px;">
            <button type="submit" class="btn" style="background:#2563eb;color:white;">Simpan Soal</button>
            <a href="{{ route('hrd.soal.index') }}" class="btn" style="background:#6b7280;color:white;">Batal</a>
        </div>

    </form>
</div>

<script>
function buatPilihan() {
    const tipe = document.getElementById('tipe_soal').value;
    const container = document.getElementById('pilihanContainer');
    container.innerHTML = '';
    if (!tipe) return;

    if (tipe === 'benar_salah') {
        container.innerHTML = `
            <label style="display:block;font-weight:600;margin-bottom:8px;">Jawaban Benar</label>
            <div style="display:flex;gap:15px;">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="radio" name="jawaban_benar[]" value="1" required>
                    <input type="hidden" name="pilihan[0][teks_jawaban]" value="Benar">
                    <input type="hidden" name="pilihan[1][teks_jawaban]" value="Salah">
                    Benar
                </label>
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="radio" name="jawaban_benar[]" value="2">
                    Salah
                </label>
            </div>`;
        return;
    }

    const inputType = tipe === 'pilihan_ganda' ? 'radio' : 'checkbox';
    const kodes = ['A','B','C','D','E'];
    let html = `<label style="display:block;font-weight:600;margin-bottom:8px;">Pilihan Jawaban</label>`;
    kodes.forEach((k, i) => {
        html += `
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
            <input type="${inputType}" name="jawaban_benar[]" value="${i+1}" style="flex-shrink:0;">
            <span style="font-weight:700;width:20px;">${k}.</span>
            <input type="text" name="pilihan[${i}][teks_jawaban]" ${i < 2 ? 'required' : ''}
                   placeholder="Pilihan ${k}${i >= 2 ? ' (opsional)' : ''}"
                   style="flex:1;padding:9px;border:1px solid #d1d5db;border-radius:6px;">
        </div>`;
    });
    container.innerHTML = html;
}
buatPilihan();
</script>

@endsection
