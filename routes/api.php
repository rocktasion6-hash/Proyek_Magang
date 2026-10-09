<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KaryawanController;
use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\HasilAssessmentController;
use App\Http\Controllers\Api\BlockchainRecordController;
use App\Http\Controllers\Api\PengembanganController;
use App\Http\Controllers\Api\ReportingController;

Route::get('/karyawan', [KaryawanController::class,'index'])->name('api.karyawan.index');
Route::get('/karyawan/{id}', [KaryawanController::class,'show'])->name('api.karyawan.show');
Route::get('/assessments', [AssessmentController::class, 'index'])->name('api.assessments.index');
Route::get('/assessments/{id}', [AssessmentController::class,'show'])->name('api.assessments.show');
Route::get('/hasil-assessment', [HasilAssessmentController::class,'index'])->name('api.hasil-assessment.index');
Route::get('/hasil-assessment/{id}', [HasilAssessmentController::class,'show'])->name('api.hasil-assessment.show');
Route::get('/blockchain-records', [BlockchainRecordController::class,'index'])->name('api.blockchain-records.index');
Route::get('/blockchain-records/{id}', [BlockchainRecordController::class,'show'])->name('api.blockchain-records.show');
Route::get('/pengembangan', [PengembanganController::class,'index'])->name('api.pengembangan.index');
Route::get('/pengembangan/{id}', [PengembanganController::class,'show'])->name('api.pengembangan.show');
Route::get('/reporting', [ReportingController::class,'index'])->name('api.reporting.index');
Route::get('/reporting/ringkasan', [ReportingController::class,'ringkasan'])->name('api.reporting.ringkasan');
Route::get('/reporting/assessment', [ReportingController::class,'assessment'])->name('api.reporting.assessment');
Route::get('/reporting/pengembangan', [ReportingController::class,'pengembangan'])->name('api.reporting.pengembangan');
Route::get('/reporting/riwayat-jabatan', [ReportingController::class,'riwayatJabatan'])->name('api.reporting.riwayat-jabatan');
Route::get('/reporting/riwayat-skill', [ReportingController::class,'riwayatSkill'])->name('api.reporting.riwayat-skill');