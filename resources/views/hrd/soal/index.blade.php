<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Bank Soal - HRD</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen">

    <nav class="bg-white border-b border-gray-200">

        <div class="max-w-7xl mx-auto px-6 py-4">

            <div class="flex items-center justify-between">

                <div>
                    <h1 class="text-xl font-bold text-gray-800">
                        Sistem Penilaian Jabatan
                    </h1>

                    <p class="text-sm text-gray-500">
                        Bank Soal
                    </p>
                </div>

                <div class="flex items-center gap-4">

                    <span class="text-sm text-gray-600">
                        {{ auth()->user()->name }}
                    </span>

                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm"
                        >
                            Logout
                        </button>
                    </form>

                </div>

            </div>

        </div>

    </nav>


    <main class="max-w-7xl mx-auto px-6 py-8">

        <!-- Header -->
        @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
        @endif
       
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <a
                href="{{ route('hrd.soal.create') }}"
                class="inline-flex items-center justify-center px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium"
            >
                + Tambah Soal
            </a>

            <div>

                <h2 class="text-2xl font-bold text-gray-800">
                    Bank Soal
                </h2>

                <p class="text-gray-500 mt-1">
                    Kelola soal yang akan digunakan dalam assessment.
                </p>
            </div>
        </div>


        <!-- Filter -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6">

            <form
                method="GET"
                action="{{ route('hrd.soal.index') }}"
            >

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    <!-- Jabatan -->
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Jabatan
                        </label>

                        <select
                            name="jabatan_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2"
                        >

                            <option value="">
                                Semua Jabatan
                            </option>

                            @foreach ($jabatans as $jabatan)

                                <option
                                    value="{{ $jabatan->id }}"
                                    @selected(request('jabatan_id') == $jabatan->id)
                                >
                                    {{ $jabatan->nama_jabatan }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <!-- Skill -->
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Skill
                        </label>

                        <select
                            name="skill_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2"
                        >

                            <option value="">
                                Semua Skill
                            </option>

                            @foreach ($skills as $skill)

                                <option
                                    value="{{ $skill->id }}"
                                    @selected(request('skill_id') == $skill->id)
                                >
                                    {{ $skill->nama_skill }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <!-- Tipe -->
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tipe Soal
                        </label>

                        <select
                            name="tipe_soal"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2"
                        >

                            <option value="">
                                Semua Tipe
                            </option>

                            <option
                                value="pilihan_tunggal"
                                @selected(request('tipe_soal') === 'pilihan_tunggal')
                            >
                                Pilihan Tunggal
                            </option>

                            <option
                                value="multi_jawaban"
                                @selected(request('tipe_soal') === 'multi_jawaban')
                            >
                                Multi Jawaban
                            </option>

                            <option
                                value="benar_salah"
                                @selected(request('tipe_soal') === 'benar_salah')
                            >
                                Benar / Salah
                            </option>

                        </select>

                    </div>


                    <!-- Kesulitan -->
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tingkat Kesulitan
                        </label>

                        <select
                            name="tingkat_kesulitan"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2"
                        >

                            <option value="">
                                Semua Tingkat
                            </option>

                            <option
                                value="mudah"
                                @selected(request('tingkat_kesulitan') === 'mudah')
                            >
                                Mudah
                            </option>

                            <option
                                value="sedang"
                                @selected(request('tingkat_kesulitan') === 'sedang')
                            >
                                Sedang
                            </option>

                            <option
                                value="sulit"
                                @selected(request('tingkat_kesulitan') === 'sulit')
                            >
                                Sulit
                            </option>

                        </select>

                    </div>

                </div>


                <div class="flex gap-3 mt-4">

                    <button
                        type="submit"
                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium"
                    >
                        Filter
                    </button>

                    <a
                        href="{{ route('hrd.soal.index') }}"
                        class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium"
                    >
                        Reset
                    </a>

                </div>

            </form>

        </div>


        <!-- Daftar Soal -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">

            <div class="px-6 py-5 border-b border-gray-200">

                <h3 class="text-lg font-semibold text-gray-800">
                    Daftar Bank Soal
                </h3>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="px-6 py-3 text-left">
                                #
                            </th>

                            <th class="px-6 py-3 text-left">
                                Pertanyaan
                            </th>

                            <th class="px-6 py-3 text-left">
                                Jabatan
                            </th>

                            <th class="px-6 py-3 text-left">
                                Skill
                            </th>

                            <th class="px-6 py-3 text-left">
                                Tipe
                            </th>

                            <th class="px-6 py-3 text-left">
                                Kesulitan
                            </th>

                            <th class="px-6 py-3 text-left">
                                Bobot
                            </th>

                            <th class="px-6 py-3 text-left">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse ($soals as $index => $soal)

                            <tr class="hover:bg-gray-50">

                                <td class="px-6 py-4">
                                    {{ $soals->firstItem() + $index }}
                                </td>


                                <td class="px-6 py-4">

                                    <div class="max-w-md">

                                        <p class="font-medium text-gray-800">
                                            {{ $soal->pertanyaan }}
                                        </p>

                                    </div>

                                </td>


                                <td class="px-6 py-4 text-gray-600">
                                    {{ $soal->jabatan?->nama_jabatan ?? '-' }}
                                </td>


                                <td class="px-6 py-4 text-gray-600">
                                    {{ $soal->skill?->nama_skill ?? '-' }}
                                </td>


                                <td class="px-6 py-4">

                                    @if ($soal->tipe_soal === 'pilihan_tunggal')

                                        <span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700">
                                            Pilihan Tunggal
                                        </span>

                                    @elseif ($soal->tipe_soal === 'multi_jawaban')

                                        <span class="px-2 py-1 rounded-full text-xs bg-purple-100 text-purple-700">
                                            Multi Jawaban
                                        </span>

                                    @else

                                        <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">
                                            Benar / Salah
                                        </span>

                                    @endif

                                </td>


                                <td class="px-6 py-4 text-gray-600">
                                    {{ ucfirst($soal->tingkat_kesulitan) }}
                                </td>


                                <td class="px-6 py-4 text-gray-600">
                                    {{ number_format($soal->bobot, 0) }}
                                </td>


                                <td class="px-6 py-4">

                                    <a
                                        href="{{ route('hrd.soal.show', $soal) }}"
                                        class="text-blue-600 hover:text-blue-800 font-medium"
                                    >
                                        Detail
                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="8"
                                    class="px-6 py-10 text-center text-gray-500"
                                >
                                    Belum ada soal.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            @if ($soals->hasPages())

                <div class="px-6 py-4 border-t border-gray-200">

                    {{ $soals->links() }}

                </div>

            @endif

        </div>

    </main>

</body>
</html>