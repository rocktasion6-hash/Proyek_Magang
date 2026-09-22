<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockchainRecord extends Model
{
    protected $table = 'blockchain_records';

    protected $fillable = [
        'block_number',
        'entity_type',
        'entity_id',
        'data_hash',
        'previous_hash',
        'block_hash',
    ];

    protected $casts = [
        'block_number' => 'integer',
        'entity_id' => 'integer',
    ];
}