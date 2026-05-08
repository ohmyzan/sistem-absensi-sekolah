<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaceRegistrationController; // Wajib ada di bagian atas
use App\Http\Controllers\AttendanceScanController;

Route::get('/', function () {
    return view('welcome');
});

// --- Route Rekam Wajah ---
Route::get('/rekam-wajah/{student}', [FaceRegistrationController::class, 'create'])->name('rekam-wajah.create');
Route::post('/rekam-wajah/{student}', [FaceRegistrationController::class, 'store'])->name('rekam-wajah.store');

// Route untuk Mesin Utama Absensi
Route::get('/absensi', [AttendanceScanController::class, 'index'])->name('absensi.index');
Route::post('/absensi/check-in', [AttendanceScanController::class, 'store'])->name('absensi.store');
