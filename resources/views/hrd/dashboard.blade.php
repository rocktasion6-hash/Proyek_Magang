@extends('layouts.hrd')

@section('title', 'Dashboard HRD - Sistem Penilaian Jabatan')
@section('page_title', 'Dashboard')

@section('content')

    <!-- Content -->
    <div class="max-w-7xl mx-auto space-y-6">


        <!-- Bagian Statistik Utama: Tampilan Panel Terpadu (Bukan Kotak Terpisah) -->
        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm space-y-6">
            <div class="flex justify-between items-center border-b border-gray-100 pb-4">
                <div>
                   <h2 class="text-xl font-bold text-gray-800">
                    Rekap Hasil
                </h2>
                     <p class="text-gray-500 mt-0.5 text-sm">
                    Ringkasan data sistem penilaian dan pengembangan jabatan.</p>
                </div>
                <span class="bg-[#f4f6f4] text-[#2c4a3e] px-3.5 py-1.5 rounded-lg font-medium text-xs border border-[#e2e8e4]">
                {{ date('d M Y') }}
                </span>
            </div>

          <!-- Grid Data Metrik (Clean Minimalist List) -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-6">
    @php
        $acuanDasar = max($totalKaryawan, $totalJabatan, $totalSkill, $assessmentAktif, 1);
        $persenKaryawan = min(($totalKaryawan / $acuanDasar) * 100, 100);
        $persenJabatan = min(($totalJabatan / $acuanDasar) * 100, 100);
        $persenSkill = min(($totalSkill / $acuanDasar) * 100, 100);
        $persenAssessment = min(($assessmentAktif / $acuanDasar) * 100, 100);
    @endphp

    <div class="border-r border-gray-100 last:border-r-0 pr-4">
        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Total Karyawan</p>
        <p class="text-3xl font-extrabold text-[#2c4a3e] mt-2">{{ $totalKaryawan }}</p>
        <div class="w-full bg-[#f4f6f4] h-1.5 rounded-full mt-3 overflow-hidden">
            <div class="bg-[#2c4a3e] h-full rounded-full" style="width: {{ $persenKaryawan }}%;"></div>
        </div>
    </div>

    <div class="border-r border-gray-100 last:border-r-0 pr-4">
        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Total Jabatan</p>
        <p class="text-3xl font-extrabold text-[#2c4a3e] mt-2">{{ $totalJabatan }}</p>
        <div class="w-full bg-[#f4f6f4] h-1.5 rounded-full mt-3 overflow-hidden">
            <div class="bg-[#4a7c59] h-full rounded-full" style="width: {{ $persenJabatan }}%;"></div>
        </div>
    </div>

    <div class="border-r border-gray-100 last:border-r-0 pr-4">
        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Total Skill</p>
        <p class="text-3xl font-extrabold text-[#2c4a3e] mt-2">{{ $totalSkill }}</p>
        <div class="w-full bg-[#f4f6f4] h-1.5 rounded-full mt-3 overflow-hidden">
            <div class="bg-[#e3b535] h-full rounded-full" style="width: {{ $persenSkill }}%;"></div>
        </div>
    </div>

    <div>
        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Assessment Aktif</p>
        <p class="text-3xl font-extrabold text-[#2c4a3e] mt-2">{{ $assessmentAktif }}</p>
        <div class="w-full bg-[#f4f6f4] h-1.5 rounded-full mt-3 overflow-hidden">
            <div class="bg-[#4a7c59] h-full rounded-full" style="width: {{ $persenAssessment }}%;"></div>
        </div>
    </div>
</div>


        <!-- Bagian Sekunder: Layout Asimetris (Performa & Pengajuan) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Panel Kiri: Aktivitas & Kelulusan (Lebar 7 Kolom) -->
            <div class="lg:col-span-7 bg-white rounded-xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-6">
                        Distribusi Penilaian & Departemen
                    </h3>

                    <div class="space-y-5">
                        <div>
                            <div class="flex justify-between text-xs font-semibold text-gray-600 mb-1.5">
                                <span>Karyawan Lulus Assessment</span>
                                <span class="font-bold text-[#2c4a3e]">{{ $karyawanLulus }} Orang</span>
                            </div>
                            <div class="w-full bg-gray-100 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-[#4a7c59] h-full rounded-full transition-all" style="width: {{ min(($karyawanLulus / max($totalKaryawan, 1)) * 100, 100) }}%;"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs font-semibold text-gray-600 mb-1.5">
                                <span>Total Departemen Terdaftar</span>
                                <span class="font-bold text-[#2c4a3e]">{{ $totalDepartemen }} Unit</span>
                            </div>
                            <div class="w-full bg-gray-100 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-[#2c4a3e] h-full rounded-full transition-all" style="width: 80%;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 text-xs text-gray-400 flex justify-between">
                    <span>Status Evaluasi Berkala</span>
                    <span class="text-gray-600 font-medium">Normal</span>
                </div>
            </div>


            <!-- Panel Kanan: Status Pengajuan Menunggu (Lebar 5 Kolom) -->
            <div class="lg:col-span-5 bg-[#fef9e7] rounded-xl p-6 border border-[#f5eab0] shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold uppercase tracking-wider text-[#9a7711] bg-white px-2.5 py-1 rounded border border-[#f5eab0]">
                            Perhatian Khusus
                        </span>
                        <span class="text-xs text-[#9a7711] font-semibold">Pending</span>
                    </div>

                    <div class="mt-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Pengajuan Menunggu Verifikasi</p>
                        <p class="text-4xl font-extrabold text-[#9a7711] mt-2">{{ $pengajuanMenunggu }} <span class="text-sm font-normal text-gray-600">berkas</span></p>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-[#f5eab0]/60 text-xs text-gray-600">
                    Memerlukan tinjauan data lanjutan dari tim HRD terkait mutasi atau kenaikan jabatan.
                </div>
            </div>

        </div>


        <!-- Tabel Assessment Terbaru -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-[#f4f6f4]">
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-700">
                    Assessment Terbaru
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50/70 text-gray-500 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5 text-left font-semibold">Nama Assessment</th>
                            <th class="px-6 py-3.5 text-left font-semibold">Jabatan</th>
                            <th class="px-6 py-3.5 text-left font-semibold">Bank Soal</th>
                            <th class="px-6 py-3.5 text-left font-semibold">Standar Nilai</th>
                            <th class="px-6 py-3.5 text-left font-semibold">Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse ($assessmentTerbaru as $assessment)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-800">
                                    {{ $assessment->nama_assessment }}
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $assessment->jabatan?->nama_jabatan ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $assessment->soalUpload?->nama_file_tampilan ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 font-semibold">
                                    {{ number_format($assessment->standar_nilai, 0) }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($assessment->status === 'aktif')
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#f4f6f4] text-[#2c4a3e] border border-[#e2e8e4]">
                                            Aktif
                                        </span>
                                    @elseif ($assessment->status === 'draft')
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                            Draft
                                        </span>
                                    @elseif ($assessment->status === 'selesai')
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#fef9e7] text-[#9a7711] border border-[#f5eab0]">
                                            Selesai
                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                            Dibatalkan
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-400">
                                    Belum ada data assessment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

@endsection