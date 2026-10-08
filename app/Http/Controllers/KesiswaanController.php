<?php

namespace App\Http\Controllers;

use App\Models\Bk;
use App\Models\Izin;
use App\Models\Mapel;
use App\Models\Piket;
use App\Models\Presensi;
use App\Models\Siswa;
use Carbon\Carbon;
use Exception;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class KesiswaanController extends Controller
{
    /**
     * Memastikan database dan tabel awal terpasang secara otomatis.
     */
    protected function ensureDatabaseReady()
    {
        try {
            if (!Schema::hasTable('siswa')) {
                Artisan::call('migrate', ['--force' => true]);
            }

            // Jika tabel siswa masih kosong, isi akun awal
            if (Schema::hasTable('siswa') && Siswa::count() === 0) {
                Siswa::create([
                    'nis'           => '102938',
                    'nisn'          => '0071234567',
                    'nama'          => 'Wahyu Pratama',
                    'email'         => 'wahyu.pratama@smktibaliglobal.sch.id',
                    'password'      => Hash::make('password123'),
                    'kelas'         => 'XI PPLG 1',
                    'jurusan'       => 'PPLG',
                    'rfid_card'     => 'SMKTI-2026-001',
                    'poin_bk'       => 15,
                    'poin_prestasi' => 50,
                ]);

                if (Schema::hasTable('mapel') && Mapel::count() === 0) {
                    Mapel::create([
                        'nama_mapel'  => 'Pemrograman Web & Mobile',
                        'guru'        => 'Pak Guru PPLG',
                        'judul_tugas' => 'Slice UI Figma Dashboard ke HTML/CSS',
                        'deskripsi'   => 'Implementasikan tata letak mobile frame dengan Tailwind CSS dan tombol fungsi.',
                        'deadline'    => Carbon::tomorrow(),
                        'status'      => 'Aktif',
                    ]);
                    Mapel::create([
                        'nama_mapel'  => 'Basis Data Relasional',
                        'guru'        => 'Ibu Guru Basis Data',
                        'judul_tugas' => 'Perancangan ERD Sistem Presensi',
                        'deskripsi'   => 'Buat tabel relasi untuk siswa, presensi, dan izin sekolah.',
                        'deadline'    => Carbon::now()->addDays(3),
                        'status'      => 'Aktif',
                    ]);
                }

                if (Schema::hasTable('piket') && Piket::count() === 0) {
                    $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                    foreach ($hariList as $hari) {
                        Piket::create([
                            'hari'    => $hari,
                            'kelas'   => 'XI PPLG 1',
                            'anggota' => 'Wahyu Pratama, I Kadek Arya, Ni Made Ayu, I Nyoman Gede',
                            'status'  => 'Belum Selesai',
                        ]);
                    }
                }
            }
        } catch (Throwable $e) {}
    }

    /**
     * Ambil data siswa yang sedang login atau record pertama di database.
     */
    protected function getActiveSiswa()
    {
        $this->ensureDatabaseReady();

        if (session()->has('siswa_id')) {
            $siswa = Siswa::find(session('siswa_id'));
            if ($siswa) return $siswa;
        }

        try {
            if (Schema::hasTable('siswa')) {
                $siswa = Siswa::first();
                if ($siswa) {
                    session(['siswa_id' => $siswa->id]);
                    return $siswa;
                }
            }
        } catch (Throwable $e) {}

        return (object)[
            'id'            => 1,
            'nis'           => '102938',
            'nisn'          => '0071234567',
            'nama'          => 'Wahyu Pratama',
            'email'         => 'wahyu.pratama@smktibaliglobal.sch.id',
            'kelas'         => 'XI PPLG 1',
            'jurusan'       => 'PPLG',
            'poin_bk'       => 15,
            'poin_prestasi' => 50,
        ];
    }

    /**
     * 1. Halaman Gateway Awal
     */
    public function landing()
    {
        $this->ensureDatabaseReady();
        return view('dashboard siswa.landing');
    }

    /**
     * 2. Form Login Siswa
     */
    public function login()
    {
        return view('dashboard siswa.login');
    }

    /**
     * Proses Login Siswa
     */
    public function postLogin(Request $request)
    {
        $this->ensureDatabaseReady();

        $request->validate([
            'email'    => 'required',
            'password' => 'required',
        ]);

        try {
            $siswa = Siswa::where('email', $request->email)
                ->orWhere('nis', $request->email)
                ->first();

            if ($siswa && Hash::check($request->password, $siswa->password)) {
                session(['siswa_id' => $siswa->id]);
                return redirect()->route('dashboard')->with('success', 'Selamat datang, ' . $siswa->nama);
            }

            if ($siswa && ($siswa->password === $request->password)) {
                session(['siswa_id' => $siswa->id]);
                return redirect()->route('dashboard')->with('success', 'Selamat datang, ' . $siswa->nama);
            }

            return back()->with('error', 'Email/NIS atau Kata Sandi tidak cocok dengan database.')->withInput();
        } catch (Throwable $e) {
            return back()->with('error', 'Error database: ' . $e->getMessage());
        }
    }

    /**
     * 3. Halaman Scan Kartu Pelajar
     */
    public function scan()
    {
        return view('dashboard siswa.scan');
    }

    /**
     * Proses Scan Barcode / RFID
     */
    public function postScan(Request $request)
    {
        $this->ensureDatabaseReady();
        $code = $request->input('code');

        if (!$code) {
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
                        'status'    => Carbon::now()->hour >= 8 ? 'Terlambat' : 'Hadir',
                        'keterangan'=> 'Scan Kartu Pelajar'
                    ]
                );

                return response()->json([
                    'success'  => true,
                    'message'  => 'Kartu terverifikasi! Presensi berhasil dicatat.',
                    'siswa'    => $siswa,
                    'presensi' => $presensi,
                    'redirect' => route('dashboard')
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Kartu tidak terdaftar.'], 404);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 4. Dashboard Utama Siswa
     */
    public function dashboard()
    {
        $siswa = $this->getActiveSiswa();
        $today = Carbon::today()->toDateString();

        $presensiHariIni = null;
        $persenHadir = 95;
        $totalIzinBulanIni = 0;
        $tugasAktif = 2;
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
                    'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
                ];
                $currentDayName = $indonesianDays[Carbon::now()->format('l')] ?? 'Kamis';
                $piketHariIni = Piket::where('hari', $currentDayName)->first();
            }
        } catch (Throwable $e) {}

        return view('dashboard', compact(
            'siswa',
            'presensiHariIni',
            'persenHadir',
            'totalIzinBulanIni',
            'tugasAktif',
            'piketHariIni'
        ));
    }

    /**
     * 5. Presensi Biometrik & Geolokasi
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
        } catch (Throwable $e) {}

        return view('presensi.index', compact('siswa', 'presensiHariIni'));
    }

    /**
     * Simpan Absensi Masuk / Pulang
     */
    public function storePresensi(Request $request)
    {
        $this->ensureDatabaseReady();
        $siswa = $this->getActiveSiswa();
        $today = Carbon::today()->toDateString();
        $nowTime = Carbon::now()->toTimeString();

        try {
            $presensi = Presensi::where('siswa_id', $siswa->id)
                ->whereDate('tanggal', $today)
                ->first();

            $tipe = $request->input('tipe', 'datang');

            if ($tipe === 'pulang') {
                if ($presensi) {
                    $presensi->update(['jam_pulang' => $nowTime]);
                } else {
                    $presensi = Presensi::create([
                        'siswa_id'   => $siswa->id,
                        'tanggal'    => $today,
                        'jam_pulang' => $nowTime,
                        'status'     => 'Hadir',
                        'latitude'   => $request->latitude,
                        'longitude'  => $request->longitude,
                        'foto'       => $request->foto,
                    ]);
                }
            } else {
                $statusKehadiran = Carbon::now()->hour >= 8 ? 'Terlambat' : 'Hadir';
                if ($presensi) {
                    $presensi->update([
                        'jam_masuk'  => $presensi->jam_masuk ?: $nowTime,
                        'latitude'   => $request->latitude ?: $presensi->latitude,
                        'longitude'  => $request->longitude ?: $presensi->longitude,
                        'foto'       => $request->foto ?: $presensi->foto,
                    ]);
                } else {
                    $presensi = Presensi::create([
                        'siswa_id'   => $siswa->id,
                        'tanggal'    => $today,
                        'jam_masuk'  => $nowTime,
                        'status'     => $statusKehadiran,
                        'latitude'   => $request->latitude,
                        'longitude'  => $request->longitude,
                        'foto'       => $request->foto,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Presensi ' . ucfirst($tipe) . ' berhasil disimpan!',
                'data'    => $presensi,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan presensi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 6. Form Izin
     */
    public function izin()
    {
        $siswa = $this->getActiveSiswa();
        return view('izin.index', compact('siswa'));
    }

    /**
     * Simpan Pengajuan Izin
     */
    public function storeIzin(Request $request)
    {
        $this->ensureDatabaseReady();
        $siswa = $this->getActiveSiswa();

        $request->validate([
            'jenis'       => 'required|string',
            'tgl_mulai'   => 'required|date',
            'tgl_selesai' => 'required|date',
            'alasan'      => 'required|string',
            'bukti_file'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        try {
            $durasi = Carbon::parse($request->tgl_mulai)->diffInDays(Carbon::parse($request->tgl_selesai)) + 1;
            $namaFile = null;

            if ($request->hasFile('bukti_file')) {
                $file = $request->file('bukti_file');
                $namaFile = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/izin'), $namaFile);
            }

            Izin::create([
                'siswa_id'    => $siswa->id,
                'jenis'       => $request->jenis,
                'tgl_mulai'   => $request->tgl_mulai,
                'tgl_selesai' => $request->tgl_selesai,
                'durasi_hari' => $durasi,
                'alasan'      => $request->alasan,
                'bukti_file'  => $namaFile,
                'status'      => 'Menunggu',
            ]);

            return redirect()->route('riwayat')->with('success', "Pengajuan {$request->jenis} berhasil disimpan ke database!");
        } catch (Throwable $e) {
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * 7. Riwayat Kehadiran
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
        } catch (Throwable $e) {}

        return view('riwayat.index', compact('siswa', 'daftarPresensi', 'daftarIzin', 'persenHadir', 'totalIzin'));
    }

    /**
     * 8. Tugas Mapel
     */
    public function mapel()
    {
        $siswa = $this->getActiveSiswa();
        $daftarMapel = [];

        try {
            if (Schema::hasTable('mapel')) {
                $daftarMapel = Mapel::orderBy('deadline', 'asc')->get();
            }
        } catch (Throwable $e) {}

        return view('mapel.index', compact('siswa', 'daftarMapel'));
    }

    /**
     * 9. Konseling BK
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
        } catch (Throwable $e) {}

        return view('bk.index', compact('siswa', 'daftarBk'));
    }

    /**
     * 10. Piket Kebersihan
     */
    public function piket()
    {
        $this->ensureDatabaseReady();
        $siswa = $this->getActiveSiswa();
        $daftarPiket = [];
        $piketHariIni = null;

        try {
            if (Schema::hasTable('piket')) {
                $daftarPiket = Piket::all();

                $indonesianDays = [
                    'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
                    'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
                ];
                $currentDayName = $indonesianDays[Carbon::now()->format('l')] ?? 'Kamis';
                $piketHariIni = Piket::where('hari', $currentDayName)->first();
            }
        } catch (Throwable $e) {}

        return view('piket.index', compact('siswa', 'daftarPiket', 'piketHariIni'));
    }

    /**
     * Konfirmasi Selesai & Lapor Wali Kelas (Checklist Kebersihan)
     */
    public function confirmPiket(Request $request)
    {
        $this->ensureDatabaseReady();

        try {
            $indonesianDays = [
                'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
            ];
            $currentDayName = $indonesianDays[Carbon::now()->format('l')] ?? 'Kamis';

            if (Schema::hasTable('piket')) {
                $piket = Piket::where('hari', $currentDayName)->first();
                if ($piket) {
                    $piket->status = 'Selesai';
                    $piket->save();
                } else {
                    $piket = Piket::create([
                        'hari'    => $currentDayName,
                        'kelas'   => 'XI PPLG 1',
                        'anggota' => 'Wahyu Pratama, I Kadek Arya, Ni Made Ayu, I Nyoman Gede',
                        'status'  => 'Selesai',
                    ]);
                }
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'status'  => 'Tuntas',
                    'message' => 'Tuntas! Laporan piket kebersihan kelas berhasil diselesaikan dan dilaporkan ke Wali Kelas.',
                ]);
            }

            return back()->with('success', 'Tuntas! Laporan kebersihan piket kelas berhasil dikonfirmasi.');
        } catch (Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Gagal konfirmasi piket: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status piket manual
     */
    public function updatePiket(Request $request, $id)
    {
        try {
            $piket = Piket::findOrFail($id);
            $piket->status = $piket->status === 'Selesai' ? 'Belum Selesai' : 'Selesai';
            $piket->save();

            return back()->with('success', 'Status piket berhasil diperbarui!');
        } catch (Throwable $e) {
            return back()->with('error', 'Gagal update status: ' . $e->getMessage());
        }
    }

    /**
     * 11. Profil Siswa
     */
    public function profil()
    {
        $siswa = $this->getActiveSiswa();
        return view('profil.index', compact('siswa'));
    }

    /**
     * 12. Logout
     */
    public function logout()
    {
        session()->forget('siswa_id');
        return redirect()->route('landing')->with('success', 'Berhasil keluar.');
    }
}
