<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_peserta_id',
        'soal_id',
        'pilihan_jawaban_id',
    ];

    public function assessmentPeserta()
    {
        return $this->belongsTo(
            AssessmentPeserta::class,
            'assessment_peserta_id'
        );
    }

    public function soal()
    {
        return $this->belongsTo(Soal::class);
    }

    public function pilihanJawaban()
    {
        return $this->belongsTo(PilihanJawaban::class);
    }
}