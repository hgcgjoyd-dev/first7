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
Route::get('/scan', [KesiswaanController::class, 'scan'])->name('scan');
Route::get('/logout', [KesiswaanController::class, 'logout'])->name('logout');

// 2. Dashboard & Fitur Siswa
Route::get('/dashboard', [KesiswaanController::class, 'dashboard'])->name('dashboard');
Route::get('/presensi', [KesiswaanController::class, 'presensi'])->name('presensi');
Route::get('/izin', [KesiswaanController::class, 'izin'])->name('izin');
Route::get('/riwayat', [KesiswaanController::class, 'riwayat'])->name('riwayat');
Route::get('/mapel', [KesiswaanController::class, 'mapel'])->name('mapel');
Route::get('/bk', [KesiswaanController::class, 'bk'])->name('bk');
Route::get('/piket', [KesiswaanController::class, 'piket'])->name('piket');
Route::get('/profil', [KesiswaanController::class, 'profil'])->name('profil');

// 3. Cek & Koneksi Database
Route::get('/cek-db', [DatabaseController::class, 'check'])->name('cek-db');
