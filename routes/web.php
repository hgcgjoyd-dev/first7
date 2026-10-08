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

// 2. Dashboard & Fitur Siswa
Route::get('/dashboard', [KesiswaanController::class, 'dashboard'])->name('dashboard');
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
