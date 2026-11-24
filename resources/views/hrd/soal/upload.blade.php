
@extends('layouts.hrd')

@section('title', 'Upload Bank Soal')
@section('page_title', 'Upload Bank Soal')

@section('content')

<div class="w-full max-w-7xl mx-auto">

    {{-- HEADER HALAMAN --}}
    <div class="flex flex-col sm:flex-row sm:items-center
                sm:justify-between gap-4 mb-5">

        <div>
            <h1 class="text-2xl font-bold text-[#304c3f]">
                Upload Bank Soal
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Unggah file Excel bank soal berdasarkan departemen dan jabatan.
            </p>
        </div>

        <a href="{{ route('hrd.soal.index') }}"
           class="inline-flex items-center justify-center gap-2
                  px-4 py-2.5 rounded-lg bg-white
                  border border-[#dfe5df] text-[#304c3f]
                  hover:bg-[#f4f6f3] text-sm font-semibold
                  transition-colors">

            <svg class="w-4 h-4" fill="none"
                 stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path d="M15 18l-6-6 6-6"/>
            </svg>

            Kembali ke Bank Soal
        </a>

    </div>

    {{-- PILIH DEPARTEMEN --}}
    @if (!$departemen)

        <div class="bg-white border border-[#e0e5df]
                    rounded-xl overflow-hidden">

            <div class="px-5 py-4 bg-[#f4f6f3]
                        border-b border-[#e0e5df]">

                <h2 class="text-sm font-bold text-[#304c3f]">
                    Pilih Departemen
                </h2>

                <p class="text-xs text-gray-500 mt-1">
                    Pilih departemen yang akan digunakan untuk mengunggah bank soal.
                </p>

            </div>

            <div class="p-5">

                <div class="grid grid-cols-1 sm:grid-cols-2
                            xl:grid-cols-3 gap-4">

                    @forelse ($departemens as $folderDepartemen)

                        <a href="{{ route('hrd.soal.upload', [
                                'departemen_id' => $folderDepartemen->id
                            ]) }}"
                           class="group flex items-center gap-4
                                  p-4 bg-white border border-[#e0e5df]
                                  rounded-xl hover:border-[#4c7b58]
                                  hover:bg-[#f8faf7]
                                  transition-colors">

                            <div class="w-12 h-12 flex-shrink-0
                                        bg-[#edf3ee] rounded-lg
                                        flex items-center justify-center
                                        group-hover:bg-[#e2ebe4]">

                                <svg class="w-6 h-6 text-[#4c7b58]"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="1.8"
                                     viewBox="0 0 24 24">

                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          d="M3 7a2 2 0 012-2h5l2 2h7
                                             a2 2 0 012 2v10
                                             a2 2 0 01-2 2H5
                                             a2 2 0 01-2-2V7z"/>

                                </svg>

                            </div>

                            <div class="flex-1 min-w-0">

                                <h3 class="text-sm font-bold
                                           text-[#304c3f] truncate">
                                    {{ $folderDepartemen->nama_departemen }}
                                </h3>

                                <p class="text-xs text-gray-500 mt-1">
                                    Buka folder departemen
                                </p>

                            </div>

                            <svg class="w-4 h-4 text-gray-400
                                        group-hover:text-[#4c7b58]"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="2"
                                 viewBox="0 0 24 24">
                                <path d="M9 18l6-6-6-6"/>
                            </svg>

                        </a>

                    @empty

                        <div class="sm:col-span-2 xl:col-span-3
                                    text-center py-12">

                            <div class="w-14 h-14 mx-auto
                                        bg-[#f4f6f3] rounded-xl
                                        flex items-center justify-center mb-3">

                                <svg class="w-7 h-7 text-gray-400"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="1.5"
                                     viewBox="0 0 24 24">
                                    <path d="M3 7a2 2 0 012-2h5l2 2h7a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
                                </svg>

                            </div>

                            <p class="text-sm font-semibold text-gray-600">
                                Belum ada departemen
                            </p>

                            <p class="text-xs text-gray-500 mt-1">
                                Tambahkan departemen terlebih dahulu.
                            </p>

                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    @else

        {{-- DEPARTEMEN TERPILIH --}}
        <div class="bg-white border border-[#e0e5df]
                    rounded-xl mb-5 overflow-hidden">

            <div class="flex flex-col sm:flex-row
                        sm:items-center sm:justify-between
                        gap-3 px-5 py-4">

                <div class="flex items-center gap-3">

                    <div class="w-10 h-10 bg-[#edf3ee]
                                rounded-lg flex items-center
                                justify-center flex-shrink-0">

                        <svg class="w-5 h-5 text-[#4c7b58]"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="1.8"
                             viewBox="0 0 24 24">
                            <path d="M3 7a2 2 0 012-2h5l2 2h7a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
                        </svg>

                    </div>

                    <div>
                        <p class="text-xs text-gray-500">
                            Departemen Terpilih
                        </p>

                        <h2 class="text-base font-bold text-[#304c3f]">
                            {{ $departemen->nama_departemen }}
                        </h2>
                    </div>

                </div>

                <a href="{{ route('hrd.soal.upload') }}"
                   class="inline-flex items-center justify-center
                          px-4 py-2 rounded-lg border
                          border-[#dfe5df] text-[#304c3f]
                          hover:bg-[#f4f6f3]
                          text-xs font-semibold transition">

                    Ganti Departemen

                </a>

            </div>

        </div>


        {{-- VALIDASI --}}
        @if ($errors->any())

            <div class="bg-red-50 border border-red-200
                        rounded-lg px-5 py-4 mb-4">

                <p class="text-sm font-bold text-red-700 mb-2">
                    Terjadi kesalahan:
                </p>

                <ul class="list-disc list-inside space-y-1
                           text-sm text-red-700">

                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                </ul>

            </div>

        @endif


        {{-- ERROR PER BARIS --}}
        @if (session('upload_errors'))

            <div class="bg-amber-50 border border-amber-200
                        rounded-lg px-5 py-4 mb-4">

                <p class="text-sm font-bold text-amber-800 mb-2">
                    Detail kesalahan pada file Excel:
                </p>

                <ul class="list-disc list-inside space-y-1
                           text-sm text-amber-800">

                    @foreach (session('upload_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach

                </ul>

            </div>

        @endif


        {{-- FORM UPLOAD --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

            {{-- PANEL KIRI --}}
            <div class="xl:col-span-2">

                <div class="bg-white border border-[#e0e5df]
                            rounded-xl overflow-hidden">

                    <div class="px-5 py-4 bg-[#f4f6f3]
                                border-b border-[#e0e5df]">

                        <h2 class="text-sm font-bold text-[#304c3f]">
                            Form Upload Bank Soal
                        </h2>

                        <p class="text-xs text-gray-500 mt-1">
                            Pilih jabatan tujuan dan unggah file Excel sesuai level.
                        </p>

                    </div>

                    <div class="p-5">

                        @if ($jabatans->isEmpty())

                            <div class="bg-amber-50 border
                                        border-amber-200 rounded-lg p-4
                                        text-sm text-amber-800">

                                Belum ada jabatan operasional level 1–5
                                atau jabatan struktural yang valid
                                untuk pilihan bank soal.

                            </div>

                        @else

                            <form method="POST"
                                  action="{{ route('hrd.soal.upload.store') }}"
                                  enctype="multipart/form-data">

                                @csrf

                                <input type="hidden"
                                       name="departemen_id"
                                       value="{{ $departemen->id }}">

                                {{-- JABATAN --}}
                                <div class="mb-5">

                                    <label for="jabatan_id"
                                           class="block text-sm font-semibold
                                                  text-[#304c3f] mb-2">

                                        Jabatan Tujuan
                                        <span class="text-red-600">*</span>

                                    </label>

                                    <select id="jabatan_id"
                                            name="jabatan_id"
                                            required
                                            class="w-full rounded-lg
                                                   border border-[#d4ddd5]
                                                   bg-white px-3 py-3
                                                   text-sm text-gray-700
                                                   outline-none
                                                   focus:border-[#4c7b58]
                                                   focus:ring-2
                                                   focus:ring-[#e0ebe2]">

                                        <option value="">
                                            -- Pilih Jabatan --
                                        </option>

                                        @foreach ($jabatans as $jabatan)

                                            <option value="{{ $jabatan->id }}"
                                                {{ old('jabatan_id') == $jabatan->id
                                                    ? 'selected'
                                                    : '' }}>

                                                {{ $jabatan->nama_jabatan }}
                                                {{ $jabatan->tipe_jabatan === 'struktural'
                                                    ? '(Jabatan Struktural)'
                                                    : '(Level '.$jabatan->level_jabatan.')' }}

                                            </option>

                                        @endforeach

                                    </select>

                                </div>


                                {{-- AREA FILE DINAMIS --}}
                                <div class="border-t border-[#e0e5df] pt-5">

                                    <h3 class="text-sm font-bold text-[#304c3f] mb-1">
                                        File Excel Bank Soal
                                    </h3>

                                    <p class="text-xs text-gray-500 mb-4">
                                        File yang diperlukan akan muncul setelah
                                        memilih jabatan tujuan.
                                    </p>

                                    <div id="file-level-container"
                                         aria-live="polite"
                                         class="space-y-3"></div>

                                    <div id="file-level-help"
                                         class="rounded-lg bg-[#f4f6f3]
                                                border border-[#e0e5df]
                                                px-4 py-3 text-xs
                                                text-gray-600 mt-3">

                                        Pilih jabatan tujuan untuk
                                        menampilkan file yang harus diupload.

                                    </div>

                                </div>


                                {{-- ACTION --}}
                                <div class="flex flex-wrap items-center
                                            justify-end gap-3
                                            border-t border-[#e0e5df]
                                            pt-5 mt-5">

                                    <a href="{{ route('hrd.soal.index') }}"
                                        class="inline-flex items-center justify-center
                                                px-5 py-2.5 rounded-lg
                                                bg-white border border-red-300
                                                text-red-600 hover:bg-red-50
                                                hover:border-red-400
                                                text-sm font-semibold transition-colors">
                                            Batal
                                    </a>

                                    <button id="submit-upload"
                                            type="submit"
                                            class="inline-flex items-center
                                                   justify-center gap-2
                                                   px-5 py-2.5
                                                   bg-[#4c7b58]
                                                   hover:bg-[#3e6749]
                                                   disabled:bg-gray-300
                                                   disabled:cursor-not-allowed
                                                   text-white rounded-lg
                                                   text-sm font-semibold
                                                   transition-colors">

                                        <svg class="w-4 h-4"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="2"
                                             viewBox="0 0 24 24">
                                            <path d="M12 16V4m0 0-4 4m4-4 4 4M4 16v4h16v-4"/>
                                        </svg>

                                        Upload Soal

                                    </button>

                                </div>

                            </form>

                        @endif

                    </div>

                </div>

            </div>


            {{-- PANEL KANAN: PANDUAN --}}
            <div class="xl:col-span-1">

                <div class="bg-white border border-[#e0e5df]
                            rounded-xl overflow-hidden">

                    <div class="px-5 py-4 bg-[#f4f6f3]
                                border-b border-[#e0e5df]">

                        <h2 class="text-sm font-bold text-[#304c3f]">
                            Panduan Format Excel
                        </h2>

                        <p class="text-xs text-gray-500 mt-1">
                            Ketentuan file sebelum diunggah.
                        </p>

                    </div>

                    <div class="p-5 space-y-5">

                        {{-- FORMAT KOLOM --}}
                        <div>

                            <h3 class="text-xs font-bold
                                       uppercase tracking-wide
                                       text-[#304c3f] mb-2">

                                Kolom Excel

                            </h3>

                            <div class="bg-[#f8faf7]
                                        border border-[#e0e5df]
                                        rounded-lg p-3">

                                <p class="text-xs text-gray-700 leading-6
                                          break-words">

                                    nomor · prosedur pengoperasian ·
                                    nilai point · pertanyaan ·
                                    a · b · c · d · e · jawaban benar

                                </p>

                            </div>

                        </div>


                        {{-- KETERANGAN --}}
                        <div>

                            <h3 class="text-xs font-bold
                                       uppercase tracking-wide
                                       text-[#304c3f] mb-3">

                                Ketentuan Jawaban

                            </h3>

                            <ul class="space-y-3 text-xs
                                       text-gray-600 leading-5">

                                <li>
                                    <strong class="text-gray-700">
                                        Pilihan Ganda:
                                    </strong>
                                    satu huruf, misalnya A.
                                </li>

                                <li>
                                    <strong class="text-gray-700">
                                        Multi Jawaban:
                                    </strong>
                                    beberapa huruf, misalnya AB,
                                    ACD, atau ABDE.
                                </li>

                                <li>
                                    <strong class="text-gray-700">
                                        Benar/Salah:
                                    </strong>
                                    gunakan 1 untuk benar,
                                    0 untuk salah.
                                </li>

                                <li>
                                    Kolom <strong>e</strong> opsional.
                                    Pilihan dapat A–D atau A–E.
                                </li>

                                <li>
                                    Kolom <strong>a–e</strong> tidak perlu
                                    diisi untuk soal Benar/Salah.
                                </li>

                                <li>
                                    <strong>Nilai point</strong>
                                    berupa angka, misalnya
                                    1 atau 0.5.
                                </li>

                                <li>
                                    <strong>Prosedur pengoperasian</strong>
                                    berupa teks nama prosedur.
                                </li>

                            </ul>

                        </div>


                        {{-- SOP --}}
                        <div class="border-t border-[#e0e5df] pt-4">

                            <h3 class="text-xs font-bold
                                       uppercase tracking-wide
                                       text-[#304c3f] mb-3">

                                SOP Jumlah Soal

                            </h3>

                            <div class="space-y-2">

                                <div class="flex items-center
                                            justify-between gap-2
                                            bg-[#f4f6f3] rounded-lg
                                            px-3 py-2">

                                    <span class="text-xs text-gray-600">
                                        Level 1
                                    </span>

                                    <span class="text-xs font-bold
                                                 text-[#304c3f]">
                                        150 Soal
                                    </span>

                                </div>

                                <div class="flex items-center
                                            justify-between gap-2
                                            bg-[#f4f6f3] rounded-lg
                                            px-3 py-2">

                                    <span class="text-xs text-gray-600">
                                        Level 2–5
                                    </span>

                                    <span class="text-xs font-bold
                                                 text-[#304c3f]">
                                        50 Soal / Level
                                    </span>

                                </div>

                            </div>

                        </div>


                        {{-- INFORMASI --}}
                        <div class="border-t border-[#e0e5df] pt-4">

                            <p class="text-xs text-gray-600 leading-5">
                                Untuk jabatan operasional, unggah
                                satu file Excel setiap level dari
                                Level 1 hingga level tujuan.
                            </p>

                            <p class="text-xs text-gray-600 leading-5 mt-3">
                                Untuk jabatan struktural, unggah
                                satu file Excel khusus jabatan tersebut.
                            </p>

                            <p class="text-xs font-medium
                                      text-[#4c7b58] mt-3">
                                Bank soal yang telah diunggah
                                sebelumnya tetap ditambahkan.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    @endif

</div>


{{-- JAVASCRIPT PEMILIHAN FILE --}}
@if ($departemen && $jabatans->isNotEmpty())

<script>
    const bankJabatans = {{ \Illuminate\Support\Js::from($bankJabatans) }};

    const jabatanSelect = document.getElementById('jabatan_id');
    const fileLevelContainer = document.getElementById('file-level-container');
    const fileLevelHelp = document.getElementById('file-level-help');
    const submitUpload = document.getElementById('submit-upload');

    function renderFileLevels() {
        fileLevelContainer.replaceChildren();
        submitUpload.disabled = false;

        const jabatanTujuan = bankJabatans.find(
            (jabatan) => String(jabatan.id) === jabatanSelect.value
        );

        if (!jabatanTujuan) {
            fileLevelHelp.textContent =
                'Pilih jabatan tujuan untuk menampilkan file yang harus diupload.';
            return;
        }

        const isStruktural = jabatanTujuan.tipe === 'struktural';

        fileLevelHelp.textContent = isStruktural
            ? 'Upload satu file Excel khusus untuk jabatan struktural ini. File maksimal 5MB.'
            : `Upload satu file Excel untuk setiap level 1 sampai ${jabatanTujuan.level}. Setiap file maksimal 5MB.`;

        for (let level = 1; level <= jabatanTujuan.level; level++) {

            const jabatanLevelMatches = bankJabatans.filter(
                (jabatan) =>
                    jabatan.nama_dasar === jabatanTujuan.nama_dasar &&
                    jabatan.level === level
            );

            if (jabatanLevelMatches.length !== 1) {

                const error = document.createElement('p');
                error.className =
                    'text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg p-3';

                error.textContent =
                    `Data jabatan untuk level ${level} tidak ditemukan atau tidak unik. Periksa data jabatan terlebih dahulu.`;

                fileLevelContainer.append(error);
                submitUpload.disabled = true;
                continue;
            }

            const jabatanLevel = jabatanLevelMatches[0];

            const wrapper = document.createElement('div');
            wrapper.className =
                'bg-[#fafbf9] border border-[#e0e5df] rounded-lg p-4';

            const label = document.createElement('label');

            label.htmlFor = `file_excel_${level}`;
            label.className =
                'block text-sm font-semibold text-[#304c3f] mb-2';

            label.textContent = isStruktural
                ? `File Excel Jabatan — ${jabatanLevel.nama}`
                : `File Excel Level ${level} — ${jabatanLevel.nama} (maks. ${level === 1 ? 150 : 50} soal)`;

            const input = document.createElement('input');

            input.id = `file_excel_${level}`;
            input.name = `file_excel[${level}]`;
            input.type = 'file';
            input.accept = '.xlsx,.xls';
            input.required = true;

            input.className =
                'block w-full text-sm text-gray-600 ' +
                'file:mr-4 file:rounded-md file:border-0 ' +
                'file:bg-[#e8eee9] file:px-4 file:py-2 ' +
                'file:text-xs file:font-semibold ' +
                'file:text-[#304c3f] ' +
                'hover:file:bg-[#dce8df] ' +
                'cursor-pointer';

            wrapper.append(label, input);
            fileLevelContainer.append(wrapper);
        }
    }

    jabatanSelect.addEventListener('change', renderFileLevels);
    renderFileLevels();
</script>

@endif

@endsection
