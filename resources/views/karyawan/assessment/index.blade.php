<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Assessment Karyawan</title>

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
                        Assessment Karyawan
                    </p>
                </div>

                <a
                    href="{{ route('karyawan.dashboard') }}"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-medium"
                >
                    Kembali
                </a>

            </div>

        </div>
    </nav>


    <main class="max-w-7xl mx-auto px-6 py-8">

        <div class="mb-8">

            <h2 class="text-2xl font-bold text-gray-800">
                Assessment Saya
            </h2>

            <p class="text-gray-500 mt-1">
                Daftar assessment yang diberikan oleh HRD.
            </p>

        </div>


        @if (session('error'))

            <div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-6">
                {{ session('error') }}
            </div>

        @endif


        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            @forelse ($assessmentPeserta as $peserta)

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

                    <div class="flex items-start justify-between gap-4">

                        <div>

                            <h3 class="text-lg font-bold text-gray-800">
                                {{ $peserta->assessment->nama_assessment }}
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Jabatan:
                                {{ $peserta->assessment->jabatan?->nama_jabatan ?? '-' }}
                            </p>

                            <p class="text-sm text-gray-500">
                                Skill:
                                {{ $peserta->assessment->skill?->nama_skill ?? 'Umum' }}
                            </p>

                        </div>


                        @if ($peserta->status === 'ditugaskan')

                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                Ditugaskan
                            </span>

                        @elseif ($peserta->status === 'sedang_mengerjakan')

                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                Sedang Mengerjakan
                            </span>

                        @elseif ($peserta->status === 'selesai')

                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                Selesai
                            </span>

                        @else

                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                {{ ucfirst($peserta->status) }}
                            </span>

                        @endif

                    </div>


                    <div class="grid grid-cols-3 gap-4 mt-6">

                        <div>
                            <p class="text-xs text-gray-500">
                                Durasi
                            </p>

                            <p class="font-semibold text-gray-800">
                                {{ $peserta->assessment->durasi }} menit
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500">
                                Standar
                            </p>

                            <p class="font-semibold text-gray-800">
                                {{ number_format($peserta->assessment->standar_nilai, 0) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500">
                                Soal
                            </p>

                            <p class="font-semibold text-gray-800">
                                {{ $peserta->assessment->soals->count() }}
                            </p>
                        </div>

                    </div>


                    <div class="mt-6">

                        <a
                            href="{{ route('karyawan.assessment.show', $peserta) }}"
                            class="inline-flex items-center justify-center w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium"
                        >
                            Lihat Assessment
                        </a>

                    </div>

                </div>

            @empty

                <div class="lg:col-span-2 bg-white rounded-xl p-10 text-center">

                    <p class="text-gray-500">
                        Belum ada assessment yang diberikan kepada Anda.
                    </p>

                </div>

            @endforelse

        </div>

    </main>

</body>

</html>