<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pengajuan Konseling & BK - SMK TI Bali Global Badung</title>

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
                            600: '#2563eb', // Royal Blue
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
    </style>
</head>
<body class="bg-[#F4F6FB] text-slate-800 antialiased min-h-screen flex flex-col selection:bg-blue-600 selection:text-white"
      x-data="{
          // State & Tabs
          searchQuery: '',
          selectedTab: 'menunggu', // 'menunggu', 'terjadwal', 'selesai', 'semua'

          // Modals
          modalJadwalkan: false,
          modalTolak: false,
          modalSelesai: false,
          modalAjukanBaru: false,

          selectedKasus: null,

          // Form Jadwalkan
          formJadwal: {
              tanggal: '{{ \Carbon\Carbon::today()->toDateString() }}',
              jam: '10:30 WITA',
              ruang: 'Ruang BK 1 (Lantai 2)',
              konselor: 'Dra. Ni Luh Suastini, S.Pd',
              catatan: 'Siapkan catatan ringkas kendala dan dokumen terkait.'
          },

          // Form Tolak
          formTolak: {
              alasan: 'Jadwal sesi minggu ini telah penuh. Diarahkan berkonsultasi awal ke Wali Kelas terlebih dahulu.'
          },

          // Form Selesai
          formSelesai: {
              hasil: 'Siswa telah diberikan arahan pemecahan masalah dan menunjukkan penurunan tingkat kecemasan.'
          },

          // Form Input Baru
          formBaru: {
              nama: '',
              kelas: 'XI PPLG 1',
              kategori: 'Karir & Magang',
              prioritas: 'Prioritas / Mendesak',
              deskripsi: '',
              pelapor: 'Pengajuan Mandiri Siswa'
          },

          // Toast Feedback
          showToast: false,
          toastMessage: '',
          toastType: 'success',
          showToastUndo: false,
          lastSelesaiKasus: null,

          // Data Antrean Pengajuan Konseling (Exact from Reference Image)
          konselingList: [
              {
                  id: 1,
                  nama: 'Ni Putu Ayu Maharani',
                  kelas: 'XII DKV 2',
                  prioritas: 'Prioritas / Mendesak',
                  prioritasClass: 'bg-gradient-to-r from-amber-100 to-yellow-100 text-amber-900 border border-amber-300 font-bold',
                  deskripsi: 'Mengalami stres persiapan UKK dan kendala memilih tempat magang industri PKL',
                  waktu: '15 menit lalu',
                  pelapor: 'Pengajuan Mandiri Siswa',
                  kategori: 'Karir & Magang',
                  kategoriColor: 'text-blue-600',
                  status: 'menunggu', // 'menunggu', 'terjadwal', 'selesai', 'ditolak'
                  jadwalInfo: null,
                  isNew: false
              },
              {
                  id: 2,
                  nama: 'Komang Aditya Pradnyana',
                  kelas: 'XI RPL 3',
                  prioritas: 'Perlu Pendampingan',
                  prioritasClass: 'bg-rose-100 text-rose-800 border border-rose-200 font-bold',
                  deskripsi: 'Kesulitan fokus belajar akibat konflik kelompok kerja dan rasa cemas berlebih',
                  waktu: '40 menit lalu',
                  pelapor: 'Pengajuan Mandiri Siswa',
                  kategori: 'Pribadi & Sosial',
                  kategoriColor: 'text-rose-600',
                  status: 'menunggu',
                  jadwalInfo: null,
                  isNew: false
              },
              {
                  id: 3,
                  nama: 'I Made Bagus Danendra',
                  kelas: 'X TKJ 1',
                  prioritas: 'Bimbingan Belajar',
                  prioritasClass: 'bg-emerald-100 text-emerald-800 border border-emerald-200 font-bold',
                  deskripsi: 'Butuh saran adaptasi di kelas baru dan metode belajar praktikum jaringan komputer',
                  waktu: 'Pagi ini, 07:45 WITA',
                  pelapor: 'Pengajuan Mandiri Siswa',
                  kategori: 'Akademik & Nilai',
                  kategoriColor: 'text-emerald-600',
                  status: 'menunggu',
                  jadwalInfo: null,
                  isNew: false
              }
          ],

          // Counter Dinamis
          get activePermohonanCount() {
              return this.konselingList.filter(k => k.status === 'menunggu').length;
          },

          get terjadwalCount() {
              return this.konselingList.filter(k => k.status === 'terjadwal').length;
          },

          get selesaiCount() {
              return this.konselingList.filter(k => k.status === 'selesai').length;
          },

          get filteredKonseling() {
              return this.konselingList.filter(k => {
                  const matchTab = (this.selectedTab === 'semua')
                      || (this.selectedTab === 'menunggu' && k.status === 'menunggu')
                      || (this.selectedTab === 'terjadwal' && k.status === 'terjadwal')
                      || (this.selectedTab === 'selesai' && k.status === 'selesai');

                  const q = this.searchQuery.toLowerCase().trim();
                  const matchQuery = !q
                      || k.nama.toLowerCase().includes(q)
                      || k.kelas.toLowerCase().includes(q)
                      || k.deskripsi.toLowerCase().includes(q)
                      || k.kategori.toLowerCase().includes(q);

                  return matchTab && matchQuery;
              });
          },

          init() {
              this.$nextTick(() => {
                  if (window.lucide) lucide.createIcons();
              });
          },

          // 1. Aksi Jadwalkan Sesi Konseling
          bukaModalJadwalkan(kasus) {
              this.selectedKasus = kasus;
              this.formJadwal.jam = '10:30 WITA';
              this.modalJadwalkan = true;
              this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
          },

          konfirmasiJadwalkan() {
              if (!this.selectedKasus) return;
              this.selectedKasus.status = 'terjadwal';
              this.selectedKasus.jadwalInfo = {
                  tanggal: this.formJadwal.tanggal,
                  jam: this.formJadwal.jam,
                  ruang: this.formJadwal.ruang,
                  konselor: this.formJadwal.konselor
              };

              this.modalJadwalkan = false;
              this.playChime();
              this.triggerToast('Sesi konseling ' + this.selectedKasus.nama + ' dijadwalkan pada ' + this.formJadwal.jam + ' di ' + this.formJadwal.ruang, 'success');
              this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
          },

          // 2. Aksi Tolak Permohonan
          bukaModalTolak(kasus) {
              this.selectedKasus = kasus;
              this.modalTolak = true;
              this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
          },

          konfirmasiTolak() {
              if (!this.selectedKasus) return;
              this.selectedKasus.status = 'ditolak';
              this.modalTolak = false;
              this.triggerToast('Permohonan konseling ' + this.selectedKasus.nama + ' telah dialihkan.', 'info');
              this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
          },

          // 3. Aksi Tandai Selesai (Langsung Hilang saat Dicentang)
          centangSelesai(kasus) {
              kasus.isChecking = true;
              this.playChime();

              // Transisi halus lalu hilangkan dari antrean
              setTimeout(() => {
                  kasus.status = 'selesai';
                  kasus.isChecking = false;
                  this.lastSelesaiKasus = kasus;
                  this.triggerToast('✓ Pengajuan konseling ' + kasus.nama + ' selesai & dihapus dari antrean.', 'success', true);
                  this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
              }, 250);
          },

          pulihkanKasusTerakhir() {
              if (this.lastSelesaiKasus) {
                  this.lastSelesaiKasus.status = 'menunggu';
                  this.lastSelesaiKasus.isChecking = false;
                  this.triggerToast('Pengajuan ' + this.lastSelesaiKasus.nama + ' dikembalikan ke antrean.', 'info');
                  this.lastSelesaiKasus = null;
                  this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
              }
          },

          bukaModalSelesai(kasus) {
              this.selectedKasus = kasus;
              this.modalSelesai = true;
              this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
          },

          konfirmasiSelesai() {
              if (!this.selectedKasus) return;
              this.centangSelesai(this.selectedKasus);
              this.modalSelesai = false;
          },

          // 4. Input Permohonan Baru
          simpanPermohonanBaru() {
              if (!this.formBaru.nama.trim() || !this.formBaru.deskripsi.trim()) {
                  this.triggerToast('Mohon lengkapi nama siswa dan ringkasan masalah.', 'warning');
                  return;
              }

              let prioritasBadge = 'bg-emerald-100 text-emerald-800 border-emerald-200 font-bold';
              if (this.formBaru.prioritas === 'Prioritas / Mendesak') {
                  prioritasBadge = 'bg-gradient-to-r from-amber-100 to-yellow-100 text-amber-900 border border-amber-300 font-bold';
              } else if (this.formBaru.prioritas === 'Perlu Pendampingan') {
                  prioritasBadge = 'bg-rose-100 text-rose-800 border border-rose-200 font-bold';
              }

              let katColor = 'text-blue-600';
              if (this.formBaru.kategori === 'Pribadi & Sosial') katColor = 'text-rose-600';
              if (this.formBaru.kategori === 'Akademik & Nilai') katColor = 'text-emerald-600';

              const itemBaru = {
                  id: Date.now(),
                  nama: this.formBaru.nama.trim(),
                  kelas: this.formBaru.kelas,
                  prioritas: this.formBaru.prioritas,
                  prioritasClass: prioritasBadge,
                  deskripsi: this.formBaru.deskripsi.trim(),
                  waktu: 'Baru saja',
                  pelapor: this.formBaru.pelapor,
                  kategori: this.formBaru.kategori,
                  kategoriColor: katColor,
                  status: 'menunggu',
                  jadwalInfo: null,
                  isNew: true
              };

              this.konselingList.unshift(itemBaru);
              this.selectedTab = 'menunggu';
              this.modalAjukanBaru = false;

              // Reset form
              this.formBaru.nama = '';
              this.formBaru.deskripsi = '';

              this.playBeepNotification();
              this.triggerToast('Permohonan konseling baru berhasil ditambahkan ke antrean!', 'success');
              this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
          },

          // 5. Tes Siswa Mengajukan Realtime (Simulasi)
          simulasiSiswaMengajukanRealtime() {
              const demoList = [
                  {
                      nama: 'Putu Bagus Wicaksana',
                      kelas: 'XI PPLG 2',
                      prioritas: 'Prioritas / Mendesak',
                      prioritasClass: 'bg-gradient-to-r from-amber-100 to-yellow-100 text-amber-900 border border-amber-300 font-bold',
                      deskripsi: 'Merasa tertekan dengan tugas akhir pemrograman dan butuh manajemen waktu belajar',
                      pelapor: 'Pengajuan Mandiri Siswa',
                      kategori: 'Akademik & Nilai',
                      kategoriColor: 'text-emerald-600'
                  },
                  {
                      nama: 'Ni Kadek Sintya Dewi',
                      kelas: 'X DKV 1',
                      prioritas: 'Perlu Pendampingan',
                      prioritasClass: 'bg-rose-100 text-rose-800 border border-rose-200 font-bold',
                      deskripsi: 'Mengalami kesulitan adaptasi sosial di asrama/lingkungan kelas baru',
                      pelapor: 'Pengajuan Mandiri Siswa',
                      kategori: 'Pribadi & Sosial',
                      kategoriColor: 'text-rose-600'
                  },
                  {
                      nama: 'I Made Wahyu Ariyasa',
                      kelas: 'XII TKJ 1',
                      prioritas: 'Bimbingan Belajar',
                      prioritasClass: 'bg-emerald-100 text-emerald-800 border border-emerald-200 font-bold',
                      deskripsi: 'Membutuhkan saran persiapan tes masuk perguruan tinggi politeknik negeri',
                      pelapor: 'Pengajuan Mandiri Siswa',
                      kategori: 'Karir & Magang',
                      kategoriColor: 'text-blue-600'
                  }
              ];

              const sample = demoList[Math.floor(Math.random() * demoList.length)];

              const itemBaru = {
                  id: Date.now(),
                  nama: sample.nama,
                  kelas: sample.kelas,
                  prioritas: sample.prioritas,
                  prioritasClass: sample.prioritasClass,
                  deskripsi: sample.deskripsi,
                  waktu: 'Baru saja (Realtime)',
                  pelapor: sample.pelapor,
                  kategori: sample.kategori,
                  kategoriColor: sample.kategoriColor,
                  status: 'menunggu',
                  jadwalInfo: null,
                  isNew: true
              };

              this.konselingList.unshift(itemBaru);
              this.selectedTab = 'menunggu';

              this.playBeepNotification();
              this.triggerToast('🔔 Permohonan Konseling Baru Masuk: ' + sample.nama + ' (' + sample.kelas + ')', 'warning');
              this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
          },

          // Audio Feedback
          playBeepNotification() {
              try {
                  const AudioCtx = window.AudioContext || window.webkitAudioContext;
                  if (!AudioCtx) return;
                  const ctx = new AudioCtx();
                  const osc = ctx.createOscillator();
                  const gain = ctx.createGain();
                  osc.type = 'triangle';
                  osc.frequency.setValueAtTime(523.25, ctx.currentTime);
                  osc.frequency.setValueAtTime(783.99, ctx.currentTime + 0.1);
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
                  osc.frequency.setValueAtTime(523.25, ctx.currentTime);
                  osc.frequency.setValueAtTime(659.25, ctx.currentTime + 0.12);
                  osc.frequency.setValueAtTime(783.99, ctx.currentTime + 0.24);
                  gain.gain.setValueAtTime(0.15, ctx.currentTime);
                  gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
                  osc.connect(gain);
                  gain.connect(ctx.destination);
                  osc.start();
                  osc.stop(ctx.currentTime + 0.5);
              } catch(e) {}
          },

          triggerToast(msg, type = 'success', showUndo = false) {
              this.toastMessage = msg;
              this.toastType = type;
              this.showToastUndo = showUndo;
              this.showToast = true;
              this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
              setTimeout(() => { this.showToast = false; }, 4500);
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

                <!-- Right Avatar with Red Robot/User Icon (Exact Mockup Match) -->
                <a href="{{ route('guru.profile') }}" 
                   title="Profil Guru BK"
                   class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-rose-500/80 text-white border-2 border-white flex items-center justify-center shadow-lg hover:scale-105 active:scale-95 transition-all cursor-pointer shrink-0">
                    <i data-lucide="bot" class="w-5 h-5 sm:w-6 sm:h-6 stroke-[2.2]"></i>
                </a>
            </div>

            <!-- Page Title: "Pengajuan Konseling" (Exact Mockup Match) -->
            <div class="pt-2 text-center">
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight leading-tight">
                    Pengajuan<br>Konseling
                </h1>

                <!-- Blue Translucent Stat Card inside Hero: "Pengajuan Konseling BK: 3 Permohonan" -->
                <div class="mt-4 bg-white/15 backdrop-blur-md border border-white/25 rounded-2xl p-4 sm:p-5 text-center max-w-xs mx-auto shadow-md">
                    <p class="text-xs font-semibold text-blue-100 tracking-wide">
                        Pengajuan Konseling BK
                    </p>
                    <h2 class="text-2xl sm:text-3xl font-black text-white mt-0.5 tracking-tight"
                        x-text="activePermohonanCount + ' Permohonan'">
                        3 Permohonan
                    </h2>
                </div>
            </div>

        </div>
    </header>


    <!-- ========================================================================= -->
    <!-- 2. MAIN CONTENT CONTAINER (Mobile & Desktop Responsive)                   -->
    <!-- ========================================================================= -->
    <main class="max-w-xl mx-auto w-full px-4 sm:px-6 pb-20 space-y-4">
        
        <!-- Floating Pill Notification: "💬 Pengajuan Konseling [ 3 ]" (Exact Mockup Match) -->
        <div class="relative -mt-5 z-20 flex justify-center">
            <button type="button" 
                    @click="selectedTab = 'menunggu'; triggerToast('Menampilkan antrean permohonan konseling aktif (' + activePermohonanCount + ' permohonan)', 'info')"
                    class="bg-white/95 hover:bg-white text-slate-800 border border-slate-200/90 shadow-md shadow-slate-200/80 rounded-full py-2.5 px-6 flex items-center space-x-2.5 transition-all cursor-pointer hover:scale-105 active:scale-95 group">
                <i data-lucide="messages-square" class="w-4 h-4 text-blue-600 group-hover:scale-110 transition-transform"></i>
                <span class="font-extrabold text-xs sm:text-sm text-blue-700">Pengajuan Konseling</span>
                <span class="bg-blue-100 text-blue-800 font-black text-xs px-2.5 py-0.5 rounded-full" 
                      x-text="activePermohonanCount">
                    3
                </span>
            </button>
        </div>


        <!-- Section Header: ANTREAN PENGAJUAN KONSELING + Realtime Badge -->
        <div class="pt-2 flex items-center justify-between">
            <div>
                <h3 class="text-xs sm:text-sm font-black uppercase tracking-wider text-slate-900">
                    ANTREAN PENGAJUAN KONSELING
                </h3>
                <p class="text-[11px] sm:text-xs text-slate-400 font-medium mt-0.5">
                    Data otomatis terisi saat ada siswa mengajukan jadwal bimbingan
                </p>
            </div>

            <!-- Green Realtime Pill Badge (Exact Mockup Match) -->
            <span class="bg-emerald-50 text-emerald-600 border border-emerald-200 px-3 py-1 rounded-full text-xs font-black inline-flex items-center space-x-1.5 shrink-0 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Realtime</span>
            </span>
        </div>


        <!-- Quick Action Bar: Input Baru & Simulasi Siswa Mengajukan Realtime -->
        <div class="flex items-center space-x-2 pt-1">
            <!-- Button: + Ajukan Sesi Baru -->
            <button type="button" 
                    @click="modalAjukanBaru = true"
                    class="flex-1 py-2.5 px-3.5 bg-blue-600 hover:bg-blue-700 active:scale-98 text-white rounded-2xl font-extrabold text-xs flex items-center justify-center space-x-1.5 shadow-md shadow-blue-500/20 transition-all cursor-pointer">
                <i data-lucide="calendar-plus" class="w-4 h-4"></i>
                <span>+ Ajukan Sesi Baru</span>
            </button>

            <!-- Button: ⚡ Simulasi Siswa Mengajukan -->
            <button type="button" 
                    @click="simulasiSiswaMengajukanRealtime()"
                    title="Simulasikan siswa baru mengirim permohonan bimbingan konseling"
                    class="py-2.5 px-3.5 bg-blue-50 hover:bg-blue-100 text-blue-800 border border-blue-200 rounded-2xl font-extrabold text-xs flex items-center justify-center space-x-1.5 transition-all cursor-pointer active:scale-95 shadow-2xs">
                <i data-lucide="zap" class="w-4 h-4 text-blue-600"></i>
                <span>Tes Siswa Mengajukan</span>
            </button>
        </div>


        <!-- Tab Filter: Permohonan Menunggu, Terjadwal, Selesai, Semua -->
        <div class="bg-white p-1 rounded-2xl border border-slate-200/80 shadow-2xs flex items-center text-xs">
            <button type="button" 
                    @click="selectedTab = 'menunggu'"
                    class="flex-1 py-2 px-2.5 rounded-xl font-bold transition-all flex items-center justify-center space-x-1.5 cursor-pointer"
                    :class="selectedTab === 'menunggu' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                <span>Antrean Masuk</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="selectedTab === 'menunggu' ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-600'" x-text="activePermohonanCount">3</span>
            </button>

            <button type="button" 
                    @click="selectedTab = 'terjadwal'"
                    class="flex-1 py-2 px-2.5 rounded-xl font-bold transition-all flex items-center justify-center space-x-1.5 cursor-pointer"
                    :class="selectedTab === 'terjadwal' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                <span>Terjadwal</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="selectedTab === 'terjadwal' ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-600'" x-text="terjadwalCount">0</span>
            </button>

            <button type="button" 
                    @click="selectedTab = 'selesai'"
                    class="flex-1 py-2 px-2.5 rounded-xl font-bold transition-all flex items-center justify-center space-x-1.5 cursor-pointer"
                    :class="selectedTab === 'selesai' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                <span>Selesai</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="selectedTab === 'selesai' ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-600'" x-text="selesaiCount">0</span>
            </button>

            <button type="button" 
                    @click="selectedTab = 'semua'"
                    class="flex-1 py-2 px-2.5 rounded-xl font-bold transition-all flex items-center justify-center space-x-1.5 cursor-pointer"
                    :class="selectedTab === 'semua' ? 'bg-slate-800 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                <span>Semua</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="selectedTab === 'semua' ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-600'" x-text="konselingList.length">3</span>
            </button>
        </div>


        <!-- ===================================================================== -->
        <!-- 3. LIST ANTREAN PENGAJUAN KONSELING SISWA                             -->
        <!-- ===================================================================== -->
        <div class="space-y-3.5 pt-1">
            
            <template x-for="item in filteredKonseling" :key="item.id">
                <div class="rounded-3xl p-5 border bg-white shadow-soft transition-all duration-300 relative group"
                     :class="[
                         item.isNew ? 'ring-2 ring-blue-400/50' : 'border-slate-200/80',
                         item.isChecking ? 'opacity-0 scale-95 -translate-y-2 pointer-events-none' : 'opacity-100 scale-100'
                     ]">

                    <!-- Top Row: Student Name, Class Badge, Priority Badge -->
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <h4 class="font-extrabold text-sm sm:text-base text-slate-900 tracking-tight" x-text="item.nama">
                                Ni Putu Ayu Maharani
                            </h4>

                            <!-- Class Badge (e.g. XII DKV 2, XI RPL 3) -->
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] sm:text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 shadow-2xs"
                                  x-text="item.kelas">
                                XII DKV 2
                            </span>
                        </div>

                        <!-- Priority Badge (Exact Mockup Match) -->
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] sm:text-[11px] font-bold border shrink-0 shadow-2xs"
                              :class="item.prioritasClass"
                              x-text="item.prioritas">
                            Prioritas / Mendesak
                        </span>
                    </div>

                    <!-- Row 2: Issue Description with Warning Icon (Exact Mockup Match) -->
                    <div class="flex items-start space-x-2 pt-2.5">
                        <i data-lucide="alert-circle" 
                           class="w-4 h-4 shrink-0 mt-0.5 text-amber-500">
                        </i>
                        <p class="text-xs sm:text-[13px] font-semibold text-slate-700 leading-relaxed"
                           x-text="item.deskripsi">
                            Mengalami stres persiapan UKK dan kendala memilih tempat magang industri PKL
                        </p>
                    </div>

                    <!-- Row 3: Meta Info (Time & Reporter) (Exact Mockup Match) -->
                    <div class="flex flex-wrap items-center text-[11px] text-slate-400 font-medium space-x-3 pt-1">
                        <span class="flex items-center space-x-1">
                            <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span x-text="item.waktu">15 menit lalu</span>
                        </span>
                        <span>•</span>
                        <span class="flex items-center space-x-1">
                            <i data-lucide="user" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span x-text="item.pelapor">Pengajuan Mandiri Siswa</span>
                        </span>
                    </div>

                    <!-- Extra Scheduled Banner if Status is 'terjadwal' -->
                    <template x-if="item.status === 'terjadwal' && item.jadwalInfo">
                        <div class="mt-3 p-3 rounded-2xl bg-blue-50 border border-blue-200 flex items-center justify-between text-xs">
                            <div class="flex items-center space-x-2">
                                <i data-lucide="calendar-check" class="w-4 h-4 text-blue-600"></i>
                                <div>
                                    <p class="font-extrabold text-blue-950" x-text="'Jadwal: ' + item.jadwalInfo.tanggal + ' pukul ' + item.jadwalInfo.jam"></p>
                                    <p class="text-[11px] text-blue-700" x-text="item.jadwalInfo.ruang + ' • Bersama: ' + item.jadwalInfo.konselor"></p>
                                </div>
                            </div>
                            <span class="bg-blue-600 text-white font-extrabold text-[10px] px-2.5 py-1 rounded-full uppercase">Terjadwal</span>
                        </div>
                    </template>

                    <!-- Row 4: Divider & Bottom Action Row (Kategori Masalah, Tolak, + Jadwalkan, ✓ Checkmark) -->
                    <div class="border-t border-slate-100/90 pt-3 mt-3 flex items-center justify-between">
                        
                        <!-- Left: Problem Category (Exact Mockup Match) -->
                        <div>
                            <p class="text-[10px] uppercase font-bold text-slate-400">Kategori Masalah:</p>
                            <p class="text-xs sm:text-sm font-black tracking-tight"
                               :class="item.kategoriColor"
                               x-text="item.kategori">
                                Karir & Magang
                            </p>
                        </div>

                        <!-- Right: Action Buttons (Tolak, + Jadwalkan, ✓ Checkmark) -->
                        <div class="flex items-center space-x-2">
                            
                            <!-- If status is still 'menunggu' -->
                            <template x-if="item.status === 'menunggu'">
                                <div class="flex items-center space-x-2">
                                    <!-- Button: Tolak (Exact Mockup Match) -->
                                    <button type="button" 
                                            @click="bukaModalTolak(item)"
                                            title="Tolak atau Alihkan Permohonan"
                                            class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-white hover:bg-rose-50 text-slate-700 hover:text-rose-700 border border-slate-200 hover:border-rose-200 transition-all active:scale-95 cursor-pointer shadow-2xs">
                                        Tolak
                                    </button>

                                    <!-- Button: + Jadwalkan (Exact Mockup Match) -->
                                    <button type="button" 
                                            @click="bukaModalJadwalkan(item)"
                                            title="Tetapkan Jadwal Sesi Konseling"
                                            class="px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 transition-all active:scale-95 cursor-pointer shadow-2xs">
                                        + Jadwalkan
                                    </button>

                                    <!-- Button: ✓ Checkmark (Langsung Selesaikan & Hilang dari Antrean) -->
                                    <button type="button" 
                                            @click="centangSelesai(item)"
                                            title="Selesaikan & Hapus dari Antrean"
                                            class="w-9 h-9 rounded-full bg-slate-100 hover:bg-emerald-600 hover:text-white text-slate-600 border border-slate-200 hover:border-emerald-600 flex items-center justify-center transition-all active:scale-90 cursor-pointer shadow-2xs group-hover:border-emerald-300">
                                        <i data-lucide="check" class="w-4 h-4 stroke-[3]"></i>
                                    </button>
                                </div>
                            </template>

                            <!-- If status is 'terjadwal' -->
                            <template x-if="item.status === 'terjadwal'">
                                <div class="flex items-center space-x-2">
                                    <button type="button" 
                                            @click="bukaModalJadwalkan(item)"
                                            class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 cursor-pointer">
                                        Ubah Jadwal
                                    </button>
                                    <button type="button" 
                                            @click="centangSelesai(item)"
                                            class="px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs cursor-pointer flex items-center space-x-1 active:scale-95 transition-all">
                                        <i data-lucide="check" class="w-3.5 h-3.5 stroke-[3]"></i>
                                        <span>Selesaikan Sesi</span>
                                    </button>
                                </div>
                            </template>

                            <!-- If status is 'selesai' -->
                            <template x-if="item.status === 'selesai'">
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center space-x-1 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                        <span>Selesai Dibimbing</span>
                                    </span>
                                    <button type="button" 
                                            @click="item.status = 'menunggu'; triggerToast('Pengajuan ' + item.nama + ' dikembalikan ke antrean.', 'info')"
                                            title="Kembalikan ke antrean masuk"
                                            class="p-1 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors">
                                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </template>

                            <!-- If status is 'ditolak' -->
                            <template x-if="item.status === 'ditolak'">
                                <span class="inline-flex items-center space-x-1 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                    <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                                    <span>Dialihkan / Ditolak</span>
                                </span>
                            </template>

                        </div>

                    </div>

                </div>
            </template>

            <!-- Fallback Empty State -->
            <div x-show="filteredKonseling.length === 0" 
                 class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-soft text-center space-y-3">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                    <i data-lucide="inbox" class="w-7 h-7"></i>
                </div>
                <div>
                    <h4 class="font-bold text-sm text-slate-800">Tidak Ada Pengajuan Konseling</h4>
                    <p class="text-xs text-slate-400 mt-0.5">Semua permohonan bimbingan telah ditangani atau filter tidak menemukan hasil.</p>
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
    <!-- MODAL 1: PENJADWALAN SESI KONSELING (+ JADWALKAN)                          -->
    <!-- ========================================================================= -->
    <div x-show="modalJadwalkan" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
        
        <div @click.away="modalJadwalkan = false" 
             x-show="modalJadwalkan" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-100 space-y-5">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                        <i data-lucide="calendar" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-slate-900">Jadwalkan Bimbingan</h3>
                        <p class="text-xs text-slate-500">Penetapan Waktu & Tempat Konseling</p>
                    </div>
                </div>
                <button @click="modalJadwalkan = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Student Summary -->
            <div class="p-4 rounded-2xl bg-blue-50/80 border border-blue-200 space-y-1 text-xs text-left">
                <div class="flex items-center justify-between">
                    <span class="font-extrabold text-slate-900 text-sm" x-text="selectedKasus ? selectedKasus.nama : ''">Nama Siswa</span>
                    <span class="bg-blue-100 text-blue-700 font-bold px-2 py-0.5 rounded-full text-[11px]" x-text="selectedKasus ? selectedKasus.kelas : ''">XII DKV 2</span>
                </div>
                <p class="text-slate-600 text-[11px]" x-text="'Topik: ' + (selectedKasus ? selectedKasus.deskripsi : '')"></p>
            </div>

            <!-- Form Penjadwalan -->
            <form @submit.prevent="konfirmasiJadwalkan()" class="space-y-3.5 text-xs text-left">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tanggal Konseling</label>
                        <input type="date" x-model="formJadwal.tanggal" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Waktu / Jam Sesi</label>
                        <select x-model="formJadwal.jam" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none bg-white font-bold text-blue-600">
                            <option value="08:30 WITA">08:30 WITA (Pagi)</option>
                            <option value="09:45 WITA">09:45 WITA (Istirahat 1)</option>
                            <option value="10:30 WITA">10:30 WITA (Jam Belajar)</option>
                            <option value="12:30 WITA">12:30 WITA (Istirahat 2)</option>
                            <option value="14:00 WITA">14:00 WITA (Pulang Sekolah)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Ruang / Lokasi Bimbingan</label>
                    <select x-model="formJadwal.ruang" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        <option value="Ruang BK 1 (Lantai 2)">Ruang Konseling BK 1 (Lantai 2)</option>
                        <option value="Ruang BK 2 (Privat)">Ruang Konseling BK 2 (Privat)</option>
                        <option value="Gazebo Literasi Sekolah">Gazebo Literasi Sekolah (Suasana Terbuka)</option>
                        <option value="Konseling Daring (Google Meet)">Konseling Daring (Google Meet)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Guru Konselor Pendamping</label>
                    <input type="text" x-model="formJadwal.konselor" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <button type="button" @click="modalJadwalkan = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-bold text-xs cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="flex-2 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black text-xs shadow-md shadow-blue-500/20 cursor-pointer flex items-center justify-center space-x-1.5">
                        <i data-lucide="calendar-check" class="w-4 h-4"></i>
                        <span>Tetapkan Jadwal Sekarang</span>
                    </button>
                </div>
            </form>

        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- MODAL 2: TOLAK / ALIKHAN PERMOHONAN                                       -->
    <!-- ========================================================================= -->
    <div x-show="modalTolak" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
        
        <div @click.away="modalTolak = false" 
             x-show="modalTolak" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-100 space-y-4">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold">
                        <i data-lucide="x-circle" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-slate-900">Alihkan / Tolak Pengajuan</h3>
                        <p class="text-xs text-slate-500">Berikan Alasan Tindak Lanjut</p>
                    </div>
                </div>
                <button @click="modalTolak = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <p class="text-xs text-slate-600">
                Anda akan mengalihkan permohonan bimbingan dari <strong x-text="selectedKasus ? selectedKasus.nama : ''"></strong>. Siswa akan mendapatkan catatan resmi dari guru BK.
            </p>

            <div>
                <label class="block font-bold text-slate-700 mb-1 text-xs text-left">Alasan & Rekomendasi untuk Siswa</label>
                <textarea x-model="formTolak.alasan" rows="3" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-rose-500 outline-none text-xs bg-slate-50"></textarea>
            </div>

            <div class="flex items-center space-x-2 pt-1">
                <button type="button" @click="modalTolak = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-bold text-xs cursor-pointer">
                    Kembali
                </button>
                <button type="button" @click="konfirmasiTolak()" class="flex-1 py-3 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl font-black text-xs shadow-md shadow-rose-500/20 cursor-pointer">
                    Konfirmasi
                </button>
            </div>

        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- MODAL 3: SELESAIKAN SESI KONSELING                                        -->
    <!-- ========================================================================= -->
    <div x-show="modalSelesai" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
        
        <div @click.away="modalSelesai = false" 
             x-show="modalSelesai" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-100 space-y-4">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                        <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-slate-900">Selesaikan Bimbingan</h3>
                        <p class="text-xs text-slate-500">Konfirmasi Sesi Konseling Berhasil</p>
                    </div>
                </div>
                <button @click="modalSelesai = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <p class="text-xs text-slate-600 text-left">
                Tandai bahwa sesi konseling bersama <strong x-text="selectedKasus ? selectedKasus.nama : ''"></strong> telah selesai dilaksanakan.
            </p>

            <div class="text-left text-xs">
                <label class="block font-bold text-slate-700 mb-1">Catatan Hasil Konseling & Solusi yang Disepakati</label>
                <textarea x-model="formSelesai.hasil" rows="3" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 outline-none text-xs bg-slate-50"></textarea>
            </div>

            <div class="flex items-center space-x-2 pt-1">
                <button type="button" @click="modalSelesai = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-bold text-xs cursor-pointer">
                    Batal
                </button>
                <button type="button" @click="konfirmasiSelesai()" class="flex-2 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-black text-xs shadow-md shadow-emerald-500/20 cursor-pointer flex items-center justify-center space-x-1.5">
                    <i data-lucide="check" class="w-4 h-4 stroke-[3]"></i>
                    <span>Tandai Selesai Dibimbing</span>
                </button>
            </div>

        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- MODAL 4: INPUT PERMOHONAN KONSELING BARU                                  -->
    <!-- ========================================================================= -->
    <div x-show="modalAjukanBaru" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
        
        <div @click.away="modalAjukanBaru = false" 
             x-show="modalAjukanBaru" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-100 space-y-4">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                        <i data-lucide="calendar-plus" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-slate-900">Ajukan Sesi Konseling</h3>
                        <p class="text-xs text-slate-500">Form Permohonan Bimbingan Siswa</p>
                    </div>
                </div>
                <button @click="modalAjukanBaru = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form @submit.prevent="simpanPermohonanBaru()" class="space-y-3.5 text-xs text-left">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Siswa</label>
                    <input type="text" x-model="formBaru.nama" placeholder="Contoh: Putu Bagus Wicaksana" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kelas</label>
                        <select x-model="formBaru.kelas" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                            <option value="X TKJ 1">X TKJ 1</option>
                            <option value="XI PPLG 1">XI PPLG 1</option>
                            <option value="XI PPLG 2">XI PPLG 2</option>
                            <option value="XI RPL 3">XI RPL 3</option>
                            <option value="XII DKV 2">XII DKV 2</option>
                            <option value="XII RPL 1">XII RPL 1</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Prioritas Kasus</label>
                        <select x-model="formBaru.prioritas" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none bg-white font-bold">
                            <option value="Prioritas / Mendesak">Prioritas / Mendesak</option>
                            <option value="Perlu Pendampingan">Perlu Pendampingan</option>
                            <option value="Bimbingan Belajar">Bimbingan Belajar</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kategori Masalah</label>
                    <select x-model="formBaru.kategori" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none bg-white font-bold text-blue-600">
                        <option value="Karir & Magang">Karir & Magang (PKL / Kuliah)</option>
                        <option value="Pribadi & Sosial">Pribadi & Sosial (Keluarga / Teman)</option>
                        <option value="Akademik & Nilai">Akademik & Nilai (Kesulitan Belajar)</option>
                        <option value="Kedisiplinan">Kedisiplinan & Tata Tertib</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Ringkasan Kendala / Cerita Masalah</label>
                    <textarea x-model="formBaru.deskripsi" rows="3" placeholder="Tuliskan kendala yang dihadapi siswa..." required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50"></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black text-xs shadow-md shadow-blue-500/25 flex items-center justify-center space-x-1.5 cursor-pointer">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Kirim Permohonan Konseling</span>
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
            <template x-if="showToastUndo">
                <button type="button" 
                        @click="pulihkanKasusTerakhir()" 
                        class="text-xs font-black underline text-amber-300 hover:text-white px-2 py-0.5 rounded cursor-pointer shrink-0 transition-colors">
                    Urungkan
                </button>
            </template>
            <button @click="showToast = false" class="text-white/60 hover:text-white cursor-pointer ml-1">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
            </button>
        </div>
    </div>

</body>
</html>

