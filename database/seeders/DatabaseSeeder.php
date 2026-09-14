<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\Karyawan;
use Illuminate\Database\Seeder;
use App\Models\Skill;
use App\Models\JabatanSkill;
use App\Models\KaryawanSkill;
use App\Models\Soal;
use App\Models\PilihanJawaban;
use App\Models\Assessment;
use App\Models\AssessmentSoal;
use App\Models\AssessmentPeserta;
use App\Models\Jawaban;
use App\Models\HasilAssessment;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ========================================
        // 1. DEPARTEMEN
        // ========================================

        $produksi = Departemen::create([
            'nama_departemen' => 'Produksi',
            'deskripsi' => 'Departemen yang menangani kegiatan produksi.',
        ]);

        $engineering = Departemen::create([
            'nama_departemen' => 'Engineering',
            'deskripsi' => 'Departemen yang menangani pemeliharaan dan teknis.',
        ]);

        $hr = Departemen::create([
            'nama_departemen' => 'Human Resources',
            'deskripsi' => 'Departemen yang menangani sumber daya manusia.',
        ]);


        // ========================================
        // 2. JABATAN
        // ========================================

        $operatorJunior = Jabatan::create([
            'nama_jabatan' => 'Operator Junior',
            'level_jabatan' => 1,
            'deskripsi' => 'Jabatan operator tingkat junior.',
            'standar_nilai' => 70,
        ]);

        $operatorSenior = Jabatan::create([
            'nama_jabatan' => 'Operator Senior',
            'level_jabatan' => 2,
            'deskripsi' => 'Jabatan operator tingkat senior.',
            'standar_nilai' => 80,
        ]);

        $teknisiJunior = Jabatan::create([
            'nama_jabatan' => 'Teknisi Junior',
            'level_jabatan' => 2,
            'deskripsi' => 'Jabatan teknisi tingkat junior.',
            'standar_nilai' => 75,
        ]);

        $teknisiSenior = Jabatan::create([
            'nama_jabatan' => 'Teknisi Senior',
            'level_jabatan' => 3,
            'deskripsi' => 'Jabatan teknisi tingkat senior.',
            'standar_nilai' => 80,
        ]);

        $supervisor = Jabatan::create([
            'nama_jabatan' => 'Supervisor',
            'level_jabatan' => 4,
            'deskripsi' => 'Jabatan supervisor.',
            'standar_nilai' => 85,
        ]);


        // ========================================
        // 3. USER HRD
        // ========================================

        User::create([
            'username' => 'hrd01',
            'name' => 'Admin HRD',
            'email' => 'hrd@example.com',
            'password' => 'password',
            'role' => 'hrd',
        ]);


        // ========================================
        // 4. USER KARYAWAN
        // ========================================

        $userBudi = User::create([
            'username' => 'budi01',
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password',
            'role' => 'karyawan',
        ]);

        $userAndi = User::create([
            'username' => 'andi01',
            'name' => 'Andi Pratama',
            'email' => 'andi@example.com',
            'password' => 'password',
            'role' => 'karyawan',
        ]);


        // ========================================
        // 5. DATA KARYAWAN
        // ========================================

        Karyawan::create([
            'user_id' => $userBudi->id,
            'nik' => 'KRY001',
            'nama' => 'Budi Santoso',
            'departemen_id' => $produksi->id,
            'jabatan_id' => $operatorJunior->id,
            'level' => 'Junior',
            'tanggal_masuk' => '2024-01-15',
            'status' => 'aktif',
        ]);

        Karyawan::create([
            'user_id' => $userAndi->id,
            'nik' => 'KRY002',
            'nama' => 'Andi Pratama',
            'departemen_id' => $engineering->id,
            'jabatan_id' => $teknisiJunior->id,
            'level' => 'Junior',
            'tanggal_masuk' => '2023-06-01',
            'status' => 'aktif',
        ]);

        // ========================================
        // 6. SKILL
        // ========================================

        $troubleshooting = Skill::create([
            'nama_skill' => 'Troubleshooting',
            'deskripsi' => 'Kemampuan mengidentifikasi dan menangani masalah pada peralatan atau proses kerja.',
        ]);

        $technical = Skill::create([
            'nama_skill' => 'Technical Skill',
            'deskripsi' => 'Kemampuan teknis sesuai dengan bidang pekerjaan.',
        ]);

        $safety = Skill::create([
            'nama_skill' => 'Safety',
            'deskripsi' => 'Kemampuan memahami dan menerapkan prosedur keselamatan kerja.',
        ]);

        $communication = Skill::create([
            'nama_skill' => 'Communication',
            'deskripsi' => 'Kemampuan berkomunikasi secara efektif dalam lingkungan kerja.',
        ]);

        $leadership = Skill::create([
            'nama_skill' => 'Leadership',
            'deskripsi' => 'Kemampuan memimpin, mengarahkan, dan mengelola anggota tim.',
        ]);

        $problemSolving = Skill::create([
            'nama_skill' => 'Problem Solving',
            'deskripsi' => 'Kemampuan menganalisis masalah dan menentukan solusi yang tepat.',
        ]);

        // ========================================
        // 7. JABATAN SKILL
        // ========================================

        // Operator Junior
        JabatanSkill::create([
            'jabatan_id' => $operatorJunior->id,
            'skill_id' => $technical->id,
            'level_dibutuhkan' => 2,
        ]);

        JabatanSkill::create([
            'jabatan_id' => $operatorJunior->id,
            'skill_id' => $safety->id,
            'level_dibutuhkan' => 2,
        ]);

        JabatanSkill::create([
            'jabatan_id' => $operatorJunior->id,
            'skill_id' => $troubleshooting->id,
            'level_dibutuhkan' => 1,
        ]);

        // Operator Senior
        JabatanSkill::create([
            'jabatan_id' => $operatorSenior->id,
            'skill_id' => $technical->id,
            'level_dibutuhkan' => 3,
        ]);

        JabatanSkill::create([
            'jabatan_id' => $operatorSenior->id,
            'skill_id' => $safety->id,
            'level_dibutuhkan' => 3,
        ]);

        JabatanSkill::create([
            'jabatan_id' => $operatorSenior->id,
            'skill_id' => $troubleshooting->id,
            'level_dibutuhkan' => 3,
        ]);

        JabatanSkill::create([
            'jabatan_id' => $operatorSenior->id,
            'skill_id' => $problemSolving->id,
            'level_dibutuhkan' => 2,
        ]);

        // Teknisi Junior
        JabatanSkill::create([
            'jabatan_id' => $teknisiJunior->id,
            'skill_id' => $technical->id,
            'level_dibutuhkan' => 3,
        ]);

        JabatanSkill::create([
            'jabatan_id' => $teknisiJunior->id,
            'skill_id' => $safety->id,
            'level_dibutuhkan' => 3,
        ]);

        JabatanSkill::create([
            'jabatan_id' => $teknisiJunior->id,
            'skill_id' => $troubleshooting->id,
            'level_dibutuhkan' => 3,
        ]);

        JabatanSkill::create([
            'jabatan_id' => $teknisiJunior->id,
            'skill_id' => $problemSolving->id,
            'level_dibutuhkan' => 2,
        ]);

        // Teknisi Senior
        JabatanSkill::create([
            'jabatan_id' => $teknisiSenior->id,
            'skill_id' => $technical->id,
            'level_dibutuhkan' => 4,
        ]);

        JabatanSkill::create([
            'jabatan_id' => $teknisiSenior->id,
            'skill_id' => $safety->id,
            'level_dibutuhkan' => 4,
        ]);

        JabatanSkill::create([
            'jabatan_id' => $teknisiSenior->id,
            'skill_id' => $troubleshooting->id,
            'level_dibutuhkan' => 4,
        ]);

        JabatanSkill::create([
            'jabatan_id' => $teknisiSenior->id,
            'skill_id' => $problemSolving->id,
            'level_dibutuhkan' => 4,
        ]);

        JabatanSkill::create([
            'jabatan_id' => $teknisiSenior->id,
            'skill_id' => $communication->id,
            'level_dibutuhkan' => 3,
        ]);

        // Supervisor
        JabatanSkill::create([
            'jabatan_id' => $supervisor->id,
            'skill_id' => $leadership->id,
            'level_dibutuhkan' => 4,
        ]);

        JabatanSkill::create([
            'jabatan_id' => $supervisor->id,
            'skill_id' => $communication->id,
            'level_dibutuhkan' => 4,
        ]);

        JabatanSkill::create([
            'jabatan_id' => $supervisor->id,
            'skill_id' => $problemSolving->id,
            'level_dibutuhkan' => 4,
        ]);

        JabatanSkill::create([
            'jabatan_id' => $supervisor->id,
            'skill_id' => $safety->id,
            'level_dibutuhkan' => 4,
        ]);

        // ========================================
        // 8. KARYAWAN SKILL
        // ========================================

        // Ambil karyawan yang sudah dibuat sebelumnya
        $budi = Karyawan::where('nik', 'KRY001')->first();
        $andi = Karyawan::where('nik', 'KRY002')->first();

        // Skill Budi
        KaryawanSkill::create([
            'karyawan_id' => $budi->id,
            'skill_id' => $technical->id,
            'level_skill' => 2,
            'tanggal_penilaian' => '2026-09-01',
        ]);

        KaryawanSkill::create([
            'karyawan_id' => $budi->id,
            'skill_id' => $safety->id,
            'level_skill' => 2,
            'tanggal_penilaian' => '2026-09-01',
        ]);

        KaryawanSkill::create([
            'karyawan_id' => $budi->id,
            'skill_id' => $troubleshooting->id,
            'level_skill' => 1,
            'tanggal_penilaian' => '2026-09-01',
        ]);

        KaryawanSkill::create([
            'karyawan_id' => $budi->id,
            'skill_id' => $problemSolving->id,
            'level_skill' => 1,
            'tanggal_penilaian' => '2026-09-01',
        ]);

        // Skill Andi
        KaryawanSkill::create([
            'karyawan_id' => $andi->id,
            'skill_id' => $technical->id,
            'level_skill' => 3,
            'tanggal_penilaian' => '2026-09-01',
        ]);

        KaryawanSkill::create([
            'karyawan_id' => $andi->id,
            'skill_id' => $safety->id,
            'level_skill' => 3,
            'tanggal_penilaian' => '2026-09-01',
        ]);

        KaryawanSkill::create([
            'karyawan_id' => $andi->id,
            'skill_id' => $troubleshooting->id,
            'level_skill' => 3,
            'tanggal_penilaian' => '2026-09-01',
        ]);

        KaryawanSkill::create([
            'karyawan_id' => $andi->id,
            'skill_id' => $problemSolving->id,
            'level_skill' => 2,
            'tanggal_penilaian' => '2026-09-01',
        ]);

        // ========================================
        // 9. BANK SOAL
        // ========================================

        // ----------------------------------------
        // SOAL 1 - PILIHAN TUNGGAL ABCD
        // ----------------------------------------

        $soal1 = Soal::create([
            'jabatan_id' => $operatorJunior->id,
            'skill_id' => $safety->id,
            'pertanyaan' => 'Apa tujuan utama penggunaan alat pelindung diri (APD) di tempat kerja?',
            'tipe_soal' => 'pilihan_tunggal',
            'tingkat_kesulitan' => 'mudah',
            'bobot' => 5,
            'status' => true,
        ]);

        PilihanJawaban::create([
            'soal_id' => $soal1->id,
            'kode' => 'A',
            'teks_jawaban' => 'Melindungi pekerja dari potensi bahaya',
            'is_benar' => true,
        ]);

        PilihanJawaban::create([
            'soal_id' => $soal1->id,
            'kode' => 'B',
            'teks_jawaban' => 'Mempercepat proses produksi',
            'is_benar' => false,
        ]);

        PilihanJawaban::create([
            'soal_id' => $soal1->id,
            'kode' => 'C',
            'teks_jawaban' => 'Mengurangi jumlah pekerja',
            'is_benar' => false,
        ]);

        PilihanJawaban::create([
            'soal_id' => $soal1->id,
            'kode' => 'D',
            'teks_jawaban' => 'Mengurangi penggunaan mesin',
            'is_benar' => false,
        ]);


        // ----------------------------------------
        // SOAL 2 - PILIHAN TUNGGAL ABCD
        // ----------------------------------------

        $soal2 = Soal::create([
            'jabatan_id' => $operatorJunior->id,
            'skill_id' => $technical->id,
            'pertanyaan' => 'Apa fungsi utama preventive maintenance?',
            'tipe_soal' => 'pilihan_tunggal',
            'tingkat_kesulitan' => 'sedang',
            'bobot' => 5,
            'status' => true,
        ]);

        PilihanJawaban::create([
            'soal_id' => $soal2->id,
            'kode' => 'A',
            'teks_jawaban' => 'Mencegah atau mengurangi kemungkinan terjadinya kerusakan',
            'is_benar' => true,
        ]);

        PilihanJawaban::create([
            'soal_id' => $soal2->id,
            'kode' => 'B',
            'teks_jawaban' => 'Menghentikan seluruh kegiatan produksi',
            'is_benar' => false,
        ]);

        PilihanJawaban::create([
            'soal_id' => $soal2->id,
            'kode' => 'C',
            'teks_jawaban' => 'Mengurangi jumlah mesin',
            'is_benar' => false,
        ]);

        PilihanJawaban::create([
            'soal_id' => $soal2->id,
            'kode' => 'D',
            'teks_jawaban' => 'Menghilangkan kebutuhan operator',
            'is_benar' => false,
        ]);


        // ----------------------------------------
        // SOAL 3 - MULTI JAWABAN ABCD
        // ----------------------------------------

        $soal3 = Soal::create([
            'jabatan_id' => $operatorSenior->id,
            'skill_id' => $safety->id,
            'pertanyaan' => 'Manakah yang termasuk alat pelindung diri (APD)?',
            'tipe_soal' => 'multi_jawaban',
            'tingkat_kesulitan' => 'mudah',
            'bobot' => 10,
            'status' => true,
        ]);

        PilihanJawaban::create([
            'soal_id' => $soal3->id,
            'kode' => 'A',
            'teks_jawaban' => 'Helm keselamatan',
            'is_benar' => true,
        ]);

        PilihanJawaban::create([
            'soal_id' => $soal3->id,
            'kode' => 'B',
            'teks_jawaban' => 'Safety shoes',
            'is_benar' => true,
        ]);

        PilihanJawaban::create([
            'soal_id' => $soal3->id,
            'kode' => 'C',
            'teks_jawaban' => 'Meja kerja',
            'is_benar' => false,
        ]);

        PilihanJawaban::create([
            'soal_id' => $soal3->id,
            'kode' => 'D',
            'teks_jawaban' => 'Sarung tangan keselamatan',
            'is_benar' => true,
        ]);


        // ----------------------------------------
        // SOAL 4 - MULTI JAWABAN ABCD
        // ----------------------------------------

        $soal4 = Soal::create([
            'jabatan_id' => $teknisiJunior->id,
            'skill_id' => $troubleshooting->id,
            'pertanyaan' => 'Manakah yang merupakan langkah awal dalam troubleshooting?',
            'tipe_soal' => 'multi_jawaban',
            'tingkat_kesulitan' => 'sedang',
            'bobot' => 10,
            'status' => true,
        ]);

        PilihanJawaban::create([
            'soal_id' => $soal4->id,
            'kode' => 'A',
            'teks_jawaban' => 'Mengidentifikasi gejala masalah',
            'is_benar' => true,
        ]);

        PilihanJawaban::create([
            'soal_id' => $soal4->id,
            'kode' => 'B',
            'teks_jawaban' => 'Mengumpulkan informasi terkait masalah',
            'is_benar' => true,
        ]);

        PilihanJawaban::create([
            'soal_id' => $soal4->id,
            'kode' => 'C',
            'teks_jawaban' => 'Langsung mengganti seluruh komponen',
            'is_benar' => false,
        ]);

        PilihanJawaban::create([
            'soal_id' => $soal4->id,
            'kode' => 'D',
            'teks_jawaban' => 'Mengabaikan gejala kerusakan',
            'is_benar' => false,
        ]);


        // ----------------------------------------
        // SOAL 5 - BENAR / SALAH
        // ----------------------------------------

        $soal5 = Soal::create([
            'jabatan_id' => $teknisiJunior->id,
            'skill_id' => $safety->id,
            'pertanyaan' => 'Penggunaan APD merupakan salah satu upaya untuk mengurangi risiko kecelakaan kerja.',
            'tipe_soal' => 'benar_salah',
            'tingkat_kesulitan' => 'mudah',
            'bobot' => 5,
            'status' => true,
        ]);

        PilihanJawaban::create([
            'soal_id' => $soal5->id,
            'kode' => 'A',
            'teks_jawaban' => 'Benar',
            'is_benar' => true,
        ]);

        PilihanJawaban::create([
            'soal_id' => $soal5->id,
            'kode' => 'B',
            'teks_jawaban' => 'Salah',
            'is_benar' => false,
        ]);


        // ----------------------------------------
        // SOAL 6 - BENAR / SALAH
        // ----------------------------------------

        $soal6 = Soal::create([
            'jabatan_id' => $teknisiSenior->id,
            'skill_id' => $troubleshooting->id,
            'pertanyaan' => 'Troubleshooting dilakukan tanpa perlu mengidentifikasi gejala kerusakan.',
            'tipe_soal' => 'benar_salah',
            'tingkat_kesulitan' => 'sedang',
            'bobot' => 5,
            'status' => true,
        ]);

        PilihanJawaban::create([
            'soal_id' => $soal6->id,
            'kode' => 'A',
            'teks_jawaban' => 'Benar',
            'is_benar' => false,
        ]);

        PilihanJawaban::create([
            'soal_id' => $soal6->id,
            'kode' => 'B',
            'teks_jawaban' => 'Salah',
            'is_benar' => true,
        ]);

        // ========================================
        // 10. ASSESSMENT
        // ========================================

        // ----------------------------------------
        // ASSESSMENT 1
        // Kenaikan Jabatan Operator Senior
        // ----------------------------------------

        $assessmentKenaikan = Assessment::create([
            'nama_assessment' => 'Ujian Kenaikan Operator Senior',
            'jabatan_id' => $operatorSenior->id,
            'skill_id' => null,
            'standar_nilai' => 80,
            'durasi' => 60,
            'tanggal_mulai' => '2026-09-10 08:00:00',
            'tanggal_selesai' => '2026-12-31 17:00:00',
            'status' => 'aktif',
        ]);

        // Soal untuk Assessment Kenaikan Operator Senior

        AssessmentSoal::create([
            'assessment_id' => $assessmentKenaikan->id,
            'soal_id' => $soal1->id,
            'nomor_soal' => 1,
        ]);

        AssessmentSoal::create([
            'assessment_id' => $assessmentKenaikan->id,
            'soal_id' => $soal2->id,
            'nomor_soal' => 2,
        ]);

        AssessmentSoal::create([
            'assessment_id' => $assessmentKenaikan->id,
            'soal_id' => $soal3->id,
            'nomor_soal' => 3,
        ]);

        // ----------------------------------------
        // ASSESSMENT 2
        // Pemindahan Jabatan ke Teknisi Junior
        // ----------------------------------------

        $assessmentPemindahan = Assessment::create([
            'nama_assessment' => 'Ujian Pemindahan ke Teknisi Junior',
            'jabatan_id' => $teknisiJunior->id,
            'skill_id' => null,
            'standar_nilai' => 80,
            'durasi' => 60,
            'tanggal_mulai' => '2026-09-10 08:00:00',
            'tanggal_selesai' => '2026-12-31 17:00:00',
            'status' => 'aktif',
        ]);

        AssessmentSoal::create([
            'assessment_id' => $assessmentPemindahan->id,
            'soal_id' => $soal2->id,
            'nomor_soal' => 1,
        ]);

        AssessmentSoal::create([
            'assessment_id' => $assessmentPemindahan->id,
            'soal_id' => $soal4->id,
            'nomor_soal' => 2,
        ]);

        AssessmentSoal::create([
            'assessment_id' => $assessmentPemindahan->id,
            'soal_id' => $soal5->id,
            'nomor_soal' => 3,
        ]);

        AssessmentSoal::create([
            'assessment_id' => $assessmentPemindahan->id,
            'soal_id' => $soal6->id,
            'nomor_soal' => 4,
        ]);

        // ----------------------------------------
        // ASSESSMENT 3
        // Peningkatan Skill Troubleshooting
        // ----------------------------------------

        $assessmentSkill = Assessment::create([
            'nama_assessment' => 'Assessment Peningkatan Skill Troubleshooting',
            'jabatan_id' => $teknisiSenior->id,
            'skill_id' => $troubleshooting->id,
            'standar_nilai' => 75,
            'durasi' => 45,
            'tanggal_mulai' => '2026-09-10 08:00:00',
            'tanggal_selesai' => '2026-12-31 17:00:00',
            'status' => 'aktif',
        ]);

        AssessmentSoal::create([
            'assessment_id' => $assessmentSkill->id,
            'soal_id' => $soal4->id,
            'nomor_soal' => 1,
        ]);

        AssessmentSoal::create([
            'assessment_id' => $assessmentSkill->id,
            'soal_id' => $soal6->id,
            'nomor_soal' => 2,
        ]);

        // ========================================
        // 11. ASSESSMENT PESERTA
        // ========================================

        // Budi mengikuti assessment kenaikan jabatan
        AssessmentPeserta::create([
            'assessment_id' => $assessmentKenaikan->id,
            'karyawan_id' => $budi->id,
            'status' => 'ditugaskan',
        ]);

        // Budi mengikuti assessment pemindahan jabatan
        AssessmentPeserta::create([
            'assessment_id' => $assessmentPemindahan->id,
            'karyawan_id' => $budi->id,
            'status' => 'ditugaskan',
        ]);

        // Andi mengikuti assessment peningkatan skill
        AssessmentPeserta::create([
            'assessment_id' => $assessmentSkill->id,
            'karyawan_id' => $andi->id,
            'status' => 'ditugaskan',
        ]);

        // ========================================
        // 12. JAWABAN & HASIL ASSESSMENT
        // ========================================

        // ----------------------------------------
        // BUDI - UJIAN KENAIKAN OPERATOR SENIOR
        // ----------------------------------------

        $pesertaKenaikan = AssessmentPeserta::where('assessment_id', $assessmentKenaikan->id)
            ->where('karyawan_id', $budi->id)
            ->first();

        $pesertaKenaikan->update([
            'status' => 'selesai',
            'waktu_mulai' => '2026-09-10 08:00:00',
            'waktu_selesai' => '2026-09-10 08:35:00',
        ]);

        // Ambil pilihan jawaban yang benar untuk setiap soal
        $jawabanSoal1 = $soal1->pilihanJawabans()
            ->where('is_benar', true)
            ->first();

        $jawabanSoal2 = $soal2->pilihanJawabans()
            ->where('is_benar', true)
            ->first();

        $jawabanSoal3 = $soal3->pilihanJawabans()
            ->where('is_benar', true)
            ->get();

        // Soal 1 - Pilihan Tunggal
        Jawaban::create([
            'assessment_peserta_id' => $pesertaKenaikan->id,
            'soal_id' => $soal1->id,
            'pilihan_jawaban_id' => $jawabanSoal1->id,
        ]);

        // Soal 2 - Pilihan Tunggal
        Jawaban::create([
            'assessment_peserta_id' => $pesertaKenaikan->id,
            'soal_id' => $soal2->id,
            'pilihan_jawaban_id' => $jawabanSoal2->id,
        ]);

        // Soal 3 - Multi Jawaban
        foreach ($jawabanSoal3 as $pilihan) {
            Jawaban::create([
                'assessment_peserta_id' => $pesertaKenaikan->id,
                'soal_id' => $soal3->id,
                'pilihan_jawaban_id' => $pilihan->id,
            ]);
        }

        HasilAssessment::create([
            'assessment_peserta_id' => $pesertaKenaikan->id,
            'nilai_akhir' => 100,
            'standar_nilai' => $assessmentKenaikan->standar_nilai,
            'status' => 'lulus',
            'tanggal_ujian' => '2026-09-10 08:00:00',
            'waktu_mulai' => '2026-09-10 08:00:00',
            'waktu_selesai' => '2026-09-10 08:35:00',
        ]);

        // ----------------------------------------
        // BUDI - UJIAN PEMINDAHAN KE TEKNISI JUNIOR
        // ----------------------------------------

        $pesertaPemindahan = AssessmentPeserta::where(
            'assessment_id',
            $assessmentPemindahan->id
        )
            ->where('karyawan_id', $budi->id)
            ->first();

        $pesertaPemindahan->update([
            'status' => 'selesai',
            'waktu_mulai' => '2026-09-11 08:00:00',
            'waktu_selesai' => '2026-09-11 08:40:00',
        ]);

        // Soal 2 - Pilihan Tunggal
        Jawaban::create([
            'assessment_peserta_id' => $pesertaPemindahan->id,
            'soal_id' => $soal2->id,
            'pilihan_jawaban_id' => $jawabanSoal2->id,
        ]);

        // Soal 4 - Multi Jawaban
        $jawabanSoal4 = $soal4->pilihanJawabans()
            ->where('is_benar', true)
            ->get();

        foreach ($jawabanSoal4 as $pilihan) {
            Jawaban::create([
                'assessment_peserta_id' => $pesertaPemindahan->id,
                'soal_id' => $soal4->id,
                'pilihan_jawaban_id' => $pilihan->id,
            ]);
        }

        // Soal 5 - Benar/Salah
        $jawabanSoal5 = $soal5->pilihanJawabans()
            ->where('is_benar', true)
            ->first();

        Jawaban::create([
            'assessment_peserta_id' => $pesertaPemindahan->id,
            'soal_id' => $soal5->id,
            'pilihan_jawaban_id' => $jawabanSoal5->id,
        ]);

        // Soal 6 - Benar/Salah
        $jawabanSoal6 = $soal6->pilihanJawabans()
            ->where('is_benar', true)
            ->first();

        Jawaban::create([
            'assessment_peserta_id' => $pesertaPemindahan->id,
            'soal_id' => $soal6->id,
            'pilihan_jawaban_id' => $jawabanSoal6->id,
        ]);

        HasilAssessment::create([
            'assessment_peserta_id' => $pesertaPemindahan->id,
            'nilai_akhir' => 85,
            'standar_nilai' => $assessmentPemindahan->standar_nilai,
            'status' => 'lulus',
            'tanggal_ujian' => '2026-09-11 08:00:00',
            'waktu_mulai' => '2026-09-11 08:00:00',
            'waktu_selesai' => '2026-09-11 08:40:00',
        ]);

        // ----------------------------------------
        // ANDI - ASSESSMENT PENINGKATAN SKILL
        // ----------------------------------------

        $pesertaSkill = AssessmentPeserta::where(
            'assessment_id',
            $assessmentSkill->id
        )
            ->where('karyawan_id', $andi->id)
            ->first();

        $pesertaSkill->update([
            'status' => 'selesai',
            'waktu_mulai' => '2026-09-12 09:00:00',
            'waktu_selesai' => '2026-09-12 09:25:00',
        ]);

        // Soal 4
        $jawabanSkill1 = $soal4->pilihanJawabans()
            ->where('is_benar', true)
            ->first();

        Jawaban::create([
            'assessment_peserta_id' => $pesertaSkill->id,
            'soal_id' => $soal4->id,
            'pilihan_jawaban_id' => $jawabanSkill1->id,
        ]);

        // Soal 6
        $jawabanSkill2 = $soal6->pilihanJawabans()
            ->where('is_benar', true)
            ->first();

        Jawaban::create([
            'assessment_peserta_id' => $pesertaSkill->id,
            'soal_id' => $soal6->id,
            'pilihan_jawaban_id' => $jawabanSkill2->id,
        ]);

        HasilAssessment::create([
            'assessment_peserta_id' => $pesertaSkill->id,
            'nilai_akhir' => 60,
            'standar_nilai' => $assessmentSkill->standar_nilai,
            'status' => 'tidak_lulus',
            'tanggal_ujian' => '2026-09-12 09:00:00',
            'waktu_mulai' => '2026-09-12 09:00:00',
            'waktu_selesai' => '2026-09-12 09:25:00',
        ]);
    }
}