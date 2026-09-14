<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentPeserta extends Model
{
    use HasFactory;

    protected $table = 'assessment_peserta';

    protected $fillable = [
        'assessment_id',
        'karyawan_id',
        'status',
        'waktu_mulai',
        'waktu_selesai',
    ];

    protected function casts(): array
    {
        return [
            'waktu_mulai' => 'datetime',
            'waktu_selesai' => 'datetime',
        ];
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function jawabans()
    {
        return $this->hasMany(Jawaban::class);
    }

    public function hasilAssessment()
    {
        return $this->hasOne(HasilAssessment::class);
    }
}