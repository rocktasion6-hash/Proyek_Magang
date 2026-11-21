<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Karyawan - Sistem Penilaian Jabatan</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after {
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

        #dkEmployeePage.dk-shell {
            width: 100%;
            min-height: 100vh;
            background: #f4f6f4;
        }

        /* Navbar */
        #dkEmployeePage .dk-topbar {
            width: 100%;
            background: #ffffff;
            border-bottom: 2px solid #2c4a3e;
            padding: 15px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        #dkEmployeePage .dk-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        #dkEmployeePage .dk-brand-bar {
            width: 6px;
            height: 36px;
            background: #e3b535;
            border-radius: 3px;
            flex-shrink: 0;
        }

        #dkEmployeePage .dk-brand-title {
            margin: 0;
            font-size: 18.5px;
            font-weight: 700;
            color: #2c4a3e;
            line-height: 1.2;
        }

        #dkEmployeePage .dk-brand-sub {
            margin: 3px 0 0;
            font-size: 12.5px;
            font-weight: 600;
            color: #4a7c59;
        }

        /* Account Dropdown (Dropdown Mengikuti Panjang Kotak Nama di Atas) */
        #dkEmployeePage .dk-account-wrap {
            position: relative;
            display: inline-block;
        }

        #dkEmployeePage .dk-account-trigger {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border-radius: 8px;
            border: 1px solid #c6d2c9;
            background: #fdfdfc;
            color: #111827;
            cursor: pointer;
            text-align: right;
            transition: background-color 0.15s ease, border-color 0.15s ease;
        }

        #dkEmployeePage .dk-account-trigger:hover,
        #dkEmployeePage .dk-account-trigger[aria-expanded="true"] {
            background: #f4f6f4;
            border-color: #2c4a3e;
        }

        #dkEmployeePage .dk-account-info {
            text-align: right;
        }

        #dkEmployeePage .dk-account-name {
            margin: 0;
            font-size: 13.5px;
            font-weight: 700;
            color: #111827;
            line-height: 1.2;
            white-space: nowrap;
        }

        #dkEmployeePage .dk-account-jabatan {
            margin: 2px 0 0;
            font-size: 12px;
            color: #4b5563;
            line-height: 1.2;
            white-space: nowrap;
        }

        #dkEmployeePage .dk-caret {
            font-size: 11px;
            color: #2c4a3e;
            font-weight: 700;
            margin-left: 2px;
            flex-shrink: 0;
        }

        #dkEmployeePage .dk-dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            left: 0;
            width: 100%;
            background: #ffffff;
            border: 1px solid #c6d2c9;
            border-radius: 8px;
            padding: 6px;
            z-index: 50;
            box-shadow: 0 6px 16px rgba(44, 74, 62, 0.08);
        }

        #dkEmployeePage .dk-dropdown-menu.is-open {
            display: block;
        }

        #dkEmployeePage .dk-btn-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 8px 12px;
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

        #dkEmployeePage .dk-btn-logout:hover,
        #dkEmployeePage .dk-btn-logout:active {
            background: #991b1b;
            color: #ffffff;
            border-color: #991b1b;
        }

        /* Content */
        #dkEmployeePage .dk-content {
            width: 100%;
            padding: 22px 32px 34px;
        }

        /* Welcome Banner */
        #dkEmployeePage .dk-welcome {
            background: #2c4a3e;
            color: #fdfdfc;
            border: 1px solid #2c4a3e;
            border-bottom: 3px solid #e3b535;
            border-radius: 10px;
            padding: 18px 24px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        #dkEmployeePage .dk-welcome h2 {
            margin: 0 0 4px;
            font-size: 21px;
            font-weight: 700;
            color: #fdfdfc;
            line-height: 1.25;
        }

        #dkEmployeePage .dk-welcome p {
            margin: 0;
            font-size: 13.5px;
            color: #dce5df;
        }

        #dkEmployeePage .dk-welcome-tags {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        #dkEmployeePage .dk-tag {
            background: #4a7c59;
            color: #fdfdfc;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 12.5px;
            font-weight: 600;
        }

        #dkEmployeePage .dk-tag.mustard {
            background: #e3b535;
            color: #111827;
            font-weight: 700;
        }

        /* Metrics */
        #dkEmployeePage .dk-metrics {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 18px;
        }

        #dkEmployeePage .dk-metric-card {
            background: #ffffff;
            border: 1px solid #d8e0da;
            border-left: 4px solid #2c4a3e;
            border-radius: 9px;
            padding: 16px 20px;
            box-shadow: 0 2px 6px rgba(44, 74, 62, 0.04);
        }

        #dkEmployeePage .dk-metric-card.is-mustard {
            border-left-color: #e3b535;
        }

        #dkEmployeePage .dk-metric-card.is-sage {
            border-left-color: #4a7c59;
        }

        #dkEmployeePage .dk-metric-label {
            margin: 0;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #4b5563;
        }

        #dkEmployeePage .dk-metric-role {
            margin: 8px 0 3px;
            font-size: 18px;
            font-weight: 700;
            color: #2c4a3e;
            line-height: 1.25;
        }

        #dkEmployeePage .dk-metric-sub {
            margin: 2px 0 0;
            font-size: 13px;
            color: #4b5563;
        }

        #dkEmployeePage .dk-metric-num {
            margin: 8px 0 0;
            font-size: 28px;
            font-weight: 700;
            color: #2c4a3e;
            line-height: 1.15;
        }

        #dkEmployeePage .dk-metric-num.sage {
            color: #4a7c59;
        }

        /* Section Cards */
        #dkEmployeePage .dk-card {
            background: #ffffff;
            border: 1px solid #d8e0da;
            border-radius: 9px;
            margin-bottom: 18px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(44, 74, 62, 0.04);
        }

        #dkEmployeePage .dk-card:last-child {
            margin-bottom: 0;
        }

        #dkEmployeePage .dk-card-head {
            padding: 14px 20px;
            border-bottom: 1px solid #e2e8e4;
            border-left: 4px solid #4a7c59;
            background: #fdfdfc;
        }

        #dkEmployeePage .dk-card-head.is-mustard {
            border-left-color: #e3b535;
        }

        #dkEmployeePage .dk-card-head.is-green {
            border-left-color: #2c4a3e;
        }

        #dkEmployeePage .dk-card-head h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            color: #2c4a3e;
        }

        #dkEmployeePage .dk-card-head p {
            margin: 3px 0 0;
            font-size: 13px;
            color: #6b7280;
        }

        #dkEmployeePage .dk-card-body {
            padding: 16px 20px;
        }

        /* Last Result */
        #dkEmployeePage .dk-last-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 14px;
            align-items: center;
        }

        #dkEmployeePage .dk-last-box {
            background: #f4f6f4;
            border: 1px solid #dfe6e1;
            border-radius: 7px;
            padding: 12px 16px;
        }

        #dkEmployeePage .dk-last-label {
            margin: 0 0 4px;
            font-size: 11.5px;
            font-weight: 600;
            color: #6b7280;
        }

        #dkEmployeePage .dk-last-title {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            color: #2c4a3e;
        }

        #dkEmployeePage .dk-last-score {
            margin: 0;
            font-size: 21px;
            font-weight: 700;
            color: #2c4a3e;
        }

        /* Tables */
        #dkEmployeePage .dk-table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        #dkEmployeePage .dk-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        #dkEmployeePage .dk-table th {
            background: #f4f6f4;
            color: #2c4a3e;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            padding: 11px 16px;
            border-bottom: 1px solid #d8e0da;
            text-align: left;
            white-space: nowrap;
        }

        #dkEmployeePage .dk-table th.dk-center,
        #dkEmployeePage .dk-table td.dk-center {
            text-align: center;
        }

        #dkEmployeePage .dk-table td {
            padding: 12px 16px;
            font-size: 13.5px;
            color: #1f2937;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        #dkEmployeePage .dk-table tbody tr {
            transition: background-color 0.15s ease;
        }

        #dkEmployeePage .dk-table tbody tr:hover {
            background: #f4f6f4;
        }

        #dkEmployeePage .dk-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Badges */
        #dkEmployeePage .dk-badge {
            display: inline-block;
            padding: 4px 11px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.2;
            white-space: nowrap;
        }

        #dkEmployeePage .dk-badge.ditugaskan,
        #dkEmployeePage .dk-badge.diajukan {
            background: #fdf6df;
            color: #2c4a3e;
            border: 1px solid #e3b535;
        }

        #dkEmployeePage .dk-badge.sedang_mengerjakan,
        #dkEmployeePage .dk-badge.diproses {
            background: #f4f6f4;
            color: #2c4a3e;
            border: 1px solid #4a7c59;
        }

        #dkEmployeePage .dk-badge.lulus,
        #dkEmployeePage .dk-badge.disetujui {
            background: #2c4a3e;
            color: #fdfdfc;
            border: 1px solid #2c4a3e;
        }

        #dkEmployeePage .dk-badge.tidak_lulus,
        #dkEmployeePage .dk-badge.ditolak {
            background: #991b1b;
            color: #ffffff;
            border: 1px solid #991b1b;
        }

        #dkEmployeePage .dk-empty-note {
            background: #fdfdfc;
            border: 1px dashed #c6d2c9;
            border-radius: 7px;
            padding: 12px 16px;
            text-align: center;
            font-size: 13px;
            color: #4b5563;
        }

        @media (max-width: 900px) {
            #dkEmployeePage .dk-metrics,
            #dkEmployeePage .dk-last-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            #dkEmployeePage .dk-topbar,
            #dkEmployeePage .dk-content {
                padding-left: 16px;
                padding-right: 16px;
            }
        }
    </style>
