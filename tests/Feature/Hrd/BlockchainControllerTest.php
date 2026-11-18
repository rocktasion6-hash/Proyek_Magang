<?php

use App\Models\Assessment;
use App\Models\BlockchainRecord;
use App\Models\PengajuanPengembangan;
use App\Models\User;
use App\Services\BlockchainService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::dropIfExists('assessments');
    Schema::dropIfExists('pengajuan_pengembangans');
    Schema::dropIfExists('blockchain_chain_heads');
    Schema::dropIfExists('blockchain_records');

    Schema::create('blockchain_records', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('block_number')->unique();
        $table->string('entity_type');
        $table->unsignedBigInteger('entity_id');
        $table->string('data_hash', 64);
        $table->json('data_snapshot')->nullable();
        $table->string('previous_hash', 64)->nullable();
        $table->string('block_hash', 64)->unique();
        $table->timestamps();
    });

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
});

afterEach(function () {
    Schema::dropIfExists('assessments');
    Schema::dropIfExists('pengajuan_pengembangans');
    Schema::dropIfExists('blockchain_chain_heads');
    Schema::dropIfExists('blockchain_records');
});

it('reports a chain whose first stored block does not start at one', function () {
    $service = app(BlockchainService::class);
    $dataHash = hash('sha256', '{}');
    $previousHash = str_repeat('a', 64);

    BlockchainRecord::create([
        'block_number' => 2,
        'entity_type' => 'pengajuan_pengembangan',
        'entity_id' => 1,
        'data_hash' => $dataHash,
        'data_snapshot' => [],
        'previous_hash' => $previousHash,
        'block_hash' => $service->calculateBlockHash(
            2,
            'pengajuan_pengembangan',
            1,
            $dataHash,
            $previousHash
        ),
    ]);

    $hrd = (new User)->forceFill([
        'id' => 1,
        'username' => 'hrd-blockchain',
        'name' => 'HRD',
        'role' => 'hrd',
    ]);

    $this->actingAs($hrd)
        ->from(route('hrd.blockchain.index'))
        ->post(route('hrd.blockchain.verify'))
        ->assertRedirect(route('hrd.blockchain.index'))
        ->assertSessionHas(
            'error',
            'Nomor block pertama harus 1. Block bermasalah: #2.'
        );

    $this->get(route('hrd.blockchain.index'))
        ->assertSee('Blockchain Tidak Valid')
        ->assertSee('Nomor block pertama harus 1. Block bermasalah: #2.');
});

it('creates a genesis block and records authenticated event metadata', function () {
    $hrd = (new User)->forceFill([
        'id' => 1,
        'username' => 'hrd-blockchain',
        'name' => 'HRD',
        'role' => 'hrd',
    ]);

    $this->actingAs($hrd);

    $eventBlock = app(BlockchainService::class)->addEvent(
        'custom_audit_event',
        19,
        ['status' => 'aktif']
    );

    expect(BlockchainRecord::query()->where('entity_type', 'genesis')->first())
        ->not->toBeNull();
    expect($eventBlock->block_number)->toBe(2);
    expect($eventBlock->previous_hash)
        ->toBe(BlockchainRecord::query()->where('block_number', 1)->value('block_hash'));
    expect($eventBlock->data_snapshot['actor_user_id'])->toBe(1);
    expect($eventBlock->data_snapshot['event_type'])->toBe('custom_audit_event');
    expect(app(BlockchainService::class)->verifyChain()['valid'])->toBeTrue();
});

it('does not fabricate a genesis block over existing incomplete history', function () {
    $service = app(BlockchainService::class);
    $dataHash = hash('sha256', '{}');
    $previousHash = str_repeat('a', 64);

    BlockchainRecord::create([
        'block_number' => 3,
        'entity_type' => 'pengajuan_pengembangan',
        'entity_id' => 1,
        'data_hash' => $dataHash,
        'data_snapshot' => [],
        'previous_hash' => $previousHash,
        'block_hash' => $service->calculateBlockHash(
            3,
            'pengajuan_pengembangan',
            1,
            $dataHash,
            $previousHash
        ),
    ]);

    DB::table('blockchain_chain_heads')
        ->where('id', 1)
        ->update([
            'block_number' => 3,
            'block_hash' => BlockchainRecord::query()->value('block_hash'),
        ]);

    expect(fn () => $service->createGenesisBlock())
        ->toThrow(LogicException::class);
    expect(BlockchainRecord::count())->toBe(1);
});

