<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    use HasFactory;

    protected $fillable = [
        'jabatan_id',
        'skill_id',
        'pertanyaan',
        'tipe_soal',
        'tingkat_kesulitan',
        'bobot',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'bobot' => 'decimal:2',
            'status' => 'boolean',
        ];
    }

    /**
     * Relasi ke Jabatan.
     */
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    /**
     * Relasi ke Skill.
     */
    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    /**
     * Relasi ke pilihan jawaban.
     */
    public function pilihanJawabans()
    {
        return $this->hasMany(PilihanJawaban::class);
    }

    /**
     * Relasi ke assessment_soal.
     */
    public function assessmentSoals()
    {
        return $this->hasMany(AssessmentSoal::class);
    }

    /**
     * Relasi langsung ke Assessment.
     */
    public function assessments()
    {
        return $this->belongsToMany(
            Assessment::class,
            'assessment_soal',
            'soal_id',
            'assessment_id'
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
}