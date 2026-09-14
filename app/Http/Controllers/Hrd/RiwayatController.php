<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\RiwayatJabatan;
use App\Models\RiwayatSkill;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::with([
            'departemen',
            'jabatan',
        ])->where('status', 'aktif');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('nik', 'like', '%' . $search . '%');
            });
        }

        $karyawans = $query
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view(
            'hrd.riwayat.index',
            compact('karyawans')
        );
    }

    public function show(Karyawan $karyawan)
    {
        $riwayatJabatans = RiwayatJabatan::with([
            'jabatan',
            'pengajuanPengembangan',
            'hasilAssessment',
        ])
            ->where('karyawan_id', $karyawan->id)
            ->orderByDesc('tanggal_mulai')
            ->orderByDesc('id')
            ->get();

        $riwayatSkills = RiwayatSkill::with([
            'skill',
            'pengajuanPengembangan',
            'hasilAssessment',
        ])
            ->where('karyawan_id', $karyawan->id)
            ->orderByDesc('tanggal_perubahan')
            ->orderByDesc('id')
            ->get();

        $karyawan->load([
            'departemen',
            'jabatan',
            'skills',
        ]);

        return view(
            'hrd.riwayat.show',
            compact(
                'karyawan',
                'riwayatJabatans',
                'riwayatSkills'
            )
        );
    }
}