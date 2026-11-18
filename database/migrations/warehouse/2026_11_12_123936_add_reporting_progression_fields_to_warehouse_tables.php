<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'warehouse';

    public function up(): void
    {
        Schema::connection('warehouse')->table('dim_jabatan', function (Blueprint $table) {
            $table->string('tipe_jabatan')->nullable()->after('level_jabatan');
            $table->unsignedTinyInteger('urutan_struktural')
                ->nullable()
                ->after('tipe_jabatan');
        });

        Schema::connection('warehouse')->table('fact_pengembangan', function (Blueprint $table) {
            $table->date('tanggal_keputusan')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::connection('warehouse')->table('fact_pengembangan', function (Blueprint $table) {
            $table->dropColumn('tanggal_keputusan');
        });

        Schema::connection('warehouse')->table('dim_jabatan', function (Blueprint $table) {
            $table->dropColumn(['tipe_jabatan', 'urutan_struktural']);
        });
    }
};
