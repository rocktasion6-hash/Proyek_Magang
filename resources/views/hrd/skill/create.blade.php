@extends('layouts.hrd')

@section('title', 'Tambah Skill')

@section('page_title', 'Tambah Skill')

@section('content')

<div class="card" style="max-width:800px;">

    <h1>
        Tambah Skill
    </h1>

    <p>
        Tambahkan kompetensi atau skill baru.
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
        action="{{ route('hrd.skill.store') }}"
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
                Nama Skill
            </label>

            <input
                type="text"
                name="nama_skill"
                value="{{ old('nama_skill') }}"
                required
                placeholder="Contoh: Problem Solving"
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
                placeholder="Jelaskan skill..."
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
            Simpan Skill
        </button>

        <a
            href="{{ route('hrd.skill.index') }}"
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