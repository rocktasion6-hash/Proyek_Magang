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
        'departemen_id',
        'soal_upload_id',
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

    public function soalUpload()
    {
        return $this->belongsTo(SoalUpload::class);
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }

    public function konfigurasiSoals()
    {
        return $this->hasMany(AssessmentKonfigurasiSoal::class);
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

    public function nilaiPointUntukSoal(Soal $soal): float
    {
        $jabatan = $this->jabatan;

        if (
            $jabatan?->tipe_jabatan !== 'operasional'
            || (int) $jabatan->level_jabatan < 1
            || (int) $jabatan->level_jabatan > 5
        ) {
            return (float) $soal->nilai_point;
        }

        if ($soal->tipe_soal !== 'multi_jawaban') {
            return 1.0;
        }

        return match ((int) $jabatan->level_jabatan) {
            1 => 3.0,
            2, 3 => 2.0,
            4, 5 => 1.0,
        };
    }
}
