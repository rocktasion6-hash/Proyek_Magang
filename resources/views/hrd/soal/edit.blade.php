<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Soal - HRD</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen">

    <main class="max-w-4xl mx-auto px-6 py-10">

        <div class="mb-6">

            <h1 class="text-2xl font-bold text-gray-800">
                Edit Soal
            </h1>

            <p class="text-gray-500 mt-1">
                Perbarui data soal dan pilihan jawaban.
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
            action="{{ route('hrd.soal.update', $soal) }}"
            id="soalForm"
        >

            @csrf
            @method('PUT')


            <!-- Informasi Soal -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

                <h2 class="text-lg font-semibold text-gray-800 mb-5">
                    Informasi Soal
                </h2>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <!-- Jabatan -->
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Jabatan
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
                                    @selected(old('jabatan_id', $soal->jabatan_id) == $jabatan->id)
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
                            required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2"
                        >

                            <option value="">
                                Pilih Skill
                            </option>

                            @foreach ($skills as $skill)

                                <option
                                    value="{{ $skill->id }}"
                                    @selected(old('skill_id', $soal->skill_id) == $skill->id)
                                >
                                    {{ $skill->nama_skill }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <!-- Tipe Soal -->
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tipe Soal
                        </label>

                        <select
                            name="tipe_soal"
                            id="tipe_soal"
                            required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2"
                        >

                            <option value="pilihan_tunggal"
                                @selected(old('tipe_soal', $soal->tipe_soal) === 'pilihan_tunggal')
                            >
                                Pilihan Tunggal ABCD
                            </option>

                            <option value="multi_jawaban"
                                @selected(old('tipe_soal', $soal->tipe_soal) === 'multi_jawaban')
                            >
                                Multi Jawaban ABCD
                            </option>

                            <option value="benar_salah"
                                @selected(old('tipe_soal', $soal->tipe_soal) === 'benar_salah')
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
                            required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2"
                        >

                            <option value="mudah"
                                @selected(old('tingkat_kesulitan', $soal->tingkat_kesulitan) === 'mudah')
                            >
                                Mudah
                            </option>

                            <option value="sedang"
                                @selected(old('tingkat_kesulitan', $soal->tingkat_kesulitan) === 'sedang')
                            >
                                Sedang
                            </option>

                            <option value="sulit"
                                @selected(old('tingkat_kesulitan', $soal->tingkat_kesulitan) === 'sulit')
                            >
                                Sulit
                            </option>

                        </select>

                    </div>

                </div>


                <!-- Pertanyaan -->
                <div class="mt-5">

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pertanyaan
                    </label>

                    <textarea
                        name="pertanyaan"
                        rows="4"
                        required
                        class="w-full border border-gray-300 rounded-lg px-3 py-3"
                    >{{ old('pertanyaan', $soal->pertanyaan) }}</textarea>

                </div>


                <!-- Bobot -->
                <div class="mt-5 max-w-xs">

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Bobot Soal
                    </label>

                    <input
                        type="number"
                        name="bobot"
                        value="{{ old('bobot', $soal->bobot) }}"
                        min="0.01"
                        step="0.01"
                        required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2"
                    >

                </div>

            </div>


            <!-- Pilihan Jawaban -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">

                <div class="mb-5">

                    <h2 class="text-lg font-semibold text-gray-800">
                        Pilihan Jawaban
                    </h2>

                    <p
                        id="petunjukJawaban"
                        class="text-sm text-gray-500 mt-1"
                    >
                        Pilih tipe soal terlebih dahulu.
                    </p>

                </div>


                <div id="pilihanContainer"></div>

            </div>


            <!-- Tombol -->
            <div class="flex items-center justify-between mt-6">

                <a
                    href="{{ route('hrd.soal.show', $soal) }}"
                    class="px-5 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold"
                >
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </main>


    <script>
        const tipeSelect = document.getElementById('tipe_soal');
        const container = document.getElementById('pilihanContainer');
        const petunjuk = document.getElementById('petunjukJawaban');

        const pilihanLama = {{ Js::from($soal->pilihanJawabans->map(fn($p) => ['kode' => $p->kode, 'teks_jawaban' => $p->teks_jawaban, 'is_benar' => $p->is_benar])->values()) }};


        function buatPilihan(useDataLama = true) {

            const tipe = tipeSelect.value;

            container.innerHTML = '';

            if (tipe === 'benar_salah') {

                petunjuk.textContent =
                    'Pilih satu jawaban yang benar.';

                const pilihan = [
                    ['A', 'Benar'],
                    ['B', 'Salah']
                ];

                pilihan.forEach(function (item, index) {

                    const dataLama = pilihanLama[index];

                    const checked = useDataLama && dataLama
                        ? dataLama.is_benar
                        : false;

                    container.innerHTML += `
                        <div class="border border-gray-200 rounded-lg p-4 mb-3">

                            <div class="flex items-center gap-4">

                                <input
                                    type="radio"
                                    name="jawaban_benar[]"
                                    value="${index + 1}"
                                    class="w-4 h-4"
                                    ${checked ? 'checked' : ''}
                                >

                                <input
                                    type="hidden"
                                    name="pilihan[${index}][kode]"
                                    value="${item[0]}"
                                >

                                <input
                                    type="hidden"
                                    name="pilihan[${index}][teks_jawaban]"
                                    value="${item[1]}"
                                >

                                <span class="font-bold text-gray-800">
                                    ${item[0]}
                                </span>

                                <span class="text-gray-700">
                                    ${item[1]}
                                </span>

                            </div>

                        </div>
                    `;

                });

                return;
            }


            petunjuk.textContent =
                tipe === 'pilihan_tunggal'
                    ? 'Pilih tepat satu jawaban yang benar.'
                    : 'Pilih semua jawaban yang benar.';


            const kode = ['A', 'B', 'C', 'D'];


            kode.forEach(function (huruf, index) {

                const dataLama = pilihanLama[index];

                const teksLama = dataLama
                    ? dataLama.teks_jawaban
                    : '';

                const checked = useDataLama && dataLama
                    ? dataLama.is_benar
                    : false;


                const inputType =
                    tipe === 'pilihan_tunggal'
                        ? 'radio'
                        : 'checkbox';


                container.innerHTML += `
                    <div class="border border-gray-200 rounded-lg p-4 mb-3">

                        <div class="flex items-start gap-4">

                            <input
                                type="${inputType}"
                                name="jawaban_benar[]"
                                value="${index + 1}"
                                class="mt-1 w-4 h-4"
                                ${checked ? 'checked' : ''}
                            >

                            <div class="flex-1">

                                <input
                                    type="hidden"
                                    name="pilihan[${index}][kode]"
                                    value="${huruf}"
                                >

                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Pilihan ${huruf}
                                </label>

                                <input
                                    type="text"
                                    name="pilihan[${index}][teks_jawaban]"
                                    value="${escapeHtml(teksLama)}"
                                    required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                    placeholder="Masukkan jawaban ${huruf}..."
                                >

                            </div>

                        </div>

                    </div>
                `;

            });
        }


        function escapeHtml(text) {

            const div = document.createElement('div');

            div.textContent = text;

            return div.innerHTML;
        }


        tipeSelect.addEventListener(
            'change',
            function () {
                // Saat HRD mengganti tipe soal,
                // buat pilihan baru.
                buatPilihan(false);
            }
        );


        // Saat pertama kali membuka halaman,
        // tampilkan data pilihan yang sudah ada.
        buatPilihan(true);
    </script>

</body>

</html>