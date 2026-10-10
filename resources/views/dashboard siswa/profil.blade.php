@extends('layouts.app')

@section('title', 'Profil & Kedisiplinan Siswa')

@section('content')
<div class="w-full space-y-6" 
     x-data="{
         getGreeting() {
             const hr = new Date().getHours();
             if (hr >= 4 && hr < 11) return 'Selamat Pagi,';
             if (hr >= 11 && hr < 15) return 'Selamat Siang,';
             if (hr >= 15 && hr < 18) return 'Selamat Sore,';
             return 'Selamat Malam,';
         }
     }">

    <!-- ========================================================================= -->
    <!-- 1. TOP HEADER HERO (Identical Across Dashboard Siswa Modules)              -->
    <!-- ========================================================================= -->
    <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 text-white rounded-3xl sm:rounded-[32px] p-5 sm:p-7 shadow-lg shadow-blue-500/15 relative overflow-hidden">
        <!-- Ambient decorative shapes -->
        <div class="absolute -top-16 -right-16 w-56 h-56 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-12 -left-12 w-48 h-48 rounded-full bg-indigo-500/20 blur-xl pointer-events-none"></div>

        <!-- Top Navigation Row: Back Button, School Brand, Dashboard Link -->
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
                <a href="{{ route('dashboard') }}" 
                   class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white/15 hover:bg-white/25 backdrop-blur-md flex items-center justify-center text-white transition-all active:scale-95 shadow-sm"
                   title="Beranda">
                    <i data-lucide="home" class="w-5 h-5"></i>
                </a>
            </div>
        </div>

        <!-- Middle Row: Greeting, Student Name, Red Pill Badge on Left; White Avatar on Right -->
        <div class="relative z-10 flex items-center justify-between pt-4 sm:pt-5">
            <div>
                <p class="text-xs sm:text-sm text-blue-100 font-medium" x-text="getGreeting()">Selamat Pagi,</p>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white leading-tight mt-0.5">
                    {{ $siswa->nama_siswa ?? $siswa->nama ?? 'Siswa' }}
                </h1>
                <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-700 font-extrabold text-[11px] sm:text-xs shadow-sm mt-2">
                    <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                    <span>Kelas {{ $siswa->kelas?->nama_kelas ?? 'XI PPLG 1' }} • NIS: {{ $siswa->no_siswa ?? $siswa->nis ?? '-' }}</span>
                </div>
            </div>

            <!-- Avatar -->
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-white text-blue-600 flex items-center justify-center shadow-lg ring-4 ring-white/25 shrink-0">
                <i data-lucide="user" class="w-8 h-8 sm:w-9 sm:h-9"></i>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. QUICK STATS SUMMARY BANNER                                             -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 sm:gap-4">
        <!-- Stat 1: Total Kehadiran -->
        <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-soft flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Kehadiran</p>
                <h4 class="text-lg font-black text-slate-900 leading-tight">{{ (int) ($totalHadir ?? 0) }} Kali Hadir</h4>
                <p class="text-[10px] text-emerald-600 font-bold mt-0.5">Tingkat Disiplin: {{ (int) ($persenHadir ?? 100) }}%</p>
            </div>
        </div>

        <!-- Stat 2: Total Izin -->
        <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-soft flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <i data-lucide="mail" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Akumulasi Izin</p>
                <h4 class="text-lg font-black text-slate-900 leading-tight">{{ (int) ($totalIzin ?? 0) }} Hari Izin</h4>
                <p class="text-[10px] text-amber-600 font-bold mt-0.5">Surat Keterangan</p>
            </div>
        </div>

        <!-- Stat 3: Poin Prestasi & BK -->
        <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-soft flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <i data-lucide="award" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Poin Prestasi</p>
                <h4 class="text-lg font-black text-slate-900 leading-tight">+{{ $siswa->poin_penghargaan ?? 50 }} Poin</h4>
                <p class="text-[10px] text-indigo-600 font-bold mt-0.5">Pelanggaran: {{ $siswa->poin_pelanggaran ?? 0 }} Poin</p>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. MAIN DETAILS: BIODATA, DISIPLIN & AKUN SISWA                           -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- LEFT COLUMN (Biodata & Aturan Presensi) -->
        <div class="lg:col-span-7 space-y-6">

            <!-- Card: Identitas Siswa Lengkap -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-soft">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i data-lucide="id-card" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-base text-slate-900">Identitas Siswa</h3>
                            <p class="text-xs text-slate-500">Data kesiswaan resmi terdaftar di sistem</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                        ● Aktif
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-3.5 bg-slate-50/80 rounded-2xl border border-slate-100">
                        <span class="text-slate-400 font-semibold block mb-0.5">Nama Lengkap</span>
                        <span class="text-slate-900 font-bold text-sm">{{ $siswa->nama_siswa ?? $siswa->nama ?? 'Siswa' }}</span>
                    </div>

                    <div class="p-3.5 bg-slate-50/80 rounded-2xl border border-slate-100">
                        <span class="text-slate-400 font-semibold block mb-0.5">Nomor Induk Siswa (NIS)</span>
                        <span class="text-slate-900 font-bold text-sm font-mono">{{ $siswa->no_siswa ?? $siswa->nis ?? '-' }}</span>
                    </div>

                    <div class="p-3.5 bg-slate-50/80 rounded-2xl border border-slate-100">
                        <span class="text-slate-400 font-semibold block mb-0.5">Nomor Induk Siswa Nasional (NISN)</span>
                        <span class="text-slate-900 font-bold text-sm font-mono">{{ $siswa->nisn ?? '0071234567' }}</span>
                    </div>

                    <div class="p-3.5 bg-slate-50/80 rounded-2xl border border-slate-100">
                        <span class="text-slate-400 font-semibold block mb-0.5">Kelas & Rombel</span>
                        <span class="text-slate-900 font-bold text-sm">{{ $siswa->kelas?->nama_kelas ?? 'XI PPLG 1' }}</span>
                    </div>

                    <div class="p-3.5 bg-slate-50/80 rounded-2xl border border-slate-100 sm:col-span-2">
                        <span class="text-slate-400 font-semibold block mb-0.5">Kompetensi Keahlian (Jurusan)</span>
                        <span class="text-slate-900 font-bold text-sm">Pengembangan Perangkat Lunak dan Gim (PPLG)</span>
                    </div>

                    <div class="p-3.5 bg-slate-50/80 rounded-2xl border border-slate-100 sm:col-span-2">
                        <span class="text-slate-400 font-semibold block mb-0.5">Email Akun Sekolah</span>
                        <span class="text-slate-900 font-bold text-sm font-mono">{{ $siswa->user?->email ?? $siswa->email ?? 'siswa@smktibaliglobal.sch.id' }}</span>
                    </div>
                </div>
            </div>

            <!-- Card: Aturan Ketentuan Presensi Sekolah (Kontras Tinggi & Font Jelas) -->
            <div class="rounded-3xl p-6 sm:p-7 shadow-xl relative overflow-hidden border border-slate-800 text-white"
                 style="background: linear-gradient(135deg, #090d16 0%, #0f172a 45%, #1e1b4b 100%);">
                <!-- Ambient glow effects -->
                <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-blue-500/20 blur-2xl pointer-events-none"></div>
                <div class="absolute -bottom-10 -left-10 w-40 h-40 rounded-full bg-indigo-500/20 blur-xl pointer-events-none"></div>

                <div class="relative z-10 flex items-center space-x-3 mb-5">
                    <div class="w-11 h-11 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-white ring-1 ring-white/20 shadow-md">
                        <i data-lucide="clock" class="w-5 h-5 text-sky-300"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-base text-white tracking-tight">Ketentuan Jam Presensi Sekolah</h4>
                        <p class="text-xs text-sky-200/90 font-medium mt-0.5">Sesuai SOP Tata Tertib SMK TI Bali Global Badung</p>
                    </div>
                </div>

                <div class="relative z-10 grid grid-cols-1 sm:grid-cols-2 gap-3.5 mb-5 text-xs">
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/15">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-sky-200 font-semibold">Presensi Datang</span>
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        </div>
                        <span class="text-white font-black text-base sm:text-lg block tracking-tight">06:30 – 07:05 WITA</span>
                        <span class="text-[11px] text-amber-300 font-semibold mt-1 inline-flex items-center">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5 mr-1 inline shrink-0"></i>
                            Setelah 07:05 otomatis Telat
                        </span>
                    </div>

                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/15">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-sky-200 font-semibold">Presensi Pulang</span>
                            <span class="w-2 h-2 rounded-full bg-sky-300"></span>
                        </div>
                        <span class="text-white font-black text-base sm:text-lg block tracking-tight">12:25 WITA</span>
                        <span class="text-[11px] text-sky-200/90 font-medium mt-1 inline-flex items-center">
                            <i data-lucide="check" class="w-3.5 h-3.5 mr-1 inline shrink-0 text-emerald-400"></i>
                            Konfirmasi kepulangan harian
                        </span>
                    </div>
                </div>

                <div class="relative z-10 pt-4 border-t border-white/15 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <span class="inline-flex items-center space-x-2 text-sky-100 font-medium bg-white/10 px-3.5 py-1.5 rounded-full border border-white/10">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-400"></i>
                        <span>Radius Geofence: <strong class="text-white">50 Meter</strong> Area Kampus</span>
                    </span>
                    <a href="{{ route('riwayat') }}" class="font-bold text-white hover:text-sky-200 inline-flex items-center space-x-1.5 bg-blue-600 hover:bg-blue-500 px-4 py-1.5 rounded-full backdrop-blur-md border border-white/20 shadow-md transition-all active:scale-95">
                        <span>Lihat Riwayat</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN (Disiplin BK & Logout Akun) -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Card: Status Poin Disiplin BK & Prestasi -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-soft">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                            <i data-lucide="shield-alert" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-base text-slate-900">Buku Disiplin Siswa</h3>
                            <p class="text-xs text-slate-500">Pelanggaran & poin penghargaan</p>
                        </div>
                    </div>
                    <span class="text-xs font-black text-rose-600 bg-rose-50 px-2.5 py-1 rounded-full border border-rose-100">
                        {{ $siswa->poin_pelanggaran ?? 0 }}/30 Poin
                    </span>
                </div>

                <!-- Progress Bar Poin Pelanggaran -->
                <div class="space-y-2 mb-5">
                    <div class="flex justify-between text-xs font-semibold">
                        <span class="text-slate-600">Poin Pelanggaran Aktif</span>
                        <span class="{{ ($siswa->poin_pelanggaran ?? 0) >= 30 ? 'text-rose-600 font-bold' : (($siswa->poin_pelanggaran ?? 0) >= 15 ? 'text-amber-600 font-bold' : 'text-emerald-600 font-bold') }}">
                            {{ ($siswa->poin_pelanggaran ?? 0) >= 30 ? 'Panggilan Orang Tua' : (($siswa->poin_pelanggaran ?? 0) >= 15 ? 'Peringatan 1 (SP-1)' : 'Tertib & Disiplin') }}
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                        @php
                            $progressPelanggaran = min(100, max(5, round(((int) ($siswa->poin_pelanggaran ?? 0) / 30) * 100)));
                        @endphp
                        <div class="bg-gradient-to-r from-amber-500 to-rose-500 h-full rounded-full transition-all duration-500"
                             @style(['width: ' . $progressPelanggaran . '%'])></div>
                    </div>
                    <p class="text-[11px] text-slate-400">Batas Surat Panggilan Orang Tua: 30 Poin</p>
                </div>

                <!-- Reward Poin Prestasi -->
                <div class="p-3.5 bg-emerald-50/70 border border-emerald-100 rounded-2xl flex items-center justify-between mb-5">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold shadow-xs">
                            <i data-lucide="trophy" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-emerald-900">Poin Reward Prestasi</p>
                            <p class="text-[11px] text-emerald-700">+{{ $siswa->poin_penghargaan ?? 50 }} Poin (Prestasi Aktif)</p>
                        </div>
                    </div>
                    <span class="text-xs bg-emerald-200/80 text-emerald-900 font-extrabold px-2.5 py-1 rounded-full">Kompensasi</span>
                </div>

                <a href="{{ route('bk') }}" class="w-full flex items-center justify-center space-x-2 py-2.5 px-4 rounded-2xl bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200 transition-colors">
                    <i data-lucide="book-open" class="w-4 h-4"></i>
                    <span>Buka Ruang Bimbingan & Tugas BK</span>
                </a>
            </div>

            <!-- Card: Akun Siswa & Tombol Keluar (Logout) -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-soft">
                <div class="flex items-center space-x-2.5 pb-4 mb-4 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                        <i data-lucide="user-check" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900">Kelola Akun Siswa</h3>
                        <p class="text-xs text-slate-500">Sesi login portal presensi siswa</p>
                    </div>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed mb-5">
                    Keluar dari akun akan menghapus sesi login di perangkat ini. Anda dapat masuk kembali menggunakan NIS atau Kartu Pelajar kapan saja.
                </p>

                <!-- Tombol Logout: Elegan & Berkelas (Memicu Modal Global Fullscreen) -->
                <button @click="$dispatch('open-logout')" 
                        type="button"
                        class="w-full group flex items-center justify-center space-x-2.5 py-3.5 px-4 rounded-2xl bg-rose-50/80 hover:bg-rose-500 text-rose-600 hover:text-white font-bold text-xs sm:text-sm border border-rose-200 hover:border-rose-500 shadow-xs hover:shadow-lg hover:shadow-rose-500/25 transition-all duration-200 active:scale-98 cursor-pointer">
                    <i data-lucide="log-out" class="w-4 h-4 transition-transform group-hover:-translate-x-1"></i>
                    <span>Keluar / Ganti Akun</span>
                </button>
            </div>

        </div>

    </div>

</div>
@endsection