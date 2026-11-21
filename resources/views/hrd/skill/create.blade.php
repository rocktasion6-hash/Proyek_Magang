@extends('layouts.hrd')

@section('title', 'Tambah Skill')
@section('page_title', 'Tambah Skill')

@push('styles')
<style>
    #tsSkillPage.ts-page {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        box-sizing: border-box;
    }

    #tsSkillPage .ts-card {
        width: 100% !important;
        max-width: none !important;
        background: #ffffff;
        border: 1px solid #d8e0da;
        border-radius: 10px;
        padding: 32px 40px;
        box-sizing: border-box;
    }

    #tsSkillPage .ts-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        padding-bottom: 18px;
        margin-bottom: 24px;
        border-bottom: 1px solid #e5e7eb;
    }

    #tsSkillPage .ts-header h1 {
        margin: 0 0 6px;
        font-size: 24px;
        font-weight: 700;
        color: #2c4a3e;
        line-height: 1.25;
    }

    #tsSkillPage .ts-header p {
        margin: 0;
        font-size: 14.5px;
        color: #4b5563;
        line-height: 1.45;
    }

    #tsSkillPage .ts-btn-back {
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

    #tsSkillPage .ts-btn-back:hover,
    #tsSkillPage .ts-btn-back:active {
        background: #2c4a3e;
        color: #fdfdfc;
        border-color: #2c4a3e;
    }

    #tsSkillPage .ts-err {
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

    #tsSkillPage .ts-err ul {
        margin: 8px 0 0 20px;
        padding: 0;
    }

    #tsSkillPage .ts-group {
        margin-bottom: 22px;
    }

    #tsSkillPage .ts-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 14.5px;
        font-weight: 700;
        color: #111827;
    }

    #tsSkillPage .ts-group input[type="text"],
    #tsSkillPage .ts-group textarea {
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

    #tsSkillPage .ts-group input[type="text"]:focus,
    #tsSkillPage .ts-group textarea:focus {
        outline: none;
        border-color: #2c4a3e;
    }

    #tsSkillPage .ts-group textarea {
        min-height: 130px;
        resize: vertical;
    }

    #tsSkillPage .ts-error {
        display: block;
        margin-top: 7px;
        font-size: 13px;
        font-weight: 600;
        color: #991b1b;
    }

    #tsSkillPage .ts-actions {
        margin-top: 28px;
        padding-top: 22px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    #tsSkillPage .ts-btn-save {
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

    #tsSkillPage .ts-btn-save:hover,
    #tsSkillPage .ts-btn-save:active {
        background: #4a7c59;
        border-color: #4a7c59;
    }

    #tsSkillPage .ts-btn-cancel {
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

    #tsSkillPage .ts-btn-cancel:hover,
    #tsSkillPage .ts-btn-cancel:active {
        background: #f4f6f4;
        border-color: #2c4a3e;
    }

    @media (max-width: 768px) {
        #tsSkillPage .ts-card {
            padding: 20px;
        }
    }
</style>
@endpush

@section('content')
<div id="tsSkillPage" class="ts-page">
    <div class="ts-card">

        <div class="ts-header">
            <div>
                <h1>Tambah Skill</h1>
                <p>Tambahkan kompetensi atau skill baru.</p>
            </div>

            <a href="{{ route('hrd.skill.index') }}" class="ts-btn-back">
                Kembali
            </a>
        </div>

        @if($errors->any())
            <div class="ts-err">
                <strong>Terjadi kesalahan:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('hrd.skill.store') }}">
            @csrf

            <div class="ts-group">
                <label for="nama_skill">Nama Skill</label>
                <input
                    type="text"
                    id="nama_skill"
                    name="nama_skill"
                    value="{{ old('nama_skill') }}"
                    required
                    placeholder="Contoh: Problem Solving"
                    autocomplete="off"
                >
                @error('nama_skill')
                    <span class="ts-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="ts-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea
                    id="deskripsi"
                    name="deskripsi"
                    rows="5"
                    placeholder="Jelaskan skill..."
                >{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <span class="ts-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="ts-actions">
                <button type="submit" class="ts-btn-save">Simpan Skill</button>
                <a href="{{ route('hrd.skill.index') }}" class="ts-btn-cancel">Batal</a>
            </div>
        </form>

    </div>
</div>
@endsection