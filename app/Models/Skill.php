<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_skill',
        'deskripsi',
    ];

    /**
     * Relasi ke tabel jabatan_skill.
     */
    public function jabatanSkills()
    {
        return $this->hasMany(JabatanSkill::class);
    }

    /**
     * Relasi ke tabel karyawan_skill.
     */
    public function karyawanSkills()
    {
        return $this->hasMany(KaryawanSkill::class);
    }

    /**
     * Relasi many-to-many dengan Jabatan.
     */
    public function jabatans()
    {
        return $this->belongsToMany(
            Jabatan::class,
            'jabatan_skill',
            'skill_id',
            'jabatan_id'
        )->withPivot('level_dibutuhkan')
         ->withTimestamps();
    }

    /**
     * Relasi many-to-many dengan Karyawan.
     */
    public function karyawans()
    {
        return $this->belongsToMany(
            Karyawan::class,
            'karyawan_skill',
            'skill_id',
            'karyawan_id'
        )->withPivot('level_skill', 'tanggal_penilaian')
         ->withTimestamps();
    }
    
    public function soals()
    {
        return $this->hasMany(Soal::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    public function pengajuanPengembangans()
    {
        return $this->hasMany(PengajuanPengembangan::class);
    }

    public function riwayatSkills()
    {
        return $this->hasMany(RiwayatSkill::class);
    }
}