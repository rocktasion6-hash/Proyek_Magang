<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blockchain_records', function (Blueprint $table) {
            $table->json('data_snapshot')
                ->nullable()
                ->after('data_hash');
        });
    }

    public function down(): void
    {
        Schema::table('blockchain_records', function (Blueprint $table) {
            $table->dropColumn('data_snapshot');
        });
    }
};