@extends('layouts.hrd')
@section('title', 'Upload Bank Soal')
@section('page_title', 'Upload Bank Soal')
@section('content')

<div class="card" style="max-width:800px;">

    <h1>Upload Bank Soal dari Excel</h1>

    @if(!$departemen)
        <p style="color:#6b7280;margin-top:0;">
            Pilih folder departemen untuk mulai mengupload bank soal.
        </p>

        <a href="{{ route('hrd.soal.index') }}" class="btn" style="background:#6b7280;color:white;margin-bottom:20px;display:inline-block;">
            ← Kembali ke Bank Soal
        </a>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:15px;">
            @forelse($departemens as $folderDepartemen)
                <a
                    href="{{ route('hrd.soal.upload', ['departemen_id' => $folderDepartemen->id]) }}"
                    style="display:block;padding:20px;border:1px solid #d1d5db;border-radius:8px;background:#f9fafb;color:#111827;text-decoration:none;"
                >
                    <strong style="display:block;font-size:16px;margin-bottom:6px;">
                        📁 {{ $folderDepartemen->nama_departemen }}
                    </strong>
                    <span style="color:#6b7280;font-size:13px;">
                        Buka folder departemen
                    </span>
                </a>
            @empty
                <p style="color:#6b7280;">Belum ada departemen. Tambahkan departemen terlebih dahulu.</p>
            @endforelse
        </div>
    @else
        <a href="{{ route('hrd.soal.upload') }}" class="btn" style="background:#6b7280;color:white;margin-bottom:20px;display:inline-block;">
            ← Kembali ke Folder Departemen
        </a>

        <h2 style="margin-top:0;">Folder: {{ $departemen->nama_departemen }}</h2>

        @if($errors->any())
            <div style="background:#fee2e2;color:#991b1b;padding:15px;border-radius:8px;margin-bottom:20px;">
                <strong>Terjadi kesalahan:</strong>
                <ul style="margin:8px 0 0 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('upload_errors'))
            <div style="background:#fef3c7;color:#92400e;padding:15px;border-radius:8px;margin-bottom:20px;">
                <strong>Detail error per baris:</strong>
                <ul style="margin:8px 0 0 20px;">
                    @foreach(session('upload_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="background:#eff6ff;color:#1e40af;padding:15px;border-radius:8px;margin-bottom:25px;font-size:14px;">
        <strong>Format Kolom Excel:</strong><br>
        <code style="background:#dbeafe;padding:2px 6px;border-radius:3px;">
            nomor | prosedur pengoperasian | nilai point | pertanyaan | a | b | c | d | e | jawaban benar
        </code>
        <br><br>
        <strong>Keterangan:</strong>
        <ul style="margin:8px 0 0 20px;">
            <li>Kolom <strong>e</strong> opsional (A–D atau A–E)</li>
            <li><strong>prosedur pengoperasian</strong>: teks bebas nama prosedur</li>
            <li><strong>nilai point</strong>: angka (contoh: 1 atau 0.5)</li>
            <li><strong>jawaban benar</strong>: A (pilihan ganda), AB / ACD / ABDE (multi jawaban), atau <strong>1</strong> (benar) / <strong>0</strong> (salah) untuk tipe benar/salah</li>
            <li>Tipe soal otomatis: nilai 1/0 = benar/salah &nbsp;|&nbsp; 1 huruf = pilihan ganda &nbsp;|&nbsp; lebih dari 1 huruf = multi jawaban</li>
            <li>Kolom a–e tidak perlu diisi untuk soal benar/salah</li>
        </ul>
        <br>
        <strong>SOP Jumlah Soal:</strong> Level 1 = 150 soal &nbsp;|&nbsp; Level 2–5 = 50 soal per level.
        <br>
        Pilih jabatan tujuan, lalu upload satu file Excel untuk setiap level operasional dari Level 1 sampai level tujuan. Jabatan struktural menggunakan satu file bank soal khusus untuk jabatan tersebut. Bank yang sudah pernah diupload tetap ditambahkan.
        </div>

        @if($jabatans->isEmpty())
            <div style="background:#fef3c7;color:#92400e;padding:15px;border-radius:8px;">
                Belum ada jabatan operasional level 1–5 atau jabatan struktural yang valid untuk pilihan bank soal.
            </div>
        @else

        <form method="POST" action="{{ route('hrd.soal.upload.store') }}" enctype="multipart/form-data">

            @csrf
            <input type="hidden" name="departemen_id" value="{{ $departemen->id }}">

            <div style="margin-bottom:20px;">
                <label for="jabatan_id" style="display:block;margin-bottom:7px;font-weight:bold;">Jabatan Tujuan</label>
                <select id="jabatan_id" name="jabatan_id" required style="width:100%;padding:11px;border:1px solid #d1d5db;border-radius:6px;">
                    <option value="">-- Pilih Jabatan --</option>
                    @foreach($jabatans as $jabatan)
                        <option value="{{ $jabatan->id }}" {{ old('jabatan_id') == $jabatan->id ? 'selected' : '' }}>
                            {{ $jabatan->nama_jabatan }}{{ $jabatan->tipe_jabatan === 'struktural' ? ' (Jabatan Struktural)' : ' (Level '.$jabatan->level_jabatan.')' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="file-level-container" aria-live="polite" style="margin-bottom:20px;"></div>

            <div id="file-level-help" style="color:#6b7280;font-size:13px;margin-bottom:20px;">
                Pilih jabatan tujuan untuk menampilkan file yang harus diupload.
            </div>

            <button id="submit-upload" type="submit" class="btn" style="background:#16a34a;color:white;">
                ↑ Upload Soal
            </button>

        </form>
        @endif
    @endif

</div>

@if($departemen && $jabatans->isNotEmpty())
<script>
    const bankJabatans = {{ \Illuminate\Support\Js::from($bankJabatans) }};
    const jabatanSelect = document.getElementById('jabatan_id');
    const fileLevelContainer = document.getElementById('file-level-container');
    const fileLevelHelp = document.getElementById('file-level-help');
    const submitUpload = document.getElementById('submit-upload');

    function renderFileLevels() {
        fileLevelContainer.replaceChildren();
        submitUpload.disabled = false;

        const jabatanTujuan = bankJabatans.find((jabatan) => String(jabatan.id) === jabatanSelect.value);

        if (!jabatanTujuan) {
            fileLevelHelp.textContent = 'Pilih jabatan tujuan untuk menampilkan file yang harus diupload.';
            return;
        }

        const isStruktural = jabatanTujuan.tipe === 'struktural';
        fileLevelHelp.textContent = isStruktural
            ? 'Upload satu file Excel khusus untuk jabatan struktural ini. File maksimal 5MB.'
            : `Upload satu file Excel untuk setiap level 1 sampai ${jabatanTujuan.level}. Setiap file maksimal 5MB.`;

        for (let level = 1; level <= jabatanTujuan.level; level++) {
            const jabatanLevelMatches = bankJabatans.filter((jabatan) =>
                jabatan.nama_dasar === jabatanTujuan.nama_dasar && jabatan.level === level
            );

            if (jabatanLevelMatches.length !== 1) {
                const error = document.createElement('p');
                error.style.color = '#b91c1c';
                error.textContent = `Data jabatan untuk level ${level} tidak ditemukan atau tidak unik. Periksa data jabatan terlebih dahulu.`;
                fileLevelContainer.append(error);
                submitUpload.disabled = true;
                continue;
            }

            const jabatanLevel = jabatanLevelMatches[0];
            const wrapper = document.createElement('div');
            wrapper.style.marginBottom = '16px';

            const label = document.createElement('label');
            label.htmlFor = `file_excel_${level}`;
            label.style.display = 'block';
            label.style.marginBottom = '7px';
            label.style.fontWeight = 'bold';
            label.textContent = isStruktural
                ? `File Excel Jabatan — ${jabatanLevel.nama}`
                : `File Excel Level ${level} — ${jabatanLevel.nama} (maks. ${level === 1 ? 150 : 50} soal)`;

            const input = document.createElement('input');
            input.id = `file_excel_${level}`;
            input.name = `file_excel[${level}]`;
            input.type = 'file';
            input.accept = '.xlsx,.xls';
            input.required = true;
            input.style.width = '100%';
            input.style.padding = '11px';
            input.style.border = '1px solid #d1d5db';
            input.style.borderRadius = '6px';
            input.style.background = 'white';

            wrapper.append(label, input);
            fileLevelContainer.append(wrapper);
        }
    }

    jabatanSelect.addEventListener('change', renderFileLevels);
    renderFileLevels();
</script>
@endif

@endsection
