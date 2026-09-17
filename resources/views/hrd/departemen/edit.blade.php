@extends('layouts.hrd')

@section('title', 'Edit Departemen')

@section('page_title', 'Edit Departemen')

@section('content')

<div class="card" style="max-width:800px;">

    <h1>
        Edit Departemen
    </h1>

    <p>
        Perbarui informasi departemen.
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
        action="{{ route(
            'hrd.departemen.update',
            $departemen
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
                Nama Departemen
            </label>

            <input
                type="text"
                name="nama_departemen"
                value="{{ old(
                    'nama_departemen',
                    $departemen->nama_departemen
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
                $departemen->deskripsi
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
                'hrd.departemen.show',
                $departemen
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