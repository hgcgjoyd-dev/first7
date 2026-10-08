<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Presensi & Kesiswaan') - SMK TI Bali Global Badung</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
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
      x-data="{ 
          currentTimeWita: '',
          currentClockLive: '',
          currentShortClock: '',
          currentDateLive: '',
          initLayout() {
              this.updateTime();
              setInterval(() => this.updateTime(), 1000);
              this.$nextTick(() => lucide.createIcons());
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

    <!-- TOP GLOBAL HEADER BAR -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">

            <!-- Left: Brand Logo PNG (Sudah berisi tulisan SMK TI Bali Global Badung) -->
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK TI Bali Global Badung" class="h-10 sm:h-11 w-auto object-contain drop-shadow-sm group-hover:scale-105 transition-transform">
                <div class="border-l border-slate-200 pl-3 hidden sm:block">
                    <p class="text-[11px] font-semibold text-slate-500 leading-tight">Presensi & Kesiswaan</p>
                    <p class="text-[10px] text-blue-600 font-bold uppercase tracking-wider">Portal Resmi Siswa</p>
                </div>
            </a>

            <!-- Middle: Live Status Badges (Online & Clock) -->
            <div class="hidden md:flex items-center space-x-4">
                <div class="flex items-center space-x-2 px-3 py-1.5 bg-emerald-50 border border-emerald-200/80 rounded-full text-xs font-semibold text-emerald-700">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span>ONLINE • Lokasi Valid (Radius 12m)</span>
                </div>

                <div class="flex items-center space-x-2 px-3 py-1.5 bg-slate-100 rounded-full text-xs font-semibold text-slate-700">
                    <i data-lucide="clock" class="w-3.5 h-3.5 text-blue-600"></i>
                    <span x-text="currentTimeWita">07:30:00 WITA</span>
                </div>
            </div>

            <!-- Right: Quick Navigation, Profile & Logout -->
            <div class="flex items-center space-x-3">

                <!-- Notifications Dropdown -->
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
<<<<<<< HEAD:resources/views/dashboard siswa/app.blade.php
=======

                <!-- User Profile Link -->
                <a href="{{ route('profil') }}" class="flex items-center space-x-2.5 p-1 rounded-xl hover:bg-slate-100 transition-colors text-left pl-2 border-l border-slate-200">
                    <div class="w-9 h-9 rounded-full bg-linear-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                        <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                    </div>

                    <div class="hidden sm:block">
                        <p class="text-xs font-bold text-slate-900 leading-tight">Wahyu Pratama</p>
                        <p class="text-[10px] text-slate-500">XI PPLG 1 • 2026042</p>
                    </div>
                </a>
>>>>>>> 09071e5 (Perbaiki error penulisan):resources/views/layouts/app.blade.php
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
                            <h4 class="font-extrabold text-sm text-slate-900">Wahyu Pratama</h4>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                                ● XI PPLG 1 (Aktif)
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

                        <a href="{{ route('bk') }}" 
                           class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-xs font-semibold transition-all {{ request()->routeIs('bk') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-600 hover:bg-slate-50' }}">
                            <div class="flex items-center space-x-3">
                                <i data-lucide="shield-alert" class="w-4 h-4"></i>
                                <span>Konseling & Tugas BK</span>
                            </div>
                            <span class="text-[10px] bg-rose-100 text-rose-700 px-2 py-0.5 rounded-full font-bold">15 Poin</span>
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
                            <span class="text-[10px] bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-bold">+40 Reward</span>
                        </a>
                    </nav>
<<<<<<< HEAD:resources/views/dashboard siswa/app.blade.php
                </div>

                <!-- School Info Card with Logo PNG -->
                <div class="bg-gradient-to-br from-blue-900 to-indigo-900 rounded-3xl p-5 text-white shadow-soft">
                    <div class="bg-white rounded-2xl p-3 mb-3 flex items-center justify-center shadow-xs">
                        <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK TI Bali Global Badung" class="h-16 w-auto object-contain">
=======

                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mt-6 mb-3 px-3">Akun Siswa</p>

                    <nav class="space-y-1.5">
                        <a href="{{ route('logout') }}" 
                           class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all text-rose-600 hover:bg-rose-50">
                            <div class="flex items-center space-x-3">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                                <span>Keluar / Ganti Akun</span>
                            </div>
                        </a>
                    </nav>
                </div>

                <!-- School Info Card with Logo PNG -->
                <div class="bg-linear-to-br from-blue-900 to-indigo-900 rounded-3xl p-5 text-white shadow-soft">
                    <div class="bg-white rounded-2xl p-2.5 mb-3 flex items-center justify-center">
                        <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK TI Bali Global Badung" class="h-12 w-auto object-contain">
>>>>>>> 09071e5 (Perbaiki error penulisan):resources/views/layouts/app.blade.php
                    </div>

                    <p class="text-xs text-blue-100/90 leading-relaxed">
                        Presensi dibuka 06:30 - 07:05 WITA, kepulangan 12:25 WITA. Keterlambatan dicatat otomatis dan mengirim notifikasi WhatsApp ke orang tua/wali.
                    </p>

                    <div class="mt-4 pt-3 border-t border-blue-800/80 flex items-center justify-between text-[11px] text-blue-200">
                        <span>Radius SMK:</span>
                        <span class="font-bold text-emerald-300">Maks. 50 Meter</span>
                    </div>
                </div>
            </aside>

            <!-- MAIN CONTENT AREA -->
            <main class="lg:col-span-9 w-full">
                @yield('content')
            </main>

        </div>
    </div>

    <!-- MOBILE BOTTOM NAVIGATION (Hanya Tampil di Dashboard Sesuai Revisi) -->
    @if(request()->routeIs('dashboard'))
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
<<<<<<< HEAD:resources/views/dashboard siswa/app.blade.php
                   title="Presensi Masuk & Pulang"
                   class="w-14 h-14 rounded-full bg-gradient-to-tr from-blue-700 to-blue-500 text-white flex items-center justify-center shadow-xl shadow-blue-500/30 hover:scale-105 active:scale-95 transition-all ring-4 ring-white">
                    <i data-lucide="scan-face" class="w-7 h-7"></i>
=======
                   title="Presensi Absen"
                   class="w-14 h-14 rounded-full bg-linear-to-tr from-blue-700 to-blue-500 text-white flex items-center justify-center shadow-xl shadow-blue-500/30 hover:scale-105 active:scale-95 transition-all ring-4 ring-white">
                    <i data-lucide="scan" class="w-7 h-7"></i>
>>>>>>> 09071e5 (Perbaiki error penulisan):resources/views/layouts/app.blade.php
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

    @stack('scripts')
</body>
</html>