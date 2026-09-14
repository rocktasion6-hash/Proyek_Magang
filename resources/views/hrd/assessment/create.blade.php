<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Buat Assessment - HRD</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="bg-gray-100 min-h-screen">

    <main class="max-w-6xl mx-auto px-6 py-10">

        <div class="mb-6">

            <h1 class="text-2xl font-bold text-gray-800">
                Buat Assessment
            </h1>

            <p class="text-gray-500 mt-1">
                Buat assessment, pilih soal, dan tentukan peserta ujian.
            </p>

        </div>


        @if ($errors->any())

            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-6">

                <ul class="list-disc pl-5 space-y-1">

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif


        <form
            method="POST"
            action="{{ route('hrd.assessment.store') }}"
        >

            @csrf


            <!-- Informasi Assessment -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

                <h2 class="text-lg font-semibold text-gray-800 mb-5">
                    Informasi Assessment
                </h2>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div class="md:col-span-2">

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Assessment
                        </label>

                        <input
                            type="text"
                            name="nama_assessment"
                            value="{{ old('nama_assessment') }}"
                            required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2"
                            placeholder="Contoh: Ujian Kenaikan Operator Senior"
                        >

                    </div>


                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Jabatan Tujuan
                        </label>

                        <select
                            name="jabatan_id"
                            required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2"
                        >

                            <option value="">
                                Pilih Jabatan
                            </option>

                            @foreach ($jabatans as $jabatan)

                                <option
                                    value="{{ $jabatan->id }}"
                                    @selected(old('jabatan_id') == $jabatan->id)
                                >
                                    {{ $jabatan->nama_jabatan }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Skill
                        </label>

                        <select
                            name="skill_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2"
                        >

                            <option value="">
                                Assessment Umum
                            </option>

                            @foreach ($skills as $skill)

                                <option
                                    value="{{ $skill->id }}"
                                    @selected(old('skill_id') == $skill->id)
                                >
                                    {{ $skill->nama_skill }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Standar Nilai
                        </label>

                        <input
                            type="number"
                            name="standar_nilai"
                            value="{{ old('standar_nilai', 80) }}"
                            min="0"
                            max="100"
                            step="0.01"
                            required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2"
                        >

                    </div>


                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Durasi (menit)
                        </label>

                        <input
                            type="number"
                            name="durasi"
                            value="{{ old('durasi', 60) }}"
                            min="1"
                            required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2"
                        >

                    </div>


                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Mulai
                        </label>

                        <input
                            type="datetime-local"
                            name="tanggal_mulai"
                            value="{{ old('tanggal_mulai') }}"
                            required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2"
                        >

                    </div>


                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Selesai
                        </label>

                        <input
                            type="datetime-local"
                            name="tanggal_selesai"
                            value="{{ old('tanggal_selesai') }}"
                            required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2"
                        >

                    </div>

                </div>

            </div>


            <!-- Pilih Soal -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">

                <div class="mb-5">

                    <h2 class="text-lg font-semibold text-gray-800">
                        Pilih Soal
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Pilih soal yang akan digunakan dalam assessment.
                    </p>

                </div>


                <div class="space-y-3">

                    @foreach ($soals as $soal)

                        <label class="block border border-gray-200 rounded-lg p-4 hover:bg-gray-50 cursor-pointer">

                            <div class="flex gap-3">

                                <input
                                    type="checkbox"
                                    name="soal_ids[]"
                                    value="{{ $soal->id }}"
                                    class="mt-1"
                                    @checked(in_array(
                                        $soal->id,
                                        old('soal_ids', [])
                                    ))
                                >

                                <div>

                                    <p class="font-medium text-gray-800">
                                        {{ $soal->pertanyaan }}
                                    </p>

                                    <div class="flex flex-wrap gap-2 mt-2">

                                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                                            {{ $soal->jabatan?->nama_jabatan ?? '-' }}
                                        </span>

                                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full">
                                            {{ $soal->skill?->nama_skill ?? '-' }}
                                        </span>

                                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                            {{ ucwords(str_replace('_', ' ', $soal->tipe_soal)) }}
                                        </span>

                                    </div>

                                </div>

                            </div>

                        </label>

                    @endforeach

                </div>

            </div>


            <!-- Pilih Peserta -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">

                <div class="mb-5">

                    <h2 class="text-lg font-semibold text-gray-800">
                        Peserta Assessment
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Pilih karyawan yang wajib mengikuti assessment.
                    </p>

                </div>


                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead class="bg-gray-50">

                            <tr>

                                <th class="px-4 py-3 text-left">
                                    Pilih
                                </th>

                                <th class="px-4 py-3 text-left">
                                    NIK
                                </th>

                                <th class="px-4 py-3 text-left">
                                    Nama
                                </th>

                                <th class="px-4 py-3 text-left">
                                    Departemen
                                </th>

                                <th class="px-4 py-3 text-left">
                                    Jabatan
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-100">

                            @foreach ($karyawans as $karyawan)

                                <tr>

                                    <td class="px-4 py-3">

                                        <input
                                            type="checkbox"
                                            name="karyawan_ids[]"
                                            value="{{ $karyawan->id }}"
                                            @checked(in_array(
                                                $karyawan->id,
                                                old('karyawan_ids', [])
                                            ))
                                        >

                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $karyawan->nik }}
                                    </td>

                                    <td class="px-4 py-3 font-medium">
                                        {{ $karyawan->nama }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $karyawan->departemen?->nama_departemen ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $karyawan->jabatan?->nama_jabatan ?? '-' }}
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- Tombol -->
            <div class="flex items-center justify-between mt-6">

                <a
                    href="{{ route('hrd.assessment.index') }}"
                    class="px-5 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold"
                >
                    Buat Assessment
                </button>

            </div>

        </form>

    </main>

</body>

</html>