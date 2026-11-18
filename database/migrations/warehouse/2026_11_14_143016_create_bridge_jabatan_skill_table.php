<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'warehouse';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('warehouse')->create('bridge_jabatan_skill', function (Blueprint $table) {
            $table->id();

            $table->foreignId('jabatan_id')
                ->constrained('dim_jabatan')
                ->cascadeOnDelete();

            $table->foreignId('skill_id')
                ->constrained('dim_skill')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('level_dibutuhkan');

            $table->unique(['jabatan_id', 'skill_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('warehouse')->dropIfExists('bridge_jabatan_skill');
    }
};
