@extends('dashboard siswa.app')

@section('title', 'Dashboard Siswa')

@section('content')
@php
    $initialStatus = 'belum';
    if (!empty($presensiHariIni)) {
        if ($presensiHariIni->status === 'Terlambat') {
            $initialStatus = 'telat';
        } else {
            $initialStatus = 'tepat';
        }
    }
@endphp

<div class="space-y-6" x-data="{
    statusAbsen: '{{ $initialStatus }}', // 'belum', 'tepat', 'telat'
    baseHadir: {{ ($totalHadir ?? 0) > 0 ? $totalHadir : 14 }},

    get totalHadirLive() {
        const initialWasBelum = '{{ $initialStatus }}' === 'belum';
        if (initialWasBelum && this.statusAbsen !== 'belum') {
            return this.baseHadir + 1;
        }
        return this.baseHadir;
    },

    init() {
        const saved = localStorage.getItem('presensi_status_today');
        if (saved === 'telat' || saved === 'tepat' || saved === 'belum') {
            this.statusAbsen = saved;
        }
    },

    toggleStatus() {
        if (this.statusAbsen === 'belum') {
            this.statusAbsen = 'tepat';
        } else if (this.statusAbsen === 'tepat') {
            this.statusAbsen = 'telat';
        } else {
            this.statusAbsen = 'belum';
        }
        localStorage.setItem('presensi_status_today', this.statusAbsen);
    },

    getGreeting() {
        const hr = new Date().getHours();
        if (hr >= 4 && hr < 11) return 'Selamat Pagi,';
        if (hr >= 11 && hr < 15) return 'Selamat Siang,';
        if (hr >= 15 && hr < 18) return 'Selamat Sore,';
        return 'Selamat Malam,';
    }
}">
    
    <!-- Header Greeting Hero Card -->
    <div class="header-gradient text-white rounded-3xl p-6 sm:p-8 shadow-soft relative overflow-hidden">
        <!-- Ambient background glow -->
        <div class="absolute -top-12 -right-12 w-64 h-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute bottom-0 right-1/4 w-44 h-44 rounded-full bg-blue-400/20 blur-xl pointer-events-none"></div>

        <!-- Top Row: Logo di kiri & Tombol Profil di pojok kanan atas -->
        <div class="relative z-10 flex items-center justify-between mb-4">
            <!-- Brand Logo (Sudah ada tulisan SMK TI Bali Global Badung di gambarnya) -->
            <div class="bg-white/95 rounded-2xl py-2 px-4 sm:px-5 inline-flex items-center shadow-md">
                <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK TI Bali Global Badung" class="h-10 sm:h-12 md:h-14 w-auto object-contain">
            </div>

            <!-- Profile Avatar Button (Pojok Kanan Atas - Mobile & Desktop) -->
            <a href="{{ route('profil') }}" 
               title="Buka Profil Siswa" 
               class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-white/20 hover:bg-white/30 backdrop-blur-md border border-white/30 flex items-center justify-center text-white shadow-md hover:scale-105 active:scale-95 transition-all group">
                <i data-lucide="user" class="w-6 h-6 sm:w-7 sm:h-7 group-hover:scale-110 transition-transform"></i>
            </a>
        </div>

        <div class="relative z-10">
            <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight" x-text="getGreeting()">Selamat Pagi,</h1>
            <p class="text-2xl sm:text-3xl font-extrabold text-blue-100 mt-0.5">Nama Siswa (Wahyu Pratama)</p>
            
            <div class="flex flex-wrap items-center gap-2 mt-4">
                <!-- Status Absen Pill (Clickable & Real-Time Sync) -->
                <button @click="toggleStatus()" 
                        title="Status Presensi: Klik untuk tes simulasi status"
                        class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-bold transition-all backdrop-blur-md shadow-xs cursor-pointer active:scale-95"
                        :class="{
                            'bg-rose-500/95 text-white hover:bg-rose-600': statusAbsen === 'belum',
                            'bg-emerald-500/95 text-white hover:bg-emerald-600': statusAbsen === 'tepat',
                            'bg-amber-500/95 text-white hover:bg-amber-600': statusAbsen === 'telat'
                        }">
                    <span class="w-2 h-2 rounded-full mr-1.5" 
                          :class="{
                              'bg-white animate-pulse': statusAbsen === 'belum',
                              'bg-emerald-200': statusAbsen === 'tepat',
                              'bg-amber-100': statusAbsen === 'telat'
                          }"></span>
                    <span x-text="statusAbsen === 'belum' ? 'Belum Absen' : (statusAbsen === 'tepat' ? 'Sudah Absen (Tepat)' : 'Sudah Absen (Telat)')">
                        Belum Absen
                    </span>
                </button>

                <!-- Live Time Pill (Mengikuti Jam Sekarang) -->
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/20 text-white backdrop-blur-md">
                    <i data-lucide="clock" class="w-3.5 h-3.5 mr-1.5"></i>
                    <span x-text="'JAM ' + currentClockLive + ' WITA'">JAM 07:30 WITA</span>
                </span>

                <!-- Class Pill -->
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/20 text-white backdrop-blur-md">
                    Kelas XI PPLG 1
                </span>
            </div>
        </div>
    </div>

    <!-- 5 MENU LAYANAN CEPAT SISWA (Exact from Image 2 Left - Fully Clickable & Responsive) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-soft">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-slate-400">PILIHAN MENU CEPAT</h3>
            <span class="text-xs font-semibold text-blue-600">Klik ikon untuk membuka</span>
        </div>

        <!-- The 4 Grid Menu Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
            
            <!-- 1. Izin & Sakit -> /izin -->
            <a href="{{ route('izin') }}" class="group flex flex-col items-center justify-center p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-100 bg-slate-50/70 hover:bg-blue-50/50 hover:border-blue-200 hover:shadow-md transition-all active:scale-95">
                <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-xs">
                    <i data-lucide="mail" class="w-6 h-6"></i>
                </div>
                <span class="font-bold text-xs sm:text-sm text-slate-800 group-hover:text-blue-600 transition-colors text-center">Izin & Sakit</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Formulir Siswa</span>
            </a>

            <!-- 2. Hadir -> /riwayat -->
            <a href="{{ route('riwayat') }}" class="group flex flex-col items-center justify-center p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-100 bg-slate-50/70 hover:bg-emerald-50/50 hover:border-emerald-200 hover:shadow-md transition-all active:scale-95">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-xs">
                    <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                </div>
                <span class="font-bold text-xs sm:text-sm text-slate-800 group-hover:text-emerald-600 transition-colors text-center">Hadir</span>
                <span class="text-[10px] text-emerald-600 font-bold mt-0.5" x-text="totalHadirLive + ' Kali'">{{ ($totalHadir ?? 0) > 0 ? $totalHadir : 14 }} Kali</span>
            </a>

            <!-- 3. Tugas -> /mapel -->
            <a href="{{ route('mapel') }}" class="group flex flex-col items-center justify-center p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-100 bg-slate-50/70 hover:bg-indigo-50/50 hover:border-indigo-200 hover:shadow-md transition-all active:scale-95">
                <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-xs">
                    <i data-lucide="clipboard-list" class="w-6 h-6"></i>
                </div>
                <span class="font-bold text-xs sm:text-sm text-slate-800 group-hover:text-indigo-600 transition-colors text-center">Tugas</span>
                <span class="text-[10px] text-rose-500 font-bold mt-0.5">2 Aktif</span>
            </a>

            <!-- 4. Konseling & BK -> /bk -->
            <a href="{{ route('bk') }}" class="group flex flex-col items-center justify-center p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-100 bg-slate-50/70 hover:bg-violet-50/50 hover:border-violet-200 hover:shadow-md transition-all active:scale-95">
                <div class="w-12 h-12 rounded-2xl bg-violet-100 text-violet-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-xs">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
                <span class="font-bold text-xs sm:text-sm text-slate-800 group-hover:text-violet-600 transition-colors text-center">Konseling & BK</span>
                <span class="text-[10px] text-violet-600 font-bold mt-0.5">15 Poin</span>
            </a>
        </div>

        <!-- SUMMARY ROW CARDS (Hadir Kali & Izin Hari - Clickable & Real-Time Dynamic) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6 pt-6 border-t border-slate-100">
            <!-- Hadir Kali -> /riwayat -->
            <a href="{{ route('riwayat') }}" class="flex items-center justify-between p-4 rounded-2xl bg-blue-50/70 border border-blue-100 hover:border-blue-300 hover:shadow-xs transition-all group">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform">
                        <i data-lucide="user-check" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-xs text-blue-900/80 font-semibold">Total Kehadiran Siswa</p>
                        <p class="text-lg font-black text-blue-900" x-text="'Hadir: ' + totalHadirLive + ' Kali'">Hadir: {{ ($totalHadir ?? 0) > 0 ? $totalHadir : 14 }} Kali</p>
                    </div>
                </div>
                <span class="text-xs bg-blue-200/60 text-blue-800 px-3 py-1 rounded-full font-bold group-hover:bg-blue-600 group-hover:text-white transition-colors">Lihat Kalender →</span>
            </a>

            <!-- Izin Hari -> /izin -->
            <a href="{{ route('izin') }}" class="flex items-center justify-between p-4 rounded-2xl bg-amber-50/70 border border-amber-100 hover:border-amber-300 hover:shadow-xs transition-all group">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform">
                        <i data-lucide="mail-check" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-xs text-amber-900/80 font-semibold">Akumulasi Izin Bulan Ini</p>
                        <p class="text-lg font-black text-amber-900">Izin: {{ $totalIzinBulanIni ?? 0 }} Hari</p>
                    </div>
                </div>
                <span class="text-xs bg-amber-200/60 text-amber-800 px-3 py-1 rounded-full font-bold group-hover:bg-amber-600 group-hover:text-white transition-colors">Ajukan Lagi →</span>
            </a>
        </div>
    </div>

    <!-- HERO ACTION SECTION: "Absen Disini" Fingerprint Trigger (Exact from Image 2) -->
    <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 rounded-3xl p-8 text-white text-center shadow-xl shadow-blue-500/15 relative overflow-hidden flex flex-col items-center justify-center">
        <!-- Decoration circles -->
        <div class="absolute -top-10 -left-10 w-40 h-40 rounded-full bg-white/10 blur-xl"></div>
        <div class="absolute -bottom-10 -right-10 w-40 h-40 rounded-full bg-white/10 blur-xl"></div>

        <div class="relative z-10 flex flex-col items-center max-w-md">
            <div class="inline-flex items-center space-x-2 bg-white/20 backdrop-blur-md px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider text-blue-100 mb-3">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                <span>Presensi Cepat Harian</span>
            </div>
            
            <h2 class="text-2xl sm:text-3xl font-black mb-2">Absen Disini</h2>
            <p class="text-xs sm:text-sm text-blue-100 mb-6 font-medium">
                Posisikan diri di area sekolah SMK TI Bali Global Badung lalu klik tombol scan di bawah ini.
            </p>

            <!-- Arrow Down Indicator -->
            <div class="mb-3 text-blue-200 animate-bounce">
                <i data-lucide="arrow-down" class="w-5 h-5 mx-auto"></i>
            </div>

            <!-- Presensi Button: Click navigates to /presensi -->
            <a href="{{ route('presensi') }}" title="Mulai Presensi Masuk & Pulang" class="relative group block">
                <div class="absolute -inset-4 bg-white/25 rounded-full blur-xl group-hover:bg-white/40 transition-all animate-pulse-ring"></div>
                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full bg-white text-blue-600 flex items-center justify-center shadow-2xl relative z-10 group-hover:scale-105 active:scale-95 transition-all">
                    <i data-lucide="scan-face" class="w-12 h-12 sm:w-14 sm:h-14"></i>
                </div>
            </a>

            <div class="mt-5 text-xs font-semibold text-blue-200">
                <span>Klik untuk Mulai Presensi Wajah & Biometrik</span>
            </div>
        </div>
    </div>

    <!-- RESPONSIVE EXTRA WIDGETS FOR DESKTOP & MOBILE -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Piket Hari Ini Card Preview -> /piket -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-soft flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                            <i data-lucide="sparkles" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-slate-900">Jadwal Piket Hari Ini</h4>
                            <p class="text-[11px] text-slate-500">Kamis • Regu 4 (XI PPLG 1)</p>
                        </div>
                    </div>
                    <a href="{{ route('piket') }}" class="text-xs font-bold text-blue-600 hover:underline">Detail Piket →</a>
                </div>

                <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 space-y-2 text-xs">
                    <div class="flex justify-between items-center text-slate-600">
                        <span>Progress Kebersihan Kelas:</span>
                        <span class="font-bold text-emerald-600">2 dari 4 Selesai (50%)</span>
                    </div>
                    <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                        <div class="bg-emerald-500 h-full w-1/2 rounded-full"></div>
                    </div>
                    <p class="text-[11px] text-slate-500 pt-1">⭐ Koordinator: <span class="font-semibold text-slate-700">Wahyu Pratama (Anda)</span></p>
                </div>
            </div>

            <div class="pt-4 mt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500">Area: Lab RPL 2 & Kelas</span>
                <a href="{{ route('piket') }}" class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold px-3 py-1.5 rounded-xl transition-colors">
                    Buka Checklist →
                </a>
            </div>
        </div>

        <!-- Tugas Terdekat Card Preview -> /mapel -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-soft flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center">
                            <i data-lucide="calendar-clock" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-slate-900">Tugas Mapel Mendesak</h4>
                            <p class="text-[11px] text-slate-500">Deadline Besok 23:59 WITA</p>
                        </div>
                    </div>
                    <a href="{{ route('mapel') }}" class="text-xs font-bold text-blue-600 hover:underline">Lihat Semua →</a>
                </div>

                <div class="p-3.5 bg-rose-50/60 rounded-2xl border border-rose-100 text-xs space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-900">Slice UI Figma Dashboard ke HTML/CSS</span>
                        <span class="bg-rose-500 text-white font-extrabold text-[10px] px-2 py-0.5 rounded-full uppercase">BESOK</span>
                    </div>
                    <p class="text-[11px] text-slate-600">Pemrograman Web & Mobile • Pak Gede</p>
                </div>
            </div>

            <div class="pt-4 mt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500">2 Tugas Belum Dikumpul</span>
                <a href="{{ route('mapel') }}" class="bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold px-3 py-1.5 rounded-xl transition-colors">
                    Kumpulkan Tugas →
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
