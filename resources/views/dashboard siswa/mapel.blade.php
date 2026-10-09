@extends('layouts.app')

@section('title', 'Tugas Mata Pelajaran')

@section('content')
<div class="w-full space-y-6" x-data="{
    showSubmitModal: false,
    showDetailModal: false,
    selectedTask: '',
    selectedSubject: '',
    selectedDeadline: '',
    submitLink: '',
    submitFile: null,
    submitFileName: '',
    isSubmitting: false,
    submittedTasks: {},
    showSuccessToast: false,

    getGreeting() {
        const hr = new Date().getHours();
        if (hr >= 4 && hr < 11) return 'Selamat Pagi,';
        if (hr >= 11 && hr < 15) return 'Selamat Siang,';
        if (hr >= 15 && hr < 18) return 'Selamat Sore,';
        return 'Selamat Malam,';
    },

    openSubmit(title, subject, deadline) {
        this.selectedTask = title;
        this.selectedSubject = subject;
        this.selectedDeadline = deadline;
        this.submitLink = '';
        this.submitFile = null;
        this.submitFileName = '';
        this.showSubmitModal = true;
        this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
    },

    openDetail(title, subject) {
        this.selectedTask = title;
        this.selectedSubject = subject;
        this.showDetailModal = true;
        this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
    },

    handleFileSelect(e) {
        const file = e.target.files[0];
        if (file) {
            this.submitFile = file;
            this.submitFileName = file.name;
        }
    },

    confirmSubmit() {
        if (!this.submitFile && !this.submitLink) {
            alert('Silakan unggah file tugas atau masukkan tautan repository/file terlebih dahulu.');
            return;
        }
        this.isSubmitting = true;
        setTimeout(() => {
            this.isSubmitting = false;
            this.submittedTasks[this.selectedTask] = true;
            this.showSubmitModal = false;
            this.showSuccessToast = true;
            this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
            setTimeout(() => { this.showSuccessToast = false; }, 5000);
        }, 800);
    }
}">

    <!-- ========================================================================= -->
    <!-- 1. TOP HEADER HERO (Matching Screenshot Reference)                        -->
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
                        title="Pemberitahuan Tugas">
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

        <!-- Bottom Row: Segmented 3-Pill Navigation: Mapel (Active), Tugas BK, Piket -->
        <div class="relative z-10 mt-5 bg-blue-900/60 backdrop-blur-md p-1.5 rounded-full flex items-center gap-1 border border-white/15 shadow-inner">
            <div class="flex-1 flex items-center justify-center space-x-1.5 sm:space-x-2 py-2 px-3 text-xs sm:text-sm font-extrabold bg-white text-blue-700 rounded-full shadow-md">
                <i data-lucide="book-open" class="w-4 h-4 text-blue-600"></i>
                <span>Mapel</span>
            </div>

            <a href="{{ route('bk') }}" 
               class="flex-1 flex items-center justify-center space-x-1.5 sm:space-x-2 py-2 px-3 text-xs sm:text-sm font-bold text-white/80 hover:text-white hover:bg-white/10 transition-all rounded-full">
                <i data-lucide="user-check" class="w-4 h-4 text-rose-300"></i>
                <span>Tugas BK</span>
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
            </a>

            <a href="{{ route('piket') }}" 
               class="flex-1 flex items-center justify-center space-x-1.5 sm:space-x-2 py-2 px-3 text-xs sm:text-sm font-bold text-white/80 hover:text-white hover:bg-white/10 transition-all rounded-full">
                <i data-lucide="sparkles" class="w-4 h-4"></i>
                <span>Piket</span>
            </a>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. MAIN RESPONSIVE CONTENT GRID (Mobile: 1 Column, Desktop: 12 Columns)   -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- ===================================================================== -->
        <!-- LEFT / MAIN COLUMN (lg:col-span-7 xl:col-span-8)                      -->
        <!-- ===================================================================== -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-4">

            <!-- Section Title Row: Icon + "TUGAS MATA PELAJARAN" + "2 Aktif" -->
            <div class="flex items-center justify-between px-1">
                <div class="flex items-center space-x-2">
                    <i data-lucide="layers" class="w-5 h-5 text-blue-600"></i>
                    <h3 class="font-extrabold text-sm sm:text-base text-slate-900 tracking-wide uppercase">
                        TUGAS MATA PELAJARAN
                    </h3>
                </div>
                <span class="bg-blue-100 text-blue-700 font-extrabold text-xs px-3 py-1 rounded-full shadow-2xs">
                    2 Aktif
                </span>
            </div>

            <!-- TASK CARD 1: PEMROGRAMAN WEB & MOBILE (Besok) -->
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-soft space-y-4 hover:shadow-md transition-all">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start space-x-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center shrink-0 shadow-2xs">
                            <i data-lucide="code-2" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <span class="text-[11px] font-black text-blue-600 uppercase tracking-wider block">
                                PEMROGRAMAN WEB & MOBILE
                            </span>
                            <h4 class="font-black text-sm sm:text-base text-slate-900 mt-0.5 leading-snug">
                                Slice UI Figma Dashboard ke HTML/CSS
                            </h4>
                        </div>
                    </div>
                    <span class="bg-rose-50 text-rose-600 border border-rose-100 font-black text-[10px] px-3 py-1 rounded-full uppercase tracking-wider shrink-0">
                        BESOK
                    </span>
                </div>

                <p class="text-xs sm:text-[13px] text-slate-600 leading-relaxed pl-0 sm:pl-1">
                    Implementasikan tata letak presisi mobile frame 414×896px dengan Tailwind CSS, tombol panah kiri header, serta kartu warna kehadiran.
                </p>

                <!-- Bottom Action Row: Deadline & Submit Button -->
                <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs">
                    <div class="flex items-center space-x-1.5 text-slate-500 font-medium">
                        <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
                        <span>25 Sep 2026 • 23:59 WITA</span>
                    </div>

                    <div>
                        <template x-if="!submittedTasks['Slice UI Figma Dashboard ke HTML/CSS']">
                            <button type="button" 
                                    @click="openSubmit('Slice UI Figma Dashboard ke HTML/CSS', 'PEMROGRAMAN WEB & MOBILE', '25 Sep 2026 • 23:59 WITA')"
                                    class="bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-extrabold text-xs px-5 py-2.5 rounded-xl shadow-md shadow-blue-500/20 transition-all cursor-pointer">
                                Kumpulkan
                            </button>
                        </template>
                        <template x-if="submittedTasks['Slice UI Figma Dashboard ke HTML/CSS']">
                            <span class="inline-flex items-center space-x-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 font-extrabold text-xs px-4 py-2 rounded-xl">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                <span>Terkumpul</span>
                            </span>
                        </template>
                    </div>
                </div>
            </div>

            <!-- TASK CARD 2: BASIS DATA (RDBMS) (3 Hari Lagi) -->
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-soft space-y-4 hover:shadow-md transition-all">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start space-x-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center shrink-0 shadow-2xs">
                            <i data-lucide="database" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <span class="text-[11px] font-black text-indigo-600 uppercase tracking-wider block">
                                BASIS DATA (RDBMS)
                            </span>
                            <h4 class="font-black text-sm sm:text-base text-slate-900 mt-0.5 leading-snug">
                                Query Relasi Tabel Absensi & Izin
                            </h4>
                        </div>
                    </div>
                    <span class="bg-amber-50 text-amber-700 border border-amber-200 font-extrabold text-[10px] px-3 py-1 rounded-full shrink-0">
                        3 Hari Lagi
                    </span>
                </div>

                <p class="text-xs sm:text-[13px] text-slate-600 leading-relaxed pl-0 sm:pl-1">
                    Buat skema SQL DDL untuk mencatat log keterlambatan siswa dan validasi surat dokter izin sakit.
                </p>

                <!-- Bottom Action Row: Deadline & Detail Button -->
                <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs">
                    <div class="flex items-center space-x-1.5 text-slate-500 font-medium">
                        <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
                        <span>27 Sep 2026 • 12:00 WITA</span>
                    </div>

                    <button type="button" 
                            @click="openDetail('Query Relasi Tabel Absensi & Izin', 'BASIS DATA (RDBMS)')"
                            class="border border-slate-200 bg-slate-50 hover:bg-slate-100 active:scale-95 text-slate-700 font-extrabold text-xs px-4 py-2.5 rounded-xl transition-all cursor-pointer">
                        Detail Soal
                    </button>
                </div>
            </div>

            <!-- TASK CARD 3: MATEMATIKA TERAPAN (Tuntas - Nilai 95) -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-soft flex items-center justify-between gap-4">
                <div class="flex items-center space-x-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 shadow-2xs">
                        <i data-lucide="check" class="w-6 h-6 stroke-[3]"></i>
                    </div>
                    <div>
                        <span class="text-[11px] font-black text-emerald-700 uppercase tracking-wider block">
                            MATEMATIKA TERAPAN
                        </span>
                        <h4 class="font-extrabold text-sm sm:text-base text-slate-900 mt-0.5 leading-tight">
                            Latihan Matriks Ordo 3×3
                        </h4>
                    </div>
                </div>

                <div class="text-right shrink-0">
                    <div class="font-black text-sm sm:text-base text-emerald-600">Nilai: 95</div>
                    <span class="text-[11px] text-slate-400 font-semibold">Tuntas</span>
                </div>
            </div>

        </div>

        <!-- ===================================================================== -->
        <!-- RIGHT / SECONDARY COLUMN (lg:col-span-5 xl:col-span-4)               -->
        <!-- ===================================================================== -->
        <div class="lg:col-span-5 xl:col-span-4 space-y-6">

            <!-- CARD: RINGKASAN PROGRESS TUGAS -->
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-soft space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">
                        STATUS AKADEMIK
                    </h3>
                    <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-0.5 rounded-full">Semester Ganjil</span>
                </div>

                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="p-3 rounded-2xl bg-blue-50/60 border border-blue-100">
                        <p class="text-[10px] font-bold text-slate-500 uppercase">Aktif</p>
                        <p class="text-xl font-black text-blue-600 mt-0.5">2</p>
                    </div>
                    <div class="p-3 rounded-2xl bg-emerald-50/60 border border-emerald-100">
                        <p class="text-[10px] font-bold text-slate-500 uppercase">Tuntas</p>
                        <p class="text-xl font-black text-emerald-600 mt-0.5">1</p>
                    </div>
                    <div class="p-3 rounded-2xl bg-indigo-50/60 border border-indigo-100">
                        <p class="text-[10px] font-bold text-slate-500 uppercase">Rata Nilai</p>
                        <p class="text-xl font-black text-indigo-600 mt-0.5">95</p>
                    </div>
                </div>

                <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 text-xs text-slate-600 space-y-1.5">
                    <div class="flex justify-between items-center">
                        <span class="font-medium">Ketuntasan Tugas:</span>
                        <span class="font-black text-slate-900">33% (1 dari 3)</span>
                    </div>
                    <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                        <div class="bg-blue-600 h-full w-1/3 rounded-full"></div>
                    </div>
                </div>
            </div>

            <!-- CARD: PETUNJUK PENGUMPULAN TUGAS -->
            <div class="bg-gradient-to-br from-slate-900 to-blue-950 text-white rounded-3xl p-5 sm:p-6 shadow-soft space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-white/10">
                    <div class="flex items-center space-x-2">
                        <i data-lucide="info" class="w-4 h-4 text-amber-400"></i>
                        <span class="text-xs font-bold uppercase tracking-wider text-blue-200">Petunjuk Tugas</span>
                    </div>
                    <span class="text-[10px] bg-white/20 text-white px-2 py-0.5 rounded-full font-bold">PPLG 2026</span>
                </div>

                <div class="space-y-2.5 text-xs text-slate-300">
                    <div class="p-2.5 rounded-xl bg-white/5 border border-white/5 flex items-start space-x-2">
                        <span class="font-black text-amber-400">1.</span>
                        <span>Format berkas: <strong>.ZIP</strong>, <strong>.PDF</strong>, atau tautan GitHub / Figma.</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-white/5 border border-white/5 flex items-start space-x-2">
                        <span class="font-black text-amber-400">2.</span>
                        <span>Maksimal batas upload per file adalah <strong>10MB</strong>.</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-rose-500/15 border border-rose-500/20 text-rose-200 flex items-start space-x-2">
                        <span class="font-black text-rose-400">3.</span>
                        <span>Keterlambatan pengumpulan sanksi pengurangan <strong>-10 poin nilai</strong>.</span>
                    </div>
                </div>
            </div>

            <!-- CARD: JADWAL KONSULTASI GURU PENGAMPU -->
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-soft space-y-3">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 pb-2 border-b border-slate-100">
                    GURU PENGAMPU MAPEL
                </h3>

                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                                <i data-lucide="user" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="font-bold text-slate-900">Pak Gede, S.Kom</p>
                                <p class="text-[10px] text-slate-400">Web & Mobile • Lab PPLG</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-lg text-[10px]">Aktif</span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                                <i data-lucide="user" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="font-bold text-slate-900">Ibu Ayu, M.Cs</p>
                                <p class="text-[10px] text-slate-400">Basis Data • Lab PPLG</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-slate-200 text-slate-600 font-bold rounded-lg text-[10px]">Offline</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 3. MODALS: SUBMIT TUGAS & DETAIL SOAL                                     -->
    <!-- ========================================================================= -->

    <!-- Modal Kumpulkan Tugas -->
    <div x-show="showSubmitModal" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         @click.self="showSubmitModal = false">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <span class="text-[10px] font-bold text-blue-600 uppercase" x-text="selectedSubject"></span>
                    <h3 class="font-extrabold text-base text-slate-900 leading-snug" x-text="selectedTask"></h3>
                </div>
                <button @click="showSubmitModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Unggah Berkas Proyek (.ZIP / .PDF)</label>
                    <input type="file" @change="handleFileSelect($event)" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <template x-if="submitFileName">
                        <p class="text-[11px] text-emerald-600 font-semibold mt-1">✓ Berkas terpilih: <span x-text="submitFileName"></span></p>
                    </template>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Atau Tautan Repository (GitHub / Figma / Drive)</label>
                    <input type="url" x-model="submitLink" placeholder="https://github.com/..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-hidden">
                </div>

                <div class="p-3 bg-amber-50 rounded-xl border border-amber-100 text-amber-800 text-[11px]">
                    ⚠️ Pastikan repositori bersifat Publik dan dapat diakses guru pengampu sebelum batas deadline.
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <button type="button" @click="showSubmitModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50">
                    Batal
                </button>
                <button type="button" 
                        @click="confirmSubmit()" 
                        :disabled="isSubmitting"
                        class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md shadow-blue-500/25 flex items-center space-x-2">
                    <template x-if="!isSubmitting">
                        <span>Kirim Tugas</span>
                    </template>
                    <template x-if="isSubmitting">
                        <span>Mengirim...</span>
                    </template>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Detail Soal -->
    <div x-show="showDetailModal" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         @click.self="showDetailModal = false">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <span class="text-[10px] font-bold text-indigo-600 uppercase" x-text="selectedSubject"></span>
                    <h3 class="font-extrabold text-base text-slate-900 leading-snug" x-text="selectedTask"></h3>
                </div>
                <button @click="showDetailModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="space-y-3 text-xs text-slate-600 leading-relaxed">
                <p class="font-bold text-slate-900">Uraian Soal Praktikum:</p>
                <ol class="list-decimal pl-4 space-y-1.5 text-slate-700">
                    <li>Rancang skema tabel database relasional `siswa`, `presensi`, dan `izin`.</li>
                    <li>Gunakan tipe data yang tepat serta definisikan Primary Key & Foreign Key.</li>
                    <li>Tuliskan query `JOIN` untuk menampilkan siswa yang hadir tepat waktu pada bulan berjalan.</li>
                </ol>
                <div class="p-3 bg-blue-50 rounded-xl border border-blue-100 text-blue-800 text-[11px]">
                    💡 Lampirkan berkas file `.sql` atau screenshot hasil query di Workbench/phpMyAdmin.
                </div>
            </div>

            <button @click="showDetailModal = false; openSubmit(selectedTask, selectedSubject, '27 Sep 2026 • 12:00 WITA')" 
                    class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs rounded-xl transition-colors shadow-md shadow-blue-500/20">
                Lanjut Kumpulkan Tugas
            </button>
        </div>
    </div>

    <!-- Toast Notifikasi Sukses -->
    <div x-show="showSuccessToast" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         x-cloak
         class="fixed bottom-6 right-6 z-50 bg-emerald-600 text-white px-5 py-3.5 rounded-2xl shadow-2xl flex items-center space-x-3 border border-emerald-500">
        <i data-lucide="check-circle" class="w-5 h-5 text-white shrink-0"></i>
        <div class="text-xs">
            <p class="font-black text-sm">Tugas Berhasil Terkumpul!</p>
            <p class="text-emerald-100 text-[11px]">Berkas telah dikirimkan ke guru pengampu mata pelajaran.</p>
        </div>
    </div>

</div>
@endsection