</head>

<body id="dkEmployeePage" class="dk-shell">

    <!-- Navbar -->
    <header class="dk-topbar">
        <div class="dk-brand">
            <span class="dk-brand-bar" aria-hidden="true"></span>
            <div>
                <h1 class="dk-brand-title">Sistem Penilaian Jabatan</h1>
                <p class="dk-brand-sub">Dashboard Karyawan</p>
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
                    <span class="dk-account-name" style="display:block;">{{ $karyawan->nama }}</span>
                    <span class="dk-account-jabatan" style="display:block;">{{ $karyawan->jabatan->nama_jabatan }}</span>
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

    <!-- Main Content -->
    <main class="dk-content">

        <!-- Welcome -->
        <section class="dk-welcome">
            <div>
                <h2>Selamat Datang, {{ $karyawan->nama }}</h2>
                <p>Berikut informasi penilaian dan pengembangan jabatan Anda.</p>
            </div>

            <div class="dk-welcome-tags">
                <span class="dk-tag">{{ $karyawan->departemen->nama_departemen }}</span>
                <span class="dk-tag mustard">PT: {{ $karyawan->pt ?: '-' }}</span>
            </div>
        </section>

        <!-- Summary Metrics -->
        <section class="dk-metrics">
            <div class="dk-metric-card">
                <p class="dk-metric-label">Jabatan Saat Ini</p>
                <p class="dk-metric-role">{{ $karyawan->jabatan->nama_jabatan }}</p>
                <p class="dk-metric-sub">{{ $karyawan->departemen->nama_departemen }}</p>
                <p class="dk-metric-sub">PT: {{ $karyawan->pt ?: '-' }}</p>
            </div>

            <div class="dk-metric-card is-mustard">
                <p class="dk-metric-label">Total Assessment</p>
                <p class="dk-metric-num">{{ $totalAssessment }}</p>
            </div>

            <div class="dk-metric-card is-sage">
                <p class="dk-metric-label">Assessment Lulus</p>
                <p class="dk-metric-num sage">{{ $totalLulus }}</p>
            </div>
        </section>

        <!-- Assessment Aktif -->
        <section class="dk-card">
            <div class="dk-card-head is-green">
                <h3>Assessment yang Harus Dikerjakan</h3>
                <p>Selesaikan assessment yang telah diberikan oleh HRD.</p>
            </div>

            <div class="dk-table-wrap">
                <table class="dk-table">
                    <thead>
                        <tr>
                            <th>Assessment</th>
                            <th>Jabatan</th>
                            <th>Skill</th>
                            <th class="dk-center" style="width: 115px;">Standar</th>
                            <th class="dk-center" style="width: 175px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($assessmentAktif as $peserta)
                            <tr>
                                <td><strong>{{ $peserta->assessment->nama_assessment }}</strong></td>
                                <td>{{ $peserta->assessment->jabatan?->nama_jabatan ?? '-' }}</td>
                                <td>{{ $peserta->assessment->soalUpload?->nama_file_tampilan ?? '-' }}</td>
                                <td class="dk-center">
                                    <strong>{{ number_format($peserta->assessment->standar_nilai, 0) }}</strong>
                                </td>
                                <td class="dk-center">
                                    @if ($peserta->status === 'ditugaskan')
                                        <span class="dk-badge ditugaskan">Ditugaskan</span>
                                    @elseif ($peserta->status === 'sedang_mengerjakan')
                                        <span class="dk-badge sedang_mengerjakan">Sedang Mengerjakan</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding: 14px 16px;">
                                    <div class="dk-empty-note">Tidak ada assessment yang harus dikerjakan.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Hasil Assessment Terakhir -->
        <section class="dk-card">
            <div class="dk-card-head is-mustard">
                <h3>Hasil Assessment Terakhir</h3>
            </div>

            <div class="dk-card-body">
                @if ($hasilAssessmentTerakhir)
                    <div class="dk-last-grid">
                        <div class="dk-last-box">
                            <p class="dk-last-label">Assessment</p>
                            <p class="dk-last-title">
                                {{ $hasilAssessmentTerakhir->assessmentPeserta->assessment->nama_assessment }}
                            </p>
                        </div>

                        <div class="dk-last-box" style="text-align: center;">
                            <p class="dk-last-label">Nilai</p>
                            <p class="dk-last-score">
                                {{ number_format($hasilAssessmentTerakhir->nilai_akhir, 2) }}
                            </p>
                        </div>

                        <div class="dk-last-box" style="text-align: center;">
                            <p class="dk-last-label">Standar</p>
                            <p class="dk-last-score">
                                {{ number_format($hasilAssessmentTerakhir->standar_nilai, 2) }}
                            </p>
                        </div>

                        <div class="dk-last-box" style="text-align: center;">
                            <p class="dk-last-label">Status</p>
                            <div style="margin-top: 4px;">
                                @if ($hasilAssessmentTerakhir->status === 'lulus')
                                    <span class="dk-badge lulus">Lulus</span>
                                @else
                                    <span class="dk-badge tidak_lulus">Tidak Lulus</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="dk-empty-note">Belum ada hasil assessment.</div>
                @endif
            </div>
        </section>

        <!-- Status Pengembangan -->
        <section class="dk-card">
            <div class="dk-card-head">
                <h3>Status Pengembangan</h3>
            </div>

            <div class="dk-table-wrap">
                <table class="dk-table">
                    <thead>
                        <tr>
                            <th>Jenis</th>
                            <th>Tujuan</th>
                            <th class="dk-center" style="width: 125px;">Nilai</th>
                            <th class="dk-center" style="width: 155px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengajuanTerbaru as $pengajuan)
                            <tr>
                                <td><strong>{{ ucwords(str_replace('_', ' ', $pengajuan->jenis_pengajuan)) }}</strong></td>
                                <td>
                                    @if ($pengajuan->jenis_pengajuan === 'peningkatan_skill')
                                        {{ $pengajuan->skill?->nama_skill ?? '-' }}
                                    @else
                                        {{ $pengajuan->jabatanTujuan?->nama_jabatan ?? '-' }}
                                    @endif
                                </td>
                                <td class="dk-center">
                                    <strong>{{ number_format($pengajuan->hasilAssessment?->nilai_akhir ?? 0, 2) }}</strong>
                                </td>
                                <td class="dk-center">
                                    @if ($pengajuan->status === 'disetujui')
                                        <span class="dk-badge disetujui">Disetujui</span>
                                    @elseif ($pengajuan->status === 'ditolak')
                                        <span class="dk-badge ditolak">Ditolak</span>
                                    @elseif ($pengajuan->status === 'diproses')
                                        <span class="dk-badge diproses">Diproses</span>
                                    @else
                                        <span class="dk-badge diajukan">Diajukan</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding: 14px 16px;">
                                    <div class="dk-empty-note">Belum ada pengajuan pengembangan.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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