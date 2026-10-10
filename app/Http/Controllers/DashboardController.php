<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Izin;
use App\Models\Mapel;
use App\Models\PelanggaranSiswa;
use App\Models\Piket;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user instanceof User, 403);

        return redirect()->route($user->dashboardRoute());
    }

    public function siswa(Request $request): View
    {
        $user = $request->user();
        abort_unless($user instanceof User, 403);

        $siswa = $user->siswa()->with('kelas')->first();

        if (! $siswa) {
            return view('dashboard.role', [
                'roleLabel' => 'Siswa',
                'name' => $user->nama,
                'profileDetails' => ['Profil siswa' => 'Belum dilengkapi'],
                'metrics' => [],
                'students' => collect(),
            ]);
        }

        $today = Carbon::today()->toDateString();
        $studentId = $siswa->id_siswa ?? $siswa->id;

        $presensiHariIni = null;
        $totalHadirBulanIni = 0;
        $totalIzinBulanIni = 0;
        $persenHadir = 100;
        $tugasAktif = 0;
        $piketHariIni = null;

        try {
            if (Schema::hasTable('presensi')) {
                $presensiHariIni = Presensi::where('siswa_id', $studentId)
                    ->whereDate('tanggal', $today)
                    ->first();

                $totalHadirBulanIni = Presensi::where('siswa_id', $studentId)
                    ->whereMonth('tanggal', Carbon::now()->month)
                    ->whereIn('status', ['Hadir', 'Terlambat'])
                    ->count();

                $totalPresensi = Presensi::where('siswa_id', $studentId)
                    ->whereMonth('tanggal', Carbon::now()->month)
                    ->count();

                if ($totalPresensi > 0) {
                    $persenHadir = round(($totalHadirBulanIni / $totalPresensi) * 100);
                }
            }

            if (! $presensiHariIni && Schema::hasTable('absensi')) {
                $presensiHariIni = Absensi::where('id_siswa', $studentId)
                    ->whereDate('tanggal', $today)
                    ->first();
            }

            if (Schema::hasTable('absensi')) {
                $totalHadirAbsensi = Absensi::where('id_siswa', $studentId)
                    ->whereMonth('tanggal', Carbon::now()->month)
                    ->whereIn('status', ['Hadir', 'Terlambat'])
                    ->count();
                if ($totalHadirAbsensi > $totalHadirBulanIni) {
                    $totalHadirBulanIni = $totalHadirAbsensi;
                }
            }

            if (Schema::hasTable('izin')) {
                $totalIzinBulanIni = Izin::where('siswa_id', $studentId)
                    ->whereMonth('tgl_mulai', Carbon::now()->month)
                    ->sum('durasi_hari') ?: 0;
            }

            if (Schema::hasTable('mapel')) {
                $tugasAktif = Mapel::where('status', 'Aktif')->count();
            }

            if (Schema::hasTable('piket')) {
                $indonesianDays = [
                    'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
                    'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
                ];
                $currentDayName = $indonesianDays[Carbon::now()->format('l')] ?? 'Senin';
                $piketHariIni = Piket::where('hari', $currentDayName)->first();
            }
        } catch (Exception $e) {
        }

        if (view()->exists('dashboard siswa.dashboard')) {
            return view('dashboard siswa.dashboard', [
                'siswa' => $siswa,
                'presensiHariIni' => $presensiHariIni,
                'totalHadir' => $totalHadirBulanIni,
                'persenHadir' => $persenHadir,
                'totalIzinBulanIni' => $totalIzinBulanIni,
                'tugasAktif' => $tugasAktif,
                'piketHariIni' => $piketHariIni,
            ]);
        }

        return view('dashboard.role', [
            'roleLabel' => 'Siswa',
            'name' => $siswa->nama_siswa,
            'profileDetails' => [
                'Nomor siswa' => $siswa->no_siswa,
                'Kelas' => $siswa->kelas->nama_kelas,
            ],
            'metrics' => [
                'Poin pelanggaran' => $siswa->poin_pelanggaran,
                'Poin penghargaan' => $siswa->poin_penghargaan,
            ],
            'students' => collect(),
        ]);
    }

    public function guru(Request $request): View
    {
        $user = $request->user();
        abort_unless($user instanceof User, 403);

        $guru = $user->guru()->with('kelasWali')->first();

        if (view()->exists('dashboard guru.dashboardguru')) {
            return view('dashboard guru.dashboardguru', [
                'guru' => $guru,
                'user' => $user,
            ]);
        }

        return view('dashboard.role', [
            'roleLabel' => 'Guru',
            'name' => $guru?->nama_guru ?? $user->nama,
            'profileDetails' => [
                'Nomor guru' => $guru?->no_guru ?? '-',
                'Kelas wali' => $guru?->kelasWali?->nama_kelas ?? 'Belum ditetapkan',
            ],
            'metrics' => [],
            'students' => collect(),
        ]);
    }

    public function guruBk(Request $request): View
    {
        $user = $request->user();
        abort_unless($user instanceof User, 403);

        $guru = $user->guru()
            ->whereHas('guruBk', fn ($query) => $query->where('status_aktif', true))
            ->firstOrFail();

        $students = Siswa::query()
            ->with('kelas')
            ->orderBy('nama_siswa')
            ->get(['id_siswa', 'nama_siswa', 'id_kelas', 'poin_pelanggaran']);

        return view('dashboard.role', [
            'roleLabel' => 'Guru BK',
            'name' => $guru->nama_guru,
            'profileDetails' => [
                'Nomor guru' => $guru->no_guru,
            ],
            'metrics' => [
                'Jumlah siswa' => $students->count(),
                'Catatan pelanggaran' => PelanggaranSiswa::count(),
            ],
            'students' => $students,
        ]);
    }

    public function admin(Request $request): View
    {
        $user = $request->user();
        abort_unless($user instanceof User, 403);

        return view('dashboard.role', [
            'roleLabel' => 'Admin',
            'name' => $user->nama,
            'profileDetails' => [],
            'metrics' => [
                'Akun' => User::count(),
                'Siswa' => Siswa::count(),
                'Guru' => Guru::count(),
            ],
            'students' => collect(),
        ]);
    }
}
