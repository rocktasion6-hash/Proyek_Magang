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

        <strong>Catatan:</strong>

        Level jabatan digunakan sebagai dasar
        dalam membedakan tingkatan jabatan dan
        proses kenaikan jabatan.

    </div>


    <form
        method="POST"
        action="{{ route('hrd.jabatan.store') }}"
    >

        @csrf


        <div style="margin-bottom:20px;">

            <label
                style="
                    display:block;
                    margin-bottom:7px;
                    font-weight:bold;
                "
            >
                Nama Jabatan
            </label>

            <input
                type="text"
                name="nama_jabatan"
                value="{{ old('nama_jabatan') }}"
                required
                placeholder="Contoh: Operator Senior"
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
                Level Jabatan
            </label>

            <input
                type="number"
                name="level_jabatan"
                value="{{ old('level_jabatan', 1) }}"
                min="1"
                required
                placeholder="Contoh: 2"
                style="
                    width:100%;
                    padding:11px;
                    border:1px solid #d1d5db;
                    border-radius:6px;
                "
            >

            <small style="color:#6b7280;">
                Semakin besar angka, semakin tinggi level jabatan.
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

@endsection