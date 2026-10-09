@extends('layouts.auth')

@section('title', 'Scan Kartu Pelajar')

@section('content')
<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-soft text-center space-y-5"
     x-data="{
        cameraActive: false,
        cameraError: false,
        errorMessage: '',
        webcamStream: null,
        facingMode: 'environment',
        scanning: false,
        scanned: false,
        scanFailed: false,
        scanErrorMessage: '',
        isBlurry: false,
        capturedPhoto: null,
        detectInterval: null,
        ocrWorker: null,
        ocrLoading: false,
        ocrStatus: '',

        init() {
            this.$nextTick(() => {
                this.startCamera();
                if (window.lucide) lucide.createIcons();
            });
        },

        startCamera() {
            this.cameraError = false;
            this.errorMessage = '';
            const videoEl = document.getElementById('scannerWebcam');

            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                this.stopCamera();

                navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: this.facingMode,
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
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
                            this.setupFocus();
                            // Tidak perlu auto-detection OCR di background — cukup saat tombol diklik
                        };
                    }
                })
                .catch(err => {
                    console.warn(
                        'Izin kamera belum aktif atau sedang digunakan:',
                        err
                    );

                    if (this.facingMode === 'environment') {
                        this.facingMode = 'user';
                        this.startCamera();
                        return;
                    }

                    this.cameraActive = false;
                    this.cameraError = true;
                    this.errorMessage =
                        'Klik tombol di bawah untuk memberikan izin kamera pada browser Anda.';
                });
            } else {
                this.cameraActive = false;
                this.cameraError = true;
                this.errorMessage =
                    'Browser ini tidak mendukung akses kamera langsung.';
            }
        },

        toggleFacingMode() {
            this.facingMode =
                (this.facingMode === 'environment')
                    ? 'user'
                    : 'environment';

            this.startCamera();
        },

        stopCamera() {
            if (this.detectInterval) {
                clearInterval(this.detectInterval);
                this.detectInterval = null;
            }

            if (this.webcamStream) {
                this.webcamStream.getTracks().forEach(t => t.stop());
                this.webcamStream = null;
            }

            this.cameraActive = false;
        },

        computeSharpness(videoEl) {
            const canvas = document.getElementById('sharpnessCanvas');
            if (!canvas) return 1000;
            const ctx = canvas.getContext('2d');
            const w = 160, h = 120;
            canvas.width = w; canvas.height = h;
            ctx.drawImage(videoEl, 0, 0, w, h);
            const data = ctx.getImageData(0, 0, w, h).data;
            const gray = new Uint8Array(w * h);
            for (let i = 0; i < w * h; i++) {
                const r = data[i * 4], g = data[i * 4 + 1], b = data[i * 4 + 2];
                gray[i] = 0.299 * r + 0.587 * g + 0.114 * b;
            }

            let sum = 0, sumSq = 0, n = 0;
            for (let y = 1; y < 119; y++) {
                for (let x = 1; x < 159; x++) {
                    const idx = y * 160 + x;
                    const v = gray[idx];
                    const lap = gray[(y - 1) * 160 + x] + gray[(y + 1) * 160 + x] +
                                gray[y * 160 + x - 1] + gray[y * 160 + x + 1] - 4 * v;
                    sum += lap;
                    sumSq += lap * lap;
                    n++;
                }
            }
            const mean = sum / n;
            return (sumSq / n) - (mean * mean); // Laplacian variance
        },

        setupFocus() {
            const videoEl = document.getElementById('scannerWebcam');
            if (!videoEl || !videoEl.srcObject) return;

            const tracks = videoEl.srcObject.getVideoTracks();
            if (tracks.length === 0) return;

            const capabilities = tracks[0].getCapabilities?.();
            if (capabilities && capabilities.focusMode) {
                try {
                    tracks[0].applyConstraints({
                        focusMode: 'continuous',
                        advanced: [{ focusDistance: 0.3 }]
                    }).catch(() => {});
                } catch (e) {}
            }
        },

        async initOcrWorker() {
            if (this.ocrWorker) return;
            this.ocrLoading = true;
            this.ocrStatus = 'Memuat mesin OCR...';
            try {
                this.ocrWorker = await Tesseract.createWorker('eng', 1, {
                    logger: m => console.log(m),
                    cacheMethod: 'write'
                });
                this.ocrLoading = false;
                this.ocrStatus = '';
            } catch (e) {
                console.error('OCR init error:', e);
                this.ocrLoading = false;
                this.ocrStatus = 'Gagal memuat OCR. Coba refresh halaman.';
            }
        },

        async performOcr() {
            // Cek ketajaman dulu
            const videoEl = document.getElementById('scannerWebcam');
            const sharpness = this.computeSharpness(videoEl);
            this.isBlurry = sharpness < 150;

            if (this.isBlurry) {
                this.scanFailed = true;
                this.scanErrorMessage = 'Layar burem, fokuskan kartu agar terbaca';
                return;
            }

            this.scanFailed = false;
            this.scanErrorMessage = '';
            this.scanning = true;
            this.scanFailed = false;
            this.scanErrorMessage = '';

            // Ambil frame dari video ke canvas (upscale 2x untuk akurasi OCR)
            const videoEl = document.getElementById('scannerWebcam');
            const ocrCanvas = this.$refs.ocrCanvas;
            const w = videoEl.videoWidth * 2;
            const h = videoEl.videoHeight * 2;
            ocrCanvas.width = w;
            ocrCanvas.height = h;
            const ctx = ocrCanvas.getContext('2d');
            ctx.drawImage(videoEl, 0, 0, w, h);

            // Inisialisasi OCR worker jika belum
            await this.initOcrWorker();
            if (!this.ocrWorker) {
                this.scanFailed = true;
                this.scanErrorMessage = 'Mesin OCR gagal dimuat. Coba refresh halaman.';
                this.scanning = false;
                return;
            }

            this.ocrStatus = 'Membaca nama...';
            this.scanning = true;
            this.scanFailed = false;
            this.scanErrorMessage = '';

            try {
                const { data: { text } } = await this.ocrWorker.recognize(this.$refs.ocrCanvas, {
                    rectangle: { top: 0, left: 0, width: this.$refs.ocrCanvas.width, height: this.$refs.ocrCanvas.height }
                });

                const name = text.trim().replace(/\s+/g, ' ');
                console.log('OCR result:', name);

                if (!name || name.length < 3) {
                    this.scanFailed = true;
                    this.scanErrorMessage = 'Nama tidak terbaca. Coba lagi dengan pencahayaan lebih baik.';
                    this.scanning = false;
                    return;
                }

                // Kirim ke server
                const res = await fetch('{{ route('scan.post') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ code: name })
                });

                const data = await res.json();

                this.scanning = false;

                if (data.success) {
                    this.scanned = true;
                    this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
                    setTimeout(() => {
                        window.location.href = data.redirect || '{{ route('dashboard.siswa') }}';
                    }, 900);
                } else {
                    this.scanFailed = true;
                    this.scanErrorMessage = data.message || 'Scan gagal. Coba lagi.';
                    this.scanned = false;
                }
            } catch (e) {
                console.error('OCR/Scan error:', e);
                this.scanning = false;
                this.scanFailed = true;
                this.scanErrorMessage = 'Terjadi kesalahan saat membaca kartu. Coba lagi.';
            }
        },

        triggerScanSuccess() {
            if (this.scanning || this.scanned) return;
            this.performOcr();
        },
        playBeep() {
            try {
                const AudioCtx =
                    window.AudioContext ||
                    window.webkitAudioContext;

                if (!AudioCtx) return;

                const ctx = new AudioCtx();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.type = 'sine';
                osc.frequency.value = 920;

                gain.gain.setValueAtTime(
                    0.12,
                    ctx.currentTime
                );

                gain.gain.exponentialRampToValueAtTime(
                    0.01,
                    ctx.currentTime + 0.16
                );

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start();
                osc.stop(ctx.currentTime + 0.16);
            } catch(e) {}
        },

        captureSnapshot() {
            const videoEl =
                document.getElementById('scannerWebcam');

            const canvas =
                document.getElementById('scannerCanvas');

            if (
                videoEl &&
                canvas &&
                this.cameraActive
            ) {
                try {
                    canvas.width =
                        videoEl.videoWidth || 640;

                    canvas.height =
                        videoEl.videoHeight || 480;

                    const ctx =
                        canvas.getContext('2d');

                    ctx.drawImage(
                        videoEl,
                        0,
                        0,
                        canvas.width,
                        canvas.height
                    );

                    this.capturedPhoto =
                        canvas.toDataURL(
                            'image/jpeg',
                            0.85
                        );
                } catch(e) {
                    console.warn(e);
                }
            }
        },

        triggerScanSuccess(cardCode = null) {
            if (this.scanning || this.scanned) return;

            const code = cardCode || this.lastDetectedCode;
            if (!code) {
                this.scanFailed = true;
                this.scanErrorMessage = 'Tidak ada data kartu terbaca. Coba lagi.';
                return;
            }

            this.scanning = true;
            this.scanFailed = false;
            this.scanErrorMessage = '';
            this.captureSnapshot();
            this.playBeep();

            fetch('{{ route('scan.post') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ code })
            })
            .then(r => r.json())
            .then(data => {
                this.scanning = false;
                if (data.success) {
                    this.scanned = true;
                    this.stopCamera();
                    this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
                    setTimeout(() => {
                        window.location.href = data.redirect || '{{ route('dashboard.siswa') }}';
                    }, 900);
                } else {
                    this.scanFailed = true;
                    this.scanErrorMessage = data.message || 'Scan gagal. Coba lagi.';
                    this.scanned = false;
                }
            })
            .catch(() => {
                this.scanning = false;
                this.scanFailed = true;
                this.scanErrorMessage = 'Terjadi kesalahan jaringan. Coba lagi.';
                this.scanned = false;
            });
        }
     }">

    <!-- Top Status Header -->
    <div class="flex items-center justify-between pb-2 border-b border-slate-100">

        <div class="flex items-center space-x-1.5">

            <span class="w-2 h-2 rounded-full"
                  :class="cameraActive
                      ? 'bg-emerald-500 animate-pulse'
                      : 'bg-amber-400'">
            </span>

            <span class="text-[11px] font-bold text-slate-600"
                  x-text="cameraActive
                      ? 'Kamera Aktif'
                      : 'Menyiapkan Kamera'">
                Kamera Aktif
            </span>

        </div>

        <!-- Toggle Camera -->
        <button type="button"
                @click="toggleFacingMode()"
                x-show="cameraActive"
                class="p-2 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer"
                title="Ganti Kamera (Depan / Belakang)">

            <i data-lucide="refresh-cw"
               class="w-4 h-4"></i>
        </button>

        <div x-show="!cameraActive"
             class="w-8">
        </div>
    </div>

    <!-- Header Title & School Brand -->
    <div>

        <div class="flex items-center justify-center mb-2">

            <img src="{{ asset('images/logo-smk.png') }}"
                 alt="Logo SMK TI Bali Global Badung"
                 class="h-14 sm:h-16 w-auto object-contain drop-shadow-sm">

        </div>

        <h2 class="text-xl sm:text-2xl font-black text-slate-900">
            SCAN KARTU PELAJAR
        </h2>

        <p class="text-xs text-slate-500 mt-0.5">
            Arahkan barcode atau QR kartu pelajar tepat ke kamera
        </p>

    </div>

    <!-- Scanner Viewfinder Box -->
    <div class="w-full aspect-square max-w-[300px] mx-auto rounded-3xl bg-slate-950 border-4 border-slate-900 relative p-3 flex items-center justify-center overflow-hidden shadow-2xl transition-all duration-300"
         :class="scanned
             ? 'ring-4 ring-emerald-400'
             : ''">

        <canvas id="sharpnessCanvas"
                class="hidden"></canvas>

        <!-- OCR Canvas (hidden, for text recognition) -->
        <canvas x-ref="ocrCanvas"
                class="hidden"></canvas>

        <!-- Real Webcam Video Stream -->
        <video id="scannerWebcam"
