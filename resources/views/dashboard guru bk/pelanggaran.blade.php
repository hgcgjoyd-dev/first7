<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pelanggaran Siswa - SMK TI Bali Global Badung</title>

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
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.08); opacity: 0.4; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }
        .animate-pulse-ring {
            animation: pulse-ring 2s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-[#F4F6FB] text-slate-800 antialiased min-h-screen flex flex-col selection:bg-blue-600 selection:text-white"
      x-data="{
          // State Kasus & Filter
          searchQuery: '',
          selectedTab: 'aktif', // 'semua', 'aktif', 'tertib'
          
          // Modals
          modalBuatLaporan: false,
          modalKonfirmasiTertib: false,
          selectedKasusTertib: null,
          poinTertibTambahan: 15,
          catatanTertib: 'Siswa telah menjalani konseling, bersikap kooperatif, dan mematuhi tata tertib sekolah.',
          
          // Toast Feedback
          showToast: false,
          toastMessage: '',
          toastType: 'success', // 'success', 'warning', 'info'

          // Form Laporan Baru
          formBaru: {
              nama: '',
              kelas: 'XII RPL 1',
              tingkat: 'Perhatian BK',
              deskripsi: '',
              pelapor: 'Laporan Siswa (Anonim)',
              poinAwal: 25
          },

          // Data Antrean Kasus Pelanggaran (Exact from Reference Image)
          kasusList: [
              {
                  id: 1,
                  nama: 'I Gede Bagus Mahendra',
                  kelas: 'XII RPL 1',
                  tingkat: 'Perhatian BK',
                  tingkatBadgeClass: 'bg-amber-50 text-amber-800 border-amber-300',
                  deskripsi: 'Terlihat merokok di area belakang kantin',
                  waktu: '10 menit lalu',
                  pelapor: 'Laporan Siswa (Anonim)',
                  poin: 40,
                  poinTertib: 0,
                  status: 'aktif', // 'aktif' atau 'tertib'
                  isNew: false,
                  borderHighlight: 'border-slate-200/80 bg-white'
              },
              {
                  id: 2,
                  nama: 'Kadek Arta Pratama',
                  kelas: 'XI TKJ 2',
                  tingkat: 'Kritis / SP',
                  tingkatBadgeClass: 'bg-rose-100 text-rose-700 border-rose-300 font-extrabold',
                  deskripsi: 'Membawa pod vape & menolak diperiksa',
                  waktu: '45 menit lalu',
                  pelapor: 'Guru Piket Lab TKJ',
                  poin: 55,
                  poinTertib: 0,
                  status: 'aktif',
                  isNew: false,
                  borderHighlight: 'border-rose-300/80 bg-rose-50/15'
              },
              {
                  id: 3,
                  nama: 'Ketut Yoga Wardana',
                  kelas: 'X RPL 1',
                  tingkat: 'Ringan',
                  tingkatBadgeClass: 'bg-emerald-50 text-emerald-800 border-emerald-200',
                  deskripsi: 'Terlambat masuk sekolah & seragam tidak lengkap',
                  waktu: 'Pagi ini, 07:35 WITA',
                  pelapor: 'Laporan Gerbang Depan',
                  poin: 15,
                  poinTertib: 0,
                  status: 'aktif',
                  isNew: false,
                  borderHighlight: 'border-slate-200/80 bg-white'
              }
          ],

          // Counter Dinamis
          get activeKasusCount() {
              return this.kasusList.filter(k => k.status === 'aktif').length;
          },

          get laporanMasukCount() {
              return this.activeKasusCount;
          },

          get tertibKasusCount() {
              return this.kasusList.filter(k => k.status === 'tertib').length;
          },

          get filteredKasus() {
              return this.kasusList.filter(k => {
                  const matchTab = (this.selectedTab === 'semua') 
                      || (this.selectedTab === 'aktif' && k.status === 'aktif')
                      || (this.selectedTab === 'tertib' && k.status === 'tertib');
                  
                  const q = this.searchQuery.toLowerCase().trim();
                  const matchQuery = !q 
                      || k.nama.toLowerCase().includes(q) 
                      || k.kelas.toLowerCase().includes(q) 
                      || k.deskripsi.toLowerCase().includes(q)
                      || k.pelapor.toLowerCase().includes(q);
                  
                  return matchTab && matchQuery;
              });
          },

          init() {
              this.$nextTick(() => {
                  if (window.lucide) lucide.createIcons();
              });
          },

          // 1. Kurangi Poin Sanksi
          kurangPoin(kasus) {
              if (kasus.poin <= 0) {
                  this.triggerToast('Poin sanksi ' + kasus.nama + ' sudah 0.', 'info');
                  return;
              }
              const step = 5;
              kasus.poin = Math.max(0, kasus.poin - step);
              
              if (kasus.poin < 20 && kasus.tingkat === 'Perhatian BK') {
                  kasus.tingkat = 'Ringan';
                  kasus.tingkatBadgeClass = 'bg-emerald-50 text-emerald-800 border-emerald-200';
              } else if (kasus.poin < 50 && kasus.tingkat === 'Kritis / SP') {
                  kasus.tingkat = 'Perhatian BK';
                  kasus.tingkatBadgeClass = 'bg-amber-50 text-amber-800 border-amber-300';
                  kasus.borderHighlight = 'border-slate-200/80 bg-white';
              }

              this.triggerToast('Poin sanksi ' + kasus.nama + ' dikurangi menjadi ' + kasus.poin + ' Poin.', 'info');
          },

          // 2. Tambah Poin Sanksi (Bila Melanggar Lagi)
          tambahPoin(kasus) {
              const step = 5;
              kasus.poin += step;

              if (kasus.poin >= 50 && kasus.tingkat !== 'Kritis / SP') {
                  kasus.tingkat = 'Kritis / SP';
                  kasus.tingkatBadgeClass = 'bg-rose-100 text-rose-700 border-rose-300 font-extrabold';
                  kasus.borderHighlight = 'border-rose-300/80 bg-rose-50/15';
              } else if (kasus.poin >= 25 && kasus.tingkat === 'Ringan') {
                  kasus.tingkat = 'Perhatian BK';
                  kasus.tingkatBadgeClass = 'bg-amber-50 text-amber-800 border-amber-300';
              }

              this.triggerToast('Poin pelanggaran ' + kasus.nama + ' bertambah menjadi ' + kasus.poin + ' Poin.', 'warning');
          },

          // 3. Buka Modal Konfirmasi Tertib (Tandai Siswa Sudah Tertib & Tambah Poin Pembinaan)
          bukaModalTertib(kasus) {
              this.selectedKasusTertib = kasus;
              this.poinTertibTambahan = 15;
              this.catatanTertib = 'Siswa telah menjalani pembinaan, bersikap tertib, dan tidak mengulangi pelanggaran.';
              this.modalKonfirmasiTertib = true;
              this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
          },

          // 4. Eksekusi Siswa Sudah Tertib & Ditambah Poinnya
          konfirmasiSiswaTertib() {
              if (!this.selectedKasusTertib) return;
              
              const k = this.selectedKasusTertib;
              k.status = 'tertib';
              k.tingkat = 'Sudah Tertib';
              k.tingkatBadgeClass = 'bg-emerald-100 text-emerald-800 border-emerald-300 font-extrabold';
              k.borderHighlight = 'border-emerald-300/80 bg-emerald-50/20';
              k.poinTertib = parseInt(this.poinTertibTambahan) || 15;
              k.poin = 0; // Poin sanksi dinolkan karena sudah tertib

              this.playChime();
              this.modalKonfirmasiTertib = false;
              this.triggerToast('Alhamdulillah! ' + k.nama + ' dinyatakan SUDAH TERTIB. +' + k.poinTertib + ' Poin Kedisiplinan berhasil ditambahkan!', 'success');
              
              this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
          },

          // 5. Kembalikan Siswa ke Status Aktif (Undo / Koreksi)
          batalkanStatusTertib(kasus) {
              kasus.status = 'aktif';
              kasus.tingkat = 'Perhatian BK';
              kasus.tingkatBadgeClass = 'bg-amber-50 text-amber-800 border-amber-300';
              kasus.borderHighlight = 'border-slate-200/80 bg-white';
              kasus.poin = 20;
              kasus.poinTertib = 0;
              this.triggerToast('Kasus ' + kasus.nama + ' dikembalikan ke status antrean aktif.', 'info');
              this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
          },

          // 6. Simpan Laporan Pelanggaran Baru (Form Input)
          simpanLaporanBaru() {
              if (!this.formBaru.nama.trim() || !this.formBaru.deskripsi.trim()) {
                  this.triggerToast('Mohon lengkapi nama siswa dan detail pelanggaran.', 'warning');
                  return;
              }

              let badgeClass = 'bg-emerald-50 text-emerald-800 border-emerald-200';
              let borderH = 'border-slate-200/80 bg-white';

              if (this.formBaru.tingkat === 'Kritis / SP') {
                  badgeClass = 'bg-rose-100 text-rose-700 border-rose-300 font-extrabold';
                  borderH = 'border-rose-300/80 bg-rose-50/15';
              } else if (this.formBaru.tingkat === 'Perhatian BK') {
                  badgeClass = 'bg-amber-50 text-amber-800 border-amber-300';
                  borderH = 'border-slate-200/80 bg-white';
              }

              const newKasus = {
                  id: Date.now(),
                  nama: this.formBaru.nama.trim(),
                  kelas: this.formBaru.kelas,
                  tingkat: this.formBaru.tingkat,
                  tingkatBadgeClass: badgeClass,
                  deskripsi: this.formBaru.deskripsi.trim(),
                  waktu: 'Baru saja',
                  pelapor: this.formBaru.pelapor,
                  poin: parseInt(this.formBaru.poinAwal) || 20,
                  poinTertib: 0,
                  status: 'aktif',
                  isNew: true,
                  borderHighlight: borderH
              };

              // Masukkan ke urutan paling atas antrean kasus (realtime incoming)
              this.kasusList.unshift(newKasus);
              this.selectedTab = 'aktif';
              this.modalBuatLaporan = false;

              // Reset form
              this.formBaru.nama = '';
              this.formBaru.deskripsi = '';
              this.formBaru.poinAwal = 25;

              this.playBeepNotification();
              this.triggerToast('Laporan pelanggaran baru berhasil diterima dan masuk ke antrean kasus!', 'success');
              
              this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
          },

          // 7. Tes Simulasi Pesan Masuk Realtime (Demo Otomatis)
          simulasiPesanMasukRealtime() {
              const demoList = [
                  {
                      nama: 'I Made Raditya Suputra',
                      kelas: 'XI PPLG 1',
                      tingkat: 'Perhatian BK',
                      deskripsi: 'Terlambat masuk lab komputer & seragam tidak memakai dasi',
                      pelapor: 'Guru Piket Lab PPLG',
                      poin: 20
                  },
                  {
                      nama: 'Komang Dwi Antara',
                      kelas: 'XII DKV 2',
                      tingkat: 'Kritis / SP',
                      deskripsi: 'Meninggalkan kelas saat jam pelajaran tanpa izin wali kelas',
                      pelapor: 'Laporan Ketua Kelas (Anonim)',
                      poin: 45
                  },
                  {
                      nama: 'Putu Gede Aditya',
                      kelas: 'X TJKT 1',
                      tingkat: 'Ringan',
                      deskripsi: 'Bermain game di HP saat guru menerangkan materi di kelas',
                      pelapor: 'Guru Pengajar Mapel Kejuruan',
                      poin: 15
                  }
              ];

              const randomDemo = demoList[Math.floor(Math.random() * demoList.length)];

              let badgeClass = 'bg-amber-50 text-amber-800 border-amber-300';
              let borderH = 'border-slate-200/80 bg-white';
              if (randomDemo.tingkat === 'Kritis / SP') {
                  badgeClass = 'bg-rose-100 text-rose-700 border-rose-300 font-extrabold';
                  borderH = 'border-rose-300/80 bg-rose-50/15';
              } else if (randomDemo.tingkat === 'Ringan') {
                  badgeClass = 'bg-emerald-50 text-emerald-800 border-emerald-200';
              }

              const newKasus = {
                  id: Date.now(),
                  nama: randomDemo.nama,
                  kelas: randomDemo.kelas,
                  tingkat: randomDemo.tingkat,
                  tingkatBadgeClass: badgeClass,
                  deskripsi: randomDemo.deskripsi,
                  waktu: 'Baru saja (Realtime)',
                  pelapor: randomDemo.pelapor,
                  poin: randomDemo.poin,
                  poinTertib: 0,
                  status: 'aktif',
                  isNew: true,
                  borderHighlight: borderH
              };

              this.kasusList.unshift(newKasus);
              this.selectedTab = 'aktif';

              this.playBeepNotification();
              this.triggerToast('🔔 Laporan Pelanggaran Baru Masuk: ' + randomDemo.nama + ' (' + randomDemo.kelas + ')', 'warning');

              this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
          },

          // Feedback Audio & Toast
          playBeepNotification() {
              try {
                  const AudioCtx = window.AudioContext || window.webkitAudioContext;
                  if (!AudioCtx) return;
                  const ctx = new AudioCtx();
                  const osc = ctx.createOscillator();
                  const gain = ctx.createGain();
                  osc.type = 'triangle';
                  osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
                  osc.frequency.setValueAtTime(880, ctx.currentTime + 0.1); // A5
                  gain.gain.setValueAtTime(0.15, ctx.currentTime);
                  gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.35);
                  osc.connect(gain);
                  gain.connect(ctx.destination);
                  osc.start();
                  osc.stop(ctx.currentTime + 0.35);
              } catch(e) {}
          },

          playChime() {
              try {
                  const AudioCtx = window.AudioContext || window.webkitAudioContext;
                  if (!AudioCtx) return;
                  const ctx = new AudioCtx();
                  const osc = ctx.createOscillator();
                  const gain = ctx.createGain();
                  osc.type = 'sine';
                  osc.frequency.setValueAtTime(523.25, ctx.currentTime); // C5
                  osc.frequency.setValueAtTime(659.25, ctx.currentTime + 0.12); // E5
                  osc.frequency.setValueAtTime(783.99, ctx.currentTime + 0.24); // G5
                  gain.gain.setValueAtTime(0.15, ctx.currentTime);
                  gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
                  osc.connect(gain);
                  gain.connect(ctx.destination);
                  osc.start();
                  osc.stop(ctx.currentTime + 0.5);
              } catch(e) {}
          },

          triggerToast(msg, type = 'success') {
              this.toastMessage = msg;
              this.toastType = type;
              this.showToast = true;
              this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
              setTimeout(() => { this.showToast = false; }, 4000);
          }
      }">

    <!-- ========================================================================= -->
    <!-- 1. TOP HEADER HERO (Exact 1:1 Reference Screenshot Layout)                -->
    <!-- ========================================================================= -->
    <header class="relative bg-gradient-to-b from-[#1d4ed8] via-[#2563eb] to-[#1e40af] text-white rounded-b-[40px] sm:rounded-b-[48px] pt-5 sm:pt-7 pb-10 sm:pb-12 px-5 sm:px-8 shadow-xl shadow-blue-600/15">
        
        <!-- Ambient Background Glows -->
        <div class="absolute inset-0 rounded-b-[40px] sm:rounded-b-[48px] overflow-hidden pointer-events-none">
            <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full bg-white/10 blur-2xl"></div>
            <div class="absolute bottom-0 left-1/4 w-72 h-72 rounded-full bg-blue-400/20 blur-3xl"></div>
        </div>

        <div class="max-w-xl mx-auto relative z-10 space-y-4">
            
            <!-- Top Navigation Row: Back Button, School Brand Logo, User Avatar -->
            <div class="flex items-center justify-between">
                <!-- Back Button to BK Dashboard -->
                <a href="{{ route('guru.bk') }}" 
                   title="Kembali ke Dashboard BK"
                   class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-md flex items-center justify-center text-white transition-all active:scale-95 shadow-sm cursor-pointer shrink-0">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                </a>

                <!-- School Brand: Logo + Title -->
                <div class="flex items-center space-x-2 bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-full border border-white/20 shadow-sm">
                    <div class="w-6 h-6 rounded-full bg-white p-0.5 flex items-center justify-center shrink-0 shadow-2xs">
                        <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK" class="w-full h-full object-contain">
                    </div>
                    <span class="text-[11px] sm:text-xs font-black tracking-wider uppercase text-white drop-shadow-xs">
                        SMK TI BALI GLOBAL BADUNG
                    </span>
                </div>

                <!-- Right Avatar with Profile Icon (Mockup Match) -->
                <a href="{{ route('guru.profile') }}" 
                   title="Profil Guru BK"
                   class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-white text-blue-600 flex items-center justify-center shadow-lg ring-2 ring-white/60 hover:scale-105 active:scale-95 transition-all cursor-pointer shrink-0">
                    <i data-lucide="user" class="w-5 h-5 sm:w-6 sm:h-6 stroke-[2.2]"></i>
                </a>
            </div>

            <!-- Page Title: "Pelanggaran siswa" (Exact Mockup Match) -->
            <div class="pt-2 text-center">
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    Pelanggaran siswa
                </h1>

                <!-- Blue Translucent Stat Card inside Hero: "Laporan Pelanggaran Aktif: 3 Kasus" -->
                <div class="mt-4 bg-white/15 backdrop-blur-md border border-white/25 rounded-2xl p-4 sm:p-5 text-center max-w-xs mx-auto shadow-md">
                    <p class="text-xs font-semibold text-blue-100 tracking-wide">
                        Laporan Pelanggaran Aktif
                    </p>
                    <h2 class="text-2xl sm:text-3xl font-black text-white mt-0.5 tracking-tight"
                        x-text="activeKasusCount + ' Kasus'">
                        3 Kasus
                    </h2>
                </div>
            </div>

        </div>
    </header>


    <!-- ========================================================================= -->
    <!-- 2. MAIN CONTENT CONTAINER (Mobile & Desktop Responsive)                   -->
    <!-- ========================================================================= -->
    <main class="max-w-xl mx-auto w-full px-4 sm:px-6 pb-20 space-y-4">
        
        <!-- Floating Pill Notification: "🔔 Laporan Masuk [ 3 ]" (Exact Mockup Match) -->
        <div class="relative -mt-5 z-20 flex justify-center">
            <button type="button" 
                    @click="selectedTab = 'aktif'; triggerToast('Menampilkan antrean laporan masuk aktif (' + laporanMasukCount + ' kasus)', 'info')"
                    class="bg-white/95 hover:bg-white text-slate-800 border border-slate-200/90 shadow-md shadow-slate-200/80 rounded-full py-2.5 px-6 flex items-center space-x-2.5 transition-all cursor-pointer hover:scale-105 active:scale-95 group">
                <i data-lucide="bell" class="w-4 h-4 text-blue-600 group-hover:scale-110 transition-transform"></i>
                <span class="font-extrabold text-xs sm:text-sm text-slate-800">Laporan Masuk</span>
                <span class="bg-rose-100 text-rose-700 font-black text-xs px-2.5 py-0.5 rounded-full" 
                      x-text="laporanMasukCount">
                    3
                </span>
            </button>
        </div>


        <!-- Section Header: ANTREAN KASUS PELANGGARAN + Realtime Badge -->
        <div class="pt-2 flex items-center justify-between">
            <div>
                <h3 class="text-xs sm:text-sm font-black uppercase tracking-wider text-slate-900">
                    ANTREAN KASUS PELANGGARAN
                </h3>
                <p class="text-[11px] sm:text-xs text-slate-400 font-medium mt-0.5">
                    Data otomatis terisi saat ada laporan masuk dari siswa/piket
                </p>
            </div>

            <!-- Green Realtime Pill Badge (Exact Mockup Match) -->
            <span class="bg-emerald-50 text-emerald-600 border border-emerald-200 px-3 py-1 rounded-full text-xs font-black inline-flex items-center space-x-1.5 shrink-0 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Realtime</span>
            </span>
        </div>


        <!-- Quick Action Bar: Input Baru & Simulasi Pesan Masuk Realtime -->
        <div class="flex items-center space-x-2 pt-1">
            <!-- Button: + Laporkan Siswa Melanggar -->
            <button type="button" 
                    @click="modalBuatLaporan = true"
                    class="flex-1 py-2.5 px-3.5 bg-blue-600 hover:bg-blue-700 active:scale-98 text-white rounded-2xl font-extrabold text-xs flex items-center justify-center space-x-1.5 shadow-md shadow-blue-500/20 transition-all cursor-pointer">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>+ Lapor Pelanggaran</span>
            </button>

            <!-- Button: ⚡ Simulasi Pesan Masuk (Untuk Demonstrasi Realtime) -->
            <button type="button" 
                    @click="simulasiPesanMasukRealtime()"
                    title="Simulasikan laporan baru masuk dari siswa atau guru piket"
                    class="py-2.5 px-3.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 rounded-2xl font-extrabold text-xs flex items-center justify-center space-x-1.5 transition-all cursor-pointer active:scale-95 shadow-2xs">
                <i data-lucide="zap" class="w-4 h-4 text-amber-600"></i>
                <span>Tes Pesan Masuk</span>
            </button>
        </div>


        <!-- Tab Filter: Antrean Aktif, Sudah Tertib, Semua -->
        <div class="bg-white p-1 rounded-2xl border border-slate-200/80 shadow-2xs flex items-center text-xs">
            <button type="button" 
                    @click="selectedTab = 'aktif'"
                    class="flex-1 py-2 px-3 rounded-xl font-bold transition-all flex items-center justify-center space-x-1.5 cursor-pointer"
                    :class="selectedTab === 'aktif' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                <span>Kasus Aktif</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="selectedTab === 'aktif' ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-600'" x-text="activeKasusCount">3</span>
            </button>

            <button type="button" 
                    @click="selectedTab = 'tertib'"
                    class="flex-1 py-2 px-3 rounded-xl font-bold transition-all flex items-center justify-center space-x-1.5 cursor-pointer"
                    :class="selectedTab === 'tertib' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                <span>Sudah Tertib</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="selectedTab === 'tertib' ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-600'" x-text="tertibKasusCount">0</span>
            </button>

            <button type="button" 
                    @click="selectedTab = 'semua'"
                    class="flex-1 py-2 px-3 rounded-xl font-bold transition-all flex items-center justify-center space-x-1.5 cursor-pointer"
                    :class="selectedTab === 'semua' ? 'bg-slate-800 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                <span>Semua</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="selectedTab === 'semua' ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-600'" x-text="kasusList.length">3</span>
            </button>
        </div>


        <!-- ===================================================================== -->
        <!-- 3. LIST ANTREAN KASUS PELANGGARAN SISWA                                -->
        <!-- ===================================================================== -->
        <div class="space-y-3.5 pt-1">
            
            <template x-for="kasus in filteredKasus" :key="kasus.id">
                <div class="rounded-3xl p-5 border shadow-soft transition-all duration-200 relative group"
                     :class="[kasus.borderHighlight, kasus.isNew ? 'ring-2 ring-blue-400/50' : '']">

                    <!-- Top Row: Student Name, Class Badge, Level Badge -->
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <h4 class="font-extrabold text-sm sm:text-base text-slate-900 tracking-tight" x-text="kasus.nama">
                                I Gede Bagus Mahendra
                            </h4>

                            <!-- Class Badge (e.g. XII RPL 1, XI TKJ 2) -->
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 shadow-2xs"
                                  x-text="kasus.kelas">
                                XII RPL 1
                            </span>
                        </div>

                        <!-- Level Badge (e.g. Perhatian BK, Kritis / SP, Ringan, Sudah Tertib) -->
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] sm:text-[11px] font-bold border shrink-0 shadow-2xs"
                              :class="kasus.tingkatBadgeClass"
                              x-text="kasus.tingkat">
                            Perhatian BK
                        </span>
                    </div>

                    <!-- Row 2: Violation Description with Warning Icon (Exact Mockup Match) -->
                    <div class="flex items-start space-x-2 pt-2.5">
                        <i data-lucide="alert-circle" 
                           class="w-4 h-4 shrink-0 mt-0.5"
                           :class="kasus.status === 'tertib' ? 'text-emerald-500' : (kasus.poin >= 50 ? 'text-rose-500' : 'text-amber-500')">
                        </i>
                        <p class="text-xs sm:text-[13px] font-semibold leading-relaxed"
                           :class="kasus.status === 'tertib' ? 'text-emerald-900' : 'text-slate-800'"
                           x-text="kasus.deskripsi">
                            Terlihat merokok di area belakang kantin
                        </p>
                    </div>

                    <!-- Row 3: Meta Info (Time & Reporter) (Exact Mockup Match) -->
                    <div class="flex flex-wrap items-center text-[11px] text-slate-400 font-medium space-x-3 pt-1">
                        <span class="flex items-center space-x-1">
                            <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span x-text="kasus.waktu">10 menit lalu</span>
                        </span>
                        <span>•</span>
                        <span class="flex items-center space-x-1">
                            <i data-lucide="user" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span x-text="kasus.pelapor">Laporan Siswa (Anonim)</span>
                        </span>
                    </div>

                    <!-- Row 4: Divider & Bottom Action Row (Points, - Kurang, + Tambah, ✓ Checkmark) -->
                    <div class="border-t border-slate-100/90 pt-3 mt-3 flex items-center justify-between">
                        
                        <!-- Left: Points Accumulation -->
                        <div>
                            <template x-if="kasus.status === 'aktif'">
                                <div>
                                    <p class="text-[10px] uppercase font-bold text-slate-400">Akumulasi Poin:</p>
                                    <div class="flex items-baseline space-x-1">
                                        <span class="text-xl sm:text-2xl font-black tracking-tight"
                                              :class="kasus.poin >= 50 ? 'text-rose-600' : (kasus.poin >= 25 ? 'text-amber-600' : 'text-emerald-600')"
                                              x-text="kasus.poin">
                                            40
                                        </span>
                                        <span class="text-xs font-bold text-slate-400">Poin</span>
                                    </div>
                                </div>
                            </template>

                            <template x-if="kasus.status === 'tertib'">
                                <div>
                                    <p class="text-[10px] uppercase font-extrabold text-emerald-600">Poin Ketertiban Ditambah:</p>
                                    <div class="flex items-baseline space-x-1">
                                        <span class="text-xl sm:text-2xl font-black text-emerald-600 tracking-tight"
                                              x-text="'+' + kasus.poinTertib">
                                            +15
                                        </span>
                                        <span class="text-xs font-bold text-emerald-700">Poin Reward</span>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Right: Action Buttons (- Kurang, + Tambah, ✓ Checkmark) -->
                        <div class="flex items-center space-x-2">
                            
                            <!-- If case is still active -->
                            <template x-if="kasus.status === 'aktif'">
                                <div class="flex items-center space-x-2">
                                    <!-- Button: - Kurang (Green Pill - Exact Mockup Match) -->
                                    <button type="button" 
                                            @click="kurangPoin(kasus)"
                                            title="Kurangi 5 Poin Sanksi"
                                            class="px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-300 transition-all active:scale-95 cursor-pointer shadow-2xs">
                                        — Kurang
                                    </button>

                                    <!-- Button: + Tambah (Rose Pill - Exact Mockup Match) -->
                                    <button type="button" 
                                            @click="tambahPoin(kasus)"
                                            title="Tambah 5 Poin Sanksi"
                                            class="px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 transition-all active:scale-95 cursor-pointer shadow-2xs">
                                        + Tambah
                                    </button>

                                    <!-- Button: ✓ Checkmark (Tandai Sudah Tertib & Tambah Poin Siswa) -->
                                    <button type="button" 
                                            @click="bukaModalTertib(kasus)"
                                            title="Tandai Siswa Sudah Tertib & Beri Tambahan Poin"
                                            class="w-9 h-9 rounded-full bg-slate-100 hover:bg-emerald-600 hover:text-white text-slate-600 border border-slate-200 hover:border-emerald-600 flex items-center justify-center transition-all active:scale-95 cursor-pointer shadow-2xs group-btn">
                                        <i data-lucide="check" class="w-4 h-4 stroke-[3]"></i>
                                    </button>
                                </div>
                            </template>

                            <!-- If case is already marked 'tertib' -->
                            <template x-if="kasus.status === 'tertib'">
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                        <span>Sudah Tertib</span>
                                    </span>

                                    <!-- Undo Button -->
                                    <button type="button" 
                                            @click="batalkanStatusTertib(kasus)"
                                            title="Buka kembali kasus"
                                            class="p-1.5 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors">
                                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </template>

                        </div>

                    </div>

                </div>
            </template>

            <!-- Fallback Empty State -->
            <div x-show="filteredKasus.length === 0" 
                 class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-soft text-center space-y-3">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                    <i data-lucide="inbox" class="w-7 h-7"></i>
                </div>
                <div>
                    <h4 class="font-bold text-sm text-slate-800">Tidak Ada Kasus Pelanggaran</h4>
                    <p class="text-xs text-slate-400 mt-0.5">Semua laporan telah ditangani atau filter tidak menemukan hasil.</p>
                </div>
                <button type="button" 
                        @click="selectedTab = 'semua'; searchQuery = ''" 
                        class="text-xs font-bold text-blue-600 hover:underline">
                    Reset Filter
                </button>
            </div>

        </div>

    </main>


    <!-- ========================================================================= -->
    <!-- MODAL 1: TANDAI SISWA SUDAH TERTIB & TAMBAH POIN KEDISIPLINAN             -->
    <!-- ========================================================================= -->
    <div x-show="modalKonfirmasiTertib" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
        
        <div @click.away="modalKonfirmasiTertib = false" 
             x-show="modalKonfirmasiTertib" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-100 space-y-5">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                        <i data-lucide="award" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-slate-900">Siswa Sudah Tertib</h3>
                        <p class="text-xs text-slate-500">Penyelesaian Pembinaan & Reward Poin</p>
                    </div>
                </div>
                <button @click="modalKonfirmasiTertib = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Student Info Card -->
            <div class="p-4 rounded-2xl bg-emerald-50/80 border border-emerald-200 space-y-1.5 text-xs text-left">
                <div class="flex items-center justify-between">
                    <span class="font-extrabold text-slate-900 text-sm" x-text="selectedKasusTertib ? selectedKasusTertib.nama : ''">Nama Siswa</span>
                    <span class="bg-blue-100 text-blue-700 font-bold px-2 py-0.5 rounded-full text-[11px]" x-text="selectedKasusTertib ? selectedKasusTertib.kelas : ''">XII RPL 1</span>
                </div>
                <p class="text-slate-600 text-[11px]" x-text="'Pelanggaran Sebelumnya: ' + (selectedKasusTertib ? selectedKasusTertib.deskripsi : '')"></p>
                <p class="text-emerald-700 font-extrabold text-[11px] pt-1">
                    ✓ Siswa telah menunjukkan itikad baik dan mematuhi tata tertib sekolah.
                </p>
            </div>

            <!-- Form: Tambah Poin Kedisiplinan -->
            <div class="space-y-3 text-xs text-left">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        Tambah Poin Ketertiban / Penghargaan Disiplin:
                    </label>
                    <div class="grid grid-cols-4 gap-2">
                        <button type="button" @click="poinTertibTambahan = 10" 
                                :class="poinTertibTambahan === 10 ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-100 text-slate-700'"
                                class="py-2 rounded-xl text-center cursor-pointer transition-all">
                            +10 Poin
                        </button>
                        <button type="button" @click="poinTertibTambahan = 15" 
                                :class="poinTertibTambahan === 15 ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-100 text-slate-700'"
                                class="py-2 rounded-xl text-center cursor-pointer transition-all">
                            +15 Poin
                        </button>
                        <button type="button" @click="poinTertibTambahan = 20" 
                                :class="poinTertibTambahan === 20 ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-100 text-slate-700'"
                                class="py-2 rounded-xl text-center cursor-pointer transition-all">
                            +20 Poin
                        </button>
                        <button type="button" @click="poinTertibTambahan = 30" 
                                :class="poinTertibTambahan === 30 ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-100 text-slate-700'"
                                class="py-2 rounded-xl text-center cursor-pointer transition-all">
                            +30 Poin
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Catatan Pembinaan Guru BK</label>
                    <textarea x-model="catatanTertib" rows="3" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 outline-none text-xs bg-slate-50"></textarea>
                </div>
            </div>

            <div class="flex items-center space-x-2 pt-1">
                <button type="button" @click="modalKonfirmasiTertib = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-bold text-xs cursor-pointer">
                    Batal
                </button>
                <button type="button" @click="konfirmasiSiswaTertib()" class="flex-2 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-black text-xs shadow-md shadow-emerald-500/20 cursor-pointer flex items-center justify-center space-x-1.5">
                    <i data-lucide="check" class="w-4 h-4 stroke-[3]"></i>
                    <span>Siswa Tertib & Tambah Poin</span>
                </button>
            </div>

        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- MODAL 2: INPUT LAPORAN PELANGGARAN BARU DARI SISWA / PIKET                 -->
    <!-- ========================================================================= -->
    <div x-show="modalBuatLaporan" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
        
        <div @click.away="modalBuatLaporan = false" 
             x-show="modalBuatLaporan" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-100 space-y-5">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                        <i data-lucide="file-plus-2" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-slate-900">Laporkan Pelanggaran</h3>
                        <p class="text-xs text-slate-500">Pesan Laporan Masuk dari Siswa / Piket</p>
                    </div>
                </div>
                <button @click="modalBuatLaporan = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form @submit.prevent="simpanLaporanBaru()" class="space-y-3.5 text-xs text-left">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Siswa yang Melanggar</label>
                    <input type="text" x-model="formBaru.nama" placeholder="Contoh: I Putu Bagus Mahendra" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kelas</label>
                        <select x-model="formBaru.kelas" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                            <option value="X RPL 1">X RPL 1</option>
                            <option value="XI PPLG 1">XI PPLG 1</option>
                            <option value="XI PPLG 2">XI PPLG 2</option>
                            <option value="XI TKJ 2">XI TKJ 2</option>
                            <option value="XII RPL 1">XII RPL 1</option>
                            <option value="XII DKV 1">XII DKV 1</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tingkat Kasus</label>
                        <select x-model="formBaru.tingkat" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none bg-white font-bold">
                            <option value="Ringan">Ringan (Hijau)</option>
                            <option value="Perhatian BK">Perhatian BK (Kuning)</option>
                            <option value="Kritis / SP">Kritis / SP (Merah)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Rincian Pelanggaran</label>
                    <input type="text" x-model="formBaru.deskripsi" placeholder="Contoh: Merokok di belakang kantin saat istirahat" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Sumber Pelapor</label>
                        <select x-model="formBaru.pelapor" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                            <option value="Laporan Siswa (Anonim)">Laporan Siswa (Anonim)</option>
                            <option value="Guru Piket Lab TKJ">Guru Piket Lab TKJ</option>
                            <option value="Laporan Gerbang Depan">Laporan Gerbang Depan</option>
                            <option value="Wali Kelas">Wali Kelas</option>
                            <option value="Koordinator BK">Koordinator BK</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Bobot Poin Sanksi</label>
                        <select x-model="formBaru.poinAwal" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none bg-white font-bold text-rose-600">
                            <option value="15">15 Poin (Ringan)</option>
                            <option value="25">25 Poin (Sedang)</option>
                            <option value="40">40 Poin (Perhatian BK)</option>
                            <option value="55">55 Poin (Kritis / SP)</option>
                        </select>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black text-xs shadow-md shadow-blue-500/25 flex items-center justify-center space-x-1.5 cursor-pointer">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Kirim Laporan ke Antrean Kasus</span>
                    </button>
                </div>
            </form>

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
         class="fixed bottom-6 inset-x-0 mx-auto max-w-sm px-4 z-50">
        <div class="rounded-2xl p-3.5 shadow-2xl flex items-center space-x-3 text-xs border"
             :class="{
                 'bg-emerald-900/95 text-white border-emerald-700': toastType === 'success',
                 'bg-amber-900/95 text-white border-amber-700': toastType === 'warning',
                 'bg-slate-900/95 text-white border-slate-700': toastType === 'info'
             }">
            <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0"
                 :class="{
                     'bg-emerald-500/30 text-emerald-300': toastType === 'success',
                     'bg-amber-500/30 text-amber-300': toastType === 'warning',
                     'bg-blue-500/30 text-blue-300': toastType === 'info'
                 }">
                <i data-lucide="bell" class="w-4 h-4"></i>
            </div>
            <p class="font-bold flex-1" x-text="toastMessage"></p>
            <button @click="showToast = false" class="text-white/60 hover:text-white cursor-pointer">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
            </button>
        </div>
    </div>

</body>
</html>

