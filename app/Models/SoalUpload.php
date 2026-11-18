<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class SoalUpload extends Model
{
    protected $fillable = ['jabatan_id', 'departemen_id', 'nama_file', 'jumlah_soal'];

    protected function namaFileTampilan(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): string => pathinfo(
                $attributes['nama_file'] ?? '',
                PATHINFO_FILENAME
            ),
        );
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }

    public function soals()
    {
        return $this->hasMany(Soal::class, 'upload_id');
    }
}
