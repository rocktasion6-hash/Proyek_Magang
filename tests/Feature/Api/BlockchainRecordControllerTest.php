<?php

use App\Models\User;
use App\Services\BlockchainService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns 401 when a guest reads blockchain records', function () {
    $routes = [
        route('api.blockchain-records.index'),
        route('api.blockchain-records.show', 1),
        route('api.hasil-assessment.index'),
        route('api.hasil-assessment.show', 1),
        route('api.pengembangan.index'),
        route('api.pengembangan.show', 1),
    ];

    foreach ($routes as $route) {
        $this->getJson($route)->assertUnauthorized();
    }
});

it('returns 403 when a non-HRD user reads blockchain records', function () {
    $employee = User::factory()->create([
        'role' => 'karyawan',
    ]);

    $this->actingAs($employee)
        ->getJson(route('api.blockchain-records.index'))
        ->assertForbidden();
});

it('allows HRD users to read blockchain records', function () {
    $hrd = User::factory()->create([
        'role' => 'hrd',
    ]);

    $this->actingAs($hrd);

    $block = app(BlockchainService::class)->addEvent(
        'custom_audit_event',
        19,
        ['status' => 'aktif']
    );

    $this->getJson(route('api.blockchain-records.index'))
        ->assertOk()
        ->assertJsonPath('total_blocks', 2)
        ->assertJsonPath('data.1.entity_type', 'custom_audit_event');

    $this->getJson(route('api.blockchain-records.show', $block->id))
        ->assertOk()
        ->assertJsonPath('data.id', $block->id)
        ->assertJsonPath('data.data_snapshot.actor_user_id', $hrd->id);
});
