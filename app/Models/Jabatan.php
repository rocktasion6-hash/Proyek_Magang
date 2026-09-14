<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_jabatan',
        'level_jabatan',
        'deskripsi',
        'standar_nilai',
    ];

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