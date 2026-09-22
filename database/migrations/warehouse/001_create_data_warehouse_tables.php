<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | DIM WAKTU
        |--------------------------------------------------------------------------
        */

        Schema::create('dim_waktu', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique();
            $table->unsignedSmallInteger('tahun');
            $table->unsignedTinyInteger('bulan');
            $table->string('nama_bulan', 20);
            $table->unsignedTinyInteger('kuartal');
            $table->unsignedTinyInteger('hari');
        });


        /*
        |--------------------------------------------------------------------------
        | DIM DEPARTEMEN
        |--------------------------------------------------------------------------
        */

        Schema::create('dim_departemen', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger(
                'departemen_asal_id'
            )->unique();

            $table->string('nama_departemen');
            $table->text('deskripsi')->nullable();
        });


        /*
        |--------------------------------------------------------------------------
        | DIM JABATAN
        |--------------------------------------------------------------------------
        */

        Schema::create('dim_jabatan', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger(
                'jabatan_asal_id'
            )->unique();

            $table->string('nama_jabatan');
            $table->integer('level_jabatan');
            $table->decimal(
                'standar_nilai',
                5,
                2
            );
            $table->text('deskripsi')->nullable();
        });


        /*
        |--------------------------------------------------------------------------
        | DIM SKILL
        |--------------------------------------------------------------------------
        */

        Schema::create('dim_skill', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger(
                'skill_asal_id'
            )->unique();

            $table->string('nama_skill');
            $table->text('deskripsi')->nullable();
        });


        /*
        |--------------------------------------------------------------------------
        | DIM KARYAWAN
        |--------------------------------------------------------------------------
        */

        Schema::create('dim_karyawan', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger(
                'karyawan_asal_id'
            )->unique();

            $table->string('nik');
            $table->string('nama');

            $table->unsignedBigInteger(
                'departemen_id'
            );

            $table->unsignedBigInteger(
                'jabatan_id'
            );

            $table->date('tanggal_masuk');
            $table->enum('status', [
                'aktif',
                'nonaktif',
            ]);

            $table->foreign('departemen_id')
                ->references('id')
                ->on('dim_departemen');

            $table->foreign('jabatan_id')
                ->references('id')
                ->on('dim_jabatan');

            $table->index('nik');
        });


        /*
        |--------------------------------------------------------------------------
        | FACT ASSESSMENT
        |--------------------------------------------------------------------------
        */

        Schema::create('fact_assessment', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('waktu_id');
            $table->unsignedBigInteger('karyawan_id');
            $table->unsignedBigInteger('jabatan_id')->nullable();
            $table->unsignedBigInteger('skill_id')->nullable();

            $table->unsignedBigInteger(
                'assessment_asal_id'
            );

            $table->unsignedBigInteger(
                'hasil_assessment_asal_id'
            );

            $table->decimal(
                'nilai_akhir',
                5,
                2
            );

            $table->decimal(
                'standar_nilai',
                5,
                2
            );

            $table->enum('status', [
                'lulus',
                'tidak_lulus',
            ]);

            $table->unsignedInteger(
                'jumlah_peserta'
            )->default(1);

            $table->foreign('waktu_id')
                ->references('id')
                ->on('dim_waktu');

            $table->foreign('karyawan_id')
                ->references('id')
                ->on('dim_karyawan');

            $table->foreign('jabatan_id')
                ->references('id')
                ->on('dim_jabatan')
                ->nullOnDelete();

            $table->foreign('skill_id')
                ->references('id')
                ->on('dim_skill')
                ->nullOnDelete();

            $table->index([
                'waktu_id',
                'karyawan_id',
            ]);

            $table->index('status');
        });


        /*
        |--------------------------------------------------------------------------
        | FACT PENGEMBANGAN
        |--------------------------------------------------------------------------
        */

        Schema::create('fact_pengembangan', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('waktu_id');
            $table->unsignedBigInteger('karyawan_id');

            $table->unsignedBigInteger(
                'hasil_assessment_asal_id'
            )->nullable();

            $table->unsignedBigInteger(
                'skill_id'
            )->nullable();

            $table->unsignedBigInteger(
                'jabatan_asal_id'
            )->nullable();

            $table->unsignedBigInteger(
                'jabatan_tujuan_id'
            )->nullable();

            $table->unsignedBigInteger(
                'pengajuan_asal_id'
            );

            $table->string('jenis_pengajuan');

            $table->string('status');

            $table->unsignedTinyInteger(
                'level_sebelum'
            )->nullable();

            $table->unsignedTinyInteger(
                'level_sesudah'
            )->nullable();

            $table->unsignedInteger(
                'jumlah_pengajuan'
            )->default(1);

            $table->foreign('waktu_id')
                ->references('id')
                ->on('dim_waktu');

            $table->foreign('karyawan_id')
                ->references('id')
                ->on('dim_karyawan');

            $table->foreign('hasil_assessment_asal_id')
                ->references('id')
                ->on('fact_assessment')
                ->nullOnDelete();

            $table->foreign('skill_id')
                ->references('id')
                ->on('dim_skill')
                ->nullOnDelete();

            $table->foreign('jabatan_asal_id')
                ->references('id')
                ->on('dim_jabatan')
                ->nullOnDelete();

            $table->foreign('jabatan_tujuan_id')
                ->references('id')
                ->on('dim_jabatan')
                ->nullOnDelete();

            $table->index([
                'waktu_id',
                'jenis_pengajuan',
            ]);

            $table->index('status');
        });


        /*
        |--------------------------------------------------------------------------
        | FACT RIWAYAT JABATAN
        |--------------------------------------------------------------------------
        */

        Schema::create('fact_riwayat_jabatan', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('waktu_id');
            $table->unsignedBigInteger('karyawan_id');
            $table->unsignedBigInteger('jabatan_id');

            $table->unsignedBigInteger(
                'pengajuan_asal_id'
            )->nullable();

            $table->unsignedBigInteger(
                'hasil_assessment_asal_id'
            )->nullable();

            $table->string('jenis_perubahan');

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();

            $table->integer('durasi_hari')->nullable();

            $table->unsignedInteger(
                'jumlah_perubahan'
            )->default(1);

            $table->foreign('waktu_id')
                ->references('id')
                ->on('dim_waktu');

            $table->foreign('karyawan_id')
                ->references('id')
                ->on('dim_karyawan');

            $table->foreign('jabatan_id')
                ->references('id')
                ->on('dim_jabatan');

            $table->index([
                'waktu_id',
                'karyawan_id',
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | FACT RIWAYAT SKILL
        |--------------------------------------------------------------------------
        */

        Schema::create('fact_riwayat_skill', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('waktu_id');
            $table->unsignedBigInteger('karyawan_id');
            $table->unsignedBigInteger('skill_id');

            $table->unsignedBigInteger(
                'pengajuan_asal_id'
            )->nullable();

            $table->unsignedBigInteger(
                'hasil_assessment_asal_id'
            )->nullable();

            $table->unsignedTinyInteger(
                'level_sebelum'
            );

            $table->unsignedTinyInteger(
                'level_sesudah'
            );

            $table->smallInteger(
                'perubahan_level'
            );

            $table->date('tanggal_perubahan');

            $table->unsignedInteger(
                'jumlah_perubahan'
            )->default(1);

            $table->foreign('waktu_id')
                ->references('id')
                ->on('dim_waktu');

            $table->foreign('karyawan_id')
                ->references('id')
                ->on('dim_karyawan');

            $table->foreign('skill_id')
                ->references('id')
                ->on('dim_skill');

            $table->index([
                'waktu_id',
                'karyawan_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists(
            'fact_riwayat_skill'
        );

        Schema::dropIfExists(
            'fact_riwayat_jabatan'
        );

        Schema::dropIfExists(
            'fact_pengembangan'
        );

        Schema::dropIfExists(
            'fact_assessment'
        );

        Schema::dropIfExists(
            'dim_karyawan'
        );

        Schema::dropIfExists(
            'dim_skill'
        );

        Schema::dropIfExists(
            'dim_jabatan'
        );

        Schema::dropIfExists(
            'dim_departemen'
        );

        Schema::dropIfExists(
            'dim_waktu'
        );

        Schema::enableForeignKeyConstraints();
    }
};