<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Karyawan</title>

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

        #dkAssessmentIndexPage.dk-page {
            min-height: 100vh;
            width: 100%;
            background: #f4f6f4;
        }

        /* Navbar */
        #dkAssessmentIndexPage .dk-topbar {
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

        #dkAssessmentIndexPage .dk-brand {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        #dkAssessmentIndexPage .dk-brand-bar {
            width: 6px;
            height: 34px;
            background: #e3b535;
            border-radius: 3px;
            flex-shrink: 0;
        }

        #dkAssessmentIndexPage .dk-brand-title {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #2c4a3e;
            line-height: 1.2;
        }

        #dkAssessmentIndexPage .dk-brand-sub {
            margin: 2px 0 0;
            font-size: 12.5px;
            font-weight: 600;
            color: #4a7c59;
        }

        /* Account Dropdown */
        #dkAssessmentIndexPage .dk-account-wrap {
            position: relative;
        }

        #dkAssessmentIndexPage .dk-account-trigger {
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

        #dkAssessmentIndexPage .dk-account-trigger:hover,
        #dkAssessmentIndexPage .dk-account-trigger[aria-expanded="true"] {
            background: #f4f6f4;
            border-color: #2c4a3e;
        }

        #dkAssessmentIndexPage .dk-account-info {
            text-align: right;
        }

        #dkAssessmentIndexPage .dk-account-name {
            display: block;
            font-size: 13.5px;
            font-weight: 700;
            color: #111827;
            line-height: 1.2;
        }

        #dkAssessmentIndexPage .dk-account-jabatan {
            display: block;
            margin-top: 2px;
            font-size: 12px;
            color: #4b5563;
            line-height: 1.2;
        }

        #dkAssessmentIndexPage .dk-caret {
            font-size: 11px;
            color: #2c4a3e;
            font-weight: 700;
            margin-left: 2px;
        }

        #dkAssessmentIndexPage .dk-dropdown-menu {
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

        #dkAssessmentIndexPage .dk-dropdown-menu.is-open {
            display: block;
        }

        #dkAssessmentIndexPage .dk-btn-logout {
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

        #dkAssessmentIndexPage .dk-btn-logout:hover,
        #dkAssessmentIndexPage .dk-btn-logout:active {
            background: #991b1b;
            color: #ffffff;
            border-color: #991b1b;
        }

        /* Content */
        #dkAssessmentIndexPage .dk-content {
            width: 100%;
            max-width: none;
            padding: 24px 36px 48px;
        }

