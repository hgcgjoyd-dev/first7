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

Route::get('/', [KesiswaanController::class, 'landing'])->name('landing');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.post');
});

Route::post('/logout', [AuthController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'active'])->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/siswa', [DashboardController::class, 'siswa'])
        ->middleware('role:siswa')
        ->name('dashboard.siswa');
    Route::get('/dashboard/guru', [DashboardController::class, 'guru'])
        ->middleware('role:guru')
        ->name('dashboard.guru');
    Route::get('/dashboard/guru-bk', [DashboardController::class, 'guruBk'])
        ->middleware('role:guru_bk')
        ->name('dashboard.guru_bk');
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])
        ->middleware('role:admin')
        ->name('dashboard.admin');

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

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function (): void {
        Route::resource('users', UserController::class);
        Route::resource('siswa', SiswaController::class);
        Route::resource('guru', GuruController::class);
        Route::resource('kelas', KelasController::class)->parameters(['kelas' => 'kelas']);
        Route::get('absensi/report', [AbsensiController::class, 'report'])->name('absensi.report');
        Route::resource('absensi', AbsensiController::class);
    });

    Route::get('/cek-db', [DatabaseController::class, 'check'])
        ->middleware('role:admin')
        ->name('cek-db');

    Route::middleware('role:guru_bk,admin')->prefix('bk')->name('bk.')->group(function (): void {
        Route::resource('pelanggaran', PelanggaranSiswaController::class);
    });

    Route::middleware('role:guru_bk,admin')->group(function (): void {
        Route::get('/dashboardbk', [DashboardController::class, 'guruBk'])->name('guru.bk');
        Route::get('/dashboard-guru/bk', [DashboardController::class, 'guruBk'])->name('dashboard.guru.bk');
        Route::view('/dashboard-guru/absensi', 'dashboard guru.absensi')->name('guru.absensi');
        Route::view('/absensi-guru', 'dashboard guru.absensi')->name('absensi.guru');
    });
});
