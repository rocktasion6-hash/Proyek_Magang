
@extends('layouts.hrd')

@section('title', 'Tambah Karyawan')
@section('page_title', 'Tambah Karyawan')

@push('styles')
<style>
    .tambah-wrap {
        width: 100%;
        max-width: 1220px;
        margin: 0 auto;
    }

    .tambah-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 26px 28px;
    }

    .catatan-bar {
        background: #f8faf9;
        color: #1f2937;
        border: 1px solid #e5e7eb;
        border-left: 4px solid #e3b535;
        border-radius: 10px;
        padding: 13px 16px;
        margin-bottom: 22px;
        font-size: 13.5px;
        line-height: 1.5;
    }

    .catatan-bar strong {
        color: #111827;
    }

    .error-bar {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
        border-left: 4px solid #991b1b;
        border-radius: 10px;
        padding: 13px 16px;
        margin-bottom: 22px;
        font-size: 13.5px;
    }

    .error-bar ul {
        margin: 6px 0 0 18px;
        padding: 0;
    }

    .section-strip {
        background: #f4f6f4;
        border-left: 4px solid #4a7c59;
        border-radius: 6px;
        padding: 9px 14px;
        margin: 26px 0 18px;
        font-size: 14px;
        font-weight: 700;
        color: #1f2937;
    }

    .section-strip:first-of-type {
        margin-top: 4px;
    }

    .grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px 24px;
    }

    .span-2 {
        grid-column: 1 / -1;
    }

    .input-item label {
        display: block;
        margin-bottom: 8px;
        font-size: 13.5px;
        font-weight: 700;
        color: #111827;
    }

    .input-item input,
    .input-item select {
        width: 100%;
        box-sizing: border-box;
        padding: 11px 14px;
        font-size: 14px;
        color: #111827;
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 10px;
    }

    .input-item input:focus,
    .input-item select:focus {
        outline: none;
        border-color: #4a7c59;
    }

    .input-item input:disabled,
    .input-item select:disabled {
        background: #f3f4f6;
        color: #6b7280;
        border-color: #e5e7eb;
        cursor: not-allowed;
    }

    .sub-hint {
        display: block;
        margin-top: 6px;
        font-size: 12.5px;
        color: #6b7280;
    }

    .field-err {
        display: block;
        margin-top: 6px;
        font-size: 12.5px;
        font-weight: 600;
        color: #991b1b;
    }

    .form-actions {
        margin-top: 28px;
        padding-top: 18px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-simpan-utama {
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        background: #4a7c59;
        color: #fdfdfc;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-simpan-utama:hover {
        background: #3b6649;
    }

    .btn-batal-link {
        padding: 10px 16px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        background: #ffffff;
        color: #374151;
        font-size: 14px;
        text-decoration: none;
    }

    .btn-batal-link:hover {
        background: #f4f6f4;
    }

    @media (max-width: 768px) {
        .grid-2 {
            grid-template-columns: 1fr;
        }

        .tambah-card {
            padding: 18px;
        }
    }
</style>
@endpush

@section('content')
<div class="tambah-wrap">
    <div class="tambah-card">

        @if($errors->any())
            <div class="error-bar">
                <strong>Terjadi kesalahan:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('hrd.karyawan.store') }}">
            @csrf

            {{-- ====================================== --}}
            {{-- DATA KARYAWAN --}}
            {{-- ====================================== --}}
            <div class="section-strip">Data Karyawan</div>

            <div class="grid-2">
                <div class="input-item">
                    <label for="nik">NIK</label>
                    <input
                        type="text"
                        id="nik"
                        name="nik"
                        value="{{ old('nik') }}"
                        required
                        placeholder="Contoh: KRY003"
                    >
                    @error('nik')
                        <span class="field-err">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-item">
                    <label for="nama">Nama Karyawan</label>
                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        value="{{ old('nama') }}"
                        required
                        placeholder="Nama lengkap karyawan"
                    >
                    @error('nama')
                        <span class="field-err">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-item span-2">
                    <label for="pt">PT/Perusahaan</label>
                    <input
                        type="text"
                        id="pt"
                        name="pt"
                        value="{{ old('pt') }}"
                        maxlength="255"
                        placeholder="Contoh: OSMI atau GCNS"
                    >
                    @error('pt')
                        <span class="field-err">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- ====================================== --}}
            {{-- AKUN LOGIN --}}
            {{-- ====================================== --}}
            <div class="section-strip">Akun Login</div>

            <div class="grid-2">
                <div class="input-item">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="{{ old('username') }}"
                        required
                        placeholder="Contoh: karyawan003"
                    >
                    <small class="sub-hint">Username digunakan saat login.</small>
                    @error('username')
                        <span class="field-err">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-item">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        placeholder="Contoh: karyawan@gmail.com"
                    >
                    @error('email')
                        <span class="field-err">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-item">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        minlength="8"
                        placeholder="Minimal 8 karakter"
                    >
                    @error('password')
                        <span class="field-err">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-item">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        minlength="8"
                        placeholder="Ulangi password"
                    >
                </div>
            </div>

            {{-- ====================================== --}}
            {{-- DATA PEKERJAAN --}}
            {{-- ====================================== --}}
            <div class="section-strip">Data Pekerjaan</div>

            <div class="grid-2">
                <div class="input-item">
                    <label for="departemen_id">Departemen</label>
                    <select id="departemen_id" name="departemen_id" required>
                        <option value="">-- Pilih Departemen --</option>
                        @foreach($departemens as $departemen)
                            <option
                                value="{{ $departemen->id }}"
                                {{ old('departemen_id') == $departemen->id ? 'selected' : '' }}
                            >
                                {{ $departemen->nama_departemen }}
                            </option>
                        @endforeach
                    </select>
                    @error('departemen_id')
                        <span class="field-err">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-item">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>
                            Aktif
                        </option>
                        <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>
                            Nonaktif
                        </option>
                    </select>
                    @error('status')
                        <span class="field-err">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-item">
                    <label for="tanggal_masuk">Tanggal Masuk</label>
                    <input
                        type="date"
                        id="tanggal_masuk"
                        name="tanggal_masuk"
                        value="{{ old('tanggal_masuk', now()->toDateString()) }}"
                        required
                    >
                    @error('tanggal_masuk')
                        <span class="field-err">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-item">
                    <label for="jabatan_awal">Jabatan Awal</label>
                    <input
                        type="text"
                        id="jabatan_awal"
                        value="Crew"
                        disabled
                    >
                    <small class="sub-hint">
                        Jabatan awal karyawan baru selalu Crew.
                        Perubahan jabatan dilakukan melalui ujian pemindahan jabatan.
                    </small>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-simpan-utama">
                    Simpan Karyawan
                </button>
                <a href="{{ route('hrd.karyawan.index') }}" class="btn-batal-link">
                    Kembali
                </a>
            </div>
        </form>

    </div>
</div>
@endsection
