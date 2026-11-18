<?php

use App\Models\Jabatan;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the same total skills as the management page', function () {
    $hrd = User::factory()->create([
        'role' => 'hrd',
    ]);

    foreach (range(1, 6) as $number) {
        Skill::create([
            'nama_skill' => 'Skill '.$number,
        ]);
    }

    foreach (range(2, 5) as $level) {
        foreach (['Loader', 'Forklift'] as $equipment) {
            Jabatan::create([
                'nama_jabatan' => "Operator {$equipment} Level {$level}",
                'tipe_jabatan' => 'operasional',
                'level_jabatan' => $level,
            ]);
        }
    }

    Jabatan::create([
        'nama_jabatan' => 'Operator Dump Truck Level 2',
        'tipe_jabatan' => 'operasional',
        'level_jabatan' => 2,
    ]);

    foreach ([
        ['nama_jabatan' => '  CREW  ', 'tipe_jabatan' => 'operasional', 'level_jabatan' => 2],
        ['nama_jabatan' => 'Operator Level 1', 'tipe_jabatan' => 'operasional', 'level_jabatan' => 1],
        ['nama_jabatan' => 'Operator Level 6', 'tipe_jabatan' => 'operasional', 'level_jabatan' => 6],
        ['nama_jabatan' => 'Supervisor Level 2', 'tipe_jabatan' => 'struktural', 'level_jabatan' => 2],
    ] as $attributes) {
        Jabatan::create($attributes);
    }

    $skillManagementResponse = $this->actingAs($hrd)
        ->get(route('hrd.skill.index'));
    $dashboardResponse = $this->get(route('hrd.dashboard'));

    $skillManagementResponse->assertViewHas('jabatans', function ($jabatans) {
        return $jabatans->total() === 9;
    });
    $dashboardResponse->assertViewHas('totalSkill', 9);
});
