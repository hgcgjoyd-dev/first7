<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Guru BK - SMK TI Bali Global Badung</title>

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
                            600: '#2563eb', // Royal Blue matching mockup
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

    <!-- Alpine.js for interactive state -->
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
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="bg-[#F4F6FB] text-slate-800 antialiased min-h-screen flex flex-col selection:bg-blue-600 selection:text-white"
      x-data="{
          activeTab: 'beranda', // 'beranda', 'absensi', 'konseling', 'pelanggaran', 'profil'
          viewMode: 'responsive', // 'responsive' or 'mobile_frame'
          liveTime: '07:30',
          liveFullTime: '07:30:00 WITA',
          greetingText: 'Selamat Pagi,',
          
          // Modals
          modalAbsensi: false,
          modalKonseling: false,
          modalPelanggaran: false,
          modalProfil: false,
          showToast: false,
          toastMessage: '',

          // Filter & Search State
          searchStudent: '',
          selectedKelas: 'Semua',

          // Data Guru BK (Mengikuti Session Login atau Default)
          guru: {
              nama: '{{ session('guru_nama', 'Dra. Ni Luh Suastini, S.Pd') }}',
              nip: '{{ session('guru_nip', '19780512 200501 2 008') }}',
              jabatan: '{{ session('guru_jabatan', 'Koordinator Guru BK & Konselor Sekolah') }}',
              email: '{{ session('guru_email', 'guru.bk@smktibaliglobal.sch.id') }}',
              telepon: '0812-3456-7890'
          },

          // Dummy Data Siswa Pelanggaran & Konseling
          pelanggaranList: [
              { id: 1, nama: 'Wahyu Pratama', kelas: 'XI PPLG 1', poin: 15, jenis: 'Keterlambatan Hadir (15 menit)', tanggal: 'Hari Ini, 07:15 WITA', status: 'Binaan' },
              { id: 2, nama: 'I Made Raditya', kelas: 'X DKV 2', poin: 20, jenis: 'Atribut Seragam Tidak Lengkap', tanggal: 'Kemarin, 08:00 WITA', status: 'Teguran' },
              { id: 3, nama: 'Ketut Agus Surya', kelas: 'XII TJAT 1', poin: 35, jenis: 'Keluar Area Sekolah Tanpa Izin', tanggal: '06 Okt 2026', status: 'Panggilan Ortu' },
              { id: 4, nama: 'Ni Kadek Sintya', kelas: 'XI PPLG 2', poin: 10, jenis: 'Terlambat Masuk Jam Pertama', tanggal: '05 Okt 2026', status: 'Binaan' }
          ],

          konselingList: [
              { id: 101, nama: 'Wahyu Pratama', kelas: 'XI PPLG 1', topik: 'Evaluasi Kedisiplinan & Bimbingan Minat', jam: '08:30 WITA', status: 'Dijadwalkan Hari Ini' },
              { id: 102, nama: 'Ni Putu Maharani', kelas: 'XII DKV 1', topik: 'Konseling Karir & Lanjutan Studi Kuliah', jam: '10:15 WITA', status: 'Selesai' },
              { id: 103, nama: 'I Gede Budiarta', kelas: 'X PPLG 1', topik: 'Adaptasi Lingkungan Baru Sekolah', jam: '13:00 WITA', status: 'Menunggu' }
          ],

          // Form Input Pelanggaran
          formPelanggaran: {
              nama: '',
              kelas: 'XI PPLG 1',
              jenis: '',
              poin: 10,
              catatan: ''
          },

          // Form Input Konseling
          formKonseling: {
              nama: '',
              kelas: 'XI PPLG 1',
              topik: '',
              jam: '09:00 WITA',
              metode: 'Tatap Muka di Ruang BK'
          },

          init() {
              this.updateClock();
              setInterval(() => this.updateClock(), 1000);
              this.$nextTick(() => {
                  if (window.lucide) lucide.createIcons();
              });
          },

          updateClock() {
              const now = new Date();
              const hr = now.getHours();
              const mn = String(now.getMinutes()).padStart(2, '0');
              const sc = String(now.getSeconds()).padStart(2, '0');
              
              this.liveTime = `${String(hr).padStart(2, '0')}:${mn}`;
              this.liveFullTime = `${String(hr).padStart(2, '0')}:${mn}:${sc} WITA`;

              if (hr >= 4 && hr < 11) this.greetingText = 'Selamat Pagi,';
              else if (hr >= 11 && hr < 15) this.greetingText = 'Selamat Siang,';
              else if (hr >= 15 && hr < 18) this.greetingText = 'Selamat Sore,';
              else this.greetingText = 'Selamat Malam,';
          },

          openAction(action) {
              if (action === 'absensi') this.modalAbsensi = true;
              if (action === 'konseling') this.modalKonseling = true;
              if (action === 'pelanggaran') this.modalPelanggaran = true;
              this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
          },

          simpanPelanggaran() {
              if (!this.formPelanggaran.nama || !this.formPelanggaran.jenis) {
                  alert('Harap lengkapi nama siswa dan jenis pelanggaran.');
                  return;
              }
              this.pelanggaranList.unshift({
                  id: Date.now(),
                  nama: this.formPelanggaran.nama,
                  kelas: this.formPelanggaran.kelas,
                  poin: parseInt(this.formPelanggaran.poin) || 10,
                  jenis: this.formPelanggaran.jenis,
                  tanggal: 'Baru saja',
                  status: 'Teguran'
              });
              this.formPelanggaran.nama = '';
              this.formPelanggaran.jenis = '';
              this.modalPelanggaran = false;
              this.triggerToast('Catatan pelanggaran siswa berhasil disimpan ke buku disiplin!');
          },

          simpanKonseling() {
              if (!this.formKonseling.nama || !this.formKonseling.topik) {
                  alert('Harap lengkapi nama siswa dan topik bimbingan.');
                  return;
              }
              this.konselingList.unshift({
                  id: Date.now(),
                  nama: this.formKonseling.nama,
                  kelas: this.formKonseling.kelas,
                  topik: this.formKonseling.topik,
                  jam: this.formKonseling.jam,
                  status: 'Dijadwalkan Hari Ini'
              });
              this.formKonseling.nama = '';
              this.formKonseling.topik = '';
              this.modalKonseling = false;
              this.triggerToast('Jadwal bimbingan konseling baru berhasil ditambahkan!');
          },

          triggerToast(msg) {
              this.toastMessage = msg;
              this.showToast = true;
              this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
              setTimeout(() => { this.showToast = false; }, 4000);
          }
      }">

    <!-- ========================================================================= -->
    <!-- DESKTOP TOP BAR (Only Visible on Large Screen in Responsive Mode)        -->
    <!-- ========================================================================= -->
    <header class="hidden lg:block bg-white border-b border-slate-200/80 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <!-- Brand Left -->
            <div class="flex items-center space-x-3">
                <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK TI Bali Global Badung" class="h-10 w-auto object-contain">
                <div>
                    <h2 class="text-sm font-black text-slate-900 tracking-tight uppercase leading-none">SMK TI BALI GLOBAL BADUNG</h2>
                    <span class="text-[11px] font-bold text-blue-600 tracking-wider">PORTAL GURU BK & KESISWAAN</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex items-center space-x-1">
                <button @click="activeTab = 'beranda'" 
                        :class="activeTab === 'beranda' ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                        class="px-4 py-2 rounded-xl text-xs transition-colors flex items-center space-x-2">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    <span>Beranda BK</span>
                </button>
                <button @click="openAction('absensi')" 
                        class="px-4 py-2 rounded-xl text-xs text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium transition-colors flex items-center space-x-2">
                    <i data-lucide="user-check" class="w-4 h-4 text-blue-600"></i>
                    <span>Absensi Siswa</span>
                </button>
                <button @click="openAction('konseling')" 
                        class="px-4 py-2 rounded-xl text-xs text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium transition-colors flex items-center space-x-2">
                    <i data-lucide="users" class="w-4 h-4 text-blue-600"></i>
                    <span>Konseling & BK</span>
                </button>
                <button @click="openAction('pelanggaran')" 
                        class="px-4 py-2 rounded-xl text-xs text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium transition-colors flex items-center space-x-2">
                    <i data-lucide="clipboard-list" class="w-4 h-4 text-blue-600"></i>
                    <span>Pelanggaran Siswa</span>
                </button>
            </nav>

            <!-- Right Controls: Live Clock & Teacher Profile Avatar -->
            <div class="flex items-center space-x-3">
                <!-- Live Clock Pill -->
                <div class="bg-blue-50 border border-blue-200/60 text-blue-700 px-3.5 py-1.5 rounded-full text-xs font-bold flex items-center space-x-2">
                    <i data-lucide="clock" class="w-3.5 h-3.5 text-blue-600 animate-pulse"></i>
                    <span x-text="liveFullTime">07:30:00 WITA</span>
                </div>

                <!-- Teacher Avatar & Info -->
                <div @click="modalProfil = true" class="flex items-center space-x-2 pl-2 border-l border-slate-200 cursor-pointer group">
                    <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold shadow-xs group-hover:scale-105 transition-transform">
                        <i data-lucide="user" class="w-5 h-5"></i>
                    </div>
                    <div class="text-left hidden xl:block">
                        <p class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition-colors" x-text="guru.nama">Dra. Ni Luh Suastini, S.Pd</p>
                        <p class="text-[10px] text-slate-500 font-medium">Koordinator BK</p>
                    </div>
                </div>

                <!-- Back to Student Portal Link -->
                <a href="{{ route('dashboard') }}" 
                   title="Beralih ke Dashboard Siswa" 
                   class="p-2 rounded-xl text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                    <i data-lucide="arrow-right-left" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </header>


    <!-- ========================================================================= -->
    <!-- MAIN CONTENT CONTAINER (Fully Responsive Mobile + Desktop)               -->
    <!-- ========================================================================= -->
    <main class="flex-1 w-full pb-24 lg:pb-12">

        <!-- ===================================================================== -->
        <!-- 1. HERO HEADER (Exact Royal Blue Gradient with Curved Bottom)          -->
        <!-- ===================================================================== -->
        <div class="relative bg-gradient-to-b from-[#1e40af] via-[#2563eb] to-[#1d4ed8] text-white rounded-b-[40px] sm:rounded-b-[48px] lg:rounded-b-[56px] pt-6 sm:pt-8 pb-14 sm:pb-16 px-5 sm:px-8 shadow-xl shadow-blue-600/15 overflow-hidden">
            
            <!-- Ambient Glowing Lighting Circles -->
            <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-1/4 w-72 h-72 rounded-full bg-blue-400/20 blur-3xl pointer-events-none"></div>

            <div class="max-w-4xl mx-auto relative z-10 space-y-6">

                <!-- Top Center: Logo & SMK TI BALI GLOBAL BADUNG Text (Mockup Match) -->
                <div class="flex flex-col items-center justify-center text-center space-y-2">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-white p-1.5 shadow-lg shadow-black/10 flex items-center justify-center">
                        <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK TI Bali Global Badung" class="w-full h-full object-contain">
                    </div>
                    <h2 class="text-sm sm:text-base font-extrabold tracking-wider text-white uppercase text-center drop-shadow-xs">
                        SMK TI BALI GLOBAL BADUNG
                    </h2>
                </div>

                <!-- Second Row: Greeting on Left & Teacher Avatar on Right (Mockup Match) -->
                <div class="flex items-center justify-between pt-2">
                    <!-- Left: Greeting & Teacher Name -->
                    <div>
                        <p class="text-xs sm:text-sm text-blue-100 font-medium tracking-wide" x-text="greetingText">
                            Selamat Pagi,
                        </p>
                        <h1 class="text-xl sm:text-3xl font-black text-white tracking-tight mt-0.5" x-text="guru.nama">
                            Nama Guru BK
                        </h1>
                        <!-- Desktop Subtitle Badge -->
                        <div class="hidden lg:flex items-center space-x-2 mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white backdrop-blur-md">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>
                                BK Aktif • Ruang Bimbingan Konseling Lt. 2
                            </span>
                        </div>
                    </div>

                    <!-- Right: White Circular Avatar with User Icon (Mockup Match) -->
                    <button @click="modalProfil = true" 
                            title="Buka Profil Guru BK"
                            class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-white text-blue-600 flex items-center justify-center shadow-lg hover:scale-105 active:scale-95 transition-all cursor-pointer shrink-0">
                        <i data-lucide="user" class="w-6 h-6 sm:w-7 sm:h-7 stroke-[2.2]"></i>
                    </button>
                </div>
            </div>

            <!-- Floating Clock Pill (Anchored at the bottom center of the hero card) -->
            <div class="absolute -bottom-5 left-1/2 -translate-x-1/2 z-20">
                <div class="inline-flex items-center space-x-2 bg-white text-blue-700 px-6 py-2.5 rounded-full shadow-floating border border-blue-100/80 font-black text-xs sm:text-sm select-none hover:scale-102 transition-transform">
                    <i data-lucide="clock" class="w-4 h-4 text-blue-600"></i>
                    <span x-text="'JAM ' + liveTime">JAM 07:30</span>
                </div>
            </div>
        </div>


        <!-- ===================================================================== -->
        <!-- 2. MAIN BODY / ACTION CARDS SECTION                                    -->
        <!-- ===================================================================== -->
        <div class="max-w-4xl mx-auto px-5 sm:px-8 mt-10 sm:mt-12 space-y-4 sm:space-y-6">

            <!-- ================================================================= -->
            <!-- ROW 1: 2 MAIN CARDS (Absensi Siswa & Konseling BK) - MOCKUP MATCH  -->
            <!-- ================================================================= -->
            <div class="grid grid-cols-2 gap-4 sm:gap-6">
                
                <!-- CARD 1: Absensi Siswa -->
                <div @click="openAction('absensi')" 
                     class="bg-white rounded-[28px] sm:rounded-3xl p-6 sm:p-8 shadow-soft border border-slate-200/70 hover:border-blue-300 hover:shadow-lg transition-all duration-200 active:scale-97 cursor-pointer flex flex-col items-center justify-center text-center group">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-blue-50/80 group-hover:bg-blue-100/90 text-blue-600 flex items-center justify-center transition-colors mb-3 sm:mb-4">
                        <i data-lucide="user-check" class="w-9 h-9 sm:w-11 sm:h-11 stroke-[2.2] group-hover:scale-110 transition-transform"></i>
                    </div>
                    <h3 class="text-sm sm:text-lg font-black text-slate-800 group-hover:text-blue-600 transition-colors">
                        Absensi Siswa
                    </h3>
                    <p class="hidden sm:block text-xs text-slate-400 mt-1">Rekap kehadiran harian & izin kelas</p>
                </div>

                <!-- CARD 2: Konseling & BK -->
                <div @click="openAction('konseling')" 
                     class="bg-white rounded-[28px] sm:rounded-3xl p-6 sm:p-8 shadow-soft border border-slate-200/70 hover:border-blue-300 hover:shadow-lg transition-all duration-200 active:scale-97 cursor-pointer flex flex-col items-center justify-center text-center group">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-blue-50/80 group-hover:bg-blue-100/90 text-blue-600 flex items-center justify-center transition-colors mb-3 sm:mb-4">
                        <i data-lucide="users" class="w-9 h-9 sm:w-11 sm:h-11 stroke-[2.2] group-hover:scale-110 transition-transform"></i>
                    </div>
                    <h3 class="text-sm sm:text-lg font-black text-slate-800 group-hover:text-blue-600 transition-colors">
                        Konseling & BK
                    </h3>
                    <p class="hidden sm:block text-xs text-slate-400 mt-1">Jadwal bimbingan & konseling siswa</p>
                </div>
            </div>


            <!-- ================================================================= -->
            <!-- ROW 2: Pelanggaran Siswa Card (Mockup Match)                       -->
            <!-- ================================================================= -->
            <div @click="openAction('pelanggaran')" 
                 class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-6 shadow-soft border border-slate-200/70 hover:border-blue-300 hover:shadow-lg transition-all duration-200 active:scale-98 cursor-pointer flex items-center justify-center space-x-3 group">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <i data-lucide="clipboard-list" class="w-6 h-6 sm:w-7 sm:h-7 stroke-[2.2]"></i>
                </div>
                <h3 class="text-base sm:text-lg font-black text-slate-800 group-hover:text-blue-600 transition-colors">
                    Pelanggaran Siswa
                </h3>
            </div>


            <!-- ================================================================= -->
            <!-- ROW 3: Siswa Hadir: 95% Pill Card (Mockup Match)                  -->
            <!-- ================================================================= -->
            <div @click="openAction('absensi')" 
                 class="bg-white rounded-2xl sm:rounded-full py-4 px-6 shadow-soft border border-slate-200/70 hover:border-blue-300 hover:shadow-md transition-all active:scale-98 cursor-pointer flex items-center justify-center space-x-3 group">
                <i data-lucide="user-check" class="w-5 h-5 text-blue-600 shrink-0 group-hover:scale-110 transition-transform"></i>
                <span class="text-base sm:text-lg font-black text-blue-700 tracking-tight">
                    Siswa Hadir: 95%
                </span>
            </div>


            <!-- ================================================================= -->
            <!-- DESKTOP ENHANCEMENT: QUICK OVERVIEW LIST & MANAGEMENT TABLES      -->
            <!-- ================================================================= -->
            <div class="hidden lg:grid grid-cols-2 gap-6 pt-4">
                
                <!-- Quick Panel: Sesi Konseling Hari Ini -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-soft space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                                <i data-lucide="calendar" class="w-4 h-4"></i>
                            </div>
                            <h4 class="font-bold text-sm text-slate-900">Jadwal Konseling Hari Ini</h4>
                        </div>
                        <button @click="openAction('konseling')" class="text-xs font-bold text-blue-600 hover:underline">
                            + Tambah Sesi
                        </button>
                    </div>

                    <div class="space-y-2.5">
                        <template x-for="item in konselingList" :key="item.id">
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between hover:bg-blue-50/50 transition-colors">
                                <div>
                                    <p class="font-bold text-xs text-slate-900" x-text="item.nama + ' (' + item.kelas + ')'"></p>
                                    <p class="text-[11px] text-slate-500 mt-0.5" x-text="item.topik"></p>
                                </div>
                                <span class="bg-blue-100 text-blue-700 font-extrabold text-[10px] px-2.5 py-1 rounded-full whitespace-nowrap" x-text="item.jam"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Quick Panel: Catatan Pelanggaran Terbaru -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-soft space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold">
                                <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                            </div>
                            <h4 class="font-bold text-sm text-slate-900">Catatan Pelanggaran Siswa</h4>
                        </div>
                        <button @click="openAction('pelanggaran')" class="text-xs font-bold text-rose-600 hover:underline">
                            + Input Sanksi
                        </button>
                    </div>

                    <div class="space-y-2.5">
                        <template x-for="item in pelanggaranList.slice(0, 3)" :key="item.id">
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between hover:bg-rose-50/50 transition-colors">
                                <div>
                                    <p class="font-bold text-xs text-slate-900" x-text="item.nama + ' (' + item.kelas + ')'"></p>
                                    <p class="text-[11px] text-slate-500 mt-0.5" x-text="item.jenis"></p>
                                </div>
                                <span class="bg-rose-100 text-rose-700 font-extrabold text-[10px] px-2.5 py-1 rounded-full whitespace-nowrap" x-text="'+' + item.poin + ' Poin'"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Quick Switcher to Student Dashboard (Helpful footer on both mobile & desktop) -->
            <div class="pt-4 text-center">
                <a href="{{ route('dashboard') }}" 
                   class="inline-flex items-center space-x-2 text-xs font-bold text-slate-400 hover:text-blue-600 transition-colors py-2 px-4 rounded-xl hover:bg-slate-200/50">
                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    <span>Kembali ke Portal Siswa SMK TI Bali Global Badung</span>
                </a>
            </div>
        </div>
    </main>


    <!-- ========================================================================= -->
    <!-- 3. MOBILE BOTTOM NAVIGATION (Fixed at bottom - Mockup Match)              -->
    <!-- ========================================================================= -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-slate-200/80 py-2.5 px-8 flex items-center justify-around z-30 shadow-lg">
        <!-- Tab 1: Beranda (Active Blue) -->
        <button @click="activeTab = 'beranda'" 
                :class="activeTab === 'beranda' ? 'text-blue-600 font-bold' : 'text-slate-400 hover:text-slate-600'"
                class="flex flex-col items-center justify-center space-y-1 transition-all">
            <i data-lucide="home" class="w-6 h-6 stroke-[2.2]"></i>
            <span class="text-[11px]">Beranda</span>
        </button>

        <!-- Tab 2: Profile (Inactive Gray) -->
        <button @click="modalProfil = true" 
                :class="modalProfil ? 'text-blue-600 font-bold' : 'text-slate-400 hover:text-slate-600'"
                class="flex flex-col items-center justify-center space-y-1 transition-all">
            <i data-lucide="user" class="w-6 h-6 stroke-[2.2]"></i>
            <span class="text-[11px]">Profile</span>
        </button>
    </nav>


    <!-- ========================================================================= -->
    <!-- MODAL 1: REKAP ABSENSI SISWA                                              -->
    <!-- ========================================================================= -->
    <div x-show="modalAbsensi" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="modalAbsensi = false" 
             x-show="modalAbsensi" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-100 space-y-6">
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center">
                        <i data-lucide="user-check" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900">Rekap Presensi Siswa</h3>
                        <p class="text-xs text-slate-500">Tingkat Kehadiran Hari Ini: <span class="font-bold text-blue-600">95%</span></p>
                    </div>
                </div>
                <button @click="modalAbsensi = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-4 gap-2 text-center text-xs">
                <div class="p-3 rounded-2xl bg-emerald-50 text-emerald-800">
                    <p class="text-[10px] font-bold text-emerald-600 uppercase">Hadir</p>
                    <p class="text-lg font-black mt-0.5">308</p>
                </div>
                <div class="p-3 rounded-2xl bg-amber-50 text-amber-800">
                    <p class="text-[10px] font-bold text-amber-600 uppercase">Telat</p>
                    <p class="text-lg font-black mt-0.5">4</p>
                </div>
                <div class="p-3 rounded-2xl bg-blue-50 text-blue-800">
                    <p class="text-[10px] font-bold text-blue-600 uppercase">Izin</p>
                    <p class="text-lg font-black mt-0.5">12</p>
                </div>
                <div class="p-3 rounded-2xl bg-rose-50 text-rose-800">
                    <p class="text-[10px] font-bold text-rose-600 uppercase">Alpa</p>
                    <p class="text-lg font-black mt-0.5">0</p>
                </div>
            </div>

            <!-- Daftar Siswa -->
            <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                    <div class="flex items-center space-x-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <div>
                            <p class="font-bold text-slate-900">Wahyu Pratama</p>
                            <p class="text-[11px] text-slate-500">XI PPLG 1 • Masuk 07:06 WITA</p>
                        </div>
                    </div>
                    <span class="bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded-md text-[10px]">Tepat Waktu</span>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                    <div class="flex items-center space-x-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        <div>
                            <p class="font-bold text-slate-900">I Made Raditya</p>
                            <p class="text-[11px] text-slate-500">X DKV 2 • Masuk 07:18 WITA</p>
                        </div>
                    </div>
                    <span class="bg-amber-100 text-amber-800 font-bold px-2 py-0.5 rounded-md text-[10px]">Terlambat</span>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                    <div class="flex items-center space-x-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                        <div>
                            <p class="font-bold text-slate-900">Ketut Agus Surya</p>
                            <p class="text-[11px] text-slate-500">XII TJAT 1 • Surat Izin Orang Tua</p>
                        </div>
                    </div>
                    <span class="bg-blue-100 text-blue-800 font-bold px-2 py-0.5 rounded-md text-[10px]">Izin Resmi</span>
                </div>
            </div>

            <button @click="modalAbsensi = false" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-2xl transition-all">
                Tutup Rekap
            </button>
        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- MODAL 2: KONSELING & BK (Jadwal & Bimbingan)                              -->
    <!-- ========================================================================= -->
    <div x-show="modalKonseling" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="modalKonseling = false" 
             x-show="modalKonseling" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 space-y-5">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center">
                        <i data-lucide="users" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900">Bimbingan & Konseling BK</h3>
                        <p class="text-xs text-slate-500">Formulir Panggilan / Sesi Baru</p>
                    </div>
                </div>
                <button @click="modalKonseling = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form @submit.prevent="simpanKonseling" class="space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Siswa</label>
                    <input type="text" x-model="formKonseling.nama" placeholder="Contoh: Wahyu Pratama" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kelas Siswa</label>
                        <select x-model="formKonseling.kelas" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                            <option value="XI PPLG 1">XI PPLG 1</option>
                            <option value="XI PPLG 2">XI PPLG 2</option>
                            <option value="X DKV 1">X DKV 1</option>
                            <option value="XII TJAT 1">XII TJAT 1</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Waktu Sesi</label>
                        <input type="text" x-model="formKonseling.jam" placeholder="08:30 WITA" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Topik Bimbingan / Konseling</label>
                    <textarea x-model="formKonseling.topik" rows="3" placeholder="Tuliskan topik konseling, misal: pembinaan kedisiplinan atau konsultasi karir..." required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-2xl transition-all shadow-md shadow-blue-500/20">
                    Jadwalkan Sesi Konseling
                </button>
            </form>
        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- MODAL 3: INPUT PELANGGARAN SISWA                                          -->
    <!-- ========================================================================= -->
    <div x-show="modalPelanggaran" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="modalPelanggaran = false" 
             x-show="modalPelanggaran" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 space-y-5">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center">
                        <i data-lucide="clipboard-list" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900">Catat Pelanggaran Siswa</h3>
                        <p class="text-xs text-slate-500">Buku Tata Tertib & Sanksi Disiplin</p>
                    </div>
                </div>
                <button @click="modalPelanggaran = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form @submit.prevent="simpanPelanggaran" class="space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Siswa</label>
                    <input type="text" x-model="formPelanggaran.nama" placeholder="Contoh: Wahyu Pratama" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-rose-500 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kelas</label>
                        <select x-model="formPelanggaran.kelas" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-rose-500 outline-none bg-white">
                            <option value="XI PPLG 1">XI PPLG 1</option>
                            <option value="XI PPLG 2">XI PPLG 2</option>
                            <option value="X DKV 2">X DKV 2</option>
                            <option value="XII TJAT 1">XII TJAT 1</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Bobot Poin Sanksi</label>
                        <select x-model="formPelanggaran.poin" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-rose-500 outline-none bg-white font-bold text-rose-600">
                            <option value="5">5 Poin (Ringan)</option>
                            <option value="10">10 Poin (Sedang)</option>
                            <option value="15">15 Poin (Keterlambatan)</option>
                            <option value="25">25 Poin (Pelanggaran Serius)</option>
                            <option value="50">50 Poin (Panggilan Ortu)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jenis Pelanggaran</label>
                    <input type="text" x-model="formPelanggaran.jenis" placeholder="Contoh: Rambut tidak rapi / Terlambat" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-rose-500 outline-none">
                </div>

                <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold py-3 rounded-2xl transition-all shadow-md shadow-rose-500/20">
                    Simpan Catatan Pelanggaran
                </button>
            </form>
        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- MODAL 4: PROFIL GURU BK                                                   -->
    <!-- ========================================================================= -->
    <div x-show="modalProfil" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="modalProfil = false" 
             x-show="modalProfil" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-100 text-center space-y-5">
            
            <!-- Avatar -->
            <div class="w-20 h-20 rounded-full bg-blue-600 text-white flex items-center justify-center mx-auto shadow-lg shadow-blue-500/20">
                <i data-lucide="user" class="w-10 h-10"></i>
            </div>

            <div>
                <h3 class="text-lg font-black text-slate-900" x-text="guru.nama">Dra. Ni Luh Suastini, S.Pd</h3>
                <p class="text-xs font-bold text-blue-600 mt-0.5" x-text="guru.jabatan">Koordinator Guru BK & Konselor Sekolah</p>
                <p class="text-[11px] text-slate-400 mt-1" x-text="'NIP: ' + guru.nip">NIP: 19780512 200501 2 008</p>
            </div>

            <div class="bg-slate-50 rounded-2xl p-4 text-xs text-left space-y-2 border border-slate-100">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Email Sekolah:</span>
                    <span class="font-bold text-slate-800" x-text="guru.email"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Ruangan:</span>
                    <span class="font-bold text-slate-800">Ruang BK Lantai 2</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Status Kehadiran:</span>
                    <span class="font-bold text-emerald-600">Aktif Bertugas</span>
                </div>
            </div>

            <div class="flex space-x-2">
                <a href="{{ route('dashboard') }}" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 rounded-xl text-xs transition-colors text-center">
                    Dashboard Siswa
                </a>
                <button @click="modalProfil = false" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl text-xs transition-colors">
                    Tutup
                </button>
            </div>

            <div class="pt-2 border-t border-slate-100">
                <a href="{{ route('logout') }}" class="w-full bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold py-2.5 rounded-xl text-xs transition-colors flex items-center justify-center space-x-1.5">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    <span>Keluar Akun / Logout</span>
                </a>
            </div>
        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- TOAST NOTIFICATION                                                        -->
    <!-- ========================================================================= -->
    <div x-show="showToast" x-cloak 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed bottom-20 lg:bottom-6 right-4 sm:right-6 z-50 max-w-sm bg-slate-900 text-white px-4 py-3 rounded-2xl shadow-2xl flex items-center space-x-3 text-xs border border-slate-800">
        <div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
            <i data-lucide="check" class="w-3.5 h-3.5"></i>
        </div>
        <p class="font-medium" x-text="toastMessage"></p>
    </div>

</body>
</html>

