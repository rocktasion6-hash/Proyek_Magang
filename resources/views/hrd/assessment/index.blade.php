<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Assessment - HRD</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="bg-gray-100 min-h-screen">

    <nav class="bg-white border-b border-gray-200">

        <div class="max-w-7xl mx-auto px-6 py-4">

            <div class="flex items-center justify-between">

                <div>

                    <h1 class="text-xl font-bold text-gray-800">
                        Sistem Penilaian Jabatan
                    </h1>

                    <p class="text-sm text-gray-500">
                        Assessment
                    </p>

                </div>

                <div class="flex items-center gap-4">

                    <span class="text-sm text-gray-600">
                        {{ auth()->user()->name }}
                    </span>

                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm"
                        >
                            Logout
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </nav>


    <main class="max-w-7xl mx-auto px-6 py-8">

        @if (session('success'))

            <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 mb-6">
                {{ session('success') }}
            </div>

        @endif


        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

            <div>

                <h2 class="text-2xl font-bold text-gray-800">
                    Assessment
                </h2>

                <p class="text-gray-500 mt-1">
                    Kelola assessment dan peserta ujian.
                </p>

            </div>


            <a
                href="{{ route('hrd.assessment.create') }}"
                class="inline-flex items-center justify-center px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium"
            >
                + Buat Assessment
            </a>

        </div>


        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="px-6 py-3 text-left">
                                #
                            </th>

                            <th class="px-6 py-3 text-left">
                                Assessment
                            </th>

                            <th class="px-6 py-3 text-left">
                                Jabatan
                            </th>

                            <th class="px-6 py-3 text-left">
                                Standar
                            </th>

                            <th class="px-6 py-3 text-left">
                                Durasi
                            </th>

                            <th class="px-6 py-3 text-left">
                                Peserta
                            </th>

                            <th class="px-6 py-3 text-left">
                                Status
                            </th>

                            <th class="px-6 py-3 text-left">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse ($assessments as $index => $assessment)

                            <tr class="hover:bg-gray-50">

                                <td class="px-6 py-4">
                                    {{ $assessments->firstItem() + $index }}
                                </td>


                                <td class="px-6 py-4">

                                    <p class="font-semibold text-gray-800">
                                        {{ $assessment->nama_assessment }}
                                    </p>

                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $assessment->skill?->nama_skill ?? 'Umum' }}
                                    </p>

                                </td>


                                <td class="px-6 py-4 text-gray-600">
                                    {{ $assessment->jabatan?->nama_jabatan ?? '-' }}
                                </td>


                                <td class="px-6 py-4 text-gray-600">
                                    {{ number_format($assessment->standar_nilai, 0) }}
                                </td>


                                <td class="px-6 py-4 text-gray-600">
                                    {{ $assessment->durasi }} menit
                                </td>


                                <td class="px-6 py-4 text-gray-600">
                                    {{ $assessment->peserta_count }} orang
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


                                <td class="px-6 py-4">

                                    <a
                                        href="{{ route('hrd.assessment.show', $assessment) }}"
                                        class="text-blue-600 hover:text-blue-800 font-medium"
                                    >
                                        Detail
                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="8"
                                    class="px-6 py-10 text-center text-gray-500"
                                >
                                    Belum ada assessment.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            @if ($assessments->hasPages())

                <div class="px-6 py-4 border-t border-gray-200">

                    {{ $assessments->links() }}

                </div>

            @endif

        </div>

    </main>

</body>

</html>