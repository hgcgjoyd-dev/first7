<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil Guru BK - SMK TI Bali Global Badung</title>

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
          // State Data Guru BK
          guru: {
              nama: '{{ session('guru_nama', 'Dra. Ni Luh Suastini, S.Pd') }}',
              nip: '{{ session('guru_nip', '19780512 200501 2 008') }}',
              jabatan: '{{ session('guru_jabatan', 'Koordinator Guru BK & Konselor Sekolah') }}',
              pangkat: 'Pembina, IV/a',
              email: '{{ session('guru_email', 'guru.bk@smktibaliglobal.sch.id') }}',
              telepon: '{{ session('guru_telepon', '+62 813-3890-4421') }}',
              pendidikan: 'S1 Bimbingan dan Konseling - Universitas Pendidikan Ganesha',
              ruang: 'Ruang Bimbingan & Konseling (Gedung A, Lt. 2)',
              jamLayanan: 'Senin - Jumat, 07:00 - 15:30 WITA'
          },

          // Modal & Dialog
          modalEditProfil: false,
          modalGantiPassword: false,
          modalKonfirmasiLogout: false,

          // Edit Profil Form
          formEdit: {
              nama: '',
              telepon: '',
              ruang: '',
              jamLayanan: ''
          },

          // Ganti Password Form
          formPassword: {
              lama: '',
              baru: '',
              konfirmasi: '',
              showLama: false,
              showBaru: false
          },

          // Toast Feedback
          showToast: false,
          toastMessage: '',
          toastType: 'success',

          init() {
              this.formEdit.nama = this.guru.nama;
              this.formEdit.telepon = this.guru.telepon;
              this.formEdit.ruang = this.guru.ruang;
              this.formEdit.jamLayanan = this.guru.jamLayanan;
              this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
          },

          bukaModalEdit() {
              this.formEdit.nama = this.guru.nama;
              this.formEdit.telepon = this.guru.telepon;
              this.formEdit.ruang = this.guru.ruang;
              this.formEdit.jamLayanan = this.guru.jamLayanan;
              this.modalEditProfil = true;
              this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
          },

          simpanEditProfil() {
              if (!this.formEdit.nama.trim()) {
                  this.triggerToast('Nama guru tidak boleh kosong.', 'warning');
                  return;
              }
              this.guru.nama = this.formEdit.nama.trim();
              this.guru.telepon = this.formEdit.telepon.trim();
              this.guru.ruang = this.formEdit.ruang.trim();
              this.guru.jamLayanan = this.formEdit.jamLayanan.trim();

              this.modalEditProfil = false;
              this.triggerToast('Profil Guru BK berhasil diperbarui!', 'success');
              this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
          },

          simpanPassword() {
              if (!this.formPassword.baru || this.formPassword.baru.length < 6) {
                  this.triggerToast('Kata sandi baru minimal 6 karakter.', 'warning');
                  return;
              }
              if (this.formPassword.baru !== this.formPassword.konfirmasi) {
                  this.triggerToast('Konfirmasi kata sandi tidak cocok.', 'warning');
                  return;
              }
              this.modalGantiPassword = false;
              this.formPassword.lama = '';
              this.formPassword.baru = '';
              this.formPassword.konfirmasi = '';
              this.triggerToast('Kata sandi akun Guru BK berhasil diperbarui!', 'success');
          },

          triggerToast(msg, type = 'success') {
              this.toastMessage = msg;
              this.toastType = type;
              this.showToast = true;
              this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
              setTimeout(() => { this.showToast = false; }, 3500);
          }
      }">

    <!-- ========================================================================= -->
    <!-- 1. TOP HEADER HERO (Royal Blue Gradient with Curved Bottom)               -->
    <!-- ========================================================================= -->
    <header class="relative bg-gradient-to-b from-[#1d4ed8] via-[#2563eb] to-[#1e40af] text-white rounded-b-[40px] sm:rounded-b-[52px] pt-5 sm:pt-7 pb-12 sm:pb-16 px-5 sm:px-8 shadow-xl shadow-blue-600/15">
        
        <!-- Ambient Background Glows -->
        <div class="absolute inset-0 rounded-b-[40px] sm:rounded-b-[52px] overflow-hidden pointer-events-none">
            <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full bg-white/10 blur-2xl"></div>
            <div class="absolute bottom-0 left-1/4 w-80 h-80 rounded-full bg-blue-400/20 blur-3xl"></div>
        </div>

        <div class="max-w-4xl mx-auto relative z-10 space-y-5">
            
            <!-- Top Navigation Row: Back Button, School Brand Logo, Home Shortcut -->
            <div class="flex items-center justify-between pb-2 border-b border-white/10">
                <!-- Back Button to Dashboard BK -->
                <a href="{{ route('guru.bk') }}" 
                   title="Kembali ke Dashboard BK"
                   class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-md flex items-center justify-center text-white transition-all active:scale-95 shadow-sm cursor-pointer shrink-0">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                </a>

                <!-- Center: School Brand Logo & Title -->
                <div class="flex items-center space-x-2 bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-full border border-white/20 shadow-sm">
                    <div class="w-6 h-6 rounded-full bg-white p-0.5 flex items-center justify-center shrink-0 shadow-2xs">
                        <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK" class="w-full h-full object-contain">
                    </div>
                    <span class="text-[11px] sm:text-xs font-black tracking-wider uppercase text-white drop-shadow-xs">
                        SMK TI BALI GLOBAL BADUNG
                    </span>
                </div>

                <!-- Right: Beranda Button -->
                <a href="{{ route('guru.bk') }}" 
                   title="Beranda BK"
                   class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-md flex items-center justify-center text-white transition-all active:scale-95 shadow-sm cursor-pointer shrink-0">
                    <i data-lucide="home" class="w-5 h-5"></i>
                </a>
            </div>

            <!-- Profile Identity Card inside Hero (Center Layout) -->
            <div class="text-center pt-2 space-y-3">
                <!-- Circular Avatar with Active Indicator -->
                <div class="relative inline-block">
                    <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full bg-white text-blue-600 flex items-center justify-center shadow-xl ring-4 ring-white/30 mx-auto">
                        <i data-lucide="user" class="w-12 h-12 sm:w-14 sm:h-14 stroke-[2.2]"></i>
                    </div>
                    <!-- Online / Status Dot -->
                    <span class="absolute bottom-1 right-1 w-6 h-6 rounded-full bg-emerald-500 border-3 border-white shadow-md flex items-center justify-center text-white" title="Status: Aktif Bertugas">
                        <i data-lucide="check" class="w-3.5 h-3.5 stroke-[3]"></i>
                    </span>
                </div>

                <!-- Teacher Name & NIP -->
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight" x-text="guru.nama">
                        Dra. Ni Luh Suastini, S.Pd
                    </h1>
                    <p class="text-xs sm:text-sm text-blue-100 font-semibold mt-0.5" x-text="'NIP. ' + guru.nip">
                        NIP. 19780512 200501 2 008
                    </p>
                </div>

                <!-- Position & Status Pills -->
                <div class="flex flex-wrap items-center justify-center gap-2 pt-1">
                    <span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-bold bg-white/20 text-white backdrop-blur-md border border-white/20 shadow-xs" x-text="guru.jabatan">
                        Koordinator Guru BK & Konselor Sekolah
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-400/90 text-slate-950 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-slate-950 mr-1.5 animate-pulse"></span>
                        PNS / Pembina (Aktif)
                    </span>
                </div>
            </div>

        </div>
    </header>


    <!-- ========================================================================= -->
    <!-- 2. QUICK METRIC STAT CARDS (4 Summary Badges)                             -->
    <!-- ========================================================================= -->
    <div class="max-w-4xl mx-auto w-full px-4 sm:px-6 -mt-8 relative z-20">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            
            <!-- Stat 1: Sesi Konseling -->
            <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-soft space-y-1 text-left hover:border-blue-300 transition-all">
                <div class="w-9 h-9 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold mb-2">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
                <p class="text-[10px] sm:text-[11px] font-extrabold uppercase text-slate-400 tracking-wider">Sesi Konseling</p>
                <h3 class="text-xl sm:text-2xl font-black text-slate-900 leading-none">48 Sesi</h3>
                <p class="text-[10px] sm:text-[11px] text-blue-600 font-semibold mt-0.5">Bulan Ini: 12 Sesi</p>
            </div>

            <!-- Stat 2: Kasus Ditangani -->
            <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-soft space-y-1 text-left hover:border-blue-300 transition-all">
                <div class="w-9 h-9 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold mb-2">
                    <i data-lucide="clipboard-check" class="w-5 h-5"></i>
                </div>
                <p class="text-[10px] sm:text-[11px] font-extrabold uppercase text-slate-400 tracking-wider">Kasus Tertib</p>
                <h3 class="text-xl sm:text-2xl font-black text-slate-900 leading-none">36 Kasus</h3>
                <p class="text-[10px] sm:text-[11px] text-emerald-600 font-semibold mt-0.5">Efektivitas: 92%</p>
            </div>

            <!-- Stat 3: Kelas Binaan -->
            <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-soft space-y-1 text-left hover:border-blue-300 transition-all">
                <div class="w-9 h-9 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold mb-2">
                    <i data-lucide="book-open" class="w-5 h-5"></i>
                </div>
                <p class="text-[10px] sm:text-[11px] font-extrabold uppercase text-slate-400 tracking-wider">Kelas Binaan</p>
                <h3 class="text-xl sm:text-2xl font-black text-slate-900 leading-none">16 Rombel</h3>
                <p class="text-[10px] sm:text-[11px] text-indigo-600 font-semibold mt-0.5">PPLG, TJKT, DKV, BD</p>
            </div>

            <!-- Stat 4: Tingkat Kehadiran Binaan -->
            <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-soft space-y-1 text-left hover:border-blue-300 transition-all">
                <div class="w-9 h-9 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold mb-2">
                    <i data-lucide="user-check" class="w-5 h-5"></i>
                </div>
                <p class="text-[10px] sm:text-[11px] font-extrabold uppercase text-slate-400 tracking-wider">Kehadiran Binaan</p>
                <h3 class="text-xl sm:text-2xl font-black text-slate-900 leading-none">95.8%</h3>
                <p class="text-[10px] sm:text-[11px] text-amber-600 font-semibold mt-0.5">Tertinggi: XI PPLG 1</p>
            </div>

        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- 3. MAIN CONTENT (2-Column Desktop Grid, 1-Column Mobile)                  -->
    <!-- ========================================================================= -->
    <main class="max-w-4xl mx-auto w-full px-4 sm:px-6 py-6 pb-24 space-y-6">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- ================================================================= -->
            <!-- LEFT COLUMN (lg:col-span-5): DATA PEGAWAI & KONTAK DINAS          -->
            <!-- ================================================================= -->
            <div class="lg:col-span-5 space-y-5">
                
                <!-- Card 1: Data Kepegawaian Resmi -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-soft space-y-4 text-left">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                                <i data-lucide="badge-check" class="w-4 h-4"></i>
                            </div>
                            <h3 class="font-extrabold text-sm text-slate-900">Data Kepegawaian</h3>
                        </div>
                        <span class="text-[11px] font-bold text-blue-600">Resmi</span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Nomor Induk Pegawai (NIP)</span>
                            <span class="font-extrabold text-slate-800 text-xs sm:text-[13px] font-mono" x-text="guru.nip">19780512 200501 2 008</span>
                        </div>

                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Pangkat / Golongan</span>
                            <span class="font-bold text-slate-800" x-text="guru.pangkat">Pembina, IV/a</span>
                        </div>

                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Jabatan Fungsional</span>
                            <span class="font-bold text-slate-800" x-text="guru.jabatan">Koordinator Guru BK & Konselor Sekolah</span>
                        </div>

                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Pendidikan Terakhir</span>
                            <span class="font-bold text-slate-800" x-text="guru.pendidikan">S1 Bimbingan dan Konseling - Undiksha</span>
                        </div>

                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Sertifikasi Pendidik</span>
                            <span class="inline-flex items-center space-x-1 font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full text-[11px] mt-0.5">
                                <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                                <span>Pendidik Bersertifikasi (Kemendikbud)</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Kontak & Ruang Konseling -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-soft space-y-4 text-left">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                                <i data-lucide="phone-call" class="w-4 h-4"></i>
                            </div>
                            <h3 class="font-extrabold text-sm text-slate-900">Kontak & Ruangan</h3>
                        </div>
                        <button type="button" @click="bukaModalEdit()" class="text-xs font-bold text-blue-600 hover:underline cursor-pointer">
                            Edit
                        </button>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="flex items-start space-x-3">
                            <i data-lucide="mail" class="w-4 h-4 text-slate-400 shrink-0 mt-0.5"></i>
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-bold">Email Dinas</span>
                                <span class="font-bold text-slate-800 select-all" x-text="guru.email">guru.bk@smktibaliglobal.sch.id</span>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <i data-lucide="phone" class="w-4 h-4 text-slate-400 shrink-0 mt-0.5"></i>
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-bold">WhatsApp / Telepon</span>
                                <span class="font-bold text-slate-800" x-text="guru.telepon">+62 813-3890-4421</span>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <i data-lucide="map-pin" class="w-4 h-4 text-slate-400 shrink-0 mt-0.5"></i>
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-bold">Lokasi Ruang Kerja</span>
                                <span class="font-bold text-slate-800" x-text="guru.ruang">Ruang Bimbingan & Konseling (Gedung A, Lt. 2)</span>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <i data-lucide="clock" class="w-4 h-4 text-slate-400 shrink-0 mt-0.5"></i>
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-bold">Jam Layanan Konsultasi</span>
                                <span class="font-bold text-slate-800" x-text="guru.jamLayanan">Senin - Jumat, 07:00 - 15:30 WITA</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Pintasan Cepat Menu Guru BK -->
                <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-soft space-y-2.5 text-left">
                    <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 mb-2">Pintasan Layanan BK</p>
                    
                    <a href="{{ route('guru.absensi') }}" class="w-full p-3 rounded-2xl bg-slate-50 hover:bg-blue-50/60 flex items-center justify-between text-xs font-bold text-slate-700 transition-colors group">
                        <span class="flex items-center space-x-2.5">
                            <i data-lucide="user-check" class="w-4 h-4 text-blue-600"></i>
                            <span>Rekap Absensi Siswa (16 Kelas)</span>
                        </span>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:translate-x-0.5 transition-transform"></i>
                    </a>

                    <a href="{{ route('guru.pelanggaran') }}" class="w-full p-3 rounded-2xl bg-slate-50 hover:bg-rose-50/60 flex items-center justify-between text-xs font-bold text-slate-700 transition-colors group">
                        <span class="flex items-center space-x-2.5">
                            <i data-lucide="clipboard-list" class="w-4 h-4 text-rose-600"></i>
                            <span>Antrean Kasus Pelanggaran</span>
                        </span>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:translate-x-0.5 transition-transform"></i>
                    </a>

                    <a href="{{ route('guru.konseling') }}" class="w-full p-3 rounded-2xl bg-slate-50 hover:bg-emerald-50/60 flex items-center justify-between text-xs font-bold text-slate-700 transition-colors group">
                        <span class="flex items-center space-x-2.5">
                            <i data-lucide="messages-square" class="w-4 h-4 text-emerald-600"></i>
                            <span>Antrean Pengajuan Konseling</span>
                        </span>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:translate-x-0.5 transition-transform"></i>
                    </a>
                </div>

            </div>


            <!-- ================================================================= -->
            <!-- RIGHT COLUMN (lg:col-span-7): JADWAL MINGGUAN & PENGATURAN AKUN   -->
            <!-- ================================================================= -->
            <div class="lg:col-span-7 space-y-5">
                
                <!-- Card 1: Agenda Layanan Bimbingan Mingguan -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-soft space-y-4 text-left">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                                <i data-lucide="calendar" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-sm text-slate-900">Agenda Kerja & Piket BK</h3>
                                <p class="text-[11px] text-slate-400">Jadwal Pelayanan Konseling Terstruktur</p>
                            </div>
                        </div>
                        <span class="bg-emerald-100 text-emerald-800 text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase">Minggu Ini</span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <!-- Senin -->
                        <div class="p-3.5 rounded-2xl bg-slate-50/90 border border-slate-100 flex items-start justify-between gap-3">
                            <div>
                                <span class="bg-blue-100 text-blue-800 font-bold px-2 py-0.5 rounded-md text-[10px]">Senin</span>
                                <h4 class="font-bold text-slate-900 mt-1">Bimbingan Karir & Magang Industri PKL</h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">Pendampingan pemilihan tempat praktek industri bagi siswa Kelas XII</p>
                            </div>
                            <span class="text-slate-400 font-mono text-[11px] shrink-0 font-semibold">08:00 - 11:30</span>
                        </div>

                        <!-- Selasa -->
                        <div class="p-3.5 rounded-2xl bg-slate-50/90 border border-slate-100 flex items-start justify-between gap-3">
                            <div>
                                <span class="bg-amber-100 text-amber-800 font-bold px-2 py-0.5 rounded-md text-[10px]">Selasa</span>
                                <h4 class="font-bold text-slate-900 mt-1">Monitoring Kedisiplinan & Piket Gerbang</h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">Pemeriksaan keterlambatan dan kepatuhan atribut seragam sekolah</p>
                            </div>
                            <span class="text-slate-400 font-mono text-[11px] shrink-0 font-semibold">06:45 - 09:30</span>
                        </div>

                        <!-- Rabu -->
                        <div class="p-3.5 rounded-2xl bg-slate-50/90 border border-slate-100 flex items-start justify-between gap-3">
                            <div>
                                <span class="bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded-md text-[10px]">Rabu</span>
                                <h4 class="font-bold text-slate-900 mt-1">Sesi Konseling Privat Ruang BK 1</h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">Bimbingan pribadi, penanganan kecemasan, dan mediasi siswa</p>
                            </div>
                            <span class="text-slate-400 font-mono text-[11px] shrink-0 font-semibold">09:00 - 14:00</span>
                        </div>

                        <!-- Kamis -->
                        <div class="p-3.5 rounded-2xl bg-slate-50/90 border border-slate-100 flex items-start justify-between gap-3">
                            <div>
                                <span class="bg-indigo-100 text-indigo-800 font-bold px-2 py-0.5 rounded-md text-[10px]">Kamis</span>
                                <h4 class="font-bold text-slate-900 mt-1">Bimbingan Belajar & Akademik Siswa</h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">Diagnostik kesulitan belajar dan evaluasi remidial mapel produktif</p>
                            </div>
                            <span class="text-slate-400 font-mono text-[11px] shrink-0 font-semibold">10:00 - 13:30</span>
                        </div>

                        <!-- Jumat -->
                        <div class="p-3.5 rounded-2xl bg-slate-50/90 border border-slate-100 flex items-start justify-between gap-3">
                            <div>
                                <span class="bg-rose-100 text-rose-800 font-bold px-2 py-0.5 rounded-md text-[10px]">Jumat</span>
                                <h4 class="font-bold text-slate-900 mt-1">Evaluasi Kesiswaan & Koordinasi Wali Kelas</h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">Sinkronisasi poin kedisiplinan dan pembahasan kasus khusus</p>
                            </div>
                            <span class="text-slate-400 font-mono text-[11px] shrink-0 font-semibold">13:00 - 15:30</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Pengaturan Akun & Keamanan -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-soft space-y-4 text-left">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold">
                                <i data-lucide="settings" class="w-4 h-4"></i>
                            </div>
                            <h3 class="font-extrabold text-sm text-slate-900">Pengaturan Akun & Keamanan</h3>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <!-- Action 1: Edit Data Pribadi -->
                        <div class="p-3.5 rounded-2xl border border-slate-100 flex items-center justify-between hover:bg-slate-50 transition-colors">
                            <div class="flex items-center space-x-3 text-xs">
                                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                                    <i data-lucide="user-cog" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900">Perbarui Informasi Profil</p>
                                    <p class="text-[11px] text-slate-400">Ubah nama, nomor WhatsApp, ruang kerja</p>
                                </div>
                            </div>
                            <button type="button" @click="bukaModalEdit()" class="px-3.5 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-extrabold text-xs cursor-pointer transition-colors">
                                Ubah
                            </button>
                        </div>

                        <!-- Action 2: Ganti Password -->
                        <div class="p-3.5 rounded-2xl border border-slate-100 flex items-center justify-between hover:bg-slate-50 transition-colors">
                            <div class="flex items-center space-x-3 text-xs">
                                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                                    <i data-lucide="key-round" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900">Ganti Kata Sandi Akun</p>
                                    <p class="text-[11px] text-slate-400">Tingkatkan keamanan akses dashboard BK</p>
                                </div>
                            </div>
                            <button type="button" @click="modalGantiPassword = true" class="px-3.5 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 font-extrabold text-xs cursor-pointer transition-colors">
                                Ganti
                            </button>
                        </div>

                        <!-- Action 3: Logout -->
                        <div class="p-3.5 rounded-2xl border border-rose-100 bg-rose-50/40 flex items-center justify-between">
                            <div class="flex items-center space-x-3 text-xs">
                                <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-rose-900">Keluar Sesi Akun Guru</p>
                                    <p class="text-[11px] text-rose-600/80">Akhiri sesi login dari perangkat ini</p>
                                </div>
                            </div>
                            <button type="button" @click="modalKonfirmasiLogout = true" class="px-3.5 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs cursor-pointer transition-colors shadow-2xs">
                                Logout
                            </button>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </main>


    <!-- ========================================================================= -->
    <!-- MODAL 1: EDIT PROFIL GURU BK                                              -->
    <!-- ========================================================================= -->
    <div x-show="modalEditProfil" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
        
        <div @click.away="modalEditProfil = false" 
             x-show="modalEditProfil" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-100 space-y-4">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                        <i data-lucide="user-cog" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-slate-900">Edit Profil Guru BK</h3>
                        <p class="text-xs text-slate-500">Perbarui Kontak & Informasi Layanan</p>
                    </div>
                </div>
                <button @click="modalEditProfil = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form @submit.prevent="simpanEditProfil()" class="space-y-3.5 text-xs text-left">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Lengkap & Gelar</label>
                    <input type="text" x-model="formEdit.nama" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor WhatsApp / Kontak</label>
                    <input type="text" x-model="formEdit.telepon" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Ruang Kerja Konseling</label>
                    <input type="text" x-model="formEdit.ruang" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jam Pelayanan Konsultasi</label>
                    <input type="text" x-model="formEdit.jamLayanan" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <button type="button" @click="modalEditProfil = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-bold text-xs cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="flex-2 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black text-xs shadow-md shadow-blue-500/20 cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- MODAL 2: GANTI PASSWORD AKUN GURU BK                                      -->
    <!-- ========================================================================= -->
    <div x-show="modalGantiPassword" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
        
        <div @click.away="modalGantiPassword = false" 
             x-show="modalGantiPassword" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-100 space-y-4">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                        <i data-lucide="lock" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-slate-900">Ganti Kata Sandi</h3>
                        <p class="text-xs text-slate-500">Keamanan Akses Akun Guru BK</p>
                    </div>
                </div>
                <button @click="modalGantiPassword = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form @submit.prevent="simpanPassword()" class="space-y-3.5 text-xs text-left">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kata Sandi Saat Ini</label>
                    <input type="password" x-model="formPassword.lama" placeholder="Masukkan kata sandi lama" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500 outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kata Sandi Baru (Min. 6 Karakter)</label>
                    <input type="password" x-model="formPassword.baru" placeholder="Masukkan kata sandi baru" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500 outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" x-model="formPassword.konfirmasi" placeholder="Ulangi kata sandi baru" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500 outline-none">
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <button type="button" @click="modalGantiPassword = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-bold text-xs cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="flex-2 py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-2xl font-black text-xs shadow-md shadow-amber-500/20 cursor-pointer">
                        Simpan Password Baru
                    </button>
                </div>
            </form>

        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- MODAL 3: KONFIRMASI LOGOUT                                                -->
    <!-- ========================================================================= -->
    <div x-show="modalKonfirmasiLogout" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
        
        <div @click.away="modalKonfirmasiLogout = false" 
             x-show="modalKonfirmasiLogout" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-3xl max-w-sm w-full p-6 text-center shadow-2xl border border-slate-100 space-y-4">
            
            <div class="w-14 h-14 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto shadow-inner">
                <i data-lucide="log-out" class="w-7 h-7"></i>
            </div>

            <div>
                <h3 class="text-lg font-black text-slate-900">Keluar dari Akun?</h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Sesi Anda sebagai Guru BK akan diakhiri. Anda perlu memasukkan email & password untuk masuk kembali.
                </p>
            </div>

            <div class="flex items-center space-x-2 pt-2">
                <button type="button" @click="modalKonfirmasiLogout = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-bold text-xs cursor-pointer">
                    Batal
                </button>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full py-3 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl font-black text-xs shadow-md shadow-rose-500/20 transition-all">
                        Ya, Keluar
                    </button>
                </form>
            </div>

        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- MOBILE BOTTOM NAVIGATION (Khusus Tampilan Smartphone)                      -->
    <!-- ========================================================================= -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-slate-200/80 py-2.5 px-8 flex items-center justify-around z-30 shadow-lg">
        <!-- Tab 1: Beranda -->
        <a href="{{ route('guru.bk') }}" 
           class="flex flex-col items-center justify-center space-y-1 text-slate-400 hover:text-slate-600 transition-all">
            <i data-lucide="home" class="w-6 h-6 stroke-[2.2]"></i>
            <span class="text-[11px]">Beranda</span>
        </a>

        <!-- Tab 2: Profile (Active Blue) -->
        <a href="{{ route('guru.profile') }}" 
           class="flex flex-col items-center justify-center space-y-1 text-blue-600 font-bold transition-all">
            <i data-lucide="user" class="w-6 h-6 stroke-[2.2]"></i>
            <span class="text-[11px]">Profile</span>
        </a>
    </nav>


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
