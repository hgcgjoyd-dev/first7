<?php

use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\GuruBk\PelanggaranSiswaController;
use App\Http\Controllers\KesiswaanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Kesiswaan SMK TI Bali Global Badung
|--------------------------------------------------------------------------
*/

// 1. Gateway & autentikasi
Route::get('/', [KesiswaanController::class, 'landing'])->name('landing');
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.post');
});

Route::middleware(['auth', 'active'])->group(function (): void {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/dashboard/siswa', [DashboardController::class, 'siswa'])
        ->middleware('role:siswa')->name('dashboard.siswa');
    Route::get('/dashboard/guru', [DashboardController::class, 'guru'])
        ->middleware('role:guru')->name('dashboard.guru');
    Route::get('/dashboard/guru-bk', [DashboardController::class, 'guruBk'])
        ->middleware('role:guru_bk')->name('dashboard.guru_bk');
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])
        ->middleware('role:admin')->name('dashboard.admin');

    Route::middleware('role:siswa')->group(function (): void {
        Route::get('/scan', [KesiswaanController::class, 'scan'])->name('scan');
        Route::post('/scan', [KesiswaanController::class, 'postScan'])->name('scan.post');
        Route::get('/presensi', [KesiswaanController::class, 'presensi'])->name('presensi');
        Route::post('/presensi', [KesiswaanController::class, 'storePresensi'])->name('presensi.store');
        Route::get('/izin', [KesiswaanController::class, 'izin'])->name('izin');
        Route::post('/izin', [KesiswaanController::class, 'storeIzin'])->name('izin.store');
        Route::get('/riwayat', [KesiswaanController::class, 'riwayat'])->name('riwayat');
        Route::get('/mapel', [KesiswaanController::class, 'mapel'])->name('mapel');
        Route::get('/bk', [KesiswaanController::class, 'bk'])->name('bk');
        Route::get('/konseling', [KesiswaanController::class, 'konseling'])->name('konseling');
        Route::get('/piket', [KesiswaanController::class, 'piket'])->name('piket');
        Route::post('/piket/confirm', [KesiswaanController::class, 'confirmPiket'])->name('piket.confirm');
        Route::post('/piket/{id}/toggle', [KesiswaanController::class, 'updatePiket'])->name('piket.toggle');
        Route::get('/profil', [KesiswaanController::class, 'profil'])->name('profil');
    });

    Route::get('/cek-db', [DatabaseController::class, 'check'])
        ->middleware('role:admin')->name('cek-db');

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function (): void {
        Route::get('/rekap-absensi', [AbsensiController::class, 'report'])->name('absensi.report');
        Route::resource('siswa', SiswaController::class);
        Route::resource('guru', GuruController::class);
        Route::resource('kelas', KelasController::class)->parameters(['kelas' => 'kelas']);
        Route::resource('absensi', AbsensiController::class);
        Route::resource('users', UserController::class);
    });

    Route::prefix('bk')->name('bk.')->middleware('role:guru_bk,admin')->group(function (): void {
        Route::resource('pelanggaran', PelanggaranSiswaController::class);
    });
});
