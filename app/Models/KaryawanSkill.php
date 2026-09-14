<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KaryawanSkill extends Model
{
    use HasFactory;

    protected $table = 'karyawan_skill';

    protected $fillable = [
        'karyawan_id',
        'skill_id',
        'level_skill',
        'tanggal_penilaian',
    ];

    protected function casts(): array
    {
        return [
            'level_skill' => 'integer',
            'tanggal_penilaian' => 'date',
        ];
    }

    /**
     * Relasi ke Karyawan.
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    /**
     * Relasi ke Skill.
     */
    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }
}