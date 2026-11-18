<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('soal_uploads', function (Blueprint $table) {
            $table->foreignId('departemen_id')
                ->nullable()
                ->constrained('departemens')
                ->restrictOnDelete();
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->foreignId('departemen_id')
                ->nullable()
                ->constrained('departemens')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropForeign(['departemen_id']);
            $table->dropColumn('departemen_id');
        });

        Schema::table('soal_uploads', function (Blueprint $table) {
            $table->dropForeign(['departemen_id']);
            $table->dropColumn('departemen_id');
        });
    }
};
