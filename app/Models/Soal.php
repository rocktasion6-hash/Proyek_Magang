<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    use HasFactory;

    public static function tipeProsedurDariNama(?string $namaProsedur): string
    {
        $namaProsedur = mb_strtolower(trim((string) $namaProsedur));

        if (str_contains($namaProsedur, 'keselamatan')) {
            return 'keselamatan_kerja';
        }

        if (str_contains($namaProsedur, 'peraturan perusahaan')) {
            return 'peraturan_perusahaan';
        }

        return 'pengoperasian_jabatan';
    }

    protected $fillable = [
        'jabatan_id',
        'upload_id',
        'pertanyaan',
        'tipe_soal',
        'tipe_prosedur',
        'nama_prosedur',
        'nilai_point',
        'status',
    ];

    protected function casts(): array
    {
        return [
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
