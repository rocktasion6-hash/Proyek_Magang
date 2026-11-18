<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_jabatan',
        'tipe_jabatan',
        'urutan_struktural',
        'level_jabatan',
        'deskripsi',
        'standar_nilai',
    ];

    /*
     * Apakah jabatan ini adalah jabatan "Crew" (jabatan awal karyawan baru).
     */
    public function isCrew(): bool
    {
        return mb_strtolower(trim($this->nama_jabatan)) === 'crew';
    }

    /*
     * Apakah jabatan ini jabatan operasional level 1
     * (hasil pemindahan jabatan pertama dari Crew).
     */
    public function isLevelSatu(): bool
    {
        return $this->tipe_jabatan === 'operasional'
            && (int) $this->level_jabatan === 1
            && ! $this->isCrew();
    }

    public function namaDasar(): string
    {
        $namaDasar = preg_replace(
            '/\s+(?:level|lev|lv)\s*\.?\s*\d+\s*$/iu',
            '',
            trim($this->nama_jabatan)
        );

        return mb_strtolower(trim($namaDasar ?? $this->nama_jabatan));
    }

    public function karyawans()
    {
        return $this->hasMany(Karyawan::class);
    }

    public function jabatanSkills()
    {
        return $this->hasMany(JabatanSkill::class);
    }

    public function skills()
    {
        return $this->belongsToMany(
            Skill::class,
            'jabatan_skill',
            'jabatan_id',
            'skill_id'
        )->withPivot('level_dibutuhkan')
            ->withTimestamps();
    }

    public function pengajuanJabatanAsal()
    {
        return $this->hasMany(
            PengajuanPengembangan::class,
            'jabatan_asal_id'
        );
    }

    public function pengajuanJabatanTujuan()
    {
        return $this->hasMany(
            PengajuanPengembangan::class,
            'jabatan_tujuan_id'
        );
    }

    public function riwayatJabatans()
    {
        return $this->hasMany(RiwayatJabatan::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    public function soals()
    {
        return $this->hasMany(Soal::class);
    }
}
