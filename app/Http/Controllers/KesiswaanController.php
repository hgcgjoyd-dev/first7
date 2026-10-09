<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Izin;
use App\Models\Mapel;
use App\Models\Piket;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class KesiswaanController extends Controller
{
    protected function getActiveSiswa(): Siswa
    {
        $siswa = request()->user()?->siswa;

        abort_unless($siswa instanceof Siswa, 403);

        return $siswa;
    }

    /**
     * 1. Halaman Gateway Awal
     */
    public function landing()
    {
        return view('auth.landing');
    }

    /**
     * 3. Halaman Scan Kartu Pelajar
     */
    public function scan()
    {
        $siswa = $this->getActiveSiswa();

        return view('auth.scan', compact('siswa'));
    }

    /**
     * Proses Scan Barcode / RFID
     */
    public function postScan(Request $request)
    {
        $siswa = $this->getActiveSiswa();
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20'],
        ]);

        if (! hash_equals((string) $siswa->no_siswa, $validated['code'])) {
            return response()->json(['success' => false, 'message' => 'Kartu tidak sesuai dengan akun yang masuk.'], 403);
        }

        $today = Carbon::today()->toDateString();
        $presensi = $this->dailyAttendance($siswa, $today, [
            'jam_masuk' => Carbon::now()->toTimeString(),
            'status' => Carbon::now()->hour >= 8 ? 'Terlambat' : 'Hadir',
            'keterangan' => 'Scan Kartu Pelajar',
        ]);

        return redirect()->route('dashboard.siswa')
            ->with('success', "Presensi untuk {$siswa->nama_siswa} berhasil dicatat.");
    }

    /**
     * 5. Presensi Biometrik & Geolokasi
     */
    public function presensi()
    {
        $siswa = $this->getActiveSiswa();
        $today = Carbon::today()->toDateString();
        $presensiHariIni = $siswa->absensi()->whereDate('tanggal', $today)->first();

        return view('presensi.index', compact('siswa', 'presensiHariIni'));
    }

    /**
     * Simpan Absensi Masuk / Pulang
     */
    public function storePresensi(Request $request)
    {
        $siswa = $this->getActiveSiswa();
        $validated = $request->validate([
            'tipe' => ['required', 'in:datang,pulang'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);
        $today = Carbon::today()->toDateString();
        $nowTime = Carbon::now()->toTimeString();
        $presensi = $this->dailyAttendance($siswa, $today);

        if ($validated['tipe'] === 'pulang') {
            $presensi->jam_pulang = $nowTime;
            $presensi->status ??= 'Hadir';
        } else {
            $presensi->jam_masuk ??= $nowTime;
            $presensi->status ??= Carbon::now()->hour >= 8 ? 'Terlambat' : 'Hadir';
        }

        $presensi->latitude = $validated['latitude'] ?? $presensi->latitude;
        $presensi->longitude = $validated['longitude'] ?? $presensi->longitude;
        $presensi->save();

        return response()->json([
            'success' => true,
            'message' => 'Presensi '.ucfirst($validated['tipe']).' berhasil disimpan!',
            'data' => $presensi,
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function dailyAttendance(Siswa $siswa, string $date, array $attributes = []): Absensi
    {
        $attendance = $siswa->absensi()->whereDate('tanggal', $date)->first();

        if ($attendance instanceof Absensi) {
            return $attendance;
        }

        try {
            return $siswa->absensi()->create([
                'tanggal' => $date,
                ...$attributes,
            ]);
        } catch (UniqueConstraintViolationException $exception) {
            $attendance = $siswa->absensi()->whereDate('tanggal', $date)->first();

            if (! $attendance instanceof Absensi) {
                throw $exception;
            }

            return $attendance;
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
        $siswa = $this->getActiveSiswa();
        abort_unless(Schema::hasTable('izin'), 404);

        $validated = $request->validate([
            'jenis' => ['required', 'string', 'max:30'],
            'tgl_mulai' => ['required', 'date'],
            'tgl_selesai' => ['required', 'date', 'after_or_equal:tgl_mulai'],
            'alasan' => ['required', 'string'],
            'bukti_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        $durasi = Carbon::parse($validated['tgl_mulai'])->diffInDays(Carbon::parse($validated['tgl_selesai'])) + 1;
        $namaFile = $request->file('bukti_file')?->store('izin', 'public');

        Izin::create([
            'id_siswa' => $siswa->id_siswa,
            'jenis' => $validated['jenis'],
            'tgl_mulai' => $validated['tgl_mulai'],
            'tgl_selesai' => $validated['tgl_selesai'],
            'durasi_hari' => $durasi,
            'alasan' => $validated['alasan'],
            'bukti_file' => $namaFile,
            'status' => 'Menunggu',
        ]);

        return redirect()->route('riwayat')->with('success', "Pengajuan {$validated['jenis']} berhasil disimpan ke database!");
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

        if (Schema::hasTable('absensi')) {
            $daftarPresensi = $siswa->absensi()
                ->orderBy('tanggal', 'desc')
                ->take(30)
                ->get();

            $totalHadir = $siswa->absensi()
                ->whereMonth('tanggal', Carbon::now()->month)
                ->whereIn('status', ['Hadir', 'Terlambat'])
                ->count();

            $totalPresensi = $siswa->absensi()
                ->whereMonth('tanggal', Carbon::now()->month)
                ->count();

            if ($totalPresensi > 0) {
                $persenHadir = round(($totalHadir / $totalPresensi) * 100);
            }
        }

        if (Schema::hasTable('izin')) {
            $daftarIzin = Izin::where('id_siswa', $siswa->id_siswa)
                ->orderBy('tgl_mulai', 'desc')
                ->get();

            $totalIzin = Izin::where('id_siswa', $siswa->id_siswa)
                ->whereMonth('tgl_mulai', Carbon::now()->month)
                ->sum('durasi_hari') ?: 0;
        }

        return view('riwayat.index', compact('siswa', 'daftarPresensi', 'daftarIzin', 'persenHadir', 'totalIzin'));
    }

    /**
     * 8. Tugas Mapel
     */
    public function mapel()
    {
        $siswa = $this->getActiveSiswa();
        $daftarMapel = [];

        if (Schema::hasTable('mapel')) {
            $daftarMapel = Mapel::orderBy('deadline', 'asc')->get();
        }

        return view('mapel.index', compact('siswa', 'daftarMapel'));
    }

    /**
     * 9. Konseling BK
     */
    public function bk()
    {
        $siswa = $this->getActiveSiswa();
        $daftarBk = $siswa->pelanggaranSiswa()
            ->with('jenisPelanggaran')
            ->orderByDesc('tanggal_kejadian')
            ->get();

        return view('bk.index', compact('siswa', 'daftarBk'));
    }

    /**
     * 10. Piket Kebersihan
     */
    public function piket()
    {
        $siswa = $this->getActiveSiswa();
        $daftarPiket = [];
        $piketHariIni = null;

        if (Schema::hasTable('piket')) {
            $daftarPiket = Piket::where('kelas', $siswa->kelas->nama_kelas)->get();

            $indonesianDays = [
                'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
            ];
            $currentDayName = $indonesianDays[Carbon::now()->format('l')] ?? 'Kamis';
            $piketHariIni = Piket::where('kelas', $siswa->kelas->nama_kelas)
                ->where('hari', $currentDayName)
                ->first();
        }

        return view('piket.index', compact('siswa', 'daftarPiket', 'piketHariIni'));
    }

    /**
     * Konfirmasi Selesai & Lapor Wali Kelas (Checklist Kebersihan)
     */
    public function confirmPiket(Request $request)
    {
        $siswa = $this->getActiveSiswa();
        abort_unless(Schema::hasTable('piket'), 404);

        $indonesianDays = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
        ];
        $currentDayName = $indonesianDays[Carbon::now()->format('l')] ?? 'Kamis';

        $piket = Piket::where('kelas', $siswa->kelas->nama_kelas)
            ->where('hari', $currentDayName)
            ->firstOrFail();
        $piket->update(['status' => 'Selesai']);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => 'Tuntas',
                'message' => 'Tuntas! Laporan piket kebersihan kelas berhasil diselesaikan dan dilaporkan ke Wali Kelas.',
            ]);
        }

        return back()->with('success', 'Tuntas! Laporan kebersihan piket kelas berhasil dikonfirmasi.');
    }

    /**
     * Toggle status piket manual
     */
    public function updatePiket(int $id)
    {
        $siswa = $this->getActiveSiswa();
        abort_unless(Schema::hasTable('piket'), 404);

        $piket = Piket::where('kelas', $siswa->kelas->nama_kelas)->findOrFail($id);
        $piket->status = $piket->status === 'Selesai' ? 'Belum Selesai' : 'Selesai';
        $piket->save();

        return back()->with('success', 'Status piket berhasil diperbarui!');
    }

    /**
     * 11. Profil Siswa
     */
    public function profil()
    {
        $siswa = $this->getActiveSiswa();

        return view('profil.index', compact('siswa'));
    }
}
