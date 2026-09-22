<?php

namespace App\Services;

use App\Models\BlockchainRecord;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BlockchainService
{
    /**
     * Membuat block baru dari suatu data.
     */
    public function addBlock(
        string $entityType,
        int $entityId,
        array $data
    ): BlockchainRecord {
        return DB::transaction(function () use (
            $entityType,
            $entityId,
            $data
        ) {
            /*
            |--------------------------------------------------------------------------
            | Ambil block terakhir
            |--------------------------------------------------------------------------
            */

            $lastBlock = BlockchainRecord::query()
                ->orderByDesc('block_number')
                ->lockForUpdate()
                ->first();

            /*
            |--------------------------------------------------------------------------
            | Tentukan nomor block
            |--------------------------------------------------------------------------
            */

            $blockNumber = $lastBlock
                ? $lastBlock->block_number + 1
                : 1;

            /*
            |--------------------------------------------------------------------------
            | Hash data
            |--------------------------------------------------------------------------
            */

            $normalizedData = $this->normalizeData($data);

            $dataHash = hash(
                'sha256',
                json_encode(
                    $normalizedData,
                    JSON_UNESCAPED_UNICODE |
                    JSON_UNESCAPED_SLASHES |
                    JSON_PRESERVE_ZERO_FRACTION
                )
            );

            /*
            |--------------------------------------------------------------------------
            | Hash block sebelumnya
            |--------------------------------------------------------------------------
            */

            $previousHash = $lastBlock?->block_hash;

            /*
            |--------------------------------------------------------------------------
            | Hash block saat ini
            |--------------------------------------------------------------------------
            */

            $blockHash = $this->calculateBlockHash(
                $blockNumber,
                $entityType,
                $entityId,
                $dataHash,
                $previousHash
            );

            /*
            |--------------------------------------------------------------------------
            | Simpan block
            |--------------------------------------------------------------------------
            */

            return BlockchainRecord::create([
                'block_number' => $blockNumber,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'data_hash' => $dataHash,
                'previous_hash' => $previousHash,
                'block_hash' => $blockHash,
            ]);
        });
    }

    /**
     * Membuat Genesis Block.
     */
    public function createGenesisBlock(): BlockchainRecord
    {
        $existing = BlockchainRecord::query()
            ->where('block_number', 1)
            ->first();

        if ($existing) {
            return $existing;
        }

        return $this->addBlock(
            'genesis',
            0,
            [
                'message' => 'Genesis Block',
                'created_at' => now()->toDateTimeString(),
            ]
        );
    }

    /**
     * Menghitung hash sebuah block.
     */
    public function calculateBlockHash(
        int $blockNumber,
        string $entityType,
        int $entityId,
        string $dataHash,
        ?string $previousHash
    ): string {
        $payload = implode('|', [
            $blockNumber,
            $entityType,
            $entityId,
            $dataHash,
            $previousHash ?? '0',
        ]);

        return hash('sha256', $payload);
    }

    /**
     * Memeriksa integritas seluruh blockchain.
     */
    public function verifyChain(): array
    {
        $blocks = BlockchainRecord::query()
            ->orderBy('block_number')
            ->get();

        if ($blocks->isEmpty()) {
            return [
                'valid' => true,
                'message' => 'Blockchain belum memiliki block.',
                'total_blocks' => 0,
                'invalid_block' => null,
            ];
        }

        $previousBlock = null;

        foreach ($blocks as $block) {

            /*
            |--------------------------------------------------------------------------
            | Verifikasi block pertama
            |--------------------------------------------------------------------------
            */

            if ($block->block_number === 1) {

                if ($block->previous_hash !== null) {
                    return [
                        'valid' => false,
                        'message' => 'Genesis block memiliki previous_hash.',
                        'total_blocks' => $blocks->count(),
                        'invalid_block' => $block->block_number,
                    ];
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Verifikasi hubungan dengan block sebelumnya
            |--------------------------------------------------------------------------
            */

            if ($previousBlock) {

                if ($block->block_number !== $previousBlock->block_number + 1) {
                    return [
                        'valid' => false,
                        'message' => 'Urutan block tidak valid.',
                        'total_blocks' => $blocks->count(),
                        'invalid_block' => $block->block_number,
                    ];
                }

                if ($block->previous_hash !== $previousBlock->block_hash) {
                    return [
                        'valid' => false,
                        'message' => 'previous_hash tidak cocok.',
                        'total_blocks' => $blocks->count(),
                        'invalid_block' => $block->block_number,
                    ];
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Hitung ulang block hash
            |--------------------------------------------------------------------------
            */

            $calculatedHash = $this->calculateBlockHash(
                $block->block_number,
                $block->entity_type,
                $block->entity_id,
                $block->data_hash,
                $block->previous_hash
            );

            if ($block->block_hash !== $calculatedHash) {
                return [
                    'valid' => false,
                    'message' => 'block_hash tidak cocok.',
                    'total_blocks' => $blocks->count(),
                    'invalid_block' => $block->block_number,
                ];
            }

            $previousBlock = $block;
        }

        return [
            'valid' => true,
            'message' => 'Seluruh blockchain valid.',
            'total_blocks' => $blocks->count(),
            'invalid_block' => null,
        ];
    }

    /**
     * Normalisasi array agar hash konsisten.
     */
    private function normalizeData(array $data): array
    {
        ksort($data);

        return $data;
    }
}