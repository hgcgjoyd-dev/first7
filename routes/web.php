<?php

use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\KesiswaanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Kesiswaan SMK TI Bali Global Badung
|--------------------------------------------------------------------------
*/

// 1. Gateway & Otentikasi
Route::get('/', [KesiswaanController::class, 'landing'])->name('landing');
Route::get('/login', [KesiswaanController::class, 'login'])->name('login');
Route::post('/login', [KesiswaanController::class, 'postLogin'])->name('login.post');
Route::get('/scan', [KesiswaanController::class, 'scan'])->name('scan');
Route::post('/scan', [KesiswaanController::class, 'postScan'])->name('scan.post');
Route::get('/logout', [KesiswaanController::class, 'logout'])->name('logout');

// 2. Dashboard & Fitur Siswa (URL resmi diubah menjadi /dashboardsiswa)
Route::get('/dashboardsiswa', [KesiswaanController::class, 'dashboard'])->name('dashboard');
Route::get('/dashboard', function () {
    return redirect()->route('dashboard');
});
Route::get('/presensi', [KesiswaanController::class, 'presensi'])->name('presensi');
Route::post('/presensi', [KesiswaanController::class, 'storePresensi'])->name('presensi.store');

// 3. Izin & Sakit
Route::get('/izin', [KesiswaanController::class, 'izin'])->name('izin');
Route::post('/izin', [KesiswaanController::class, 'storeIzin'])->name('izin.store');

// 4. Riwayat & Mapel & BK & Konseling
Route::get('/riwayat', [KesiswaanController::class, 'riwayat'])->name('riwayat');
Route::get('/mapel', [KesiswaanController::class, 'mapel'])->name('mapel');
Route::get('/bk', [KesiswaanController::class, 'bk'])->name('bk');
Route::get('/konseling', [KesiswaanController::class, 'konseling'])->name('konseling');

// 5. Piket & Checklist Kebersihan
Route::get('/piket', [KesiswaanController::class, 'piket'])->name('piket');
Route::post('/piket/confirm', [KesiswaanController::class, 'confirmPiket'])->name('piket.confirm');
Route::post('/piket/{id}/toggle', [KesiswaanController::class, 'updatePiket'])->name('piket.toggle');

// 6. Profil Siswa
Route::get('/profil', [KesiswaanController::class, 'profil'])->name('profil');

// 7. Cek Koneksi Database
Route::get('/cek-db', [DatabaseController::class, 'check'])->name('cek-db');

// 8. Dashboard Guru BK (Responsive Mobile & Desktop)
Route::get('/dashboardbk', function () {
    return view('dashboard guru.dashboardbk');
})->name('guru.bk');

Route::get('/dashboard-guru/bk', function () {
    return view('dashboard guru.dashboardbk');
})->name('dashboard.guru.bk');

// 9. Absensi Siswa Semua Kelas (Guru BK)
Route::get('/dashboard-guru/absensi', function () {
    return view('dashboard guru.absensi');
})->name('guru.absensi');

Route::get('/absensi-guru', function () {
    return view('dashboard guru.absensi');
})->name('absensi.guru');

// 10. Pelanggaran Siswa (Guru BK - Antrean Kasus & Penyesuaian Poin)
Route::get('/dashboard-guru/pelanggaran', function () {
    return view('dashboard guru.pelanggaran');
})->name('guru.pelanggaran');

Route::get('/pelanggaran', function () {
    return view('dashboard guru.pelanggaran');
})->name('pelanggaran');

// 11. Pengajuan Konseling & BK (Guru BK - Antrean & Penjadwalan)
Route::get('/dashboard-guru/konseling', function () {
    return view('dashboard guru.konseling');
})->name('guru.konseling');

Route::get('/konseling-guru', function () {
    return view('dashboard guru.konseling');
})->name('konseling.guru');

// 12. Profil Guru BK (Responsive Mobile & Desktop)
Route::get('/profilebk', function () {
    return view('dashboard guru.profilebk');
})->name('profilebk');

Route::get('/dashboard-guru/profile', function () {
    return view('dashboard guru.profilebk');
})->name('guru.profile');

Route::get('/dashboard-guru/profilebk', function () {
    return view('dashboard guru.profilebk');
})->name('dashboard.guru.profilebk');


