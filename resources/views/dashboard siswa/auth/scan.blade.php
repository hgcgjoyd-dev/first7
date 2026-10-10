@extends('layouts.auth')

@section('title', 'Scan Kartu Pelajar')

@section('content')
<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-soft text-center space-y-5"
     x-data="cardScanner()">

    <!-- Top Status Header -->
    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
        <div class="flex items-center space-x-2">
            <span class="w-2.5 h-2.5 rounded-full transition-colors"
                  :class="cameraActive ? 'bg-emerald-500 animate-pulse' : (cameraError ? 'bg-rose-500' : 'bg-amber-400')">
            </span>
            <span class="text-xs font-bold text-slate-600"
                  x-text="cameraActive ? 'Kamera Pemindai Siap' : (cameraError ? 'Kamera Tidak Tersedia' : 'Menyiapkan Kamera...')">
                Menyiapkan Kamera...
            </span>
        </div>

        <!-- Toggle Front/Back Camera -->
        <button type="button"
                @click="toggleFacingMode()"
                x-show="cameraActive"
                class="p-2 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer"
                title="Ganti Kamera Depan / Belakang">
            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
        </button>
    </div>

    <!-- Header Title & School Brand -->
    <div>
        <div class="flex items-center justify-center mb-2">
            <img src="{{ asset('images/logo-smk.png') }}"
                 alt="Logo SMK TI Bali Global Badung"
                 class="h-14 sm:h-16 w-auto object-contain drop-shadow-sm">
        </div>

        <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
            SCAN KARTU PELAJAR
        </h2>
        <p class="text-xs text-slate-500 mt-1">
            Posisikan kartu pelajar di dalam bingkai, lalu tekan tombol pindai di bawah
        </p>
    </div>

    <!-- Scanner Viewfinder Box -->
    <div class="w-full aspect-square max-w-[310px] mx-auto rounded-3xl bg-slate-950 border-4 border-slate-900 relative p-3 flex items-center justify-center overflow-hidden shadow-2xl transition-all duration-300"
         :class="scanned ? 'ring-4 ring-emerald-400' : ''">

        <!-- Hidden canvas for image capture & processing -->
        <canvas id="scannerCanvas" class="hidden"></canvas>

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

        <!-- Snapshot Image (Displayed upon successful scan) -->
        <template x-if="scanned && capturedPhoto">
            <img :src="capturedPhoto"
                 alt="Hasil Scan Kartu"
                 class="absolute inset-0 w-full h-full object-cover z-10">
        </template>

        <!-- Fallback View When Camera Inactive -->
        <div x-show="!cameraActive && !scanned"
             class="absolute inset-0 flex flex-col items-center justify-center z-[5] bg-gradient-to-b from-slate-900 to-black p-5 text-center space-y-3">
            <div class="w-16 h-16 rounded-full border-2 border-dashed border-cyan-400/50 flex items-center justify-center text-cyan-400 bg-cyan-950/30">
                <i data-lucide="camera" class="w-8 h-8 animate-pulse"></i>
            </div>

            <div class="space-y-1 max-w-[230px]">
                <p class="text-xs text-white font-extrabold">
                    Kamera Belum Terbuka
                </p>
                <p class="text-[11px] text-slate-400 leading-relaxed"
                   x-text="errorMessage || 'Izinkan akses kamera browser Anda untuk memindai kartu pelajar secara langsung.'">
                </p>
            </div>

            <button type="button"
                    @click="startCamera()"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs rounded-xl shadow-lg shadow-blue-500/25 active:scale-95 transition-all cursor-pointer">
                Buka Kamera Sekarang
            </button>
        </div>

        <!-- Corner Scanner Brackets -->
        <div class="absolute top-3 left-3 w-8 h-8 border-t-4 border-l-4 rounded-tl-xl z-20 pointer-events-none transition-colors duration-300"
             :class="scanned ? 'border-emerald-400' : 'border-cyan-400'">
        </div>
        <div class="absolute top-3 right-3 w-8 h-8 border-t-4 border-r-4 rounded-tr-xl z-20 pointer-events-none transition-colors duration-300"
             :class="scanned ? 'border-emerald-400' : 'border-cyan-400'">
        </div>
        <div class="absolute bottom-3 left-3 w-8 h-8 border-b-4 border-l-4 rounded-bl-xl z-20 pointer-events-none transition-colors duration-300"
             :class="scanned ? 'border-emerald-400' : 'border-cyan-400'">
        </div>
        <div class="absolute bottom-3 right-3 w-8 h-8 border-b-4 border-r-4 rounded-br-xl z-20 pointer-events-none transition-colors duration-300"
             :class="scanned ? 'border-emerald-400' : 'border-cyan-400'">
        </div>

        <!-- Animated Laser Line -->
        <div x-show="cameraActive && !scanned"
             class="absolute left-4 right-4 h-0.5 bg-gradient-to-r from-transparent via-cyan-400 to-transparent shadow-[0_0_15px_#22d3ee] animate-laser z-20 pointer-events-none">
        </div>

        <!-- Quick Camera Shutter Button inside Viewfinder (Bottom Center) -->
        <div x-show="cameraActive && !scanned && !scanning"
             class="absolute bottom-4 inset-x-0 flex justify-center z-25">
            <button type="button"
                    @click="triggerScan()"
                    class="w-14 h-14 rounded-full bg-white/90 hover:bg-white text-blue-600 border-4 border-blue-500 flex items-center justify-center shadow-2xl active:scale-90 transition-transform cursor-pointer group"
                    title="Ambil Foto & Pindai Kartu">
                <i data-lucide="camera" class="w-6 h-6 text-blue-600 group-hover:scale-110 transition-transform"></i>
            </button>
        </div>

        <!-- Success Overlay -->
        <div x-show="scanned"
             x-cloak
             class="absolute inset-0 bg-emerald-950/85 backdrop-blur-xs flex flex-col items-center justify-center space-y-2 z-30 p-4 animate-in fade-in zoom-in-95 duration-200 text-center">
            <div class="w-16 h-16 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-xl shadow-emerald-500/40">
                <i data-lucide="check" class="w-9 h-9 stroke-[3]"></i>
            </div>
            <div class="text-xs font-black text-emerald-200 uppercase tracking-wider">
                KARTU TERVERIFIKASI!
            </div>
            <div class="text-xs font-black text-white bg-black/40 px-3.5 py-1.5 rounded-full border border-white/20">
                <span x-text="scannedStudentName || 'Siswa Terdaftar'"></span>
                <span x-show="scannedStudentClass" x-text="' • ' + scannedStudentClass"></span>
            </div>
            <div x-show="scannedStudentNis" class="text-[11px] text-emerald-300 font-mono font-bold" x-text="scannedStudentNis"></div>
        </div>
    </div>

    <!-- Petunjuk Arahkan Kartu -->
    <p class="text-[11px] text-slate-500 font-medium">
        Arahkan barcode atau nama kartu tepat ke dalam bingkai pemindai kamera.
    </p>

    <!-- Status Saat Memproses Scan -->
    <div x-show="scanning"
         x-cloak
         class="w-full py-2.5 px-3 text-center text-blue-700 text-xs font-bold bg-blue-50 border border-blue-200 rounded-xl flex items-center justify-center space-x-2 animate-pulse">
        <svg class="animate-spin h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
        <span x-text="scanStatusMessage || 'Memproses kartu pelajar...'"></span>
    </div>

    <!-- Pesan Error Scan -->
    <div x-show="scanFailed"
         x-cloak
         class="w-full py-2.5 px-3 text-center text-rose-600 text-xs font-semibold bg-rose-50 border border-rose-200 rounded-xl leading-relaxed"
         x-text="scanErrorMessage">
    </div>

    <!-- TOMBOL UTAMA: PINDAI KARTU SEKARANG -->
    <button type="button"
            @click="triggerScan()"
            :disabled="scanning || scanned"
            class="w-full font-black py-4 px-6 rounded-2xl shadow-xl flex items-center justify-center space-x-3 transition-all active:scale-[0.98] cursor-pointer"
            :class="scanned
                ? 'bg-emerald-600 text-white shadow-emerald-500/25 cursor-default'
                : (scanning
                    ? 'bg-blue-500 text-white cursor-wait opacity-90'
                    : 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-blue-500/30 ring-4 ring-blue-500/20')">

        <!-- Tampilan Normal -->
        <span x-show="!scanning && !scanned" class="flex items-center space-x-2 text-sm sm:text-base tracking-wide uppercase">
            <i data-lucide="scan" class="w-5 h-5"></i>
            <span>PINDAI KARTU SEKARANG</span>
        </span>

        <!-- Tampilan Saat Memindai -->
        <span x-show="scanning" x-cloak class="flex items-center space-x-2 text-sm sm:text-base">
            <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span x-text="scanStatusMessage || 'MEMINDAI KARTU PELAJAR...'"></span>
        </span>

        <!-- Tampilan Berhasil -->
        <span x-show="scanned" x-cloak class="flex items-center space-x-2 text-sm sm:text-base">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            <span>BERHASIL! MENGALIHKAN...</span>
        </span>
    </button>

    <!-- Opsi Alternatif: Unggah Foto atau Ketik Nomor Manual -->
    <div class="pt-1">
        <div class="grid grid-cols-2 gap-2 text-xs">
            <!-- Tombol Unggah Gambar Kartu -->
            <label class="w-full bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 font-bold py-2.5 px-3 rounded-xl flex items-center justify-center space-x-1.5 transition-colors cursor-pointer text-center">
                <i data-lucide="image" class="w-4 h-4 text-slate-500"></i>
                <span>Unggah Foto Kartu</span>
                <input type="file" accept="image/*" class="hidden" @change="handleFileUpload($event)">
            </label>

            <!-- Toggle Input Nomor Manual -->
            <button type="button"
                    @click="showManualInput = !showManualInput"
                    class="w-full bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 font-bold py-2.5 px-3 rounded-xl flex items-center justify-center space-x-1.5 transition-colors cursor-pointer text-center">
                <i data-lucide="keypad" class="w-4 h-4 text-slate-500"></i>
                <span>Input No. Kartu</span>
            </button>
        </div>

        <!-- Form Input Nomor Siswa / NIS Manual -->
        <div x-show="showManualInput"
             x-cloak
             x-transition
             class="mt-3 p-4 bg-slate-50 border border-slate-200 rounded-2xl text-left space-y-2.5">
            <label for="manualCardCode" class="block text-xs font-bold text-slate-700">
                Nomor Siswa (NIS) atau Nama Kartu:
            </label>
            <div class="flex space-x-2">
                <input id="manualCardCode"
                       type="text"
                       x-model="manualCode"
                       @keydown.enter.prevent="submitManual()"
                       placeholder="Contoh: 0000001 atau Andhika Maraville"
                       class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="button"
                        @click="submitManual()"
                        :disabled="scanning"
                        class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs rounded-xl shadow-md transition-colors shrink-0 cursor-pointer">
                    Kirim
                </button>
            </div>
            <p class="text-[10px] text-slate-400">
                Masukkan nomor siswa (NIS) atau nama siswa sesuai kartu pelajar.
            </p>
        </div>
    </div>

    <!-- Divider "ATAU" -->
    <div class="flex items-center justify-center my-1">
        <span class="text-xs font-bold text-slate-400 uppercase px-4 bg-white">
            ATAU
        </span>
    </div>

    <!-- Tombol Login Manual -->
    <a href="{{ route('login') }}"
       class="w-full bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 font-extrabold py-3.5 rounded-2xl flex items-center justify-center space-x-2 transition-all text-xs block text-center">
        <i data-lucide="user-check" class="w-4 h-4 inline-block mr-1"></i>
        <span>MASUK DENGAN AKUN (NIS & PASSWORD)</span>
    </a>

</div>

<!-- Tesseract OCR Library for Card Text Reading -->
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>

<script>
function cardScanner() {
    return {
        cameraActive: false,
        cameraError: false,
        errorMessage: '',
        webcamStream: null,
        facingMode: 'environment',
        scanning: false,
        scanned: false,
        scanFailed: false,
        scanErrorMessage: '',
        scanStatusMessage: '',
        isBlurry: false,
        capturedPhoto: null,
        scannedStudentName: '',
        scannedStudentNis: '',
        scannedStudentClass: '',
        showManualInput: false,
        manualCode: '',
        barcodeDetector: null,
        autoScanInterval: null,
        ocrWorker: null,
        ocrLoading: false,
        seededStudents: @json(array_values(\Database\Seeders\UserSeeder::STUDENTS)),

        init() {
            this.$nextTick(() => {
                this.initBarcodeDetector();
                this.startCamera();
                this.initOcr();
                if (window.lucide) lucide.createIcons();
            });
        },

        async initOcr() {
            if (!this.ocrWorker && window.Tesseract) {
                try {
                    this.ocrLoading = true;
                    this.ocrWorker = await Tesseract.createWorker('eng', 1, {
                        cacheMethod: 'write'
                    });
                    this.ocrLoading = false;
                } catch (e) {
                    console.warn('OCR Preload error:', e);
                    this.ocrLoading = false;
                }
            }
        },

        initBarcodeDetector() {
            if ('BarcodeDetector' in window) {
                try {
                    this.barcodeDetector = new BarcodeDetector({
                        formats: ['code_128', 'code_39', 'ean_13', 'qr_code', 'upc_a']
                    });
                } catch (e) {
                    console.warn('BarcodeDetector error:', e);
                }
            }
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
                            this.startAutoDetection();
                        };
                    }
                })
                .catch(err => {
                    console.warn('Camera access error:', err);

                    if (this.facingMode === 'environment') {
                        this.facingMode = 'user';
                        this.startCamera();
                        return;
                    }

                    this.cameraActive = false;
                    this.cameraError = true;
                    this.errorMessage = 'Izin kamera belum aktif. Berikan izin di browser atau gunakan input nomor manual.';
                });
            } else {
                this.cameraActive = false;
                this.cameraError = true;
                this.errorMessage = 'Browser ini tidak mendukung akses kamera langsung. Silakan gunakan opsi input nomor kartu manual.';
            }
        },

        toggleFacingMode() {
            this.facingMode = (this.facingMode === 'environment') ? 'user' : 'environment';
            this.startCamera();
        },

        stopCamera() {
            if (this.autoScanInterval) {
                clearInterval(this.autoScanInterval);
                this.autoScanInterval = null;
            }

            if (this.webcamStream) {
                this.webcamStream.getTracks().forEach(t => t.stop());
                this.webcamStream = null;
            }

            this.cameraActive = false;
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

        startAutoDetection() {
            if (this.autoScanInterval) clearInterval(this.autoScanInterval);
            if (!this.barcodeDetector) return;

            this.autoScanInterval = setInterval(async () => {
                if (!this.cameraActive || this.scanning || this.scanned) return;
                const videoEl = document.getElementById('scannerWebcam');
                if (!videoEl || videoEl.readyState < 2) return;

                try {
                    const barcodes = await this.barcodeDetector.detect(videoEl);
                    if (barcodes && barcodes.length > 0) {
                        const code = barcodes[0].rawValue?.trim();
                        if (code) {
                            this.processCode(code);
                        }
                    }
                } catch (e) {}
            }, 450);
        },

        preprocessCanvas(sourceCanvas) {
            const w = sourceCanvas.width;
            const h = sourceCanvas.height;
            const pCanvas = document.createElement('canvas');
            pCanvas.width = w;
            pCanvas.height = h;
            const pctx = pCanvas.getContext('2d');
            pctx.drawImage(sourceCanvas, 0, 0);

            try {
                const imgData = pctx.getImageData(0, 0, w, h);
                const d = imgData.data;
                const contrast = 1.35;
                const factor = (259 * (contrast + 255)) / (255 * (259 - contrast));

                for (let i = 0; i < d.length; i += 4) {
                    const gray = 0.299 * d[i] + 0.587 * d[i + 1] + 0.114 * d[i + 2];
                    const adjusted = Math.min(255, Math.max(0, factor * (gray - 128) + 128));
                    d[i] = adjusted;
                    d[i + 1] = adjusted;
                    d[i + 2] = adjusted;
                }
                pctx.putImageData(imgData, 0, 0);
                return pCanvas;
            } catch (e) {
                return sourceCanvas;
            }
        },

        levenshtein(a, b) {
            if (a === b) return 0;
            if (a.length === 0) return b.length;
            if (b.length === 0) return a.length;

            const matrix = [];
            for (let i = 0; i <= b.length; i++) matrix[i] = [i];
            for (let j = 0; j <= a.length; j++) matrix[0][j] = j;

            for (let i = 1; i <= b.length; i++) {
                for (let j = 1; j <= a.length; j++) {
                    if (b.charAt(i - 1) === a.charAt(j - 1)) {
                        matrix[i][j] = matrix[i - 1][j - 1];
                    } else {
                        matrix[i][j] = Math.min(
                            matrix[i - 1][j - 1] + 1,
                            matrix[i][j - 1] + 1,
                            matrix[i - 1][j] + 1
                        );
                    }
                }
            }
            return matrix[b.length][a.length];
        },

        matchStudentName(rawText) {
            if (!rawText) return null;
            const clean = rawText.toLowerCase().replace(/[^a-z0-9\s]/g, ' ').replace(/\s+/g, ' ').trim();
            if (clean.length < 2) return null;

            const commonPrefixes = ['i', 'ni', 'gede', 'made', 'nyoman', 'ketut', 'kadek', 'komang', 'wayan', 'putu', 'gusti', 'ayu', 'bagus', 'dewa', 'ida'];

            // 1. Cek baris "Nama : [Nama Siswa]"
            const nameMatch = rawText.match(/(?:nama|name)\s*[:.\-]?\s*([a-zA-Z\s]{3,})/i);
            let extractedNameClean = '';
            if (nameMatch && nameMatch[1]) {
                extractedNameClean = nameMatch[1].toLowerCase().replace(/[^a-z0-9\s]/g, ' ').replace(/\s+/g, ' ').trim();
            }

            // 2. Cek kecocokan persis substring nama dari data seeder
            for (const student of this.seededStudents) {
                const sNorm = student.toLowerCase().replace(/[^a-z0-9\s]/g, ' ').replace(/\s+/g, ' ').trim();
                if (clean.includes(sNorm)) {
                    return { name: student, score: 1.0 };
                }
                if (extractedNameClean && (extractedNameClean.includes(sNorm) || sNorm.includes(extractedNameClean))) {
                    return { name: student, score: 0.95 };
                }
            }

            // 3. Pencocokan token / kata kunci fuzzy
            const inputWords = clean.split(' ').filter(w => w.length >= 2);
            let bestMatch = null;
            let bestScore = 0;

            for (const student of this.seededStudents) {
                const sNorm = student.toLowerCase().replace(/[^a-z0-9\s]/g, ' ').replace(/\s+/g, ' ').trim();
                const candWords = sNorm.split(' ').filter(w => w.length >= 2);
                if (candWords.length === 0) continue;

                let matched = 0;
                let distinctiveTotal = 0;
                let matchedDistinctive = 0;

                for (const cWord of candWords) {
                    const isPrefix = commonPrefixes.includes(cWord);
                    if (!isPrefix) distinctiveTotal++;

                    let wordMatched = false;
                    for (const iWord of inputWords) {
                        if (iWord === cWord) {
                            wordMatched = true;
                            break;
                        }
                        const maxLen = Math.max(iWord.length, cWord.length);
                        const maxDist = maxLen > 6 ? 2 : (maxLen > 3 ? 1 : 0);
                        if (this.levenshtein(iWord, cWord) <= maxDist) {
                            wordMatched = true;
                            break;
                        }
                    }

                    if (wordMatched) {
                        matched++;
                        if (!isPrefix) matchedDistinctive++;
                    }
                }

                const score = distinctiveTotal > 0
                    ? (matchedDistinctive / distinctiveTotal) * 0.7 + (matched / candWords.length) * 0.3
                    : matched / candWords.length;

                if (score > bestScore) {
                    bestScore = score;
                    bestMatch = student;
                }
            }

            if (bestMatch && bestScore >= 0.5) {
                return { name: bestMatch, score: bestScore };
            }

            return null;
        },

        async triggerScan() {
            if (this.scanning || this.scanned) return;

            const videoEl = document.getElementById('scannerWebcam');
            if (!this.cameraActive || !videoEl) {
                this.scanFailed = true;
                this.scanErrorMessage = 'Kamera belum aktif. Klik Buka Kamera atau gunakan input nomor kartu manual.';
                return;
            }

            this.scanning = true;
            this.scanFailed = false;
            this.scanErrorMessage = '';
            this.scanStatusMessage = 'Mengambil gambar kartu...';

            const canvas = document.getElementById('scannerCanvas') || document.createElement('canvas');
            const w = videoEl.videoWidth || 1280;
            const h = videoEl.videoHeight || 720;
            canvas.width = w;
            canvas.height = h;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(videoEl, 0, 0, w, h);

            try {
                this.capturedPhoto = canvas.toDataURL('image/jpeg', 0.85);
            } catch (e) {}

            const processedCanvas = this.preprocessCanvas(canvas);

            // 1. Coba Barcode/QR Detector
            if (this.barcodeDetector) {
                this.scanStatusMessage = 'Mencari Barcode/QR pada kartu...';
                try {
                    const barcodes = await this.barcodeDetector.detect(canvas);
                    if (barcodes && barcodes.length > 0) {
                        const code = barcodes[0].rawValue?.trim();
                        if (code) {
                            await this.processCode(code);
                            return;
                        }
                    }
                } catch (e) {
                    console.warn('Barcode error:', e);
                }
            }

            // 2. OCR Tesseract — Fokus Deteksi Nama Kartu Pelajar Cocokkan Data Seeders
            this.scanStatusMessage = 'Membaca nama di kartu pelajar...';
            try {
                if (!this.ocrWorker && window.Tesseract) {
                    this.scanStatusMessage = 'Menyiapkan modul pembaca kartu...';
                    this.ocrWorker = await Tesseract.createWorker('eng', 1, {
                        cacheMethod: 'write'
                    });
                }

                if (this.ocrWorker) {
                    this.scanStatusMessage = 'Mengenali teks nama kartu pelajar...';
                    const { data: { text } } = await this.ocrWorker.recognize(processedCanvas);
                    const rawText = text ? text.trim() : '';

                    // 1) PRIORITAS UTAMA: Cocokkan nama dengan data seeder siswa
                    const match = this.matchStudentName(rawText);
                    if (match && match.name) {
                        this.scanStatusMessage = `Nama terdeteksi: ${match.name}! Memverifikasi...`;
                        await this.processCode(match.name, rawText);
                        return;
                    }

                    // 2) Jika tidak cocok nama seeder, coba cek digit NIS 7-digit
                    const numMatch = rawText.match(/\b\d{7}\b/) || rawText.match(/\b\d{5,10}\b/);
                    if (numMatch) {
                        await this.processCode(numMatch[0], rawText);
                        return;
                    }

                    // 3) Kirim teks OCR yang terbaca ke backend untuk pencocokan multi-tier
                    if (rawText.length >= 3) {
                        await this.processCode(rawText, rawText);
                        return;
                    }
                }
            } catch (ocrErr) {
                console.warn('OCR error:', ocrErr);
            }

            this.scanning = false;
            this.scanFailed = true;
            this.scanErrorMessage = 'Kartu belum terbaca jelas. Posisikan nama di kartu lebih terang & dekat, atau ketik nama kartu manual di bawah.';
        },

        async processCode(code, rawText = '') {
            if (!code || this.scanned) return;

            this.scanning = true;
            this.scanFailed = false;
            this.scanErrorMessage = '';
            this.scanStatusMessage = 'Memverifikasi data kartu ke sistem...';
            this.playBeep();

            try {
                const res = await fetch('{{ route('scan.post') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        code: code.trim(),
                        raw_text: (rawText || '').trim()
                    })
                });

                const data = await res.json();
                this.scanning = false;

                if (data.success) {
                    this.scanned = true;
                    this.scannedStudentName = data.siswa?.nama || 'Siswa Terdaftar';
                    this.scannedStudentNis = data.siswa?.no_siswa ? `NIS: ${data.siswa.no_siswa}` : '';
                    this.scannedStudentClass = data.siswa?.kelas || 'SMK TI Bali Global';
                    this.stopCamera();

                    this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });

                    setTimeout(() => {
                        window.location.href = data.redirect || '{{ route('dashboard.siswa') }}';
                    }, 1100);
                } else {
                    this.scanFailed = true;
                    this.scanErrorMessage = data.message || 'Nama kartu belum cocok dengan data siswa.';
                    this.scanned = false;
                }
            } catch (err) {
                console.error('Fetch error:', err);
                this.scanning = false;
                this.scanFailed = true;
                this.scanErrorMessage = 'Gagal memproses kartu. Periksa koneksi internet Anda.';
                this.scanned = false;
            }
        },

        submitManual() {
            const input = this.manualCode.trim();
            if (!input) {
                this.scanFailed = true;
                this.scanErrorMessage = 'Ketik nomor siswa atau nama lengkap Anda terlebih dahulu.';
                return;
            }
            const match = this.matchStudentName(input);
            const finalCode = match ? match.name : input;
            this.processCode(finalCode, input);
        },

        handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.scanning = true;
            this.scanFailed = false;
            this.scanErrorMessage = '';
            this.scanStatusMessage = 'Membaca foto kartu yang diunggah...';

            const reader = new FileReader();
            reader.onload = async (e) => {
                const img = new Image();
                img.onload = async () => {
                    const canvas = document.createElement('canvas');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0);
                    this.capturedPhoto = canvas.toDataURL('image/jpeg', 0.85);

                    if (this.barcodeDetector) {
                        try {
                            const barcodes = await this.barcodeDetector.detect(canvas);
                            if (barcodes && barcodes.length > 0) {
                                const code = barcodes[0].rawValue?.trim();
                                if (code) {
                                    await this.processCode(code);
                                    return;
                                }
                            }
                        } catch (e) {}
                    }

                    const processedCanvas = this.preprocessCanvas(canvas);

                    try {
                        if (!this.ocrWorker && window.Tesseract) {
                            this.ocrWorker = await Tesseract.createWorker('eng', 1);
                        }
                        if (this.ocrWorker) {
                            const { data: { text } } = await this.ocrWorker.recognize(processedCanvas);
                            const cleaned = text ? text.trim() : '';

                            // 1) Prioritas Nama Seeder
                            const match = this.matchStudentName(cleaned);
                            if (match && match.name) {
                                await this.processCode(match.name, cleaned);
                                return;
                            }

                            // 2) Nomor NIS
                            const num = cleaned.match(/\b\d{7}\b/) || cleaned.match(/\b\d{5,10}\b/);
                            if (num) {
                                await this.processCode(num[0], cleaned);
                                return;
                            }

                            if (cleaned.length >= 3) {
                                await this.processCode(cleaned, cleaned);
                                return;
                            }
                        }
                    } catch (e) {}

                    this.scanning = false;
                    this.scanFailed = true;
                    this.scanErrorMessage = 'Foto kartu tidak dapat terbaca. Gunakan foto yang lebih tajam atau ketik nama kartu manual.';
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        },

        playBeep() {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.type = 'sine';
                osc.frequency.value = 880;
                gain.gain.setValueAtTime(0.15, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.18);

                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.18);
            } catch (e) {}
        }
    };
}
</script>
@endsection