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

<body class="bg-stone-100 min-h-screen text-neutral-800 antialiased flex items-center justify-center p-6">

    <main class="w-full max-w-xl">

        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-8 sm:p-10 text-center">

            <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400">
                Hasil Assessment
            </p>

            <h1 class="text-xl sm:text-2xl font-bold text-neutral-900 mt-2">
                {{ $peserta->assessment->nama_assessment }}
            </h1>

            @if ($peserta->hasilAssessment)

                @php
                    $isLulus = $peserta->hasilAssessment->status === 'lulus';
                @endphp

                <!-- Blok Nilai Akhir: Emas jika Lulus, Biru jika Tidak Lulus -->
                <div
                    class="mt-7 rounded-xl border py-8 px-6 {{ $isLulus ? 'bg-amber-100 border-amber-300 text-amber-950' : 'bg-blue-100 border-blue-300 text-blue-950' }}"
                >
                    <p class="text-xs font-bold uppercase tracking-widest opacity-75">
                        Nilai Akhir
                    </p>

                    <p class="text-6xl font-extrabold tracking-tight mt-2 tabular-nums">
                        {{ number_format($peserta->hasilAssessment->nilai_akhir, 2) }}
                    </p>
                </div>

                <!-- Standar Kelulusan & Status -->
                <div class="grid grid-cols-2 divide-x divide-stone-200 border border-stone-200 rounded-xl mt-6 bg-stone-50/60">

                    <div class="py-4 px-4">
                        <p class="text-xs font-medium text-neutral-500">
                            Standar Kelulusan
                        </p>
                        <p class="text-xl font-bold text-neutral-900 mt-1 tabular-nums">
                            {{ number_format($peserta->hasilAssessment->standar_nilai, 2) }}
                        </p>
                    </div>

                    <div class="py-4 px-4 flex flex-col items-center justify-center">
                        <p class="text-xs font-medium text-neutral-500">
                            Status
                        </p>
                        @if ($isLulus)
                            <span class="mt-1.5 inline-flex px-3.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                LULUS
                            </span>
                        @else
                            <span class="mt-1.5 inline-flex px-3.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                TIDAK LULUS
                            </span>
                        @endif
                    </div>

                </div>

                <!-- Keterangan Singkat -->
                <div class="mt-6 text-sm text-neutral-600 leading-relaxed">
                    @if ($isLulus)
                        <p class="font-semibold text-neutral-900">
                            Selamat, Anda memenuhi standar kelulusan assessment.
                        </p>
                        <p class="mt-1">
                            Hasil ini dapat menjadi dasar proses pengembangan jabatan atau skill sesuai keputusan HRD.
                        </p>
                    @else
                        <p class="font-semibold text-neutral-900">
                            Anda belum memenuhi standar kelulusan assessment.
                        </p>
                        <p class="mt-1">
                            Silakan menunggu informasi atau evaluasi lebih lanjut dari HRD.
                        </p>
                    @endif
                </div>

            @else

                <div class="mt-7 py-6 px-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-900">
                    Hasil assessment belum tersedia.
                </div>

            @endif

            <div class="mt-8">
                <a
                    href="{{ route('karyawan.assessment.index') }}"
                    class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl bg-neutral-900 hover:bg-neutral-800 text-white text-sm font-semibold transition-colors"
                >
                    Kembali ke Assessment
                </a>
            </div>

        </div>

    </main>

</body>

</html>