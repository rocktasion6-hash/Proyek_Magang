@extends('layouts.hrd')

@section('title', 'Edit Karyawan')
@section('page_title', 'Edit Karyawan')

@push('styles')
<style>
    .edit-page-wide {
        width: 100%;
        max-width: 100%;
    }

    .edit-card-main {
        background: #ffffff;
        border: 1px solid #d8e0da;
        border-radius: 10px;
        padding: 32px 44px;
    }

    .edit-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        padding-bottom: 18px;
        margin-bottom: 24px;
        border-bottom: 1px solid #e5e7eb;
    }

    .edit-header-row h1 {
        margin: 0 0 6px;
        font-size: 24px;
        font-weight: 700;
        color: #2c4a3e;
        line-height: 1.25;
    }

    .edit-header-row p {
        margin: 0;
        font-size: 14.5px;
        color: #4b5563;
        line-height: 1.45;
    }

    .btn-top-back {
        display: inline-block;
        padding: 10px 18px;
        border-radius: 7px;
        background: #f4f6f4;
        color: #2c4a3e;
        border: 1px solid #c6d2c9;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
    }

    .btn-top-back:hover {
        background: #2c4a3e;
        color: #fdfdfc;
        border-color: #2c4a3e;
    }

    .info-notice {
        background: #f4f6f4;
        color: #1f2937;
        border: 1px solid #d8e0da;
        border-left: 4px solid #e3b535;
        border-radius: 8px;
        padding: 14px 18px;
        margin-bottom: 22px;
        font-size: 14.5px;
        line-height: 1.55;
    }

    .info-notice strong {
        color: #2c4a3e;
    }

    .err-notice {
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

    .err-notice ul {
        margin: 8px 0 0 20px;
        padding: 0;
    }

    .part-heading {
        margin: 28px 0 16px;
        padding: 9px 16px;
        background: #f4f6f4;
        border-left: 4px solid #4a7c59;
        border-radius: 4px;
        font-size: 15.5px;
        font-weight: 700;
        color: #2c4a3e;
    }

    .part-heading:first-of-type {
        margin-top: 6px;
    }

    .form-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 22px 36px;
    }

    .span-full {
        grid-column: 1 / -1;
    }

    .f-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 14.5px;
        font-weight: 700;
        color: #111827;
    }

    .f-group input,
    .f-group select {
        width: 100%;
        box-sizing: border-box;
        padding: 12px 15px;
        font-size: 15px;
        line-height: 1.4;
        color: #111827;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
    }

    .f-group input:focus,
    .f-group select:focus {
        outline: none;
        border-color: #2c4a3e;
    }

    .f-group input[readonly],
    .f-group select:disabled {
        background: #f4f6f4;
        color: #374151;
        border-color: #d8e0da;
        cursor: not-allowed;
    }

    .f-help {
        display: block;
        margin-top: 7px;
        font-size: 13px;
        color: #4b5563;
        line-height: 1.45;
    }

    .f-error {
        display: block;
        margin-top: 7px;
        font-size: 13px;
        font-weight: 600;
        color: #991b1b;
    }

    .form-footer-actions {
        margin-top: 32px;
        padding-top: 22px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .btn-save-primary {
        padding: 12px 26px;
        border-radius: 8px;
        border: none;
        background: #2c4a3e;
        color: #fdfdfc;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-save-primary:hover {
        background: #4a7c59;
    }

    .btn-cancel-light {
        padding: 11px 22px;
        border-radius: 8px;
        border: 1.5px solid #cbd5e1;
        background: #ffffff;
        color: #1f2937;
        font-size: 15px;
        font-weight: 600;
        text-decoration: none;
    }

    .btn-cancel-light:hover {
        background: #f4f6f4;
        border-color: #2c4a3e;
    }

    @media (max-width: 768px) {
        .form-grid-2 {
            grid-template-columns: 1fr;
        }

        .edit-card-main {
            padding: 20px;
        }
    }
</style>
@endpush

@section('content')
<div class="edit-page-wide">
    <div class="edit-card-main">

        <div class="edit-header-row">
            <div>
                <h1>Edit Karyawan</h1>
                <p>Perbarui informasi karyawan.</p>
            </div>

            <a href="{{ route('hrd.karyawan.show', $karyawan) }}" class="btn-top-back">
                Kembali
            </a>
        </div>

        @if($errors->any())
            <div class="err-notice">
                <strong>Terjadi kesalahan:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <form method="POST" action="{{ route('hrd.karyawan.update', $karyawan) }}">
            @csrf
            @method('PUT')

            {{-- ================================================= --}}
            {{-- DATA KARYAWAN --}}
            {{-- ================================================= --}}
            <div class="part-heading">Data Karyawan</div>

            <div class="form-grid-2">
                <div class="f-group">
                    <label for="nik">NIK</label>
                    <input
                        type="text"
                        id="nik"
                        name="nik"
                        value="{{ old('nik', $karyawan->nik) }}"
                        required
                    >
                    @error('nik')
                        <span class="f-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="f-group">
                    <label for="nama">Nama Karyawan</label>
                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        value="{{ old('nama', $karyawan->nama) }}"
                        required
                    >
                    @error('nama')
                        <span class="f-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="f-group span-full">
                    <label for="pt">PT/Perusahaan</label>
                    <input
                        type="text"
                        id="pt"
                        name="pt"
                        value="{{ old('pt', $karyawan->pt) }}"
                        maxlength="255"
                        placeholder="Contoh: OSMI atau GCNS"
                    >
                    @error('pt')
                        <span class="f-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- ================================================= --}}
            {{-- AKUN --}}
            {{-- ================================================= --}}
            <div class="part-heading">Akun Login</div>

            <div class="form-grid-2">
                <div class="f-group">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        value="{{ $karyawan->user->username ?? '-' }}"
                        readonly
                    >
                    <small class="f-help">Username tidak diubah dari halaman ini.</small>
                </div>

                <div class="f-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        value="{{ $karyawan->user->email ?? '-' }}"
                        readonly
                    >
                    <small class="f-help">Email login juga tidak diubah dari halaman ini.</small>
                </div>
            </div>

            {{-- ================================================= --}}
            {{-- PEKERJAAN --}}
            {{-- ================================================= --}}
            <div class="part-heading">Data Pekerjaan</div>

            <div class="form-grid-2">
                <div class="f-group">
                    <label for="departemen_id">Departemen</label>
                    <select id="departemen_id" name="departemen_id" required>
                        @foreach($departemens as $departemen)
                            <option
                                value="{{ $departemen->id }}"
                                {{ old('departemen_id', $karyawan->departemen_id) == $departemen->id ? 'selected' : '' }}
                            >
                                {{ $departemen->nama_departemen }}
                            </option>
                        @endforeach
                    </select>
                    @error('departemen_id')
                        <span class="f-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="f-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option
                            value="aktif"
                            {{ old('status', $karyawan->status) === 'aktif' ? 'selected' : '' }}
                        >
                            Aktif
                        </option>
                        <option
                            value="nonaktif"
                            {{ old('status', $karyawan->status) === 'nonaktif' ? 'selected' : '' }}
                        >
                            Nonaktif
                        </option>
                    </select>
                    @error('status')
                        <span class="f-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="f-group">
                    <label for="tanggal_masuk">Tanggal Masuk</label>
                    <input
                        type="date"
                        id="tanggal_masuk"
                        name="tanggal_masuk"
                        value="{{ old('tanggal_masuk', $karyawan->tanggal_masuk) }}"
                        required
                    >
                    @error('tanggal_masuk')
                        <span class="f-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="f-group">
                    <label for="jabatan_saat_ini">Jabatan Saat Ini</label>
                    <select id="jabatan_saat_ini" disabled>
                        @foreach($jabatans as $jabatan)
                            @if($jabatan->id === $karyawan->jabatan_id)
                                <option selected>
                                    {{ $jabatan->nama_jabatan }} — Level {{ $jabatan->level_jabatan }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    <small class="f-help">
                        Jabatan tidak dapat diubah melalui Edit Karyawan.
                        Gunakan modul Kenaikan Jabatan atau Pemindahan Jabatan.
                    </small>
                </div>
            </div>

            <div class="form-footer-actions">
                <button type="submit" class="btn-save-primary">Simpan Perubahan</button>
                <a href="{{ route('hrd.karyawan.show', $karyawan) }}" class="btn-cancel-light">Batal</a>
            </div>
        </form>

    </div>
</div>
@endsection