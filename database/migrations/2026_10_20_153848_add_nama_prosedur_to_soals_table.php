<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('soals', 'nama_prosedur')) {
            Schema::table('soals', function (Blueprint $table) {
                $table->string('nama_prosedur')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('soals', 'nama_prosedur')) {
            Schema::table('soals', function (Blueprint $table) {
                $table->dropColumn('nama_prosedur');
            });
        }
    }
};
