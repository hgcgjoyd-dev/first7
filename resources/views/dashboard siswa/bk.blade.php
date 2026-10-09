@extends('layouts.app')

@section('title', 'Bimbingan Konseling & Disiplin')

@section('content')
<div class="w-full space-y-6" x-data="{
    uploadedFile: null,
    uploadedFileName: '',
    isUploading: false,
    taskSubmitted: false,
    showToast: false,
    toastMessage: '',

    getGreeting() {
        const hr = new Date().getHours();
        if (hr >= 4 && hr < 11) return 'Selamat Pagi,';
        if (hr >= 11 && hr < 15) return 'Selamat Siang,';
        if (hr >= 15 && hr < 18) return 'Selamat Sore,';
        return 'Selamat Malam,';
    },

    handleFileSelect(e) {
        const file = e.target.files[0];
        if (file) {
            this.uploadedFile = file;
            this.uploadedFileName = file.name;
        }
    },

    submitBkTask() {
        if (!this.uploadedFile && !this.uploadedFileName) {
            alert('Silakan pilih atau unggah foto/file berkas sanksi terlebih dahulu.');
            return;
        }
        this.isUploading = true;
        setTimeout(() => {
            this.isUploading = false;
            this.taskSubmitted = true;
            this.toastMessage = 'Tugas pembinaan wajib berhasil dikirimkan ke Guru BK (Dra. Ni Luh Suastini, S.Pd). Poin sanksi akan diverifikasi!';
            this.showToast = true;
            this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
            setTimeout(() => { this.showToast = false; }, 6000);
        }, 900);
    }
}">

    <!-- ========================================================================= -->
    <!-- 1. TOP HEADER HERO (Identical Across Mapel, BK, Piket)                     -->
    <!-- ========================================================================= -->
    <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 text-white rounded-3xl sm:rounded-[32px] p-5 sm:p-7 shadow-lg shadow-blue-500/15 relative overflow-hidden">
        <!-- Ambient decorative shapes -->
        <div class="absolute -top-16 -right-16 w-56 h-56 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-12 -left-12 w-48 h-48 rounded-full bg-indigo-500/20 blur-xl pointer-events-none"></div>

        <!-- Top Navigation Row: Back Button, School Brand, Notification Bell -->
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

            <div class="relative">
                <button type="button" 
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white/15 hover:bg-white/25 backdrop-blur-md flex items-center justify-center text-white transition-all active:scale-95 shadow-sm"
                        title="Pemberitahuan BK">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-blue-600"></span>
                </button>
            </div>
        </div>

        <!-- Middle Row: Greeting & Name on Left; White Circular Avatar on Right -->
        <div class="relative z-10 flex items-center justify-between pt-4 sm:pt-5">
            <div>
                <p class="text-xs sm:text-sm text-blue-100 font-medium" x-text="getGreeting()">Selamat Pagi,</p>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white leading-tight mt-0.5">
                    {{ $siswa->nama ?? 'Nama Siswa' }}
                </h1>
                <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-blue-500/50 sm:bg-white/20 text-[11px] sm:text-xs font-semibold text-white backdrop-blur-md mt-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>Kelas {{ $siswa->kelas ?? 'XI PPLG 1' }} • NIS {{ $siswa->nis ?? '2026042' }}</span>
                </div>
            </div>

            <!-- Avatar -->
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-white text-blue-600 flex items-center justify-center shadow-lg ring-4 ring-white/25 shrink-0">
                <i data-lucide="user" class="w-8 h-8 sm:w-9 sm:h-9"></i>
            </div>
        </div>

        <!-- Bottom Row: Segmented 3-Pill Navigation: Mapel, Tugas BK (ACTIVE), Piket -->
        <div class="relative z-10 mt-5 bg-blue-900/60 backdrop-blur-md p-1.5 rounded-full flex items-center gap-1 border border-white/15 shadow-inner">
            <a href="{{ route('mapel') }}" 
               class="flex-1 flex items-center justify-center space-x-1.5 sm:space-x-2 py-2 px-3 text-xs sm:text-sm font-bold text-white/80 hover:text-white hover:bg-white/10 transition-all rounded-full">
                <i data-lucide="book-open" class="w-4 h-4"></i>
                <span>Mapel</span>
            </a>

            <!-- Tab 2: Tugas BK (ACTIVE) -->
            <div class="flex-1 flex items-center justify-center space-x-1.5 sm:space-x-2 py-2 px-3 text-xs sm:text-sm font-extrabold bg-white text-slate-900 rounded-full shadow-md">
                <i data-lucide="user-check" class="w-4 h-4 text-rose-600"></i>
                <span>Tugas BK</span>
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
            </div>

            <a href="{{ route('piket') }}" 
               class="flex-1 flex items-center justify-center space-x-1.5 sm:space-x-2 py-2 px-3 text-xs sm:text-sm font-bold text-white/80 hover:text-white hover:bg-white/10 transition-all rounded-full">
                <i data-lucide="sparkles" class="w-4 h-4"></i>
                <span>Piket</span>
            </a>
        </div>
    </div>

    <!-- TOAST NOTIFICATION ON SUBMIT -->
    <div x-show="showToast" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-3"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-3"
         class="p-4 rounded-2xl bg-emerald-600 text-white shadow-xl shadow-emerald-600/25 border border-emerald-500 flex items-start justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-9 h-9 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center shrink-0">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-white"></i>
            </div>
            <div>
                <h5 class="font-extrabold text-sm">Pengumpulan Terverifikasi!</h5>
                <p class="text-xs text-emerald-100 mt-0.5" x-text="toastMessage"></p>
            </div>
        </div>
        <button type="button" @click="showToast = false" class="text-emerald-200 hover:text-white p-1">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. MAIN RESPONSIVE CONTENT GRID (Mobile: 1 Column, Desktop: 12 Columns)   -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- ===================================================================== -->
        <!-- LEFT / MAIN STREAM (lg:col-span-8) Matching Reference Screenshot      -->
        <!-- ===================================================================== -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-4">

            <!-- ================================================================= -->
            <!-- CARD 1: BUKU DISIPLIN & PELANGGARAN - STATUS POIN TATA TERTIB     -->
            <!-- ================================================================= -->
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-soft space-y-3.5 hover:shadow-md transition-all">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start space-x-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center shrink-0 shadow-2xs">
                            <i data-lucide="triangle-alert" class="w-6 h-6 stroke-[2.2]"></i>
                        </div>
                        <div>
                            <span class="text-[11px] font-black text-slate-400 uppercase tracking-wider block">
                                BUKU DISIPLIN & PELANGGARAN
                            </span>
                            <h3 class="font-black text-sm sm:text-base text-slate-900 mt-0.5 leading-snug">
                                Status Poin Tata Tertib
                            </h3>
                        </div>
                    </div>

                    <div class="text-right shrink-0">
                        <span class="text-xl sm:text-2xl font-black text-rose-600">{{ $siswa->poin_bk ?? 15 }}</span>
                        <span class="text-xs font-bold text-slate-400">/ 30 Poin</span>
                    </div>
                </div>

                <!-- Progress Bar: 15 / 30 = 50% -->
                <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-500 via-rose-500 to-rose-600 h-full rounded-full transition-all duration-500" style="width: 50%;"></div>
                </div>

                <!-- Status Row -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <div class="flex items-center space-x-1.5 text-rose-600 font-bold">
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                        <span>Peringatan 1 (SP-1 Aktif)</span>
                    </div>
                    <span class="text-slate-400 font-medium">
                        Batas Surat Panggilan: 30 Poin
                    </span>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- CARD 2: TUGAS PEMBINAAN WAJIB (Card dengan aksen garis merah)     -->
            <!-- ================================================================= -->
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-soft relative overflow-hidden space-y-4 hover:shadow-md transition-all">
                <!-- Red Left Accent Line (matching reference) -->
                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-rose-500"></div>

                <!-- Badge Row -->
                <div class="flex items-center justify-between gap-3 pl-1">
                    <span class="bg-rose-50 text-rose-600 border border-rose-100 font-black text-[10px] sm:text-[11px] px-3 py-1 rounded-full uppercase tracking-wider flex items-center space-x-1.5">
                        <i data-lucide="pin" class="w-3.5 h-3.5"></i>
                        <span>TUGAS PEMBINAAN WAJIB</span>
                    </span>
                    <span class="border border-rose-200 text-rose-600 font-black text-xs px-3 py-0.5 rounded-full bg-rose-50/50">
                        Poin -10
                    </span>
                </div>

                <!-- Task Title -->
                <div class="pl-1">
                    <h3 class="text-base sm:text-lg font-black text-slate-900 leading-snug">
                        Resume Pedoman Tata Tertib & Refleksi Kedisiplinan
                    </h3>
                </div>

                <!-- Red Kasus Highlight Box -->
                <div class="bg-rose-50/70 border border-rose-100 rounded-2xl p-3 sm:p-3.5 flex items-center space-x-2.5 text-xs text-rose-700">
                    <div class="w-6 h-6 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    </div>
                    <p class="font-medium">
                        Kasus: <strong class="text-rose-900 font-black">Terlambat Masuk Sekolah 3x Berturut-turut</strong>
                    </p>
                </div>

                <!-- Instruction Gray Box -->
                <div class="bg-slate-50/90 border border-slate-100 rounded-2xl p-4 text-xs space-y-2.5">
                    <p class="font-black text-slate-800 uppercase tracking-wide text-[11px]">Instruksi Guru BK:</p>
                    <p class="text-slate-600 leading-relaxed text-[12px] sm:text-[13px]">
                        Tulis tangan resume <em>Bab III (Kedisiplinan Waktu & Sanksi)</em> minimal 2 lembar folio bergaris. Wajib ditandatangani oleh <strong>Orang Tua / Wali</strong> dan <strong>Wali Kelas</strong>, kemudian lampirkan foto fisiknya di bawah ini.
                    </p>
                    <div class="flex items-center space-x-2 text-slate-600 pt-1">
                        <i data-lucide="user-check" class="w-3.5 h-3.5 text-blue-600"></i>
                        <span class="font-bold text-[11px] text-slate-700">Dra. Ni Luh Suastini, S.Pd</span>
                    </div>
                </div>

                <!-- Deadline Row -->
                <div class="flex items-center justify-between text-xs pt-1 pl-1">
                    <span class="text-slate-500 font-semibold">Batas Pengumpulan:</span>
                    <span class="bg-rose-50 border border-rose-100 text-rose-600 font-black px-3 py-1 rounded-full flex items-center space-x-1.5 shadow-2xs">
                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                        <span>26 Sep 2026 • 12:00 WITA</span>
                    </span>
                </div>

                <!-- Upload Section (Dashed dropzone with camera icon) -->
                <div class="space-y-2 pt-1 pl-1">
                    <label class="font-extrabold text-xs text-slate-800 block">
                        Unggah Foto Berkas Sanksi (Folio & Tanda Tangan):
                    </label>

                    <label class="block border-2 border-dashed border-rose-200/90 hover:border-rose-300 bg-rose-50/20 hover:bg-rose-50/40 rounded-2xl p-6 text-center cursor-pointer transition-all">
                        <input type="file" @change="handleFileSelect($event)" accept="image/*,.pdf" class="hidden">
                        <div class="flex flex-col items-center justify-center space-y-2">
                            <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shadow-xs">
                                <i data-lucide="camera" class="w-6 h-6"></i>
                            </div>
                            <template x-if="!uploadedFileName">
                                <div class="space-y-1">
                                    <p class="font-extrabold text-xs sm:text-sm text-slate-800">Ambil Foto / Pilih File Bukti</p>
                                    <p class="text-[11px] text-slate-400 font-medium">Format: JPG, PNG, atau PDF (Maksimal 5MB)</p>
                                </div>
                            </template>
                            <template x-if="uploadedFileName">
                                <div class="space-y-1 text-center">
                                    <p class="font-extrabold text-xs text-emerald-700 flex items-center justify-center space-x-1">
                                        <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                                        <span x-text="uploadedFileName"></span>
                                    </p>
                                    <p class="text-[11px] text-blue-600 underline font-medium">Klik untuk mengganti berkas</p>
                                </div>
                            </template>
                        </div>
                    </label>
                </div>

                <!-- Red Submit Button -->
                <div class="pt-2 pl-1">
                    <template x-if="!taskSubmitted">
                        <button type="button" 
                                @click="submitBkTask()" 
                                :disabled="isUploading"
                                class="w-full py-3.5 bg-gradient-to-r from-rose-600 via-rose-600 to-rose-700 hover:from-rose-700 hover:to-rose-800 text-white font-extrabold text-xs sm:text-sm rounded-2xl shadow-lg shadow-rose-500/25 flex items-center justify-center space-x-2 active:scale-98 transition-all cursor-pointer">
                            <template x-if="!isUploading">
                                <span class="inline-flex items-center space-x-2">
                                    <i data-lucide="send" class="w-4 h-4"></i>
                                    <span>Kumpulkan Tugas ke Guru BK</span>
                                </span>
                            </template>
                            <template x-if="isUploading">
                                <span class="inline-flex items-center space-x-2">
                                    <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                                    <span>Mengunggah Berkas Fisik...</span>
                                </span>
                            </template>
                        </button>
                    </template>
                    <template x-if="taskSubmitted">
                        <div class="w-full py-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 font-extrabold text-xs sm:text-sm rounded-2xl flex items-center justify-center space-x-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                            <span>Tugas Pembinaan Berhasil Dikirimkan & Menunggu Validasi</span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- SECTION 3: RIWAYAT PENYELESAIAN SANKSI                           -->
            <!-- ================================================================= -->
            <div class="space-y-3 pt-2">
                <h4 class="text-xs font-black uppercase tracking-wider text-slate-400 px-1">
                    RIWAYAT PENYELESAIAN SANKSI
                </h4>

                <!-- Item 1: Literasi Karakter di Perpustakaan -->
                <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-soft flex items-center justify-between gap-3 hover:border-slate-300 transition-all">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 shadow-2xs">
                            <i data-lucide="check" class="w-5 h-5 stroke-[3]"></i>
                        </div>
                        <div>
                            <h5 class="font-extrabold text-xs sm:text-sm text-slate-900 leading-snug">
                                Literasi Karakter di Perpustakaan (45 Menit)
                            </h5>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                Diverifikasi 14 Agu 2026 • Guru: Bpk. Gede
                            </p>
                        </div>
                    </div>
                    <span class="border border-emerald-200 bg-emerald-50/60 text-emerald-700 font-black text-xs px-3 py-1 rounded-full shrink-0">
                        Poin -5
                    </span>
                </div>

                <!-- Item 2: Konseling Individual 1-on-1 Ruang BK -->
                <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-soft flex items-center justify-between gap-3 hover:border-slate-300 transition-all">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center shrink-0 shadow-2xs">
                            <i data-lucide="user-check" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h5 class="font-extrabold text-xs sm:text-sm text-slate-900 leading-snug">
                                Konseling Individual 1-on-1 Ruang BK
                            </h5>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                Selesai • Pembinaan Perilaku Disiplin
                            </p>
                        </div>
                    </div>
                    <span class="border border-blue-200 bg-blue-50/60 text-blue-700 font-extrabold text-xs px-3 py-1 rounded-full shrink-0">
                        Selesai
                    </span>
                </div>

                <!-- Item 3: Hubungi Guru BK via WhatsApp -->
                <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-soft flex items-center justify-between gap-3 hover:border-slate-300 transition-all">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-md shadow-emerald-500/25">
                            <i data-lucide="message-circle" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h5 class="font-extrabold text-xs sm:text-sm text-slate-900 leading-snug">
                                Hubungi Guru BK via WhatsApp
                            </h5>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                Klarifikasi status poin atau jadwal konsultasi
                            </p>
                        </div>
                    </div>
                    <a href="https://wa.me/6281234567890?text=Halo%20Bu%20Luh%2C%20saya%20Wahyu%20Pratama%20dari%20kelas%20XI%20PPLG%201%20ingin%20konsultasi%20poin%20kedisiplinan" 
                       target="_blank" 
                       rel="noopener" 
                       class="border border-emerald-400 bg-emerald-50/50 hover:bg-emerald-100 text-emerald-700 font-black text-xs px-4 py-2 rounded-full transition-all shrink-0 active:scale-95 shadow-2xs">
                        Chat WA
                    </a>
                </div>
            </div>

        </div>

        <!-- ===================================================================== -->
        <!-- RIGHT COLUMN (lg:col-span-4) - DESKTOP SIDEBAR WIDGETS                -->
        <!-- ===================================================================== -->
        <div class="lg:col-span-5 xl:col-span-4 space-y-5">

            <!-- Pedoman Poin Disiplin SMK TI -->
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-soft space-y-4">
                <div class="flex items-center space-x-2.5 pb-3 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                        <i data-lucide="shield-alert" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm text-slate-900">Pedoman Poin Disiplin</h4>
                        <p class="text-[11px] text-slate-500">Ketentuan resmi SMK TI Bali Global</p>
                    </div>
                </div>

                <div class="space-y-2.5 text-xs">
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <span class="text-slate-700 font-medium">Terlambat Masuk (> 07:15)</span>
                        <span class="font-black text-rose-600">+5 Poin</span>
                    </div>
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <span class="text-slate-700 font-medium">Atribut Tidak Lengkap</span>
                        <span class="font-black text-rose-600">+3 Poin</span>
                    </div>
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <span class="text-slate-700 font-medium">Alpa Tanpa Keterangan</span>
                        <span class="font-black text-rose-600">+10 Poin</span>
                    </div>
                    <div class="p-3 rounded-2xl bg-rose-50/70 border border-rose-200 flex items-center justify-between">
                        <span class="text-rose-900 font-bold">Ambang Batas SP-1</span>
                        <span class="font-black text-rose-700">30 Poin</span>
                    </div>
                </div>
            </div>

            <!-- Jadwal Pelayanan Guru BK -->
            <div class="bg-gradient-to-br from-slate-900 to-blue-950 text-white rounded-3xl p-5 sm:p-6 shadow-soft space-y-3.5">
                <div class="flex items-center space-x-2 text-blue-300 text-xs font-bold uppercase tracking-wider">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                    <span>Jadwal Ruang Konseling</span>
                </div>
                <h4 class="font-black text-base text-white">Layanan Konseling Siswa</h4>
                <p class="text-xs text-blue-200/90 leading-relaxed">
                    Ruang Bimbingan & Konseling (Gedung A, Lt 1) buka setiap hari kerja pukul <strong>07:30 - 15:30 WITA</strong>.
                </p>
                <div class="pt-2 border-t border-white/10 text-xs space-y-1 text-blue-100">
                    <p>• <strong>Dra. Ni Luh Suastini, S.Pd</strong> (Guru BK Kelas XI)</p>
                    <p>• <strong>I Wayan Sudarma, S.Pd</strong> (Koordinator BK)</p>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
