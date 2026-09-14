<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Dashboard Karyawan - Sistem Penilaian Jabatan
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-200">

        <div class="max-w-7xl mx-auto px-6 py-4">

            <div class="flex items-center justify-between">

                <div>
                    <h1 class="text-xl font-bold text-gray-800">
                        Sistem Penilaian Jabatan
                    </h1>

                    <p class="text-sm text-gray-500">
                        Dashboard Karyawan
                    </p>
                </div>

                <div class="flex items-center gap-4">

                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-800">
                            {{ $karyawan->nama }}
                        </p>

                        <p class="text-xs text-gray-500">
                            {{ $karyawan->jabatan->nama_jabatan }}
                        </p>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="px-4 py-2 text-sm bg-red-500 text-white rounded-lg hover:bg-red-600"
                        >
                            Logout
                        </button>
                    </form>

                </div>

            </div>

        </div>

    </nav>


    <!-- Content -->
    <main class="max-w-7xl mx-auto px-6 py-8">

        <!-- Welcome -->
        <div class="mb-8">

            <h2 class="text-2xl font-bold text-gray-800">
                Selamat Datang, {{ $karyawan->nama }}
            </h2>

            <p class="text-gray-500 mt-1">
                Berikut informasi penilaian dan pengembangan jabatan Anda.
            </p>

        </div>


        <!-- Informasi Utama -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Jabatan -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

                <p class="text-sm text-gray-500">
                    Jabatan Saat Ini
                </p>

                <p class="text-xl font-bold text-gray-800 mt-2">
                    {{ $karyawan->jabatan->nama_jabatan }}
                </p>

                <p class="text-sm text-gray-500 mt-1">
                    {{ $karyawan->departemen->nama_departemen }}
                </p>

            </div>


            <!-- Total Assessment -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

                <p class="text-sm text-gray-500">
                    Total Assessment
                </p>

                <p class="text-3xl font-bold text-blue-600 mt-2">
                    {{ $totalAssessment }}
                </p>

            </div>


            <!-- Assessment Lulus -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

                <p class="text-sm text-gray-500">
                    Assessment Lulus
                </p>

                <p class="text-3xl font-bold text-green-600 mt-2">
                    {{ $totalLulus }}
                </p>

            </div>

        </div>


        <!-- Assessment Aktif -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-8">

            <div class="px-6 py-5 border-b border-gray-200">

                <h3 class="text-lg font-semibold text-gray-800">
                    Assessment yang Harus Dikerjakan
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Selesaikan assessment yang telah diberikan oleh HRD.
                </p>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="px-6 py-3 text-left font-semibold text-gray-600">
                                Assessment
                            </th>

                            <th class="px-6 py-3 text-left font-semibold text-gray-600">
                                Jabatan
                            </th>

                            <th class="px-6 py-3 text-left font-semibold text-gray-600">
                                Skill
                            </th>

                            <th class="px-6 py-3 text-left font-semibold text-gray-600">
                                Standar
                            </th>

                            <th class="px-6 py-3 text-left font-semibold text-gray-600">
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse ($assessmentAktif as $peserta)

                            <tr class="hover:bg-gray-50">

                                <td class="px-6 py-4 font-medium text-gray-800">
                                    {{ $peserta->assessment->nama_assessment }}
                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    {{ $peserta->assessment->jabatan?->nama_jabatan ?? '-' }}
                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    {{ $peserta->assessment->skill?->nama_skill ?? 'Umum' }}
                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    {{ number_format($peserta->assessment->standar_nilai, 0) }}
                                </td>

                                <td class="px-6 py-4">

                                    @if ($peserta->status === 'ditugaskan')

                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                            Ditugaskan
                                        </span>

                                    @elseif ($peserta->status === 'sedang_mengerjakan')

                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                            Sedang Mengerjakan
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="5"
                                    class="px-6 py-8 text-center text-gray-500"
                                >
                                    Tidak ada assessment yang harus dikerjakan.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        <!-- Hasil Assessment Terakhir -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-8">

            <div class="px-6 py-5 border-b border-gray-200">

                <h3 class="text-lg font-semibold text-gray-800">
                    Hasil Assessment Terakhir
                </h3>

            </div>


            <div class="p-6">

                @if ($hasilAssessmentTerakhir)

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                        <div>
                            <p class="text-sm text-gray-500">
                                Assessment
                            </p>

                            <p class="font-semibold text-gray-800 mt-1">
                                {{ $hasilAssessmentTerakhir->assessmentPeserta->assessment->nama_assessment }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                Nilai
                            </p>

                            <p class="text-2xl font-bold text-gray-800 mt-1">
                                {{ number_format($hasilAssessmentTerakhir->nilai_akhir, 2) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                Standar
                            </p>

                            <p class="text-2xl font-bold text-gray-800 mt-1">
                                {{ number_format($hasilAssessmentTerakhir->standar_nilai, 2) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                Status
                            </p>

                            <div class="mt-2">

                                @if ($hasilAssessmentTerakhir->status === 'lulus')

                                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-700">
                                        Lulus
                                    </span>

                                @else

                                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-700">
                                        Tidak Lulus
                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>

                @else

                    <p class="text-gray-500">
                        Belum ada hasil assessment.
                    </p>

                @endif

            </div>

        </div>


        <!-- Pengajuan Pengembangan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-8">

            <div class="px-6 py-5 border-b border-gray-200">

                <h3 class="text-lg font-semibold text-gray-800">
                    Status Pengembangan
                </h3>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="px-6 py-3 text-left font-semibold text-gray-600">
                                Jenis
                            </th>

                            <th class="px-6 py-3 text-left font-semibold text-gray-600">
                                Tujuan
                            </th>

                            <th class="px-6 py-3 text-left font-semibold text-gray-600">
                                Nilai
                            </th>

                            <th class="px-6 py-3 text-left font-semibold text-gray-600">
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse ($pengajuanTerbaru as $pengajuan)

                            <tr class="hover:bg-gray-50">

                                <td class="px-6 py-4 text-gray-800">
                                    {{ ucwords(str_replace('_', ' ', $pengajuan->jenis_pengajuan)) }}
                                </td>

                                <td class="px-6 py-4 text-gray-600">

                                    @if ($pengajuan->jenis_pengajuan === 'peningkatan_skill')

                                        {{ $pengajuan->skill?->nama_skill ?? '-' }}

                                    @else

                                        {{ $pengajuan->jabatanTujuan?->nama_jabatan ?? '-' }}

                                    @endif

                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    {{ number_format($pengajuan->hasilAssessment?->nilai_akhir ?? 0, 2) }}
                                </td>

                                <td class="px-6 py-4">

                                    @if ($pengajuan->status === 'disetujui')

                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            Disetujui
                                        </span>

                                    @elseif ($pengajuan->status === 'ditolak')

                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                            Ditolak
                                        </span>

                                    @elseif ($pengajuan->status === 'diproses')

                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                            Diproses
                                        </span>

                                    @else

                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                            Diajukan
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="4"
                                    class="px-6 py-8 text-center text-gray-500"
                                >
                                    Belum ada pengajuan pengembangan.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </main>

</body>

</html>