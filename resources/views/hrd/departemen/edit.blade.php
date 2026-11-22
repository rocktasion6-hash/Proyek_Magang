@extends('layouts.hrd')

@section('title', 'Edit Departemen')
@section('page_title', 'Edit Departemen')

@push('styles')
<style>
    #edDepartemenPage.ed-page {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        box-sizing: border-box;
    }

    #edDepartemenPage .ed-card {
        width: 100% !important;
        max-width: none !important;
        background: #ffffff;
        border: 1px solid #d8e0da;
        border-radius: 10px;
        padding: 32px 40px;
        box-sizing: border-box;
    }

    #edDepartemenPage .ed-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        padding-bottom: 18px;
        margin-bottom: 24px;
        border-bottom: 1px solid #e5e7eb;
    }

    #edDepartemenPage .ed-header h1 {
        margin: 0 0 6px;
        font-size: 24px;
        font-weight: 700;
        color:#2c4a3e;
        line-height: 1.25;
    }

    #edDepartemenPage .ed-header p {
        margin: 0;
        font-size: 14.5px;
        color: #4b5563;
        line-height: 1.45;
    }

    #edDepartemenPage .ed-btn-back {
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
    }

    #edDepartemenPage .ed-btn-back:hover,
    #edDepartemenPage .ed-btn-back:active {
        background: #3b6649;
        color: #fdfdfc;
        border-color: #3b6649;
    }

    #edDepartemenPage .ed-err {
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

    #edDepartemenPage .ed-err ul {
        margin: 8px 0 0 20px;
        padding: 0;
    }

    #edDepartemenPage .ed-group {
        margin-bottom: 22px;
    }

    #edDepartemenPage .ed-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 14.5px;
        font-weight: 700;
        color: #111827;
    }

    #edDepartemenPage .ed-group input[type="text"],
    #edDepartemenPage .ed-group textarea {
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

    #edDepartemenPage .ed-group input[type="text"]:focus,
    #edDepartemenPage .ed-group textarea:focus {
        outline: none;
        border-color: #4a7c59;
    }

    #edDepartemenPage .ed-group textarea {
        min-height: 130px;
        resize: vertical;
    }

    #edDepartemenPage .ed-error {
        display: block;
        margin-top: 7px;
        font-size: 13px;
        font-weight: 600;
        color: #991b1b;
    }

    #edDepartemenPage .ed-actions {
        margin-top: 28px;
        padding-top: 22px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    #edDepartemenPage .ed-btn-save {
        padding: 12px 24px;
        border-radius: 8px;
        border: none;
        background: #4a7c59;
        color: #fdfdfc;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
    }

    #edDepartemenPage .ed-btn-save:hover,
    #edDepartemenPage .ed-btn-save:active {
        background: #3b6649;
    }

    #edDepartemenPage .ed-btn-cancel {
        padding: 11px 22px;
        border-radius: 8px;
        border: 1.5px solid #cbd5e1;
        background: #ffffff;
        color: #1f2937;
        font-size: 15px;
        font-weight: 600;
        text-decoration: none;
    }

    #edDepartemenPage .ed-btn-cancel:hover,
    #edDepartemenPage .ed-btn-cancel:active {
        background: #f4f6f4;
        border-color:#3b6649;
    }

    @media (max-width: 768px) {
        #edDepartemenPage .ed-card {
            padding: 20px;
        }
    }
</style>
@endpush

@section('content')
<div id="edDepartemenPage" class="ed-page">
    <div class="ed-card">

        <div class="ed-header">
            <div>
                <h1>Edit Departemen</h1>
                <p>Perbarui informasi departemen.</p>
            </div>

            <a href="{{ route('hrd.departemen.show', $departemen) }}" class="ed-btn-back">
                Kembali
            </a>
        </div>

        @if($errors->any())
            <div class="ed-err">
                <strong>Terjadi kesalahan:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('hrd.departemen.update', $departemen) }}">
            @csrf
            @method('PUT')

            <div class="ed-group">
                <label for="nama_departemen">Nama Departemen</label>
                <input
                    type="text"
                    id="nama_departemen"
                    name="nama_departemen"
                    value="{{ old('nama_departemen', $departemen->nama_departemen) }}"
                    required
                >
                @error('nama_departemen')
                    <span class="ed-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="ed-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea
                    id="deskripsi"
                    name="deskripsi"
                    rows="5"
                >{{ old('deskripsi', $departemen->deskripsi) }}</textarea>
                @error('deskripsi')
                    <span class="ed-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="ed-actions">
                <button type="submit" class="ed-btn-save">Simpan Perubahan</button>
                <a href="{{ route('hrd.departemen.show', $departemen) }}" class="ed-btn-cancel">Batal</a>
            </div>
        </form>

    </div>
</div>
@endsection