#dkAssessmentIndexPage .dk-header-box {
    width: 100%;
    height: 116px;
    background: #2c4a3e;
    color: #fdfdfc;
    border: 1px solid #2c4a3e;
    border-bottom: 3px solid #e3b535;
    border-radius: 10px 10px 0 0;
    padding: 18px 30px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

        #dkAssessmentIndexPage .dk-title {
            margin: 0 0 5px;
            font-size: 22px;
            font-weight: 700;
            color: #fdfdfc;
            line-height: 1.25;
        }

        #dkAssessmentIndexPage .dk-subtitle {
            margin: 0;
            font-size: 13.5px;
            color: #dce5df;
        }

        #dkAssessmentIndexPage .dk-error {
            width: 100%;
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
            border-left: 4px solid #991b1b;
            padding: 13px 20px;
            font-size: 13.5px;
            font-weight: 600;
        }

        /* Connected Full-Width Cards Stack */
        #dkAssessmentIndexPage .dk-stack {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        #dkAssessmentIndexPage .dk-item-card {
            width: 100%;
            background: #ffffff;
            border: 1px solid #d8e0da;
            border-top: none;
            border-left: 5px solid #4a7c59;
            padding: 28px 34px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        #dkAssessmentIndexPage .dk-item-card:nth-child(even) {
            background: #fdfdfc;
        }

        #dkAssessmentIndexPage .dk-item-card:last-child {
            border-radius: 0 0 10px 10px;
        }

        #dkAssessmentIndexPage .dk-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
        }

        #dkAssessmentIndexPage .dk-item-title {
            margin: 0 0 8px;
            font-size: 19px;
            font-weight: 700;
            color: #111827;
            line-height: 1.3;
        }

        #dkAssessmentIndexPage .dk-meta-line {
            margin: 6px 0 0;
            font-size: 14px;
            color: #4b5563;
            line-height: 1.45;
        }

        #dkAssessmentIndexPage .dk-badges {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            flex-shrink: 0;
        }

        #dkAssessmentIndexPage .dk-badge {
            display: inline-block;
            padding: 5px 13px;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 700;
            line-height: 1.2;
            white-space: nowrap;
        }

        #dkAssessmentIndexPage .dk-badge.ditugaskan {
            background: #fdf6df;
            color: #2c4a3e;
            border: 1px solid #e3b535;
        }

        #dkAssessmentIndexPage .dk-badge.sedang_mengerjakan {
            background: #f4f6f4;
            color: #2c4a3e;
            border: 1px solid #4a7c59;
        }

        #dkAssessmentIndexPage .dk-badge.selesai {
            background: #2c4a3e;
            color: #fdfdfc;
            border: 1px solid #2c4a3e;
        }

        #dkAssessmentIndexPage .dk-badge.neutral {
            background: #f4f6f4;
            color: #374151;
            border: 1px solid #d1dbd4;
        }

        /* Stats Strip */
        #dkAssessmentIndexPage .dk-stats {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        #dkAssessmentIndexPage .dk-stat-box {
            background: #ffffff;
            border: 1px solid #dfe6e1;
            border-top: 3px solid #2c4a3e;
            border-radius: 8px;
            padding: 16px 20px;
        }

        #dkAssessmentIndexPage .dk-stat-box.mustard {
            border-top-color: #e3b535;
        }

        #dkAssessmentIndexPage .dk-stat-box.sage {
            border-top-color: #4a7c59;
        }

        #dkAssessmentIndexPage .dk-stat-label {
            margin: 0;
            font-size: 12.5px;
            font-weight: 600;
            color: #6b7280;
        }

        #dkAssessmentIndexPage .dk-stat-value {
            margin: 6px 0 0;
            font-size: 16.5px;
            font-weight: 700;
            color: #111827;
        }

        /* Action & Back Footer */
        #dkAssessmentIndexPage .dk-card-actions {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        #dkAssessmentIndexPage .dk-btn-view {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 13px 20px;
            border-radius: 8px;
            border: 1.5px solid #2c4a3e;
            background: #2c4a3e;
            color: #fdfdfc;
            font-size: 14.5px;
            font-weight: 700;
            text-decoration: none;
            transition: background-color 0.15s ease, border-color 0.15s ease;
        }

        #dkAssessmentIndexPage .dk-btn-view:hover,
        #dkAssessmentIndexPage .dk-btn-view:active {
            background: #1b3027;
            border-color: #1b3027;
            color: #fdfdfc;
        }

        #dkAssessmentIndexPage .dk-back-link {
            display: inline-block;
            align-self: center;
            padding: 5px 10px;
            color: #4b5563;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        #dkAssessmentIndexPage .dk-back-link:hover,
        #dkAssessmentIndexPage .dk-back-link:active {
            color: #2c4a3e;
            text-decoration: underline;
        }

        /* Empty State */
        #dkAssessmentIndexPage .dk-empty-card {
            width: 100%;
            background: #ffffff;
            border: 1px solid #d8e0da;
            border-top: none;
            border-radius: 0 0 10px 10px;
            padding: 36px 28px;
            text-align: center;
            color: #4b5563;
            font-size: 14px;
            font-weight: 500;
        }

        @media (max-width: 640px) {
            #dkAssessmentIndexPage .dk-topbar,
            #dkAssessmentIndexPage .dk-content {
                padding-left: 16px;
                padding-right: 16px;
            }

            #dkAssessmentIndexPage .dk-header-box,
            #dkAssessmentIndexPage .dk-item-card {
                padding-left: 20px;
                padding-right: 20px;
            }

            #dkAssessmentIndexPage .dk-card-top {
                flex-direction: column;
                align-items: flex-start;
            }

            #dkAssessmentIndexPage .dk-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body id="dkAssessmentIndexPage" class="dk-page">

    @php
        $user = auth()->user();
        $namaAkun = $user?->karyawan?->nama ?? ($user?->name ?? 'Karyawan');
        $jabatanAkun = $user?->karyawan?->jabatan?->nama_jabatan ?? ($user?->role ?? 'Karyawan');
    @endphp

    <header class="dk-topbar">
        <div class="dk-brand">
            <span class="dk-brand-bar" aria-hidden="true"></span>
            <div>
                <h1 class="dk-brand-title">Sistem Penilaian Jabatan</h1>
                <p class="dk-brand-sub">Assessment Karyawan</p>
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

    <main class="dk-content">

        <section class="dk-header-box">
            <h2 class="dk-title">Assessment Saya</h2>
            <p class="dk-subtitle">Daftar assessment yang diberikan oleh HRD.</p>
        </section>

        @if (session('error'))
            <div class="dk-error">
                {{ session('error') }}
            </div>
        @endif

        <section class="dk-stack">

            @forelse ($assessmentPeserta as $peserta)

                <article class="dk-item-card">

                    <div class="dk-card-top">
                        <div>
                            <h3 class="dk-item-title">
                                {{ $peserta->assessment->nama_assessment }}
                            </h3>

                            <p class="dk-meta-line">
                                Jabatan: <strong>{{ $peserta->assessment->jabatan?->nama_jabatan ?? '-' }}</strong>
                            </p>

                            <p class="dk-meta-line">
                                Bank Soal: <strong>{{ $peserta->assessment->soalUpload?->nama_file_tampilan ?? '-' }}</strong>
                            </p>
                        </div>

                        <div class="dk-badges">
                            @if ($peserta->status === 'ditugaskan')
                                <span class="dk-badge ditugaskan">Ditugaskan</span>
                            @elseif ($peserta->status === 'sedang_mengerjakan')
                                <span class="dk-badge sedang_mengerjakan">Sedang Mengerjakan</span>
                            @elseif ($peserta->status === 'selesai')
                                <span class="dk-badge selesai">Selesai</span>
                            @else
                                <span class="dk-badge neutral">{{ ucfirst($peserta->status) }}</span>
                            @endif

                            @if ($peserta->assessment->status !== 'aktif')
                                <span class="dk-badge neutral">Assessment Tidak Aktif</span>
                            @endif
                        </div>
                    </div>

                    <div class="dk-stats">
                        <div class="dk-stat-box mustard">
                            <p class="dk-stat-label">Durasi</p>
                            <p class="dk-stat-value">{{ $peserta->assessment->durasi }} menit</p>
                        </div>

                        <div class="dk-stat-box">
                            <p class="dk-stat-label">Standar</p>
                            <p class="dk-stat-value">{{ number_format($peserta->assessment->standar_nilai, 0) }}</p>
                        </div>

                        <div class="dk-stat-box sage">
                            <p class="dk-stat-label">Soal</p>
                            <p class="dk-stat-value">{{ $peserta->assessment->soals->count() }}</p>
                        </div>
                    </div>

                    <div class="dk-card-actions">
                        <a href="{{ route('karyawan.assessment.show', $peserta) }}" class="dk-btn-view">
                            Lihat Assessment
                        </a>

                        <a href="{{ route('karyawan.dashboard') }}" class="dk-back-link">
                            &larr; Kembali ke dashboard
                        </a>
                    </div>

                </article>

            @empty

                <div class="dk-empty-card">
                    <p style="margin: 0 0 12px;">Belum ada assessment yang diberikan kepada Anda.</p>
                    <a href="{{ route('karyawan.dashboard') }}" class="dk-back-link">
                        &larr; Kembali ke dashboard
                    </a>
                </div>

            @endforelse

        </section>

    </main>

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