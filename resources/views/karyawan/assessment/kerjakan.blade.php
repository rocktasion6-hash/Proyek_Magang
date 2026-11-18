<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        {{ $peserta->assessment->nama_assessment }}
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="max-w-5xl mx-auto px-6 py-8">

        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                <div>

                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ $peserta->assessment->nama_assessment }}
                    </h1>

                    <p class="text-gray-500 mt-1">
                        Silakan kerjakan seluruh soal dengan teliti.
                    </p>

                </div>

                <!-- Timer -->
                <div class="bg-red-50 border border-red-200 rounded-lg px-5 py-3 text-center">

                    <p class="text-xs text-red-500 font-medium">
                        SISA WAKTU
                    </p>

                    <p
                        id="timer"
                        class="text-2xl font-bold text-red-600"
                    >
                        --:--
                    </p>

                </div>

            </div>

        </div>


        <!-- Form Ujian -->
        <form
            id="examForm"
            method="POST"
            action="{{ route('karyawan.assessment.submit', $peserta) }}"
        >

            @csrf


            @php
                $nomorSoal = 1;
                $labelTipeSoal = [
                    'pilihan_ganda' => 'Pilihan Ganda / Pilihan Tunggal',
                    'pilihan_tunggal' => 'Pilihan Ganda / Pilihan Tunggal',
                    'multi_jawaban' => 'Multi Jawaban',
                    'benar_salah' => 'Benar / Salah',
                ];
            @endphp

            @foreach ($soalsPerTipe as $tipeSoal => $soals)

                <div class="mb-4 mt-8 first:mt-0">
                    <h2 class="text-xl font-bold text-gray-800">
                        {{ $labelTipeSoal[$tipeSoal] ?? $tipeSoal }}
                    </h2>
                </div>

                @foreach ($soals as $soal)

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">

                    <div class="flex items-start gap-4">

                        <div class="flex-shrink-0">

                            <span class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                                {{ $nomorSoal++ }}
                            </span>

                        </div>


                        <div class="flex-1">

                            <div class="flex flex-wrap items-center gap-2 mb-3">

                                @if (in_array($soal->tipe_soal, ['pilihan_ganda', 'pilihan_tunggal'], true))

                                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                                        Pilihan Ganda / Pilihan Tunggal
                                    </span>

                                @elseif ($soal->tipe_soal === 'multi_jawaban')

                                    <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full">
                                        Multi Jawaban
                                    </span>

                                @else

                                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">
                                        Benar / Salah
                                    </span>

                                @endif

                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                    Poin {{ number_format($soal->nilai_point, 2) }}
                                </span>

                            </div>


                            <p class="text-lg font-semibold text-gray-800 leading-relaxed">
                                {{ $soal->pertanyaan }}
                            </p>

                            @if ($soal->nama_prosedur)
                                <p class="text-sm text-gray-500 mt-2">
                                    {{ $soal->nama_prosedur }}
                                </p>
                            @endif


                            <!-- Pilihan -->
                            <div class="mt-5 space-y-3">

                                @foreach ($soal->pilihanJawabans as $pilihan)

                                    @if (
                                        $soal->tipe_soal === 'multi_jawaban'
                                    )

                                        <label class="flex items-start gap-3 border border-gray-200 rounded-lg p-4 cursor-pointer hover:bg-gray-50">

                                            <input
                                                type="checkbox"
                                                name="jawaban[{{ $soal->id }}][]"
                                                value="{{ $pilihan->id }}"
                                                class="mt-1 w-4 h-4"
                                            >

                                            <div>

                                                <span class="font-semibold text-gray-800">
                                                    {{ $pilihan->kode }}.
                                                </span>

                                                <span class="text-gray-700">
                                                    {{ $pilihan->teks_jawaban }}
                                                </span>

                                            </div>

                                        </label>

                                    @else

                                        <label class="flex items-start gap-3 border border-gray-200 rounded-lg p-4 cursor-pointer hover:bg-gray-50">

                                            <input
                                                type="radio"
                                                name="jawaban[{{ $soal->id }}]"
                                                value="{{ $pilihan->id }}"
                                                class="mt-1 w-4 h-4"
                                            >

                                            <div>

                                                @if ($soal->tipe_soal !== 'benar_salah')
                                                    <span class="font-semibold text-gray-800">
                                                        {{ $pilihan->kode }}.
                                                    </span>
                                                @endif

                                                <span class="text-gray-700">
                                                    {{ $soal->tipe_soal === 'benar_salah'
                                                        ? mb_strtoupper($pilihan->teks_jawaban)
                                                        : $pilihan->teks_jawaban }}
                                                </span>

                                            </div>

                                        </label>

                                    @endif

                                @endforeach

                            </div>

                        </div>

                    </div>

                </div>

                @endforeach

            @endforeach


            <!-- Submit -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-5">

                    <p class="text-sm text-yellow-800">
                        Pastikan semua jawaban sudah diperiksa sebelum menekan tombol
                        <strong>Submit Ujian</strong>.
                    </p>

                </div>

                <button
                    type="submit"
                    id="submitButton"
                    class="w-full py-3 px-6 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold"
                >
                    Submit Ujian
                </button>

            </div>

        </form>

    </div>


    <script>
        /*
         * Durasi ujian dalam menit
         */
        const durasiMenit = {{$peserta->assessment->durasi}};

        /*
         * Waktu mulai dari server
         */
        const waktuMulai = new Date(
            "{{ $peserta->waktu_mulai->toIso8601String() }}"
        ).getTime();

        /*
         * Batas waktu ujian
         */
        const waktuSelesai = waktuMulai + (
            durasiMenit * 60 * 1000
        );

        const timerElement = document.getElementById('timer');
        const examForm = document.getElementById('examForm');

        let sudahSubmit = false;


        function updateTimer() {

            const sekarang = new Date().getTime();

            const sisa = waktuSelesai - sekarang;


            if (sisa <= 0) {

                timerElement.textContent = '00:00';

                if (!sudahSubmit) {

                    sudahSubmit = true;

                    alert('Waktu ujian telah habis. Jawaban akan dikirim otomatis.');

                    examForm.submit();
                }

                return;
            }


            const totalDetik = Math.floor(
                sisa / 1000
            );

            const menit = Math.floor(
                totalDetik / 60
            );

            const detik = totalDetik % 60;


            timerElement.textContent =
                String(menit).padStart(2, '0')
                + ':'
                + String(detik).padStart(2, '0');
        }


        updateTimer();

        setInterval(updateTimer, 1000);


        /*
         * Konfirmasi sebelum submit manual
         */
        examForm.addEventListener('submit', function (event) {

            if (sudahSubmit) {
                return;
            }

            const yakin = confirm(
                'Apakah Anda yakin ingin mengirim jawaban?'
            );

            if (!yakin) {
                event.preventDefault();
                return;
            }

            sudahSubmit = true;
        });
    </script>

</body>

</html>