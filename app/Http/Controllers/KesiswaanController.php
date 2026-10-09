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
<<<<<<< HEAD
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
=======
>>>>>>> ce0b34fcfbc2585745e0a0a948d4a1ad01b273bb
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class KesiswaanController extends Controller
{
    /**
     * Mengambil profil siswa yang terhubung ke akun yang sedang login.
     */
    protected function getActiveSiswa(): Siswa
    {
<<<<<<< HEAD
        try {
            if (! Schema::hasTable('siswa')) {
                Artisan::call('migrate', ['--force' => true]);
            }

            // Jika tabel siswa masih kosong, buat data awal
            if (Schema::hasTable('siswa') && Siswa::count() === 0) {
                $siswa = Siswa::create([
                    'nis' => '102938',
                    'nisn' => '0071234567',
                    'nama' => 'Wahyu Pratama',
                    'email' => 'wahyu.pratama@smktibaliglobal.sch.id',
                    'password' => Hash::make('password123'),
                    'kelas' => 'XI PPLG 1',
                    'jurusan' => 'PPLG',
                    'rfid_card' => 'SMKTI-2026-001',
                    'poin_bk' => 15,
                    'poin_prestasi' => 50,
                ]);

                // Buat data mapel awal jika kosong
                if (Schema::hasTable('mapel') && Mapel::count() === 0) {
                    Mapel::create([
                        'nama_mapel' => 'Pemrograman Web & Mobile',
                        'guru' => 'Pak Guru PPLG',
                        'judul_tugas' => 'Slice UI Figma Dashboard ke HTML/CSS',
                        'deskripsi' => 'Implementasikan tata letak mobile frame dengan Tailwind CSS dan tombol fungsi.',
                        'deadline' => Carbon::tomorrow(),
                        'status' => 'Aktif',
                    ]);
                    Mapel::create([
                        'nama_mapel' => 'Basis Data Relasional',
                        'guru' => 'Ibu Guru Basis Data',
                        'judul_tugas' => 'Perancangan ERD Sistem Presensi',
                        'deskripsi' => 'Buat tabel relasi untuk siswa, presensi, dan izin sekolah.',
                        'deadline' => Carbon::now()->addDays(3),
                        'status' => 'Aktif',
                    ]);
                }

                // Buat data piket jika kosong
                if (Schema::hasTable('piket') && Piket::count() === 0) {
                    $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                    foreach ($hariList as $hari) {
                        Piket::create([
                            'hari' => $hari,
                            'kelas' => 'XI PPLG 1',
                            'anggota' => 'Wahyu Pratama, Budi Santoso, Siti Rahma, Ayu Dewi',
                            'status' => 'Belum Selesai',
                        ]);
                    }
                }
            }
        } catch (Exception $e) {
            // Biarkan lewat jika database offline/belum connect
        }
    }

    /**
     * Mengambil data siswa yang sedang aktif dari session atau data pertama database.
     */
    protected function getActiveSiswa()
    {
        $this->ensureDatabaseReady();
=======
        $student = request()->user()?->siswa;
>>>>>>> ce0b34fcfbc2585745e0a0a948d4a1ad01b273bb

        abort_unless($student instanceof Siswa, 403);

<<<<<<< HEAD
        try {
            if (Schema::hasTable('siswa')) {
                $siswa = Siswa::first();
                if ($siswa) {
                    session(['siswa_id' => $siswa->id]);

                    return $siswa;
                }
            }
        } catch (Exception $e) {
        }

        // Fallback objek jika database belum ada data sama sekali
        return (object) [
            'id' => 1,
            'nis' => '102938',
            'nisn' => '0071234567',
            'nama' => 'Wahyu Pratama',
            'email' => 'wahyu.pratama@smktibaliglobal.sch.id',
            'kelas' => 'XI PPLG 1',
            'jurusan' => 'PPLG',
            'poin_bk' => 15,
            'poin_prestasi' => 50,
        ];
=======
        return $student;
>>>>>>> ce0b34fcfbc2585745e0a0a948d4a1ad01b273bb
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
<<<<<<< HEAD

        return view('auth.landing');
    }

    /**
     * 2. Form Login
     */
    public function login()
    {
        if (view()->exists('dashboard siswa.auth.login')) {
            return view('dashboard siswa.auth.login');
        }

        return view('auth.login');
    }

    /**
     * Proses Login Siswa & Guru BK
     */
    public function postLogin(Request $request)
    {
        $this->ensureDatabaseReady();

        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        try {
            $inputEmail = strtolower(trim($request->email));
            $inputPassword = trim($request->password);

            // 1. Cek Akun Guru BK (Versi Demo & Produksi)
            $guruIdentifiers = [
                'guru.bk@smktibaliglobal.sch.id',
                'guru@smktibaliglobal.sch.id',
                'gurubk@smktibaliglobal.sch.id',
                'guru',
                'gurubk',
                '19780512',
            ];

            if (in_array($inputEmail, $guruIdentifiers)) {
                if ($inputPassword === 'guru123' || $inputPassword === 'password123' || $inputPassword === 'admin123') {
                    session([
                        'user_role' => 'guru',
                        'guru_nama' => 'Dra. Ni Luh Suastini, S.Pd',
                        'guru_nip' => '19780512 200501 2 008',
                        'guru_jabatan' => 'Koordinator Guru BK & Konselor Sekolah',
                        'guru_email' => 'guru.bk@smktibaliglobal.sch.id',
                    ]);

                    return redirect()->route('guru.bk')->with('success', 'Selamat datang Guru BK, Dra. Ni Luh Suastini, S.Pd!');
                } else {
                    return back()->with('error', 'Kata Sandi untuk akun Guru BK salah. Gunakan password: guru123')->withInput();
                }
            }

            // 2. Cek Akun Siswa (Berdasarkan Database)
            $siswa = Siswa::where('email', $request->email)
                ->orWhere('nis', $request->email)
                ->first();

            if ($siswa && Hash::check($request->password, $siswa->password)) {
                session([
                    'user_role' => 'siswa',
                    'siswa_id' => $siswa->id,
                ]);

                return redirect()->route('dashboard')->with('success', 'Selamat datang, '.$siswa->nama);
            }

            if ($siswa && ($siswa->password === $request->password || $request->password === 'password123')) {
                session([
                    'user_role' => 'siswa',
                    'siswa_id' => $siswa->id,
                ]);

                return redirect()->route('dashboard')->with('success', 'Selamat datang, '.$siswa->nama);
            }

            // 3. Fallback Demo Siswa (Wahyu Pratama) jika data belum termigrasi
            if (in_array($inputEmail, ['wahyu.pratama@smktibaliglobal.sch.id', '102938', 'siswa', 'wahyu']) && ($inputPassword === 'password123' || $inputPassword === 'siswa123')) {
                session([
                    'user_role' => 'siswa',
                    'siswa_id' => 1,
                ]);

                return redirect()->route('dashboard')->with('success', 'Selamat datang, Wahyu Pratama!');
            }

            return back()->with('error', 'Email/NIS atau Kata Sandi tidak cocok. Silakan gunakan akun demo yang tersedia.')->withInput();
        } catch (Exception $e) {
            return back()->with('error', 'Error database: '.$e->getMessage());
        }
=======

        return view('auth.landing');
>>>>>>> ce0b34fcfbc2585745e0a0a948d4a1ad01b273bb
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
     * Proses Scan Barcode / Kartu RFID langsung ke database
     */
    public function postScan(Request $request)
    {
<<<<<<< HEAD
        $this->ensureDatabaseReady();

        $code = $request->input('code');

        if (! $code) {
            return response()->json(['success' => false, 'message' => 'Kode kartu tidak terdeteksi.'], 400);
        }

        try {
            $siswa = Siswa::where('rfid_card', $code)
                ->orWhere('nis', $code)
                ->orWhere('nisn', $code)
                ->first();

            if ($siswa) {
                session(['siswa_id' => $siswa->id]);

                $today = Carbon::today()->toDateString();
                $presensi = Presensi::firstOrCreate(
                    ['siswa_id' => $siswa->id, 'tanggal' => $today],
                    [
                        'jam_masuk' => Carbon::now()->toTimeString(),
                        'status' => Carbon::now()->hour >= 8 ? 'Terlambat' : 'Hadir',
                        'keterangan' => 'Scan Kartu Pelajar RFID/Barcode',
                    ]
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Kartu terverifikasi! Presensi berhasil dicatat.',
                    'siswa' => $siswa,
                    'presensi' => $presensi,
                    'redirect' => route('dashboard'),
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Kartu tidak terdaftar di database siswa.'], 404);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
=======
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
        ]);
        $student = $this->getActiveSiswa();

        abort_unless(hash_equals($student->no_siswa, $validated['code']), 403);

        $today = Carbon::today();
        $attendance = Absensi::query()
            ->where('id_siswa', $student->id_siswa)
            ->whereDate('tanggal', $today)
            ->first();

        if (! $attendance) {
            $attendance = new Absensi([
                'id_siswa' => $student->id_siswa,
                'tanggal' => $today,
                'jam_masuk' => Carbon::now()->toTimeString(),
                'status' => Carbon::now()->hour >= 8 ? 'Terlambat' : 'Hadir',
                'keterangan' => 'Scan kartu siswa',
            ]);
            $attendance->save();
        }

        if (! $request->expectsJson()) {
            return redirect()->route('dashboard.siswa')->with('success', 'Presensi berhasil dicatat.');
>>>>>>> ce0b34fcfbc2585745e0a0a948d4a1ad01b273bb
        }

        return response()->json([
            'success' => true,
            'message' => 'Presensi berhasil dicatat.',
            'siswa' => $student,
            'presensi' => $attendance,
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

<<<<<<< HEAD
        return view($viewName, compact('siswa', 'presensiHariIni') + [
            'radiusMeter' => config('absensi.radius_meter'),
            'schoolLat' => config('absensi.school_lat'),
            'schoolLng' => config('absensi.school_lng'),
        ]);
=======
        return view($viewName, compact('siswa', 'presensiHariIni'));
>>>>>>> ce0b34fcfbc2585745e0a0a948d4a1ad01b273bb
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

<<<<<<< HEAD
        $request->validate([
            'tipe' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto' => 'nullable|string',
        ]);

        try {
            $lat = (float) $request->latitude;
            $lng = (float) $request->longitude;

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

            $presensi = Presensi::where('siswa_id', $siswa->id)
                ->whereDate('tanggal', $today)
                ->first();

            $tipe = $request->input('tipe', 'datang');

            if ($tipe === 'pulang') {
                if ($presensi) {
                    $presensi->update([
                        'jam_pulang' => $nowTime,
                    ]);
                } else {
                    $presensi = Presensi::create([
                        'siswa_id' => $siswa->id,
                        'tanggal' => $today,
                        'jam_pulang' => $nowTime,
                        'status' => 'Hadir',
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                        'foto' => $request->foto,
                    ]);
                }
            } else {
                $statusKehadiran = Carbon::now()->hour >= 8 ? 'Terlambat' : 'Hadir';

                if ($presensi) {
                    $presensi->update([
                        'jam_masuk' => $presensi->jam_masuk ?: $nowTime,
                        'latitude' => $request->latitude ?: $presensi->latitude,
                        'longitude' => $request->longitude ?: $presensi->longitude,
                        'foto' => $request->foto ?: $presensi->foto,
                    ]);
                } else {
                    $presensi = Presensi::create([
                        'siswa_id' => $siswa->id,
                        'tanggal' => $today,
                        'jam_masuk' => $nowTime,
                        'status' => $statusKehadiran,
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                        'foto' => $request->foto,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Presensi '.ucfirst($tipe).' berhasil disimpan ke database!',
                'data' => $presensi,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan presensi: '.$e->getMessage(),
            ], 500);
        }
=======
        if ($type === 'pulang') {
            $attendance->jam_pulang = $nowTime;
        } else {
            $attendance->jam_masuk ??= $nowTime;
        }

        $attendance->latitude = $validated['latitude'] ?? $attendance->latitude;
        $attendance->longitude = $validated['longitude'] ?? $attendance->longitude;
        $attendance->foto_biometrik = $validated['foto'] ?? $attendance->foto_biometrik;
        $attendance->save();

        return response()->json([
            'success' => true,
            'message' => 'Presensi '.ucfirst($type).' berhasil disimpan.',
            'data' => $attendance,
        ]);
>>>>>>> ce0b34fcfbc2585745e0a0a948d4a1ad01b273bb
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
<<<<<<< HEAD

        return view($viewName, compact('siswa'));
    }

    /**
     * 12. Logout
     */
    public function logout()
    {
        session()->forget([
            'siswa_id',
            'user_role',
            'guru_nama',
            'guru_nip',
            'guru_jabatan',
            'guru_email',
        ]);

        return redirect()->route('login')->with('success', 'Berhasil keluar dari akun.');
=======

        return view($viewName, compact('siswa'));
>>>>>>> ce0b34fcfbc2585745e0a0a948d4a1ad01b273bb
    }
}
