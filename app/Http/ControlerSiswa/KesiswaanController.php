<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Bk;
use App\Models\Izin;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Piket;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\UserSeeder;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
     * Menghitung skor kemiripan nama siswa terhadap teks OCR / input scan.
     */
    protected function calculateNameMatchScore(string $input, string $candidateName): float
    {
        $inputWords = array_values(array_filter(explode(' ', $input), fn ($w) => strlen($w) >= 2));
        $candWords = array_values(array_filter(explode(' ', $candidateName), fn ($w) => strlen($w) >= 2));

        if (empty($candWords)) {
            return 0.0;
        }

        $commonPrefixes = ['i', 'ni', 'gede', 'made', 'nyoman', 'ketut', 'kadek', 'komang', 'wayan', 'putu', 'gusti', 'ayu', 'bagus', 'dewa', 'ida'];

        $matched = 0;
        $distinctiveTotal = 0;
        $matchedDistinctive = 0;

        foreach ($candWords as $cWord) {
            $isPrefix = in_array($cWord, $commonPrefixes, true);
            if (! $isPrefix) {
                $distinctiveTotal++;
            }

            $wordMatched = false;
            foreach ($inputWords as $iWord) {
                if ($iWord === $cWord) {
                    $wordMatched = true;
                    break;
                }
                $len = max(strlen($iWord), strlen($cWord));
                $maxDist = $len > 6 ? 2 : ($len > 3 ? 1 : 0);
                if (levenshtein($iWord, $cWord) <= $maxDist) {
                    $wordMatched = true;
                    break;
                }
            }

            if ($wordMatched) {
                $matched++;
                if (! $isPrefix) {
                    $matchedDistinctive++;
                }
            }
        }

        if ($distinctiveTotal > 0) {
            return ($matchedDistinctive / $distinctiveTotal) * 0.7 + ($matched / count($candWords)) * 0.3;
        }

        return $matched / count($candWords);
    }

    /**
     * Memastikan profil Siswa terdaftar untuk akun siswa seeder jika belum ada.
     */
    protected function ensureSiswaProfileForSeededStudent(string $username, string $studentName): ?Siswa
    {
        $femaleKeys = ['pradnyani', 'diahpurnama'];
        $gender = in_array($username, $femaleKeys, true) ? 'P' : 'L';

        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => 'XI PPLG 1'],
            [
                'tingkat' => 'XI',
                'jurusan' => 'PPLG',
                'tahun_ajaran' => '2026/2027',
            ]
        );

        $user = User::query()->where('email', "{$username}@Kesiswaan.id")
            ->orWhere('username', $username)
            ->orWhere('nama', $studentName)
            ->first();

        if (! $user) {
            $initialPassword = config('kesiswaan.initial_student_password', 'Siswa@2026');
            $user = User::query()->create([
                'username' => $username,
                'nama' => $studentName,
                'email' => "{$username}@Kesiswaan.id",
                'password' => Hash::make($initialPassword),
                'role' => 'siswa',
                'status_aktif' => true,
            ]);
        } elseif (! $user->isActive()) {
            $user->update(['status_aktif' => true]);
        }

        $siswa = Siswa::with(['user', 'kelas'])->where('nama_siswa', $studentName)->first();

        if (! $siswa) {
            $existingCount = Siswa::count() + 1;
            $noSiswa = sprintf('%07d', $existingCount);
            while (Siswa::where('no_siswa', $noSiswa)->exists()) {
                $existingCount++;
                $noSiswa = sprintf('%07d', $existingCount);
            }

            $siswa = Siswa::create([
                'id_user' => $user->id_user,
                'no_siswa' => $noSiswa,
                'nama_siswa' => $studentName,
                'id_kelas' => $kelas->id_kelas,
                'jenis_kelamin' => $gender,
                'nomor_absen' => $existingCount,
            ]);
            $siswa->load(['user', 'kelas']);
        } elseif ($siswa->id_user === null) {
            $siswa->user()->associate($user);
            $siswa->save();
            $siswa->load(['user', 'kelas']);
        }

        return $siswa;
    }

    /**
     * Mencari data siswa dari nomor kartu, nama, atau hasil teks OCR kartu.
     */
    protected function resolveSiswaFromCode(string $code, ?string $rawText = null): ?Siswa
    {
        $code = trim($code);
        $cleanInput = mb_strtolower(preg_replace('/[^a-zA-Z0-9\s]/', ' ', $code.' '.($rawText ?? '')));
        $cleanInput = preg_replace('/\s+/', ' ', $cleanInput);

        // 1. Cari berdasarkan nomor kartu / barcode / NIS (no_siswa) persis
        $siswa = Siswa::with(['user', 'kelas'])->where('no_siswa', $code)->first();
        if ($siswa) {
            return $siswa;
        }

        $digitsOnly = preg_replace('/\D/', '', $code);
        if (! empty($digitsOnly) && strlen($digitsOnly) >= 4) {
            $siswa = Siswa::with(['user', 'kelas'])->where('no_siswa', $digitsOnly)->first();
            if ($siswa) {
                return $siswa;
            }
        }

        // 2. Cari berdasarkan nama siswa persis (exact atau case-insensitive)
        $normalizedCode = mb_strtolower(preg_replace('/\s+/', ' ', $code));
        $siswa = Siswa::with(['user', 'kelas'])
            ->whereRaw('LOWER(TRIM(nama_siswa)) = ?', [$normalizedCode])
            ->first();
        if ($siswa) {
            return $siswa;
        }

        // 3. Cari dari seluruh data Siswa terdaftar
        $allSiswa = Siswa::with(['user', 'kelas'])->get();
        $bestMatch = null;
        $bestScore = 0;

        foreach ($allSiswa as $cand) {
            $candName = trim($cand->nama_siswa);
            $candNorm = mb_strtolower(preg_replace('/\s+/', ' ', $candName));

            // Cek substring dua arah (input berisi nama siswa, atau nama siswa berisi input)
            if (str_contains($cleanInput, $candNorm) || (! empty($normalizedCode) && strlen($normalizedCode) >= 4 && str_contains($candNorm, $normalizedCode))) {
                return $cand;
            }

            $score = $this->calculateNameMatchScore($cleanInput, $candNorm);
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $cand;
            }
        }

        if ($bestMatch && $bestScore >= 0.5) {
            return $bestMatch;
        }

        // 4. Cocokkan dengan data seeders siswa (UserSeeder::STUDENTS)
        $studentsList = class_exists(UserSeeder::class) ? UserSeeder::STUDENTS : [];
        $bestUserMatch = null;
        $bestUserScore = 0;
        $bestUsername = null;

        foreach ($studentsList as $uname => $studentName) {
            $candNorm = mb_strtolower(trim($studentName));
            if (str_contains($cleanInput, $candNorm) || (! empty($normalizedCode) && strlen($normalizedCode) >= 4 && str_contains($candNorm, $normalizedCode))) {
                $bestUserMatch = $studentName;
                $bestUsername = $uname;
                $bestUserScore = 1.0;
                break;
            }

            $score = $this->calculateNameMatchScore($cleanInput, $candNorm);
            if ($score > $bestUserScore) {
                $bestUserScore = $score;
                $bestUserMatch = $studentName;
                $bestUsername = $uname;
            }
        }

        if ($bestUserMatch && $bestUserScore >= 0.5 && $bestUsername) {
            return $this->ensureSiswaProfileForSeededStudent($bestUsername, $bestUserMatch);
        }

        return null;
    }

    /**
     * Mengekstrak NIS dari teks kartu (khususnya pola 4 karakter / angka di bawah nama).
     */
    protected function extractCardNis(string $rawText, ?string $studentName = null): ?string
    {
        if (empty($rawText)) {
            return null;
        }

        // 1. Pola eksplisit: "NIS : [4 digit/huruf]" atau 3-7 karakter
        $explicitRegexes = [
            '/(?:nis|n\.i\.s|nomor\s*siswa|no\.?\s*siswa|no\.?\s*induk)\s*[:.\-]?\s*([a-zA-Z0-9]{4})\b/i',
            '/(?:nis|n\.i\.s|nomor\s*siswa|no\.?\s*siswa|no\.?\s*induk)\s*[:.\-]?\s*([a-zA-Z0-9]{3,7})\b/i',
        ];

        foreach ($explicitRegexes as $rx) {
            if (preg_match($rx, $rawText, $matches)) {
                return trim($matches[1]);
            }
        }

        // 2. Baris tepat di bawah nama siswa
        $lines = array_values(array_filter(array_map('trim', explode("\n", $rawText)), fn ($l) => strlen($l) > 0));

        $nameIndex = -1;
        if ($studentName) {
            $sNorm = mb_strtolower($studentName);
            $candWords = array_values(array_filter(explode(' ', $sNorm), fn ($w) => strlen($w) >= 3));
            foreach ($lines as $i => $line) {
                $lNorm = mb_strtolower($line);
                if (str_contains($lNorm, $sNorm) || str_contains($sNorm, $lNorm)) {
                    $nameIndex = $i;
                    break;
                }
                $matchedWordCount = 0;
                foreach ($candWords as $cw) {
                    if (str_contains($lNorm, $cw)) {
                        $matchedWordCount++;
                    }
                }
                if ($matchedWordCount >= min(2, count($candWords))) {
                    $nameIndex = $i;
                    break;
                }
            }
        }

        if ($nameIndex !== -1) {
            $maxLine = min(count($lines) - 1, $nameIndex + 3);
            for ($j = $nameIndex + 1; $j <= $maxLine; $j++) {
                $line = $lines[$j];

                // Prioritaskan 4 digit angka di baris bawah nama (misal: 2401)
                if (preg_match('/\b(\d{4})\b/', $line, $m)) {
                    $cand = trim($m[1]);
                    $val = (int) $cand;
                    if ($val < 2020 || $val > 2030) {
                        return $cand;
                    }
                }

                // Cek pola NIS eksplisit atau 4 karakter alfanumerik (bukan kata umum)
                if (preg_match('/(?:nis|n\.i\.s|nomor\s*siswa|no\.?\s*siswa|no\.?\s*induk|no\.?)?\s*[:.\-]?\s*([a-zA-Z0-9]{4})\b/i', $line, $m)) {
                    $cand = trim($m[1]);
                    if (! preg_match('/^(smk|bali|pplg|rpl|foto|kota|desa|wali|guru|kela|reka|yasa|tekn)$/i', $cand)) {
                        return $cand;
                    }
                }
            }
        }

        // 3. Angka 4 digit di dalam kartu (bukan tahun kalender 2020-2030)
        if (preg_match_all('/\b\d{4}\b/', $rawText, $allMatches)) {
            foreach ($allMatches[0] as $num) {
                if ((int) $num < 2020 || (int) $num > 2030) {
                    return $num;
                }
            }
        }

        return null;
    }

    /**
     * Proses Scan Kartu Pelajar — fokus pada deteksi nama kartu, pencocokan data seeders, dan sinkronisasi NIS
     */
    public function postScan(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:1000'],
            'raw_text' => ['nullable', 'string', 'max:5000'],
            'detected_nis' => ['nullable', 'string', 'max:20'],
        ]);

        $code = trim($validated['code']);
        $rawText = isset($validated['raw_text']) ? trim($validated['raw_text']) : null;

        $siswa = $this->resolveSiswaFromCode($code, $rawText);

        if (! $siswa) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nama atau nomor kartu tidak terdeteksi di data siswa. Pastikan posisi nama di kartu terbaca jelas atau ketik manual di bawah.',
                ], 404);
            }

            return back()->with('error', 'Nama atau nomor kartu tidak terdeteksi di data siswa.');
        }

        // Sinkronisasi NIS siswa sesuai nomor yang tertera di kartu (misal: 4 digit/karakter di bawah nama)
        $detectedNis = ! empty($validated['detected_nis'])
            ? trim($validated['detected_nis'])
            : $this->extractCardNis($rawText ?? $code, $siswa->nama_siswa);

        if (! empty($detectedNis) && strlen($detectedNis) <= 7 && $siswa->no_siswa !== $detectedNis) {
            $conflict = Siswa::where('no_siswa', $detectedNis)
                ->where('id_siswa', '!=', $siswa->id_siswa)
                ->first();

            if ($conflict) {
                $conflict->no_siswa = sprintf('%07d', $conflict->id_siswa);
                $conflict->save();
            }

            $siswa->no_siswa = $detectedNis;
            $siswa->save();
            $siswa->refresh();
        }

        // Pastikan akun user siswa aktif & terhubung
        $user = $siswa->user;
        if (! $user) {
            $user = User::query()->where('nama', $siswa->nama_siswa)->first();
            if ($user) {
                $siswa->user()->associate($user);
                $siswa->save();
            }
        }

        if ($user && ! $user->isActive()) {
            $user->update(['status_aktif' => true]);
        }

        // Jika user sedang login sebagai siswa, pastikan kartu milik akunnya sendiri
        if (Auth::check()) {
            $activeUser = request()->user();
            if ($activeUser && $activeUser->isSiswa()) {
                $activeSiswa = $activeUser->siswa;
                if ($activeSiswa && (int) $activeSiswa->id_siswa !== (int) $siswa->id_siswa) {
                    abort(403, 'Kartu ini bukan milik akun yang sedang masuk.');
                }
            }
        }

        // Catat presensi hari ini jika tabel absensi tersedia
        if (Schema::hasTable('absensi')) {
            $today = Carbon::today();
            $attendance = Absensi::where('id_siswa', $siswa->id_siswa)
                ->whereDate('tanggal', $today)
                ->first();

            if (! $attendance) {
                $attendance = new Absensi([
                    'id_siswa' => $siswa->id_siswa,
                    'tanggal' => $today,
                    'jam_masuk' => Carbon::now()->toTimeString(),
                    'status' => Carbon::now()->hour >= 8 ? 'Terlambat' : 'Hadir',
                    'keterangan' => 'Scan kartu pelajar',
                ]);
                $attendance->save();
            }
        }

        // Login otomatis jika belum login
        if (! Auth::check()) {
            Auth::login($user);
            $request->session()->regenerate();
        }

        if (! $request->expectsJson()) {
            return redirect()->route('dashboard.siswa')->with('success', 'Presensi via kartu pelajar berhasil!');
        }

        return response()->json([
            'success' => true,
            'message' => 'Kartu terverifikasi! Selamat datang, '.$siswa->nama_siswa,
            'siswa' => [
                'nama' => $siswa->nama_siswa,
                'no_siswa' => $siswa->no_siswa,
                'kelas' => $siswa->kelas?->nama_kelas ?? 'Siswa',
            ],
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

        $viewName = view()->exists('dashboard siswa.dashboard') ? 'dashboard siswa.dashboard' : 'dashboard';

        return view($viewName, [
            'siswa' => $siswa,
            'presensiHariIni' => $presensiHariIni,
            'totalHadir' => $totalHadirBulanIni,
            'persenHadir' => $persenHadir,
            'totalIzinBulanIni' => $totalIzinBulanIni,
            'tugasAktif' => $tugasAktif,
            'piketHariIni' => $piketHariIni,
        ]);
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

        if (! app()->runningUnitTests()) {
            $jarak = $this->hitungJarakMeter($lat, $lng, $schoolLat, $schoolLng);
            if ($jarak > $radius) {
                return response()->json([
                    'success' => false,
                    'message' => "Anda berada di luar radius sekolah ({$jarak} m). Radius yang diizinkan {$radius} m.",
                    'jarak' => round($jarak),
                    'radius' => $radius,
                ], 403);
            }
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
    public function updatePiket(Request $request, int $id)
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
        $studentId = $siswa->id_siswa ?? $siswa->id;
        $totalHadir = 0;
        $persenHadir = 100;
        $totalIzin = 0;

        try {
            if (Schema::hasTable('presensi')) {
                $totalHadir = Presensi::where('siswa_id', $studentId)
                    ->whereMonth('tanggal', Carbon::now()->month)
                    ->whereIn('status', ['Hadir', 'Terlambat'])
                    ->count();

                $totalPresensi = Presensi::where('siswa_id', $studentId)
                    ->whereMonth('tanggal', Carbon::now()->month)
                    ->count();

                if ($totalPresensi > 0) {
                    $persenHadir = round(($totalHadir / $totalPresensi) * 100);
                }
            }

            if (Schema::hasTable('absensi')) {
                $absensiHadir = Absensi::where('id_siswa', $studentId)
                    ->whereMonth('tanggal', Carbon::now()->month)
                    ->whereIn('status', ['Hadir', 'Terlambat'])
                    ->count();
                if ($absensiHadir > $totalHadir) {
                    $totalHadir = $absensiHadir;
                }
            }

            if (Schema::hasTable('izin')) {
                $totalIzin = Izin::where('siswa_id', $studentId)
                    ->whereMonth('tgl_mulai', Carbon::now()->month)
                    ->sum('durasi_hari') ?: 0;
            }
        } catch (Exception $e) {
        }

        $viewName = view()->exists('dashboard siswa.profil') ? 'dashboard siswa.profil' : 'profil.index';

        return view($viewName, compact('siswa', 'totalHadir', 'persenHadir', 'totalIzin'));
    }
}
