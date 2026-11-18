@extends('layouts.hrd')
@section('title', 'Edit Jabatan')
@section('page_title', 'Edit Jabatan')
@section('content')

<div class="card" style="max-width:800px;">

    <h1>
        Edit Jabatan
    </h1>

    <p>
        Perbarui informasi jabatan.
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
            background:#fef3c7;
            color:#92400e;
            padding:15px;
            border-radius:8px;
            margin-bottom:20px;
        "
    >
        <strong>Perhatian:</strong>
        Perubahan tipe atau urutan struktural dapat memengaruhi alur kenaikan jabatan.
    </div>


    <form
        method="POST"
        action="{{ route('hrd.jabatan.update', $jabatan) }}"
    >

        @csrf
        @method('PUT')


        <div style="margin-bottom:20px;">
            <label style="display:block;margin-bottom:7px;font-weight:bold;">Nama Jabatan</label>
            <input
                type="text"
                name="nama_jabatan"
                value="{{ old('nama_jabatan', $jabatan->nama_jabatan) }}"
                required
                style="width:100%;padding:11px;border:1px solid #d1d5db;border-radius:6px;"
            >
        </div>


        <div style="margin-bottom:20px;">
            <label style="display:block;margin-bottom:7px;font-weight:bold;">Tipe Jabatan</label>
            <select
                name="tipe_jabatan"
                id="tipe_jabatan"
                required
                onchange="toggleUrutan()"
                style="width:100%;padding:11px;border:1px solid #d1d5db;border-radius:6px;"
            >
                <option value="operasional" {{ old('tipe_jabatan', $jabatan->tipe_jabatan) === 'operasional' ? 'selected' : '' }}>
                    Operasional (Crew, Operator, Teknisi, dll)
                </option>
                <option value="struktural" {{ old('tipe_jabatan', $jabatan->tipe_jabatan) === 'struktural' ? 'selected' : '' }}>
                    Struktural (Wakil Foreman, Foreman, Supervisor, dll)
                </option>
            </select>
        </div>


        <div
            id="wrap_urutan"
            style="margin-bottom:20px; {{ old('tipe_jabatan', $jabatan->tipe_jabatan) === 'struktural' ? '' : 'display:none;' }}"
        >
            <label style="display:block;margin-bottom:7px;font-weight:bold;">Urutan Struktural</label>
            <select
                name="urutan_struktural"
                style="width:100%;padding:11px;border:1px solid #d1d5db;border-radius:6px;"
            >
                <option value="">-- Pilih Urutan --</option>
                <option value="1" {{ old('urutan_struktural', $jabatan->urutan_struktural) == 1 ? 'selected' : '' }}>1 — Wakil Foreman</option>
                <option value="2" {{ old('urutan_struktural', $jabatan->urutan_struktural) == 2 ? 'selected' : '' }}>2 — Foreman</option>
                <option value="3" {{ old('urutan_struktural', $jabatan->urutan_struktural) == 3 ? 'selected' : '' }}>3 — Wakil Supervisor</option>
                <option value="4" {{ old('urutan_struktural', $jabatan->urutan_struktural) == 4 ? 'selected' : '' }}>4 — Supervisor</option>
                <option value="5" {{ old('urutan_struktural', $jabatan->urutan_struktural) == 5 ? 'selected' : '' }}>5 — Wakil General Manager</option>
                <option value="6" {{ old('urutan_struktural', $jabatan->urutan_struktural) == 6 ? 'selected' : '' }}>6 — Manager</option>
            </select>
            <small style="color:#6b7280;">Urutan menentukan hierarki kenaikan jabatan struktural.</small>
        </div>


        <div
            id="wrap_level"
            style="margin-bottom:20px; {{ old('tipe_jabatan', $jabatan->tipe_jabatan) === 'struktural' ? 'display:none;' : '' }}"
        >
            <label style="display:block;margin-bottom:7px;font-weight:bold;">Level Jabatan</label>
            <input
                type="number"
                name="level_jabatan"
                value="{{ old('level_jabatan', $jabatan->level_jabatan) }}"
                min="0"
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
                    $jabatan->standar_nilai
                ) }}"
                min="0"
                max="100"
                step="0.01"
                required
                style="
                    width:100%;
                    padding:11px;
                    border:1px solid #d1d5db;
                    border-radius:6px;
                "
            >

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
                style="
                    width:100%;
                    padding:11px;
                    border:1px solid #d1d5db;
                    border-radius:6px;
                    resize:vertical;
                "
            >{{ old(
                'deskripsi',
                $jabatan->deskripsi
            ) }}</textarea>

        </div>


        <button
            type="submit"
            class="btn"
            style="
                background:#2563eb;
                color:white;
            "
        >
            Simpan Perubahan
        </button>

        <a
            href="{{ route('hrd.jabatan.show', $jabatan) }}"
            class="btn"
            style="background:#6b7280;color:white;"
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