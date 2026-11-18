<?php

use App\Models\Jabatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows a delete button for each job title', function () {
    $hrd = User::factory()->create([
        'username' => 'hrd-jabatan-index',
        'role' => 'hrd',
    ]);
    $jabatan = Jabatan::create([
        'nama_jabatan' => 'Operator',
    ]);

    $this->actingAs($hrd)
        ->get(route('hrd.jabatan.index'))
        ->assertSee('Hapus')
        ->assertSee(
            'action="'.route('hrd.jabatan.destroy', $jabatan).'"',
            false
        )
        ->assertSee('value="DELETE"', false);
});
