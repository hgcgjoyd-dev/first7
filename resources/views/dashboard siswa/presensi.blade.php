@extends('dashboard siswa.app')

@section('title', 'Sistem Absensi Siswa')

@section('content')
<div class="w-full space-y-6" x-data="{
    activeTab: '{{ request('tab') === 'pulang' ? 'pulang' : 'datang' }}',
    currentScreen: '{{ request('tab') === 'pulang' ? 'pulang_confirm' : 'datang_camera' }}',
    datangResultType: 'tepat',      // 'tepat' or 'telat'
    
    cameraActive: false,
    cameraError: false,
    errorMessage: '',
    webcamStream: null,
    isProcessing: false,
    isProcessingPulang: false,
    capturedPhoto: null,

    recordedTime: '07:05:00 WITA',
    recordedDate: 'Kamis, 24 Sep 2026',
    currentClock: '12:25:00 WITA',
    currentHour: 12,
    currentMinute: 25,
    currentSecond: 0,

    pulangTime: '12:25',
    pulangTimeFull: '12:25:44 WITA',
    isPulangConfirmed: false,

    getGreeting() {
        const hr = new Date().getHours();
        if (hr >= 4 && hr < 11) return 'Selamat Pagi,';
        if (hr >= 11 && hr < 15) return 'Selamat Siang,';
        if (hr >= 15 && hr < 18) return 'Selamat Sore,';
        return 'Selamat Malam,';
    },

    init() {
        this.updateClock();
        setInterval(() => this.updateClock(), 1000);
        
        // Cek apakah sudah pernah konfirmasi pulang sebelumnya
        try {
            const savedPulang = localStorage.getItem('presensi_pulang_done');
            if (savedPulang === 'true') {
                this.isPulangConfirmed = true;
                const savedTime = localStorage.getItem('presensi_pulang_time');
                if (savedTime) {
                    this.pulangTime = savedTime;
                    this.pulangTimeFull = savedTime + ':44 WITA';
                }
            }
        } catch(e) {}

        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        if (tabParam === 'pulang' || this.activeTab === 'pulang') {
            this.switchTab('pulang');
        } else {
            this.$nextTick(() => {
                this.startCamera();
                if (window.lucide) lucide.createIcons();
            });
        }
    },

    updateClock() {
        const now = new Date();
        this.currentHour = now.getHours();
        this.currentMinute = now.getMinutes();
        this.currentSecond = now.getSeconds();
        
        const h = String(this.currentHour).padStart(2, '0');
        const m = String(this.currentMinute).padStart(2, '0');
        const s = String(this.currentSecond).padStart(2, '0');
        this.currentClock = `${h}:${m}:${s} WITA`;

        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        this.recordedDate = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
    },

    startCamera() {
        this.cameraError = false;
        this.errorMessage = '';
        const videoEl = document.getElementById('presensiWebcam');
        
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'user',
                    width: { ideal: 640 },
                    height: { ideal: 480 }
                },
                audio: false
            })
            .then(stream => {
                this.webcamStream = stream;
                if (videoEl) {
                    videoEl.srcObject = stream;
                    videoEl.onloadedmetadata = () => {
                        videoEl.play().catch(e => console.warn(e));
                        this.cameraActive = true;
                    };
                }
            })
            .catch(err => {
                console.warn('Izin kamera belum diberikan atau diblokir browser:', err);
                this.cameraActive = false;
                this.cameraError = true;
                this.errorMessage = 'Klik tombol di bawah untuk memberikan izin akses kamera di browser Anda.';
            });
        } else {
            this.cameraActive = false;
            this.cameraError = true;
            this.errorMessage = 'Browser ini tidak mendukung akses webcam langsung.';
        }
    },

    stopCamera() {
        if (this.webcamStream) {
            this.webcamStream.getTracks().forEach(t => t.stop());
            this.webcamStream = null;
        }
        this.cameraActive = false;
    },

    switchTab(tab) {
        this.activeTab = tab;
        if (tab === 'pulang') {
            this.stopCamera();
            // Selalu bawa ke halaman konfirmasi pulang agar tombol selalu bisa dipencet
            this.currentScreen = 'pulang_confirm';
        } else {
            if (this.capturedPhoto) {
                this.currentScreen = 'datang_result';
            } else {
                this.currentScreen = 'datang_camera';
                this.startCamera();
            }
        }
        this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
    },

    confirmPulang() {
        if (this.isProcessingPulang) return;
        this.isProcessingPulang = true;
        const now = new Date();
        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');
        this.pulangTime = `${h}:${m}`;
        this.pulangTimeFull = `${h}:${m}:${s} WITA`;

        try {
            localStorage.setItem('presensi_pulang_done', 'true');
            localStorage.setItem('presensi_pulang_time', this.pulangTime);
            this.isPulangConfirmed = true;
        } catch(e) {}

        fetch('{{ route('presensi.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                tipe: 'pulang',
                latitude: -8.6478,
                longitude: 115.1764
            })
        }).catch(() => {});

        setTimeout(() => {
            this.isProcessingPulang = false;
            this.currentScreen = 'pulang_result';
            this.$nextTick(() => { 
                if (window.lucide) lucide.createIcons(); 
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }, 500);
    },

    resetPulang() {
        this.isPulangConfirmed = false;
        try {
            localStorage.removeItem('presensi_pulang_done');
            localStorage.removeItem('presensi_pulang_time');
        } catch(e) {}
        this.currentScreen = 'pulang_confirm';
        this.$nextTick(() => { 
            if (window.lucide) lucide.createIcons(); 
        });
    },

    takePhotoAndSubmit() {
        this.isProcessing = true;
        const now = new Date();
        const h = now.getHours();
        const m = now.getMinutes();
        const s = String(now.getSeconds()).padStart(2, '0');
        const hStr = String(h).padStart(2, '0');
        const mStr = String(m).padStart(2, '0');
        this.recordedTime = `${hStr}:${mStr}:${s} WITA`;

        const videoEl = document.getElementById('presensiWebcam');
        const canvas = document.getElementById('snapshotCanvas');
        if (videoEl && canvas && this.cameraActive) {
            try {
                canvas.width = videoEl.videoWidth || 640;
                canvas.height = videoEl.videoHeight || 480;
                const ctx = canvas.getContext('2d');
                ctx.translate(canvas.width, 0);
                ctx.scale(-1, 1);
                ctx.drawImage(videoEl, 0, 0, canvas.width, canvas.height);
                this.capturedPhoto = canvas.toDataURL('image/jpeg', 0.85);
            } catch(e) {
                console.warn('Canvas capture error:', e);
            }
        }

        const isLate = (h > 7) || (h === 7 && m > 5);
        this.datangResultType = isLate ? 'telat' : 'tepat';

        const todayDateStr = new Date().toISOString().slice(0, 10);
        try {
            localStorage.setItem('presensi_date_today', todayDateStr);
            localStorage.setItem('presensi_status_today', this.datangResultType);
            localStorage.setItem('presensi_jam_today', this.recordedTime);
            if (this.capturedPhoto) {
                localStorage.setItem('presensi_foto_today', this.capturedPhoto);
            }
        } catch(e) {}

        fetch('{{ route('presensi.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                tipe: 'datang',
                foto: this.capturedPhoto || null,
                latitude: -8.6478,
                longitude: 115.1764
            })
        }).catch(() => {});

        setTimeout(() => {
            this.isProcessing = false;
            this.stopCamera();
            this.currentScreen = 'datang_result';
            this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
        }, 800);
    },

    setResultType(type) {
        this.datangResultType = type;
        localStorage.setItem('presensi_status_today', type);
        this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
    }
}">

    <!-- Hidden Canvas untuk capture foto wajah asli -->
    <canvas id="snapshotCanvas" class="hidden"></canvas>

    <!-- ========================================================================= -->
    <!-- 1. TOP HEADER HERO (Matching 5 Screens in Reference)                      -->
    <!-- ========================================================================= -->
    <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 text-white rounded-3xl sm:rounded-[32px] p-5 sm:p-7 shadow-lg shadow-blue-500/15 relative overflow-hidden">
        <!-- Ambient decorative shapes -->
        <div class="absolute -top-16 -right-16 w-56 h-56 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-12 -left-12 w-48 h-48 rounded-full bg-indigo-500/20 blur-xl pointer-events-none"></div>

        <!-- Top Navigation Row: Back Button, School Brand, Avatar -->
        <div class="relative z-10 flex items-center justify-between pb-4 sm:pb-5 border-b border-white/10">
            <a href="{{ route('dashboard') }}" 
               class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white/15 hover:bg-white/25 backdrop-blur-md flex items-center justify-center text-white transition-all active:scale-95 shadow-sm"
               title="Kembali ke Dashboard">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>

            <!-- Logo Resmi SMK TI Bali Global Badung (Sesuai Mockup Desain) -->
            <div class="flex items-center space-x-2 shrink-0">
                <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK TI Bali Global Badung" class="h-7 sm:h-8 w-auto object-contain drop-shadow-sm">
                <span class="font-extrabold text-[11px] sm:text-xs tracking-wider uppercase text-white drop-shadow-xs">SMK TI BALI GLOBAL BADUNG</span>
            </div>

            <!-- Avatar -->
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white text-blue-600 flex items-center justify-center shadow-md ring-2 ring-white/30 shrink-0">
                <i data-lucide="user" class="w-5 h-5 sm:w-6 sm:h-6"></i>
            </div>
        </div>

        <!-- Middle Row: Student Info on Left, Status Badge on Right -->
        <div class="relative z-10 flex items-center justify-between pt-4 sm:pt-5">
            <div>
                <p class="text-xs sm:text-sm text-blue-100 font-medium" x-text="getGreeting()">Selamat Pagi,</p>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white leading-tight mt-0.5">
                    {{ $siswa->nama ?? 'Nama Siswa' }}
                </h1>
                <p class="text-xs sm:text-[13px] text-blue-100/90 font-semibold mt-1">
                    {{ $siswa->kelas ?? 'XI PPLG 1' }} • NIS: {{ $siswa->nis ?? '2026001' }}
                </p>
            </div>

            <!-- Dynamic Right Badge (Matches Reference Screenshots) -->
            <div>
                <!-- Screen 1: Absen Datang Time Pill -->
                <div x-show="currentScreen === 'datang_camera'" class="bg-blue-900/60 backdrop-blur-md border border-white/20 rounded-2xl px-3 sm:px-4 py-2 text-right">
                    <span class="text-[10px] font-bold text-blue-200 uppercase tracking-wider block">ABSEN DATANG</span>
                    <div class="flex items-center space-x-1.5 text-xs sm:text-sm font-black text-white mt-0.5">
                        <i data-lucide="clock" class="w-3.5 h-3.5 text-blue-300"></i>
                        <span x-text="currentHour + ':' + String(currentMinute).padStart(2, '0') + ' WITA'">07:05 WITA</span>
                    </div>
                </div>

                <!-- Screen 2 & 3: Status Verified Pill -->
                <div x-show="currentScreen === 'datang_result'" class="bg-emerald-500/20 backdrop-blur-md border border-emerald-400/40 rounded-2xl px-3 sm:px-4 py-2 text-right">
                    <span class="text-[10px] font-bold text-emerald-200 uppercase tracking-wider block">STATUS</span>
                    <span class="inline-flex items-center space-x-1 text-xs font-black text-emerald-300 mt-0.5">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                        <span>Verified</span>
                    </span>
                </div>

                <!-- Screen 4 & 5: Absen Pulang Time Pill (Exact Mockup Match) -->
                <div x-show="currentScreen === 'pulang_confirm' || currentScreen === 'pulang_result'" class="bg-white/20 backdrop-blur-md border border-white/30 rounded-2xl px-3.5 py-1.5 sm:py-2 text-right">
                    <span class="text-[9px] font-extrabold text-blue-100 uppercase tracking-wider block">ABSEN PULANG</span>
                    <div class="flex items-center space-x-1.5 text-xs sm:text-sm font-black text-white mt-0.5">
                        <i data-lucide="clock" class="w-3.5 h-3.5 text-blue-200"></i>
                        <span x-text="(pulangTime || '12:25') + ' WITA'">12:25 WITA</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. MAIN RESPONSIVE CONTENT GRID (Mobile: 1 Column, Desktop: 12 Columns)   -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- ===================================================================== -->
        <!-- LEFT COLUMN (lg:col-span-8) - ACTIVE PRESENSI SCREEN                  -->
        <!-- ===================================================================== -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-5">

            <!-- SUB-TABS: ABSEN DATENG VS ABSEN PULANG (Exact Mockup Match) -->
            <div class="bg-white p-1.5 rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs flex items-center max-w-md mx-auto w-full">
                <button type="button" 
                        @click="switchTab('datang')"
                        class="flex-1 py-3 px-4 rounded-xl sm:rounded-2xl font-bold text-xs sm:text-sm transition-all flex items-center justify-center space-x-2 cursor-pointer"
                        :class="activeTab === 'datang' 
                            ? 'bg-[#10b981] text-white shadow-md shadow-emerald-500/25' 
                            : 'text-slate-600 hover:text-slate-900 bg-transparent'">
                    <span>Absen Dateng</span>
                </button>

                <button type="button" 
                        @click="switchTab('pulang')"
                        class="flex-1 py-3 px-4 rounded-xl sm:rounded-2xl font-bold text-xs sm:text-sm transition-all flex items-center justify-center space-x-2 cursor-pointer"
                        :class="activeTab === 'pulang' 
                            ? 'bg-[#10b981] text-white shadow-md shadow-emerald-500/25' 
                            : 'text-slate-600 hover:text-slate-900 bg-transparent'">
                    <span>Absen Pulang</span>
                </button>
            </div>

            <!-- ================================================================= -->
            <!-- SCREEN 1: HALAMAN DATANG (Real Camera Face Viewfinder)            -->
            <!-- ================================================================= -->
            <div x-show="currentScreen === 'datang_camera'" class="space-y-4">
                
                <!-- Camera Viewfinder Card -->
                <div class="w-full max-w-md mx-auto aspect-square sm:aspect-4/3 rounded-3xl bg-slate-950 border-4 border-slate-900 shadow-2xl relative overflow-hidden flex flex-col items-center justify-between p-4">
                    
                    <!-- 1. Real Webcam Video Stream (Mirrored, Live Face View) -->
                    <video id="presensiWebcam" 
                           autoplay 
                           playsinline 
                           muted 
                           class="absolute inset-0 w-full h-full object-cover z-10 transition-opacity duration-300"
                           :class="cameraActive ? 'opacity-100 scale-x-[-1]' : 'opacity-0 pointer-events-none'">
                    </video>

                    <!-- 2. Fallback jika kamera belum diizinkan -->
                    <div x-show="!cameraActive" class="absolute inset-0 flex flex-col items-center justify-center z-5 bg-gradient-to-b from-slate-900 via-slate-950 to-black p-6 text-center space-y-3">
                        <div class="w-20 h-20 rounded-full border-2 border-dashed border-cyan-400/50 flex items-center justify-center text-cyan-400 bg-cyan-950/30">
                            <i data-lucide="camera" class="w-10 h-10 animate-pulse"></i>
                        </div>
                        <div class="space-y-1 max-w-xs">
                            <p class="text-xs text-white font-extrabold">Kamera Siap Diaktifkan</p>
                            <p class="text-[11px] text-slate-400 leading-relaxed" x-text="errorMessage || 'Izinkan akses kamera browser agar wajah asli tampil di bingkai.'"></p>
                        </div>
                        <button type="button" 
                                @click="startCamera()" 
                                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs rounded-xl shadow-lg shadow-blue-500/25 active:scale-95 transition-all cursor-pointer">
                            Buka / Aktifkan Kamera Wajah
                        </button>
                    </div>

                    <!-- 3. Neon Cyan Brackets (Always on top) -->
                    <div class="absolute top-4 left-4 w-9 h-9 border-t-4 border-l-4 border-cyan-400 rounded-tl-xl z-20 pointer-events-none"></div>
                    <div class="absolute top-4 right-4 w-9 h-9 border-t-4 border-r-4 border-cyan-400 rounded-tr-xl z-20 pointer-events-none"></div>
                    <div class="absolute bottom-16 left-4 w-9 h-9 border-b-4 border-l-4 border-cyan-400 rounded-bl-xl z-20 pointer-events-none"></div>
                    <div class="absolute bottom-16 right-4 w-9 h-9 border-b-4 border-r-4 border-cyan-400 rounded-br-xl z-20 pointer-events-none"></div>

                    <!-- 4. Animated Laser Scanner Line -->
                    <div class="absolute left-6 right-6 h-0.5 bg-gradient-to-r from-transparent via-cyan-400 to-transparent shadow-[0_0_15px_#22d3ee] animate-laser z-20 pointer-events-none"></div>

                    <!-- 5. Top Live Badge -->
                    <div class="relative z-30 self-end">
                        <span x-show="cameraActive" class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-emerald-500/90 backdrop-blur-md text-[10px] font-black text-white shadow-md">
                            <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                            <span>KAMERA AKTIF</span>
                        </span>
                    </div>

                    <!-- 6. Bottom Location Pill (Inside camera box) -->
                    <div class="relative z-30 w-full max-w-sm bg-slate-900/90 backdrop-blur-md border border-slate-700/80 px-3.5 py-2 rounded-2xl flex items-center justify-between text-xs text-white shadow-lg">
                        <div class="flex items-center space-x-2">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-400"></i>
                            <span class="font-bold text-[11px] sm:text-xs text-slate-200">SMK TI BALI GLOBAL Badung (Radius 12m)</span>
                        </div>
                        <span class="bg-emerald-500 text-slate-950 font-black text-[10px] px-2.5 py-0.5 rounded-full uppercase">
                            Lokasi Valid
                        </span>
                    </div>
                </div>

                <!-- Instruction Alert Box -->
                <div class="w-full max-w-md mx-auto bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex items-start space-x-3 text-xs">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 font-bold">
                        <i data-lucide="info" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h5 class="font-extrabold text-slate-900 text-xs sm:text-[13px]">Pastikan Wajah Jelas & Terang</h5>
                        <p class="text-slate-500 text-[11px] mt-0.5 leading-relaxed">
                            Posisikan wajah tepat di tengah bingkai dan jangan memakai kacamata hitam.
                        </p>
                    </div>
                </div>

                <!-- Action Button: Ambil Foto & Catat Absensi -->
                <div class="w-full max-w-md mx-auto">
                    <button type="button" 
                            @click="takePhotoAndSubmit()" 
                            :disabled="isProcessing"
                            class="w-full py-4 bg-blue-600 hover:bg-blue-700 active:scale-98 text-white font-extrabold text-sm rounded-2xl shadow-lg shadow-blue-500/25 flex items-center justify-center space-x-2 transition-all cursor-pointer">
                        <span x-show="!isProcessing" class="inline-flex items-center space-x-2">
                            <i data-lucide="camera" class="w-5 h-5"></i>
                            <span>Ambil Foto & Catat Absensi</span>
                            <i data-lucide="eye" class="w-4 h-4 opacity-80"></i>
                        </span>
                        <span x-show="isProcessing" class="inline-flex items-center space-x-2" style="display: none;">
                            <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                            <span>Mengambil Foto Wajah & Merekam Jam...</span>
                        </span>
                    </button>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- SCREEN 2 & 3: DATENG HASIL (TELAT ATAU TEPAT WAKTU)               -->
            <!-- ================================================================= -->
            <div x-show="currentScreen === 'datang_result'" class="space-y-4 w-full max-w-md mx-auto">
                
                <!-- Time Filter Simulator Pills (Exact from Screens 2 & 3) -->
                <div class="flex items-center space-x-2 bg-slate-100 p-1.5 rounded-full border border-slate-200">
                    <button type="button" 
                            @click="setResultType('tepat')" 
                            class="flex-1 py-2 px-3 rounded-full text-xs font-black transition-all cursor-pointer flex items-center justify-center space-x-1.5"
                            :class="datangResultType === 'tepat' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        <span>Tepat Waktu (≤ 07:05)</span>
                    </button>

                    <button type="button" 
                            @click="setResultType('telat')" 
                            class="flex-1 py-2 px-3 rounded-full text-xs font-black transition-all cursor-pointer flex items-center justify-center space-x-1.5"
                            :class="datangResultType === 'telat' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                        <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                        <span>Telat Waktu (> 07:05)</span>
                    </button>
                </div>

                <!-- ========================================================= -->
                <!-- OUTCOME A: TELAT (Screen 2: DATENG TERLAM...)             -->
                <!-- ========================================================= -->
                <div x-show="datangResultType === 'telat'" class="space-y-4">
                    <!-- Red Result Card with Face Snapshot -->
                    <div class="bg-gradient-to-r from-rose-500 via-rose-600 to-rose-700 rounded-3xl p-5 sm:p-6 text-white shadow-xl shadow-rose-500/20 relative">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start space-x-3.5">
                                <!-- Captured Real Face or Icon -->
                                <template x-if="capturedPhoto">
                                    <img :src="capturedPhoto" alt="Wajah Asli" class="w-14 h-14 rounded-2xl object-cover border-2 border-white/60 shadow-md shrink-0">
                                </template>
                                <template x-if="!capturedPhoto">
                                    <div class="w-12 h-12 rounded-2xl bg-black/20 flex items-center justify-center shrink-0">
                                        <i data-lucide="x" class="w-7 h-7 stroke-[3] text-white"></i>
                                    </div>
                                </template>
                                
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-rose-200 block">
                                        ABSENSI MASUK BERHASIL
                                    </span>
                                    <h3 class="text-xl sm:text-2xl font-black text-white mt-0.5 leading-snug">
                                        Telat
                                    </h3>
                                    <p class="text-xs text-rose-100 mt-0.5 leading-relaxed">
                                        Tercatat melewati batas toleransi pukul 07.05 WITA.
                                    </p>
                                </div>
                            </div>
                            <span class="bg-rose-900/60 border border-rose-400/40 text-white font-black text-[10px] sm:text-xs px-3 py-1 rounded-full uppercase shrink-0">
                                TERLAMBAT
                            </span>
                        </div>
                    </div>

                    <!-- Details Table -->
                    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-soft space-y-3 text-xs">
                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-slate-500 flex items-center space-x-1.5">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>Tanggal</span>
                            </span>
                            <span class="font-extrabold text-slate-900" x-text="recordedDate">Kamis, 8 Oktober 2026</span>
                        </div>

                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-slate-500 flex items-center space-x-1.5">
                                <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>Jam</span>
                            </span>
                            <span class="font-black text-rose-600 text-sm" x-text="recordedTime">07:06:22 WITA</span>
                        </div>

                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-slate-500 flex items-center space-x-1.5">
                                <i data-lucide="navigation" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>Radius Lokasi</span>
                            </span>
                            <span class="font-bold text-slate-800 text-right">SMK TI BALI GLOBAL BADUNG (Radius 8m)</span>
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <span class="text-slate-500 flex items-center space-x-1.5">
                                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>ID Bukti Validasi</span>
                            </span>
                            <span class="bg-blue-50 text-blue-700 font-mono font-black text-[11px] px-2.5 py-1 rounded-lg">
                                ABS-{{ date('Ymd') }}-8841
                            </span>
                        </div>
                    </div>

                    <!-- Back to Home Button -->
                    <a href="{{ route('dashboard') }}" 
                       class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 active:scale-98 text-white font-extrabold text-xs sm:text-sm rounded-2xl shadow-lg shadow-blue-500/25 flex items-center justify-center space-x-2 transition-all">
                        <i data-lucide="home" class="w-4 h-4"></i>
                        <span>Kembali ke Halaman Beranda</span>
                    </a>
                </div>

                <!-- ========================================================= -->
                <!-- OUTCOME B: TEPAT WAKTU (Screen 3: DATENG TEPAT)            -->
                <!-- ========================================================= -->
                <div x-show="datangResultType === 'tepat'" class="space-y-4">
                    <!-- Green Result Card with Face Snapshot -->
                    <div class="bg-emerald-50/90 border border-emerald-200 rounded-3xl p-5 sm:p-6 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start space-x-3.5">
                                <!-- Captured Real Face or Icon -->
                                <template x-if="capturedPhoto">
                                    <img :src="capturedPhoto" alt="Wajah Asli" class="w-14 h-14 rounded-2xl object-cover border-2 border-emerald-300 shadow-md shrink-0">
                                </template>
                                <template x-if="!capturedPhoto">
                                    <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-md shadow-emerald-500/20">
                                        <i data-lucide="check" class="w-7 h-7 stroke-[3]"></i>
                                    </div>
                                </template>

                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700 block">
                                        ABSENSI MASUK BERHASIL
                                    </span>
                                    <h3 class="text-xl sm:text-2xl font-black text-slate-900 mt-0.5 leading-snug">
                                        Hadir Tepat Waktu
                                    </h3>
                                    <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
                                        Berhasil dicatat sebelum batas pukul 07.05 WITA.
                                    </p>
                                </div>
                            </div>
                            <span class="bg-emerald-100 text-emerald-800 font-black text-[10px] sm:text-xs px-3 py-1 rounded-full uppercase shrink-0">
                                TEPAT WAKTU
                            </span>
                        </div>
                    </div>

                    <!-- Details Table -->
                    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-soft space-y-3 text-xs">
                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-slate-500 flex items-center space-x-1.5">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>Tanggal</span>
                            </span>
                            <span class="font-extrabold text-slate-900" x-text="recordedDate">Kamis, 8 Oktober 2026</span>
                        </div>

                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-slate-500 flex items-center space-x-1.5">
                                <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>Jam</span>
                            </span>
                            <span class="font-black text-emerald-700 text-sm">07:05:00 WITA</span>
                        </div>

                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-slate-500 flex items-center space-x-1.5">
                                <i data-lucide="navigation" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>Radius Lokasi</span>
                            </span>
                            <span class="font-bold text-slate-800 text-right">SMK TI BALI GLOBAL BADUNG (Radius 8m)</span>
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <span class="text-slate-500 flex items-center space-x-1.5">
                                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>ID Bukti Validasi</span>
                            </span>
                            <span class="bg-blue-50 text-blue-700 font-mono font-black text-[11px] px-2.5 py-1 rounded-lg">
                                ABS-{{ date('Ymd') }}-8841
                            </span>
                        </div>
                    </div>

                    <!-- Back to Home Button -->
                    <a href="{{ route('dashboard') }}" 
                       class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 active:scale-98 text-white font-extrabold text-xs sm:text-sm rounded-2xl shadow-lg shadow-blue-500/25 flex items-center justify-center space-x-2 transition-all">
                        <i data-lucide="home" class="w-4 h-4"></i>
                        <span>Kembali ke Halaman Beranda</span>
                    </a>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- SCREEN 4: HALAMAN PULANG (Confirmation View - Exact Mockup Left)  -->
            <!-- ================================================================= -->
            <div x-show="currentScreen === 'pulang_confirm'" class="space-y-4 w-full max-w-md mx-auto animate-in fade-in duration-200">
                
                <!-- Waktu Kepulangan Card -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 border border-emerald-200/80 shadow-soft text-center space-y-4 relative overflow-hidden">
                    <!-- Top row: Pill WAKTU KEPULANGAN di kiri, Tanggal di kanan -->
                    <div class="flex items-center justify-between text-xs">
                        <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-extrabold">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>WAKTU KEPULANGAN</span>
                        </span>
                        <span class="font-semibold text-slate-500 text-xs" x-text="recordedDate">Kamis, 24 Sep 2026</span>
                    </div>

                    <!-- Big Clock Display -->
                    <div class="py-2 flex items-baseline justify-center space-x-2">
                        <h2 class="text-5xl sm:text-6xl font-black text-emerald-600 tracking-tight" 
                            x-text="pulangTime || '12:25'">12 : 25</h2>
                        <span class="bg-emerald-100 text-emerald-800 font-extrabold text-xs px-2.5 py-1 rounded-lg">WITA</span>
                    </div>

                    <!-- Pill: Tidak perlu scan wajah — Cukup konfirmasi -->
                    <div class="inline-flex items-center space-x-2 px-4 py-2 rounded-full bg-white border border-emerald-200 text-slate-700 text-xs font-bold shadow-2xs">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600 stroke-[3]"></i>
                        <span>Tidak perlu scan wajah — Cukup konfirmasi</span>
                    </div>
                </div>

                <!-- Info banner jika sudah pernah konfirmasi hari ini -->
                <div x-show="isPulangConfirmed" class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-between text-xs">
                    <span class="text-emerald-800 font-bold flex items-center space-x-1.5">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                        <span>Kepulangan tercatat pukul <strong x-text="pulangTime">12:25</strong> WITA</span>
                    </span>
                    <button type="button" @click="currentScreen = 'pulang_result'" class="text-emerald-700 font-extrabold underline cursor-pointer hover:text-emerald-950">
                        Lihat Bukti Berhasil →
                    </button>
                </div>

                <!-- Row 1: Absen Datang Pagi Status -->
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-2xs flex items-center justify-between">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold shrink-0">
                            <i data-lucide="user-check" class="w-5 h-5"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">ABSEN DATANG PAGI</p>
                            <p class="font-bold text-slate-900 mt-0.5 text-xs sm:text-sm">
                                07:05 • <span class="text-emerald-600 font-extrabold">Hadir Tepat Waktu</span>
                            </p>
                        </div>
                    </div>
                    <span class="border border-emerald-300 text-emerald-700 bg-emerald-50 text-[11px] font-bold px-3 py-1 rounded-full shrink-0">
                        Terverifikasi
                    </span>
                </div>

                <!-- Row 2: GPS Location Status -->
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-2xs flex items-center justify-between">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold shrink-0">
                            <i data-lucide="map-pin" class="w-5 h-5"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">POSISI ANDA SAAT INI</p>
                            <p class="font-bold text-slate-900 mt-0.5 text-xs sm:text-sm">SMK TI BALI GLOBAL Badung</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Radius GPS: 8 meter (Di Dalam Zona)</p>
                        </div>
                    </div>
                    <span class="bg-[#064e3b] text-white font-bold text-xs px-3.5 py-1.5 rounded-full shadow-2xs shrink-0">
                        Lokasi Valid
                    </span>
                </div>

                <!-- Confirm Button (Always Clickable) -->
                <button type="button" 
                        @click="confirmPulang()" 
                        :disabled="isProcessingPulang"
                        class="w-full py-4 px-6 bg-[#10b981] hover:bg-emerald-600 active:scale-98 text-white font-extrabold text-sm sm:text-base rounded-2xl shadow-lg shadow-emerald-500/25 flex items-center justify-center space-x-2 transition-all cursor-pointer">
                    <span x-show="!isProcessingPulang" class="inline-flex items-center space-x-2">
                        <i data-lucide="check" class="w-5 h-5 stroke-[3]"></i>
                        <span>Konfirmasi Absen Pulang Sekarang →</span>
                    </span>
                    <span x-show="isProcessingPulang" class="inline-flex items-center space-x-2" style="display: none;">
                        <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                        <span>Menyimpan Kepulangan...</span>
                    </span>
                </button>
            </div>

            <!-- ================================================================= -->
            <!-- SCREEN 5: HALAMAN BERHASIL PULANG (Exact Mockup Right)            -->
            <!-- ================================================================= -->
            <div x-show="currentScreen === 'pulang_result'" class="space-y-4 w-full max-w-md mx-auto animate-in fade-in duration-200">
                
                <!-- Notification Banner Card -->
                <div class="bg-white rounded-2xl sm:rounded-3xl p-5 border border-slate-200/80 shadow-soft flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-2xl bg-[#10b981] text-white flex items-center justify-center shrink-0 shadow-md shadow-emerald-500/20">
                        <i data-lucide="check" class="w-7 h-7 stroke-[3]"></i>
                    </div>
                    <div class="text-left">
                        <h4 class="font-black text-base text-slate-900 flex items-center space-x-1.5">
                            <span>Absen Pulang Berhasil!</span>
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                        </h4>
                        <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                            Data kepulangan telah tercatat resmi di server sekolah, Selamat beristirahat!
                        </p>
                    </div>
                </div>

                <!-- Summary Card: ABSENSI -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-soft space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center space-x-2">
                            <i data-lucide="book-open" class="w-4 h-4 text-blue-600"></i>
                            <h4 class="font-extrabold text-xs uppercase tracking-wider text-slate-800">ABSENSI</h4>
                        </div>
                        <span class="bg-emerald-100 text-emerald-800 text-[11px] font-black px-3 py-0.5 rounded-full uppercase">BERHASIL</span>
                    </div>

                    <!-- 2 Mini Cards: Masuk & Pulang -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3.5 rounded-2xl bg-slate-50/80 border border-slate-100 space-y-1.5 text-left">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">ABSEN MASUK PAGI</span>
                            <h5 class="text-base sm:text-lg font-black text-slate-900">07:05 <span class="text-[10px] text-slate-400 font-semibold">WITA</span></h5>
                            <span class="inline-flex items-center space-x-1 bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                <i data-lucide="check" class="w-3 h-3"></i>
                                <span>Tepat Waktu</span>
                            </span>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-[#ecfdf5] border border-emerald-200 space-y-1.5 text-left">
                            <span class="text-[10px] font-black text-emerald-700 uppercase tracking-wider block">ABSEN PULANG</span>
                            <h5 class="text-base sm:text-lg font-black text-emerald-600" x-text="(pulangTime || '12:25') + ' WITA'">12:25 WITA</h5>
                            <span class="inline-flex items-center space-x-1 bg-emerald-100/70 text-emerald-800 border border-emerald-300 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                <i data-lucide="info" class="w-3 h-3"></i>
                                <span>Sesuai Jadwal</span>
                            </span>
                        </div>
                    </div>

                    <!-- Detail List -->
                    <div class="divide-y divide-slate-100 text-xs pt-1">
                        <div class="py-2.5 flex items-center justify-between">
                            <span class="text-slate-500">Hari & Tanggal</span>
                            <span class="font-extrabold text-slate-900" x-text="recordedDate">Kamis, 24 September 2026</span>
                        </div>

                        <div class="py-2.5 flex items-center justify-between">
                            <span class="text-slate-500">Total Waktu Belajar</span>
                            <span class="font-extrabold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg">5 Jam 20 Menit</span>
                        </div>

                        <div class="py-2.5 flex items-center justify-between">
                            <span class="text-slate-500">Posisi Lokasi</span>
                            <span class="font-extrabold text-emerald-700 flex items-center space-x-1">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-600"></i>
                                <span>Gerbang SMK TI (Valid)</span>
                            </span>
                        </div>

                        <div class="py-2.5 flex items-center justify-between">
                            <span class="text-slate-500">Status Konfirmasi</span>
                            <span class="font-extrabold text-emerald-600">Terverifikasi</span>
                        </div>

                        <div class="pt-2.5 flex items-center justify-between bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <div class="flex items-center space-x-2">
                                <i data-lucide="qr-code" class="w-4 h-4 text-slate-500"></i>
                                <div>
                                    <p class="text-[9px] uppercase font-bold text-slate-400">ID VERIFIKASI DIGITAL</p>
                                    <p class="font-mono font-bold text-slate-700 text-xs">OUT-20260924-88419</p>
                                </div>
                            </div>
                            <span class="font-mono text-[10px] text-slate-400" x-text="pulangTimeFull || '12:25:44 WITA'">12:25:44 WITA</span>
                        </div>
                    </div>

                    <!-- WhatsApp Notification Card -->
                    <div class="p-4 rounded-2xl bg-[#ecfdf5] border border-emerald-200/90 flex items-center space-x-3.5 text-xs">
                        <div class="w-10 h-10 rounded-2xl bg-[#25D366] text-white flex items-center justify-center shrink-0 shadow-xs">
                            <i data-lucide="message-circle" class="w-5 h-5"></i>
                        </div>
                        <div class="text-left">
                            <h6 class="font-black text-slate-900 text-xs">Notifikasi WhatsApp Terkirim</h6>
                            <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                                Pesan otomatis telah dikirim ke nomor orang tua / wali murid (+62 812-****-3391).
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons: Kembali ke Beranda & Reset Simulasi -->
                <div class="space-y-2">
                    <a href="{{ route('dashboard') }}" 
                       class="w-full py-4 px-6 bg-[#1d4ed8] hover:bg-blue-700 active:scale-98 text-white font-extrabold text-sm rounded-2xl shadow-lg shadow-blue-600/25 flex items-center justify-center space-x-2 transition-all">
                        <i data-lucide="home" class="w-4 h-4"></i>
                        <span>Kembali ke Beranda</span>
                    </a>

                    <button type="button" 
                            @click="resetPulang()" 
                            class="w-full py-2.5 text-center text-xs font-bold text-slate-400 hover:text-slate-700 transition-colors flex items-center justify-center space-x-1.5 cursor-pointer">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                        <span>Ulangi / Coba Konfirmasi Pulang Lagi</span>
                    </button>
                </div>
            </div>

        </div>

        <!-- ===================================================================== -->
        <!-- RIGHT COLUMN (lg:col-span-4) - DESKTOP SIDEBAR WIDGETS                -->
        <!-- ===================================================================== -->
        <div class="lg:col-span-5 xl:col-span-4 space-y-5">

            <!-- KETENTUAN JAM PRESENSI (07:05 WITA) -->
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-soft space-y-4">
                <div class="flex items-center space-x-2.5 pb-3 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm text-slate-900">Ketentuan Jam Absensi</h4>
                        <p class="text-[11px] text-slate-500">SMK TI Bali Global Badung</p>
                    </div>
                </div>

                <div class="space-y-2.5 text-xs">
                    <div class="p-3 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-between">
                        <div>
                            <span class="font-extrabold text-emerald-900">≤ 07:05 WITA</span>
                            <p class="text-[11px] text-emerald-700">Absensi Tepat Waktu</p>
                        </div>
                        <span class="font-black text-emerald-800 bg-white px-2.5 py-0.5 rounded-full text-[10px]">Tepat</span>
                    </div>

                    <div class="p-3 rounded-2xl bg-rose-50 border border-rose-200 flex items-center justify-between">
                        <div>
                            <span class="font-extrabold text-rose-900">> 07:05 WITA</span>
                            <p class="text-[11px] text-rose-700">Dinyatakan Terlambat</p>
                        </div>
                        <span class="font-black text-rose-800 bg-white px-2.5 py-0.5 rounded-full text-[10px]">Telat</span>
                    </div>
                </div>
            </div>

            <!-- GEOFENCE & GPS STATUS -->
            <div class="bg-gradient-to-br from-slate-900 to-blue-950 text-white rounded-3xl p-5 sm:p-6 shadow-soft space-y-3.5">
                <div class="flex items-center space-x-2 text-blue-300 text-xs font-bold uppercase tracking-wider">
                    <i data-lucide="map-pin" class="w-4 h-4"></i>
                    <span>Geofence GPS</span>
                </div>
                <h4 class="font-black text-base text-white">Status Lokasi Siswa</h4>
                <p class="text-xs text-blue-200/90 leading-relaxed">
                    Sistem memvalidasi koordinat GPS dalam radius <strong>50 meter</strong> dari gerbang sekolah.
                </p>
                <div class="pt-2 border-t border-white/10 flex items-center justify-between text-xs">
                    <span class="text-slate-300">Akurasi GPS:</span>
                    <span class="font-bold text-emerald-400">8 Meter (Valid)</span>
                </div>
            </div>

            <!-- QUICK SHORTCUT BUTTONS -->
            <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-soft space-y-2.5">
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-2">PINTASAN LAYANAN</h4>
                
                <a href="{{ route('riwayat') }}" class="w-full p-3 rounded-2xl bg-slate-50 hover:bg-slate-100 flex items-center justify-between text-xs font-bold text-slate-700 transition-colors">
                    <span class="flex items-center space-x-2">
                        <i data-lucide="calendar" class="w-4 h-4 text-blue-600"></i>
                        <span>Lihat Kalender Kehadiran</span>
                    </span>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                </a>

                <a href="{{ route('izin') }}" class="w-full p-3 rounded-2xl bg-slate-50 hover:bg-slate-100 flex items-center justify-between text-xs font-bold text-slate-700 transition-colors">
                    <span class="flex items-center space-x-2">
                        <i data-lucide="mail" class="w-4 h-4 text-amber-500"></i>
                        <span>Formulir Izin & Sakit</span>
                    </span>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                </a>
            </div>

        </div>

    </div>

</div>
@endsection
