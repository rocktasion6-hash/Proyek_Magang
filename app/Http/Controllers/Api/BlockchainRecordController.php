<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlockchainRecord;
use Illuminate\Http\JsonResponse;

class BlockchainRecordController extends Controller
{
    /**
     * Menampilkan semua blockchain record.
     */
    public function index(): JsonResponse
    {
        $records = BlockchainRecord::query()
            ->orderBy('block_number')
            ->get();

        $data = $records->map(function ($record) {
            return [
                'id' => $record->id,

                'block_number' =>
                    $record->block_number,

                'entity_type' =>
                    $record->entity_type,

                'entity_id' =>
                    $record->entity_id,

                'data_hash' =>
                    $record->data_hash,

                'data_snapshot' =>
                    $record->data_snapshot,

                'previous_hash' =>
                    $record->previous_hash,

                'block_hash' =>
                    $record->block_hash,

                'created_at' =>
                    $record->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'message' =>
                'Data blockchain record berhasil diambil.',
            'total_blocks' =>
                $records->count(),
            'data' => $data,
        ]);
    }

    /**
     * Menampilkan satu blockchain record.
     */
    public function show(int $id): JsonResponse
    {
        $record = BlockchainRecord::find($id);

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Blockchain record tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' =>
                'Detail blockchain record berhasil diambil.',
            'data' => [
                'id' => $record->id,

                'block_number' =>
                    $record->block_number,

                'entity_type' =>
                    $record->entity_type,

                'entity_id' =>
                    $record->entity_id,

                'data_hash' =>
                    $record->data_hash,

                'data_snapshot' =>
                    $record->data_snapshot,

                'previous_hash' =>
                    $record->previous_hash,

                'block_hash' =>
                    $record->block_hash,

                'created_at' =>
                    $record->created_at,

                'updated_at' =>
                    $record->updated_at,
            ],
        ]);
    }
}