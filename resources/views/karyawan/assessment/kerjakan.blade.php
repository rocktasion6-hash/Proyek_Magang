<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $peserta->assessment->nama_assessment }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            min-height: 100vh;
            background: #f4f6f4;
            color: #1f2937;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            scroll-behavior: smooth;
        }

        /* Fixed Top Header (Diam tidak bergerak saat scroll) */
        .ex-fixed-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 80;
            background: #ffffff;
            border-bottom: 1px solid #d5dfd8;
            border-top: 4px solid #2c4a3e;
            padding: 12px 28px;
            box-shadow: 0 4px 14px rgba(31, 41, 55, 0.08);
        }

        .ex-header-inner {
            max-width: 1680px;
            margin: 0 auto;
        }

        .ex-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .ex-title-wrap {
            min-width: 0;
            flex: 1;
        }

        .ex-title {
            margin: 0;
            font-size: 18.5px;
            font-weight: 700;
            color: #1f2937;
            line-height: 1.25;
        }

        .ex-bank-line {
            margin: 3px 0 0;
            font-size: 12.5px;
            color: #4b5563;
        }

        .ex-bank-line strong {
            color: #2c4a3e;
            font-weight: 700;
        }

        .ex-right-controls {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .ex-timer {
            background: #fef2f2;
            border: 1.5px solid #dc2626;
            border-radius: 9px;
            padding: 6px 15px;
            text-align: center;
        }

        .ex-timer-label {
            margin: 0;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #b91c1c;
        }

        .ex-timer-val {
            margin: 1px 0 0;
            font-size: 20px;
            font-weight: 800;
            color: #991b1b;
            font-variant-numeric: tabular-nums;
            line-height: 1.1;
        }

        .ex-btn-kirim-top {
            padding: 11px 22px;
            border: none;
            border-radius: 9px;
            background: #2c4a3e;
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            line-height: 1.2;
            white-space: nowrap;
            transition: background-color 0.15s ease;
        }

        .ex-btn-kirim-top:hover {
            background: #3d6454;
        }

        .ex-meta-strip {
            margin-top: 9px;
            padding-top: 9px;
            border-top: 1px solid #e7ece8;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px 24px;
            font-size: 12.5px;
        }

        .ex-meta-cell {
            display: inline-flex;
            align-items: baseline;
            gap: 6px;
        }

        .ex-meta-key {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6b7280;
        }

        .ex-meta-val {
            font-weight: 700;
            color: #111827;
        }

        /* Main Shell di bawah Fixed Header */
        .ex-shell {
            width: 100%;
            max-width: 1680px;
            margin: 0 auto;
            padding: 122px 28px 48px;
        }

        /* Grid Utama: Panel Kiri Tetap (320px) + Area Soal Kanan */
        .ex-layout {
            display: grid;
            grid-template-columns: 320px minmax(0, 1fr);
            gap: 24px;
            align-items: start;
        }

        /* Fixed Left Navigator (Diam tidak bergerak saat scroll) */
        .ex-nav-fixed {
            position: fixed;
            top: 122px;
            width: 320px;
            max-height: calc(100vh - 138px);
            background: #ffffff;
            border: 1px solid #d5dfd8;
            border-radius: 12px;
            padding: 15px 16px;
            box-shadow: 0 2px 8px rgba(31, 41, 55, 0.05);
            display: flex;
            flex-direction: column;
            z-index: 50;
        }

        .ex-nav-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding-bottom: 10px;
            margin-bottom: 10px;
            border-bottom: 1px solid #e7ece8;
            flex-shrink: 0;
        }

        .ex-nav-title {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: #1f2937;
        }

        .ex-nav-stat {
            font-size: 12px;
            font-weight: 700;
            color: #2c4a3e;
            background: #edf3ee;
            padding: 3px 10px;
            border-radius: 999px;
            white-space: nowrap;
        }

        /* Keterangan / Legenda selalu terlihat & tidak tenggelam */
        .ex-legend {
            padding-bottom: 10px;
            margin-bottom: 10px;
            border-bottom: 1px solid #e7ece8;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 8px;
            font-size: 11px;
            color: #4b5563;
            flex-shrink: 0;
        }

        .ex-legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .ex-swatch {
            width: 12px;
            height: 12px;
            border-radius: 3px;
            flex-shrink: 0;
            border: 1px solid #cbd5e1;
            background: #fafbfa;
        }

        .ex-swatch.pg {
            background: #4a7c59;
            border-color: #4a7c59;
        }

        .ex-swatch.multi {
            background: #e3b535;
            border-color: #c89b1e;
        }

        .ex-swatch.bs {
            background: #2c4a3e;
            border-color: #2c4a3e;
        }

        .ex-swatch.now {
            background: #ffffff;
            border: 2px solid #111827;
        }

        /* Area nomor soal di-scroll internal jika jumlah soal banyak */
        .ex-nav-scroll {
            overflow-y: auto;
            padding-right: 4px;
            flex: 1;
            min-height: 0;
        }

        .ex-nav-groups {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .ex-nav-group {
            padding-left: 10px;
            border-left: 3px solid #cbd5e1;
        }

        .ex-nav-group.type-pg {
            border-left-color: #4a7c59;
        }

        .ex-nav-group.type-multi {
            border-left-color: #d9a71c;
        }

        .ex-nav-group.type-bs {
            border-left-color: #2c4a3e;
        }

        .ex-nav-group-name {
            margin: 0 0 7px;
            font-size: 11.5px;
            font-weight: 700;
            color: #374151;
        }

        .ex-nav-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 6px;
        }

        .ex-box {
            height: 32px;
            border-radius: 7px;
            border: 1px solid #d1d9d3;
            background: #fafbfa;
            color: #374151;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.12s ease;
        }

        .ex-box:hover {
            border-color: #2c4a3e;
            background: #f0f5f1;
        }

        .ex-box.type-pg.is-answered {
            background: #4a7c59;
            border-color: #4a7c59;
            color: #ffffff;
        }

        .ex-box.type-multi.is-answered {
            background: #e3b535;
            border-color: #c89b1e;
            color: #111827;
        }

        .ex-box.type-bs.is-answered {
            background: #2c4a3e;
            border-color: #2c4a3e;
            color: #ffffff;
        }

        .ex-box.is-current {
            box-shadow: 0 0 0 2.5px #111827;
            border-color: #111827;
        }

        /* Kolom Kanan: Soal */
        .ex-questions-col {
            grid-column: 2;
            min-width: 0;
        }

        .ex-section {
            margin: 26px 0 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #dce4de;
        }

        .ex-section:first-child {
            margin-top: 0;
        }

        .ex-section-title {
            margin: 0;
            font-size: 17px;
            font-weight: 700;
            color: #1f2937;
        }

        .ex-section-badge {
            font-size: 12px;
            font-weight: 600;
            color: #4b5563;
        }

        /* Question Card */
        .ex-card {
            background: #ffffff;
            border: 1px solid #d5dfd8;
            border-radius: 12px;
            padding: 22px 26px;
            margin-bottom: 16px;
            scroll-margin-top: 126px;
            box-shadow: 0 2px 6px rgba(31, 41, 55, 0.04);
            transition: border-color 0.15s ease;
        }

        .ex-card.is-current {
            border-color: #2c4a3e;
        }

        .ex-card-row {
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }

        .ex-q-badge {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .ex-q-badge.type-pg {
            background: #eaf2ec;
            color: #2c4a3e;
        }

        .ex-q-badge.type-multi {
            background: #fdf6df;
            color: #785506;
        }

        .ex-q-badge.type-bs {
            background: #2c4a3e;
            color: #ffffff;
        }

        .ex-q-content {
            flex: 1;
            min-width: 0;
        }

        .ex-q-tags {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 10px;
        }

        .ex-tag {
            font-size: 11.5px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 999px;
        }

        .ex-tag.type-pg {
            background: #eaf2ec;
            color: #2c4a3e;
        }

        .ex-tag.type-multi {
            background: #fdf6df;
            color: #785506;
        }

        .ex-tag.type-bs {
            background: #2c4a3e;
            color: #ffffff;
        }

        .ex-tag-pt {
            background: #f3f4f6;
            color: #4b5563;
        }

        .ex-q-prompt {
            margin: 0;
            font-size: 16.5px;
            font-weight: 600;
            color: #111827;
            line-height: 1.55;
        }

        .ex-choices {
            margin-top: 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .ex-choice {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 16px;
            border: 1px solid #dce4de;
            border-radius: 9px;
            background: #ffffff;
            cursor: pointer;
            transition: background-color 0.12s ease, border-color 0.12s ease;
        }

        .ex-choice:hover {
            background: #f5f8f6;
            border-color: #4a7c59;
        }

        .ex-choice.is-selected {
            background: #eef5f0;
            border-color: #2c4a3e;
        }

        .ex-choice input {
            margin-top: 3px;
            width: 16px;
            height: 16px;
            accent-color: #2c4a3e;
            cursor: pointer;
            flex-shrink: 0;
        }

        .ex-choice-text {
            font-size: 14.5px;
            color: #1f2937;
            line-height: 1.45;
        }

        .ex-choice-code {
            font-weight: 700;
            color: #2c4a3e;
            margin-right: 4px;
        }

        /* Footer Note */
        .ex-footer-card {
            background: #ffffff;
            border: 1px solid #d5dfd8;
            border-radius: 12px;
            padding: 18px 24px;
            margin-top: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            box-shadow: 0 2px 6px rgba(31, 41, 55, 0.04);
        }

        .ex-note {
            font-size: 13.5px;
            color: #374151;
            margin: 0;
        }

        @media (max-width: 1024px) {
            .ex-fixed-header {
                position: static;
            }

            .ex-shell {
                padding: 14px 16px 36px;
            }

            .ex-layout {
                grid-template-columns: 1fr;
            }

            .ex-nav-fixed {
                position: static;
                width: 100%;
                max-height: none;
                margin-bottom: 18px;
            }

            .ex-questions-col {
                grid-column: 1;
            }

            .ex-card {
                scroll-margin-top: 24px;
            }
        }

        @media (max-width: 640px) {
            .ex-fixed-header,
            .ex-card,
            .ex-footer-card,
            .ex-nav-fixed {
                padding: 14px 16px;
            }

            .ex-right-controls {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
</head>

<body>

    @php
        $labelTipeSoal = [
            'pilihan_ganda' => 'Pilihan Ganda / Pilihan Tunggal',
            'pilihan_tunggal' => 'Pilihan Ganda / Pilihan Tunggal',
            'multi_jawaban' => 'Multi Jawaban',
            'benar_salah' => 'Benar / Salah',
        ];

        $getTipeClass = function ($tipe) {
            if (in_array($tipe, ['pilihan_ganda', 'pilihan_tunggal'], true)) {
                return 'type-pg';
            }
            if ($tipe === 'multi_jawaban') {
                return 'type-multi';
            }
            return 'type-bs';
        };

        $totalSemuaSoal = 0;
        foreach ($soalsPerTipe as $listSoal) {
            $totalSemuaSoal += count($listSoal);
        }

        $karyawanData = $peserta->karyawan ?? ($karyawan ?? auth()->user()?->karyawan ?? auth()->user());
        $namaPeserta = $karyawanData?->nama ?? $karyawanData?->name ?? '-';
        $idPeserta = $karyawanData?->nik ?? $karyawanData?->nip ?? $karyawanData?->id ?? '-';
        $ptPeserta = $karyawanData?->pt ?: '-';
        $deptPeserta = $karyawanData?->departemen?->nama_departemen ?? $karyawanData?->departemen ?? '-';
        $namaBankSoal = $peserta->assessment->soalUpload?->nama_file_tampilan
            ?? $peserta->assessment->soalUpload?->nama_file
            ?? $peserta->assessment->nama_assessment;
    @endphp

    <!-- Fixed Header: Judul, Bank Soal, Identitas, Tombol Kirim & Sisa Waktu -->
    <header class="ex-fixed-header">
        <div class="ex-header-inner">
            <div class="ex-header-top">
                <div class="ex-title-wrap">
                    <h1 class="ex-title">{{ $peserta->assessment->nama_assessment }}</h1>
                    <p class="ex-bank-line">Bank Soal: <strong>{{ $namaBankSoal }}</strong></p>
                </div>

                <div class="ex-right-controls">
                    <button
                        type="submit"
                        form="examForm"
                        id="submitButton"
                        class="ex-btn-kirim-top"
                    >
                        Kirim
                    </button>

                    <div class="ex-timer">
                        <p class="ex-timer-label">Sisa Waktu</p>
                        <p id="timer" class="ex-timer-val">--:--</p>
                    </div>
                </div>
            </div>

            <div class="ex-meta-strip">
                <div class="ex-meta-cell">
                    <span class="ex-meta-key">Nama</span>
                    <span class="ex-meta-val">{{ $namaPeserta }}</span>
                </div>
                <div class="ex-meta-cell">
                    <span class="ex-meta-key">ID</span>
                    <span class="ex-meta-val">{{ $idPeserta }}</span>
                </div>
                <div class="ex-meta-cell">
                    <span class="ex-meta-key">PT</span>
                    <span class="ex-meta-val">{{ $ptPeserta }}</span>
                </div>
                <div class="ex-meta-cell">
                    <span class="ex-meta-key">Departemen</span>
                    <span class="ex-meta-val">{{ $deptPeserta }}</span>
                </div>
            </div>
        </div>
    </header>

    <div class="ex-shell">

        <div class="ex-layout">

            <!-- Panel Kiri Tetap: Keterangan + Daftar Kotak Soal -->
            <aside class="ex-nav-fixed" aria-label="Daftar Soal">
                <div class="ex-nav-top">
                    <h2 class="ex-nav-title">Daftar Soal</h2>
                    <span id="answeredCounter" class="ex-nav-stat">0 / {{ $totalSemuaSoal }} Terjawab</span>
                </div>

                <div class="ex-legend">
                    <div class="ex-legend-item">
                        <span class="ex-swatch now"></span>
                        <span>Posisi kini</span>
                    </div>
                    <div class="ex-legend-item">
                        <span class="ex-swatch"></span>
                        <span>Belum dijawab</span>
                    </div>
                    <div class="ex-legend-item">
                        <span class="ex-swatch pg"></span>
                        <span>PG / Tunggal</span>
                    </div>
                    <div class="ex-legend-item">
                        <span class="ex-swatch multi"></span>
                        <span>Multi Jawaban</span>
                    </div>
                    <div class="ex-legend-item">
                        <span class="ex-swatch bs"></span>
                        <span>Benar / Salah</span>
                    </div>
                </div>

                @php $nomorNav = 1; @endphp

                <div class="ex-nav-scroll">
                    <div class="ex-nav-groups">
                        @foreach ($soalsPerTipe as $tipeSoal => $soals)
                            @php $tipeClass = $getTipeClass($tipeSoal); @endphp

                            <div class="ex-nav-group {{ $tipeClass }}">
                                <p class="ex-nav-group-name">
                                    {{ $labelTipeSoal[$tipeSoal] ?? $tipeSoal }} ({{ count($soals) }})
                                </p>

                                <div class="ex-nav-grid">
                                    @foreach ($soals as $soal)
                                        @php $noBox = $nomorNav++; @endphp
                                        <button
                                            type="button"
                                            class="ex-box {{ $tipeClass }} {{ $noBox === 1 ? 'is-current' : '' }}"
                                            data-target-soal="{{ $soal->id }}"
                                            title="Soal nomor {{ $noBox }}"
                                        >
                                            {{ $noBox }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </aside>

            <!-- Kolom Kanan: Soal Ujian -->
            <div class="ex-questions-col">
                <form
                    id="examForm"
                    method="POST"
                    action="{{ route('karyawan.assessment.submit', $peserta) }}"
                >
                    @csrf

                    @php $nomorSoal = 1; @endphp

                    @foreach ($soalsPerTipe as $tipeSoal => $soals)
                        @php $tipeClass = $getTipeClass($tipeSoal); @endphp

                        <div class="ex-section">
                            <h2 class="ex-section-title">{{ $labelTipeSoal[$tipeSoal] ?? $tipeSoal }}</h2>
                            <span class="ex-section-badge">{{ count($soals) }} soal</span>
                        </div>

                        @foreach ($soals as $soal)
                            @php $noUrut = $nomorSoal++; @endphp

                            <div
                                id="soal-card-{{ $soal->id }}"
                                class="ex-card"
                                data-soal-id="{{ $soal->id }}"
                            >
                                <div class="ex-card-row">
                                    <span class="ex-q-badge {{ $tipeClass }}">{{ $noUrut }}</span>

                                    <div class="ex-q-content">
                                        <div class="ex-q-tags">
                                            <span class="ex-tag {{ $tipeClass }}">
                                                {{ $labelTipeSoal[$soal->tipe_soal] ?? $soal->tipe_soal }}
                                            </span>
                                            <span class="ex-tag ex-tag-pt">
                                                Poin {{ number_format($soal->nilai_point, 2) }}
                                            </span>
                                        </div>

                                        <p class="ex-q-prompt">{{ $soal->pertanyaan }}</p>

                                        <div class="ex-choices">
                                            @foreach ($soal->pilihanJawabans as $pilihan)
                                                @if ($soal->tipe_soal === 'multi_jawaban')
                                                    <label class="ex-choice">
                                                        <input
                                                            type="checkbox"
                                                            name="jawaban[{{ $soal->id }}][]"
                                                            value="{{ $pilihan->id }}"
                                                            class="ex-answer-input"
                                                            data-soal-id="{{ $soal->id }}"
                                                        >
                                                        <span class="ex-choice-text">
                                                            <span class="ex-choice-code">{{ $pilihan->kode }}.</span>
                                                            {{ $pilihan->teks_jawaban }}
                                                        </span>
                                                    </label>
                                                @else
                                                    <label class="ex-choice">
                                                        <input
                                                            type="radio"
                                                            name="jawaban[{{ $soal->id }}]"
                                                            value="{{ $pilihan->id }}"
                                                            class="ex-answer-input"
                                                            data-soal-id="{{ $soal->id }}"
                                                        >
                                                        <span class="ex-choice-text">
                                                            @if ($soal->tipe_soal !== 'benar_salah')
                                                                <span class="ex-choice-code">{{ $pilihan->kode }}.</span>
                                                            @endif
                                                            {{ $soal->tipe_soal === 'benar_salah'
                                                                ? mb_strtoupper($pilihan->teks_jawaban)
                                                                : $pilihan->teks_jawaban }}
                                                        </span>
                                                    </label>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach

                    <div class="ex-footer-card">
                        <p class="ex-note">
                            Pastikan seluruh jawaban sudah diperiksa sebelum menekan tombol <strong>Kirim</strong> di bagian atas.
                        </p>
                    </div>
                </form>
            </div>

        </div>

    </div>

    <script>
        const durasiMenit = {{ $peserta->assessment->durasi }};
        const waktuMulai = new Date(
            "{{ $peserta->waktu_mulai->toIso8601String() }}"
        ).getTime();
        const waktuSelesai = waktuMulai + (durasiMenit * 60 * 1000);

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

            const totalDetik = Math.floor(sisa / 1000);
            const menit = Math.floor(totalDetik / 60);
            const detik = totalDetik % 60;

            timerElement.textContent =
                String(menit).padStart(2, '0') + ':' + String(detik).padStart(2, '0');
        }

        updateTimer();
        setInterval(updateTimer, 1000);

        (function () {
            const questionCards = Array.from(document.querySelectorAll('.ex-card'));
            const navBoxes = Array.from(document.querySelectorAll('.ex-box'));
            const answerInputs = Array.from(document.querySelectorAll('.ex-answer-input'));
            const counterEl = document.getElementById('answeredCounter');
            const totalSoal = questionCards.length;

            let currentSoalId = questionCards.length ? questionCards[0].dataset.soalId : null;

            function setCurrentSoal(soalId) {
                if (!soalId) return;
                currentSoalId = String(soalId);

                navBoxes.forEach(function (box) {
                    box.classList.toggle('is-current', box.dataset.targetSoal === currentSoalId);
                });

                questionCards.forEach(function (card) {
                    card.classList.toggle('is-current', card.dataset.soalId === currentSoalId);
                });
            }

            function refreshAnsweredStates() {
                let answeredCount = 0;

                questionCards.forEach(function (card) {
                    const soalId = card.dataset.soalId;
                    const inputs = card.querySelectorAll('.ex-answer-input');
                    let isAnswered = false;

                    inputs.forEach(function (input) {
                        const label = input.closest('.ex-choice');
                        if (label) {
                            label.classList.toggle('is-selected', input.checked);
                        }
                        if (input.checked) {
                            isAnswered = true;
                        }
                    });

                    if (isAnswered) {
                        answeredCount++;
                    }

                    navBoxes.forEach(function (box) {
                        if (box.dataset.targetSoal === soalId) {
                            box.classList.toggle('is-answered', isAnswered);
                        }
                    });
                });

                if (counterEl) {
                    counterEl.textContent = answeredCount + ' / ' + totalSoal + ' Terjawab';
                }
            }

            navBoxes.forEach(function (box) {
                box.addEventListener('click', function () {
                    const targetId = box.dataset.targetSoal;
                    const targetCard = document.getElementById('soal-card-' + targetId);
                    setCurrentSoal(targetId);

                    if (targetCard) {
                        targetCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });

            questionCards.forEach(function (card) {
                card.addEventListener('click', function () {
                    setCurrentSoal(card.dataset.soalId);
                });
            });

            answerInputs.forEach(function (input) {
                input.addEventListener('change', function () {
                    setCurrentSoal(input.dataset.soalId);
                    refreshAnsweredStates();
                });
            });

            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver(
                    function (entries) {
                        const visible = entries
                            .filter(function (entry) { return entry.isIntersecting; })
                            .sort(function (a, b) {
                                return a.boundingClientRect.top - b.boundingClientRect.top;
                            });

                        if (visible.length > 0) {
                            setCurrentSoal(visible[0].target.dataset.soalId);
                        }
                    },
                    { rootMargin: '-20% 0px -65% 0px', threshold: 0.1 }
                );

                questionCards.forEach(function (card) {
                    observer.observe(card);
                });
            }

            refreshAnsweredStates();
            setCurrentSoal(currentSoalId);
        })();

        examForm.addEventListener('submit', function (event) {
            if (sudahSubmit) {
                return;
            }

            const yakin = confirm('Apakah Anda yakin ingin mengirim jawaban?');

            if (!yakin) {
                event.preventDefault();
                return;
            }

            sudahSubmit = true;
        });
    </script>

</body>

</html>