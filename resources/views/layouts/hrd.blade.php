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

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            color: #111827;
        }

        .app {
            display: flex;
            min-height: 100vh;
        }

        /* ==========================================
           SIDEBAR
        ========================================== */

        .sidebar {
            width: 250px;
            background: #111827;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            overflow-y: auto;
            z-index: 1000;
        }

        .brand {
            padding: 22px 20px;
            border-bottom: 1px solid #374151;
        }

        .brand h2 {
            margin: 0;
            font-size: 20px;
        }

        .brand p {
            margin: 6px 0 0;
            color: #9ca3af;
            font-size: 13px;
        }

        .menu-title {
            padding: 18px 20px 8px;
            color: #9ca3af;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .menu {
            padding: 0 10px 10px;
        }

        .menu a {
            display: block;
            padding: 11px 12px;
            color: #d1d5db;
            text-decoration: none;
            border-radius: 7px;
            margin-bottom: 3px;
            font-size: 14px;
        }

        .menu a:hover {
            background: #1f2937;
            color: white;
        }

        .menu a.active {
            background: #2563eb;
            color: white;
        }

        .logout-section {
            margin-top: 20px;
            padding: 15px 10px;
            border-top: 1px solid #374151;
        }

        .logout-button {
            width: 100%;
            padding: 11px 12px;
            background: #dc2626;
            color: white;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            font-size: 14px;
        }

        .logout-button:hover {
            background: #b91c1c;
        }

        /* ==========================================
           MAIN CONTENT
        ========================================== */

        .main {
            margin-left: 250px;
            width: calc(100% - 250px);
            min-height: 100vh;
        }

        .topbar {
            height: 70px;
            background: white;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 25px;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .page-title {
            font-size: 18px;
            font-weight: bold;
        }

        .user-info {
            text-align: right;
        }

        .user-info strong {
            display: block;
            font-size: 14px;
        }

        .user-info span {
            font-size: 12px;
            color: #6b7280;
        }

        .content {
            padding: 25px;
        }

        /* ==========================================
           GLOBAL COMPONENT
        ========================================== */

        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }

        @media (max-width: 900px) {

            .sidebar {
                width: 220px;
            }

            .main {
                margin-left: 220px;
                width: calc(100% - 220px);
            }

        }

        @media (max-width: 700px) {

            .sidebar {
                position: static;
                width: 100%;
                min-height: auto;
            }

            .app {
                display: block;
            }

            .main {
                margin-left: 0;
                width: 100%;
            }

            .topbar {
                position: static;
            }

        }

    </style>

    @stack('styles')

</head>

<body>

<div class="app">


    {{-- ================================================= --}}
    {{-- SIDEBAR --}}
    {{-- ================================================= --}}

    <aside class="sidebar">

        <div class="brand">

            <h2>SIM Penilaian</h2>

            <p>
                Panel HRD
            </p>

        </div>


        <div class="menu-title">
            Utama
        </div>

        <div class="menu">

            <a
                href="{{ route('hrd.dashboard') }}"
                class="{{ request()->routeIs('hrd.dashboard')
                    ? 'active'
                    : '' }}"
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
                class="{{ request()->routeIs('hrd.karyawan.*')
                    ? 'active'
                    : '' }}"
            >
                Manajemen Karyawan
            </a>

            <a 
                href="{{ route('hrd.departemen.index') }}"
                class="{{ request()->routeIs('hrd.departemen.*')
                    ? 'active'
                    : '' }}"
            >
                Manajemen Departemen
            </a>

            <a 
                href="{{ route('hrd.jabatan.index') }}"
                class="{{ request()->routeIs('hrd.jabatan.*')
                    ? 'active'
                    : '' }}"
            >
                Manajemen Jabatan
            </a>

            <a 
                href="{{ route('hrd.skill.index') }}"
                class="{{ request()->routeIs('hrd.skill.*')
                    ? 'active'
                    : '' }}"
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
                class="{{ request()->routeIs('hrd.soal.*')
                    ? 'active'
                    : '' }}"
            >
                Bank Soal
            </a>

            <a
                href="{{ route('hrd.assessment.index') }}"
                class="{{ request()->routeIs('hrd.assessment.*')
                    ? 'active'
                    : '' }}"
            >
                Assessment
            </a>

            <a
                href="{{ route('hrd.hasil-assessment.index') }}"
                class="{{ request()->routeIs('hrd.hasil-assessment.*')
                    ? 'active'
                    : '' }}"
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
                class="{{ request()->routeIs('hrd.kenaikan-jabatan.*')
                    ? 'active'
                    : '' }}"
            >
                Kenaikan Jabatan
            </a>

            <a
                href="{{ route('hrd.pemindahan-jabatan.index') }}"
                class="{{ request()->routeIs('hrd.pemindahan-jabatan.*')
                    ? 'active'
                    : '' }}"
            >
                Pemindahan Jabatan
            </a>

            <a
                href="{{ route('hrd.peningkatan-skill.index') }}"
                class="{{ request()->routeIs('hrd.peningkatan-skill.*')
                    ? 'active'
                    : '' }}"
            >
                Peningkatan Skill
            </a>

        </div>


        <div class="menu-title">
            Riwayat & Laporan
        </div>

        <div class="menu">

            <a
                href="{{ route('hrd.riwayat.index') }}"
                class="{{ request()->routeIs('hrd.riwayat.*')
                    ? 'active'
                    : '' }}"
            >
                Riwayat
            </a>

            <a
                href="{{ route('hrd.laporan.index') }}"
                class="{{ request()->routeIs('hrd.laporan.*')
                ? 'active'
                : '' }}"
            >
                
            Laporan & Analitik
            </a>

        </div>


        <div class="logout-section">

            <form
                method="POST"
                action="{{ route('logout') }}"
            >

                @csrf

                <button
                    type="submit"
                    class="logout-button"
                >
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

            <div class="page-title">

                @yield(
                    'page_title',
                    'Dashboard HRD'
                )

            </div>


            <div class="user-info">

                <strong>
                    {{ auth()->user()->name }}
                </strong>

                <span>
                    HRD
                </span>

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


@stack('scripts')

</body>

</html>