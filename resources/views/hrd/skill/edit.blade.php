@extends('layouts.hrd')

@section('title', 'Edit Skill')

@section('page_title', 'Edit Skill')

@section('content')

<div class="card" style="max-width:800px;">

    <h1>
        Edit Skill
    </h1>

    <p>
        Perbarui informasi skill.
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
            'hrd.skill.update',
            $skill
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
                Nama Skill
            </label>

            <input
                type="text"
                name="nama_skill"
                value="{{ old(
                    'nama_skill',
                    $skill->nama_skill
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
                $skill->deskripsi
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
                'hrd.skill.show',
                $skill
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