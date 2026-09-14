<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatJabatan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_jabatans';

    protected $fillable = [
        'karyawan_id',
        'jabatan_id',
        'jenis_perubahan',
        'pengajuan_pengembangan_id',
        'hasil_assessment_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    /**
     * Karyawan pemilik riwayat.
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    /**
     * Jabatan yang ditempati.
     */
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    /**
     * Pengajuan yang menyebabkan perubahan jabatan.
     */
    public function pengajuanPengembangan()
    {
        return $this->belongsTo(
            PengajuanPengembangan::class,
            'pengajuan_pengembangan_id'
        );
    }

    /**
     * Assessment yang menjadi dasar perubahan.
     */
    public function hasilAssessment()
    {
        return $this->belongsTo(
            HasilAssessment::class,
            'hasil_assessment_id'
        );
    }
}