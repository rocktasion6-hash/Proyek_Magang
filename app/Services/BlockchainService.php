<?php

namespace App\Services;

use App\Models\BlockchainRecord;
use Carbon\Carbon;
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
            $lastBlock = BlockchainRecord::query()
                ->orderByDesc('block_number')
                ->lockForUpdate()
                ->first();

            $blockNumber = $lastBlock
                ? $lastBlock->block_number + 1
                : 1;

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

            $previousHash = $lastBlock?->block_hash;

            $blockHash = $this->calculateBlockHash(
                $blockNumber,
                $entityType,
                $entityId,
                $dataHash,
                $previousHash
            );

            return BlockchainRecord::create([
                'block_number'   => $blockNumber,
                'entity_type'    => $entityType,
                'entity_id'      => $entityId,
                'data_hash'      => $dataHash,
                'data_snapshot'  => $normalizedData,
                'previous_hash'  => $previousHash,
                'block_hash'     => $blockHash,
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
                'message'    => 'Genesis Block',
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
                'valid'         => true,
                'message'       => 'Blockchain belum memiliki block.',
                'total_blocks'  => 0,
                'invalid_block' => null,
            ];
        }

        $previousBlock = null;

        foreach ($blocks as $block) {

            if ($block->block_number === 1) {
                if ($block->previous_hash !== null) {
                    return [
                        'valid'         => false,
                        'message'       => 'Genesis block memiliki previous_hash.',
                        'total_blocks'  => $blocks->count(),
                        'invalid_block' => $block->block_number,
                    ];
                }
            }

            if ($previousBlock) {
                if ($block->block_number !== $previousBlock->block_number + 1) {
                    return [
                        'valid'         => false,
                        'message'       => 'Urutan block tidak valid.',
                        'total_blocks'  => $blocks->count(),
                        'invalid_block' => $block->block_number,
                    ];
                }

                if ($block->previous_hash !== $previousBlock->block_hash) {
                    return [
                        'valid'         => false,
                        'message'       => 'previous_hash tidak cocok.',
                        'total_blocks'  => $blocks->count(),
                        'invalid_block' => $block->block_number,
                    ];
                }
            }

            $calculatedHash = $this->calculateBlockHash(
                $block->block_number,
                $block->entity_type,
                $block->entity_id,
                $block->data_hash,
                $block->previous_hash
            );

            if ($block->block_hash !== $calculatedHash) {
                return [
                    'valid'         => false,
                    'message'       => 'block_hash tidak cocok.',
                    'total_blocks'  => $blocks->count(),
                    'invalid_block' => $block->block_number,
                ];
            }

            $previousBlock = $block;
        }

        return [
            'valid'         => true,
            'message'       => 'Seluruh blockchain valid.',
            'total_blocks'  => $blocks->count(),
            'invalid_block' => null,
        ];
    }

    /**
     * Memverifikasi apakah data sumber masih sama
     * dengan data yang saat pertama kali dicatat.
     */
    public function verifyDataIntegrity(): array
    {
        $blocks = BlockchainRecord::query()
            ->orderBy('block_number')
            ->get();

        $checked       = 0;
        $skipped       = 0;
        $invalidBlocks = [];

        foreach ($blocks as $block) {

            if ($block->entity_type === 'genesis') {
                $skipped++;
                continue;
            }

            if (!$block->data_snapshot) {
                $skipped++;
                continue;
            }

            $currentData = $this->getCurrentSourceData($block);

            if ($currentData === null) {
                $invalidBlocks[] = [
                    'block_number' => $block->block_number,
                    'reason'       => 'Data sumber tidak ditemukan atau jenis entity tidak dikenali.',
                ];
                continue;
            }

            $normalizedCurrentData = $this->normalizeData($currentData);

            $currentDataHash = hash(
                'sha256',
                json_encode(
                    $normalizedCurrentData,
                    JSON_UNESCAPED_UNICODE |
                    JSON_UNESCAPED_SLASHES |
                    JSON_PRESERVE_ZERO_FRACTION
                )
            );

            $snapshot = $this->normalizeData($block->data_snapshot);

            $snapshotHash = hash(
                'sha256',
                json_encode(
                    $snapshot,
                    JSON_UNESCAPED_UNICODE |
                    JSON_UNESCAPED_SLASHES |
                    JSON_PRESERVE_ZERO_FRACTION
                )
            );

            $snapshotValid = $snapshotHash === $block->data_hash;
            $sourceValid   = $currentDataHash === $block->data_hash;

            if (!$snapshotValid || !$sourceValid) {
                $invalidBlocks[] = [
                    'block_number' => $block->block_number,
                    'reason'       => match (true) {
                        !$snapshotValid && !$sourceValid =>
                            'Snapshot dan data sumber tidak sesuai dengan data_hash.',
                        !$snapshotValid =>
                            'Snapshot blockchain tidak sesuai dengan data_hash.',
                        default =>
                            'Data sumber telah berubah.',
                    },
                ];
                continue;
            }

            $checked++;
        }

        return [
            'valid'          => empty($invalidBlocks),
            'total_blocks'   => $blocks->count(),
            'checked'        => $checked,
            'skipped'        => $skipped,
            'invalid_blocks' => $invalidBlocks,
        ];
    }

    /**
     * Mengambil kembali data sumber sesuai jenis event blockchain.
     */
    private function getCurrentSourceData(BlockchainRecord $block): ?array
    {
        switch ($block->entity_type) {

            case 'hasil_assessment':
                $hasil = \App\Models\HasilAssessment::with([
                    'assessmentPeserta.assessment',
                ])->find($block->entity_id);

                if (!$hasil) {
                    return null;
                }

                $peserta = $hasil->assessmentPeserta;

                if (!$peserta) {
                    return null;
                }

                $assessment = $peserta->assessment;

                return [
                    'assessment_peserta_id' => $peserta->id,
                    'assessment_id'         => $assessment?->id,
                    'karyawan_id'           => $peserta->karyawan_id,
                    'nilai_akhir'           => $hasil->nilai_akhir,
                    'standar_nilai'         => $hasil->standar_nilai,
                    'status'                => $hasil->status,
                    'tanggal_ujian'         => $this->formatDateTime($hasil->tanggal_ujian),
                    'waktu_mulai'           => $this->formatDateTime($hasil->waktu_mulai),
                    'waktu_selesai'         => $this->formatDateTime($hasil->waktu_selesai),
                ];

            case 'pengajuan_pengembangan':
                $pengajuan = \App\Models\PengajuanPengembangan::find($block->entity_id);

                if (!$pengajuan) {
                    return null;
                }

                $data = [
                    'karyawan_id'          => $pengajuan->karyawan_id,
                    'hasil_assessment_id'  => $pengajuan->hasil_assessment_id,
                    'jenis_pengajuan'      => $pengajuan->jenis_pengajuan,
                    'jabatan_asal_id'      => $pengajuan->jabatan_asal_id,
                    'jabatan_tujuan_id'    => $pengajuan->jabatan_tujuan_id,
                    'skill_id'             => $pengajuan->skill_id,
                    'status'               => 'diajukan',
                    'tanggal_pengajuan'    => $pengajuan->tanggal_pengajuan,
                    'tanggal_keputusan'    => null,
                    'catatan'              => $pengajuan->catatan,
                ];

                if ($pengajuan->jenis_pengajuan === 'peningkatan_skill') {
                    $data['level_sebelum'] = $pengajuan->level_sebelum;
                    $data['level_sesudah'] = $pengajuan->level_sesudah;
                }

                return $data;

            case 'keputusan_kenaikan_jabatan':
            case 'keputusan_pemindahan_jabatan':
                $pengajuan = \App\Models\PengajuanPengembangan::find($block->entity_id);

                if (!$pengajuan) {
                    return null;
                }

                return [
                    'pengajuan_pengembangan_id' => $pengajuan->id,
                    'karyawan_id'               => $pengajuan->karyawan_id,
                    'hasil_assessment_id'       => $pengajuan->hasil_assessment_id,
                    'jabatan_asal_id'           => $pengajuan->jabatan_asal_id,
                    'jabatan_tujuan_id'         => $pengajuan->jabatan_tujuan_id,
                    'status'                    => $pengajuan->status,
                    'tanggal_keputusan'         => $pengajuan->tanggal_keputusan,
                ];

            case 'keputusan_peningkatan_skill':
                $pengajuan = \App\Models\PengajuanPengembangan::find($block->entity_id);

                if (!$pengajuan) {
                    return null;
                }

                $data = [
                    'pengajuan_pengembangan_id' => $pengajuan->id,
                    'karyawan_id'               => $pengajuan->karyawan_id,
                    'hasil_assessment_id'       => $pengajuan->hasil_assessment_id,
                    'skill_id'                  => $pengajuan->skill_id,
                    'level_sebelum'             => $pengajuan->level_sebelum,
                    'level_sesudah'             => $pengajuan->level_sesudah,
                    'status'                    => $pengajuan->status,
                    'tanggal_keputusan'         => $pengajuan->tanggal_keputusan,
                ];

                if ($pengajuan->status === 'ditolak') {
                    $data['catatan'] = $pengajuan->catatan;
                }

                return $data;
        }

        return null;
    }

    /**
     * Format datetime ke string konsisten untuk hashing.
     */
    private function formatDateTime(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return Carbon::parse($value)->format('Y-m-d H:i:s');
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