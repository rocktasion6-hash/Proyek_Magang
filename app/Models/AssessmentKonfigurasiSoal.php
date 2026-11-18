<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentKonfigurasiSoal extends Model
{
    protected $table = 'assessment_konfigurasi_soals';

    protected $fillable = [
        'assessment_id',
        'level_soal',
        'tipe_prosedur',
        'tipe_soal',
        'jumlah_soal',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
}
