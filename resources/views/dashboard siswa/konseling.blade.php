@extends('layouts.app')

@section('title', 'Bimbingan Konseling & Kedisiplinan')

@section('content')
<div class="w-full space-y-5" x-data="{
    // Modals
    openModalPoin: false,
    openModalPrestasi: false,
    openModalAktivitas: false,
    openModalJadwal: false,

    // Active Tab in Chart or Tooltip
    selectedBar: '25'
}">

    <!-- ========================================================================= -->
    <!-- 1. TOP HEADER BAR (Exact 1:1 Matching Mockup)                             -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-soft flex items-center justify-between">
        <!-- Back Button to Dashboard -->
        <a href="{{ route('dashboard') }}" 
           class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-slate-50 hover:bg-slate-100 border border-slate-200/80 flex items-center justify-center text-slate-700 transition-all active:scale-95 shadow-2xs shrink-0"
           title="Kembali ke Dashboard">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>

        <!-- Middle: Avatar Graduation Cap + Student Info -->
        <div class="flex items-center space-x-3 sm:space-x-3.5">
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-500/25 shrink-0">
                <i data-lucide="graduation-cap" class="w-5 h-5 sm:w-6 sm:h-6"></i>
            </div>
            <div>
                <h1 class="text-sm sm:text-base font-black text-slate-900 leading-tight">
                    Halo, {{ $siswa->nama ?? 'Wahyu Pratama' }}
                </h1>
                <p class="text-[11px] sm:text-xs text-slate-500 font-semibold mt-0.5">
                    Siswa • {{ $siswa->kelas ?? 'XI PPLG 1' }} • SMK TI Bali Global
                </p>
            </div>
        </div>

        <!-- Right: Notification Bell Button -->
        <div class="relative shrink-0">
            <button type="button" 
                    @click="openModalAktivitas = true"
                    class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-slate-50 hover:bg-slate-100 border border-slate-200/80 flex items-center justify-center text-slate-700 transition-all active:scale-95 shadow-2xs cursor-pointer"
                    title="Pemberitahuan Aktivitas BK">
                <i data-lucide="bell" class="w-5 h-5"></i>
                <span class="absolute top-2 right-2 w-2.5 h-2.5 rounded-full bg-rose-500 ring-2 ring-white"></span>
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. DATE BANNER 'HARI INI' (Exact Blue Rounded Pill Card)                  -->
    <!-- ========================================================================= -->
    <div class="bg-gradient-to-r from-blue-600 via-blue-600 to-indigo-600 rounded-3xl p-4 sm:p-5 text-white shadow-lg shadow-blue-500/15 flex items-center justify-between">
        <div class="flex items-center space-x-3 sm:space-x-3.5">
            <div class="w-11 h-11 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white shrink-0 shadow-inner">
                <i data-lucide="calendar" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-[10px] sm:text-[11px] font-black uppercase tracking-wider text-blue-200 block">
                    HARI INI
                </span>
                <h3 class="text-xs sm:text-sm md:text-base font-black text-white mt-0.5">
                    {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                </h3>
            </div>
        </div>
        <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-white/80 shrink-0">
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. 2x2 GRID STATS CARDS (Mobile: 2 Cols, Desktop: 4 Cols)                 -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
        
        <!-- CARD 1: Poin Penalti BK -->
        <div @click="openModalPoin = true" 
             class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-soft hover:shadow-md transition-all cursor-pointer flex flex-col justify-between space-y-2.5 group">
            <div class="flex items-start justify-between">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <i data-lucide="triangle-alert" class="w-5 h-5"></i>
                </div>
                <span class="text-[10px] sm:text-[11px] font-black text-amber-600">Teguran</span>
            </div>
            <div>
                <p class="text-[11px] sm:text-xs text-slate-500 font-semibold">Poin Penalti BK</p>
                <div class="flex items-baseline space-x-1 mt-1">
                    <span class="text-2xl sm:text-3xl font-black text-rose-600">15</span>
                    <span class="text-xs font-bold text-slate-400">/ 30 Poin</span>
                </div>
                <!-- Progress Bar: 15 / 30 = 50% -->
                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden mt-2">
                    <div class="bg-rose-500 h-full rounded-full w-1/2"></div>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-400 font-medium mt-1.5">Batas SP-1: 30 Poin</p>
            </div>
        </div>

        <!-- CARD 2: Poin Prestasi -->
        <div @click="openModalPrestasi = true" 
             class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-soft hover:shadow-md transition-all cursor-pointer flex flex-col justify-between space-y-2.5 group">
            <div class="flex items-start justify-between">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <i data-lucide="trophy" class="w-5 h-5"></i>
                </div>
                <span class="text-[10px] font-black bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full">+Reward</span>
            </div>
            <div>
                <p class="text-[11px] sm:text-xs text-slate-500 font-semibold">Poin Prestasi</p>
                <div class="flex items-baseline space-x-1 mt-1">
                    <span class="text-2xl sm:text-3xl font-black text-emerald-600">+40</span>
                    <span class="text-xs font-bold text-slate-400">Poin</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-emerald-600 font-bold mt-2.5">2 Penghargaan aktif</p>
            </div>
        </div>

        <!-- CARD 3: Total Alpa Siswa -->
        <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-soft flex flex-col justify-between space-y-2.5">
            <div class="flex items-start justify-between">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center shrink-0">
                    <i data-lucide="x" class="w-5 h-5 stroke-[3]"></i>
                </div>
                <span class="text-[10px] sm:text-[11px] font-medium text-slate-400">Bulan Ini</span>
            </div>
            <div>
                <p class="text-[11px] sm:text-xs text-slate-500 font-semibold">Total Alpa Siswa</p>
                <div class="flex items-baseline space-x-1 mt-1">
                    <span class="text-2xl sm:text-3xl font-black text-slate-900">1</span>
                    <span class="text-xs font-bold text-slate-500">Hari</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-amber-600 font-bold flex items-center space-x-1 mt-2.5">
                    <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                    <span>Maks: 3 hari</span>
                </p>
            </div>
        </div>

        <!-- CARD 4: Jadwal Konseling -->
        <div @click="openModalJadwal = true" 
             class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-soft hover:shadow-md transition-all cursor-pointer flex flex-col justify-between space-y-2.5 group">
            <div class="flex items-start justify-between">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
                <span class="text-[10px] font-bold bg-blue-50 text-blue-600 px-2.5 py-0.5 rounded-full">Jadwal</span>
            </div>
            <div>
                <p class="text-[11px] sm:text-xs text-slate-500 font-semibold">Jadwal Konseling</p>
                <div class="flex items-baseline space-x-1 mt-1">
                    <span class="text-2xl sm:text-3xl font-black text-blue-600">1</span>
                    <span class="text-xs font-bold text-slate-500">Sesi Aktif</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-medium mt-2.5">Hari ini, 09:30 WITA</p>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 4. MAIN CONTENT RESPONSIVE GRID (Mobile: 1 Col, Desktop: 12 Cols)         -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

        <!-- ===================================================================== -->
        <!-- LEFT COLUMN (lg:col-span-7 xl:col-span-7)                             -->
        <!-- ===================================================================== -->
        <div class="lg:col-span-7 xl:col-span-7 space-y-5">

            <!-- ================================================================= -->
            <!-- CARD: TREN KEDISIPLINAN 7 HARI TERAKHIR (Exact Bar Chart Mockup)  -->
            <!-- ================================================================= -->
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-soft space-y-5">
                <!-- Header: Title on Left, Month on Right -->
                <div class="flex items-center justify-between pb-1">
                    <h3 class="font-black text-sm sm:text-base text-slate-900">
                        Tren Kedisiplinan 7 Hari Terakhir
                    </h3>
                    <span class="text-xs font-bold text-slate-400">
                        {{ \Carbon\Carbon::now()->format('M Y') }}
                    </span>
                </div>

                <!-- 7 Vertical Bars Chart Grid -->
                <div class="pt-4 pb-2">
                    <div class="grid grid-cols-7 gap-1.5 sm:gap-3 items-end justify-items-center h-44 sm:h-48 border-b border-slate-100 pb-3">
                        
                        <!-- Day 1: 19 Jum (Alpa: Red bottom pill) -->
                        <div class="flex flex-col items-center h-full justify-end w-full max-w-[36px] sm:max-w-[42px] cursor-pointer group"
                             @click="selectedBar = '19'">
                            <div class="w-full h-full bg-slate-100/70 rounded-2xl relative overflow-hidden flex flex-col justify-end p-0">
                                <div class="w-full bg-rose-500 rounded-b-2xl transition-all duration-300" style="height: 28%;"></div>
                            </div>
                            <div class="text-center mt-2">
                                <span class="text-[11px] sm:text-xs font-bold text-slate-500 block leading-tight">19</span>
                                <span class="text-[10px] text-slate-400 font-medium block">Jum</span>
                            </div>
                        </div>

                        <!-- Day 2: 20 Sab (Weekend / Libur: Gray bar) -->
                        <div class="flex flex-col items-center h-full justify-end w-full max-w-[36px] sm:max-w-[42px] cursor-pointer group"
                             @click="selectedBar = '20'">
                            <div class="w-full h-full bg-slate-100/70 rounded-2xl relative overflow-hidden flex flex-col justify-end p-0">
                                <div class="w-full bg-slate-200/90 rounded-b-2xl transition-all duration-300" style="height: 38%;"></div>
                            </div>
                            <div class="text-center mt-2">
                                <span class="text-[11px] sm:text-xs font-bold text-slate-400 block leading-tight">20</span>
                                <span class="text-[10px] text-slate-400 font-medium block">Sab</span>
                            </div>
                        </div>

                        <!-- Day 3: 21 Min (Weekend / Libur: Gray bar) -->
                        <div class="flex flex-col items-center h-full justify-end w-full max-w-[36px] sm:max-w-[42px] cursor-pointer group"
                             @click="selectedBar = '21'">
                            <div class="w-full h-full bg-slate-100/70 rounded-2xl relative overflow-hidden flex flex-col justify-end p-0">
                                <div class="w-full bg-slate-200/90 rounded-b-2xl transition-all duration-300" style="height: 38%;"></div>
                            </div>
                            <div class="text-center mt-2">
                                <span class="text-[11px] sm:text-xs font-bold text-slate-400 block leading-tight">21</span>
                                <span class="text-[10px] text-slate-400 font-medium block">Min</span>
                            </div>
                        </div>

                        <!-- Day 4: 22 Sen (Terlambat: Yellow bar) -->
                        <div class="flex flex-col items-center h-full justify-end w-full max-w-[36px] sm:max-w-[42px] cursor-pointer group"
                             @click="selectedBar = '22'">
                            <div class="w-full h-full bg-slate-100/70 rounded-2xl relative overflow-hidden flex flex-col justify-end p-0">
                                <div class="w-full bg-amber-400 rounded-b-2xl transition-all duration-300" style="height: 36%;"></div>
                            </div>
                            <div class="text-center mt-2">
                                <span class="text-[11px] sm:text-xs font-bold text-slate-600 block leading-tight">22</span>
                                <span class="text-[10px] text-slate-400 font-medium block">Sen</span>
                            </div>
                        </div>

                        <!-- Day 5: 23 Sel (Tepat Waktu: Solid Blue Full Bar) -->
                        <div class="flex flex-col items-center h-full justify-end w-full max-w-[36px] sm:max-w-[42px] cursor-pointer group"
                             @click="selectedBar = '23'">
                            <div class="w-full h-full bg-slate-100/70 rounded-2xl relative overflow-hidden flex flex-col justify-end p-0">
                                <div class="w-full bg-blue-600 rounded-2xl transition-all duration-300 shadow-xs" style="height: 100%;"></div>
                            </div>
                            <div class="text-center mt-2">
                                <span class="text-[11px] sm:text-xs font-bold text-slate-700 block leading-tight">23</span>
                                <span class="text-[10px] text-slate-400 font-medium block">Sel</span>
                            </div>
                        </div>

                        <!-- Day 6: 24 Rab (Tepat Waktu: Solid Blue Full Bar) -->
                        <div class="flex flex-col items-center h-full justify-end w-full max-w-[36px] sm:max-w-[42px] cursor-pointer group"
                             @click="selectedBar = '24'">
                            <div class="w-full h-full bg-slate-100/70 rounded-2xl relative overflow-hidden flex flex-col justify-end p-0">
                                <div class="w-full bg-blue-600 rounded-2xl transition-all duration-300 shadow-xs" style="height: 100%;"></div>
                            </div>
                            <div class="text-center mt-2">
                                <span class="text-[11px] sm:text-xs font-bold text-slate-700 block leading-tight">24</span>
                                <span class="text-[10px] text-slate-400 font-medium block">Rab</span>
                            </div>
                        </div>

                        <!-- Day 7: 25 Hari ini (Tepat Waktu: Solid Green Full Bar) -->
                        <div class="flex flex-col items-center h-full justify-end w-full max-w-[36px] sm:max-w-[42px] cursor-pointer group"
                             @click="selectedBar = '25'">
                            <div class="w-full h-full bg-slate-100/70 rounded-2xl relative overflow-hidden flex flex-col justify-end p-0 ring-2 ring-emerald-400/40">
                                <div class="w-full bg-gradient-to-t from-emerald-600 to-emerald-500 rounded-2xl transition-all duration-300 shadow-md shadow-emerald-500/25" style="height: 100%;"></div>
                            </div>
                            <div class="text-center mt-2">
                                <span class="text-[11px] sm:text-xs font-black text-emerald-600 block leading-tight">25</span>
                                <span class="text-[9px] sm:text-[10px] font-black text-emerald-600 block">Hari ini</span>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Legend Row (Exact from Mockup: Tepat Waktu, Terlambat, Alpa) -->
                <div class="flex items-center justify-center space-x-5 text-xs font-bold pt-1">
                    <div class="flex items-center space-x-1.5 text-slate-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                        <span class="text-[11px] sm:text-xs">Tepat Waktu</span>
                    </div>
                    <div class="flex items-center space-x-1.5 text-slate-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                        <span class="text-[11px] sm:text-xs">Terlambat</span>
                    </div>
                    <div class="flex items-center space-x-1.5 text-slate-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        <span class="text-[11px] sm:text-xs">Alpa</span>
                    </div>
                </div>
            </div>

            <!-- Link to BK Tugas Pembinaan -->
            <div class="bg-blue-50/70 border border-blue-200/90 rounded-3xl p-5 flex items-center justify-between text-xs">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center shrink-0">
                        <i data-lucide="file-text" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm text-slate-900">Tugas Pembinaan & Sanksi</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">Lihat lembar tugas sanksi atau unggah resume di halaman BK</p>
                    </div>
                </div>
                <a href="{{ route('bk') }}" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs rounded-xl transition-all shrink-0 shadow-xs">
                    Buka Tugas BK →
                </a>
            </div>

        </div>

        <!-- ===================================================================== -->
        <!-- RIGHT COLUMN (lg:col-span-5 xl:col-span-5)                            -->
        <!-- ===================================================================== -->
        <div class="lg:col-span-5 xl:col-span-5 space-y-5">

            <!-- ================================================================= -->
            <!-- SECTION: MENU CEPAT (2 Side-by-Side Cards Exact from Mockup)      -->
            <!-- ================================================================= -->
            <div class="space-y-3">
                <h3 class="font-black text-sm text-slate-900 px-1">
                    Menu Cepat
                </h3>

                <div class="grid grid-cols-2 gap-3">
                    <!-- Button 1: Cek Poin BK -->
                    <button type="button" 
                            @click="openModalPoin = true"
                            class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/80 shadow-soft hover:shadow-md hover:border-blue-200 transition-all flex items-center justify-between text-left group cursor-pointer">
                        <div class="flex items-center space-x-2.5 sm:space-x-3">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                                <i data-lucide="shield" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-xs sm:text-sm text-slate-900 group-hover:text-blue-600 transition-colors">
                                    Cek Poin BK
                                </h4>
                                <p class="text-[10px] text-slate-400 mt-0.5">
                                    Rincian tata tertib
                                </p>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 group-hover:text-blue-500 transition-colors shrink-0"></i>
                    </button>

                    <!-- Button 2: Prestasi Siswa -->
                    <button type="button" 
                            @click="openModalPrestasi = true"
                            class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/80 shadow-soft hover:shadow-md hover:border-emerald-200 transition-all flex items-center justify-between text-left group cursor-pointer">
                        <div class="flex items-center space-x-2.5 sm:space-x-3">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                                <i data-lucide="trophy" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-xs sm:text-sm text-slate-900 group-hover:text-emerald-600 transition-colors">
                                    Prestasi Siswa
                                </h4>
                                <p class="text-[10px] text-slate-400 mt-0.5">
                                    Poin penghargaan
                                </p>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 group-hover:text-emerald-500 transition-colors shrink-0"></i>
                    </button>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- SECTION: AKTIVITAS TERBARU (3 Items with Badges & Times)          -->
            <!-- ================================================================= -->
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-soft space-y-4">
                <div class="flex items-center justify-between pb-1">
                    <div class="flex items-center space-x-2">
                        <i data-lucide="clock" class="w-4 h-4 text-blue-600"></i>
                        <h3 class="font-black text-sm text-slate-900">
                            Aktivitas Terbaru
                        </h3>
                    </div>
                    <button type="button" 
                            @click="openModalAktivitas = true" 
                            class="text-xs font-extrabold text-blue-600 hover:underline cursor-pointer">
                        Lihat Semua
                    </button>
                </div>

                <div class="divide-y divide-slate-100 text-xs">
                    
                    <!-- Item 1: Panggilan BK -->
                    <div @click="openModalJadwal = true" class="py-3 flex items-center justify-between gap-3 cursor-pointer hover:bg-slate-50/80 -mx-2 px-2 rounded-xl transition-colors">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                <i data-lucide="calendar" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h5 class="font-black text-xs sm:text-[13px] text-slate-900 leading-snug">
                                    Panggilan BK: Dra. Ni Luh Suastini
                                </h5>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    Jadwal evaluasi kedisiplinan ruang BK
                                </p>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold text-slate-400 shrink-0">
                            08:30
                        </span>
                    </div>

                    <!-- Item 2: Reward Juara 2 LKS Web Tech -->
                    <div @click="openModalPrestasi = true" class="py-3 flex items-center justify-between gap-3 cursor-pointer hover:bg-slate-50/80 -mx-2 px-2 rounded-xl transition-colors">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                <i data-lucide="check" class="w-5 h-5 stroke-[3]"></i>
                            </div>
                            <div>
                                <h5 class="font-black text-xs sm:text-[13px] text-slate-900 leading-snug">
                                    Reward Juara 2 LKS Web Tech
                                </h5>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    Ditambahkan +30 poin penghargaan
                                </p>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold text-slate-400 shrink-0">
                            Kemarin
                        </span>
                    </div>

                    <!-- Item 3: Pelanggaran Terlambat Sekolah -->
                    <div @click="openModalPoin = true" class="py-3 flex items-center justify-between gap-3 cursor-pointer hover:bg-slate-50/80 -mx-2 px-2 rounded-xl transition-colors">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h5 class="font-black text-xs sm:text-[13px] text-slate-900 leading-snug">
                                    Pelanggaran Terlambat Sekolah
                                </h5>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    +5 Poin dicatat oleh Guru Piket
                                </p>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold text-slate-400 shrink-0">
                            22 Sep
                        </span>
                    </div>

                </div>
            </div>

            <!-- ================================================================= -->
            <!-- CARD: KONTAK GURU BK & LAYANAN KONSULTASI                         -->
            <!-- ================================================================= -->
            <div class="bg-gradient-to-br from-slate-900 to-blue-950 text-white rounded-3xl p-5 sm:p-6 shadow-soft space-y-3.5">
                <div class="flex items-center space-x-2 text-blue-300 text-xs font-bold uppercase tracking-wider">
                    <i data-lucide="message-square" class="w-4 h-4"></i>
                    <span>Konsultasi Siswa</span>
                </div>
                <h4 class="font-black text-base text-white">Ruang Bimbingan & Konseling</h4>
                <p class="text-xs text-blue-200/90 leading-relaxed">
                    Gedung A, Lantai 1 SMK TI Bali Global Badung. Jam pelayanan aktif: <strong>07:30 - 15:30 WITA</strong>.
                </p>
                <div class="pt-2 border-t border-white/10 flex items-center justify-between">
                    <div>
                        <p class="text-[11px] text-slate-300">Guru BK Pendamping:</p>
                        <p class="text-xs font-bold text-white">Dra. Ni Luh Suastini, S.Pd</p>
                    </div>
                    <a href="https://wa.me/6281234567890?text=Halo%20Bu%20Luh%2C%20saya%20Wahyu%20Pratama%20dari%20kelas%20XI%20PPLG%201%20ingin%20konsultasi%20BK" 
                       target="_blank" 
                       rel="noopener" 
                       class="px-3.5 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-xs rounded-xl shadow-xs transition-all flex items-center space-x-1.5 active:scale-95">
                        <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                        <span>Chat WA</span>
                    </a>
                </div>
            </div>

        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 1: CEK POIN BK (Detail Rincian Pelanggaran & Tata Tertib)           -->
    <!-- ========================================================================= -->
    <div x-show="openModalPoin" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.away="openModalPoin = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-slate-100">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                        <i data-lucide="shield-alert" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-sm text-slate-900">Rincian Poin Penalti BK</h4>
                        <p class="text-[11px] text-slate-500">Tata tertib kedisiplinan siswa</p>
                    </div>
                </div>
                <button @click="openModalPoin = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="p-4 rounded-2xl bg-rose-50/70 border border-rose-100 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-black uppercase text-rose-700">TOTAL POIN PENALTI</span>
                    <h5 class="text-2xl font-black text-rose-600 mt-0.5">15 <span class="text-xs text-rose-950 font-bold">/ 30 Poin</span></h5>
                </div>
                <span class="bg-rose-500 text-white font-black text-xs px-3 py-1 rounded-full">SP-1 Aktif</span>
            </div>

            <div class="space-y-2 text-xs">
                <p class="font-extrabold text-slate-700">Histori Pelanggaran Tercatat:</p>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="font-bold text-slate-900">Terlambat Masuk Sekolah (3x)</p>
                        <p class="text-[10px] text-slate-500">22 Sep 2026 • Dicatat Guru Piket</p>
                    </div>
                    <span class="font-black text-rose-600">+10 Poin</span>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="font-bold text-slate-900">Atribut Seragam Tidak Lengkap</p>
                        <p class="text-[10px] text-slate-500">18 Sep 2026 • Dasi / Sabuk</p>
                    </div>
                    <span class="font-black text-rose-600">+5 Poin</span>
                </div>
            </div>

            <button @click="openModalPoin = false" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-xl transition-all cursor-pointer">
                Tutup Rincian
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 2: PRESTASI SISWA (Detail Penghargaan & Reward)                      -->
    <!-- ========================================================================= -->
    <div x-show="openModalPrestasi" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.away="openModalPrestasi = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-slate-100">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                        <i data-lucide="trophy" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-sm text-slate-900">Poin Prestasi & Penghargaan</h4>
                        <p class="text-[11px] text-slate-500">Kompensasi reward kedisiplinan</p>
                    </div>
                </div>
                <button @click="openModalPrestasi = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-100 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-black uppercase text-emerald-700">AKUMULASI REWARD</span>
                    <h5 class="text-2xl font-black text-emerald-600 mt-0.5">+40 <span class="text-xs text-emerald-950 font-bold">Poin</span></h5>
                </div>
                <span class="bg-emerald-600 text-white font-black text-xs px-3 py-1 rounded-full">2 Sertifikat</span>
            </div>

            <div class="space-y-2 text-xs">
                <p class="font-extrabold text-slate-700">Daftar Penghargaan:</p>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="font-bold text-slate-900">Juara 2 LKS Tingkat Provinsi (Web Tech)</p>
                        <p class="text-[10px] text-slate-500">24 Sep 2026 • Komite LKS</p>
                    </div>
                    <span class="font-black text-emerald-600">+30 Poin</span>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="font-bold text-slate-900">Koordinator Piket Teladan</p>
                        <p class="text-[10px] text-slate-500">10 Sep 2026 • Tim Kesiswaan</p>
                    </div>
                    <span class="font-black text-emerald-600">+10 Poin</span>
                </div>
            </div>

            <button @click="openModalPrestasi = false" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl transition-all cursor-pointer">
                Tutup Prestasi
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 3: SEMUA AKTIVITAS TERBARU                                          -->
    <!-- ========================================================================= -->
    <div x-show="openModalAktivitas" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.away="openModalAktivitas = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-slate-100">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-sm text-slate-900">Semua Aktivitas BK</h4>
                        <p class="text-[11px] text-slate-500">Riwayat pemberitahuan & pembinaan</p>
                    </div>
                </div>
                <button @click="openModalAktivitas = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="space-y-2.5 max-h-72 overflow-y-auto pr-1 text-xs">
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="font-black text-slate-900">Panggilan Konseling Disiplin</span>
                        <span class="text-[10px] text-slate-400">Hari ini, 08:30</span>
                    </div>
                    <p class="text-slate-600 text-[11px]">Jadwal sesi 1-on-1 dengan Dra. Ni Luh Suastini di ruang BK.</p>
                </div>

                <div class="p-3 rounded-2xl bg-emerald-50/70 border border-emerald-100 space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="font-black text-emerald-950">Reward Juara 2 LKS Web Tech</span>
                        <span class="text-[10px] text-emerald-600 font-bold">+30 Poin</span>
                    </div>
                    <p class="text-emerald-800 text-[11px]">Sertifikat reward diverifikasi oleh pihak kesiswaan sekolah.</p>
                </div>

                <div class="p-3 rounded-2xl bg-rose-50/70 border border-rose-100 space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="font-black text-rose-950">Pelanggaran Terlambat Sekolah</span>
                        <span class="text-[10px] text-rose-600 font-bold">+5 Poin</span>
                    </div>
                    <p class="text-rose-800 text-[11px]">Dicatat oleh Guru Piket saat melewati gerbang pukul 07:18 WITA.</p>
                </div>
            </div>

            <button @click="openModalAktivitas = false" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-xl transition-all cursor-pointer">
                Tutup
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 4: JADWAL KONSELING INDIVIDUAL                                      -->
    <!-- ========================================================================= -->
    <div x-show="openModalJadwal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.away="openModalJadwal = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-slate-100">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-sm text-slate-900">Jadwal Sesi Konseling</h4>
                        <p class="text-[11px] text-slate-500">Evaluasi & pendampingan belajar</p>
                    </div>
                </div>
                <button @click="openModalJadwal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="p-4 rounded-2xl bg-blue-50/80 border border-blue-100 space-y-2 text-xs">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-blue-900">Waktu Pelaksanaan:</span>
                    <span class="font-black text-blue-600">Hari ini, 09:30 WITA</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="font-bold text-blue-900">Lokasi:</span>
                    <span class="font-bold text-slate-800">Ruang BK Lantai 1 (Gedung A)</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="font-bold text-blue-900">Guru Pendamping:</span>
                    <span class="font-bold text-slate-800">Dra. Ni Luh Suastini, S.Pd</span>
                </div>
            </div>

            <p class="text-xs text-slate-500 leading-relaxed">
                Silakan datang tepat waktu membawa buku pedoman tata tertib dan kartu pelajar untuk verifikasi.
            </p>

            <button @click="openModalJadwal = false" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs rounded-xl transition-all cursor-pointer">
                Saya Mengerti
            </button>
        </div>
    </div>

</div>
@endsection
