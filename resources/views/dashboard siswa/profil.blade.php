@extends('layouts.app')

@section('title', 'Profil & Kedisiplinan Siswa')

@section('content')
<div class="w-full space-y-6" x-data="{
    confirmLogout: false,

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
                    {{ $siswa->nama ?? 'Wahyu Pratama' }}
                </h1>
                <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-700 font-extrabold text-[11px] sm:text-xs shadow-sm mt-2">
                    <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                    <span>Kelas {{ $siswa->kelas ?? 'XI PPLG 1' }} • NIS: {{ $siswa->nis ?? '2026042' }}</span>
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
        <!-- Stat 1: Total Kehadiran (Dihitung dari Berapa Kali Hadir) -->
        <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-soft flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Kehadiran</p>
                <h4 class="text-lg font-black text-slate-900 leading-tight">{{ $totalHadir ?? 14 }} Kali Hadir</h4>
                <p class="text-[10px] text-emerald-600 font-bold mt-0.5">Disiplin: {{ $persenHadir ?? 93 }}%</p>
            </div>
        </div>

        <!-- Stat 2: Total Izin -->
        <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-soft flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <i data-lucide="mail" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Akumulasi Izin</p>
                <h4 class="text-lg font-black text-slate-900 leading-tight">{{ $totalIzin ?? 3 }} Hari Izin</h4>
                <p class="text-[10px] text-amber-600 font-bold mt-0.5">Surat Terverifikasi</p>
            </div>
        </div>

        <!-- Stat 3: Poin Prestasi & BK -->
        <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-soft flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <i data-lucide="award" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Poin Prestasi</p>
                <h4 class="text-lg font-black text-slate-900 leading-tight">+{{ $siswa->poin_prestasi ?? 50 }} Poin</h4>
                <p class="text-[10px] text-indigo-600 font-bold mt-0.5">BK: {{ $siswa->poin_bk ?? 15 }} Poin</p>
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
                    <div class="p-3 bg-slate-50/80 rounded-2xl border border-slate-100">
                        <span class="text-slate-400 font-semibold block mb-0.5">Nama Lengkap</span>
                        <span class="text-slate-900 font-bold text-sm">{{ $siswa->nama ?? 'Wahyu Pratama' }}</span>
                    </div>

                    <div class="p-3 bg-slate-50/80 rounded-2xl border border-slate-100">
                        <span class="text-slate-400 font-semibold block mb-0.5">Nomor Induk Siswa (NIS)</span>
                        <span class="text-slate-900 font-bold text-sm font-mono">{{ $siswa->nis ?? '2026042' }}</span>
                    </div>

                    <div class="p-3 bg-slate-50/80 rounded-2xl border border-slate-100">
                        <span class="text-slate-400 font-semibold block mb-0.5">Nomor Induk Siswa Nasional (NISN)</span>
                        <span class="text-slate-900 font-bold text-sm font-mono">{{ $siswa->nisn ?? '0071234567' }}</span>
                    </div>

                    <div class="p-3 bg-slate-50/80 rounded-2xl border border-slate-100">
                        <span class="text-slate-400 font-semibold block mb-0.5">Kelas & Rombel</span>
                        <span class="text-slate-900 font-bold text-sm">{{ $siswa->kelas ?? 'XI PPLG 1' }}</span>
                    </div>

                    <div class="p-3 bg-slate-50/80 rounded-2xl border border-slate-100 sm:col-span-2">
                        <span class="text-slate-400 font-semibold block mb-0.5">Kompetensi Keahlian (Jurusan)</span>
                        <span class="text-slate-900 font-bold text-sm">Pengembangan Perangkat Lunak dan Gim (PPLG)</span>
                    </div>

                    <div class="p-3 bg-slate-50/80 rounded-2xl border border-slate-100 sm:col-span-2">
                        <span class="text-slate-400 font-semibold block mb-0.5">Email Akun Sekolah</span>
                        <span class="text-slate-900 font-bold text-sm font-mono">{{ $siswa->email ?? 'wahyu.pratama@smktibaliglobal.sch.id' }}</span>
                    </div>
                </div>
            </div>

            <!-- Card: Aturan Ketentuan Presensi Sekolah -->
            <div class="bg-linear-to-br from-blue-900 to-indigo-900 text-white rounded-3xl p-6 shadow-soft relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full bg-white/10 blur-xl pointer-events-none"></div>

                <div class="relative z-10 flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 rounded-2xl bg-white/15 flex items-center justify-center text-white backdrop-blur-md">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-sm text-white">Ketentuan Jam Presensi Sekolah</h4>
                        <p class="text-xs text-blue-200">Sesuai SOP Tata Tertib SMK TI Bali Global Badung</p>
                    </div>
                </div>

                <div class="relative z-10 grid grid-cols-2 gap-3 text-xs mb-4">
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 border border-white/10">
                        <span class="text-blue-200 font-semibold block mb-1">Presensi Datang</span>
                        <span class="text-white font-extrabold text-sm block">06:30 - 07:05 WITA</span>
                        <span class="text-[10px] text-blue-300">Setelah 07:05 otomatis Telat</span>
                    </div>

                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 border border-white/10">
                        <span class="text-blue-200 font-semibold block mb-1">Presensi Pulang</span>
                        <span class="text-white font-extrabold text-sm block">12:25 WITA</span>
                        <span class="text-[10px] text-blue-300">Konfirmasi kepulangan harian</span>
                    </div>
                </div>

                <div class="relative z-10 pt-3 border-t border-white/15 flex items-center justify-between text-xs text-blue-200">
                    <span class="flex items-center space-x-1.5">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-400"></i>
                        <span>Radius Geofence: 50 Meter Area Kampus</span>
                    </span>
                    <a href="{{ route('riwayat') }}" class="font-bold text-white hover:underline flex items-center space-x-1">
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
                        {{ $siswa->poin_bk ?? 15 }}/30 Poin
                    </span>
                </div>

                <!-- Progress Bar Poin Pelanggaran -->
                <div class="space-y-2 mb-5">
                    <div class="flex justify-between text-xs font-semibold">
                        <span class="text-slate-600">Poin Pelanggaran Aktif</span>
                        <span class="text-rose-600 font-bold">Peringatan 1 (SP-1)</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                        <div class="bg-gradient-to-r from-amber-500 to-rose-500 h-full rounded-full" style="width: 50%"></div>
                    </div>
                    <p class="text-[11px] text-slate-400">Batas Surat Panggilan Orang Tua: 30 Poin</p>
                </div>

                <!-- Reward Poin Prestasi -->
                <div class="p-3.5 bg-emerald-50/70 border border-emerald-100 rounded-2xl flex items-center justify-between mb-5">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold">
                            <i data-lucide="trophy" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-emerald-900">Poin Reward Prestasi</p>
                            <p class="text-[11px] text-emerald-700">+{{ $siswa->poin_prestasi ?? 50 }} Poin (Juara 2 LKS Web)</p>
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

                <!-- Tombol Logout -->
                <button @click="confirmLogout = true" 
                        type="button"
                        class="w-full flex items-center justify-center space-x-2 py-3 px-4 rounded-2xl bg-rose-50 text-rose-600 hover:bg-rose-100 hover:text-rose-700 font-bold text-xs sm:text-sm border border-rose-200 transition-all active:scale-98 cursor-pointer">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    <span>Keluar / Ganti Akun</span>
                </button>
            </div>

        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 4. MODAL KONFIRMASI LOGOUT                                                -->
    <!-- ========================================================================= -->
    <div x-show="confirmLogout" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         @keydown.escape.window="confirmLogout = false">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 text-center shadow-2xl border border-slate-100"
             @click.away="confirmLogout = false">
            <div class="w-14 h-14 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4">
                <i data-lucide="alert-triangle" class="w-7 h-7"></i>
            </div>

            <h3 class="text-lg font-extrabold text-slate-900 mb-1">Konfirmasi Keluar</h3>
            <p class="text-xs text-slate-500 mb-6 leading-relaxed">
                Apakah Anda yakin ingin keluar dari akun siswa SMK TI Bali Global Badung?
            </p>

            <div class="grid grid-cols-2 gap-3">
                <button @click="confirmLogout = false" 
                        type="button" 
                        class="w-full py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <a href="{{ route('logout') }}" 
                   class="w-full py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-colors flex items-center justify-center">
                    Ya, Keluar
                </a>
            </div>
        </div>
    </div>

</div>
@endsection