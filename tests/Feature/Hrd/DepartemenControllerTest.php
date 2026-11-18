<?php

use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\SoalUpload;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows a delete button for each department', function () {
    $hrd = User::factory()->create([
        'username' => 'hrd-departemen-index',
        'role' => 'hrd',
    ]);
    $departemen = Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);

    $this->actingAs($hrd)
        ->get(route('hrd.departemen.index'))
        ->assertSee('Hapus')
        ->assertSee(
            'action="'.route('hrd.departemen.destroy', $departemen).'"',
            false
        )
        ->assertSee('value="DELETE"', false);
});

it('lets HRD delete a department without employees', function () {
    $hrd = User::factory()->create([
        'username' => 'hrd-departemen-delete',
        'role' => 'hrd',
    ]);
    $departemen = Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);

    $this->actingAs($hrd)
        ->delete(route('hrd.departemen.destroy', $departemen))
        ->assertRedirect(route('hrd.departemen.index'))
        ->assertSessionHas('success', 'Departemen berhasil dihapus.');

    $this->assertDatabaseMissing('departemens', [
        'id' => $departemen->id,
    ]);
});

it('prevents deleting a department that still has employees', function () {
    $hrd = User::factory()->create([
        'username' => 'hrd-departemen-protected',
        'role' => 'hrd',
    ]);
    $employeeUser = User::factory()->create([
        'username' => 'employee-departemen-protected',
        'role' => 'karyawan',
    ]);
    $departemen = Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);
    $jabatan = Jabatan::create([
        'nama_jabatan' => 'Crew',
    ]);
    Karyawan::create([
        'user_id' => $employeeUser->id,
        'nik' => 'KRY-DEPT-001',
        'nama' => 'Budi',
        'departemen_id' => $departemen->id,
        'jabatan_id' => $jabatan->id,
        'tanggal_masuk' => '2026-01-01',
        'status' => 'aktif',
    ]);

    $this->actingAs($hrd)
        ->from(route('hrd.departemen.index'))
        ->delete(route('hrd.departemen.destroy', $departemen))
        ->assertRedirect(route('hrd.departemen.index'))
        ->assertSessionHas(
            'error',
            'Departemen tidak dapat dinonaktifkan karena masih memiliki karyawan.'
        );

    $this->assertDatabaseHas('departemens', [
        'id' => $departemen->id,
    ]);
});

it('prevents deleting a department that has a question bank', function () {
    $hrd = User::factory()->create([
        'username' => 'hrd-departemen-bank-protected',
        'role' => 'hrd',
    ]);
    $departemen = Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);
    $jabatan = Jabatan::create([
        'nama_jabatan' => 'Operator Loader Level 1',
        'tipe_jabatan' => 'operasional',
        'level_jabatan' => 1,
    ]);
    SoalUpload::create([
        'jabatan_id' => $jabatan->id,
        'departemen_id' => $departemen->id,
        'nama_file' => 'operator-loader-level-1.xlsx',
        'jumlah_soal' => 1,
    ]);

    $this->actingAs($hrd)
        ->from(route('hrd.departemen.index'))
        ->delete(route('hrd.departemen.destroy', $departemen))
        ->assertRedirect(route('hrd.departemen.index'))
        ->assertSessionHas(
            'error',
            'Departemen tidak dapat dihapus karena sudah digunakan oleh bank soal atau assessment.'
        );

    $this->assertDatabaseHas('departemens', [
        'id' => $departemen->id,
    ]);
});
