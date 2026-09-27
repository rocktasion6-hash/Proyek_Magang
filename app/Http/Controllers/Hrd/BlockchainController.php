<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\BlockchainRecord;
use App\Services\BlockchainService;
use Illuminate\Http\Request;

class BlockchainController extends Controller
{
    /**
     * Menampilkan daftar seluruh block.
     */
    public function index(Request $request)
    {
        $query = BlockchainRecord::query();

        /*
        |--------------------------------------------------------------------------
        | Filter entity type
        |--------------------------------------------------------------------------
        */

        if ($request->filled('entity_type')) {
            $query->where(
                'entity_type',
                $request->entity_type
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Pencarian berdasarkan entity ID / nomor block
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where(
                    'entity_id',
                    'like',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'block_number',
                    'like',
                    '%' . $search . '%'
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Daftar block
        |--------------------------------------------------------------------------
        */

        $blocks = $query
            ->orderByDesc('block_number')
            ->paginate(15)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Jenis entity yang tersedia
        |--------------------------------------------------------------------------
        */

        $entityTypes = BlockchainRecord::query()
            ->select('entity_type')
            ->distinct()
            ->orderBy('entity_type')
            ->pluck('entity_type');

        /*
        |--------------------------------------------------------------------------
        | Statistik blockchain
        |--------------------------------------------------------------------------
        */

        $totalBlocks = BlockchainRecord::count();

        $genesisBlock = BlockchainRecord::query()
            ->where('block_number', 1)
            ->first();

        $lastBlock = BlockchainRecord::query()
            ->orderByDesc('block_number')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Verifikasi chain
        |--------------------------------------------------------------------------
        */

        $blockchainService = app(BlockchainService::class);

        $verification = $blockchainService->verifyChain();

        return view(
            'hrd.blockhain.index',
            compact(
                'blocks',
                'entityTypes',
                'totalBlocks',
                'genesisBlock',
                'lastBlock',
                'verification'
            )
        );
    }

    /**
     * Menjalankan verifikasi blockchain secara khusus.
     */
    public function verify()
    {
        $blockchainService = app(BlockchainService::class);

        $verification = $blockchainService->verifyChain();

        if ($verification['valid']) {
            return back()->with(
                'success',
                $verification['message']
                . ' Total block: '
                . $verification['total_blocks']
                . '.'
            );
        }

        return back()->with(
            'error',
            $verification['message']
            . ' Block bermasalah: #'
            . ($verification['invalid_block'] ?? '-')
            . '.'
        );
    }

    public function verifyData()
    {
        $blockchainService = app(BlockchainService::class);

        $verification = $blockchainService->verifyDataIntegrity();

        if ($verification['valid']) {

            $message =
                'Integritas data sumber valid. '
                . 'Diperiksa: '
                . $verification['checked']
                . ' block.';

        if ($verification['skipped'] > 0) {
            $message .=
                ' '
                . $verification['skipped']
                . ' block belum memiliki snapshot data.';
        }

        return back()->with(
            'success',
            $message
        );
    }

    $firstInvalid = $verification['invalid_blocks'][0] ?? null;

        return back()->with(
            'error',
            'Integritas data tidak valid. '
            . 'Block bermasalah: #'
            . ($firstInvalid['block_number'] ?? '-')
            . '. '
            . ($firstInvalid['reason'] ?? '')
        );
    }
}