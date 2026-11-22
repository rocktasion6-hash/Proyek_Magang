
@extends('layouts.hrd')

@section('title', 'Tambah Jabatan')
@section('page_title', 'Tambah Jabatan')

@push('styles')
<style>
    #tjJabatanPage.tj-page {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        box-sizing: border-box;
    }

    #tjJabatanPage .tj-card {
        width: 100% !important;
        max-width: none !important;
        background: #ffffff;
        border: 1px solid #d8e0da;
        border-radius: 10px;
        padding: 32px 40px;
        box-sizing: border-box;
    }

    #tjJabatanPage .tj-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        padding-bottom: 18px;
        margin-bottom: 24px;
        border-bottom: 1px solid #e5e7eb;
    }

    #tjJabatanPage .tj-header h1 {
        margin: 0 0 6px;
        font-size: 24px;
        font-weight: 700;
        color: #2c4a3e;
        line-height: 1.25;
    }

    #tjJabatanPage .tj-header p {
        margin: 0;
        font-size: 14.5px;
        color: #4b5563;
        line-height: 1.45;
    }

    /* Tombol Kembali */
    #tjJabatanPage .tj-btn-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 18px;
        border-radius: 7px;
        background: #f4f6f4;
        color: #2c4a3e;
        border: 1px solid #c6d2c9;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
        box-sizing: border-box;
    }

    #tjJabatanPage .tj-btn-back:hover,
    #tjJabatanPage .tj-btn-back:active {
        background: #4a7c59;
        color: #fdfdfc;
        border-color: #4a7c59;
    }

    /* Pesan Error */
    #tjJabatanPage .tj-err {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
        border-left: 4px solid #991b1b;
        border-radius: 8px;
        padding: 14px 18px;
        margin-bottom: 22px;
        font-size: 14.5px;
        line-height: 1.55;
    }

    #tjJabatanPage .tj-err ul {
        margin: 8px 0 0 20px;
        padding: 0;
    }

    /* Panduan Tipe Jabatan */
    #tjJabatanPage .tj-guide {
        background: #f4f6f4;
        color: #1f2937;
        border: 1px solid #d8e0da;
        border-left: 4px solid #e3b535;
        border-radius: 8px;
        padding: 15px 18px;
        margin-bottom: 24px;
        font-size: 14px;
        line-height: 1.6;
    }

    #tjJabatanPage .tj-guide strong {
        color: #2c4a3e;
    }

    /* Grid Form */
    #tjJabatanPage .tj-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 22px 32px;
        margin-bottom: 22px;
    }

    #tjJabatanPage .tj-group {
        margin-bottom: 22px;
    }

    #tjJabatanPage .tj-grid-2 .tj-group {
        margin-bottom: 0;
    }

    #tjJabatanPage .tj-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 14.5px;
        font-weight: 700;
        color: #111827;
    }

    /* Input, Select dan Textarea */
    #tjJabatanPage .tj-group input[type="text"],
    #tjJabatanPage .tj-group input[type="number"],
    #tjJabatanPage .tj-group select,
    #tjJabatanPage .tj-group textarea {
        width: 100%;
        box-sizing: border-box;
        padding: 12px 15px;
        font-size: 15px;
        line-height: 1.45;
        color: #111827;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        font-family: inherit;
    }

    #tjJabatanPage .tj-group input[type="text"]:focus,
    #tjJabatanPage .tj-group input[type="number"]:focus,
    #tjJabatanPage .tj-group select:focus,
    #tjJabatanPage .tj-group textarea:focus {
        outline: none;
        border-color: #4a7c59;
    }

    #tjJabatanPage .tj-group textarea {
        min-height: 125px;
        resize: vertical;
    }

    #tjJabatanPage .tj-help {
        display: block;
        margin-top: 7px;
        font-size: 13px;
        color: #4b5563;
        line-height: 1.45;
    }

    #tjJabatanPage .tj-error {
        display: block;
        margin-top: 7px;
        font-size: 13px;
        font-weight: 600;
        color: #991b1b;
    }

    /* Bagian Tombol Aksi */
    #tjJabatanPage .tj-actions {
        margin-top: 28px;
        padding-top: 22px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    /* Tombol Simpan Jabatan */
    #tjJabatanPage .tj-btn-save {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 24px;
        border-radius: 8px;
        border: 1.5px solid #4a7c59;
        background: #4a7c59;
        color: #fdfdfc;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        box-sizing: border-box;
    }

    #tjJabatanPage .tj-btn-save:hover,
    #tjJabatanPage .tj-btn-save:active {
        background: #3b6649;
        border-color: #3b6649;
    }

    /* Tombol Batal */
    #tjJabatanPage .tj-btn-cancel {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 22px;
        border-radius: 8px;
        border: 1.5px solid #cbd5e1;
        background: #ffffff;
        color: #1f2937;
        font-size: 15px;
        font-weight: 600;
        text-decoration: none;
        box-sizing: border-box;
    }

    #tjJabatanPage .tj-btn-cancel:hover,
    #tjJabatanPage .tj-btn-cancel:active {
        background: #f4f6f4;
        border-color: #4a7c59;
    }

    /* Responsive */
    @media (max-width: 768px) {
        #tjJabatanPage .tj-grid-2 {
            grid-template-columns: 1fr;
        }

        #tjJabatanPage .tj-card {
            padding: 20px;
        }
    }
