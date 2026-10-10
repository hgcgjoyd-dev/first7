<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Absensi & Kesiswaan') - SMK TI Bali Global Badung</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        display: ['Inter', '"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(37, 99, 235, 0.08), 0 2px 6px -1px rgba(0, 0, 0, 0.04)',
                        'glow': '0 0 25px rgba(37, 99, 235, 0.35)',
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        @keyframes scan-laser {
            0% { top: 10%; opacity: 0.3; }
            50% { top: 85%; opacity: 1; }
            100% { top: 10%; opacity: 0.3; }
        }

        .animate-laser {
            animation: scan-laser 2.4s ease-in-out infinite;
        }

        @keyframes pulse-ring {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.08); opacity: 0.4; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }

        .animate-pulse-ring {
            animation: pulse-ring 2s ease-in-out infinite;
        }

        .header-gradient {
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 50%, #3b82f6 100%);
        }
    </style>

    @stack('styles')
</head>

<body class="bg-[#f4f7fb] text-slate-800 font-sans antialiased min-h-screen selection:bg-blue-600 selection:text-white"
      :class="{ 'overflow-hidden': confirmLogout }"
      @open-logout.window="confirmLogout = true"
      x-data="{ 
          confirmLogout: false,
          currentTimeWita: '',
          currentClockLive: '',
          currentShortClock: '',
          currentDateLive: '',
          initLayout() {
              this.updateTime();
              setInterval(() => this.updateTime(), 1000);
              this.$nextTick(() => lucide.createIcons());
              window.__triggerLogoutModal = () => { this.confirmLogout = true; };
          },
          updateTime() {
              const now = new Date();
              const hours = String(now.getHours()).padStart(2, '0');
              const minutes = String(now.getMinutes()).padStart(2, '0');
              const seconds = String(now.getSeconds()).padStart(2, '0');

              this.currentClockLive = `${hours}:${minutes}:${seconds}`;
              this.currentShortClock = `${hours}:${minutes}`;
              this.currentTimeWita = `${hours}:${minutes}:${seconds} WITA`;

              const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
              const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

              this.currentDateLive = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
          }
      }"
      x-init="initLayout()">

    @php
        $pageTitle = null;
        if (trim($__env->yieldContent('header_title'))) {
            $pageTitle = trim($__env->yieldContent('header_title'));
        } else {
            $pageTitle = match (true) {
                request()->routeIs('dashboard', 'dashboard.*') => 'Dashboard',
                request()->routeIs('profil', 'profil.*') => 'Profil',
                request()->routeIs('bk', 'bk.*', 'konseling', 'konseling.*') => 'Bimbingan Konseling',
                request()->routeIs('presensi', 'presensi.*') => 'Presensi Siswa',
                request()->routeIs('riwayat', 'riwayat.*') => 'Riwayat Absensi',
                request()->routeIs('piket', 'piket.*') => 'Jadwal Piket',
                request()->routeIs('mapel', 'mapel.*') => 'Mata Pelajaran',
                request()->routeIs('izin', 'izin.*') => 'Pengajuan Izin',
                request()->routeIs('scan', 'scan.*') => 'Scan Kartu Pelajar',
                default => null,
            };

            if (!$pageTitle) {
                $rawTitle = trim($__env->yieldContent('title', 'Dashboard'));
                $cleanTitle = trim(explode('-', $rawTitle)[0]);
                $cleanTitle = trim(explode('•', $cleanTitle)[0]);
                $pageTitle = $cleanTitle ?: 'Dashboard';
            }
        }
    @endphp

    <!-- TOP GLOBAL HEADER BAR -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">

            <!-- Left: Brand Logo PNG & Dynamic Page Title -->
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3.5 group">
                <img src="{{ asset('images/logo-smk.png') }}" 
                     alt="Logo SMK TI Bali Global Badung" 
                     class="h-10 sm:h-11 w-auto object-contain drop-shadow-xs group-hover:scale-105 transition-transform duration-200">
                <div class="border-l-2 border-slate-200 pl-3.5 sm:pl-4 flex flex-col justify-center">
                    <p class="font-sans text-xl sm:text-2xl font-extrabold leading-none tracking-tight text-slate-900 group-hover:text-blue-600 transition-colors">
                        {{ $pageTitle }}
                    </p>
                    <p class="mt-1 text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-gradient-to-r from-blue-600 to-indigo-500"></span>
                        Sistem Informasi
                    </p>
                </div>
            </a>

            <!-- Right: Quick Navigation & Profile Avatar (Pojok Kanan) -->
            <div class="flex items-center space-x-3">

                <!-- Notifications Dropdown (Pojok Kanan Atas) -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="relative p-2 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors" title="Pemberitahuan">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white"></span>
                    </button>

                    <div x-show="open" @click.away="open = false" x-cloak
                         class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-xl border border-slate-100 py-3 z-50">

                        <div class="px-4 py-2 border-b border-slate-100 flex items-center justify-between">
                            <span class="font-bold text-sm text-slate-800">Pemberitahuan Siswa</span>
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">3 Baru</span>
                        </div>

                        <div class="divide-y divide-slate-100 text-xs">
                            <a href="{{ route('bk') }}" class="p-3 hover:bg-slate-50 flex items-start space-x-3 block">
                                <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                </div>

                                <div>
                                    <p class="font-semibold text-slate-900">Panggilan BK: Dra. Ni Luh Suastini</p>
                                    <p class="text-slate-500 text-[11px] mt-0.5">Jadwal evaluasi kedisiplinan ruang BK (08:30 WITA)</p>
                                </div>
                            </a>

                            <a href="{{ route('profil') }}" class="p-3 hover:bg-slate-50 flex items-start space-x-3 block">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                    <i data-lucide="award" class="w-4 h-4"></i>
                                </div>

                                <div>
                                    <p class="font-semibold text-slate-900">Reward Juara 2 LKS Web Tech</p>
                                    <p class="text-slate-500 text-[11px] mt-0.5">Ditambahkan +30 poin penghargaan siswa</p>
                                </div>
                            </a>

                            <a href="{{ route('mapel') }}" class="p-3 hover:bg-slate-50 flex items-start space-x-3 block">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                    <i data-lucide="code" class="w-4 h-4"></i>
                                </div>

                                <div>
                                    <p class="font-semibold text-slate-900">Tugas Web & Mobile: Besok!</p>
                                    <p class="text-slate-500 text-[11px] mt-0.5">Deadline Slice UI Figma pukul 23:59 WITA</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User Profile Link -->
                <a href="{{ route('profil') }}" class="flex items-center space-x-2.5 p-1 rounded-xl hover:bg-slate-100 transition-colors text-left pl-2 border-l border-slate-200">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                        <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-xs font-bold text-slate-900 leading-tight">{{ auth()->user()->nama }}</p>
                        <p class="text-[10px] text-slate-500">{{ auth()->user()->siswa?->kelas?->nama_kelas }} • {{ auth()->user()->siswa?->no_siswa }}</p>
                    </div>
                </a>
            </div>
        </div>
    </header>

    <!-- MAIN BODY CONTENT -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="lg:grid lg:grid-cols-12 lg:gap-8 items-start">

            <!-- DESKTOP SIDEBAR NAVIGATION -->
            <aside class="hidden lg:block lg:col-span-3 space-y-4 sticky top-24">

                <!-- Navigation Card -->
                <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-soft">

                    <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-100">
                        <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                            <i data-lucide="user-check" class="w-6 h-6"></i>
                        </div>

                        <div>
                            <h4 class="font-extrabold text-sm text-slate-900">{{ auth()->user()->nama }}</h4>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                                ● {{ auth()->user()->siswa?->kelas?->nama_kelas }} (Aktif)
                            </span>
                        </div>
                    </div>

                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-3 px-3">Menu Utama</p>

                    <nav class="space-y-1.5">
                        <a href="{{ route('dashboard') }}" 
                           class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-xs font-semibold transition-all {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-600 hover:bg-slate-50' }}">
                            <div class="flex items-center space-x-3">
                                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                                <span>Dashboard Siswa</span>
                            </div>
                            <span class="text-[10px] px-2 py-0.5 rounded-full {{ request()->routeIs('dashboard') ? 'bg-blue-700/60 text-white' : 'bg-slate-100 text-slate-600' }}">Beranda</span>
                        </a>
                    </nav>

                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mt-6 mb-3 px-3">Akademik & Disiplin</p>

                    <nav class="space-y-1.5">
                        <a href="{{ route('mapel') }}" 
                           class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-xs font-semibold transition-all {{ request()->routeIs('mapel') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-600 hover:bg-slate-50' }}">
                            <div class="flex items-center space-x-3">
                                <i data-lucide="book-open" class="w-4 h-4"></i>
                                <span>Tugas Mata Pelajaran</span>
                            </div>
                            <span class="text-[10px] bg-rose-500 text-white px-2 py-0.5 rounded-full font-bold">2 Aktif</span>
                        </a>

                        <a href="{{ route('konseling') }}" 
                           class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-xs font-semibold transition-all {{ request()->routeIs('konseling', 'bk') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-600 hover:bg-slate-50' }}">
                            <div class="flex items-center space-x-3">
                                <i data-lucide="shield-alert" class="w-4 h-4"></i>
                                <span>Konseling Siswa (BK)</span>
                            </div>
                            <span class="text-[10px] {{ request()->routeIs('konseling', 'bk') ? 'bg-blue-700/60 text-white' : 'bg-rose-100 text-rose-700' }} px-2 py-0.5 rounded-full font-bold">15 Poin</span>
                        </a>

                        <a href="{{ route('piket') }}" 
                           class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-xs font-semibold transition-all {{ request()->routeIs('piket') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-600 hover:bg-slate-50' }}">
                            <div class="flex items-center space-x-3">
                                <i data-lucide="sparkles" class="w-4 h-4"></i>
                                <span>Jadwal & Tugas Piket</span>
                            </div>
                            <span class="text-[10px] bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full font-bold">Hari Ini</span>
                        </a>

                        <a href="{{ route('profil') }}" 
                           class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-xs font-semibold transition-all {{ request()->routeIs('profil') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-600 hover:bg-slate-50' }}">
                            <div class="flex items-center space-x-3">
                                <i data-lucide="user-cog" class="w-4 h-4"></i>
                                <span>Profil & Kedisiplinan</span>
                            </div>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold {{ request()->routeIs('profil') ? 'bg-white text-blue-600' : 'bg-indigo-100 text-indigo-700' }}">+40 Reward</span>
                        </a>
                    </nav>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mt-6 mb-3 px-3">Akun Siswa</p>
                    <nav class="space-y-1.5">
                        <button type="button" 
                                @click="confirmLogout = true"
                                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all text-rose-600 hover:bg-rose-50 cursor-pointer">
                            <div class="flex items-center space-x-3">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                                <span>Keluar / Ganti Akun</span>
                            </div>
                        </button>
                    </nav>
                </div>

                <!-- School Info Card with Logo PNG -->
                <div class="rounded-3xl p-4 text-white shadow-soft text-center"
                     style="background: linear-gradient(135deg, #090d16 0%, #0f172a 45%, #1e1b4b 100%);">
                    <div class="bg-white rounded-2xl p-2.5 flex items-center justify-center shadow-xs mb-2.5">
                        <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK TI Bali Global Badung" class="h-11 w-auto object-contain">
                    </div>
                    <p class="text-[11px] font-black uppercase tracking-wider text-white">SMK TI BALI GLOBAL</p>
                    <p class="text-[10px] text-sky-200/80 font-medium mt-0.5">Badung, Bali</p>
                </div>
            </aside>

            <!-- MAIN CONTENT AREA -->
            <main class="lg:col-span-9 w-full">
                @yield('content')
            </main>

        </div>
    </div>

    <!-- MOBILE BOTTOM NAVIGATION (Hanya Tampil di Dashboard Sesuai Revisi) -->
    @if(request()->routeIs('dashboard', 'dashboard.siswa'))
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-slate-200/80 px-8 py-2.5 z-40 shadow-lg">
        <div class="max-w-md mx-auto flex items-center justify-between relative">

            <!-- 1. Beranda -->
            <a href="{{ route('dashboard') }}" 
               class="flex flex-col items-center justify-center space-y-1 text-[11px] transition-colors {{ request()->routeIs('dashboard') ? 'text-blue-600 font-bold' : 'text-slate-400 hover:text-slate-700' }}">
                <i data-lucide="home" class="w-5 h-5"></i>
                <span>Beranda</span>
            </a>

            <!-- 2. Floating Center Presensi Button -->
            <div class="relative -top-5">
                <a href="{{ route('presensi') }}" 
                   title="Absensi Masuk & Pulang"
                   class="w-14 h-14 rounded-full bg-gradient-to-tr from-blue-700 to-blue-500 text-white flex items-center justify-center shadow-xl shadow-blue-500/30 hover:scale-105 active:scale-95 transition-all ring-4 ring-white">
                    <i data-lucide="scan-face" class="w-7 h-7"></i>
                </a>
            </div>

            <!-- 3. Riwayat -->
            <a href="{{ route('riwayat') }}" 
               class="flex flex-col items-center justify-center space-y-1 text-[11px] transition-colors {{ request()->routeIs('riwayat') ? 'text-blue-600 font-bold' : 'text-slate-400 hover:text-slate-700' }}">
                <i data-lucide="history" class="w-5 h-5"></i>
                <span>Riwayat</span>
            </a>

        </div>
    </nav>

    <div class="h-20 lg:h-0"></div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>

    <!-- ========================================================================= -->
    <!-- GLOBAL MODAL KONFIRMASI LOGOUT (100% FULLSCREEN GLASSMORPHISM BACKDROP)   -->
    <!-- ========================================================================= -->
    <div x-show="confirmLogout" 
         x-cloak 
         class="fixed inset-0 overflow-y-auto bg-slate-950/75 backdrop-blur-md flex items-center justify-center p-4 sm:p-6"
         style="z-index: 999999; margin: 0;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="confirmLogout = false">

        <!-- Modal Dialog Box -->
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 text-center shadow-2xl border border-slate-100 relative overflow-hidden my-auto"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             @click.away="confirmLogout = false">

            <!-- Decorative Top Ambient Glow -->
            <div class="absolute -top-16 inset-x-0 h-32 bg-gradient-to-b from-rose-500/15 to-transparent pointer-events-none"></div>

            <!-- Close Button (X) -->
            <button @click="confirmLogout = false"
                    type="button"
                    class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-colors cursor-pointer"
                    title="Tutup">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>

            <!-- 3D Glowing Logout Icon -->
            <div class="relative mx-auto w-20 h-20 mb-4 flex items-center justify-center">
                <div class="absolute inset-0 rounded-3xl bg-rose-500/25 blur-xl animate-pulse"></div>
                <div class="relative w-16 h-16 rounded-2xl bg-gradient-to-tr from-rose-600 to-red-500 text-white flex items-center justify-center shadow-xl shadow-rose-500/30 ring-4 ring-rose-50">
                    <i data-lucide="log-out" class="w-8 h-8"></i>
                </div>
            </div>

            <!-- Header Badge & Title -->
            <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-700 font-extrabold text-[11px] mb-2 border border-rose-100">
                <span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-pulse"></span>
                <span>Konfirmasi Keluar Akun</span>
            </div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight">Ingin Mengakhiri Sesi?</h3>
            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed max-w-[300px] mx-auto">
                Sesi login Anda di perangkat ini akan ditutup. Anda dapat masuk kembali menggunakan NIS atau Kartu Pelajar kapan saja.
            </p>

            <!-- Student Preview Mini-Badge -->
            <div class="my-5 p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-left flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-xs shrink-0">
                    <i data-lucide="user" class="w-5 h-5"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()?->nama ?? auth()->user()?->name ?? 'Siswa' }}</p>
                    <p class="text-[10px] text-slate-500 font-medium">NIS: {{ auth()->user()?->siswa?->no_siswa ?? auth()->user()?->no_siswa ?? '-' }} • {{ auth()->user()?->siswa?->kelas?->nama_kelas ?? 'Siswa Aktif' }}</p>
                </div>
                <span class="text-[10px] font-extrabold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full shrink-0">Aktif</span>
            </div>

            <!-- Action Buttons Grid -->
            <div class="grid grid-cols-2 gap-3 pt-1">
                <button @click="confirmLogout = false" 
                        type="button" 
                        class="w-full py-3 px-4 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-extrabold text-slate-700 shadow-xs hover:border-slate-300 transition-all cursor-pointer active:scale-98">
                    Batal
                </button>
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit"
                            class="w-full py-3 px-4 rounded-2xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white text-xs font-extrabold shadow-lg shadow-rose-600/30 transition-all flex items-center justify-center space-x-1.5 cursor-pointer active:scale-98">
                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                        <span>Ya, Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>