<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_assessment',
        'jabatan_id',
        'skill_id',
        'standar_nilai',
        'durasi',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'standar_nilai' => 'decimal:2',
            'tanggal_mulai' => 'datetime',
            'tanggal_selesai' => 'datetime',
        ];
    }

    /**
     * Jabatan tujuan assessment.
     */
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    /**
     * Skill yang diuji, jika assessment khusus skill.
     */
    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    /**
     * Relasi ke assessment_soal.
     */
    public function assessmentSoals()
    {
        return $this->hasMany(AssessmentSoal::class);
    }

    /**
     * Relasi langsung ke soal.
     */
    public function soals()
    {
        return $this->belongsToMany(
            Soal::class,
            'assessment_soal',
            'assessment_id',
            'soal_id'
        )->withPivot('nomor_soal')
         ->withTimestamps();
    }

    /**
     * Relasi ke jawaban karyawan.
     */
    public function jawabans()
    {
        return $this->hasMany(Jawaban::class);
    }

    /**
     * Relasi ke hasil assessment.
     */
    public function hasilAssessments()
    {
        return $this->hasMany(HasilAssessment::class);
    }

    public function peserta()
    {
        return $this->hasMany(AssessmentPeserta::class);
    }
}