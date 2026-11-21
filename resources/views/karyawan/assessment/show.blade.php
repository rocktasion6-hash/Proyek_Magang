<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $peserta->assessment->nama_assessment }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            min-height: 100vh;
            background: #f4f6f4;
            color: #1f2937;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        #dkExamPrepPage.dk-page {
            min-height: 100vh;
            width: 100%;
            background: #f4f6f4;
        }

        /* Navbar */
        #dkExamPrepPage .dk-topbar {
            width: 100%;
            background: #ffffff;
            border-bottom: 2px solid #2c4a3e;
            padding: 14px 36px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        #dkExamPrepPage .dk-brand {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        #dkExamPrepPage .dk-brand-bar {
            width: 6px;
            height: 34px;
            background: #e3b535;
            border-radius: 3px;
            flex-shrink: 0;
        }

        #dkExamPrepPage .dk-brand-title {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #2c4a3e;
            line-height: 1.2;
        }

        #dkExamPrepPage .dk-brand-sub {
            margin: 2px 0 0;
            font-size: 12.5px;
            font-weight: 600;
            color: #4a7c59;
        }

        /* Account Dropdown */
        #dkExamPrepPage .dk-account-wrap {
            position: relative;
        }

        #dkExamPrepPage .dk-account-trigger {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 7px 14px;
            border-radius: 8px;
            border: 1px solid #c6d2c9;
            background: #fdfdfc;
            color: #111827;
            cursor: pointer;
            text-align: right;
            transition: background-color 0.15s ease, border-color 0.15s ease;
        }

        #dkExamPrepPage .dk-account-trigger:hover,
        #dkExamPrepPage .dk-account-trigger[aria-expanded="true"] {
            background: #f4f6f4;
            border-color: #2c4a3e;
        }

        #dkExamPrepPage .dk-account-info {
            text-align: right;
        }

        #dkExamPrepPage .dk-account-name {
            display: block;
            font-size: 13.5px;
            font-weight: 700;
            color: #111827;
            line-height: 1.2;
        }

        #dkExamPrepPage .dk-account-jabatan {
            display: block;
            margin-top: 2px;
            font-size: 12px;
            color: #4b5563;
            line-height: 1.2;
        }

        #dkExamPrepPage .dk-caret {
            font-size: 11px;
            color: #2c4a3e;
            font-weight: 700;
            margin-left: 2px;
        }

        #dkExamPrepPage .dk-dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            min-width: 150px;
            background: #ffffff;
            border: 1px solid #c6d2c9;
            border-radius: 8px;
            padding: 6px;
            z-index: 50;
            box-shadow: 0 6px 16px rgba(44, 74, 62, 0.08);
        }

        #dkExamPrepPage .dk-dropdown-menu.is-open {
            display: block;
        }

        #dkExamPrepPage .dk-btn-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 8px 14px;
            border-radius: 6px;
            border: 1.5px solid #991b1b;
            background: #ffffff;
            color: #991b1b;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            line-height: 1.2;
            white-space: nowrap;
            transition: background-color 0.15s ease, color 0.15s ease;
        }

        #dkExamPrepPage .dk-btn-logout:hover,
        #dkExamPrepPage .dk-btn-logout:active {
            background: #991b1b;
            color: #ffffff;
            border-color: #991b1b;
        }

        /* Main Area */
        #dkExamPrepPage .dk-main-wrap {
            width: 100%;
            padding: 24px 36px 44px;
        }

        #dkExamPrepPage .dk-card {
            width: 100%;
            max-width: none;
            background: #ffffff;
            border: 1px solid #d8e0da;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(44, 74, 62, 0.05);
        }

        /* Header Banner */
     #dkExamPrepPage .dk-head {
    width: 100%;
    height: 116px;
    background: #2c4a3e;
    color: #fdfdfc;
    padding: 18px 30px;
    border-bottom: 3px solid #e3b535;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
}

        #dkExamPrepPage .dk-eyebrow {
            display: inline-block;
            background: #4a7c59;
            color: #fdfdfc;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 4px 10px;
            border-radius: 5px;
            margin-bottom: 8px;
        }

        #dkExamPrepPage .dk-title {
            margin: 0;
            font-size: 23px;
            font-weight: 700;
            color: #fdfdfc;
            line-height: 1.25;
        }

        #dkExamPrepPage .dk-sub {
            margin: 5px 0 0;
            font-size: 13.5px;
            color: #dce5df;
        }

        #dkExamPrepPage .dk-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 7px;
            font-size: 12.5px;
            font-weight: 700;
            line-height: 1.2;
            white-space: nowrap;
        }

        #dkExamPrepPage .dk-badge.ditugaskan {
            background: #e3b535;
            color: #111827;
            border: 1px solid #e3b535;
        }

        #dkExamPrepPage .dk-badge.sedang_mengerjakan {
            background: #4a7c59;
            color: #fdfdfc;
            border: 1px solid #fdfdfc;
        }

        #dkExamPrepPage .dk-badge.selesai {
            background: #fdfdfc;
            color: #2c4a3e;
            border: 1px solid #fdfdfc;
        }

        /* Split Pane */
        #dkExamPrepPage .dk-split {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            border-left: 5px solid #4a7c59;
        }

        #dkExamPrepPage .dk-pane-left {
            padding: 26px 30px;
            border-right: 1px solid #e2e8e4;
        }

        #dkExamPrepPage .dk-pane-right {
            padding: 26px 30px;
            background: #fdfdfc;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 20px;
        }

        #dkExamPrepPage .dk-pane-title {
            margin: 0 0 16px;
            font-size: 15.5px;
            font-weight: 700;
            color: #2c4a3e;
        }

        #dkExamPrepPage .dk-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 13px 0;
            border-bottom: 1px solid #edf1ee;
        }

        #dkExamPrepPage .dk-row:first-of-type {
            padding-top: 0;
        }

        #dkExamPrepPage .dk-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        #dkExamPrepPage .dk-row-label {
            font-size: 13.5px;
            font-weight: 600;
            color: #4b5563;
        }

        #dkExamPrepPage .dk-row-val {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            text-align: right;
        }

        #dkExamPrepPage .dk-stat-strip {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        #dkExamPrepPage .dk-stat-box {
            background: #f4f6f4;
            border: 1px solid #d8e0da;
            border-top: 3px solid #4a7c59;
            border-radius: 8px;
            padding: 14px 10px;
            text-align: center;
        }

        #dkExamPrepPage .dk-stat-box.mustard {
            border-top-color: #e3b535;
        }

        #dkExamPrepPage .dk-stat-box.green {
            border-top-color: #2c4a3e;
        }

        #dkExamPrepPage .dk-stat-num {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: #2c4a3e;
            line-height: 1.2;
        }

        #dkExamPrepPage .dk-stat-lbl {
            margin: 4px 0 0;
            font-size: 12px;
            font-weight: 600;
            color: #4b5563;
        }

        #dkExamPrepPage .dk-guide-list {
            margin: 0;
            padding-left: 18px;
            font-size: 13px;
            font-weight: 500;
            color: #2c4a3e;
            line-height: 1.55;
        }

        #dkExamPrepPage .dk-guide-box {
            background: #f4f6f4;
            border-left: 4px solid #e3b535;
            border-radius: 8px;
            padding: 12px 16px;
        }

        #dkExamPrepPage .dk-error {
            padding: 12px 15px;
            border-radius: 8px;
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
            border-left: 4px solid #991b1b;
            font-size: 13.5px;
            font-weight: 600;
        }

        #dkExamPrepPage .dk-btn-start {
            width: 100%;
            padding: 12px 20px;
            border-radius: 8px;
            border: 1.5px solid #2c4a3e;
            background: #2c4a3e;
            color: #fdfdfc;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.15s ease, border-color 0.15s ease;
        }

        #dkExamPrepPage .dk-btn-start:hover,
        #dkExamPrepPage .dk-btn-start:active {
            background: #1b3027;
            border-color: #1b3027;
            color: #fdfdfc;
        }

        #dkExamPrepPage .dk-btn-disabled {
            width: 100%;
            padding: 12px 20px;
            border-radius: 8px;
            border: 1px solid #c6d2c9;
            background: #f4f6f4;
            color: #2c4a3e;
            font-size: 14px;
            font-weight: 700;
            cursor: not-allowed;
        }

        #dkExamPrepPage .dk-done {
            margin: 0;
            padding: 12px 16px;
            border-radius: 8px;
            background: #2c4a3e;
            color: #fdfdfc;
            text-align: center;
            font-size: 14px;
            font-weight: 700;
        }

        #dkExamPrepPage .dk-back-footer {
            margin-top: 14px;
            text-align: center;
        }

        #dkExamPrepPage .dk-back-plain {
            display: inline-block;
            padding: 6px 10px;
            color: #4b5563;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        #dkExamPrepPage .dk-back-plain:hover,
        #dkExamPrepPage .dk-back-plain:active {
            color: #2c4a3e;
            text-decoration: underline;
        }

        @media (max-width: 900px) {
            #dkExamPrepPage .dk-split {
                grid-template-columns: 1fr;
            }

            #dkExamPrepPage .dk-pane-left {
                border-right: none;
                border-bottom: 1px solid #e2e8e4;
            }
        }

        @media (max-width: 640px) {
            #dkExamPrepPage .dk-topbar,
            #dkExamPrepPage .dk-main-wrap {
                padding-left: 16px;
                padding-right: 16px;
            }

            #dkExamPrepPage .dk-main-wrap {
                padding-top: 18px;
                padding-bottom: 32px;
            }

            #dkExamPrepPage .dk-head,
            #dkExamPrepPage .dk-pane-left,
            #dkExamPrepPage .dk-pane-right {
                padding-left: 18px;
                padding-right: 18px;
            }

            #dkExamPrepPage .dk-stat-strip {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body id="dkExamPrepPage" class="dk-page">

    @php
        $user = auth()->user();
        $namaAkun = $peserta->karyawan?->nama ?? ($user?->karyawan?->nama ?? ($user?->name ?? 'Karyawan'));
        $jabatanAkun = $peserta->karyawan?->jabatan?->nama_jabatan ?? ($user?->karyawan?->jabatan?->nama_jabatan ?? ($user?->role ?? 'Karyawan'));
    @endphp

    <header class="dk-topbar">
        <div class="dk-brand">
            <span class="dk-brand-bar" aria-hidden="true"></span>
            <div>
                <h1 class="dk-brand-title">Sistem Penilaian Jabatan</h1>
                <p class="dk-brand-sub">Persiapan Assessment</p>
            </div>
        </div>

        <div class="dk-account-wrap" id="dkAccountDropdown">
            <button
                type="button"
                class="dk-account-trigger"
                id="dkAccountTrigger"
                aria-expanded="false"
                aria-controls="dkAccountMenu"
            >
                <span class="dk-account-info">
                    <span class="dk-account-name">{{ $namaAkun }}</span>
                    <span class="dk-account-jabatan">{{ $jabatanAkun }}</span>
                </span>
                <span class="dk-caret" aria-hidden="true">&#9662;</span>
            </button>

            <div class="dk-dropdown-menu" id="dkAccountMenu" role="menu">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dk-btn-logout">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <div class="dk-main-wrap">
        <main class="dk-card">

            <header class="dk-head">
                <div>
                    <span class="dk-eyebrow">Sebelum Mulai Ujian</span>
                    <h2 class="dk-title">{{ $peserta->assessment->nama_assessment }}</h2>
                    <p class="dk-sub">Baca informasi di bawah ini lalu tekan Mulai Ujian jika sudah siap.</p>
                </div>

                <div>
                    @if ($peserta->status === 'ditugaskan')
                        <span class="dk-badge ditugaskan">Belum Dikerjakan</span>
                    @elseif ($peserta->status === 'sedang_mengerjakan')
                        <span class="dk-badge sedang_mengerjakan">Sedang Dikerjakan</span>
                    @elseif ($peserta->status === 'selesai')
                        <span class="dk-badge selesai">Sudah Selesai</span>
                    @else
                        <span class="dk-badge ditugaskan">{{ ucfirst(str_replace('_', ' ', $peserta->status)) }}</span>
                    @endif
                </div>
            </header>

            <div class="dk-split">

                <!-- Sisi Kiri -->
                <section class="dk-pane-left">
                    <h3 class="dk-pane-title">Informasi Ujian</h3>

                    <div class="dk-row">
                        <span class="dk-row-label">Jabatan</span>
                        <span class="dk-row-val">{{ $peserta->assessment->jabatan?->nama_jabatan ?? '-' }}</span>
                    </div>

                    <div class="dk-row">
                        <span class="dk-row-label">Paket Soal</span>
                        <span class="dk-row-val">{{ $peserta->assessment->soalUpload?->nama_file_tampilan ?? '-' }}</span>
                    </div>

                    <div class="dk-row">
                        <span class="dk-row-label">Waktu Pengerjaan</span>
                        <span class="dk-row-val">{{ $peserta->assessment->durasi }} menit</span>
                    </div>

                    <div class="dk-row">
                        <span class="dk-row-label">Nilai Minimal Lulus</span>
                        <span class="dk-row-val">{{ number_format($peserta->assessment->standar_nilai, 0) }}</span>
                    </div>

                    <div class="dk-row">
                        <span class="dk-row-label">Total Pertanyaan</span>
                        <span class="dk-row-val">{{ $peserta->assessment->soals->count() }} soal</span>
                    </div>

                    <div class="dk-row">
                        <span class="dk-row-label">Status Kamu</span>
                        <span class="dk-row-val">
                            @if ($peserta->status === 'ditugaskan')
                                Belum Dikerjakan
                            @elseif ($peserta->status === 'sedang_mengerjakan')
                                Sedang Dikerjakan
                            @elseif ($peserta->status === 'selesai')
                                Sudah Selesai
                            @else
                                {{ ucfirst(str_replace('_', ' ', $peserta->status)) }}
                            @endif
                        </span>
                    </div>
                </section>

                <!-- Sisi Kanan -->
                <aside class="dk-pane-right">
                    <div>
                        <h3 class="dk-pane-title">Ringkasan &amp; Petunjuk</h3>

                        <div class="dk-stat-strip" style="margin-bottom: 16px;">
                            <div class="dk-stat-box mustard">
                                <p class="dk-stat-num">{{ $peserta->assessment->durasi }} mnt</p>
                                <p class="dk-stat-lbl">Waktu Ujian</p>
                            </div>

                            <div class="dk-stat-box green">
                                <p class="dk-stat-num">{{ number_format($peserta->assessment->standar_nilai, 0) }}</p>
                                <p class="dk-stat-lbl">Nilai Lulus</p>
                            </div>

                            <div class="dk-stat-box">
                                <p class="dk-stat-num">{{ $peserta->assessment->soals->count() }}</p>
                                <p class="dk-stat-lbl">Jumlah Soal</p>
                            </div>
                        </div>

                        <div class="dk-guide-box">
                            <ul class="dk-guide-list">
                                <li>Waktu langsung jalan saat tombol <strong>Mulai Ujian</strong> ditekan.</li>
                                <li>Pastikan jaringan dan waktu luangmu aman sebelum mulai.</li>
                                <li>Jawab semua soal sampai selesai sebelum waktu habis.</li>
                            </ul>
                        </div>
                    </div>

                    @if (session('error'))
                        <div class="dk-error">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div>
                        @if ($peserta->status === 'ditugaskan')
                            @if ($peserta->assessment->status === 'aktif')
                                <form method="POST" action="{{ route('karyawan.assessment.start', $peserta) }}">
                                    @csrf
                                    <button type="submit" class="dk-btn-start">
                                        Mulai Ujian
                                    </button>
                                </form>
                            @else
                                <button type="button" disabled class="dk-btn-disabled">
                                    Ujian Belum Dibuka HRD
                                </button>
                            @endif
                        @elseif ($peserta->status === 'sedang_mengerjakan')
                            <button type="button" disabled class="dk-btn-disabled">
                                Ujian Sedang Berlangsung
                            </button>
                        @elseif ($peserta->status === 'selesai')
                            <p class="dk-done">
                                Ujian sudah selesai dikerjakan.
                            </p>
                        @endif

                        <div class="dk-back-footer">
                            <a href="{{ route('karyawan.assessment.index') }}" class="dk-back-plain">
                                &larr; Kembali ke daftar ujian
                            </a>
                        </div>
                    </div>
                </aside>

            </div>

        </main>
    </div>

    <script>
        (function () {
            const wrap = document.getElementById('dkAccountDropdown');
            const trigger = document.getElementById('dkAccountTrigger');
            const menu = document.getElementById('dkAccountMenu');

            if (!wrap || !trigger || !menu) return;

            function closeMenu() {
                menu.classList.remove('is-open');
                trigger.setAttribute('aria-expanded', 'false');
            }

            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                const isOpen = menu.classList.contains('is-open');
                if (isOpen) {
                    closeMenu();
                } else {
                    menu.classList.add('is-open');
                    trigger.setAttribute('aria-expanded', 'true');
                }
            });

            document.addEventListener('click', function (e) {
                if (!wrap.contains(e.target)) {
                    closeMenu();
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeMenu();
                }
            });
        })();
    </script>

</body>

</html>