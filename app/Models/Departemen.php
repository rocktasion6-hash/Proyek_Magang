<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departemen extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_departemen',
        'deskripsi',
    ];

    public function karyawans()
    {
        return $this->hasMany(Karyawan::class);
    }

    public function soalUploads(): HasMany
    {
        return $this->hasMany(SoalUpload::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }
}
