<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Detail Assessment - HRD</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>


<body class="bg-gray-100 min-h-screen">

    <main class="max-w-6xl mx-auto px-6 py-10">

        <div class="mb-6">

            <h1 class="text-2xl font-bold text-gray-800">
                {{ $assessment->nama_assessment }}
            </h1>

            <p class="text-gray-500 mt-1">
                Detail assessment dan peserta.
            </p>

        </div>


        <!-- Informasi -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div>

                    <p class="text-sm text-gray-500">
                        Jabatan
                    </p>

                    <p class="font-semibold text-gray-800 mt-1">
                        {{ $assessment->jabatan?->nama_jabatan ?? '-' }}
                    </p>

                </div>


                <div>

                    <p class="text-sm text-gray-500">
                        Skill
                    </p>

                    <p class="font-semibold text-gray-800 mt-1">
                        {{ $assessment->skill?->nama_skill ?? 'Umum' }}
                    </p>

                </div>


                <div>

                    <p class="text-sm text-gray-500">
                        Standar Nilai
                    </p>

                    <p class="font-semibold text-gray-800 mt-1">
                        {{ number_format($assessment->standar_nilai, 0) }}
                    </p>

                </div>


                <div>

                    <p class="text-sm text-gray-500">
                        Durasi
                    </p>

                    <p class="font-semibold text-gray-800 mt-1">
                        {{ $assessment->durasi }} menit
                    </p>

                </div>


                <div>

                    <p class="text-sm text-gray-500">
                        Tanggal Mulai
                    </p>

                    <p class="font-semibold text-gray-800 mt-1">
                        {{ $assessment->tanggal_mulai?->format('d M Y H:i') }}
                    </p>

                </div>


                <div>

                    <p class="text-sm text-gray-500">
                        Tanggal Selesai
                    </p>

                    <p class="font-semibold text-gray-800 mt-1">
                        {{ $assessment->tanggal_selesai?->format('d M Y H:i') }}
                    </p>

                </div>

            </div>

        </div>


        <!-- Soal -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">

            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                Soal Assessment
            </h2>

            <div class="space-y-3">

                @foreach ($assessment->soals as $index => $soal)

                    <div class="border border-gray-200 rounded-lg p-4">

                        <div class="flex gap-4">

                            <div class="font-bold text-blue-600">
                                {{ $index + 1 }}
                            </div>

                            <div>

                                <p class="font-medium text-gray-800">
                                    {{ $soal->pertanyaan }}
                                </p>

                                <p class="text-xs text-gray-500 mt-1">
                                    {{ ucwords(str_replace('_', ' ', $soal->tipe_soal)) }}
                                </p>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>


        <!-- Peserta -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">

            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                Peserta Assessment
            </h2>


            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="px-4 py-3 text-left">
                                #
                            </th>

                            <th class="px-4 py-3 text-left">
                                NIK
                            </th>

                            <th class="px-4 py-3 text-left">
                                Nama
                            </th>

                            <th class="px-4 py-3 text-left">
                                Jabatan
                            </th>

                            <th class="px-4 py-3 text-left">
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse ($assessment->peserta as $index => $peserta)

                            <tr>

                                <td class="px-4 py-3">
                                    {{ $index + 1 }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $peserta->karyawan->nik }}
                                </td>

                                <td class="px-4 py-3 font-medium">
                                    {{ $peserta->karyawan->nama }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $peserta->karyawan->jabatan?->nama_jabatan ?? '-' }}
                                </td>

                                <td class="px-4 py-3">

                                    @if ($peserta->status === 'ditugaskan')

                                        <span class="px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">
                                            Ditugaskan
                                        </span>

                                    @elseif ($peserta->status === 'sedang_mengerjakan')

                                        <span class="px-3 py-1 rounded-full text-xs bg-blue-100 text-blue-700">
                                            Sedang Mengerjakan
                                        </span>

                                    @elseif ($peserta->status === 'selesai')

                                        <span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-700">
                                            Selesai
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="5"
                                    class="px-4 py-8 text-center text-gray-500"
                                >
                                    Belum ada peserta.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        <div class="mt-6">

            <a
                href="{{ route('hrd.assessment.index') }}"
                class="inline-flex px-5 py-3 bg-gray-800 hover:bg-gray-900 text-white rounded-lg font-medium"
            >
                ← Kembali
            </a>

        </div>

    </main>

</body>

</html>