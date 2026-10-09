<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Guru - SMK TI Bali Global Badung</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb', // Royal Blue Mockup Match
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05), 0 2px 6px -1px rgba(0, 0, 0, 0.03)',
                        'floating': '0 10px 25px -3px rgba(37, 99, 235, 0.25), 0 4px 10px -2px rgba(37, 99, 235, 0.15)',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        [x-cloak] { display: none !important; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        /* Custom smooth scrollbar */
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
    </style>
</head>
<body class="bg-[#F4F6FB] text-slate-800 antialiased min-h-screen flex flex-col selection:bg-blue-600 selection:text-white"
      x-data="{
          liveClock: '03:24',
          liveFullClock: '03:24:00 WITA',
          greetingText: 'Selamat Pagi,',

          // Data Guru
          guru: {
              nama: '{{ session('guru_nama', 'Nama Guru') }}',
              role: 'Guru Mata Pelajaran • RPL',
              nip: '19850314 201001 1 015',
              mapelUtama: 'Pemrograman Web & Mobile'
          },

          // Modal Popups untuk Interaktivitas Lengkap
          modalMapel: false,
          modalSesi: false,
          modalJadwal: false,
          modalNilai: false,
          modalProfil: false,

          // Toast Notifikasi
          showToast: false,
          toastMsg: '',

          init() {
              this.updateClock();
              setInterval(() => this.updateClock(), 1000);
              this.$nextTick(() => {
                  if (window.lucide) lucide.createIcons();
              });
          },

          updateClock() {
              const now = new Date();
              const hr = String(now.getHours()).padStart(2, '0');
              const mn = String(now.getMinutes()).padStart(2, '0');
              const sc = String(now.getSeconds()).padStart(2, '0');
              this.liveClock = `${hr}:${mn}`;
              this.liveFullClock = `${hr}:${mn}:${sc} WITA`;

              const hourNum = now.getHours();
              if (hourNum >= 4 && hourNum < 11) this.greetingText = 'Selamat Pagi,';
              else if (hourNum >= 11 && hourNum < 15) this.greetingText = 'Selamat Siang,';
              else if (hourNum >= 15 && hourNum < 18) this.greetingText = 'Selamat Sore,';
              else this.greetingText = 'Selamat Malam,';
          },

          triggerToast(msg) {
              this.toastMsg = msg;
              this.showToast = true;
              this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
              setTimeout(() => { this.showToast = false; }, 3500);
          }
      }">

    <!-- ========================================================================= -->
    <!-- DESKTOP TOP BAR (Hanya Tampil di Layar Desktop lg:)                       -->
    <!-- ========================================================================= -->
    <header class="hidden lg:block bg-white/95 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-40 shadow-xs">
        <div class="max-w-6xl mx-auto px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center p-1.5 shadow-xs">
                    <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK" class="w-full h-full object-contain">
                </div>
                <div>
                    <h2 class="text-xs font-black tracking-wider uppercase text-blue-900 leading-none">SMK TI BALI GLOBAL BADUNG</h2>
                    <p class="text-[11px] font-bold text-slate-400 mt-0.5">Portal Layanan Guru Mata Pelajaran</p>
                </div>
            </div>

            <!-- Right Desktop Profile -->
            <div class="flex items-center space-x-4">
                <button type="button" @click="modalProfil = true" class="flex items-center space-x-2.5 p-1.5 hover:bg-slate-100 rounded-2xl transition-all cursor-pointer">
                    <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </div>
                    <div class="text-left hidden sm:block">
                        <span class="block text-xs font-extrabold text-slate-800 leading-tight" x-text="guru.nama">Nama Guru</span>
                        <span class="block text-[10px] font-semibold text-slate-400" x-text="guru.role">Guru Mapel RPL</span>
                    </div>
                </button>
            </div>
        </div>
    </header>


    <!-- ========================================================================= -->
    <!-- MAIN CONTAINER (Mobile Frame di HP, Lebar Penuh Responsif di Desktop)    -->
    <!-- ========================================================================= -->
    <div class="flex-1 w-full max-w-md sm:max-w-2xl lg:max-w-6xl mx-auto px-0 sm:px-4 lg:px-8 py-0 sm:py-6 lg:py-8 flex flex-col justify-between">

        <main class="w-full flex-1 pb-24 lg:pb-12">

            <!-- ================================================================= -->
            <!-- 1. HERO HEADER (Royal Blue Gradient with Curved Bottom)            -->
            <!-- ================================================================= -->
            <div class="relative bg-gradient-to-b from-[#1d4ed8] via-[#2563eb] to-[#2563eb] text-white rounded-b-[40px] sm:rounded-[36px] lg:rounded-[40px] pt-7 sm:pt-8 lg:pt-10 pb-7 sm:pb-8 lg:pb-10 px-6 sm:px-8 lg:px-10 shadow-xl shadow-blue-600/20 overflow-hidden">
                
                <!-- Ambient Subtle Lighting Overlay -->
                <div class="absolute inset-0 rounded-b-[40px] sm:rounded-[36px] lg:rounded-[40px] overflow-hidden pointer-events-none">
                    <div class="absolute -top-12 -right-12 w-64 h-64 rounded-full bg-white/10 blur-2xl"></div>
                    <div class="absolute bottom-0 left-1/4 w-72 h-72 rounded-full bg-blue-300/15 blur-3xl"></div>
                </div>

                <div class="relative z-10 space-y-4 sm:space-y-5">

                    <!-- Top Pill: School Brand (Official SMK TI Logo + Name) -->
                    <div class="flex items-center justify-center">
                        <div class="inline-flex items-center space-x-2.5 bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-full border border-white/20 shadow-xs">
                            <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-white p-0.5 flex items-center justify-center shrink-0 shadow-xs">
                                <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK" class="w-full h-full object-contain">
                            </div>
                            <span class="text-xs sm:text-sm font-black tracking-wider uppercase text-white drop-shadow-xs">
                                SMK TI BALI GLOBAL BADUNG
                            </span>
                        </div>
                    </div>

                    <!-- Middle Content Row: Greeting + Nama Guru + Guru Mapel RPL + Waktu Realtime SATU-SATUNYA -->
                    <div class="flex items-center justify-between pt-1">
                        <!-- Left Side -->
                        <div class="space-y-1">
                            <p class="text-xs sm:text-sm text-blue-100 font-medium tracking-wide" x-text="greetingText">
                                Selamat Pagi,
                            </p>
                            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight" x-text="guru.nama">
                                Nama Guru
                            </h1>
                            <p class="text-xs sm:text-sm text-blue-100 font-semibold" x-text="guru.role">
                                Guru Mata Pelajaran • RPL
                            </p>

                            <!-- WAKTU REALTIME SATU-SATUNYA (Di Dekat Nama Guru & Mapel RPL) -->
                            <div class="pt-2 flex items-center">
                                <div class="inline-flex items-center space-x-2 bg-white/20 hover:bg-white/25 backdrop-blur-md text-white px-3.5 py-1.5 rounded-full text-xs font-black border border-white/30 shadow-xs tabular-nums select-none">
                                    <i data-lucide="clock" class="w-3.5 h-3.5 text-blue-200 animate-pulse"></i>
                                    <span x-text="'JAM ' + liveFullClock">JAM 03:24:00 WITA</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Avatar: White Circular Pill with Blue Teacher Icon -->
                        <button type="button" 
                                @click="modalProfil = true"
                                title="Buka Profil Guru"
                                class="w-14 h-14 sm:w-16 sm:h-16 lg:w-20 lg:h-20 rounded-full bg-white text-blue-600 flex items-center justify-center shadow-lg hover:scale-105 active:scale-95 transition-all cursor-pointer shrink-0">
                            <!-- Teacher Silhouette with Collar/Tie matching mockup -->
                            <svg class="w-7 h-7 sm:w-8 sm:h-8 lg:w-10 lg:h-10 text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd" d="M12 2a5 5 0 100 10 5 5 0 000-10zm-7 18a7 7 0 0114 0H5z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                </div>

            </div>


            <!-- ================================================================= -->
            <!-- 2. ACTION CARDS SECTION (Mobile 2x2 Grid, Desktop 4 Columns)       -->
            <!-- ================================================================= -->
            <div class="px-5 sm:px-0 mt-6 sm:mt-8 lg:mt-8 space-y-6 lg:space-y-8">

                <!-- Responsive Grid: 2 Columns on Mobile, 4 Columns on Desktop -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 lg:gap-6">
                    
                    <!-- Card 1: Mapel Guru -->
                    <div @click="modalMapel = true" 
                         class="bg-white rounded-3xl p-5 sm:p-6 lg:p-7 shadow-soft border border-slate-100/90 hover:border-blue-300 hover:shadow-lg transition-all duration-200 active:scale-97 cursor-pointer flex flex-col items-center justify-center text-center group">
                        
                        <!-- Icon Box (Light Blue) -->
                        <div class="w-14 h-14 sm:w-16 sm:h-16 lg:w-18 lg:h-18 rounded-2xl bg-blue-50/90 text-blue-600 flex items-center justify-center mb-3 sm:mb-4 group-hover:scale-110 transition-transform">
                            <i data-lucide="clipboard-list" class="w-7 h-7 sm:w-8 sm:h-8 lg:w-9 lg:h-9 stroke-[2.2]"></i>
                        </div>

                        <h3 class="text-sm sm:text-base lg:text-lg font-black text-slate-800 group-hover:text-blue-600 transition-colors">
                            Mapel Guru
                        </h3>
                        <p class="text-[11px] sm:text-xs text-slate-400 font-medium mt-0.5">
                            3 Mata Pelajaran
                        </p>
                    </div>

                    <!-- Card 2: Sesi Aktif -->
                    <div @click="modalSesi = true" 
                         class="bg-white rounded-3xl p-5 sm:p-6 lg:p-7 shadow-soft border border-slate-100/90 hover:border-emerald-300 hover:shadow-lg transition-all duration-200 active:scale-97 cursor-pointer flex flex-col items-center justify-center text-center group">
                        
                        <!-- Icon Box (Light Green) -->
                        <div class="w-14 h-14 sm:w-16 sm:h-16 lg:w-18 lg:h-18 rounded-2xl bg-emerald-50/90 text-emerald-600 flex items-center justify-center mb-3 sm:mb-4 group-hover:scale-110 transition-transform">
                            <i data-lucide="presentation" class="w-7 h-7 sm:w-8 sm:h-8 lg:w-9 lg:h-9 stroke-[2.2]"></i>
                        </div>

                        <h3 class="text-sm sm:text-base lg:text-lg font-black text-slate-800 group-hover:text-emerald-600 transition-colors">
                            Sesi Aktif
                        </h3>
                        <!-- Badge: XI RPL 2 -->
                        <span class="inline-block bg-emerald-50 text-emerald-700 font-black text-[10px] sm:text-xs px-3 py-1 rounded-full mt-1.5 border border-emerald-100 shadow-2xs">
                            XI RPL 2
                        </span>
                    </div>

                    <!-- Card 3: Jadwal Mengajar -->
                    <div @click="modalJadwal = true" 
                         class="bg-white rounded-3xl p-5 sm:p-6 lg:p-7 shadow-soft border border-slate-100/90 hover:border-indigo-300 hover:shadow-lg transition-all duration-200 active:scale-97 cursor-pointer flex flex-col items-center justify-center text-center group">
                        
                        <!-- Icon Box (Light Purple / Indigo) -->
                        <div class="w-14 h-14 sm:w-16 sm:h-16 lg:w-18 lg:h-18 rounded-2xl bg-indigo-50/90 text-indigo-600 flex items-center justify-center mb-3 sm:mb-4 group-hover:scale-110 transition-transform">
                            <i data-lucide="calendar" class="w-7 h-7 sm:w-8 sm:h-8 lg:w-9 lg:h-9 stroke-[2.2]"></i>
                        </div>

                        <h3 class="text-sm sm:text-base lg:text-lg font-black text-slate-800 group-hover:text-indigo-600 transition-colors">
                            Jadwal Mengajar
                        </h3>
                        <p class="text-[11px] sm:text-xs text-slate-400 font-medium mt-0.5">
                            3 Kelas Hari Ini
                        </p>
                    </div>

                    <!-- Card 4: Nilai -->
                    <div @click="modalNilai = true" 
                         class="bg-white rounded-3xl p-5 sm:p-6 lg:p-7 shadow-soft border border-slate-100/90 hover:border-amber-300 hover:shadow-lg transition-all duration-200 active:scale-97 cursor-pointer flex flex-col items-center justify-center text-center group">
                        
                        <!-- Icon Box (Light Amber) -->
                        <div class="w-14 h-14 sm:w-16 sm:h-16 lg:w-18 lg:h-18 rounded-2xl bg-amber-50/90 text-amber-600 flex items-center justify-center mb-3 sm:mb-4 group-hover:scale-110 transition-transform">
                            <i data-lucide="file-signature" class="w-7 h-7 sm:w-8 sm:h-8 lg:w-9 lg:h-9 stroke-[2.2]"></i>
                        </div>

                        <h3 class="text-sm sm:text-base lg:text-lg font-black text-slate-800 group-hover:text-amber-600 transition-colors">
                            Nilai
                        </h3>
                        <!-- Badge: Tugas & PTS -->
                        <span class="inline-block bg-amber-50 text-amber-800 font-black text-[10px] sm:text-xs px-3 py-1 rounded-full mt-1.5 border border-amber-100 shadow-2xs">
                            Tugas & PTS
                        </span>
                    </div>

                </div>


                <!-- ============================================================= -->
                <!-- 3. DESKTOP ONLY PANELS (Membuat Tampilan Desktop Luas & Profesional) -->
                <!-- ============================================================= -->
                <div class="hidden lg:grid grid-cols-12 gap-6 pt-2">
                    
                    <!-- Left: Jadwal Mengajar Hari Ini Full Table -->
                    <div class="col-span-7 bg-white rounded-3xl p-6 shadow-soft border border-slate-100 space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <i data-lucide="calendar-check" class="w-4 h-4"></i>
                                </div>
                                <h4 class="font-extrabold text-sm text-slate-900">Jadwal Kelas Hari Ini</h4>
                            </div>
                            <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">3 Sesi Mengajar</span>
                        </div>

                        <div class="space-y-2.5 text-xs">
                            <div class="p-3.5 rounded-2xl bg-emerald-50/80 border border-emerald-200 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <div>
                                        <p class="font-black text-slate-900">07:30 - 09:45 WITA</p>
                                        <p class="text-slate-600 text-[11px]">XI RPL 2 • Pemrograman Web & Mobile</p>
                                    </div>
                                </div>
                                <span class="bg-emerald-600 text-white font-black text-[10px] px-2.5 py-1 rounded-full uppercase">Sesi Berjalan</span>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <span class="w-2.5 h-2.5 rounded-full bg-slate-300"></span>
                                    <div>
                                        <p class="font-black text-slate-900">10:15 - 12:30 WITA</p>
                                        <p class="text-slate-600 text-[11px]">XI RPL 1 • Basis Data Relasional</p>
                                    </div>
                                </div>
                                <span class="bg-slate-200 text-slate-700 font-extrabold text-[10px] px-2.5 py-1 rounded-full uppercase">Mendatang</span>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <span class="w-2.5 h-2.5 rounded-full bg-slate-300"></span>
                                    <div>
                                        <p class="font-black text-slate-900">13:15 - 15:30 WITA</p>
                                        <p class="text-slate-600 text-[11px]">XII RPL 1 • PBO Lanjutan</p>
                                    </div>
                                </div>
                                <span class="bg-slate-200 text-slate-700 font-extrabold text-[10px] px-2.5 py-1 rounded-full uppercase">Mendatang</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Info Cepat Guru & Jurnal -->
                    <div class="col-span-5 bg-white rounded-3xl p-6 shadow-soft border border-slate-100 space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                    <i data-lucide="book-open" class="w-4 h-4"></i>
                                </div>
                                <h4 class="font-extrabold text-sm text-slate-900">Jurnal & Agenda Guru</h4>
                            </div>
                            <span class="text-xs font-bold text-slate-400">RPL</span>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div class="bg-slate-50 rounded-2xl p-3.5 border border-slate-100 space-y-1.5">
                                <span class="text-slate-400 block text-[10px] uppercase font-bold">Materi Aktif</span>
                                <p class="font-extrabold text-slate-800">Implementasi Layout Responsif Tailwind CSS</p>
                                <p class="text-slate-500 text-[11px]">Siswa sedang praktik mandiri membuat interface dashboard mobile & desktop.</p>
                            </div>

                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div class="bg-blue-50/70 rounded-2xl p-3 border border-blue-100 text-center">
                                    <span class="text-[10px] uppercase font-bold text-blue-600">Presensi Siswa</span>
                                    <p class="text-lg font-black text-blue-900 mt-0.5">34 / 36</p>
                                    <span class="text-[10px] text-blue-600 font-bold">94% Hadir</span>
                                </div>
                                <div class="bg-amber-50/70 rounded-2xl p-3 border border-amber-100 text-center">
                                    <span class="text-[10px] uppercase font-bold text-amber-600">Tugas Masuk</span>
                                    <p class="text-lg font-black text-amber-900 mt-0.5">32 File</p>
                                    <span class="text-[10px] text-amber-700 font-bold">Perlu Review</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </main>


        <!-- ===================================================================== -->
        <!-- 4. BOTTOM NAVBAR MOBILE (Hanya Tampil di Mobile, Sesuai Mockup Gambar) -->
        <!-- ===================================================================== -->
        <nav class="lg:hidden fixed bottom-0 inset-x-0 mx-auto max-w-md bg-white/95 backdrop-blur-md border-t border-slate-200/80 py-2.5 px-12 flex items-center justify-between z-30 shadow-lg">
            
            <!-- Left: Beranda (Active Blue - Exact Mockup Match) -->
            <button type="button" 
                    @click="triggerToast('Anda berada di Beranda Dashboard Guru')"
                    class="flex flex-col items-center justify-center space-y-1 text-blue-600 font-bold transition-all cursor-pointer">
                <i data-lucide="home" class="w-6 h-6 stroke-[2.5]"></i>
                <span class="text-[11px]">Beranda</span>
            </button>

            <!-- Right: Profil (Inactive Gray - Exact Mockup Match) -->
            <button type="button" 
                    @click="modalProfil = true"
                    class="flex flex-col items-center justify-center space-y-1 text-slate-400 hover:text-blue-600 font-semibold transition-all cursor-pointer">
                <i data-lucide="user" class="w-6 h-6 stroke-[2]"></i>
                <span class="text-[11px]">Profil</span>
            </button>

        </nav>

    </div>


    <!-- ========================================================================= -->
    <!-- MODAL 1: DETAIL MAPEL GURU                                                -->
    <!-- ========================================================================= -->
    <div x-show="modalMapel" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="modalMapel = false" 
             x-show="modalMapel" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                        <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Mata Pelajaran Diampu</h3>
                        <p class="text-[11px] text-slate-400">Tahun Ajaran 2026/2027 Ganjil</p>
                    </div>
                </div>
                <button @click="modalMapel = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <div class="space-y-2.5 text-xs">
                <div class="p-3.5 rounded-2xl bg-blue-50/60 border border-blue-100 space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="font-extrabold text-blue-950">Pemrograman Web & Mobile</span>
                        <span class="bg-blue-600 text-white font-black text-[10px] px-2 py-0.5 rounded-full">XI RPL 2</span>
                    </div>
                    <p class="text-slate-500 text-[11px]">4 Jam Pelajaran / Minggu • Ruang Lab RPL 1</p>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="font-extrabold text-slate-800">Basis Data Relasional</span>
                        <span class="bg-slate-200 text-slate-700 font-black text-[10px] px-2 py-0.5 rounded-full">XI RPL 1</span>
                    </div>
                    <p class="text-slate-500 text-[11px]">3 Jam Pelajaran / Minggu • Ruang Lab RPL 2</p>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="font-extrabold text-slate-800">Pemrograman Berorientasi Objek</span>
                        <span class="bg-slate-200 text-slate-700 font-black text-[10px] px-2 py-0.5 rounded-full">XII RPL 1</span>
                    </div>
                    <p class="text-slate-500 text-[11px]">4 Jam Pelajaran / Minggu • Ruang Lab TeFa</p>
                </div>
            </div>

            <button type="button" @click="modalMapel = false" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 rounded-xl text-xs transition-colors">
                Tutup
            </button>
        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- MODAL 2: SESI PEMBELAJARAN AKTIF (XI RPL 2)                               -->
    <!-- ========================================================================= -->
    <div x-show="modalSesi" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="modalSesi = false" 
             x-show="modalSesi" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                        <i data-lucide="presentation" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Sesi Mengajar Berlangsung</h3>
                        <p class="text-[11px] text-emerald-600 font-bold">Status: Aktif di Kelas</p>
                    </div>
                </div>
                <button @click="modalSesi = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-100 space-y-2 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-emerald-800 font-bold">Rombel:</span>
                    <span class="font-extrabold text-emerald-950 bg-white px-2.5 py-0.5 rounded-full border border-emerald-200">XI RPL 2</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-emerald-800 font-bold">Materi:</span>
                    <span class="font-extrabold text-emerald-950">Slice UI Dashboard Tailwind CSS</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-emerald-800 font-bold">Waktu:</span>
                    <span class="font-bold text-emerald-900">Jam Ke-3 s.d. 5 (08:30 - 10:45)</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-emerald-800 font-bold">Siswa Hadir:</span>
                    <span class="font-black text-emerald-700">34 / 36 Siswa (94%)</span>
                </div>
            </div>

            <div class="flex items-center space-x-2 pt-1">
                <button type="button" @click="triggerToast('Jurnal mengajar tersimpan!'); modalSesi = false;" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold py-2.5 rounded-xl text-xs transition-colors">
                    Selesaikan Sesi Ini
                </button>
                <button type="button" @click="modalSesi = false" class="px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 rounded-xl text-xs transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- MODAL 3: JADWAL MENGAJAR HARI INI                                         -->
    <!-- ========================================================================= -->
    <div x-show="modalJadwal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="modalJadwal = false" 
             x-show="modalJadwal" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <i data-lucide="calendar" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Jadwal Mengajar Hari Ini</h3>
                        <p class="text-[11px] text-slate-400">Total: 3 Rombongan Belajar</p>
                    </div>
                </div>
                <button @click="modalJadwal = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <div class="space-y-2.5 text-xs">
                <div class="p-3 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-between">
                    <div>
                        <p class="font-extrabold text-slate-900">07:30 - 09:00 WITA</p>
                        <p class="text-[11px] text-slate-600 mt-0.5">XI RPL 2 • Web & Mobile</p>
                    </div>
                    <span class="bg-emerald-600 text-white font-extrabold text-[10px] px-2.5 py-1 rounded-full uppercase">Sedang Berjalan</span>
                </div>

                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="font-extrabold text-slate-900">09:30 - 11:45 WITA</p>
                        <p class="text-[11px] text-slate-600 mt-0.5">XI RPL 1 • Basis Data</p>
                    </div>
                    <span class="bg-slate-200 text-slate-700 font-extrabold text-[10px] px-2.5 py-1 rounded-full uppercase">Mendatang</span>
                </div>

                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="font-extrabold text-slate-900">12:30 - 14:45 WITA</p>
                        <p class="text-[11px] text-slate-600 mt-0.5">XII RPL 1 • PBO Lanjutan</p>
                    </div>
                    <span class="bg-slate-200 text-slate-700 font-extrabold text-[10px] px-2.5 py-1 rounded-full uppercase">Mendatang</span>
                </div>
            </div>

            <button type="button" @click="modalJadwal = false" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 rounded-xl text-xs transition-colors">
                Tutup
            </button>
        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- MODAL 4: INPUT & REKAP NILAI (TUGAS & PTS)                                -->
    <!-- ========================================================================= -->
    <div x-show="modalNilai" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="modalNilai = false" 
             x-show="modalNilai" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                        <i data-lucide="file-signature" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Penilaian Tugas & PTS</h3>
                        <p class="text-[11px] text-slate-400">Kurikulum Merdeka 2026</p>
                    </div>
                </div>
                <button @click="modalNilai = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <div class="space-y-2.5 text-xs">
                <div class="p-3 rounded-2xl bg-amber-50/70 border border-amber-200 flex items-center justify-between">
                    <div>
                        <p class="font-extrabold text-slate-900">Tugas 1: Slice UI Figma</p>
                        <p class="text-[11px] text-amber-800 mt-0.5">Terkumpul 32 dari 36 siswa</p>
                    </div>
                    <span class="bg-amber-600 text-white font-extrabold text-[10px] px-2.5 py-1 rounded-full">Perlu Nilai (4)</span>
                </div>

                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="font-extrabold text-slate-900">PTS Ganjil: Teori & Praktik</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Jadwal: Minggu Ke-3 Oktober</p>
                    </div>
                    <span class="bg-slate-200 text-slate-700 font-extrabold text-[10px] px-2.5 py-1 rounded-full">Bank Soal Siap</span>
                </div>
            </div>

            <button type="button" @click="triggerToast('Data nilai berhasil diperbarui!'); modalNilai = false;" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl text-xs transition-colors">
                Kelola Nilai Selengkapnya
            </button>
        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- MODAL 5: PROFIL GURU & LOGOUT                                             -->
    <!-- ========================================================================= -->
    <div x-show="modalProfil" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="modalProfil = false" 
             x-show="modalProfil" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 text-center space-y-4">
            
            <div class="w-16 h-16 rounded-full bg-blue-600 text-white flex items-center justify-center mx-auto shadow-md shadow-blue-500/20">
                <i data-lucide="user" class="w-8 h-8"></i>
            </div>

            <div>
                <h3 class="text-base font-black text-slate-900" x-text="guru.nama">Nama Guru</h3>
                <p class="text-xs font-bold text-blue-600 mt-0.5" x-text="guru.role">Guru Mata Pelajaran • RPL</p>
                <p class="text-[11px] text-slate-400 mt-1" x-text="'NIP: ' + guru.nip">NIP: 19850314 201001 1 015</p>
            </div>

            <div class="bg-slate-50 rounded-2xl p-3.5 text-xs text-left space-y-2 border border-slate-100">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Mata Pelajaran:</span>
                    <span class="font-bold text-slate-800" x-text="guru.mapelUtama">Web & Mobile</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Status Akun:</span>
                    <span class="font-bold text-emerald-600">Guru Aktif Bertugas</span>
                </div>
            </div>

            <div class="space-y-2 pt-1">
                <button type="button" @click="modalProfil = false" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 rounded-xl text-xs transition-colors">
                    Tutup
                </button>
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold py-2.5 rounded-xl text-xs transition-colors flex items-center justify-center space-x-1.5">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    <span>Keluar Akun</span>
                    </button>
                </form>
            </div>
        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- TOAST NOTIFICATION BANNER                                                 -->
    <!-- ========================================================================= -->
    <div x-show="showToast" 
         x-cloak 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-3"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-3"
         class="fixed bottom-20 lg:bottom-8 inset-x-0 mx-auto max-w-sm px-4 z-50">
        <div class="rounded-2xl p-3.5 shadow-2xl flex items-center space-x-3 text-xs bg-slate-900/95 text-white border border-slate-700 backdrop-blur-md">
            <div class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                <i data-lucide="check" class="w-3.5 h-3.5"></i>
            </div>
            <p class="font-bold flex-1" x-text="toastMsg"></p>
            <button @click="showToast = false" class="text-white/60 hover:text-white cursor-pointer">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
            </button>
        </div>
    </div>

</body>
</html>
