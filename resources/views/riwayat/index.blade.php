@extends('layouts.app')

@section('title', 'Riwayat Presensi & Kalender')

@section('content')
<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-soft"
     x-data="{
        selectedDateText: '24 September 2026',
        selectedDateStatus: 'Hadir',
        selectDate(day, status) {
            this.selectedDateText = `${day} September 2026`;
            this.selectedDateStatus = status;
        }
     }">
    
    <!-- Header Greeting (Exact from Image 2 Right) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
        <div class="flex items-center space-x-3">
            <a href="{{ route('dashboard') }}" class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <p class="text-xs text-slate-500 font-semibold">Selamat Pagi,</p>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900">{{ $siswa->nama_siswa }}</h2>
            </div>
        </div>
        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-extrabold bg-blue-100 text-blue-700 self-start sm:self-auto">
            ● XI PPLG 1
        </span>
    </div>

    <!-- Summary Pill Cards (Image 2 Right) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center">
                    <i data-lucide="user-check" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500">Tingkat Disiplin Baik</p>
                    <p class="text-lg font-black text-slate-900">Hadir: 95%</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center">
                    <i data-lucide="mail" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500">Bulan September</p>
                    <p class="text-lg font-black text-slate-900">Izin: 3 Hari</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CALENDAR GRID: September 2026 (Exact from Image 2 Right) -->
    <div class="mt-8 bg-slate-50/70 p-6 rounded-3xl border border-slate-200/80">
        <!-- Calendar Header -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-2 font-bold text-sm text-slate-900">
                <i data-lucide="calendar" class="w-4 h-4 text-blue-600"></i>
                <span>September 2026</span>
            </div>
            <div class="flex items-center space-x-2 bg-white px-3 py-1 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600">
                <button class="hover:text-blue-600">‹</button>
                <span>Bulan Ini</span>
                <button class="hover:text-blue-600">›</button>
            </div>
        </div>

        <!-- Legend -->
        <div class="flex flex-wrap items-center gap-4 text-[11px] font-semibold text-slate-600 mb-6">
            <span class="flex items-center space-x-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span><span>Hadir (Hijau)</span></span>
            <span class="flex items-center space-x-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span><span>Izin (Kuning)</span></span>
            <span class="flex items-center space-x-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span><span>Alpha (Merah)</span></span>
        </div>

        <!-- Days of Week -->
        <div class="grid grid-cols-7 gap-1 text-center font-bold text-xs text-slate-400 mb-2">
            <span>Min</span><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span>
        </div>

        <!-- Days Grid -->
        <div class="grid grid-cols-7 gap-2 text-center text-xs">
            <span class="p-2.5 text-slate-300">30</span>
            <span class="p-2.5 text-slate-300">31</span>
            
            <!-- 1 - 4: Hadir (Green) -->
            <button @click="selectDate(1, 'Hadir')" class="p-2.5 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-800 font-bold hover:scale-105 transition-transform">1 •</button>
            <button @click="selectDate(2, 'Hadir')" class="p-2.5 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-800 font-bold hover:scale-105 transition-transform">2 •</button>
            <button @click="selectDate(3, 'Hadir')" class="p-2.5 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-800 font-bold hover:scale-105 transition-transform">3 •</button>
            <button @click="selectDate(4, 'Hadir')" class="p-2.5 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-800 font-bold hover:scale-105 transition-transform">4 •</button>
            <span class="p-2.5 text-slate-400">5</span>

            <span class="p-2.5 text-rose-300">6</span>
            <button @click="selectDate(7, 'Hadir')" class="p-2.5 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-800 font-bold hover:scale-105 transition-transform">7 •</button>
            <button @click="selectDate(8, 'Hadir')" class="p-2.5 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-800 font-bold hover:scale-105 transition-transform">8 •</button>
            <!-- 9 - 10: Izin (Yellow) -->
            <button @click="selectDate(9, 'Izin')" class="p-2.5 rounded-xl border border-amber-300 bg-amber-50 text-amber-800 font-bold hover:scale-105 transition-transform">9 •</button>
            <button @click="selectDate(10, 'Izin')" class="p-2.5 rounded-xl border border-amber-300 bg-amber-50 text-amber-800 font-bold hover:scale-105 transition-transform">10 •</button>
            <button @click="selectDate(11, 'Hadir')" class="p-2.5 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-800 font-bold hover:scale-105 transition-transform">11 •</button>
            <span class="p-2.5 text-slate-400">12</span>

            <span class="p-2.5 text-rose-300">13</span>
            <!-- 14: Alpha (Red) -->
            <button @click="selectDate(14, 'Alpha')" class="p-2.5 rounded-xl border border-rose-300 bg-rose-50 text-rose-800 font-bold hover:scale-105 transition-transform">14 •</button>
            <button @click="selectDate(15, 'Hadir')" class="p-2.5 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-800 font-bold hover:scale-105 transition-transform">15 •</button>
            <button @click="selectDate(16, 'Hadir')" class="p-2.5 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-800 font-bold hover:scale-105 transition-transform">16 •</button>
            <button @click="selectDate(17, 'Hadir')" class="p-2.5 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-800 font-bold hover:scale-105 transition-transform">17 •</button>
            <button @click="selectDate(18, 'Izin')" class="p-2.5 rounded-xl border border-amber-300 bg-amber-50 text-amber-800 font-bold hover:scale-105 transition-transform">18 •</button>
            <span class="p-2.5 text-slate-400">19</span>

            <span class="p-2.5 text-rose-300">20</span>
            <button @click="selectDate(21, 'Hadir')" class="p-2.5 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-800 font-bold hover:scale-105 transition-transform">21 •</button>
            <button @click="selectDate(22, 'Hadir')" class="p-2.5 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-800 font-bold hover:scale-105 transition-transform">22 •</button>
            <button @click="selectDate(23, 'Hadir')" class="p-2.5 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-800 font-bold hover:scale-105 transition-transform">23 •</button>
            <!-- 24: Active Day Highlight -->
            <button @click="selectDate(24, 'Hadir')" class="p-2.5 rounded-xl bg-emerald-600 text-white font-extrabold shadow-md shadow-emerald-600/30 ring-2 ring-emerald-400 hover:scale-105 transition-transform">24 •</button>
            <span class="p-2.5 text-slate-400">25</span>
            <span class="p-2.5 text-slate-400">26</span>

            <span class="p-2.5 text-rose-300">27</span>
            <span class="p-2.5 text-slate-400">28</span>
            <span class="p-2.5 text-slate-400">29</span>
            <span class="p-2.5 text-slate-400">30</span>
            <span class="p-2.5 text-slate-300">1</span>
            <span class="p-2.5 text-slate-300">2</span>
            <span class="p-2.5 text-slate-300">3</span>
        </div>
    </div>

    <!-- Selected Day Inspection Banner (Exact from Image 2 Right) -->
    <div class="mt-6 p-4 rounded-2xl bg-blue-50/70 border border-blue-200/80 flex items-center justify-between">
        <div>
            <p class="font-extrabold text-sm text-slate-900" x-text="selectedDateText">24 September 2026</p>
            <p class="text-xs text-slate-600 mt-0.5" x-text="'Status: ' + selectedDateStatus + ' Hari Ini (07.10 WITA)'">Status: Hadir Hari Ini (07.10 WITA)</p>
        </div>
        <span class="bg-emerald-600 text-white text-xs font-bold px-3 py-1 rounded-full" x-text="selectedDateStatus">Hadir</span>
    </div>

    <!-- REKAPITULASI KEHADIRAN 3 CARDS (Exact from Image 2 Right Bottom) -->
    <div class="mt-8">
        <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-4">REKAPITULASI KEHADIRAN</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Card 1: Total Kehadiran -->
            <div class="p-5 rounded-2xl bg-emerald-50/50 border border-emerald-100 flex flex-col justify-between">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center mb-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-semibold">Total Kehadiran</p>
                    <p class="text-2xl font-black text-slate-900">14 <span class="text-xs font-semibold text-slate-500">Hari</span></p>
                    <p class="text-[11px] font-bold text-emerald-600 mt-1">93.3% Hadir</p>
                </div>
            </div>

            <!-- Card 2: Total Izin -->
            <div class="p-5 rounded-2xl bg-amber-50/50 border border-amber-100 flex flex-col justify-between">
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center mb-2">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-semibold">Total Izin</p>
                    <p class="text-2xl font-black text-slate-900">3 <span class="text-xs font-semibold text-slate-500">Hari</span></p>
                    <p class="text-[11px] font-bold text-amber-600 mt-1">Ada Surat</p>
                </div>
            </div>

            <!-- Card 3: Total Tidak Hadir -->
            <div class="p-5 rounded-2xl bg-rose-50/50 border border-rose-100 flex flex-col justify-between">
                <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center mb-2">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-semibold">Total Tidak Hadir</p>
                    <p class="text-2xl font-black text-slate-900">1 <span class="text-xs font-semibold text-slate-500">Hari</span></p>
                    <p class="text-[11px] font-bold text-rose-600 mt-1">Alpha</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