it('verifies assessment data when numeric inputs are stored with different PHP types', function () {
    Schema::create('assessments', function (Blueprint $table) {
        $table->id();
        $table->string('nama_assessment');
        $table->unsignedBigInteger('jabatan_id');
        $table->unsignedBigInteger('departemen_id')->nullable();
        $table->unsignedBigInteger('soal_upload_id')->nullable();
        $table->decimal('standar_nilai', 5, 2);
        $table->unsignedInteger('durasi');
        $table->dateTime('tanggal_mulai')->nullable();
        $table->dateTime('tanggal_selesai')->nullable();
        $table->string('status');
        $table->timestamps();
    });

    $assessment = Assessment::create([
        'nama_assessment' => 'Assessment Integritas',
        'jabatan_id' => 4,
        'departemen_id' => '3',
        'standar_nilai' => '80.00',
        'durasi' => '90',
        'status' => 'aktif',
    ]);

    app(BlockchainService::class)
        ->recordAssessmentEvent('assessment_dibuat', $assessment);

    $hrd = (new User)->forceFill([
        'id' => 1,
        'username' => 'hrd-blockchain',
        'name' => 'HRD',
        'role' => 'hrd',
    ]);

    $this->actingAs($hrd)
        ->from(route('hrd.blockchain.index'))
        ->post(route('hrd.blockchain.verify-data'))
        ->assertRedirect(route('hrd.blockchain.index'))
        ->assertSessionHas(
            'success',
            'Integritas data sumber valid. Diperiksa: 1 block.'
        );

    $this->get(route('hrd.blockchain.index'))
        ->assertSee('Integritas data sumber valid. Diperiksa: 1 block.');

    $assessment->update([
        'departemen_id' => 4,
    ]);

    $this->from(route('hrd.blockchain.index'))
        ->post(route('hrd.blockchain.verify-data'))
        ->assertRedirect(route('hrd.blockchain.index'))
        ->assertSessionHas(
            'error',
            'Integritas data tidak valid. Block bermasalah: #2. Data sumber telah berubah.'
        );
});

