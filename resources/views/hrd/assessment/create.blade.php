@extends('layouts.hrd')
@section('title', 'Tambah Assessment')
@section('page_title', 'Tambah Assessment')
@section('content')
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-7 pb-12">

        {{-- Header Buat Assessment --}}
        <div class="relative overflow-hidden bg-white rounded-2xl border border-gray-200 shadow-sm px-6 py-5 mb-5">
            <div class="absolute -right-14 -bottom-24 w-96 h-40 rounded-[100%] bg-[#e8f5eb] opacity-70 pointer-events-none"></div>
            <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-[#dff1e4] flex items-center justify-center flex-shrink-0">
                        <svg class="w-8 h-8 text-[#27643d]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4h6M9 10h6M9 14h6M9 18h4"/></svg>
                    </div>
                    <div class="border-l-2 border-[#4a7c59] pl-4">
                        <h1 class="text-2xl font-bold text-[#172b24] tracking-tight">Buat Assessment</h1>
                        <p class="text-sm text-gray-500 mt-1">Buat assessment baru untuk karyawan.</p>
                    </div>
                </div>
                <a href="{{ route('hrd.assessment.index') }}" class="self-start sm:self-auto inline-flex items-center justify-center gap-2 rounded-lg border border-[#4a7c59] bg-white px-4 py-2.5 text-sm font-semibold text-[#2c4a3e] hover:bg-[#e8f1ea] transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6M20 12H9"/></svg>
                    Kembali
                </a>
            </div>
        </div>

        @if ($errors->any())

            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-6">

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
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 sm:p-6">

                <div class="flex items-center gap-3 mb-5">
                    <span class="w-7 h-7 rounded-lg bg-[#2c4a3e] text-white flex items-center justify-center text-xs font-bold">1</span>
                    <div>
                        <h2 class="text-lg font-bold text-[#203e32]">Informasi Assessment</h2>
                    </div>
                </div>


                <div class="grid grid-cols-1 md:grid-cols-3 gap-x-5 gap-y-4">

                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Nama Assessment
                        </label>

                        <input
                            type="text"
                            name="nama_assessment"
                            value="{{ old('nama_assessment') }}"
                            required
                            class="w-full h-11 border border-gray-300 rounded-xl px-4 py-2.5 bg-white text-sm text-gray-800 outline-none focus:border-[#4a7c59] focus:ring-2 focus:ring-[#e2efe5] transition"
                            placeholder="Contoh: Ujian Kenaikan Operator Senior"
                        >

                    </div>

                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Departemen Peserta
                        </label>

                        <select
                            name="departemen_id"
                            id="departemen_id"
                            required
                            class="w-full h-11 border border-gray-300 rounded-xl px-4 py-2.5 bg-white text-sm text-gray-800 outline-none focus:border-[#4a7c59] focus:ring-2 focus:ring-[#e2efe5] transition"
                        >

                            <option value="">
                                Pilih Departemen
                            </option>

                            @foreach ($departemens as $departemen)

                                <option
                                    value="{{ $departemen->id }}"
                                    @selected(old('departemen_id') == $departemen->id)
                                >
                                    {{ $departemen->nama_departemen }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Jabatan Tujuan
                        </label>

                        <select
                            name="jabatan_id"
                            id="jabatan_id"
                            required
                            class="w-full h-11 border border-gray-300 rounded-xl px-4 py-2.5 bg-white text-sm text-gray-800 outline-none focus:border-[#4a7c59] focus:ring-2 focus:ring-[#e2efe5] transition"
                        >

                            <option value="">
                                Pilih Jabatan
                            </option>

                            @foreach ($jabatans as $jabatan)

                                <option
                                    value="{{ $jabatan->id }}"
                                    @selected(old('jabatan_id') == $jabatan->id)
                                >
                                    {{ $jabatan->nama_jabatan }}{{ $jabatan->tipe_jabatan === 'struktural' ? ' (Kenaikan Jabatan)' : '' }}
                                </option>

                            @endforeach

                        </select>

                    </div>



                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Durasi (menit)
                        </label>

                        <input
                            type="number"
                            name="durasi"
                            value="{{ old('durasi', 60) }}"
                            min="1"
                            required
                            class="w-full h-11 border border-gray-300 rounded-xl px-4 py-2.5 bg-white text-sm text-gray-800 outline-none focus:border-[#4a7c59] focus:ring-2 focus:ring-[#e2efe5] transition"
                        >

                    </div>


                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Tanggal Mulai
                        </label>

                        <input
                            type="datetime-local"
                            name="tanggal_mulai"
                            value="{{ old('tanggal_mulai') }}"
                            required
                            class="w-full h-11 border border-gray-300 rounded-xl px-4 py-2.5 bg-white text-sm text-gray-800 outline-none focus:border-[#4a7c59] focus:ring-2 focus:ring-[#e2efe5] transition"
                        >

                    </div>


                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Tanggal Selesai
                        </label>

                        <input
                            type="datetime-local"
                            name="tanggal_selesai"
                            value="{{ old('tanggal_selesai') }}"
                            required
                            class="w-full h-11 border border-gray-300 rounded-xl px-4 py-2.5 bg-white text-sm text-gray-800 outline-none focus:border-[#4a7c59] focus:ring-2 focus:ring-[#e2efe5] transition"
                        >

                    </div>

                    <div class="md:col-span-3 rounded-xl bg-[#f0f7f1] border border-[#dbe9dd] px-4 py-3 flex items-start gap-2">
                        <svg class="w-5 h-5 text-[#4a7c59] shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 11v6M12 7h.01"/></svg>
                        <p class="text-sm text-[#436250]">Standar kelulusan otomatis mengikuti standar nilai yang ditetapkan pada jabatan.</p>
                    </div>

                </div>

            </div>


            <!-- Konfigurasi Soal -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 sm:p-6 mt-5">

                <div class="mb-5">

                    <div class="flex items-center gap-3 mb-3">
                        <span class="w-7 h-7 rounded-lg bg-[#2c4a3e] text-white flex items-center justify-center text-xs font-bold">2</span>
                        <h2 class="text-lg font-bold text-[#203e32]">Susun Soal</h2>
                    </div>

                    <p class="text-sm text-gray-500 mt-1">
                        Pilih peraturan, K3, atau SOP, lalu klik Tambahkan untuk mengatur komposisi soal. Bank soal tersedia mengikuti departemen dan jabatan tujuan.
                    </p>

                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <select
                        id="pilihan-soal"
                        class="w-full h-11 border border-gray-300 rounded-xl px-4 py-2.5 bg-white text-sm text-gray-800 outline-none focus:border-[#4a7c59] focus:ring-2 focus:ring-[#e2efe5] transition"
                    >
                        <option value="">Pilih prosedur atau SOP</option>
                    </select>

                    <button
                        id="tambah-soal"
                        type="button"
                        class="shrink-0 rounded-xl bg-[#2c4a3e] px-5 py-2.5 font-semibold text-white hover:bg-[#3c624e] transition disabled:cursor-not-allowed disabled:bg-gray-300"
                        disabled
                    >
                        Tambahkan
                    </button>
                </div>

                <p id="bank-soal-info" class="text-sm text-gray-500 mt-2"></p>

                <div id="bank-soal-container" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5"></div>

                <div
                    id="konfigurasi-soal-container"
                    class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-5"
                >
                    <p class="text-sm text-gray-500">
                        Belum ada kategori soal yang ditambahkan.
                    </p>
                </div>

            </div>


            <!-- Peserta Otomatis -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 sm:p-6 mt-5">

                <div class="mb-5">

                    <div class="flex items-center gap-3 mb-3">
                        <span class="w-7 h-7 rounded-lg bg-[#2c4a3e] text-white flex items-center justify-center text-xs font-bold">3</span>
                        <h2 class="text-lg font-bold text-[#203e32]">Peserta Otomatis</h2>
                    </div>

                    <p class="text-sm text-gray-500 mt-1">
                        Peserta yang memenuhi ketentuan akan muncul otomatis berdasarkan departemen dan jabatan tujuan.
                    </p>

                </div>


                <p id="peserta-info" class="text-sm text-gray-600 mb-3">
                    Pilih departemen dan jabatan tujuan untuk melihat peserta yang akan ditugaskan.
                </p>

                <div class="overflow-x-auto rounded-xl border border-gray-200">

                    <table class="w-full min-w-[700px] text-sm">

                        <thead class="bg-[#e0f0e4] text-[#203e32]">

                            <tr>

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

                                <tr
                                    data-karyawan-row
                                    data-departemen-id="{{ $karyawan->departemen_id }}"
                                    data-jabatan-nama="{{ $karyawan->jabatan?->nama_jabatan ?? '' }}"
                                    data-jabatan-level="{{ $karyawan->jabatan?->level_jabatan ?? '' }}"
                                    data-jabatan-jenis="{{ $karyawan->jabatan?->tipe_jabatan ?? '' }}"
                                    data-jabatan-urutan-struktural="{{ $karyawan->jabatan?->urutan_struktural ?? '' }}"
                                >

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
            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 mt-5 bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">

                <a
                    href="{{ route('hrd.assessment.index') }}"
                    class="inline-flex items-center justify-center px-5 py-3 bg-white border border-[#4a7c59] hover:bg-[#e8f1ea] text-[#2c4a3e] rounded-xl font-semibold text-sm transition"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center px-6 py-3 bg-[#2c4a3e] hover:bg-[#3c624e] text-white rounded-xl font-semibold text-sm shadow-sm transition"
                >
                    Simpan Assessment
                </button>

            </div>

        </form>

    </main>

    <script>
        const bankSoals = {{ \Illuminate\Support\Js::from($bankSoals) }};
        const jabatanBankSoals = {{ \Illuminate\Support\Js::from($jabatanBankSoals) }};
        const tipeSoals = {{ \Illuminate\Support\Js::from($tipeSoals) }};
        const konfigurasiLama = {{ \Illuminate\Support\Js::from(old('konfigurasi', [])) }};
        const bankLama = {{ \Illuminate\Support\Js::from(old('bank_uploads', [])) }};
        const departemenSelect = document.getElementById('departemen_id');
        const jabatanSelect = document.getElementById('jabatan_id');
        const pilihanSoalSelect = document.getElementById('pilihan-soal');
        const tambahSoalButton = document.getElementById('tambah-soal');
        const bankContainer = document.getElementById('bank-soal-container');
        const konfigurasiContainer = document.getElementById('konfigurasi-soal-container');
        const bankSoalInfo = document.getElementById('bank-soal-info');
        const pesertaInfo = document.getElementById('peserta-info');
        const bankSumberTerpilih = new Map(
            Object.entries(bankLama ?? {}).map(([level, bankId]) => [String(level), String(bankId)])
        );
        const pilihanDitambahkan = [];

        (Array.isArray(konfigurasiLama) ? konfigurasiLama : []).forEach((konfigurasi) => {
            const level = Number(konfigurasi.level_soal);
            const tipeProsedur = konfigurasi.tipe_prosedur;
            const key = `${level}|${tipeProsedur}`;
            let pilihan = pilihanDitambahkan.find((item) => item.key === key);

            if (!pilihan) {
                pilihan = { level, tipeProsedur, key, jumlahSoal: {} };
                pilihanDitambahkan.push(pilihan);
            }

            pilihan.jumlahSoal[konfigurasi.tipe_soal] = Number(konfigurasi.jumlah_soal);
        });

        function updateDepartmentParticipants() {
            const departemenId = departemenSelect.value;
            const jabatanTujuan = jabatanBankSoals.find((jabatan) => String(jabatan.id) === jabatanSelect.value);
            let jumlahPeserta = 0;

            document.querySelectorAll('[data-karyawan-row]').forEach((row) => {
                let isEligible = departemenId !== ''
                    && row.dataset.departemenId === departemenId
                    && Boolean(jabatanTujuan);

                if (isEligible && jabatanTujuan.level === 1) {
                    if (jabatanTujuan.jenis === 'struktural') {
                        isEligible = jabatanTujuan.urutan_struktural === 1
                            ? row.dataset.jabatanJenis === 'operasional'
                            : row.dataset.jabatanJenis === 'struktural'
                                && Number(row.dataset.jabatanUrutanStruktural) === jabatanTujuan.urutan_struktural - 1;
                    } else {
                        isEligible = row.dataset.jabatanNama.trim().toLowerCase() === 'crew';
                    }
                } else if (isEligible) {
                    isEligible = Number(row.dataset.jabatanLevel) === jabatanTujuan.level - 1
                        && normalizeNamaJabatan(row.dataset.jabatanNama) === jabatanTujuan.nama_dasar;
                }

                row.hidden = !isEligible;
                jumlahPeserta += isEligible ? 1 : 0;
            });

            pesertaInfo.textContent = !jabatanTujuan || departemenId === ''
                ? 'Pilih departemen dan jabatan tujuan untuk melihat peserta yang akan ditugaskan.'
                : jumlahPeserta > 0
                    ? `${jumlahPeserta} karyawan akan otomatis ditugaskan ke assessment ini.`
                    : 'Belum ada karyawan yang sesuai dengan departemen dan jabatan tujuan.';
        }

        function normalizeNamaJabatan(namaJabatan) {
            return namaJabatan
                .trim()
                .replace(/\s+(?:level|lev|lv)\s*\.?\s*\d+\s*$/i, '')
                .trim()
                .toLowerCase();
        }

        function labelPilihanSoal(level, tipeProsedur) {
            const jabatanTujuan = jabatanBankSoals.find((jabatan) => String(jabatan.id) === jabatanSelect.value);

            if (jabatanTujuan?.jenis === 'struktural') {
                return {
                    peraturan_perusahaan: 'Peraturan Perusahaan',
                    keselamatan_kerja: 'K3 / Keselamatan Kerja',
                    pengoperasian_jabatan: 'Prosedur Jabatan',
                }[tipeProsedur];
            }

            if (tipeProsedur === 'peraturan_perusahaan') {
                return 'Peraturan Perusahaan / SOP Level 1';
            }

            if (tipeProsedur === 'keselamatan_kerja') {
                return 'K3 / Keselamatan Kerja / SOP Level 1';
            }

            return `Prosedur Pengoperasian Jabatan / SOP Level ${level}`;
        }

        function jabatanPadaLevel(jabatanTujuan, level) {
            if (jabatanTujuan.jenis === 'struktural') {
                return level === 1 ? jabatanTujuan : null;
            }

            return jabatanBankSoals.find((jabatan) =>
                jabatan.nama_dasar === jabatanTujuan.nama_dasar && jabatan.level === level
            );
        }

        function levelDitambahkan(level) {
            return pilihanDitambahkan.some((item) => item.level === level);
        }

        function renderPilihanSoal() {
            pilihanSoalSelect.replaceChildren();
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = 'Pilih peraturan, K3, atau SOP';
            pilihanSoalSelect.append(placeholder);

            const jabatanTujuan = jabatanBankSoals.find((jabatan) => String(jabatan.id) === jabatanSelect.value);
            const siapPilih = Boolean(jabatanTujuan && departemenSelect.value);

            if (siapPilih) {
                for (let level = 1; level <= jabatanTujuan.level; level++) {
                    if (!jabatanPadaLevel(jabatanTujuan, level)) {
                        continue;
                    }

                    const tipeProsedurLevel = level === 1
                        ? ['peraturan_perusahaan', 'keselamatan_kerja', 'pengoperasian_jabatan']
                        : ['pengoperasian_jabatan'];

                    tipeProsedurLevel.forEach((tipeProsedur) => {
                        const key = `${level}|${tipeProsedur}`;
                        if (pilihanDitambahkan.some((item) => item.key === key)) {
                            return;
                        }

                        const option = document.createElement('option');
                        option.value = key;
                        option.textContent = labelPilihanSoal(level, tipeProsedur);
                        pilihanSoalSelect.append(option);
                    });
                }
            }

            pilihanSoalSelect.disabled = !siapPilih || pilihanSoalSelect.options.length === 1;
            tambahSoalButton.disabled = pilihanSoalSelect.disabled || !pilihanSoalSelect.value;
        }

        function renderBankSoal() {
            bankContainer.replaceChildren();
            const levels = [...new Set(pilihanDitambahkan.map((item) => item.level))]
                .sort((a, b) => a - b);
            const jabatanTujuan = jabatanBankSoals.find((jabatan) => String(jabatan.id) === jabatanSelect.value);

            if (levels.length === 0) {
                bankSoalInfo.textContent = '';
                renderKonfigurasiSoal();
                return;
            }

            const bankPerLevel = new Map();

            levels.forEach((level) => {
                const jabatanLevel = jabatanTujuan ? jabatanPadaLevel(jabatanTujuan, level) : null;
                const bankTersedia = bankSoals.filter((bank) =>
                    jabatanLevel
                    && bank.jabatan_id === jabatanLevel.id
                    && String(bank.departemen_id) === departemenSelect.value
                );

                const field = document.createElement('div');
                field.className = 'rounded-xl border border-gray-200 bg-[#fbfdfb] p-4';

                const label = document.createElement('label');
                label.htmlFor = `bank-upload-${level}`;
                label.className = 'mb-2 block text-sm font-medium text-gray-700';
                label.textContent = jabatanTujuan?.jenis === 'struktural'
                    ? `Bank Soal Jabatan — ${jabatanLevel ? jabatanLevel.nama : ''}`
                    : `Bank Soal Level ${level}${jabatanLevel ? ` — ${jabatanLevel.nama}` : ''}`;

                const select = document.createElement('select');
                select.id = `bank-upload-${level}`;
                select.name = `bank_uploads[${level}]`;
                select.required = true;
                select.className = 'w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-[#4a7c59] focus:outline-none';

                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = bankTersedia.length > 0
                    ? 'Pilih file bank soal'
                    : 'Belum ada bank soal di departemen ini';
                select.append(placeholder);

                bankTersedia.forEach((bank) => {
                    const option = document.createElement('option');
                    option.value = bank.id;
                    option.textContent = `${bank.nama_file} (${bank.jumlah_soal} soal aktif)`;
                    option.selected = bankSumberTerpilih.get(String(level)) === String(bank.id);
                    select.append(option);
                });

                field.append(label, select);
                bankContainer.append(field);
                if (select.value) {
                    bankPerLevel.set(level, bankSoals.find((bank) => String(bank.id) === select.value));
                }
                select.addEventListener('change', () => {
                    if (select.value) {
                        bankSumberTerpilih.set(String(level), select.value);
                    } else {
                        bankSumberTerpilih.delete(String(level));
                    }
                    renderKonfigurasiSoal();
                });
            });

            const levelTanpaBank = levels.filter((level) => !bankPerLevel.has(level));
            bankSoalInfo.textContent = jabatanTujuan?.jenis === 'struktural'
                ? (levelTanpaBank.length === 0
                    ? 'Bank soal jabatan struktural sudah dipilih.'
                    : 'Pilih file bank soal untuk jabatan struktural ini.')
                : (levelTanpaBank.length === 0
                    ? `Bank dipilih untuk Level/SOP ${levels.join(', ')}.`
                    : `Pilih file bank soal untuk Level/SOP ${levelTanpaBank.join(', ')}.`);

            renderKonfigurasiSoal();
        }

        function renderKonfigurasiSoal() {
            konfigurasiContainer.replaceChildren();
            const bankPerLevel = new Map(
                Array.from(bankContainer.querySelectorAll('select[name^="bank_uploads["]'))
                    .map((select) => [
                        Number(select.name.match(/\[(\d+)\]/)?.[1]),
                        bankSoals.find((bank) => String(bank.id) === select.value),
                    ])
                    .filter(([, bank]) => Boolean(bank))
            );

            if (pilihanDitambahkan.length === 0) {
                const message = document.createElement('p');
                message.className = 'text-sm text-gray-500';
                message.textContent = 'Belum ada kategori soal yang ditambahkan.';
                konfigurasiContainer.append(message);
                return;
            }

            const levels = [...new Set(pilihanDitambahkan.map((item) => item.level))];
            levels.forEach((level) => {
                const levelInput = document.createElement('input');
                levelInput.type = 'hidden';
                levelInput.name = 'soal_levels[]';
                levelInput.value = String(level);
                konfigurasiContainer.append(levelInput);
            });

            let index = 0;
            pilihanDitambahkan.forEach((item, pilihanIndex) => {
                const card = document.createElement('section');
                card.className = 'rounded-lg border border-gray-200 p-4 md:col-span-3';

                const header = document.createElement('div');
                header.className = 'mb-4 flex items-center justify-between gap-3';

                const heading = document.createElement('h3');
                heading.className = 'font-semibold text-gray-800';
                heading.textContent = `${pilihanIndex + 1}. ${labelPilihanSoal(item.level, item.tipeProsedur)}`;

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className = 'text-sm font-medium text-red-600 hover:text-red-800';
                removeButton.textContent = 'Hapus';
                removeButton.addEventListener('click', () => {
                    pilihanDitambahkan.splice(pilihanIndex, 1);
                    if (!levelDitambahkan(item.level)) {
                        bankSumberTerpilih.delete(String(item.level));
                    }
                    renderPilihanSoal();
                    renderBankSoal();
                });

                header.append(heading, removeButton);
                card.append(header);

                const prosedurLevelInput = document.createElement('input');
                prosedurLevelInput.type = 'hidden';
                prosedurLevelInput.name = `prosedur_levels[${item.level}][]`;
                prosedurLevelInput.value = item.tipeProsedur;
                card.append(prosedurLevelInput);

                const jumlahContainer = document.createElement('div');
                jumlahContainer.className = 'grid grid-cols-1 sm:grid-cols-3 gap-4';

                Object.entries(tipeSoals).forEach(([tipeSoal, labelSoal]) => {
                    const tersedia = bankPerLevel.get(item.level)
                        ?.jumlah_tersedia[item.tipeProsedur][tipeSoal] ?? 0;
                    const field = document.createElement('div');

                    const levelInput = document.createElement('input');
                    levelInput.type = 'hidden';
                    levelInput.name = `konfigurasi[${index}][level_soal]`;
                    levelInput.value = item.level;

                    const tipeProsedurInput = document.createElement('input');
                    tipeProsedurInput.type = 'hidden';
                    tipeProsedurInput.name = `konfigurasi[${index}][tipe_prosedur]`;
                    tipeProsedurInput.value = item.tipeProsedur;

                    const tipeSoalInput = document.createElement('input');
                    tipeSoalInput.type = 'hidden';
                    tipeSoalInput.name = `konfigurasi[${index}][tipe_soal]`;
                    tipeSoalInput.value = tipeSoal;

                    const label = document.createElement('label');
                    label.htmlFor = `jumlah-soal-${index}`;
                    label.className = 'mb-1 block text-sm text-gray-700';
                    label.textContent = `${labelSoal} (tersedia ${tersedia})`;

                    const jumlahInput = document.createElement('input');
                    jumlahInput.id = `jumlah-soal-${index}`;
                    jumlahInput.type = 'number';
                    jumlahInput.name = `konfigurasi[${index}][jumlah_soal]`;
                    jumlahInput.min = '0';
                    jumlahInput.max = String(tersedia);
                    jumlahInput.required = true;
                    jumlahInput.value = item.jumlahSoal[tipeSoal] ?? 0;
                    jumlahInput.className = 'w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-[#4a7c59] focus:outline-none';
                    jumlahInput.addEventListener('input', () => {
                        item.jumlahSoal[tipeSoal] = Number(jumlahInput.value);
                    });

                    field.append(levelInput, tipeProsedurInput, tipeSoalInput, label, jumlahInput);
                    jumlahContainer.append(field);
                    index++;
                });

                card.append(jumlahContainer);
                konfigurasiContainer.append(card);
            });
        }

        function tambahPilihanSoal() {
            const [levelValue, tipeProsedur] = pilihanSoalSelect.value.split('|');
            const level = Number(levelValue);
            const key = `${level}|${tipeProsedur}`;

            if (!level || !tipeProsedur || pilihanDitambahkan.some((item) => item.key === key)) {
                return;
            }

            const nilaiLama = (Array.isArray(konfigurasiLama) ? konfigurasiLama : []).filter((konfigurasi) =>
                Number(konfigurasi.level_soal) === level
                && konfigurasi.tipe_prosedur === tipeProsedur
            );

            pilihanDitambahkan.push({
                level,
                tipeProsedur,
                key,
                jumlahSoal: Object.fromEntries(
                    nilaiLama.map((konfigurasi) => [konfigurasi.tipe_soal, Number(konfigurasi.jumlah_soal)])
                ),
            });

            pilihanSoalSelect.value = '';
            renderPilihanSoal();
            renderBankSoal();
        }

        departemenSelect.addEventListener('change', () => {
            updateDepartmentParticipants();
            bankSumberTerpilih.clear();
            renderPilihanSoal();
            renderBankSoal();
        });
        jabatanSelect.addEventListener('change', () => {
            updateDepartmentParticipants();
            bankSumberTerpilih.clear();
            pilihanDitambahkan.splice(0, pilihanDitambahkan.length);
            renderPilihanSoal();
            renderBankSoal();
        });
        pilihanSoalSelect.addEventListener('change', () => {
            tambahSoalButton.disabled = !pilihanSoalSelect.value;
        });
        tambahSoalButton.addEventListener('click', tambahPilihanSoal);
        updateDepartmentParticipants();
        renderPilihanSoal();
        renderBankSoal();
    </script>
@endsection
