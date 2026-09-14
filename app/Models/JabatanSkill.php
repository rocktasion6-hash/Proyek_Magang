<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JabatanSkill extends Model
{
    use HasFactory;

    protected $table = 'jabatan_skill';

    protected $fillable = [
        'jabatan_id',
        'skill_id',
        'level_dibutuhkan',
    ];

    protected function casts(): array
    {
        return [
            'level_dibutuhkan' => 'integer',
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
}