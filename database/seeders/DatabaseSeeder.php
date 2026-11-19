<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
       
        // ========================================
        // 3. USER HRD
        // ========================================

        User::firstOrCreate(
            ['username' => 'hrd01'],
            ['name' => 'Admin HRD', 'email' => 'hrd@example.com', 'password' => 'password', 'role' => 'hrd']
        );

    }
}