</style>
@endpush

@section('content')
<div id="tjJabatanPage" class="tj-page">
    <div class="tj-card">

        <div class="tj-header">
            <div>
                <h1>Tambah Jabatan</h1>
                <p>Tambahkan jabatan baru ke sistem.</p>
            </div>

            <a href="{{ route('hrd.jabatan.index') }}" class="tj-btn-back">
                Kembali
            </a>
        </div>

        {{-- Pesan Error --}}
        @if($errors->any())
            <div class="tj-err">
                <strong>Terjadi kesalahan:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Panduan Tipe Jabatan --}}
        <div class="tj-guide">
            <strong>Panduan Tipe Jabatan:</strong><br>
            <strong>Operasional</strong> —
            Crew, Operator Loader Level 1, Operator Loader Level 2, dst.<br>
            <strong>Struktural</strong> —
            Wakil Foreman (urutan 1), Foreman (2),
            Wakil Supervisor (3), Supervisor (4),
            Wakil General Manager (5), Manager (6).
        </div>

        <form method="POST" action="{{ route('hrd.jabatan.store') }}">
            @csrf

            {{-- Nama Jabatan dan Tipe Jabatan --}}
            <div class="tj-grid-2">

                <div class="tj-group">
                    <label for="nama_jabatan">Nama Jabatan</label>
                    <input
                        type="text"
                        id="nama_jabatan"
                        name="nama_jabatan"
                        value="{{ old('nama_jabatan') }}"
                        required
                        placeholder="Contoh: Operator Loader Level 1"
                        autocomplete="off"
                    >

                    @error('nama_jabatan')
                        <span class="tj-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="tj-group">
                    <label for="tipe_jabatan">Tipe Jabatan</label>
                    <select
                        name="tipe_jabatan"
                        id="tipe_jabatan"
                        required
                        onchange="toggleUrutan()"
                    >
                        <option
                            value="operasional"
                            {{ old('tipe_jabatan', 'operasional') === 'operasional' ? 'selected' : '' }}
                        >
                            Operasional (Crew, Operator, Teknisi, dll)
                        </option>

                        <option
                            value="struktural"
                            {{ old('tipe_jabatan') === 'struktural' ? 'selected' : '' }}
                        >
                            Struktural (Wakil Foreman, Foreman, Supervisor, dll)
                        </option>
                    </select>

                    @error('tipe_jabatan')
                        <span class="tj-error">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            {{-- Level, Urutan dan Standar Nilai --}}
            <div class="tj-grid-2">

                <div
                    id="wrap_urutan"
                    class="tj-group"
                    style="{{ old('tipe_jabatan') === 'struktural' ? '' : 'display:none;' }}"
                >
                    <label for="urutan_struktural">
                        Urutan Struktural
                    </label>

                    <select
                        id="urutan_struktural"
                        name="urutan_struktural"
                    >
                        <option value="">-- Pilih Urutan --</option>

                        <option value="1" {{ old('urutan_struktural') == 1 ? 'selected' : '' }}>
                            1 — Wakil Foreman
                        </option>

                        <option value="2" {{ old('urutan_struktural') == 2 ? 'selected' : '' }}>
                            2 — Foreman
                        </option>

                        <option value="3" {{ old('urutan_struktural') == 3 ? 'selected' : '' }}>
                            3 — Wakil Supervisor
                        </option>

                        <option value="4" {{ old('urutan_struktural') == 4 ? 'selected' : '' }}>
                            4 — Supervisor
                        </option>

                        <option value="5" {{ old('urutan_struktural') == 5 ? 'selected' : '' }}>
                            5 — Wakil General Manager
                        </option>

                        <option value="6" {{ old('urutan_struktural') == 6 ? 'selected' : '' }}>
                            6 — Manager
                        </option>
                    </select>

                    <small class="tj-help">
                        Urutan menentukan hierarki kenaikan jabatan struktural.
                    </small>

                    @error('urutan_struktural')
                        <span class="tj-error">{{ $message }}</span>
                    @enderror
                </div>

                <div
                    id="wrap_level"
                    class="tj-group"
                    style="{{ old('tipe_jabatan') === 'struktural' ? 'display:none;' : '' }}"
                >
                    <label for="level_jabatan">
                        Level Jabatan
                    </label>

                    <input
                        type="number"
                        id="level_jabatan"
                        name="level_jabatan"
                        value="{{ old('level_jabatan', 1) }}"
                        min="0"
                        placeholder="Contoh: 1"
                    >

                    <small class="tj-help">
                        0 = Crew, 1 = Level 1, 2 = Level 2, dst.
                    </small>

                    @error('level_jabatan')
                        <span class="tj-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="tj-group">
                    <label for="standar_nilai">
                        Standar Nilai Assessment
                    </label>

                    <input
                        type="number"
                        id="standar_nilai"
                        name="standar_nilai"
                        value="{{ old('standar_nilai', 80) }}"
                        min="0"
                        max="100"
                        step="0.01"
                        required
                        placeholder="Contoh: 80"
                    >

                    <small class="tj-help">
                        Nilai minimal yang digunakan sebagai standar kelulusan.
                    </small>

                    @error('standar_nilai')
                        <span class="tj-error">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            {{-- Deskripsi --}}
            <div class="tj-group">
                <label for="deskripsi">Deskripsi</label>

                <textarea
                    id="deskripsi"
                    name="deskripsi"
                    rows="5"
                    placeholder="Deskripsi jabatan..."
                >{{ old('deskripsi') }}</textarea>

                @error('deskripsi')
                    <span class="tj-error">{{ $message }}</span>
                @enderror
            </div>

            {{-- Tombol Aksi --}}
            <div class="tj-actions">
                <button type="submit" class="tj-btn-save">
                    Simpan Jabatan
                </button>

                <a
                    href="{{ route('hrd.jabatan.index') }}"
                    class="tj-btn-cancel"
                >
                    Batal
                </a>
            </div>

        </form>

    </div>
</div>

<script>
function toggleUrutan() {
    const tipe = document.getElementById('tipe_jabatan').value;

    document.getElementById('wrap_urutan').style.display =
        tipe === 'struktural' ? 'block' : 'none';

    document.getElementById('wrap_level').style.display =
        tipe === 'struktural' ? 'none' : 'block';
}
</script>
@endsection