autoplay
                playsinline
                muted
                class="absolute inset-0 w-full h-full object-cover z-10 transition-opacity duration-300"
                :class="{
                    'opacity-100': cameraActive && !scanned,
                    'opacity-0 pointer-events-none': !cameraActive || scanned
                }"
                style="touch-action: none;"
                @loadedmetadata="setupFocus()">
        </video>

        <!-- Hidden Canvas -->
        <canvas id="scannerCanvas"
                class="hidden">
        </canvas>

        <!-- Snapshot Image -->
        <template x-if="scanned && capturedPhoto">

            <img :src="capturedPhoto"
                 alt="Hasil Scan Kartu"
                 class="absolute inset-0 w-full h-full object-cover z-10">

        </template>

        <!-- Fallback View -->
        <div x-show="!cameraActive && !scanned"
             class="absolute inset-0 flex flex-col items-center justify-center z-[5] bg-linear-to-b from-slate-900 to-black p-5 text-center space-y-3">

            <div class="w-16 h-16 rounded-full border-2 border-dashed border-cyan-400/50 flex items-center justify-center text-cyan-400 bg-cyan-950/30">

                <i data-lucide="camera"
                   class="w-8 h-8 animate-pulse">
                </i>

            </div>

            <div class="space-y-1 max-w-[220px]">

                <p class="text-xs text-white font-extrabold">
                    Kamera Siap Dibuka
                </p>

                <p class="text-[10px] text-slate-400 leading-relaxed"
                   x-text="errorMessage || 'Izinkan browser mengakses kamera untuk membaca kartu pelajar.'">
                </p>

            </div>

            <button type="button"
                    @click="startCamera()"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-[11px] rounded-xl shadow-lg shadow-blue-500/25 active:scale-95 transition-all cursor-pointer">

                Buka Kamera Sekarang

            </button>

        </div>

        <!-- Corner Scanner Brackets -->

        <div class="absolute top-3 left-3 w-8 h-8 border-t-4 border-l-4 rounded-tl-xl z-20 pointer-events-none transition-colors duration-300"
             :class="scanned
                 ? 'border-emerald-400'
                 : 'border-cyan-400'">
        </div>

        <div class="absolute top-3 right-3 w-8 h-8 border-t-4 border-r-4 rounded-tr-xl z-20 pointer-events-none transition-colors duration-300"
             :class="scanned
                 ? 'border-emerald-400'
                 : 'border-cyan-400'">
        </div>

        <div class="absolute bottom-3 left-3 w-8 h-8 border-b-4 border-l-4 rounded-bl-xl z-20 pointer-events-none transition-colors duration-300"
             :class="scanned
                 ? 'border-emerald-400'
                 : 'border-cyan-400'">
        </div>

        <div class="absolute bottom-3 right-3 w-8 h-8 border-b-4 border-r-4 rounded-br-xl z-20 pointer-events-none transition-colors duration-300"
             :class="scanned
                 ? 'border-emerald-400'
                 : 'border-cyan-400'">
        </div>

        <!-- Animated Laser Line -->
        <div x-show="cameraActive && !scanned"
             class="absolute left-4 right-4 h-0.5 bg-linear-to-r from-transparent via-cyan-400 to-transparent shadow-[0_0_15px_#22d3ee] animate-laser z-20 pointer-events-none">
        </div>

        <!-- Success Overlay -->
        <div x-show="scanned"
             x-cloak
             class="absolute inset-0 bg-emerald-950/70 backdrop-blur-[2px] flex flex-col items-center justify-center space-y-2 z-30 p-4 animate-in fade-in zoom-in-95 duration-200">

            <div class="w-16 h-16 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-xl shadow-emerald-500/40">

                <i data-lucide="check"
                   class="w-9 h-9 stroke-[3]">
                </i>

            </div>

            <div class="text-xs font-black text-emerald-200 uppercase tracking-wider">
                KARTU TERVERIFIKASI!
            </div>

            <div class="text-xs font-black text-white bg-black/40 px-3 py-1 rounded-full border border-white/20">
                Wahyu Pratama • XI PPLG 1
            </div>

        </div>

    </div>

    <!-- Petunjuk Arahkan Kartu -->
    <p class="text-[11px] text-slate-500 font-medium">
        Posisikan kartu pelajar di dalam bingkai pemindai kamera.
    </p>

    <!-- Peringatan Layar Burem -->
    <div x-show="cameraActive && isBlurry && !scanned && !scanning"
         class="w-full py-2 text-center text-amber-600 text-xs font-medium bg-amber-50 border border-amber-200 rounded-xl animate-pulse">
        <i data-lucide="alert-triangle" class="w-4 h-4 inline-block mr-1"></i>
        Layar burem, fokuskan kartu agar terbaca
    </div>

    <!-- Pesan Error Scan -->
    <div x-show="scanFailed"
         class="w-full py-2 text-center text-rose-600 text-xs font-medium bg-rose-50 border border-rose-200 rounded-xl"
         x-text="scanErrorMessage"></div>

    <!-- Tombol Scan Sekarang -->
    <button @click="triggerScanSuccess()"
            :disabled="scanning || scanned"
            class="w-full font-extrabold py-3.5 rounded-2xl shadow-lg flex items-center justify-center space-x-2 transition-all active:scale-[0.98] cursor-pointer"
            :class="scanned
                ? 'bg-emerald-600 text-white shadow-emerald-500/25'
                : (scanning
                    ? 'bg-blue-500 text-white cursor-wait'
                    : 'bg-blue-600 hover:bg-blue-700 text-white shadow-blue-500/25')">

        <template x-if="!scanning && !scanned">

            <span class="flex items-center space-x-2">

                <i data-lucide="scan"
                   class="w-4 h-4">
                </i>

                <span>
                    PINDAI KARTU SEKARANG
                </span>

            </span>

        </template>

        <template x-if="scanning">

            <span class="flex items-center space-x-2">

                <svg class="animate-spin h-4 w-4 text-white"
                     fill="none"
                     viewBox="0 0 24 24">

                    <circle class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4">
                    </circle>

                    <path class="opacity-75"
                          fill="currentColor"
                          d="M4 12a8 8 0 018-8v8H4z">
                    </path>

                </svg>

                <span>
                    MEMINDAI KARTU PELAJAR...
                </span>

            </span>

        </template>

        <template x-if="scanned">

            <span class="flex items-center space-x-2">

                <i data-lucide="check-circle"
                   class="w-4 h-4">
                </i>

                <span>
                    BERHASIL! MENGALIHKAN...
                </span>

            </span>

        </template>

    </button>

    <!-- Divider "ATAU" -->
    <div class="flex items-center justify-center my-1">

        <span class="text-xs font-bold text-slate-400 uppercase px-4 bg-white">
            ATAU
        </span>

    </div>

    <!-- Tombol Login Manual -->
    <a href="{{ route('login') }}"
       class="w-full bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 font-extrabold py-3 rounded-2xl flex items-center justify-center space-x-2 transition-all text-xs block text-center">

        <i data-lucide="user-check"
           class="w-4 h-4 inline-block mr-1">
        </i>

        <span>
            MASUK DENGAN AKUN
        </span>

    </a>

</div>
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
@endsection