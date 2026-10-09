<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportingController extends Controller
{
    /**
     * GET /api/reporting
     * Ringkasan dashboard Data Warehouse.
     */
    public function index(): JsonResponse
    {
        return $this->ringkasan();
    }

    /**
     * GET /api/reporting/ringkasan
     */
    public function ringkasan(): JsonResponse
    {
        $database = DB::connection('warehouse');
        $schema = Schema::connection('warehouse');

        $tables = [
            'dim_waktu',
            'dim_departemen',
            'dim_jabatan',
            'dim_skill',
            'dim_karyawan',
            'fact_assessment',
            'fact_pengembangan',
            'fact_riwayat_jabatan',
            'fact_riwayat_skill',
        ];

        // Pastikan tabel Data Warehouse tersedia.
        $missingTables = [];

        foreach ($tables as $table) {
            if (!$schema->hasTable($table)) {
                $missingTables[] = $table;
            }
        }

        if (!empty($missingTables)) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Terdapat tabel Data Warehouse yang belum tersedia.',
                'missing_tables' => $missingTables,
            ], 500);
        }

        // Hitung jumlah record pada setiap tabel.
        $jumlahRecord = [];

        foreach ($tables as $table) {
            $jumlahRecord[$table] =
                $database->table($table)->count();
        }

        // Statistik hasil assessment.
        $assessmentColumns =
            $schema->getColumnListing('fact_assessment');

        $rataRataNilai = null;

        if (in_array('nilai_akhir', $assessmentColumns)) {
            $rataRataNilai = $database
                ->table('fact_assessment')
                ->avg('nilai_akhir');

            $rataRataNilai = $rataRataNilai !== null
                ? round((float) $rataRataNilai, 2)
                : null;
        }

        $statusAssessment = $this->hitungBerdasarkanKolom(
            'fact_assessment',
            'status'
        );

        // Statistik data pengembangan.
        $pengembanganPerJenis = $this->hitungBerdasarkanKolom(
            'fact_pengembangan',
            'jenis_pengajuan'
        );

        $pengembanganPerStatus = $this->hitungBerdasarkanKolom(
            'fact_pengembangan',
            'status'
        );

        return response()->json([
            'success' => true,
            'message' =>
                'Ringkasan Data Warehouse berhasil diambil.',

            'source' => 'dw_penilaian_jabatan',

            'data' => [
                'jumlah_record' => $jumlahRecord,

                'assessment' => [
                    'total_record' =>
                        $jumlahRecord['fact_assessment'],

                    'rata_rata_nilai' =>
                        $rataRataNilai,

                    'berdasarkan_status' =>
                        $statusAssessment,
                ],

                'pengembangan' => [
                    'total_record' =>
                        $jumlahRecord['fact_pengembangan'],

                    'berdasarkan_jenis' =>
                        $pengembanganPerJenis,

                    'berdasarkan_status' =>
                        $pengembanganPerStatus,
                ],

                'riwayat' => [
                    'total_riwayat_jabatan' =>
                        $jumlahRecord['fact_riwayat_jabatan'],

                    'total_riwayat_skill' =>
                        $jumlahRecord['fact_riwayat_skill'],
                ],
            ],
        ]);
    }

    /**
     * GET /api/reporting/assessment
     * Membaca fact_assessment dengan pagination.
     */
    public function assessment(Request $request): JsonResponse
    {
        return $this->ambilDataWarehouse(
            $request,
            'fact_assessment',
            'Data laporan assessment berhasil diambil.'
        );
    }

    /**
     * GET /api/reporting/pengembangan
     * Membaca fact_pengembangan dengan pagination.
     */
    public function pengembangan(Request $request): JsonResponse
    {
        return $this->ambilDataWarehouse(
            $request,
            'fact_pengembangan',
            'Data laporan pengembangan berhasil diambil.'
        );
    }

    /**
     * GET /api/reporting/riwayat-jabatan
     */
    public function riwayatJabatan(Request $request): JsonResponse
    {
        return $this->ambilDataWarehouse(
            $request,
            'fact_riwayat_jabatan',
            'Data riwayat jabatan berhasil diambil.'
        );
    }

    /**
     * GET /api/reporting/riwayat-skill
     */
    public function riwayatSkill(Request $request): JsonResponse
    {
        return $this->ambilDataWarehouse(
            $request,
            'fact_riwayat_skill',
            'Data riwayat skill berhasil diambil.'
        );
    }

    /**
     * Menghitung jumlah record berdasarkan suatu kolom.
     */
    private function hitungBerdasarkanKolom(
        string $table,
        string $column
    ): array {
        $schema = Schema::connection('warehouse');

        if (!$schema->hasTable($table)) {
            return [];
        }

        $columns = $schema->getColumnListing($table);

        if (!in_array($column, $columns)) {
            return [];
        }

        return DB::connection('warehouse')
            ->table($table)
            ->select(
                $column,
                DB::raw('COUNT(*) as total')
            )
            ->groupBy($column)
            ->orderBy($column)
            ->get()
            ->map(function ($row) use ($column) {
                return [
                    'label' => $row->{$column},
                    'total' => (int) $row->total,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Membaca tabel warehouse dengan pagination.
     */
    private function ambilDataWarehouse(
        Request $request,
        string $table,
        string $message
    ): JsonResponse {
        $validated = $request->validate([
            'page' => [
                'sometimes',
                'integer',
                'min:1',
            ],

            'per_page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
            ],
        ]);

        $schema = Schema::connection('warehouse');

        if (!$schema->hasTable($table)) {
            return response()->json([
                'success' => false,
                'message' =>
                    "Tabel {$table} tidak ditemukan di Data Warehouse.",
            ], 500);
        }

        $page = (int) ($validated['page'] ?? 1);
        $perPage = (int) ($validated['per_page'] ?? 20);

        $database = DB::connection('warehouse');
        $columns = $schema->getColumnListing($table);

        $query = $database->table($table);

        // Urutkan berdasarkan kolom pertama tabel jika tersedia.
        if (!empty($columns)) {
            $query->orderByDesc($columns[0]);
        }

        $total = $database->table($table)->count();

        $rows = $query
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return response()->json([
            'success' => true,
            'message' => $message,

            'source' => 'dw_penilaian_jabatan',
            'table' => $table,

            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => max(
                    1,
                    (int) ceil($total / $perPage)
                ),
            ],

            'data' => $rows,
        ]);
    }
}