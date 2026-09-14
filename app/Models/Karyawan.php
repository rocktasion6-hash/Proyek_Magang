<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nik',
        'nama',
        'departemen_id',
        'jabatan_id',
        'level',
        'tanggal_masuk',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function karyawanSkills()
    {
        return $this->hasMany(KaryawanSkill::class);
    }

    public function skills()
    {
        return $this->belongsToMany(
            Skill::class,
            'karyawan_skill',
            'karyawan_id',
            'skill_id'
        )->withPivot('level_skill', 'tanggal_penilaian')
         ->withTimestamps();
    }

    public function pengajuanPengembangans()
    {
        return $this->hasMany(PengajuanPengembangan::class);
    }

    public function riwayatJabatans()
    {
        return $this->hasMany(RiwayatJabatan::class);
    }

    public function riwayatSkills()
    {
        return $this->hasMany(RiwayatSkill::class);
    }

    public function jawabans()
    {
        return $this->hasMany(Jawaban::class);
    }

    public function hasilAssessments()
    {
        return $this->hasMany(HasilAssessment::class);
    }

    public function assessmentPeserta()
    {
        return $this->hasMany(AssessmentPeserta::class);
    }
}