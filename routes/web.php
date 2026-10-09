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

// 7b. Dashboard Guru Mapel (resources/views/dashboard guru/dashboardguru.blade.php)
Route::get('/dashboardguru', function () {
    return view('dashboard guru.dashboardguru');
})->name('guru.dashboard');

Route::get('/dashboard-guru', function () {
    return view('dashboard guru.dashboardguru');
})->name('dashboard.guru');

Route::get('/guru', function () {
    return view('dashboard guru.dashboardguru');
});

// Helper view Guru BK (Mendukung folder 'dashboard guru bk' maupun fallback 'dashboard guru')
if (!function_exists('renderBkView')) {
    function renderBkView(string $viewName) {
        if (view()->exists("dashboard guru bk.{$viewName}")) {
            return view("dashboard guru bk.{$viewName}");
        }
        return view("dashboard guru.{$viewName}");
    }
}

// 8. Dashboard Guru BK (Responsive Mobile & Desktop)
Route::get('/dashboardbk', function () {
    return renderBkView('dashboardbk');
})->name('guru.bk');

Route::get('/dashboard-guru-bk', function () {
    return renderBkView('dashboardbk');
})->name('dashboard.guru.bk');

Route::get('/dashboard-gurubk', function () {
    return renderBkView('dashboardbk');
})->name('dashboard.gurubk');

Route::get('/dashboard-guru/bk', function () {
    return redirect()->route('guru.bk');
});

// 9. Absensi Siswa Semua Kelas (Guru BK)
Route::get('/dashboard-guru-bk/absensi', function () {
    return renderBkView('absensi');
})->name('guru.absensi');

Route::get('/dashboard-gurubk/absensi', function () {
    return renderBkView('absensi');
})->name('gurubk.absensi');

Route::get('/dashboardbk/absensi', function () {
    return renderBkView('absensi');
});

Route::get('/absensi-bk', function () {
    return renderBkView('absensi');
})->name('absensi.bk');

Route::get('/absensi-guru-bk', function () {
    return renderBkView('absensi');
});

Route::get('/dashboard-guru/absensi', function () {
    return redirect()->route('guru.absensi');
});

Route::get('/absensi-guru', function () {
    return redirect()->route('guru.absensi');
});

// 10. Pelanggaran Siswa (Guru BK - Antrean Kasus & Penyesuaian Poin)
Route::get('/dashboard-guru-bk/pelanggaran', function () {
    return renderBkView('pelanggaran');
})->name('guru.pelanggaran');

Route::get('/dashboard-gurubk/pelanggaran', function () {
    return renderBkView('pelanggaran');
})->name('gurubk.pelanggaran');

Route::get('/dashboardbk/pelanggaran', function () {
    return renderBkView('pelanggaran');
});

Route::get('/pelanggaran-bk', function () {
    return renderBkView('pelanggaran');
})->name('pelanggaran.bk');

Route::get('/pelanggaran', function () {
    return renderBkView('pelanggaran');
})->name('pelanggaran');

Route::get('/dashboard-guru/pelanggaran', function () {
    return redirect()->route('guru.pelanggaran');
});

// 11. Pengajuan Konseling & BK (Guru BK - Antrean & Penjadwalan)
Route::get('/dashboard-guru-bk/konseling', function () {
    return renderBkView('konseling');
})->name('guru.konseling');

Route::get('/dashboard-gurubk/konseling', function () {
    return renderBkView('konseling');
})->name('gurubk.konseling');

Route::get('/dashboardbk/konseling', function () {
    return renderBkView('konseling');
});

Route::get('/konseling-bk', function () {
    return renderBkView('konseling');
})->name('konseling.bk');

Route::get('/konseling-guru-bk', function () {
    return renderBkView('konseling');
});

Route::get('/dashboard-guru/konseling', function () {
    return redirect()->route('guru.konseling');
});

Route::get('/konseling-guru', function () {
    return redirect()->route('guru.konseling');
});

// 12. Profil Guru BK (Responsive Mobile & Desktop)
Route::get('/dashboard-guru-bk/profile', function () {
    return renderBkView('profilebk');
})->name('guru.profile');

Route::get('/dashboard-gurubk/profile', function () {
    return renderBkView('profilebk');
})->name('gurubk.profile');

Route::get('/dashboardbk/profile', function () {
    return renderBkView('profilebk');
});

Route::get('/profilebk', function () {
    return renderBkView('profilebk');
})->name('profilebk');

Route::get('/dashboard-guru-bk/profilebk', function () {
    return renderBkView('profilebk');
})->name('dashboard.guru.profilebk');

Route::get('/dashboard-gurubk/profilebk', function () {
    return renderBkView('profilebk');
});

Route::get('/dashboard-guru/profile', function () {
    return redirect()->route('guru.profile');
});

Route::get('/dashboard-guru/profilebk', function () {
    return redirect()->route('guru.profile');
});


