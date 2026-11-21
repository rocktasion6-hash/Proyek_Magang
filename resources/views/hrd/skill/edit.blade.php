@extends('layouts.hrd')

@section('title', 'Edit Skill')
@section('page_title', 'Edit Skill')

@push('styles')
<style>
    #esSkillPage.es-page {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        box-sizing: border-box;
    }

    #esSkillPage .es-card {
        width: 100% !important;
        max-width: none !important;
        background: #ffffff;
        border: 1px solid #d8e0da;
        border-radius: 10px;
        padding: 32px 40px;
        box-sizing: border-box;
    }

    #esSkillPage .es-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        padding-bottom: 18px;
        margin-bottom: 24px;
        border-bottom: 1px solid #e5e7eb;
    }

    #esSkillPage .es-header h1 {
        margin: 0 0 6px;
        font-size: 24px;
        font-weight: 700;
        color: #2c4a3e;
        line-height: 1.25;
    }

    #esSkillPage .es-header p {
        margin: 0;
        font-size: 14.5px;
        color: #4b5563;
        line-height: 1.45;
    }

    #esSkillPage .es-btn-back {
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

    #esSkillPage .es-btn-back:hover,
    #esSkillPage .es-btn-back:active {
        background: #2c4a3e;
        color: #fdfdfc;
        border-color: #2c4a3e;
    }

    #esSkillPage .es-err {
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

    #esSkillPage .es-err ul {
        margin: 8px 0 0 20px;
        padding: 0;
    }

    #esSkillPage .es-group {
        margin-bottom: 22px;
    }

    #esSkillPage .es-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 14.5px;
        font-weight: 700;
        color: #111827;
    }

    #esSkillPage .es-group input[type="text"],
    #esSkillPage .es-group textarea {
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

    #esSkillPage .es-group input[type="text"]:focus,
    #esSkillPage .es-group textarea:focus {
        outline: none;
        border-color: #2c4a3e;
    }

    #esSkillPage .es-group textarea {
        min-height: 130px;
        resize: vertical;
    }

    #esSkillPage .es-error {
        display: block;
        margin-top: 7px;
        font-size: 13px;
        font-weight: 600;
        color: #991b1b;
    }

    #esSkillPage .es-actions {
        margin-top: 28px;
        padding-top: 22px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    #esSkillPage .es-btn-save {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 24px;
        border-radius: 8px;
        border: 1.5px solid #2c4a3e;
        background: #2c4a3e;
        color: #fdfdfc;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        box-sizing: border-box;
    }

    #esSkillPage .es-btn-save:hover,
    #esSkillPage .es-btn-save:active {
        background: #4a7c59;
        border-color: #4a7c59;
    }

    #esSkillPage .es-btn-cancel {
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

    #esSkillPage .es-btn-cancel:hover,
    #esSkillPage .es-btn-cancel:active {
        background: #f4f6f4;
        border-color: #2c4a3e;
    }

    @media (max-width: 768px) {
        #esSkillPage .es-card {
            padding: 20px;
        }
    }
</style>
@endpush

@section('content')
<div id="esSkillPage" class="es-page">
    <div class="es-card">

        <div class="es-header">
            <div>
                <h1>Edit Skill</h1>
                <p>Perbarui informasi skill.</p>
            </div>

            <a href="{{ route('hrd.skill.show', $skill) }}" class="es-btn-back">
                Kembali
            </a>
        </div>

        @if($errors->any())
            <div class="es-err">
                <strong>Terjadi kesalahan:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('hrd.skill.update', $skill) }}">
            @csrf
            @method('PUT')

            <div class="es-group">
                <label for="nama_skill">Nama Skill</label>
                <input
                    type="text"
                    id="nama_skill"
                    name="nama_skill"
                    value="{{ old('nama_skill', $skill->nama_skill) }}"
                    required
                    autocomplete="off"
                >
                @error('nama_skill')
                    <span class="es-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="es-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea
                    id="deskripsi"
                    name="deskripsi"
                    rows="5"
                >{{ old('deskripsi', $skill->deskripsi) }}</textarea>
                @error('deskripsi')
                    <span class="es-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="es-actions">
                <button type="submit" class="es-btn-save">Simpan Perubahan</button>
                <a href="{{ route('hrd.skill.show', $skill) }}" class="es-btn-cancel">Batal</a>
            </div>
        </form>

    </div>
</div>
@endsection