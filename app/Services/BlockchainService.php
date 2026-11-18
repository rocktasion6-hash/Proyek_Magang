<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentPeserta;
use App\Models\BlockchainRecord;
use App\Models\HasilAssessment;
use App\Models\Karyawan;
use App\Models\PengajuanPengembangan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use LogicException;

class BlockchainService
{
    /**
     * Menambahkan block berantai. Genesis dibuat otomatis untuk rantai baru.
     */
    public function addBlock(
        string $entityType,
        int $entityId,
        array $data
    ): BlockchainRecord {
        return DB::transaction(function () use ($entityType, $entityId, $data) {
            $head = $this->lockChainHead();
            $this->assertHeadMatchesBlocks($head);

            if ((int) $head->block_number === 0 && $entityType !== 'genesis') {
                $this->appendBlock($head, 'genesis', 0, [
                    'message' => 'Genesis Block',
                    'created_at' => now()->toDateTimeString(),
                ]);
            }

            return $this->appendBlock($head, $entityType, $entityId, $data);
        });
    }

    /**
     * Mencatat event beserta pelaku, waktu, dan snapshot sumbernya.
     */
    public function addEvent(
        string $eventType,
        int $entityId,
        array $sourceData,
        bool $sourceDeleted = false
    ): BlockchainRecord {
        $snapshot = [
            'event_type' => $eventType,
            'recorded_at' => now()->toIso8601String(),
            'actor_user_id' => Auth::id(),
            'source_data' => $this->normalizeData($sourceData),
        ];

        if ($sourceDeleted) {
            $snapshot['source_deleted'] = true;
        }

        return $this->addBlock($eventType, $entityId, $snapshot);
    }

    public function recordAssessmentEvent(
        string $eventType,
        Assessment $assessment,
        bool $sourceDeleted = false
    ): BlockchainRecord {
        return $this->addEvent(
            $eventType,
            $assessment->id,
            $this->assessmentData($assessment),
            $sourceDeleted
        );
    }

    public function recordAssessmentParticipantEvent(
        string $eventType,
        AssessmentPeserta $participant,
        bool $sourceDeleted = false
    ): BlockchainRecord {
        return $this->addEvent(
            $eventType,
            $participant->id,
            $this->assessmentParticipantData($participant),
            $sourceDeleted
        );
    }

    public function recordAssessmentResultEvent(
        HasilAssessment $result,
        string $eventType = 'hasil_assessment',
        bool $sourceDeleted = false
    ): BlockchainRecord {
        $participant = $result->assessmentPeserta;

        if (! $participant) {
            throw new LogicException(
                'Peserta assessment tidak ditemukan untuk hasil yang akan dicatat.'
            );
        }

        return $this->addEvent($eventType, $result->id, [
            'assessment_peserta_id' => $participant->id,
            'assessment_id' => $participant->assessment_id,
            'karyawan_id' => $participant->karyawan_id,
            'nilai_akhir' => $result->nilai_akhir,
            'standar_nilai' => $result->standar_nilai,
            'status' => $result->status,
            'tanggal_ujian' => $this->formatDateTime($result->tanggal_ujian),
            'waktu_mulai' => $this->formatDateTime($result->waktu_mulai),
            'waktu_selesai' => $this->formatDateTime($result->waktu_selesai),
        ], $sourceDeleted);
    }

    public function recordKaryawanDeletionEvent(
        Karyawan $employee
    ): BlockchainRecord {
        return $this->addEvent(
            'karyawan_dihapus',
            $employee->id,
            [
                'karyawan_id' => $employee->id,
                'departemen_id' => $employee->departemen_id,
                'jabatan_id' => $employee->jabatan_id,
                'status' => $employee->status,
            ],
            true
        );
    }

    public function recordPengembanganEvent(
        string $eventType,
        PengajuanPengembangan $application,
        bool $sourceDeleted = false
    ): BlockchainRecord {
        return $this->addEvent(
            $eventType,
            $application->id,
            $this->pengembanganData($application),
            $sourceDeleted
        );
    }

