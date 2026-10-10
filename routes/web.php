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

Route::get('/scan', [KesiswaanController::class, 'scan'])->name('scan');
Route::post('/scan', [KesiswaanController::class, 'postScan'])->name('scan.post');

// Dashboard Guru Mapel (Dapat diakses langsung untuk preview & responsif)
Route::get('/dashboardguru', function () {
    return view('dashboard guru.dashboardguru');
})->name('guru.dashboard');

Route::get('/dashboard-guru', function () {
    return view('dashboard guru.dashboardguru');
})->name('dashboard.guru.direct');

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
        Route::redirect('/dashboardsiswa', '/dashboard/siswa')->name('dashboard.siswa.legacy');
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

    Route::middleware('role:guru')->group(function (): void {
        Route::redirect('/guru', '/dashboard/guru');
    });

    Route::middleware('role:guru_bk,admin')->group(function (): void {
        Route::get('/dashboardbk', [DashboardController::class, 'guruBk'])->name('guru.bk');
        Route::get('/dashboard-guru/bk', [DashboardController::class, 'guruBk'])->name('dashboard.guru.bk');
        Route::redirect('/dashboard-guru-bk', '/dashboard/guru-bk')->name('dashboard.gurubk');
        Route::redirect('/dashboard-gurubk', '/dashboard/guru-bk');
        Route::redirect('/dashboardbk/absensi', '/dashboard-guru-bk/absensi');
        Route::redirect('/dashboard-guru/absensi', '/dashboard-guru-bk/absensi');
        Route::redirect('/absensi-guru', '/dashboard-guru-bk/absensi')->name('absensi.guru');
        Route::view('/dashboard-guru-bk/absensi', 'dashboard guru bk.absensi')->name('guru.absensi');
        Route::redirect('/dashboard-gurubk/absensi', '/dashboard-guru-bk/absensi')->name('gurubk.absensi');
        Route::redirect('/absensi-bk', '/dashboard-guru-bk/absensi')->name('absensi.bk');
        Route::redirect('/absensi-guru-bk', '/dashboard-guru-bk/absensi');

        Route::redirect('/dashboard-guru-bk/pelanggaran', '/bk/pelanggaran')->name('guru.pelanggaran');
        Route::redirect('/dashboard-gurubk/pelanggaran', '/bk/pelanggaran')->name('gurubk.pelanggaran');
        Route::redirect('/dashboardbk/pelanggaran', '/bk/pelanggaran');
        Route::redirect('/pelanggaran-bk', '/bk/pelanggaran')->name('pelanggaran.bk');
        Route::redirect('/pelanggaran', '/bk/pelanggaran')->name('pelanggaran');

        Route::view('/dashboard-guru-bk/konseling', 'dashboard guru bk.konseling')->name('guru.konseling');
        Route::redirect('/dashboard-gurubk/konseling', '/dashboard-guru-bk/konseling')->name('gurubk.konseling');
        Route::redirect('/dashboardbk/konseling', '/dashboard-guru-bk/konseling');
        Route::redirect('/konseling-bk', '/dashboard-guru-bk/konseling')->name('konseling.bk');
        Route::redirect('/konseling-guru-bk', '/dashboard-guru-bk/konseling');
        Route::redirect('/dashboard-guru/konseling', '/dashboard-guru-bk/konseling');
        Route::redirect('/konseling-guru', '/dashboard-guru-bk/konseling');

        Route::view('/dashboard-guru-bk/profile', 'dashboard guru bk.profilebk')->name('guru.profile');
        Route::redirect('/dashboard-gurubk/profile', '/dashboard-guru-bk/profile')->name('gurubk.profile');
        Route::redirect('/dashboardbk/profile', '/dashboard-guru-bk/profile');
        Route::redirect('/profilebk', '/dashboard-guru-bk/profile')->name('profilebk');
        Route::redirect('/dashboard-guru-bk/profilebk', '/dashboard-guru-bk/profile')->name('dashboard.guru.profilebk');
        Route::redirect('/dashboard-gurubk/profilebk', '/dashboard-guru-bk/profile');
        Route::redirect('/dashboard-guru/profile', '/dashboard-guru-bk/profile');
        Route::redirect('/dashboard-guru/profilebk', '/dashboard-guru-bk/profile');

        Route::resource('bk/pelanggaran', PelanggaranSiswaController::class)
            ->names('bk.pelanggaran');
    });
});
