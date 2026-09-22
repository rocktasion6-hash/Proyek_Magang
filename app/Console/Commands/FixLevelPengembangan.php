<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixLevelPengembangan extends Command
{
    protected $signature = 'fix:level-pengembangan';

    protected $description = 'Mengisi level_sebelum dan level_sesudah yang null pada pengajuan_pengembangans dari riwayat_skills';

    public function handle(): int
    {
        $pengajuans = DB::table('pengajuan_pengembangans')
            ->where('jenis_pengajuan', 'peningkatan_skill')
            ->where(function ($q) {
                $q->whereNull('level_sebelum')
                  ->orWhereNull('level_sesudah');
            })
            ->get();

        $this->info("Ditemukan {$pengajuans->count()} pengajuan dengan level null.");

        $updated = 0;

        foreach ($pengajuans as $pengajuan) {
            // Ambil dari riwayat_skills yang terhubung
            $riwayat = DB::table('riwayat_skills')
                ->where('pengajuan_pengembangan_id', $pengajuan->id)
                ->first();

            if ($riwayat) {
                DB::table('pengajuan_pengembangans')
                    ->where('id', $pengajuan->id)
                    ->update([
                        'level_sebelum' => $riwayat->level_sebelum,
                        'level_sesudah' => $riwayat->level_sesudah,
                    ]);

                $updated++;
                continue;
            }

            // Fallback: ambil dari karyawan_skill saat ini
            $karyawanSkill = DB::table('karyawan_skill')
                ->where('karyawan_id', $pengajuan->karyawan_id)
                ->where('skill_id', $pengajuan->skill_id)
                ->first();

            if ($karyawanSkill) {
                $levelSesudah = (int) $karyawanSkill->level_skill;
                $levelSebelum = max(0, $levelSesudah - 1);

                DB::table('pengajuan_pengembangans')
                    ->where('id', $pengajuan->id)
                    ->update([
                        'level_sebelum' => $levelSebelum,
                        'level_sesudah' => $levelSesudah,
                    ]);

                $updated++;
            }
        }

        $this->info("Berhasil mengupdate {$updated} pengajuan.");
        $this->info("Jalankan 'php artisan warehouse:etl' untuk memperbarui data warehouse.");

        return self::SUCCESS;
    }
}
