<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pilih Kelas - Absensi Siswa - SMK TI Bali Global Badung</title>

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
                            600: '#2563eb',
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
          searchQuery: '',
          selectedFilter: 'Semua', // 'Semua', 'PPLG', 'TJKT', 'DKV', 'BD'
          liveTimeWita: '',
          liveDateDay: 'Hari Ini',
          
          // Modal Detail Siswa Kelas
          selectedKelasData: null,
          modalDetailSiswa: false,
          searchSiswaInModal: '',
          filterStatusSiswa: 'Semua',
          showToast: false,
          toastMessage: '',

          // 16 Daftar Rombongan Belajar (Exact from Reference Image)
          kelasList: [
              // PPLG
              {
                  id: 1,
                  nama: 'X PPLG 1',
                  jurusan: 'PPLG',
                  sub: 'Pengembangan Perangkat Lunak & Gim',
                  status: 'Belum Diabsen',
                  siswaCount: 36,
                  wali: 'I Ketut Adi Suartama, M...',
                  iconType: 'laptop',
                  iconBg: 'bg-blue-50 text-blue-600',
                  badgeBg: 'bg-blue-100 text-blue-700'
              },
              {
                  id: 2,
                  nama: 'X PPLG 2',
                  jurusan: 'PPLG',
                  sub: 'Pengembangan Perangkat Lunak & Gim',
                  status: 'Belum Diabsen',
                  siswaCount: 35,
                  wali: 'Ni Putu Desi Indrayani,...',
                  iconType: 'laptop',
                  iconBg: 'bg-blue-50 text-blue-600',
                  badgeBg: 'bg-blue-100 text-blue-700'
              },
              {
                  id: 3,
                  nama: 'XI PPLG 1',
                  jurusan: 'PPLG',
                  sub: 'Pengembangan Perangkat Lunak & Gim',
                  status: 'Belum Diabsen',
                  siswaCount: 36,
                  wali: 'I Made Sudirga, S.Kom.',
                  iconType: 'code',
                  iconBg: 'bg-blue-50 text-blue-600',
                  badgeBg: 'bg-blue-100 text-blue-700'
              },
              {
                  id: 4,
                  nama: 'XI PPLG 2',
                  jurusan: 'PPLG',
                  sub: 'Pengembangan Perangkat Lunak & Gim',
                  status: 'Sudah Diabsen',
                  siswaCount: 34,
                  wali: 'Ni Putu Ayu Mitah, S.Pd.',
                  iconType: 'code',
                  iconBg: 'bg-blue-50 text-blue-600',
                  badgeBg: 'bg-blue-100 text-blue-700'
              },

              // TJKT
              {
                  id: 5,
                  nama: 'X TJKT 1',
                  jurusan: 'TJKT',
                  sub: 'Teknik Jaringan Komputer & Telekomunikasi',
                  status: 'Belum Diabsen',
                  siswaCount: 34,
                  wali: 'I Gede Budiarta, S.T.',
                  iconType: 'network',
                  iconBg: 'bg-cyan-50 text-cyan-600',
                  badgeBg: 'bg-cyan-100 text-cyan-700'
              },
              {
                  id: 6,
                  nama: 'XI TJKT 1',
                  jurusan: 'TJKT',
                  sub: 'Teknik Jaringan Komputer & Telekomunikasi',
                  status: 'Sudah Diabsen',
                  siswaCount: 35,
                  wali: 'I Kadek Agus Sujana, S...',
                  iconType: 'server',
                  iconBg: 'bg-cyan-50 text-cyan-600',
                  badgeBg: 'bg-cyan-100 text-cyan-700'
              },

              // DKV
              {
                  id: 7,
                  nama: 'X DKV 1',
                  jurusan: 'DKV',
                  sub: 'Desain Komunikasi Visual',
                  status: 'Belum Diabsen',
                  siswaCount: 36,
                  wali: 'Desak Ketut Ratih, S.Sn.',
                  iconType: 'palette',
                  iconBg: 'bg-rose-50 text-rose-600',
                  badgeBg: 'bg-rose-100 text-rose-700'
              },
              {
                  id: 8,
                  nama: 'X DKV 2',
                  jurusan: 'DKV',
                  sub: 'Desain Komunikasi Visual',
                  status: 'Belum Diabsen',
                  siswaCount: 34,
                  wali: 'I Nyoman Suardika, S.Ds.',
                  iconType: 'palette',
                  iconBg: 'bg-rose-50 text-rose-600',
                  badgeBg: 'bg-rose-100 text-rose-700'
              },
              {
                  id: 9,
                  nama: 'X DKV 3',
                  jurusan: 'DKV',
                  sub: 'Desain Komunikasi Visual',
                  status: 'Belum Diabsen',
                  siswaCount: 35,
                  wali: 'Ni Kadek Sintya Pertiwi,...',
                  iconType: 'palette',
                  iconBg: 'bg-rose-50 text-rose-600',
                  badgeBg: 'bg-rose-100 text-rose-700'
              },
              {
                  id: 10,
                  nama: 'XI DKV 1',
                  jurusan: 'DKV',
                  sub: 'Desain Komunikasi Visual',
                  status: 'Belum Diabsen',
                  siswaCount: 35,
                  wali: 'Luh Putu Ratna Sari, S.P...',
                  iconType: 'image',
                  iconBg: 'bg-rose-50 text-rose-600',
                  badgeBg: 'bg-rose-100 text-rose-700'
              },
              {
                  id: 11,
                  nama: 'XI DKV 2',
                  jurusan: 'DKV',
                  sub: 'Desain Komunikasi Visual',
                  status: 'Sudah Diabsen',
                  siswaCount: 33,
                  wali: 'I Made Wahyu Ariyasa,...',
                  iconType: 'image',
                  iconBg: 'bg-rose-50 text-rose-600',
                  badgeBg: 'bg-rose-100 text-rose-700'
              },

              // BD (Bisnis Digital)
              {
                  id: 12,
                  nama: 'X BD 1',
                  jurusan: 'BD',
                  sub: 'Bisnis Digital',
                  status: 'Belum Diabsen',
                  siswaCount: 36,
                  wali: 'Ni Kadek Srinadi, S.E.',
                  iconType: 'shopping-cart',
                  iconBg: 'bg-amber-50 text-amber-600',
                  badgeBg: 'bg-amber-100 text-amber-700'
              },
              {
                  id: 13,
                  nama: 'X BD 2',
                  jurusan: 'BD',
                  sub: 'Bisnis Digital',
                  status: 'Belum Diabsen',
                  siswaCount: 35,
                  wali: 'I Ketut Darmawan, S.Pd.',
                  iconType: 'shopping-cart',
                  iconBg: 'bg-amber-50 text-amber-600',
                  badgeBg: 'bg-amber-100 text-amber-700'
              },
              {
                  id: 14,
                  nama: 'X BD 3',
                  jurusan: 'BD',
                  sub: 'Bisnis Digital',
                  status: 'Belum Diabsen',
                  siswaCount: 34,
                  wali: 'I Made Suartika, S.E.',
                  iconType: 'shopping-cart',
                  iconBg: 'bg-amber-50 text-amber-600',
                  badgeBg: 'bg-amber-100 text-amber-700'
              },
              {
                  id: 15,
                  nama: 'XI BD 1',
                  jurusan: 'BD',
                  sub: 'Bisnis Digital',
                  status: 'Belum Diabsen',
                  siswaCount: 34,
                  wali: 'Ni Made Yani Astuti, S.E...',
                  iconType: 'store',
                  iconBg: 'bg-amber-50 text-amber-600',
                  badgeBg: 'bg-amber-100 text-amber-700'
              },
              {
                  id: 16,
                  nama: 'XI BD 2',
                  jurusan: 'BD',
                  sub: 'Bisnis Digital',
                  status: 'Sudah Diabsen',
                  siswaCount: 33,
                  wali: 'I Putu Hendra Sudewa,...',
                  iconType: 'store',
                  iconBg: 'bg-amber-50 text-amber-600',
                  badgeBg: 'bg-amber-100 text-amber-700'
              }
          ],

          // Dummy Siswa Generator for Modal Detail
          daftarSiswaKelas: [],

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
              this.liveTimeWita = `${hr}:${mn}:${sc} WITA`;
          },

          get filteredKelas() {
              return this.kelasList.filter(item => {
                  const matchFilter = this.selectedFilter === 'Semua' || item.jurusan === this.selectedFilter;
                  const query = this.searchQuery.toLowerCase().trim();
                  const matchSearch = !query || 
                      item.nama.toLowerCase().includes(query) || 
                      item.sub.toLowerCase().includes(query) || 
                      item.wali.toLowerCase().includes(query);
                  return matchFilter && matchSearch;
              });
          },

          openDetailKelas(kelas) {
              this.selectedKelasData = kelas;
              this.searchSiswaInModal = '';
              this.filterStatusSiswa = 'Semua';
              
              // Generate dummy students for this class
              const names = [
                  'Wahyu Pratama', 'I Made Raditya', 'Ni Putu Maharani', 'Ketut Agus Surya',
                  'Ni Kadek Sintya', 'I Gede Budiarta', 'Kadek Dwi Lestari', 'Komang Arya Wijaya',
                  'Putu Bagus Wicaksana', 'Made Danu Saputra', 'Nyoman Riko Pratama', 'Ni Luh Ayu Sukma',
                  'I Wayan Gede Aditya', 'Gusti Ayu Melati', 'Dewa Ketut Suardana', 'Anak Agung Raka'
              ];

              this.daftarSiswaKelas = names.slice(0, 12).map((nama, idx) => {
                  let status = 'Hadir';
                  let jam = '06:58 WITA';
                  let statusBadge = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                  
                  if (idx === 1) {
                      status = 'Terlambat';
                      jam = '07:18 WITA';
                      statusBadge = 'bg-amber-50 text-amber-700 border-amber-200';
                  } else if (idx === 3) {
                      status = 'Izin';
                      jam = 'Surat Dokter';
                      statusBadge = 'bg-blue-50 text-blue-700 border-blue-200';
                  } else if (idx === 6 && kelas.status === 'Belum Diabsen') {
                      status = 'Belum Absen';
                      jam = '-';
                      statusBadge = 'bg-slate-100 text-slate-500 border-slate-200';
                  }

                  return {
                      nis: '2026' + String(1000 + idx + kelas.id * 10),
                      nama: nama,
                      status: status,
                      jam: jam,
                      statusBadge: statusBadge
                  };
              });

              this.modalDetailSiswa = true;
              this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
          },

          get filteredSiswa() {
              if (!this.daftarSiswaKelas) return [];
              return this.daftarSiswaKelas.filter(s => {
                  const matchStatus = this.filterStatusSiswa === 'Semua' || s.status === this.filterStatusSiswa;
                  const matchSearch = !this.searchSiswaInModal || s.nama.toLowerCase().includes(this.searchSiswaInModal.toLowerCase()) || s.nis.includes(this.searchSiswaInModal);
                  return matchStatus && matchSearch;
              });
          },

          refreshData() {
              this.searchQuery = '';
              this.selectedFilter = 'Semua';
              this.triggerToast('Data absensi kelas berhasil diperbarui!');
          },

          triggerToast(msg) {
              this.toastMessage = msg;
              this.showToast = true;
              this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
              setTimeout(() => { this.showToast = false; }, 3500);
          }
      }">

    <!-- ========================================================================= -->
    <!-- 1. HEADER HERO (Exact from Reference Image: Deep Blue with Curved Bottom) -->
    <!-- ========================================================================= -->
    <header class="relative bg-gradient-to-b from-[#1e40af] via-[#2563eb] to-[#1d4ed8] text-white rounded-b-[36px] sm:rounded-b-[44px] lg:rounded-b-[52px] pt-5 sm:pt-7 pb-8 sm:pb-10 px-5 sm:px-8 shadow-xl shadow-blue-600/15">
        
        <!-- Ambient Lighting Glows (Contained with overflow-hidden and rounded bottom) -->
        <div class="absolute inset-0 rounded-b-[36px] sm:rounded-b-[44px] lg:rounded-b-[52px] overflow-hidden pointer-events-none">
            <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full bg-white/10 blur-2xl"></div>
            <div class="absolute bottom-0 left-1/4 w-72 h-72 rounded-full bg-blue-400/20 blur-3xl"></div>
        </div>

        <div class="max-w-4xl mx-auto relative z-10 space-y-4 sm:space-y-5">
            
            <!-- Top Navigation Row: Back Button, School Logo + Name, Refresh Button -->
            <div class="flex items-center justify-between">
                <!-- Back Button (Arrow Left) -->
                <a href="{{ route('guru.bk') }}" 
                   title="Kembali ke Dashboard BK"
                   class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-md flex items-center justify-center text-white transition-all active:scale-95 shadow-sm cursor-pointer">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                </a>

                <!-- Center: School Brand Logo & Title -->
                <div class="flex items-center space-x-2.5 bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-full border border-white/20 shadow-sm">
                    <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-white p-0.5 flex items-center justify-center shrink-0 shadow-xs">
                        <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK" class="w-full h-full object-contain">
                    </div>
                    <span class="text-xs sm:text-sm font-extrabold tracking-wider uppercase text-white drop-shadow-xs">
                        SMK TI BALI GLOBAL BADUNG
                    </span>
                </div>

                <!-- Refresh Button -->
                <button @click="refreshData()" 
                        type="button"
                        title="Muat Ulang Data"
                        class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-md flex items-center justify-center text-white transition-all active:scale-95 shadow-sm cursor-pointer">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="23 4 23 10 17 10"></polyline>
                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                    </svg>
                </button>
            </div>

            <!-- Header Titles: Pilih Kelas & Subtitle -->
            <div class="pt-2 text-left">
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    Pilih Kelas
                </h1>
                <p class="text-xs sm:text-sm text-blue-100 font-medium mt-1">
                    Silakan tentukan kelas untuk memulai absensi.
                </p>
            </div>
        </div>

        <!-- Floating Clock Pill (Tampil Sempurna & Tidak Terpotong) -->
        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-1/2 z-30 pointer-events-auto">
            <div class="inline-flex items-center space-x-2 bg-white text-blue-700 px-5 sm:px-6 py-2.5 rounded-full shadow-lg shadow-blue-950/15 border border-blue-100 font-extrabold text-xs sm:text-sm select-none whitespace-nowrap tabular-nums">
                <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5 text-blue-600 stroke-[2.5] shrink-0 animate-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span x-text="'Hari Ini • ' + (liveTimeWita || '07:45:00 WITA')">Hari Ini • 07:45:00 WITA</span>
            </div>
        </div>
    </header>


    <!-- ========================================================================= -->
    <!-- 2. MAIN BODY (Search, Filter Jurusan, and Class Cards List)               -->
    <!-- ========================================================================= -->
    <main class="flex-1 w-full max-w-4xl mx-auto px-5 sm:px-8 mt-10 sm:mt-12 pb-16 space-y-5">
        
        <!-- Search Input Bar (Exact from Mockup) -->
        <div class="relative">
            <svg class="w-4 h-4 text-slate-400 absolute left-4 top-3.5 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" 
                   x-model="searchQuery" 
                   placeholder="Cari kelas (contoh: XI PPLG 1, X DKV 3, X BD 3)..." 
                   class="w-full bg-white border border-slate-200/90 rounded-2xl pl-11 pr-10 py-3 text-xs sm:text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-soft placeholder:text-slate-400 transition-all">
            <button x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-3.5 text-slate-400 hover:text-slate-600">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>


        <!-- Filter Jurusan Header & Horizontal Pills (Exact from Mockup) -->
        <div class="space-y-2.5">
            <div class="flex items-center justify-between text-xs">
                <span class="font-extrabold uppercase text-slate-500 tracking-wider text-[11px]">
                    FILTER JURUSAN
                </span>
                <span class="font-bold text-blue-600 text-xs" x-text="filteredKelas.length + ' Kelas'">
                    16 Kelas
                </span>
            </div>

            <!-- Horizontal Scrollable Filter Tabs -->
            <div class="flex items-center space-x-2 overflow-x-auto pb-1.5 scrollbar-none">
                <!-- Tab: Semua -->
                <button type="button" 
                        @click="selectedFilter = 'Semua'" 
                        :class="selectedFilter === 'Semua' ? 'bg-blue-600 text-white font-extrabold shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-200 font-semibold'"
                        class="px-4 py-2 rounded-xl text-xs whitespace-nowrap transition-all cursor-pointer">
                    Semua (16)
                </button>

                <!-- Tab: PPLG -->
                <button type="button" 
                        @click="selectedFilter = 'PPLG'" 
                        :class="selectedFilter === 'PPLG' ? 'bg-blue-600 text-white font-extrabold shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-200 font-semibold'"
                        class="px-4 py-2 rounded-xl text-xs whitespace-nowrap transition-all cursor-pointer">
                    PPLG (4)
                </button>

                <!-- Tab: TJKT -->
                <button type="button" 
                        @click="selectedFilter = 'TJKT'" 
                        :class="selectedFilter === 'TJKT' ? 'bg-blue-600 text-white font-extrabold shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-200 font-semibold'"
                        class="px-4 py-2 rounded-xl text-xs whitespace-nowrap transition-all cursor-pointer">
                    TJKT (2)
                </button>

                <!-- Tab: DKV -->
                <button type="button" 
                        @click="selectedFilter = 'DKV'" 
                        :class="selectedFilter === 'DKV' ? 'bg-blue-600 text-white font-extrabold shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-200 font-semibold'"
                        class="px-4 py-2 rounded-xl text-xs whitespace-nowrap transition-all cursor-pointer">
                    DKV (5)
                </button>

                <!-- Tab: BD -->
                <button type="button" 
                        @click="selectedFilter = 'BD'" 
                        :class="selectedFilter === 'BD' ? 'bg-blue-600 text-white font-extrabold shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-200 font-semibold'"
                        class="px-4 py-2 rounded-xl text-xs whitespace-nowrap transition-all cursor-pointer">
                    BD (5)
                </button>
            </div>
        </div>


        <!-- Section Header: Daftar Rombongan Belajar (Exact from Mockup) -->
        <div class="flex items-center justify-between text-xs pt-1">
            <span class="font-extrabold uppercase text-slate-500 tracking-wider text-[11px]">
                DAFTAR ROMBONGAN BELAJAR
            </span>
            <span class="font-bold text-slate-400 text-[10px] tracking-wider uppercase">
                KETUK UNTUK LIHAT SISWA
            </span>
        </div>


        <!-- ================================================================= -->
        <!-- CLASS CARDS LIST (Responsive 1-Col Mobile / 2-Col Tablet & Desktop) -->
        <!-- ================================================================= -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
            <template x-for="item in filteredKelas" :key="item.id">
                <div @click="openDetailKelas(item)" 
                     class="bg-white rounded-2xl p-4 sm:p-5 shadow-soft border border-slate-200/70 hover:border-blue-300 hover:shadow-md transition-all active:scale-98 cursor-pointer flex items-center justify-between group">
                    
                    <!-- Left: Major Themed Icon -->
                    <div class="flex items-center space-x-3.5">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 transition-transform group-hover:scale-105"
                             :class="item.iconBg">
                            <!-- Laptop (PPLG) -->
                            <svg x-show="item.iconType === 'laptop'" class="w-6 h-6 stroke-[2.2]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 16V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v9m16 0H4m16 0 1.28 2.55a1 1 0 0 1-.9 1.45H3.62a1 1 0 0 1-.9-1.45L4 16"/>
                            </svg>
                            <!-- Code (PPLG) -->
                            <svg x-show="item.iconType === 'code'" class="w-6 h-6 stroke-[2.2]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="16 18 22 12 16 6"/>
                                <polyline points="8 6 2 12 8 18"/>
                            </svg>
                            <!-- Network (TJKT) -->
                            <svg x-show="item.iconType === 'network'" class="w-6 h-6 stroke-[2.2]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="16" y="16" width="6" height="6" rx="1"/>
                                <rect x="2" y="16" width="6" height="6" rx="1"/>
                                <rect x="9" y="2" width="6" height="6" rx="1"/>
                                <path d="M5 16v-3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3"/>
                                <path d="M12 12V8"/>
                            </svg>
                            <!-- Server (TJKT) -->
                            <svg x-show="item.iconType === 'server'" class="w-6 h-6 stroke-[2.2]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="8" x="2" y="2" rx="2" ry="2"/>
                                <rect width="20" height="8" x="2" y="14" rx="2" ry="2"/>
                                <line x1="6" x2="6.01" y1="6" y2="6"/>
                                <line x1="6" x2="6.01" y1="18" y2="18"/>
                            </svg>
                            <!-- Palette (DKV) -->
                            <svg x-show="item.iconType === 'palette'" class="w-6 h-6 stroke-[2.2]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="13.5" cy="6.5" r=".5" fill="currentColor"/>
                                <circle cx="17.5" cy="10.5" r=".5" fill="currentColor"/>
                                <circle cx="8.5" cy="7.5" r=".5" fill="currentColor"/>
                                <circle cx="6.5" cy="12.5" r=".5" fill="currentColor"/>
                                <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.563-2.512 5.563-5.563C22 6.5 17.5 2 12 2z"/>
                            </svg>
                            <!-- Image (DKV) -->
                            <svg x-show="item.iconType === 'image'" class="w-6 h-6 stroke-[2.2]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                                <circle cx="9" cy="9" r="2"/>
                                <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                            </svg>
                            <!-- Shopping Cart (BD) -->
                            <svg x-show="item.iconType === 'shopping-cart'" class="w-6 h-6 stroke-[2.2]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="8" cy="21" r="1"/>
                                <circle cx="19" cy="21" r="1"/>
                                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
                            </svg>
                            <!-- Store (BD) -->
                            <svg x-show="item.iconType === 'store'" class="w-6 h-6 stroke-[2.2]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/>
                                <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/>
                                <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/>
                                <path d="M2 7h20"/>
                            </svg>
                        </div>

                        <!-- Middle Details -->
                        <div>
                            <!-- Top: Class Name, Major Badge, Attendance Status Badge -->
                            <div class="flex items-center space-x-2">
                                <h3 class="text-sm sm:text-base font-black text-slate-900 group-hover:text-blue-600 transition-colors" x-text="item.nama">
                                </h3>
                                
                                <!-- Jurusan Pill Badge -->
                                <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-md uppercase"
                                      :class="item.badgeBg"
                                      x-text="item.jurusan">
                                </span>

                                <!-- Status Absen Badge -->
                                <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full border whitespace-nowrap"
                                      :class="item.status === 'Sudah Diabsen' ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-rose-50 text-rose-600 border-rose-200'"
                                      x-text="item.status">
                                </span>
                            </div>

                            <!-- Major Long Subtitle -->
                            <p class="text-[11px] text-slate-500 font-medium mt-0.5 line-clamp-1" x-text="item.sub">
                            </p>

                            <!-- Bottom Row: Student Count & Homeroom Teacher -->
                            <div class="flex items-center space-x-2 text-[11px] text-slate-400 mt-1.5 font-medium">
                                <span class="flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    <span x-text="item.siswaCount + ' Siswa'"></span>
                                </span>
                                <span>•</span>
                                <span class="flex items-center space-x-1 truncate max-w-[130px] sm:max-w-[170px]">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                    <span class="truncate" x-text="item.wali"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Arrow Chevron Right -->
                    <div class="pl-2 shrink-0">
                        <div class="w-8 h-8 rounded-full bg-slate-50 group-hover:bg-blue-50 text-slate-300 group-hover:text-blue-600 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 stroke-[2.5]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State if no class matches search -->
        <div x-show="filteredKelas.length === 0" class="bg-white rounded-3xl p-10 text-center space-y-3 border border-slate-200/80 shadow-soft">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    <line x1="8" y1="8" x2="14" y2="14"></line>
                    <line x1="14" y1="8" x2="8" y2="14"></line>
                </svg>
            </div>
            <h4 class="font-bold text-slate-900 text-sm">Kelas tidak ditemukan</h4>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                Tidak ada rombongan belajar yang cocok dengan kata kunci pencarian Anda.
            </p>
            <button @click="searchQuery = ''; selectedFilter = 'Semua'" class="text-xs font-bold text-blue-600 hover:underline cursor-pointer">
                Reset Pencarian
            </button>
        </div>
    </main>


    <!-- ========================================================================= -->
    <!-- MODAL DETAIL SISWA KELAS (Ketuk Rombel untuk Melihat Absen Siswa)        -->
    <!-- ========================================================================= -->
    <div x-show="modalDetailSiswa" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="modalDetailSiswa = false" 
             x-show="modalDetailSiswa" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-100 space-y-5">
            
            <!-- Header Modal -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-3">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0"
                         :class="selectedKelasData?.iconBg">
                        <svg class="w-6 h-6 stroke-[2.2]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <h3 class="text-lg font-black text-slate-900" x-text="selectedKelasData?.nama"></h3>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border"
                                  :class="selectedKelasData?.status === 'Sudah Diabsen' ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-rose-50 text-rose-600 border-rose-200'"
                                  x-text="selectedKelasData?.status"></span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5" x-text="'Wali Kelas: ' + selectedKelasData?.wali"></p>
                    </div>
                </div>
                <button @click="modalDetailSiswa = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 cursor-pointer transition-colors">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- Summary Stats for This Class -->
            <div class="grid grid-cols-4 gap-2 text-center text-xs">
                <div class="p-2.5 rounded-xl bg-emerald-50 text-emerald-800">
                    <p class="text-[10px] font-bold text-emerald-600 uppercase">Hadir</p>
                    <p class="text-base font-black mt-0.5">34</p>
                </div>
                <div class="p-2.5 rounded-xl bg-amber-50 text-amber-800">
                    <p class="text-[10px] font-bold text-amber-600 uppercase">Telat</p>
                    <p class="text-base font-black mt-0.5">1</p>
                </div>
                <div class="p-2.5 rounded-xl bg-blue-50 text-blue-800">
                    <p class="text-[10px] font-bold text-blue-600 uppercase">Izin</p>
                    <p class="text-base font-black mt-0.5">1</p>
                </div>
                <div class="p-2.5 rounded-xl bg-rose-50 text-rose-800">
                    <p class="text-[10px] font-bold text-rose-600 uppercase">Alpa</p>
                    <p class="text-base font-black mt-0.5">0</p>
                </div>
            </div>

            <!-- Search & Filter Inside Modal -->
            <div class="flex items-center space-x-2">
                <div class="relative flex-1">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-2.5 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" 
                           x-model="searchSiswaInModal" 
                           placeholder="Cari nama siswa..." 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <select x-model="filterStatusSiswa" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-700 outline-none cursor-pointer">
                    <option value="Semua">Semua Status</option>
                    <option value="Hadir">Hadir</option>
                    <option value="Terlambat">Terlambat</option>
                    <option value="Izin">Izin</option>
                </select>
            </div>

            <!-- Student Attendance List for This Class -->
            <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                <template x-for="siswa in filteredSiswa" :key="siswa.nis">
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs hover:bg-slate-100/70 transition-colors">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-black flex items-center justify-center text-xs shrink-0"
                                 x-text="siswa.nama.charAt(0)">
                            </div>
                            <div>
                                <p class="font-bold text-slate-900" x-text="siswa.nama"></p>
                                <p class="text-[11px] text-slate-400" x-text="'NIS: ' + siswa.nis + ' • ' + siswa.jam"></p>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full border whitespace-nowrap"
                              :class="siswa.statusBadge"
                              x-text="siswa.status">
                        </span>
                    </div>
                </template>
            </div>

            <!-- Modal Bottom Button -->
            <div class="pt-2">
                <button @click="modalDetailSiswa = false" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl text-xs transition-colors cursor-pointer">
                    Tutup Rincian Kelas
                </button>
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
         class="fixed bottom-6 right-4 sm:right-6 z-50 max-w-sm bg-slate-900 text-white px-4 py-3 rounded-2xl shadow-2xl flex items-center space-x-3 text-xs border border-slate-800">
        <div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
            <svg class="w-3.5 h-3.5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>
        <p class="font-medium" x-text="toastMessage"></p>
    </div>

</body>
</html>

