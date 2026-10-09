<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\JsonResponse;

class KaryawanController extends Controller
{
    /**
     * Menampilkan semua karyawan.
     */
    public function index(): JsonResponse
    {
        $karyawans = Karyawan::with([
            'departemen',
            'jabatan',
        ])
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data karyawan berhasil diambil.',
            'data' => $karyawans,
        ]);
    }

    /**
     * Menampilkan satu karyawan berdasarkan ID.
     */
    public function show(int $id): JsonResponse
    {
        $karyawan = Karyawan::with([
            'departemen',
            'jabatan',
            'skills',
        ])->find($id);

        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data karyawan berhasil diambil.',
            'data' => $karyawan,
        ]);
    }
}