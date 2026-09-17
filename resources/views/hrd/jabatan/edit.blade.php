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

        Perubahan level jabatan dapat memengaruhi
        proses kenaikan jabatan yang menggunakan
        level sebagai dasar perbandingan.

    </div>


    <form
        method="POST"
        action="{{ route(
            'hrd.jabatan.update',
            $jabatan
        ) }}"
    >

        @csrf

        @method('PUT')


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
                value="{{ old(
                    'nama_jabatan',
                    $jabatan->nama_jabatan
                ) }}"
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
                Level Jabatan
            </label>

            <input
                type="number"
                name="level_jabatan"
                value="{{ old(
                    'level_jabatan',
                    $jabatan->level_jabatan
                ) }}"
                min="1"
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
            href="{{ route(
                'hrd.jabatan.show',
                $jabatan
            ) }}"
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