<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration.
     */
    public function up(): void
    {
        Schema::create('blockchain_records', function (Blueprint $table) {
            $table->id();

            // Nomor urut block
            $table->unsignedBigInteger('block_number')->unique();

            // Jenis data yang dicatat
            // Contoh: hasil_assessment, pengajuan_pengembangan
            $table->string('entity_type');

            // ID data asal dari tabel transaksi
            $table->unsignedBigInteger('entity_id');

            // Hash dari data yang dicatat
            $table->string('data_hash', 64);

            // Hash block sebelumnya
            // NULL hanya untuk block pertama (genesis block)
            $table->string('previous_hash', 64)->nullable();

            // Hash block saat ini
            $table->string('block_hash', 64)->unique();

            $table->timestamps();

            // Index agar pencarian berdasarkan entity lebih cepat
            $table->index(['entity_type', 'entity_id']);
        });
    }

    /**
     * Rollback migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('blockchain_records');
    }
};