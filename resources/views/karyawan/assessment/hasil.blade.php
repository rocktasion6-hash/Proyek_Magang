<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Hasil Assessment</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen">

    <main class="max-w-3xl mx-auto px-6 py-10">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">

            <p class="text-sm text-gray-500">
                Hasil Assessment
            </p>

            <h1 class="text-2xl font-bold text-gray-800 mt-2">
                {{ $peserta->assessment->nama_assessment }}
            </h1>


            @if ($peserta->hasilAssessment)

                <div class="mt-8">

                    <p class="text-sm text-gray-500">
                        Nilai Akhir
                    </p>

                    <p class="text-6xl font-bold text-gray-800 mt-2">
                        {{ number_format($peserta->hasilAssessment->nilai_akhir, 2) }}
                    </p>

                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-8">

                    <div class="bg-gray-50 rounded-xl p-5">

                        <p class="text-sm text-gray-500">
                            Standar Kelulusan
                        </p>

                        <p class="text-xl font-bold text-gray-800 mt-1">
                            {{ number_format($peserta->hasilAssessment->standar_nilai, 2) }}
                        </p>

                    </div>


                    <div class="bg-gray-50 rounded-xl p-5">

                        <p class="text-sm text-gray-500">
                            Status
                        </p>

                        <div class="mt-2">

                            @if ($peserta->hasilAssessment->status === 'lulus')

                                <span class="inline-flex px-4 py-2 rounded-full bg-green-100 text-green-700 font-semibold">
                                    LULUS
                                </span>

                            @else

                                <span class="inline-flex px-4 py-2 rounded-full bg-red-100 text-red-700 font-semibold">
                                    TIDAK LULUS
                                </span>

                            @endif

                        </div>

                    </div>

                </div>


                <div class="mt-8 text-left bg-blue-50 rounded-xl p-5">

                    @if ($peserta->hasilAssessment->status === 'lulus')

                        <p class="font-semibold text-blue-900">
                            Selamat, Anda memenuhi standar kelulusan assessment.
                        </p>

                        <p class="text-sm text-blue-800 mt-1">
                            Hasil assessment ini dapat menjadi dasar proses
                            pengembangan jabatan atau skill sesuai keputusan HRD.
                        </p>

                    @else

                        <p class="font-semibold text-blue-900">
                            Anda belum memenuhi standar kelulusan assessment.
                        </p>

                        <p class="text-sm text-blue-800 mt-1">
                            Silakan menunggu informasi atau evaluasi lebih lanjut
                            dari HRD.
                        </p>

                    @endif

                </div>

            @else

                <div class="mt-8 bg-yellow-50 text-yellow-800 p-4 rounded-lg">
                    Hasil assessment belum tersedia.
                </div>

            @endif


            <div class="mt-8">

                <a
                    href="{{ route('karyawan.assessment.index') }}"
                    class="inline-flex px-5 py-3 bg-gray-800 hover:bg-gray-900 text-white rounded-lg font-semibold"
                >
                    Kembali ke Assessment
                </a>

            </div>

        </div>

    </main>

</body>

</html>