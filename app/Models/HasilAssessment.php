<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilAssessment extends Model
{
    use HasFactory;

    protected $table = 'hasil_assessments';

    protected $fillable = [
        'assessment_peserta_id',
        'nilai_akhir',
        'standar_nilai',
        'status',
        'tanggal_ujian',
        'waktu_mulai',
        'waktu_selesai',
    ];

    protected function casts(): array
    {
        return [
            'nilai_akhir' => 'decimal:2',
            'standar_nilai' => 'decimal:2',
            'tanggal_ujian' => 'datetime',
            'waktu_mulai' => 'datetime',
            'waktu_selesai' => 'datetime',
        ];
    }

    public function assessmentPeserta()
    {
        return $this->belongsTo(
            AssessmentPeserta::class,
            'assessment_peserta_id'
        );
    }

    public function pengajuanPengembangans()
    {
        return $this->hasMany(
            PengajuanPengembangan::class
        );
    }

    public function riwayatJabatans()
    {
        return $this->hasMany(RiwayatJabatan::class);
    }

    public function riwayatSkills()
    {
        return $this->hasMany(RiwayatSkill::class);
    }
}