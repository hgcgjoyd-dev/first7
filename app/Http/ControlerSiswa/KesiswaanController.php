<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Bk;
use App\Models\Izin;
use App\Models\Mapel;
use App\Models\Piket;
use App\Models\Presensi;
use App\Models\Siswa;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class KesiswaanController extends Controller
{
    /**
     * Mengambil profil siswa yang terhubung ke akun yang sedang login.
     */
    protected function getActiveSiswa(): Siswa
    {
        $student = request()->user()?->siswa;

        abort_unless($student instanceof Siswa, 403);

        return $student;
    }

    /**
     * Menghitung jarak dua koordinat dalam meter (rumus Haversine).
     */
    protected function hitungJarakMeter(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $r = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return 2 * $r * asin(sqrt($a));
    }

    /**
     * 1. Halaman Gateway Awal
     */
    public function landing(): View
    {
        if (view()->exists('dashboard siswa.auth.landing')) {
            return view('dashboard siswa.auth.landing');
        }

        return view('auth.landing');
    }

    /**
     * 3. Halaman Scan Kartu Pelajar
     */
    public function scan()
    {
        if (view()->exists('dashboard siswa.auth.scan')) {
            return view('dashboard siswa.auth.scan');
        }

        return view('auth.scan');
    }

    /**
     * Proses Scan Kartu Pelajar — login langsung via nama di kartu
     */
    public function postScan(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:100'],
        ]);

        $code = trim($validated['code']);

        // Cari siswa berdasarkan nama lengkap (nama_siswa)
        $siswa = Siswa::where('nama_siswa', $validated['code'])->first();

        if (! $siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Akun tidak sesuai. Nama kartu tidak terdaftar.',
            ], 404);
        }

        // Ambil akun user yang terhubung ke siswa
        $user = $siswa->user;

        if (! $user || ! $user->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Akun tidak sesuai. Silakan login manual.',
            ], 403);
        }

        // Login otomatis tanpa email/password
        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Login via kartu pelajar berhasil!',
            'redirect' => route('dashboard.siswa'),
        ]);
    }

    /**
     * 4. Dashboard Utama Siswa (Memuat data real dari database)
     */
    public function dashboard()
    {
        $siswa = $this->getActiveSiswa();
        $today = Carbon::today()->toDateString();

        $presensiHariIni = null;
        $totalHadirBulanIni = 0;
        $totalIzinBulanIni = 0;
        $persenHadir = 95;
        $tugasAktif = 0;
        $piketHariIni = null;

        try {
            if (Schema::hasTable('presensi')) {
                $presensiHariIni = Presensi::where('siswa_id', $siswa->id)
                    ->whereDate('tanggal', $today)
                    ->first();

                $totalHadir = Presensi::where('siswa_id', $siswa->id)
                    ->whereMonth('tanggal', Carbon::now()->month)
                    ->whereIn('status', ['Hadir', 'Terlambat'])
                    ->count();

                $totalPresensi = Presensi::where('siswa_id', $siswa->id)
                    ->whereMonth('tanggal', Carbon::now()->month)
                    ->count();

                if ($totalPresensi > 0) {
                    $persenHadir = round(($totalHadir / $totalPresensi) * 100);
                }
            }

            if (Schema::hasTable('izin')) {
                $totalIzinBulanIni = Izin::where('siswa_id', $siswa->id)
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

        $viewName = view()->exists('dashboard siswa.dashboard') ? 'dashboard siswa.dashboard' : 'dashboard';

        return view($viewName, compact(
            'siswa',
            'presensiHariIni',
            'persenHadir',
            'totalIzinBulanIni',
            'tugasAktif',
            'piketHariIni'
        ));
    }

    /**
     * 5. Halaman Presensi Biometrik & Geolokasi
     */
    public function presensi()
    {
        $siswa = $this->getActiveSiswa();
        $today = Carbon::today()->toDateString();
        $presensiHariIni = null;

        try {
            if (Schema::hasTable('presensi')) {
                $presensiHariIni = Presensi::where('siswa_id', $siswa->id)
                    ->whereDate('tanggal', $today)
                    ->first();
            }
        } catch (Exception $e) {
        }

        $viewName = view()->exists('dashboard siswa.presensi') ? 'dashboard siswa.presensi' : 'presensi.index';

        return view($viewName, compact('siswa', 'presensiHariIni') + [
            'radiusMeter' => config('absensi.radius_meter'),
            'schoolLat' => config('absensi.school_lat'),
            'schoolLng' => config('absensi.school_lng'),
        ]);
    }

    /**
     * Simpan Absensi Masuk/Pulang ke Database
     */
    public function storePresensi(Request $request)
    {
        $validated = $request->validate([
            'tipe' => ['sometimes', 'string', 'in:datang,pulang'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'foto' => ['nullable', 'string', 'max:255'],
        ]);
        $student = $this->getActiveSiswa();
        $today = Carbon::today()->toDateString();
        $nowTime = Carbon::now()->toTimeString();
        $type = $validated['tipe'] ?? 'datang';

        $attendance = Absensi::query()
            ->where('id_siswa', $student->id_siswa)
            ->whereDate('tanggal', $today)
            ->first() ?? new Absensi([
                'id_siswa' => $student->id_siswa,
                'tanggal' => Carbon::today(),
                'status' => Carbon::now()->hour >= 8 ? 'Terlambat' : 'Hadir',
            ]);

        // Validasi ulang latitude/longitude sebagai required untuk geofence
        $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        // Geofence radius check
        $lat = (float) $validated['latitude'];
        $lng = (float) $validated['longitude'];

        $schoolLat = config('absensi.school_lat');
        $schoolLng = config('absensi.school_lng');
        $radius = config('absensi.radius_meter');

        $jarak = $this->hitungJarakMeter($lat, $lng, $schoolLat, $schoolLng);
        if ($jarak > $radius) {
            return response()->json([
                'success' => false,
                'message' => "Anda berada di luar radius sekolah ({$jarak} m). Radius yang diizinkan {$radius} m.",
                'jarak' => round($jarak),
                'radius' => $radius,
            ], 403);
        }

        if ($type === 'pulang') {
            $attendance->jam_pulang = $nowTime;
        } else {
            $attendance->jam_masuk ??= $nowTime;
        }

        $attendance->latitude = $validated['latitude'];
        $attendance->longitude = $validated['longitude'];
        $attendance->foto_biometrik = $validated['foto'] ?? $attendance->foto_biometrik;
        $attendance->save();

        return response()->json([
            'success' => true,
            'message' => 'Presensi '.ucfirst($type).' berhasil disimpan.',
            'data' => $attendance,
        ]);
    }

    /**
     * 6. Halaman Form Pengajuan Izin
     */
    public function izin()
    {
        $siswa = $this->getActiveSiswa();
        $viewName = view()->exists('dashboard siswa.izin') ? 'dashboard siswa.izin' : 'izin.index';

        return view($viewName, compact('siswa'));
    }

    /**
     * Simpan Pengajuan Izin / Sakit langsung ke Database
     */
    public function storeIzin(Request $request)
    {
        $siswa = $this->getActiveSiswa();

        $request->validate([
            'jenis' => 'required|string',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date',
            'alasan' => 'required|string',
            'bukti_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        try {
            $durasi = Carbon::parse($request->tgl_mulai)->diffInDays(Carbon::parse($request->tgl_selesai)) + 1;

            $namaFile = null;
            if ($request->hasFile('bukti_file')) {
                $file = $request->file('bukti_file');
                $namaFile = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('uploads/izin'), $namaFile);
            }

            $izin = Izin::create([
                'siswa_id' => $siswa->id,
                'jenis' => $request->jenis,
                'tgl_mulai' => $request->tgl_mulai,
                'tgl_selesai' => $request->tgl_selesai,
                'durasi_hari' => $durasi,
                'alasan' => $request->alasan,
                'bukti_file' => $namaFile,
                'status' => 'Menunggu',
            ]);

            return redirect()->route('riwayat')->with('success', "Pengajuan {$request->jenis} berhasil disimpan ke database!");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menyimpan pengajuan: '.$e->getMessage())->withInput();
        }
    }

    /**
     * 7. Riwayat Presensi & Kalender Kehadiran dari Database
     */
    public function riwayat()
    {
        $siswa = $this->getActiveSiswa();

        $daftarPresensi = [];
        $daftarIzin = [];
        $persenHadir = 95;
        $totalIzin = 0;

        try {
            if (Schema::hasTable('presensi')) {
                $daftarPresensi = Presensi::where('siswa_id', $siswa->id)
                    ->orderBy('tanggal', 'desc')
                    ->take(30)
                    ->get();

                $totalHadir = Presensi::where('siswa_id', $siswa->id)
                    ->whereMonth('tanggal', Carbon::now()->month)
                    ->whereIn('status', ['Hadir', 'Terlambat'])
                    ->count();

                $totalPresensi = Presensi::where('siswa_id', $siswa->id)
                    ->whereMonth('tanggal', Carbon::now()->month)
                    ->count();

                if ($totalPresensi > 0) {
                    $persenHadir = round(($totalHadir / $totalPresensi) * 100);
                }
            }

            if (Schema::hasTable('izin')) {
                $daftarIzin = Izin::where('siswa_id', $siswa->id)
                    ->orderBy('tgl_mulai', 'desc')
                    ->get();

                $totalIzin = Izin::where('siswa_id', $siswa->id)
                    ->whereMonth('tgl_mulai', Carbon::now()->month)
                    ->sum('durasi_hari') ?: 0;
            }
        } catch (Exception $e) {
        }

        $viewName = view()->exists('dashboard siswa.riwayat') ? 'dashboard siswa.riwayat' : 'riwayat.index';

        return view($viewName, compact('siswa', 'daftarPresensi', 'daftarIzin', 'persenHadir', 'totalIzin'));
    }

    /**
     * 8. Tugas Mata Pelajaran (Akademik) dari Database
     */
    public function mapel()
    {
        $siswa = $this->getActiveSiswa();
        $daftarMapel = [];

        try {
            if (Schema::hasTable('mapel')) {
                $daftarMapel = Mapel::orderBy('deadline', 'asc')->get();
            }
        } catch (Exception $e) {
        }

        $viewName = view()->exists('dashboard siswa.mapel') ? 'dashboard siswa.mapel' : 'mapel.index';

        return view($viewName, compact('siswa', 'daftarMapel'));
    }

    /**
     * 9. Konseling & Buku Disiplin BK dari Database
     */
    public function bk()
    {
        $siswa = $this->getActiveSiswa();
        $daftarBk = [];

        try {
            if (Schema::hasTable('bk')) {
                $daftarBk = Bk::where('siswa_id', $siswa->id)
                    ->orderBy('tanggal', 'desc')
                    ->get();
            }
        } catch (Exception $e) {
        }

        $viewName = view()->exists('dashboard siswa.bk') ? 'dashboard siswa.bk' : 'bk.index';

        return view($viewName, compact('siswa', 'daftarBk'));
    }

    /**
     * Konseling Siswa (Fitur Mandiri)
     */
    public function konseling()
    {
        $siswa = $this->getActiveSiswa();
        $daftarBk = [];

        try {
            if (Schema::hasTable('bk')) {
                $daftarBk = Bk::where('siswa_id', $siswa->id)
                    ->orderBy('tanggal', 'desc')
                    ->get();
            }
        } catch (Exception $e) {
        }

        $viewName = view()->exists('dashboard siswa.konseling') ? 'dashboard siswa.konseling' : (view()->exists('dashboard siswa.bk') ? 'dashboard siswa.bk' : 'bk.index');

        return view($viewName, compact('siswa', 'daftarBk'));
    }

    /**
     * 10. Jadwal & Checklist Kebersihan Piket Kelas dari Database
     */
    public function piket()
    {
        $siswa = $this->getActiveSiswa();
        $daftarPiket = [];

        try {
            if (Schema::hasTable('piket')) {
                $daftarPiket = Piket::all();
            }
        } catch (Exception $e) {
        }

        $viewName = view()->exists('dashboard siswa.piket') ? 'dashboard siswa.piket' : 'piket.index';

        return view($viewName, compact('siswa', 'daftarPiket'));
    }

    public function confirmPiket(Request $request)
    {
        return back()->with('success', 'Piket berhasil dikonfirmasi selesai!');
    }

    /**
     * Update Status Piket Kelas
     */
    public function updatePiket(Request $request, $id)
    {
        try {
            $piket = Piket::findOrFail($id);
            $piket->status = $piket->status === 'Selesai' ? 'Belum Selesai' : 'Selesai';
            $piket->save();

            return back()->with('success', 'Status piket kebersihan berhasil diperbarui!');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memperbarui status piket: '.$e->getMessage());
        }
    }

    /**
     * 11. Profil Siswa dari Database
     */
    public function profil()
    {
        $siswa = $this->getActiveSiswa();
        $viewName = view()->exists('dashboard siswa.profil') ? 'dashboard siswa.profil' : 'profil.index';

        return view($viewName, compact('siswa'));
    }
}