it('verifies rejected promotion request and decision snapshots', function () {
    Schema::create('pengajuan_pengembangans', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('karyawan_id');
        $table->unsignedBigInteger('hasil_assessment_id');
        $table->string('jenis_pengajuan');
        $table->unsignedBigInteger('jabatan_asal_id')->nullable();
        $table->unsignedBigInteger('jabatan_tujuan_id')->nullable();
        $table->unsignedBigInteger('skill_id')->nullable();
        $table->unsignedInteger('level_sebelum')->nullable();
        $table->unsignedInteger('level_sesudah')->nullable();
        $table->string('status');
        $table->date('tanggal_pengajuan');
        $table->date('tanggal_keputusan')->nullable();
        $table->text('catatan')->nullable();
        $table->timestamps();
    });

    $pengajuan = PengajuanPengembangan::create([
        'karyawan_id' => 5,
        'hasil_assessment_id' => 7,
        'jenis_pengajuan' => 'kenaikan_jabatan',
        'jabatan_asal_id' => 2,
        'jabatan_tujuan_id' => 3,
        'status' => 'diajukan',
        'tanggal_pengajuan' => '2026-01-15',
        'catatan' => 'Catatan awal',
    ]);

    $service = app(BlockchainService::class);
    $service->addBlock('pengajuan_pengembangan', $pengajuan->id, [
        'karyawan_id' => $pengajuan->karyawan_id,
        'hasil_assessment_id' => $pengajuan->hasil_assessment_id,
        'jenis_pengajuan' => $pengajuan->jenis_pengajuan,
        'jabatan_asal_id' => $pengajuan->jabatan_asal_id,
        'jabatan_tujuan_id' => $pengajuan->jabatan_tujuan_id,
        'status' => $pengajuan->status,
        'tanggal_pengajuan' => $pengajuan->tanggal_pengajuan,
        'tanggal_keputusan' => $pengajuan->tanggal_keputusan,
        'catatan' => $pengajuan->catatan,
    ]);

    $pengajuan->update([
        'status' => 'ditolak',
        'tanggal_keputusan' => '2026-02-01',
        'catatan' => 'Tidak memenuhi persyaratan.',
    ]);

    $service->addBlock('keputusan_kenaikan_jabatan', $pengajuan->id, [
        'pengajuan_pengembangan_id' => $pengajuan->id,
        'karyawan_id' => $pengajuan->karyawan_id,
        'hasil_assessment_id' => $pengajuan->hasil_assessment_id,
        'jabatan_asal_id' => $pengajuan->jabatan_asal_id,
        'jabatan_tujuan_id' => $pengajuan->jabatan_tujuan_id,
        'status' => 'ditolak',
        'tanggal_keputusan' => $pengajuan->tanggal_keputusan->toDateString(),
        'catatan' => $pengajuan->catatan,
    ]);

    $hrd = (new User)->forceFill([
        'id' => 1,
        'username' => 'hrd-blockchain',
        'name' => 'HRD',
        'role' => 'hrd',
    ]);

    $this->actingAs($hrd)
        ->from(route('hrd.blockchain.index'))
        ->post(route('hrd.blockchain.verify-data'))
        ->assertRedirect(route('hrd.blockchain.index'))
        ->assertSessionHas(
            'success',
            'Integritas data sumber valid. Diperiksa: 2 block.'
        );

    $this->get(route('hrd.blockchain.index'))
        ->assertSee('Integritas data sumber valid. Diperiksa: 2 block.')
        ->assertSee('Verifikasi Blockchain')
        ->assertSee('Verifikasi Data');

    $pengajuan->update([
        'catatan' => 'Data diubah setelah keputusan.',
    ]);

    $verification = app(BlockchainService::class)->verifyDataIntegrity();

    expect($verification['valid'])->toBeFalse();
    expect($verification['invalid_blocks'][0]['reason'])
        ->toBe('Data sumber telah berubah.');
});

it('verifies approved skill decision snapshots with date-only values', function () {
    Schema::create('pengajuan_pengembangans', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('karyawan_id');
        $table->unsignedBigInteger('hasil_assessment_id');
        $table->string('jenis_pengajuan');
        $table->unsignedBigInteger('jabatan_asal_id')->nullable();
        $table->unsignedBigInteger('jabatan_tujuan_id')->nullable();
        $table->unsignedBigInteger('skill_id')->nullable();
        $table->unsignedInteger('level_sebelum')->nullable();
        $table->unsignedInteger('level_sesudah')->nullable();
        $table->string('status');
        $table->date('tanggal_pengajuan');
        $table->date('tanggal_keputusan')->nullable();
        $table->text('catatan')->nullable();
        $table->timestamps();
    });

    $pengajuan = PengajuanPengembangan::create([
        'karyawan_id' => 4,
        'hasil_assessment_id' => 4,
        'jenis_pengajuan' => 'peningkatan_skill',
        'jabatan_asal_id' => 24,
        'jabatan_tujuan_id' => 25,
        'level_sebelum' => 1,
        'level_sesudah' => 2,
        'status' => 'disetujui',
        'tanggal_pengajuan' => '2026-11-12',
        'tanggal_keputusan' => '2026-11-12',
    ]);

    app(BlockchainService::class)->addBlock(
        'keputusan_peningkatan_skill',
        $pengajuan->id,
        [
            'pengajuan_pengembangan_id' => $pengajuan->id,
            'karyawan_id' => $pengajuan->karyawan_id,
            'jabatan_asal_id' => $pengajuan->jabatan_asal_id,
            'jabatan_tujuan_id' => $pengajuan->jabatan_tujuan_id,
            'level_sebelum' => $pengajuan->level_sebelum,
            'level_sesudah' => $pengajuan->level_sesudah,
            'status' => $pengajuan->status,
            'tanggal_keputusan' => '2026-11-12',
        ]
    );

    $verification = app(BlockchainService::class)->verifyDataIntegrity();

    expect($verification['valid'])->toBeTrue();
    expect($verification['checked'])->toBe(1);
    expect($verification['invalid_blocks'])->toBe([]);
});
