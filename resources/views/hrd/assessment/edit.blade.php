@extends('layouts.hrd')

@section('title', 'Ubah Assessment')
@section('page_title', 'Ubah Assessment')

@section('content')
    <main class="mx-auto max-w-4xl px-6 py-10">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Ubah Assessment</h1>
            <p class="mt-1 text-gray-500">
                Ubah informasi assessment. Bank soal, konfigurasi soal, dan peserta tetap seperti semula.
            </p>
        </div>

        @if (session('error'))
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('hrd.assessment.update', $assessment) }}"
            class="space-y-6"
        >
            @csrf
            @method('PUT')

            <section class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label for="nama_assessment" class="mb-2 block text-sm font-medium text-gray-700">
                            Nama Assessment
                        </label>
                        <input
                            id="nama_assessment"
                            type="text"
                            name="nama_assessment"
                            value="{{ old('nama_assessment', $assessment->nama_assessment) }}"
                            required
                            maxlength="255"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2"
                        >
                    </div>

                    <div>
                        <label for="durasi" class="mb-2 block text-sm font-medium text-gray-700">
                            Durasi (menit)
                        </label>
                        <input
                            id="durasi"
                            type="number"
                            name="durasi"
                            value="{{ old('durasi', $assessment->durasi) }}"
                            min="1"
                            required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2"
                        >
                    </div>

                    <div>
                        <label for="tanggal_mulai" class="mb-2 block text-sm font-medium text-gray-700">
                            Tanggal Mulai
                        </label>
                        <input
                            id="tanggal_mulai"
                            type="datetime-local"
                            name="tanggal_mulai"
                            value="{{ old('tanggal_mulai', $assessment->tanggal_mulai?->format('Y-m-d\TH:i')) }}"
                            required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2"
                        >
                    </div>

                    <div>
                        <label for="tanggal_selesai" class="mb-2 block text-sm font-medium text-gray-700">
                            Tanggal Selesai
                        </label>
                        <input
                            id="tanggal_selesai"
                            type="datetime-local"
                            name="tanggal_selesai"
                            value="{{ old('tanggal_selesai', $assessment->tanggal_selesai?->format('Y-m-d\TH:i')) }}"
                            required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2"
                        >
                    </div>
                </div>
            </section>

            <div class="flex flex-wrap justify-end gap-3">
                <a
                    href="{{ route('hrd.assessment.index') }}"
                    class="rounded-lg border border-gray-300 px-5 py-3 font-medium text-gray-700 hover:bg-gray-50"
                >
                    Batal
                </a>
                <button
                    type="submit"
                    class="rounded-lg bg-blue-600 px-5 py-3 font-medium text-white hover:bg-blue-700"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </main>
@endsection
