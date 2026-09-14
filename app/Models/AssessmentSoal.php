<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentSoal extends Model
{
    use HasFactory;

    protected $table = 'assessment_soal';

    protected $fillable = [
        'assessment_id',
        'soal_id',
        'nomor_soal',
    ];

    protected function casts(): array
    {
        return [
            'nomor_soal' => 'integer',
        ];
    }

    /**
     * Relasi ke Assessment.
     */
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Relasi ke Soal.
     */
    public function soal()
    {
        return $this->belongsTo(Soal::class);
    }
}