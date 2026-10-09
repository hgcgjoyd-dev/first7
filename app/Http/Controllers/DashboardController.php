<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\PelanggaranSiswa;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $siswa = $user->siswa()->with('kelas')->firstOrFail();

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

        $guru = $user->guru()->with('kelasWali')->firstOrFail();

        return view('dashboard.role', [
            'roleLabel' => 'Guru',
            'name' => $guru->nama_guru,
            'profileDetails' => [
                'Nomor guru' => $guru->no_guru,
                'Kelas wali' => $guru->kelasWali?->nama_kelas ?? 'Belum ditetapkan',
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

    public function admin(): View
    {
        return view('dashboard.role', [
            'roleLabel' => 'Admin',
            'name' => auth()->user()->nama,
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
