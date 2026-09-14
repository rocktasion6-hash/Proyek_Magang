<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PilihanJawaban extends Model
{
    use HasFactory;

    protected $fillable = [
        'soal_id',
        'kode',
        'teks_jawaban',
        'is_benar',
    ];

    protected function casts(): array
    {
        return [
            'is_benar' => 'boolean',
        ];
    }

    /**
     * Relasi ke Soal.
     */
    public function soal()
    {
        return $this->belongsTo(Soal::class);
    }

    /**
     * Relasi ke jawaban karyawan.
     */
    public function jawabans()
    {
        return $this->hasMany(Jawaban::class);
    }
}