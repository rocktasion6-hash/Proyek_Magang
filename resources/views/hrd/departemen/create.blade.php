@extends('layouts.hrd')

@section('title', 'Tambah Departemen')

@section('page_title', 'Tambah Departemen')

@section('content')

<div class="card" style="max-width:800px;">

    <h1>
        Tambah Departemen
    </h1>

    <p>
        Tambahkan departemen baru ke sistem.
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


    <form
        method="POST"
        action="{{ route('hrd.departemen.store') }}"
    >

        @csrf

        <div
            style="margin-bottom:20px;"
        >

            <label
                style="
                    display:block;
                    margin-bottom:7px;
                    font-weight:bold;
                "
            >
                Nama Departemen
            </label>

            <input
                type="text"
                name="nama_departemen"
                value="{{ old('nama_departemen') }}"
                required
                placeholder="Contoh: Produksi"
                style="
                    width:100%;
                    padding:11px;
                    border:1px solid #d1d5db;
                    border-radius:6px;
                "
            >

        </div>


        <div
            style="margin-bottom:20px;"
        >

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
                placeholder="Deskripsi departemen..."
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
            Simpan Departemen
        </button>

        <a
            href="{{ route('hrd.departemen.index') }}"
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