    /**
     * Membuat Genesis Block hanya jika rantai masih kosong.
     */
    public function createGenesisBlock(): BlockchainRecord
    {
        return DB::transaction(function () {
            $head = $this->lockChainHead();
            $this->assertHeadMatchesBlocks($head);

            if ((int) $head->block_number === 0) {
                return $this->appendBlock($head, 'genesis', 0, [
                    'message' => 'Genesis Block',
                    'created_at' => now()->toDateTimeString(),
                ]);
            }

            $genesis = BlockchainRecord::query()
                ->where('block_number', 1)
                ->first();

            if (! $genesis || $genesis->entity_type !== 'genesis') {
                throw new LogicException(
                    'Genesis tidak dapat dibuat di atas riwayat yang sudah ada. Pulihkan block asli terlebih dahulu.'
                );
            }

            return $genesis;
        });
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
     * Memeriksa urutan, tautan hash, hash block, dan metadata head.
     */
    public function verifyChain(): array
    {
        $blocks = BlockchainRecord::query()
            ->orderBy('block_number')
            ->get();

        $head = DB::table('blockchain_chain_heads')
            ->where('id', 1)
            ->first();

        if (! $head) {
            return $this->chainFailure(
                'Metadata head blockchain tidak ditemukan.',
                $blocks->last()?->block_number
            );
        }

        if ($blocks->isEmpty()) {
            $validEmptyHead = (int) $head->block_number === 0
                && $head->block_hash === null;

            return [
                'valid' => $validEmptyHead,
                'message' => $validEmptyHead
                    ? 'Blockchain belum memiliki block.'
                    : 'Head blockchain tidak cocok dengan block terakhir.',
                'total_blocks' => 0,
                'invalid_block' => $validEmptyHead ? null : (int) $head->block_number,
            ];
        }

        $previousBlock = null;

        foreach ($blocks as $block) {
            if ($previousBlock === null && $block->block_number !== 1) {
                return $this->chainFailure(
                    'Nomor block pertama harus 1.',
                    $block->block_number,
                    $blocks->count()
                );
            }

            if ($block->block_number === 1 && $block->previous_hash !== null) {
                return $this->chainFailure(
                    'Genesis block memiliki previous_hash.',
                    $block->block_number,
                    $blocks->count()
                );
            }

            if ($previousBlock) {
                if ($block->block_number !== $previousBlock->block_number + 1) {
                    return $this->chainFailure(
                        'Urutan block tidak valid.',
                        $block->block_number,
                        $blocks->count()
                    );
                }

                if ($block->previous_hash !== $previousBlock->block_hash) {
                    return $this->chainFailure(
                        'previous_hash tidak cocok.',
                        $block->block_number,
                        $blocks->count()
                    );
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
                return $this->chainFailure(
                    'block_hash tidak cocok.',
                    $block->block_number,
                    $blocks->count()
                );
            }

            $previousBlock = $block;
        }

        $lastBlock = $blocks->last();

        if (
            (int) $head->block_number !== $lastBlock->block_number
            || $head->block_hash !== $lastBlock->block_hash
        ) {
            return $this->chainFailure(
                'Head blockchain tidak cocok dengan block terakhir.',
                $lastBlock->block_number,
                $blocks->count()
            );
        }

        return [
            'valid' => true,
            'message' => 'Seluruh blockchain valid.',
            'total_blocks' => $blocks->count(),
            'invalid_block' => null,
        ];
    }

    /**
     * Memeriksa hash snapshot dan membandingkan event terakhir dengan data aktif.
     */
    public function verifyDataIntegrity(): array
    {
        $blocks = BlockchainRecord::query()
            ->orderBy('block_number')
            ->get();

        $latestBlockByEntity = [];

        foreach ($blocks as $block) {
            $entityKey = $this->getIntegrityEntityKey($block);

            if ($entityKey !== null) {
                $latestBlockByEntity[$entityKey] = $block->block_number;
            }
        }

        $checked = 0;
        $skipped = 0;
        $invalidBlocks = [];

        foreach ($blocks as $block) {
            if ($block->entity_type === 'genesis') {
                continue;
            }

            if ($block->data_snapshot === null) {
                $skipped++;

                continue;
            }

            $snapshot = $this->normalizeData($block->data_snapshot);
            $snapshotHash = $this->hashData($snapshot);

            if ($snapshotHash !== $block->data_hash) {
                $invalidBlocks[] = [
                    'block_number' => $block->block_number,
                    'reason' => 'Snapshot blockchain tidak sesuai dengan data_hash.',
                ];

                continue;
            }

            $checked++;
            $entityKey = $this->getIntegrityEntityKey($block);

            if (
                $entityKey !== null
                && $latestBlockByEntity[$entityKey] !== $block->block_number
            ) {
                continue;
            }

            $sourceSnapshot = $this->getSnapshotSourceData($snapshot);

            if ($sourceSnapshot === null) {
                $invalidBlocks[] = [
                    'block_number' => $block->block_number,
                    'reason' => 'Format snapshot data sumber tidak valid.',
                ];

                continue;
            }

            $currentData = $this->getCurrentSourceData($block);

            if (($snapshot['source_deleted'] ?? false) === true) {
                if ($currentData !== null) {
                    $invalidBlocks[] = [
                        'block_number' => $block->block_number,
                        'reason' => 'Data sumber masih ada setelah event penghapusan.',
                    ];
                }

                continue;
            }

            if ($currentData === null) {
                $invalidBlocks[] = [
                    'block_number' => $block->block_number,
                    'reason' => 'Data sumber tidak ditemukan atau jenis event tidak dikenali.',
                ];

                continue;
            }

            $currentSource = array_intersect_key(
                $currentData,
                $sourceSnapshot
            );

            foreach ($currentSource as $key => $value) {
                $snapshotValue = $sourceSnapshot[$key] ?? null;

                if (
                    is_int($value)
                    && is_string($snapshotValue)
                    && preg_match('/^-?\d+$/D', $snapshotValue) === 1
                ) {
                    $sourceSnapshot[$key] = (int) $snapshotValue;
                    $snapshotValue = $sourceSnapshot[$key];
                }

                if (
                    $value instanceof Carbon
                    && is_string($snapshotValue)
                    && preg_match('/^\d{4}-\d{2}-\d{2}$/', $snapshotValue) === 1
                ) {
                    $currentSource[$key] = $value->toDateString();
                }
            }

            if ($this->hashData($currentSource) !== $this->hashData($sourceSnapshot)) {
                $invalidBlocks[] = [
                    'block_number' => $block->block_number,
                    'reason' => 'Data sumber telah berubah.',
                ];
            }
        }

        return [
            'valid' => empty($invalidBlocks),
            'total_blocks' => $blocks->count(),
            'checked' => $checked,
            'skipped' => $skipped,
            'invalid_blocks' => $invalidBlocks,
        ];
    }

    private function lockChainHead(): object
    {
        $head = DB::table('blockchain_chain_heads')
            ->where('id', 1)
            ->lockForUpdate()
            ->first();

        if (! $head) {
            throw new LogicException(
                'Metadata head blockchain tidak ditemukan. Jalankan migration terlebih dahulu.'
            );
        }

        return $head;
    }

    private function assertHeadMatchesBlocks(object $head): void
    {
        $lastBlock = BlockchainRecord::query()
            ->orderByDesc('block_number')
            ->first();

        $headNumber = (int) $head->block_number;
        $lastBlockNumber = $lastBlock?->block_number ?? 0;
        $lastBlockHash = $lastBlock?->block_hash;

        if (
            $headNumber !== $lastBlockNumber
            || $head->block_hash !== $lastBlockHash
        ) {
            throw new LogicException(
                'Head blockchain tidak cocok dengan block terakhir. Pulihkan metadata dari riwayat asli sebelum menambah block.'
            );
        }
    }

    private function appendBlock(
        object $head,
        string $entityType,
        int $entityId,
        array $data
    ): BlockchainRecord {
        $blockNumber = (int) $head->block_number + 1;
        $normalizedData = $this->normalizeData($data);
        $dataHash = $this->hashData($normalizedData);
        $previousHash = $head->block_hash;

        $blockHash = $this->calculateBlockHash(
            $blockNumber,
            $entityType,
            $entityId,
            $dataHash,
            $previousHash
        );

        $block = BlockchainRecord::create([
            'block_number' => $blockNumber,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'data_hash' => $dataHash,
            'data_snapshot' => $normalizedData,
            'previous_hash' => $previousHash,
            'block_hash' => $blockHash,
        ]);

        DB::table('blockchain_chain_heads')
            ->where('id', 1)
            ->update([
                'block_number' => $blockNumber,
                'block_hash' => $blockHash,
                'updated_at' => now(),
            ]);

        $head->block_number = $blockNumber;
        $head->block_hash = $blockHash;

        return $block;
    }

    private function chainFailure(
        string $message,
        ?int $invalidBlock,
        int $totalBlocks = 0
    ): array {
        return [
            'valid' => false,
            'message' => $message,
            'total_blocks' => $totalBlocks,
            'invalid_block' => $invalidBlock,
        ];
    }

    private function getIntegrityEntityKey(BlockchainRecord $block): ?string
    {
        if ($block->entity_type === 'genesis') {
            return null;
        }

        $entityType = match ($block->entity_type) {
            'assessment_dibuat',
            'assessment_diubah',
            'assessment_status_diubah',
            'assessment_dihapus' => 'assessment',
            'assessment_peserta_ditugaskan',
            'assessment_peserta_ditugaskan_ulang',
            'assessment_dimulai',
            'assessment_diselesaikan',
            'assessment_peserta_dihapus' => 'assessment_peserta',
            'hasil_assessment',
            'hasil_assessment_dihapus' => 'hasil_assessment',
            'karyawan_dihapus' => 'karyawan',
            'pengajuan_pengembangan',
            'status_pengajuan_pengembangan',
            'keputusan_kenaikan_jabatan',
            'keputusan_pemindahan_jabatan',
            'keputusan_peningkatan_skill',
            'pengajuan_pengembangan_dihapus' => 'pengajuan_pengembangan',
            default => $block->entity_type,
        };

        return $entityType.':'.$block->entity_id;
    }

    private function getSnapshotSourceData(array $snapshot): ?array
    {
        if (array_key_exists('source_data', $snapshot)) {
            return is_array($snapshot['source_data'])
                ? $snapshot['source_data']
                : null;
        }

        return $snapshot;
    }

    private function getCurrentSourceData(BlockchainRecord $block): ?array
    {
        switch ($block->entity_type) {
            case 'assessment_dibuat':
            case 'assessment_diubah':
            case 'assessment_status_diubah':
            case 'assessment_dihapus':
                $assessment = Assessment::find($block->entity_id);

                return $assessment
                    ? $this->assessmentData($assessment)
                    : null;

            case 'assessment_peserta_ditugaskan':
            case 'assessment_peserta_ditugaskan_ulang':
            case 'assessment_dimulai':
            case 'assessment_diselesaikan':
            case 'assessment_peserta_dihapus':
                $participant = AssessmentPeserta::find($block->entity_id);

                return $participant
                    ? $this->assessmentParticipantData($participant)
                    : null;

            case 'hasil_assessment':
            case 'hasil_assessment_dihapus':
                $result = HasilAssessment::with([
                    'assessmentPeserta.assessment',
                ])->find($block->entity_id);

                if (! $result || ! $result->assessmentPeserta) {
                    return null;
                }

                return [
                    'assessment_peserta_id' => $result->assessmentPeserta->id,
                    'assessment_id' => $result->assessmentPeserta->assessment?->id,
                    'karyawan_id' => $result->assessmentPeserta->karyawan_id,
                    'nilai_akhir' => $result->nilai_akhir,
                    'standar_nilai' => $result->standar_nilai,
                    'status' => $result->status,
                    'tanggal_ujian' => $this->formatDateTime($result->tanggal_ujian),
                    'waktu_mulai' => $this->formatDateTime($result->waktu_mulai),
                    'waktu_selesai' => $this->formatDateTime($result->waktu_selesai),
                ];

            case 'karyawan_dihapus':
                $employee = Karyawan::find($block->entity_id);

                return $employee
                    ? [
                        'karyawan_id' => $employee->id,
                        'departemen_id' => $employee->departemen_id,
                        'jabatan_id' => $employee->jabatan_id,
                        'status' => $employee->status,
                    ]
                    : null;

            case 'pengajuan_pengembangan':
            case 'status_pengajuan_pengembangan':
            case 'keputusan_kenaikan_jabatan':
            case 'keputusan_pemindahan_jabatan':
            case 'keputusan_peningkatan_skill':
            case 'pengajuan_pengembangan_dihapus':
                $application = PengajuanPengembangan::find($block->entity_id);

                return $application
                    ? $this->pengembanganData($application)
                    : null;
        }

        return null;
    }

    private function assessmentData(Assessment $assessment): array
    {
        return [
            'assessment_id' => $assessment->id,
            'nama_assessment' => $assessment->nama_assessment,
            'jabatan_id' => $assessment->jabatan_id,
            'departemen_id' => $assessment->departemen_id,
            'soal_upload_id' => $assessment->soal_upload_id,
            'standar_nilai' => $assessment->standar_nilai,
            'durasi' => $assessment->durasi,
            'tanggal_mulai' => $assessment->tanggal_mulai,
            'tanggal_selesai' => $assessment->tanggal_selesai,
            'status' => $assessment->status,
        ];
    }

    private function assessmentParticipantData(
        AssessmentPeserta $participant
    ): array {
        return [
            'assessment_peserta_id' => $participant->id,
            'assessment_id' => $participant->assessment_id,
            'karyawan_id' => $participant->karyawan_id,
            'percobaan_ke' => $participant->percobaan_ke,
            'status' => $participant->status,
            'waktu_mulai' => $participant->waktu_mulai,
            'waktu_selesai' => $participant->waktu_selesai,
        ];
    }

    private function pengembanganData(
        PengajuanPengembangan $application
    ): array {
        return [
            'pengajuan_pengembangan_id' => $application->id,
            'karyawan_id' => $application->karyawan_id,
            'hasil_assessment_id' => $application->hasil_assessment_id,
            'jenis_pengajuan' => $application->jenis_pengajuan,
            'jabatan_asal_id' => $application->jabatan_asal_id,
            'jabatan_tujuan_id' => $application->jabatan_tujuan_id,
            'skill_id' => $application->skill_id,
            'level_sebelum' => $application->level_sebelum,
            'level_sesudah' => $application->level_sesudah,
            'status' => $application->status,
            'tanggal_pengajuan' => $application->tanggal_pengajuan,
            'tanggal_keputusan' => $application->tanggal_keputusan,
            'catatan' => $application->catatan,
        ];
    }

    private function formatDateTime(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    private function hashData(array $data): string
    {
        return hash(
            'sha256',
            json_encode(
                $this->normalizeData($data),
                JSON_UNESCAPED_UNICODE
                    | JSON_UNESCAPED_SLASHES
                    | JSON_PRESERVE_ZERO_FRACTION
                    | JSON_THROW_ON_ERROR
            )
        );
    }

    private function normalizeData(array $data): array
    {
        ksort($data);

        return $data;
    }
}
