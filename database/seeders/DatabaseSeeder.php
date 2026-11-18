<?php

namespace Database\Seeders;

use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ========================================
        // 1. DEPARTEMEN
        // ========================================

        $produksi = Departemen::firstOrCreate(
            ['nama_departemen' => 'Produksi'],
            ['deskripsi' => 'Departemen yang menangani kegiatan produksi.']
        );

        $engineering = Departemen::firstOrCreate(
            ['nama_departemen' => 'Engineering'],
            ['deskripsi' => 'Departemen yang menangani pemeliharaan dan teknis.']
        );

        Departemen::firstOrCreate(
            ['nama_departemen' => 'Human Resources'],
            ['deskripsi' => 'Departemen yang menangani sumber daya manusia.']
        );

        // ========================================
        // 2. JABATAN
        // ========================================

        $crew = Jabatan::firstOrCreate(
            ['nama_jabatan' => 'Crew', 'tipe_jabatan' => 'operasional'],
            ['urutan_struktural' => null, 'level_jabatan' => 0, 'deskripsi' => 'Jabatan awal karyawan baru.', 'standar_nilai' => 0]
        );

        $operatorJunior = Jabatan::firstOrCreate(
            ['nama_jabatan' => 'Operator Junior', 'tipe_jabatan' => 'operasional'],
            ['urutan_struktural' => null, 'level_jabatan' => 1, 'deskripsi' => 'Jabatan operator tingkat junior.', 'standar_nilai' => 70]
        );

        Jabatan::firstOrCreate(
            ['nama_jabatan' => 'Operator Senior', 'tipe_jabatan' => 'operasional'],
            ['urutan_struktural' => null, 'level_jabatan' => 2, 'deskripsi' => 'Jabatan operator tingkat senior.', 'standar_nilai' => 80]
        );

        $teknisiJunior = Jabatan::firstOrCreate(
            ['nama_jabatan' => 'Teknisi Junior', 'tipe_jabatan' => 'operasional'],
            ['urutan_struktural' => null, 'level_jabatan' => 1, 'deskripsi' => 'Jabatan teknisi tingkat junior.', 'standar_nilai' => 75]
        );

        Jabatan::firstOrCreate(
            ['nama_jabatan' => 'Teknisi Senior', 'tipe_jabatan' => 'operasional'],
            ['urutan_struktural' => null, 'level_jabatan' => 2, 'deskripsi' => 'Jabatan teknisi tingkat senior.', 'standar_nilai' => 80]
        );

        Jabatan::firstOrCreate(
            ['nama_jabatan' => 'Wakil Foreman', 'tipe_jabatan' => 'struktural'],
            ['urutan_struktural' => 1, 'level_jabatan' => null, 'deskripsi' => 'Jabatan struktural Wakil Foreman.', 'standar_nilai' => 80]
        );

        Jabatan::firstOrCreate(
            ['nama_jabatan' => 'Foreman', 'tipe_jabatan' => 'struktural'],
            ['urutan_struktural' => 2, 'level_jabatan' => null, 'deskripsi' => 'Jabatan struktural Foreman.', 'standar_nilai' => 80]
        );

        Jabatan::firstOrCreate(
            ['nama_jabatan' => 'Wakil Supervisor', 'tipe_jabatan' => 'struktural'],
            ['urutan_struktural' => 3, 'level_jabatan' => null, 'deskripsi' => 'Jabatan struktural Wakil Supervisor.', 'standar_nilai' => 85]
        );

        Jabatan::firstOrCreate(
            ['nama_jabatan' => 'Supervisor', 'tipe_jabatan' => 'struktural'],
            ['urutan_struktural' => 4, 'level_jabatan' => null, 'deskripsi' => 'Jabatan struktural Supervisor.', 'standar_nilai' => 85]
        );

        Jabatan::firstOrCreate(
            ['nama_jabatan' => 'Wakil General Manager', 'tipe_jabatan' => 'struktural'],
            ['urutan_struktural' => 5, 'level_jabatan' => null, 'deskripsi' => 'Jabatan struktural Wakil GM.', 'standar_nilai' => 90]
        );

        Jabatan::firstOrCreate(
            ['nama_jabatan' => 'Manager', 'tipe_jabatan' => 'struktural'],
            ['urutan_struktural' => 6, 'level_jabatan' => null, 'deskripsi' => 'Jabatan struktural Manager.', 'standar_nilai' => 90]
        );

        // ========================================
        // 3. USER HRD
        // ========================================

        User::firstOrCreate(
            ['username' => 'hrd01'],
            ['name' => 'Admin HRD', 'email' => 'hrd@example.com', 'password' => 'password', 'role' => 'hrd']
        );

        // ========================================
        // 4. USER & KARYAWAN
        // ========================================

        $userBudi = User::firstOrCreate(
            ['username' => 'budi01'],
            ['name' => 'Budi Santoso', 'email' => 'budi@example.com', 'password' => 'password', 'role' => 'karyawan']
        );

        $userAndi = User::firstOrCreate(
            ['username' => 'andi01'],
            ['name' => 'Andi Pratama', 'email' => 'andi@example.com', 'password' => 'password', 'role' => 'karyawan']
        );

        Karyawan::firstOrCreate(
            ['nik' => 'KRY001'],
            ['user_id' => $userBudi->id, 'nama' => 'Budi Santoso', 'departemen_id' => $produksi->id, 'jabatan_id' => $crew->id, 'tanggal_masuk' => '2024-01-15', 'status' => 'aktif']
        );

        Karyawan::firstOrCreate(
            ['nik' => 'KRY002'],
            ['user_id' => $userAndi->id, 'nama' => 'Andi Pratama', 'departemen_id' => $engineering->id, 'jabatan_id' => $crew->id, 'tanggal_masuk' => '2023-06-01', 'status' => 'aktif']
        );
    }
}
