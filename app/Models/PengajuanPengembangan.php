<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanPengembangan extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_pengembangans';

    protected $fillable = [
        'karyawan_id',
        'hasil_assessment_id',
        'jenis_pengajuan',
        'jabatan_asal_id',
        'jabatan_tujuan_id',
        'skill_id',
        'status',
        'tanggal_pengajuan',
        'tanggal_keputusan',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengajuan' => 'date',
            'tanggal_keputusan' => 'date',
            'level_sebelum' => 'integer',
            'level_sesudah' => 'integer',
        ];
    }

    /**
     * Karyawan yang mengajukan / menjadi objek pengembangan.
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    /**
     * Hasil assessment yang menjadi dasar pengajuan.
     */
    public function hasilAssessment()
    {
        return $this->belongsTo(HasilAssessment::class);
    }

    /**
     * Jabatan asal karyawan.
     */
    public function jabatanAsal()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_asal_id');
    }

    /**
     * Jabatan tujuan karyawan.
     */
    public function jabatanTujuan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_tujuan_id');
    }

    /**
     * Skill yang ditingkatkan.
     */
    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    /**
     * Riwayat jabatan yang dihasilkan dari pengajuan ini.
     */
    public function riwayatJabatans()
    {
        return $this->hasMany(RiwayatJabatan::class);
    }

    /**
     * Riwayat skill yang dihasilkan dari pengajuan ini.
     */
    public function riwayatSkills()
    {
        return $this->hasMany(RiwayatSkill::class);
    }
}