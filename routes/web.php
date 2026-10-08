<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Presensi & Kesiswaan SMK TI Bali Global Badung
| Alur: Halaman Awal (Landing) -> Login / Scan -> Dashboard Siswa
|--------------------------------------------------------------------------
*/

// 1. Alur Pertama: Halaman Awal Gateway (Landing)
Route::get('/', function () {
    return view('auth.landing');
})->name('landing');

// 2. Alur Masuk: Halaman Login Manual Siswa
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// 3. Alur Masuk: Halaman Scan Kartu Pelajar
Route::get('/scan', function () {
    return view('auth.scan');
})->name('scan');

// 4. Halaman Dashboard Utama Siswa (Setelah Login / Scan)
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// 5. Presensi Biometrik & Geolokasi (Datang & Pulang)
Route::get('/presensi', function () {
    return view('presensi.index');
})->name('presensi');

// 6. Formulir Pengajuan Izin, Cuti & Sakit
Route::get('/izin', function () {
    return view('izin.index');
})->name('izin');

// 7. Riwayat Presensi & Kalender Kehadiran
Route::get('/riwayat', function () {
    return view('riwayat.index');
})->name('riwayat');

// 8. Tugas Mata Pelajaran (Akademik)
Route::get('/mapel', function () {
    return view('mapel.index');
})->name('mapel');

// 9. Konseling & Buku Disiplin / Tugas BK
Route::get('/bk', function () {
    return view('bk.index');
})->name('bk');

// 10. Jadwal & Checklist Kebersihan Piket Kelas
Route::get('/piket', function () {
    return view('piket.index');
})->name('piket');

// 11. Profil Siswa & Tren Kedisiplinan Murid
Route::get('/profil', function () {
    return view('profil.index');
})->name('profil');

// 12. Logout: Kembali ke Halaman Awal
Route::get('/logout', function () {
    return redirect()->route('landing');
})->name('logout');

// 13. Cek Koneksi Database db_kesiswaan & Auto-Create jika belum ada
Route::get('/cek-db', function () {
    $host = config('database.connections.mysql.host', '127.0.0.1');
    $port = config('database.connections.mysql.port', '3306');
    $user = config('database.connections.mysql.username', 'root');
    $pass = config('database.connections.mysql.password', '');
    $targetDb = config('database.connections.mysql.database', 'db_kesiswaan');

    try {
        $pdoServer = new \PDO("mysql:host={$host};port={$port}", $user, $pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ]);
        $existingDatabases = $pdoServer->query('SHOW DATABASES')->fetchAll(\PDO::FETCH_COLUMN);

        // Jika user klik buat otomatis atau meminta pembuatan
        if (request()->has('buat')) {
            $pdoServer->exec("CREATE DATABASE IF NOT EXISTS `{$targetDb}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            return view('cek-db', [
                'status' => 'created',
                'targetDb' => $targetDb,
                'host' => $host,
                'port' => $port,
                'user' => $user,
                'tables' => [],
                'existingDatabases' => $pdoServer->query('SHOW DATABASES')->fetchAll(\PDO::FETCH_COLUMN),
                'errorMsg' => null,
            ]);
        }

        // Cek apakah database target sudah ada
        if (in_array($targetDb, $existingDatabases)) {
            \Illuminate\Support\Facades\DB::connection()->getPdo();
            $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
            return view('cek-db', [
                'status' => 'success',
                'targetDb' => $targetDb,
                'host' => $host,
                'port' => $port,
                'user' => $user,
                'tables' => $tables,
                'existingDatabases' => $existingDatabases,
                'errorMsg' => null,
            ]);
        } else {
            return view('cek-db', [
                'status' => 'not_found',
                'targetDb' => $targetDb,
                'host' => $host,
                'port' => $port,
                'user' => $user,
                'tables' => [],
                'existingDatabases' => $existingDatabases,
                'errorMsg' => "Database '{$targetDb}' belum terdaftar di MySQL Server.",
            ]);
        }

    } catch (\Throwable $e) {
        return view('cek-db', [
            'status' => 'error',
            'targetDb' => $targetDb,
            'host' => $host,
            'port' => $port,
            'user' => $user,
            'tables' => [],
            'existingDatabases' => [],
            'errorMsg' => $e->getMessage(),
        ]);
    }
})->name('cek-db');
