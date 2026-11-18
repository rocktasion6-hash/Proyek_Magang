@extends('layouts.hrd')
@section('title', 'Tambah Jabatan')
@section('page_title', 'Tambah Jabatan')
@section('content')

<div class="card" style="max-width:800px;">

    <h1>
        Tambah Jabatan
    </h1>

    <p>
        Tambahkan jabatan baru ke sistem.
    </p>


    @if($errors->any())

        <div class="alert alert-error">

            <strong>
                Terjadi kesalahan:
            </strong>

            <ul>

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    <div
        style="
            background:#eff6ff;
            color:#1e40af;
            padding:15px;
            border-radius:8px;
            margin-bottom:20px;
        "
    >
        <strong>Panduan Tipe Jabatan:</strong><br>
        <strong>Operasional</strong> — Crew, Operator Loader Level 1, Operator Loader Level 2, dst.<br>
        <strong>Struktural</strong> — Wakil Foreman (urutan 1), Foreman (2), Wakil Supervisor (3), Supervisor (4), Wakil General Manager (5), Manager (6).
    </div>


    <form
        method="POST"
        action="{{ route('hrd.jabatan.store') }}"
    >

        @csrf


        <div style="margin-bottom:20px;">

            <label style="display:block;margin-bottom:7px;font-weight:bold;">
                Nama Jabatan
            </label>

            <input
                type="text"
                name="nama_jabatan"
                value="{{ old('nama_jabatan') }}"
                required
                placeholder="Contoh: Operator Loader Level 1"
                style="width:100%;padding:11px;border:1px solid #d1d5db;border-radius:6px;"
            >

        </div>


        <div style="margin-bottom:20px;">

            <label style="display:block;margin-bottom:7px;font-weight:bold;">
                Tipe Jabatan
            </label>

            <select
                name="tipe_jabatan"
                id="tipe_jabatan"
                required
                onchange="toggleUrutan()"
                style="width:100%;padding:11px;border:1px solid #d1d5db;border-radius:6px;"
            >
                <option value="operasional" {{ old('tipe_jabatan', 'operasional') === 'operasional' ? 'selected' : '' }}>
                    Operasional (Crew, Operator, Teknisi, dll)
                </option>
                <option value="struktural" {{ old('tipe_jabatan') === 'struktural' ? 'selected' : '' }}>
                    Struktural (Wakil Foreman, Foreman, Supervisor, dll)
                </option>
            </select>

        </div>


        <div id="wrap_urutan" style="margin-bottom:20px; {{ old('tipe_jabatan') === 'struktural' ? '' : 'display:none;' }}">

            <label style="display:block;margin-bottom:7px;font-weight:bold;">
                Urutan Struktural
            </label>

            <select
                name="urutan_struktural"
                style="width:100%;padding:11px;border:1px solid #d1d5db;border-radius:6px;"
            >
                <option value="">-- Pilih Urutan --</option>
                <option value="1" {{ old('urutan_struktural') == 1 ? 'selected' : '' }}>1 — Wakil Foreman</option>
                <option value="2" {{ old('urutan_struktural') == 2 ? 'selected' : '' }}>2 — Foreman</option>
                <option value="3" {{ old('urutan_struktural') == 3 ? 'selected' : '' }}>3 — Wakil Supervisor</option>
                <option value="4" {{ old('urutan_struktural') == 4 ? 'selected' : '' }}>4 — Supervisor</option>
                <option value="5" {{ old('urutan_struktural') == 5 ? 'selected' : '' }}>5 — Wakil General Manager</option>
                <option value="6" {{ old('urutan_struktural') == 6 ? 'selected' : '' }}>6 — Manager</option>
            </select>

            <small style="color:#6b7280;">Urutan menentukan hierarki kenaikan jabatan struktural.</small>

        </div>


        <div id="wrap_level" style="margin-bottom:20px; {{ old('tipe_jabatan') === 'struktural' ? 'display:none;' : '' }}">

            <label style="display:block;margin-bottom:7px;font-weight:bold;">
                Level Jabatan
            </label>

            <input
                type="number"
                name="level_jabatan"
                value="{{ old('level_jabatan', 1) }}"
                min="0"
                placeholder="Contoh: 1"
                style="width:100%;padding:11px;border:1px solid #d1d5db;border-radius:6px;"
            >

            <small style="color:#6b7280;">
                0 = Crew, 1 = Level 1, 2 = Level 2, dst.
            </small>

        </div>


        <div style="margin-bottom:20px;">

            <label
                style="
                    display:block;
                    margin-bottom:7px;
                    font-weight:bold;
                "
            >
                Standar Nilai Assessment
            </label>

            <input
                type="number"
                name="standar_nilai"
                value="{{ old(
                    'standar_nilai',
                    80
                ) }}"
                min="0"
                max="100"
                step="0.01"
                required
                placeholder="Contoh: 80"
                style="
                    width:100%;
                    padding:11px;
                    border:1px solid #d1d5db;
                    border-radius:6px;
                "
            >

            <small style="color:#6b7280;">
                Nilai minimal yang digunakan sebagai standar kelulusan.
            </small>

        </div>


        <div style="margin-bottom:20px;">

            <label
                style="
                    display:block;
                    margin-bottom:7px;
                    font-weight:bold;
                "
            >
                Deskripsi
            </label>

            <textarea
                name="deskripsi"
                rows="5"
                placeholder="Deskripsi jabatan..."
                style="
                    width:100%;
                    padding:11px;
                    border:1px solid #d1d5db;
                    border-radius:6px;
                    resize:vertical;
                "
            >{{ old('deskripsi') }}</textarea>

        </div>


        <button
            type="submit"
            class="btn"
            style="
                background:#2563eb;
                color:white;
            "
        >
            Simpan Jabatan
        </button>

        <a
            href="{{ route('hrd.jabatan.index') }}"
            class="btn"
            style="
                background:#6b7280;
                color:white;
            "
        >
            Batal
        </a>

    </form>

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