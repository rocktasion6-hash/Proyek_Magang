@extends('layouts.hrd')

@section('title', 'Tambah Departemen')
@section('page_title', 'Tambah Departemen')

@push('styles')
<style>
    #tdDepartemenPage.td-page {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        box-sizing: border-box;
    }

    #tdDepartemenPage .td-card {
        width: 100% !important;
        max-width: none !important;
        background: #ffffff;
        border: 1px solid #d8e0da;
        border-radius: 10px;
        padding: 32px 40px;
        box-sizing: border-box;
    }

    #tdDepartemenPage .td-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        padding-bottom: 18px;
        margin-bottom: 24px;
        border-bottom: 1px solid #e5e7eb;
    }

    #tdDepartemenPage .td-header h1 {
        margin: 0 0 6px;
        font-size: 24px;
        font-weight: 700;
        color: #2c4a3e;
        line-height: 1.25;
    }

    #tdDepartemenPage .td-header p {
        margin: 0;
        font-size: 14.5px;
        color: #4b5563;
        line-height: 1.45;
    }

    #tdDepartemenPage .td-btn-back {
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

    #tdDepartemenPage .td-btn-back:hover,
    #tdDepartemenPage .td-btn-back:active {
        background: #2c4a3e;
        color: #fdfdfc;
        border-color: #2c4a3e;
    }

    #tdDepartemenPage .td-err {
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

    #tdDepartemenPage .td-err ul {
        margin: 8px 0 0 20px;
        padding: 0;
    }

    #tdDepartemenPage .td-group {
        margin-bottom: 22px;
    }

    #tdDepartemenPage .td-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 14.5px;
        font-weight: 700;
        color: #111827;
    }

    #tdDepartemenPage .td-group input[type="text"],
    #tdDepartemenPage .td-group textarea {
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

    #tdDepartemenPage .td-group input[type="text"]:focus,
    #tdDepartemenPage .td-group textarea:focus {
        outline: none;
        border-color: #2c4a3e;
    }

    #tdDepartemenPage .td-group textarea {
        min-height: 130px;
        resize: vertical;
    }

    #tdDepartemenPage .td-error {
        display: block;
        margin-top: 7px;
        font-size: 13px;
        font-weight: 600;
        color: #991b1b;
    }

    #tdDepartemenPage .td-actions {
        margin-top: 28px;
        padding-top: 22px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    #tdDepartemenPage .td-btn-save {
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

    #tdDepartemenPage .td-btn-save:hover,
    #tdDepartemenPage .td-btn-save:active {
        background: #4a7c59;
        border-color: #4a7c59;
    }

    #tdDepartemenPage .td-btn-cancel {
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

    #tdDepartemenPage .td-btn-cancel:hover,
    #tdDepartemenPage .td-btn-cancel:active {
        background: #f4f6f4;
        border-color: #2c4a3e;
    }

    @media (max-width: 768px) {
        #tdDepartemenPage .td-card {
            padding: 20px;
        }
    }
</style>
@endpush

@section('content')
<div id="tdDepartemenPage" class="td-page">
    <div class="td-card">

        <div class="td-header">
            <div>
                <h1>Tambah Departemen</h1>
                <p>Tambahkan departemen baru ke sistem.</p>
            </div>

            <a href="{{ route('hrd.departemen.index') }}" class="td-btn-back">
                Kembali
            </a>
        </div>

        @if($errors->any())
            <div class="td-err">
                <strong>Terjadi kesalahan:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('hrd.departemen.store') }}">
            @csrf

            <div class="td-group">
                <label for="nama_departemen">Nama Departemen</label>
                <input
                    type="text"
                    id="nama_departemen"
                    name="nama_departemen"
                    value="{{ old('nama_departemen') }}"
                    placeholder="Contoh: Produksi"
                    autocomplete="off"
                    required
                >
                @error('nama_departemen')
                    <span class="td-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="td-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea
                    id="deskripsi"
                    name="deskripsi"
                    rows="5"
                    placeholder="Deskripsi departemen..."
                >{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <span class="td-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="td-actions">
                <button type="submit" class="td-btn-save">Simpan Departemen</button>
                <a href="{{ route('hrd.departemen.index') }}" class="td-btn-cancel">Batal</a>
            </div>
        </form>

    </div>
</div>
@endsection