<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard HRD - Sistem Penilaian Jabatan</title>

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
                        Dashboard HRD
                    </p>
                </div>

                <div class="flex items-center gap-4">

                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-800">
                            {{ auth()->user()->name }}
                        </p>

                        <p class="text-xs text-gray-500">
                            HRD
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

        <!-- Header -->
        <div class="mb-8">

            <h2 class="text-2xl font-bold text-gray-800">
                Dashboard HRD
            </h2>

            <p class="text-gray-500 mt-1">
                Ringkasan data sistem penilaian dan pengembangan jabatan.
            </p>

        </div>


        <!-- Statistik -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <!-- Total Karyawan -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">

                <p class="text-sm text-gray-500">
                    Total Karyawan
                </p>

                <p class="text-3xl font-bold text-gray-800 mt-2">
                    {{ $totalKaryawan }}
                </p>

            </div>


            <!-- Total Jabatan -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">

                <p class="text-sm text-gray-500">
                    Total Jabatan
                </p>

                <p class="text-3xl font-bold text-gray-800 mt-2">
                    {{ $totalJabatan }}
                </p>

            </div>


            <!-- Total Skill -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">

                <p class="text-sm text-gray-500">
                    Total Skill
                </p>

                <p class="text-3xl font-bold text-gray-800 mt-2">
                    {{ $totalSkill }}
                </p>

            </div>


            <!-- Assessment Aktif -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">

                <p class="text-sm text-gray-500">
                    Assessment Aktif
                </p>

                <p class="text-3xl font-bold text-gray-800 mt-2">
                    {{ $assessmentAktif }}
                </p>

            </div>

        </div>


        <!-- Statistik kedua -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">

            <!-- Karyawan Lulus -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">

                <p class="text-sm text-gray-500">
                    Karyawan Lulus Assessment
                </p>

                <p class="text-3xl font-bold text-green-600 mt-2">
                    {{ $karyawanLulus }}
                </p>

            </div>


            <!-- Pengajuan Menunggu -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">

                <p class="text-sm text-gray-500">
                    Pengajuan Menunggu
                </p>

                <p class="text-3xl font-bold text-orange-500 mt-2">
                    {{ $pengajuanMenunggu }}
                </p>

            </div>


            <!-- Departemen -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">

                <p class="text-sm text-gray-500">
                    Total Departemen
                </p>

                <p class="text-3xl font-bold text-blue-600 mt-2">
                    {{ $totalDepartemen }}
                </p>

            </div>

        </div>


        <!-- Assessment Terbaru -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-8">

            <div class="px-6 py-5 border-b border-gray-200">

                <h3 class="text-lg font-semibold text-gray-800">
                    Assessment Terbaru
                </h3>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="px-6 py-3 text-left font-semibold text-gray-600">
                                Nama Assessment
                            </th>

                            <th class="px-6 py-3 text-left font-semibold text-gray-600">
                                Jabatan
                            </th>

                            <th class="px-6 py-3 text-left font-semibold text-gray-600">
                                Skill
                            </th>

                            <th class="px-6 py-3 text-left font-semibold text-gray-600">
                                Standar Nilai
                            </th>

                            <th class="px-6 py-3 text-left font-semibold text-gray-600">
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse ($assessmentTerbaru as $assessment)

                            <tr class="hover:bg-gray-50">

                                <td class="px-6 py-4 font-medium text-gray-800">
                                    {{ $assessment->nama_assessment }}
                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    {{ $assessment->jabatan?->nama_jabatan ?? '-' }}
                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    {{ $assessment->skill?->nama_skill ?? 'Umum' }}
                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    {{ number_format($assessment->standar_nilai, 0) }}
                                </td>

                                <td class="px-6 py-4">

                                    @if ($assessment->status === 'aktif')

                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            Aktif
                                        </span>

                                    @elseif ($assessment->status === 'draft')

                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                            Draft
                                        </span>

                                    @elseif ($assessment->status === 'selesai')

                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                            Selesai
                                        </span>

                                    @else

                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                            Dibatalkan
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
                                    Belum ada assessment.
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