<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        {{ $peserta->assessment->nama_assessment }}
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen">

    <main class="max-w-4xl mx-auto px-6 py-10">

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">

            <div class="mb-8">

                <h1 class="text-2xl font-bold text-gray-800">
                    {{ $peserta->assessment->nama_assessment }}
                </h1>

                <p class="text-gray-500 mt-2">
                    Assessment yang diberikan oleh HRD.
                </p>

            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <p class="text-sm text-gray-500">
                        Jabatan
                    </p>

                    <p class="font-semibold text-gray-800 mt-1">
                        {{ $peserta->assessment->jabatan?->nama_jabatan ?? '-' }}
                    </p>
                </div>


                <div>
                    <p class="text-sm text-gray-500">
                        Skill
                    </p>

                    <p class="font-semibold text-gray-800 mt-1">
                        {{ $peserta->assessment->skill?->nama_skill ?? 'Umum' }}
                    </p>
                </div>


                <div>
                    <p class="text-sm text-gray-500">
                        Durasi
                    </p>

                    <p class="font-semibold text-gray-800 mt-1">
                        {{ $peserta->assessment->durasi }} menit
                    </p>
                </div>


                <div>
                    <p class="text-sm text-gray-500">
                        Standar Kelulusan
                    </p>

                    <p class="font-semibold text-gray-800 mt-1">
                        {{ number_format($peserta->assessment->standar_nilai, 0) }}
                    </p>
                </div>


                <div>
                    <p class="text-sm text-gray-500">
                        Jumlah Soal
                    </p>

                    <p class="font-semibold text-gray-800 mt-1">
                        {{ $peserta->assessment->soals->count() }} soal
                    </p>
                </div>


                <div>
                    <p class="text-sm text-gray-500">
                        Status
                    </p>

                    <p class="font-semibold text-gray-800 mt-1">
                        {{ ucfirst(str_replace('_', ' ', $peserta->status)) }}
                    </p>
                </div>

            </div>


            @if (session('error'))

                <div class="mt-6 bg-red-100 text-red-700 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>

            @endif


            <div class="mt-8 pt-6 border-t border-gray-200">

                @if ($peserta->status === 'ditugaskan')

                    <form
                        method="POST"
                        action="{{ route('karyawan.assessment.start', $peserta) }}"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg"
                        >
                            Mulai Ujian
                        </button>
                    </form>

                @elseif ($peserta->status === 'sedang_mengerjakan')

                    <button
                        type="button"
                        disabled
                        class="w-full bg-gray-400 text-white font-semibold py-3 rounded-lg cursor-not-allowed"
                    >
                        Ujian Sedang Berlangsung
                    </button>

                @elseif ($peserta->status === 'selesai')

                    <div class="text-center">

                        <p class="text-green-600 font-semibold">
                            Ujian telah selesai.
                        </p>

                    </div>

                @endif

            </div>


            <div class="mt-6">

                <a
                    href="{{ route('karyawan.assessment.index') }}"
                    class="block text-center text-gray-600 hover:text-gray-800"
                >
                    ← Kembali ke daftar assessment
                </a>

            </div>

        </div>

    </main>

</body>

</html>