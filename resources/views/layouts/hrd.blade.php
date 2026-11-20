<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'HRD')
    </title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #fdfdfc;
            color: #111827;
            overflow-x: hidden;
        }

        .app {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* ==========================================
           SIDEBAR (TERSEMBUNYI AWALNYA)
        ========================================== */

        .sidebar {
            width: 260px;
            background: #2c4a3e;
            color: #fdfdfc;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            overflow-y: auto;
            z-index: 1000;
            transform: translateX(-100%);
            transition: transform 0.22s ease;
            border-right: 1px solid rgba(253, 253, 252, 0.14);
        }

        .sidebar.is-open {
            transform: translateX(0);
        }

        .brand {
            padding: 20px;
            border-bottom: 1px solid rgba(253, 253, 252, 0.14);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .brand h2 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #fdfdfc;
        }

        .brand p {
            margin: 4px 0 0;
            color: #f4f6f4;
            font-size: 12px;
            opacity: 0.85;
        }

        .close-sidebar-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #4a7c59;
            color: #fdfdfc;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: 700;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .close-sidebar-btn:hover {
            background: #e3b535;
            color: #111827;
        }

        .menu-title {
            padding: 16px 20px 6px;
            color: #e3b535;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .menu {
            padding: 0 12px 8px;
        }

        .menu a {
            display: block;
            padding: 10px 12px;
            color: #f4f6f4;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 3px;
            font-size: 14px;
            font-weight: 500;
        }

        .menu a:hover {
            background: #4a7c59;
            color: #fdfdfc;
        }

        .menu a.active {
            background: #4a7c59;
            color: #fdfdfc;
            font-weight: 700;
            border-left: 3px solid #e3b535;
        }

        .logout-section {
            margin-top: 16px;
            padding: 14px 12px 20px;
            border-top: 1px solid rgba(253, 253, 252, 0.14);
        }

        .logout-button {
            width: 100%;
            padding: 10px 12px;
            background: #991b1b;
            color: #fdfdfc;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }

        .logout-button:hover {
            background: #7f1d1d;
        }

        /* ==========================================
           MAIN CONTENT (MENYESUAIKAN KE SAMPING)
        ========================================== */

        .main {
            margin-left: 0;
            width: 100%;
            min-height: 100vh;
            background: #fdfdfc;
            transition: margin-left 0.22s ease, width 0.22s ease;
        }

        @media (min-width: 701px) {
            .app.sidebar-open .main {
                margin-left: 260px;
                width: calc(100% - 260px);
            }
        }

        @media (max-width: 700px) {
            .sidebar {
                width: 230px;
            }
            .app.sidebar-open .main {
                margin-left: 230px;
                width: calc(100% - 230px);
            }
        }

        .topbar {
            height: 68px;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            position: sticky;
            top: 0;
            z-index: 900;
            gap: 12px;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .hamburger-btn {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: #f4f6f4;
            color: #2c4a3e;
            border: 1px solid #e2e8e4;
            cursor: pointer;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            flex-shrink: 0;
        }

        .hamburger-btn:hover {
            background: #2c4a3e;
            color: #fdfdfc;
            border-color: #2c4a3e;
        }

        .hamburger-line {
            width: 16px;
            height: 2px;
            background: currentColor;
            display: block;
        }

        .page-title {
            font-size: 18px;
            font-weight: 700;
            color: #2c4a3e;
        }

        .content {
            padding: 24px;
        }

        /* ==========================================
           GLOBAL COMPONENT
        ========================================== */

        .card {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .alert-success {
            background: #f4f6f4;
            color: #2c4a3e;
            border: 1px solid #4a7c59;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
    </style>

    @stack('styles')

</head>

<body>

<div id="hrd-app" class="app">

    {{-- ================================================= --}}
    {{-- SIDEBAR --}}
    {{-- ================================================= --}}

    <aside id="hrd-sidebar" class="sidebar">

        <div class="brand">
            <div>
                <h2>SIM Penilaian</h2>
                <p>Panel HRD</p>
            </div>
            <button
                type="button"
                class="close-sidebar-btn"
                onclick="toggleSidebar(false)"
                aria-label="Tutup menu"
            >
                &times;
            </button>
        </div>

        <div class="menu-title">
            Utama
        </div>

        <div class="menu">
            <a
                href="{{ route('hrd.dashboard') }}"
                class="{{ request()->routeIs('hrd.dashboard') ? 'active' : '' }}"
            >
                Dashboard
            </a>
        </div>

        <div class="menu-title">
            Data Master
        </div>

        <div class="menu">
            <a
                href="{{ route('hrd.karyawan.index') }}"
                class="{{ request()->routeIs('hrd.karyawan.*') ? 'active' : '' }}"
            >
                Manajemen Karyawan
            </a>

            <a
                href="{{ route('hrd.departemen.index') }}"
                class="{{ request()->routeIs('hrd.departemen.*') ? 'active' : '' }}"
            >
                Manajemen Departemen
            </a>

            <a
                href="{{ route('hrd.jabatan.index') }}"
                class="{{ request()->routeIs('hrd.jabatan.*') ? 'active' : '' }}"
            >
                Manajemen Jabatan
            </a>

            <a
                href="{{ route('hrd.skill.index') }}"
                class="{{ request()->routeIs('hrd.skill.*') ? 'active' : '' }}"
            >
                Manajemen Skill
            </a>
        </div>

        <div class="menu-title">
            Penilaian
        </div>

        <div class="menu">
            <a
                href="{{ route('hrd.soal.index') }}"
                class="{{ request()->routeIs('hrd.soal.*') ? 'active' : '' }}"
            >
                Bank Soal
            </a>

            <a
                href="{{ route('hrd.assessment.index') }}"
                class="{{ request()->routeIs('hrd.assessment.*') ? 'active' : '' }}"
            >
                Assessment
            </a>

            <a
                href="{{ route('hrd.hasil-assessment.index') }}"
                class="{{ request()->routeIs('hrd.hasil-assessment.*') ? 'active' : '' }}"
            >
                Hasil Penilaian
            </a>
        </div>

        <div class="menu-title">
            Pengembangan
        </div>

        <div class="menu">
            <a
                href="{{ route('hrd.kenaikan-jabatan.index') }}"
                class="{{ request()->routeIs('hrd.kenaikan-jabatan.*') ? 'active' : '' }}"
            >
                Kenaikan Jabatan
            </a>

            <a
                href="{{ route('hrd.pemindahan-jabatan.index') }}"
                class="{{ request()->routeIs('hrd.pemindahan-jabatan.*') ? 'active' : '' }}"
            >
                Pemindahan Jabatan
            </a>

            <a
                href="{{ route('hrd.peningkatan-skill.index') }}"
                class="{{ request()->routeIs('hrd.peningkatan-skill.*') ? 'active' : '' }}"
            >
                Peningkatan Skill
            </a>
        </div>

        <div class="menu-title">
            Riwayat &amp; Laporan
        </div>

        <div class="menu">
            <a
                href="{{ route('hrd.riwayat.index') }}"
                class="{{ request()->routeIs('hrd.riwayat.*') ? 'active' : '' }}"
            >
                Riwayat
            </a>

            <a
                href="{{ route('hrd.laporan.index') }}"
                class="{{ request()->routeIs('hrd.laporan.*') ? 'active' : '' }}"
            >
                Laporan &amp; Analitik
            </a>

            <a
                href="{{ route('hrd.blockchain.index') }}"
                class="{{ request()->routeIs('hrd.blockchain.*') ? 'active' : '' }}"
            >
                Blockchain Explorer
            </a>
        </div>

        <div class="logout-section">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-button">
                    Keluar
                </button>
            </form>
        </div>

    </aside>

    {{-- ================================================= --}}
    {{-- MAIN --}}
    {{-- ================================================= --}}

    <main class="main">

        <header class="topbar">

            <div class="topbar-left">
                <button
                    type="button"
                    class="hamburger-btn"
                    onclick="toggleSidebar()"
                    aria-label="Buka menu navigasi"
                >
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                </button>

                <div class="page-title">
                    @yield('page_title', 'Dashboard HRD')
                </div>
            </div>

            <!-- User Dropdown Menu (ET) di Pojok Kanan Atas -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 focus:outline-none bg-white border border-gray-200 hover:bg-[#f4f6f4] px-3 py-1.5 rounded-lg shadow-sm transition">
                    <div class="text-left hidden sm:block">
                        <strong class="text-sm font-semibold text-gray-800 leading-tight block">
                            {{ auth()->user()->name }}
                        </strong>
                        <span class="text-xs text-gray-500 leading-tight">
                            HRD
                        </span>
                    </div>
                    <svg class="w-4 h-4 text-gray-500 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Dropdown Box (Muncul saat ditekan, berisi tombol Logout) -->
                <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg py-1 z-50 text-sm text-gray-700">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100 text-red-600 font-medium cursor-pointer">
                            Log out
                        </button>
                    </form>
                </div>
            </div>

        </header>

        <section class="content">

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')

        </section>

    </main>

</div>

<script>
    function toggleSidebar(forceState = null) {
        const app = document.getElementById('hrd-app');
        const sidebar = document.getElementById('hrd-sidebar');
        const isOpen = sidebar.classList.contains('is-open');
        const shouldOpen = forceState !== null ? forceState : !isOpen;

        if (shouldOpen) {
            sidebar.classList.add('is-open');
            app.classList.add('sidebar-open');
        } else {
            sidebar.classList.remove('is-open');
            app.classList.remove('sidebar-open');
        }
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            toggleSidebar(false);
        }
    });
</script>

@stack('scripts')

</body>

</html>