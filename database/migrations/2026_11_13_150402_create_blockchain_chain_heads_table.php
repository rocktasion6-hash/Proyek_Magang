<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blockchain_chain_heads', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->unsignedBigInteger('block_number')->default(0);
            $table->string('block_hash', 64)->nullable();
            $table->timestamps();
        });

        DB::table('blockchain_chain_heads')->insert([
            'id' => 1,
            'block_number' => 0,
            'block_hash' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $lastBlock = DB::table('blockchain_records')
            ->orderByDesc('block_number')
            ->first(['block_number', 'block_hash']);

        if ($lastBlock) {
            DB::table('blockchain_chain_heads')
                ->where('id', 1)
                ->update([
                    'block_number' => $lastBlock->block_number,
                    'block_hash' => $lastBlock->block_hash,
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blockchain_chain_heads');
    }
};
