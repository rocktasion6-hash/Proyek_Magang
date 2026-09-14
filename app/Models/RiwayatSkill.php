<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatSkill extends Model
{
    use HasFactory;

    protected $table = 'riwayat_skills';

    protected $fillable = [
        'karyawan_id',
        'skill_id',
        'level_sebelum',
        'level_sesudah',
        'pengajuan_pengembangan_id',
        'hasil_assessment_id',
        'tanggal_perubahan',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'level_sebelum' => 'integer',
            'level_sesudah' => 'integer',
            'tanggal_perubahan' => 'date',
        ];
    }

    /**
     * Karyawan pemilik riwayat skill.
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    /**
     * Skill yang mengalami perubahan.
     */
    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    /**
     * Pengajuan yang menyebabkan peningkatan skill.
     */
    public function pengajuanPengembangan()
    {
        return $this->belongsTo(
            PengajuanPengembangan::class,
            'pengajuan_pengembangan_id'
        );
    }

    /**
     * Assessment yang menjadi dasar perubahan skill.
     */
    public function hasilAssessment()
    {
        return $this->belongsTo(
            HasilAssessment::class,
            'hasil_assessment_id'
        );
    }
}