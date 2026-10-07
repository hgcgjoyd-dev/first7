@extends('layouts.app')

@section('title', 'Presensi Biometrik & Geolokasi')

@section('content')
<div class="space-y-6" x-data="{
    absenSubTab: 'datang',
    cameraActive: false,
    webcamStream: null,
    showResultModal: false,
    resultModalType: 'tepat_waktu',
    recordedTime: '',
    recordedDate: '',

    toggleWebcam() {
        const videoEl = document.getElementById('webcamStream');
        const simEl = document.getElementById('simulatedFaceBox');
        
        if (!this.cameraActive) {
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
                    .then(stream => {
                        this.webcamStream = stream;
                        videoEl.srcObject = stream;
                        videoEl.style.display = 'block';
                        if (simEl) simEl.style.display = 'none';
                        this.cameraActive = true;
                    })
                    .catch(err => {
                        alert('Izin kamera tidak aktif atau tidak ditemukan. Menggunakan mode simulasi presensi.');
                        this.cameraActive = false;
                    });
            } else {
                alert('Browser tidak mendukung akses kamera langsung. Mode simulasi aktif.');
            }
        } else {
            if (this.webcamStream) {
                this.webcamStream.getTracks().forEach(track => track.stop());
            }
            videoEl.style.display = 'none';
            if (simEl) simEl.style.display = 'flex';
            this.cameraActive = false;
        }
    },

    submitPresensi(type) {
        this.resultModalType = type;
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        this.recordedTime = `${hours}:${minutes}:${seconds} WITA`;

        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        this.recordedDate = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;

        this.showResultModal = true;
        this.$nextTick(() => lucide.createIcons());
    }
}">
    <!-- Top Header Card -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-soft">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div class="flex items-center space-x-3">
                <a href="{{ route('dashboard') }}" class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h2 class="text-xl font-black text-slate-900">Sistem Presensi Biometrik & Geolokasi</h2>
                    <p class="text-xs text-slate-500">SMK TI BALI GLOBAL BADUNG • Radius Kampus</p>
                </div>
            </div>

            <!-- Switch between Absen Datang & Absen Pulang (Exact from Image 4) -->
            <div class="flex p-1 bg-slate-100 rounded-2xl text-xs font-bold w-full sm:w-auto">
                <button @click="absenSubTab = 'datang'" 
                        :class="absenSubTab === 'datang' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                        class="flex-1 sm:flex-none px-5 py-2.5 rounded-xl transition-all flex items-center justify-center space-x-2">
                    <i data-lucide="scan" class="w-4 h-4"></i>
                    <span>Absen Datang</span>
                </button>
                <button @click="absenSubTab = 'pulang'" 
                        :class="absenSubTab === 'pulang' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                        class="flex-1 sm:flex-none px-5 py-2.5 rounded-xl transition-all flex items-center justify-center space-x-2">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    <span>Absen Pulang</span>
                </button>
            </div>
        </div>

        <!-- 1. SUB-TAB: ABSEN DATANG (Image 4 Left) -->
        <div x-show="absenSubTab === 'datang'" class="mt-6 space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                
                <!-- Left: Viewfinder Camera (Exact from Image 4 Left) -->
                <div class="lg:col-span-7 flex flex-col items-center">
                    <div class="w-full max-w-md aspect-4/5 rounded-[36px] bg-slate-950 border-4 border-slate-800 shadow-2xl relative overflow-hidden flex flex-col items-center justify-between p-6">
                        
                        <!-- Top status bar inside viewfinder -->
                        <div class="w-full flex items-center justify-between text-white/90 z-20 text-xs">
                            <span class="flex items-center space-x-1.5 bg-black/60 backdrop-blur-md px-3 py-1 rounded-full">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>Kamera Aktif</span>
                            </span>
                            <span class="font-mono bg-black/60 backdrop-blur-md px-3 py-1 rounded-full" x-text="currentTimeWita">07:08:22 WITA</span>
                        </div>

                        <!-- Real Video Stream -->
                        <video id="webcamStream" class="absolute inset-0 w-full h-full object-cover" autoplay playsinline muted style="display: none;"></video>
                        
                        <!-- Simulated Face Silhouette if webcam not active -->
                        <div id="simulatedFaceBox" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="w-48 h-64 rounded-full border border-blue-400/30 flex flex-col items-center justify-center">
                                <i data-lucide="user" class="w-32 h-32 text-blue-400/20"></i>
                            </div>
                        </div>

                        <!-- Cyan Corner Scanner Reticle & Laser (Exact from Image 4) -->
                        <div class="w-64 h-72 sm:w-72 sm:h-80 relative flex items-center justify-center z-20 pointer-events-none">
                            <div class="absolute top-0 left-0 w-10 h-10 border-t-4 border-l-4 border-blue-500 rounded-tl-2xl"></div>
                            <div class="absolute top-0 right-0 w-10 h-10 border-t-4 border-r-4 border-blue-500 rounded-tr-2xl"></div>
                            <div class="absolute bottom-0 left-0 w-10 h-10 border-b-4 border-l-4 border-blue-500 rounded-bl-2xl"></div>
                            <div class="absolute bottom-0 right-0 w-10 h-10 border-b-4 border-r-4 border-blue-500 rounded-br-2xl"></div>

                            <!-- Animated Laser Scan Bar -->
                            <div class="absolute left-2 right-2 h-0.5 bg-gradient-to-r from-transparent via-cyan-400 to-transparent shadow-[0_0_12px_#38bdf8] animate-laser"></div>
                            <div class="w-3 h-3 rounded-full bg-cyan-400/40 animate-ping"></div>
                        </div>

                        <!-- Bottom GPS Badge -->
                        <div class="w-full z-20 flex flex-col items-center space-y-2">
                            <div class="bg-black/70 backdrop-blur-md border border-white/10 px-3.5 py-1.5 rounded-full flex items-center space-x-2 text-white text-xs">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-400"></i>
                                <span class="font-medium">SMK TI BALI GLOBAL, Badung (Radius 12m)</span>
                                <span class="bg-emerald-500/80 text-white font-bold text-[10px] px-2 py-0.5 rounded-full">Lokasi Valid</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 flex items-center space-x-3 text-xs text-slate-500">
                        <button @click="toggleWebcam()" class="text-blue-600 hover:underline flex items-center space-x-1 font-semibold">
                            <i data-lucide="video" class="w-3.5 h-3.5"></i>
                            <span x-text="cameraActive ? 'Gunakan Simulasi Wajah' : 'Buka Kamera Perangkat Asli'">Buka Kamera Asli</span>
                        </button>
                    </div>
                </div>

                <!-- Right: Rules & Actions -->
                <div class="lg:col-span-5 space-y-5">
                    <div class="bg-blue-50/80 border border-blue-200/80 rounded-3xl p-5">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0">
                                <i data-lucide="info" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-sm text-blue-900">Petunjuk Pengambilan Foto</h4>
                                <p class="text-xs text-blue-800/80 mt-1 leading-relaxed">
                                    Pastikan wajah jelas & terang. Posisikan wajah tepat di tengah bingkai dan jangan memakai kacamata hitam atau masker saat memindai.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 rounded-3xl p-5 border border-slate-200/80 space-y-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 font-medium">Batas Waktu Tepat Waktu:</span>
                            <span class="font-bold text-slate-800">Sebelum 07:15 WITA</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 font-medium">Status Jam Sekolah:</span>
                            <span class="font-bold text-emerald-600">Presensi Datang Dibuka</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 font-medium">Toleransi Koordinat:</span>
                            <span class="font-bold text-slate-800">50 Meter dari Gerbang</span>
                        </div>
                    </div>

                    <div class="space-y-3 pt-2">
                        <button @click="submitPresensi('tepat_waktu')" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-4 px-6 rounded-2xl shadow-lg shadow-blue-500/25 flex items-center justify-center space-x-2 transition-all active:scale-98">
                            <i data-lucide="camera" class="w-5 h-5"></i>
                            <span>Ambil Foto & Catat Absensi (Tepat Waktu)</span>
                        </button>

                        <button @click="submitPresensi('terlambat')" class="w-full bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold py-3 px-6 rounded-2xl flex items-center justify-center space-x-2 transition-all text-xs">
                            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                            <span>Simulasikan Kondisi Terlambat (Lewat 07:15 WITA)</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. SUB-TAB: ABSEN PULANG (Image 4 Middle) -->
        <div x-show="absenSubTab === 'pulang'" class="mt-6 space-y-6">
            <div class="max-w-xl mx-auto bg-slate-50 rounded-3xl p-6 sm:p-8 border border-slate-200 space-y-6">
                <div class="bg-white rounded-2xl p-6 border border-slate-100 text-center shadow-xs">
                    <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold mb-3">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>WAKTU KEPULANGAN REAL-TIME</span>
                    </div>
                    <div class="text-4xl sm:text-5xl font-black text-slate-900 tracking-tight">
                        <span x-text="currentClockLive">12:25:00</span> <span class="text-xl sm:text-2xl font-bold text-slate-500">WITA</span>
                    </div>
                    <p class="text-xs text-slate-500 font-medium mt-1" x-text="currentDateLive">Kamis, 24 September 2026</p>
                </div>

                <div class="p-4 bg-emerald-50/80 border border-emerald-200 rounded-2xl flex items-center space-x-3 text-xs text-emerald-800 font-semibold">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                    <span>Tidak perlu scan wajah — Cukup konfirmasi lokasi saat berada di lingkungan sekolah.</span>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-100 flex items-center justify-between text-xs">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                            <i data-lucide="user-check" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">Absen Datang Pagi</p>
                            <p class="text-slate-500">07:05 WITA • Hadir Tepat Waktu</p>
                        </div>
                    </div>
                    <span class="bg-emerald-100 text-emerald-800 px-2.5 py-1 rounded-full font-bold">Terverifikasi</span>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-100 flex items-center justify-between text-xs">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                            <i data-lucide="map-pin" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">Posisi Anda Saat Ini</p>
                            <p class="text-slate-500">SMK TI BALI GLOBAL (Radius 18m)</p>
                        </div>
                    </div>
                    <span class="bg-emerald-100 text-emerald-800 px-2.5 py-1 rounded-full font-bold">Lokasi Valid</span>
                </div>

                <button @click="submitPresensi('pulang_sukses')" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold py-4 px-6 rounded-2xl shadow-lg shadow-emerald-500/25 flex items-center justify-center space-x-2 transition-all">
                    <i data-lucide="arrow-right-circle" class="w-5 h-5"></i>
                    <span>Konfirmasi Absen Pulang Sekarang →</span>
                </button>
            </div>
        </div>
    </div>

    <!-- RESULT MODAL (Exact from Image 4 Slips) -->
    <div x-show="showResultModal" x-cloak class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-[36px] max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative">
            
            <!-- Tepat Waktu (Green) -->
            <template x-if="resultModalType === 'tepat_waktu'">
                <div class="space-y-6">
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-3xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-4 shadow-sm">
                            <i data-lucide="check-check" class="w-8 h-8"></i>
                        </div>
                        <span class="text-xs font-extrabold uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">PENERIMAAN MASUK BERHASIL</span>
                        <h3 class="text-2xl font-black text-slate-900 mt-2">Hadir Tepat Waktu!</h3>
                        <p class="text-xs text-slate-500 mt-1">Dicatat masuk sekolah tepat waktu sebelum 07:15 WITA.</p>
                    </div>

                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 space-y-2.5 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Tanggal:</span>
                            <span class="font-bold text-slate-800" x-text="recordedDate || currentDateLive">Kamis, 24 September 2026</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Jam Presensi:</span>
                            <span class="font-mono font-bold text-emerald-600" x-text="recordedTime || currentTimeWita">07:08:22 WITA</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Radius Lokasi:</span>
                            <span class="font-bold text-slate-800">SMK TI BALI GLOBAL (Radius 8m)</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">ID Bukti Validasi:</span>
                            <span class="font-mono text-blue-600 font-bold">IN-260924-1142</span>
                        </div>
                    </div>

                    <button @click="showResultModal = false" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-3.5 rounded-2xl transition-all">
                        Tutup & Selesai
                    </button>
                </div>
            </template>

            <!-- Terlambat (Red) -->
            <template x-if="resultModalType === 'terlambat'">
                <div class="space-y-6">
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-3xl bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4 shadow-sm">
                            <i data-lucide="x-circle" class="w-8 h-8"></i>
                        </div>
                        <span class="text-xs font-extrabold uppercase tracking-widest text-rose-600 bg-rose-50 px-3 py-1 rounded-full">RESENSI MASUK BERHASIL</span>
                        <h3 class="text-2xl font-black text-rose-600 mt-2">Status: Terlambat</h3>
                        <p class="text-xs text-slate-500 mt-1">Jam masuk dicatat lewat pukul 07:15 WITA (+5 Poin Pelanggaran).</p>
                    </div>

                    <div class="bg-rose-50/60 rounded-2xl p-4 border border-rose-100 space-y-2.5 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Tanggal:</span>
                            <span class="font-bold text-slate-800" x-text="recordedDate || currentDateLive">Kamis, 24 September 2026</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Jam Presensi:</span>
                            <span class="font-mono font-bold text-rose-600" x-text="recordedTime || currentTimeWita">07:26:22 WITA</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Radius Lokasi:</span>
                            <span class="font-bold text-slate-800">SMK TI BALI GLOBAL (Radius 11m)</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">ID Bukti Validasi:</span>
                            <span class="font-mono text-rose-600 font-bold">TELAT-260924-9143</span>
                        </div>
                    </div>

                    <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-[11px] text-amber-900 flex items-center space-x-2">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600 shrink-0"></i>
                        <span>Notifikasi keterlambatan telah dikirim ke nomor WhatsApp orang tua/wali.</span>
                    </div>

                    <div class="flex space-x-2">
                        <button @click="showResultModal = false" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-3.5 rounded-2xl transition-all">
                            Tutup
                        </button>
                        <a href="{{ route('bk') }}" class="flex-1 bg-rose-600 hover:bg-rose-700 text-white font-extrabold py-3.5 rounded-2xl transition-all text-center">
                            Lihat Poin BK
                        </a>
                    </div>
                </div>
            </template>

            <!-- Pulang Sukses (Green Slip) -->
            <template x-if="resultModalType === 'pulang_sukses'">
                <div class="space-y-5">
                    <div class="text-center">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="shield-check" class="w-8 h-8"></i>
                        </div>
                        <h3 class="text-xl font-black text-slate-900">Absen Pulang Berhasil!</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Sertifikat Digital Presensi Harian Siswa</p>
                    </div>

                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 space-y-2.5 text-xs">
                        <div class="grid grid-cols-2 gap-2 pb-2 border-b border-slate-200">
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase">Absen Masuk Pagi</span>
                                <p class="font-bold text-slate-800">07:05 WITA <span class="text-emerald-600 text-[10px]">(Tepat Waktu)</span></p>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase">Absen Pulang</span>
                                <p class="font-bold text-slate-800"><span x-text="recordedTime || currentTimeWita">12:25 WITA</span> <span class="text-emerald-600 text-[10px]">(Sesuai Jadwal)</span></p>
                            </div>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Hari & Tanggal:</span>
                            <span class="font-semibold text-slate-800" x-text="recordedDate || currentDateLive">Kamis, 24 September 2026</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Total Waktu Belajar:</span>
                            <span class="font-bold text-blue-600">5 Jam 20 Menit</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Posisi/Lokasi:</span>
                            <span class="font-semibold text-slate-800">Gerbang SMK TI (Valid)</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">ID Sertifikasi Digital:</span>
                            <span class="font-mono font-bold text-slate-700">OUT-260924-80419</span>
                        </div>
                    </div>

                    <div class="p-3 bg-emerald-50 rounded-2xl border border-emerald-200 flex items-center space-x-2.5 text-xs text-emerald-800">
                        <i data-lucide="message-square" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                        <span><strong>WhatsApp Terkirim:</strong> Notifikasi kepulangan berhasil diteruskan ke Orang Tua Siswa.</span>
                    </div>

                    <button @click="showResultModal = false" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-3.5 rounded-2xl transition-all">
                        Kembali ke Halaman
                    </button>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection

