<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HrdDashboardController;
use App\Http\Controllers\KaryawanDashboardController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\Hrd\SoalController;
use App\Http\Controllers\Hrd\AssessmentController as HrdAssessmentController;
use App\Http\Controllers\Hrd\HasilAssessmentController;
use App\Http\Controllers\Hrd\KenaikanJabatanController;
use App\Http\Controllers\Hrd\PemindahanJabatanController;
use App\Http\Controllers\Hrd\PeningkatanSkillController;
use App\Http\Controllers\Hrd\RiwayatController;
use App\Http\Controllers\Hrd\KaryawanController;
use App\Http\Controllers\Hrd\DepartemenController;
use App\Http\Controllers\Hrd\JabatanController;
use App\Http\Controllers\Hrd\SkillController;
use App\Http\Controllers\Hrd\JabatanSkillController;
use App\Http\Controllers\Hrd\KaryawanSkillController;
use App\Http\Controllers\Hrd\LaporanController;
/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.process');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');


/*
|--------------------------------------------------------------------------
| Dashboard HRD
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:hrd'])->group(function () {
    Route::get('/hrd/dashboard', [HrdDashboardController::class, 'index'])->name('hrd.dashboard');
    Route::get('/hrd/soal/tambah', [SoalController::class, 'create'])->name('hrd.soal.create');
    Route::post('/hrd/soal', [SoalController::class, 'store'])->name('hrd.soal.store');
    Route::get('/hrd/soal/{soal}/edit', [SoalController::class,'edit'])->name('hrd.soal.edit');
    Route::put('/hrd/soal/{soal}', [SoalController::class, 'update'])->name('hrd.soal.update');
    Route::delete('/hrd/soal/{soal}', [SoalController::class,'destroy'])->name('hrd.soal.destroy');
    Route::get('/hrd/soal', [SoalController::class, 'index'])->name('hrd.soal.index');
    Route::get('/hrd/soal/{soal}', [SoalController::class, 'show'])->name('hrd.soal.show');
    Route::get('/hrd/assessment', [HrdAssessmentController::class,'index'])->name('hrd.assessment.index');
    Route::get('/hrd/assessment/tambah', [HrdAssessmentController::class,'create'])->name('hrd.assessment.create');
    Route::post('/hrd/assessment', [HrdAssessmentController::class,'store'])->name('hrd.assessment.store');
    Route::get('/hrd/assessment/{assessment}', [HrdAssessmentController::class,'show'])->name('hrd.assessment.show');
    Route::get('/hrd/hasil-penilaian', [HasilAssessmentController::class,'index'])->name('hrd.hasil-assessment.index');
    Route::get('/hrd/hasil-penilaian/{hasilAssessment}', [HasilAssessmentController::class,'show'])->name('hrd.hasil-assessment.show');
    Route::get('/hrd/kenaikan-jabatan', [KenaikanJabatanController::class,'index'])->name('hrd.kenaikan-jabatan.index');
    Route::get('/hrd/kenaikan-jabatan/tambah', [KenaikanJabatanController::class,'create'])->name('hrd.kenaikan-jabatan.create');
    Route::post('/hrd/kenaikan-jabatan', [KenaikanJabatanController::class,'store'])->name('hrd.kenaikan-jabatan.store');
    Route::get('/hrd/kenaikan-jabatan/{pengajuan}', [KenaikanJabatanController::class,'show'])->name('hrd.kenaikan-jabatan.show');
    Route::post('/hrd/kenaikan-jabatan/{pengajuan}/proses', [KenaikanJabatanController::class,'process'])->name('hrd.kenaikan-jabatan.process');
    Route::post('/hrd/kenaikan-jabatan/{pengajuan}/setujui', [KenaikanJabatanController::class,'approve'])->name('hrd.kenaikan-jabatan.approve');
    Route::post('/hrd/kenaikan-jabatan/{pengajuan}/tolak', [KenaikanJabatanController::class,'reject'])->name('hrd.kenaikan-jabatan.reject');
    Route::get('/hrd/pemindahan-jabatan', [PemindahanJabatanController::class,'index'])->name('hrd.pemindahan-jabatan.index');
    Route::get('/hrd/pemindahan-jabatan/tambah', [PemindahanJabatanController::class,'create'])->name('hrd.pemindahan-jabatan.create');
    Route::post('/hrd/pemindahan-jabatan', [PemindahanJabatanController::class,'store'])->name('hrd.pemindahan-jabatan.store');
    Route::get('/hrd/pemindahan-jabatan/{pengajuan}', [PemindahanJabatanController::class,'show'])->name('hrd.pemindahan-jabatan.show');
    Route::post('/hrd/pemindahan-jabatan/{pengajuan}/proses', [PemindahanJabatanController::class,'process'])->name('hrd.pemindahan-jabatan.process');
    Route::post('/hrd/pemindahan-jabatan/{pengajuan}/setujui', [PemindahanJabatanController::class,'approve'])->name('hrd.pemindahan-jabatan.approve');
    Route::post('/hrd/pemindahan-jabatan/{pengajuan}/tolak', [PemindahanJabatanController::class,'reject'])->name('hrd.pemindahan-jabatan.reject');
    Route::get('/hrd/peningkatan-skill', [PeningkatanSkillController::class,'index'])->name('hrd.peningkatan-skill.index');
    Route::get('/hrd/peningkatan-skill/tambah', [PeningkatanSkillController::class,'create'])->name('hrd.peningkatan-skill.create');
    Route::post('/hrd/peningkatan-skill', [PeningkatanSkillController::class,'store'])->name('hrd.peningkatan-skill.store');
    Route::get('/hrd/peningkatan-skill/{pengajuan}', [PeningkatanSkillController::class,'show'])->name('hrd.peningkatan-skill.show');
    Route::post('/hrd/peningkatan-skill/{pengajuan}/proses', [PeningkatanSkillController::class,'process'])->name('hrd.peningkatan-skill.process');
    Route::post('/hrd/peningkatan-skill/{pengajuan}/setujui', [PeningkatanSkillController::class,'approve'])->name('hrd.peningkatan-skill.approve');
    Route::post('/hrd/peningkatan-skill/{pengajuan}/tolak', [PeningkatanSkillController::class,'reject'])->name('hrd.peningkatan-skill.reject');
    Route::get('/hrd/riwayat', [RiwayatController::class,'index'])->name('hrd.riwayat.index');
    Route::get('/hrd/riwayat/{karyawan}', [RiwayatController::class,'show'])->name('hrd.riwayat.show');
    Route::get('/hrd/karyawan', [KaryawanController::class,'index'])->name('hrd.karyawan.index');
    Route::get('/hrd/karyawan/tambah', [KaryawanController::class,'create'])->name('hrd.karyawan.create');
    Route::post('/hrd/karyawan', [KaryawanController::class,'store'])->name('hrd.karyawan.store');
    Route::get('/hrd/karyawan/{karyawan}', [KaryawanController::class,'show'])->name('hrd.karyawan.show');
    Route::get('/hrd/karyawan/{karyawan}/edit', [KaryawanController::class,'edit'])->name('hrd.karyawan.edit');
    Route::put('/hrd/karyawan/{karyawan}', [KaryawanController::class,'update'])->name('hrd.karyawan.update');
    Route::delete('/hrd/karyawan/{karyawan}', [KaryawanController::class,'destroy'])->name('hrd.karyawan.destroy');
    Route::get('/hrd/departemen', [DepartemenController::class,'index'])->name('hrd.departemen.index');
    Route::get('/hrd/departemen/tambah', [DepartemenController::class,'create'])->name('hrd.departemen.create');
    Route::post('/hrd/departemen', [DepartemenController::class,'store'])->name('hrd.departemen.store');
    Route::get('/hrd/departemen/{departemen}', [DepartemenController::class,'show'])->name('hrd.departemen.show');
    Route::get('/hrd/departemen/{departemen}/edit', [DepartemenController::class,'edit'])->name('hrd.departemen.edit');
    Route::put('/hrd/departemen/{departemen}', [DepartemenController::class,'update'])->name('hrd.departemen.update');
    Route::delete('/hrd/departemen/{departemen}', [DepartemenController::class,'destroy'])->name('hrd.departemen.destroy');
    Route::get('/hrd/jabatan', [JabatanController::class,'index'])->name('hrd.jabatan.index');
    Route::get('/hrd/jabatan/tambah', [JabatanController::class,'create'])->name('hrd.jabatan.create');
    Route::post('/hrd/jabatan', [JabatanController::class,'store'])->name('hrd.jabatan.store');
    Route::get('/hrd/jabatan/{jabatan}', [JabatanController::class,'show'])->name('hrd.jabatan.show');
    Route::get('/hrd/jabatan/{jabatan}/edit', [JabatanController::class,'edit'])->name('hrd.jabatan.edit');
    Route::put('/hrd/jabatan/{jabatan}', [JabatanController::class,'update'])->name('hrd.jabatan.update');
    Route::delete('/hrd/jabatan/{jabatan}', [JabatanController::class,'destroy'])->name('hrd.jabatan.destroy');
    Route::get('/hrd/skill', [SkillController::class,'index'])->name('hrd.skill.index');
    Route::get('/hrd/skill/tambah', [SkillController::class,'create'])->name('hrd.skill.create');
    Route::post('/hrd/skill', [SkillController::class,'store'])->name('hrd.skill.store');
    Route::get('/hrd/skill/{skill}', [SkillController::class,'show'])->name('hrd.skill.show');
    Route::get('/hrd/skill/{skill}/edit', [SkillController::class,'edit'])->name('hrd.skill.edit');
    Route::put('/hrd/skill/{skill}', [SkillController::class,'update'])->name('hrd.skill.update');
    Route::delete('/hrd/skill/{skill}', [SkillController::class,'destroy'])->name('hrd.skill.destroy');
    Route::post('/hrd/jabatan/{jabatan}/skill', [JabatanSkillController::class,'store'])->name('hrd.jabatan.skill.store');
    Route::delete('/hrd/jabatan/{jabatan}/skill/{jabatanSkill}', [JabatanSkillController::class,'destroy'])->name('hrd.jabatan.skill.destroy');
    Route::post('/hrd/karyawan/{karyawan}/skill', [KaryawanSkillController::class,'store'])->name('hrd.karyawan.skill.store');
    Route::delete('/hrd/karyawan/{karyawan}/skill/{karyawanSkill}', [KaryawanSkillController::class,'destroy'])->name('hrd.karyawan.skill.destroy');
    Route::get('/hrd/laporan', [LaporanController::class,'index'])->name('hrd.laporan.index');
});

/*
|--------------------------------------------------------------------------
| Dashboard Karyawan
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:karyawan'])->group(function () {
    Route::get('/karyawan/dashboard', [KaryawanDashboardController::class, 'index'])->name('karyawan.dashboard');
    Route::get('/karyawan/assessment', [AssessmentController::class, 'index'])->name('karyawan.assessment.index');
    Route::get('/karyawan/assessment/{peserta}', [AssessmentController::class, 'show'])->name('karyawan.assessment.show');
    Route::post('/karyawan/assessment/{peserta}/mulai', [AssessmentController::class, 'start'   ])->name('karyawan.assessment.start');
    Route::get('/karyawan/assessment/{peserta}/kerjakan', [AssessmentController::class, 'kerjakan'])->name('karyawan.assessment.kerjakan');
    Route::post('/karyawan/assessment/{peserta}/submit', [AssessmentController::class,'submit'])->name('karyawan.assessment.submit');
    Route::get('/karyawan/assessment/{peserta}/hasil', [ AssessmentController::class,'hasil'])->name('karyawan.assessment.hasil');
});