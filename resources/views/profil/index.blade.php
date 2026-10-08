@extends('layouts.app')

@section('title', 'Profil & Kedisiplinan Murid')

@section('content')
<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-soft">
    
    <!-- Top Profile Header (Exact from Image 5 Top) -->
    <div class="flex items-center justify-between pb-6 border-b border-slate-100">
        <div class="flex items-center space-x-3 sm:space-x-4">
            <a href="{{ route('dashboard') }}" title="Kembali ke Dashboard" class="p-2.5 rounded-2xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-3xl bg-blue-600 text-white flex items-center justify-center font-bold shadow-lg shadow-blue-500/20 shrink-0">
                <i data-lucide="graduation-cap" class="w-7 h-7 sm:w-10 sm:h-10"></i>
            </div>
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900">Halo, Wahyu Pratama</h2>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">Siswa • XI PPLG 1 • SMK TI Bali Global</p>
            </div>
        </div>
        <button class="p-2.5 rounded-2xl border border-slate-200 text-slate-600 hover:bg-slate-50 relative">
            <i data-lucide="bell" class="w-5 h-5"></i>
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full"></span>
        </button>
    </div>

    <!-- Date Banner (Exact from Image 5) -->
    <div class="mt-6 p-4 rounded-2xl bg-blue-600 text-white flex items-center justify-between shadow-md shadow-blue-600/15">
        <div class="flex items-center space-x-3 text-xs font-bold">
            <i data-lucide="calendar" class="w-5 h-5 text-blue-200"></i>
            <div>
                <span class="text-[10px] uppercase tracking-wider text-blue-200 block">HARI INI (REAL-TIME)</span>
                <span class="text-sm font-extrabold" x-text="currentDateLive + ' • ' + currentTimeWita">Jumat, 25 September 2026</span>
            </div>
        </div>
        <i data-lucide="chevron-right" class="w-5 h-5 text-blue-200"></i>
    </div>

    <!-- 2x2 METRIC CARDS GRID (Exact from Image 5) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
        <!-- 1. Poin Penalti BK -->
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 space-y-3">
            <div class="flex items-center justify-between">
                <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                </div>
                <span class="bg-amber-100 text-amber-800 font-extrabold text-[10px] px-2.5 py-1 rounded-full uppercase">Teguran</span>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-semibold">Poin Penalti BK</p>
                <p class="text-2xl font-black text-rose-600">15 <span class="text-xs text-slate-400 font-normal">/ 30 Poin</span></p>
            </div>
            <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                <div class="bg-rose-500 h-full rounded-full" style="width: 50%;"></div>
            </div>
            <p class="text-[10px] text-slate-500 font-semibold">Batas SP-1: 30 Poin</p>
        </div>

        <!-- 2. Poin Prestasi -->
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 space-y-3">
            <div class="flex items-center justify-between">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="trophy" class="w-4 h-4"></i>
                </div>
                <span class="bg-emerald-100 text-emerald-800 font-extrabold text-[10px] px-2.5 py-1 rounded-full uppercase">+Reward</span>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-semibold">Poin Prestasi</p>
                <p class="text-2xl font-black text-emerald-600">+40 <span class="text-xs text-slate-400 font-normal">Poin</span></p>
            </div>
            <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                <div class="bg-emerald-500 h-full rounded-full" style="width: 80%;"></div>
            </div>
            <p class="text-[10px] text-slate-500 font-semibold">2 Penghargaan aktif</p>
        </div>

        <!-- 3. Total Alpa Siswa -->
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 space-y-3">
            <div class="flex items-center justify-between">
                <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </div>
                <span class="bg-slate-200 text-slate-700 font-bold text-[10px] px-2.5 py-1 rounded-full">Bulan Ini</span>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-semibold">Total Alpa Siswa</p>
                <p class="text-2xl font-black text-slate-900">1 <span class="text-xs text-slate-400 font-normal">Hari</span></p>
            </div>
            <p class="text-[10px] text-amber-600 font-bold">⚠️ Maks: 3 hari per semester</p>
        </div>

        <!-- 4. Jadwal Konseling -->
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 space-y-3">
            <div class="flex items-center justify-between">
                <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
                <span class="bg-blue-100 text-blue-800 font-bold text-[10px] px-2.5 py-1 rounded-full">Jadwal</span>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-semibold">Jadwal Konseling</p>
                <p class="text-2xl font-black text-blue-600">1 <span class="text-xs text-slate-400 font-normal">Sesi Aktif</span></p>
            </div>
            <p class="text-[10px] text-blue-600 font-bold">Hari ini, 09:30 WITA</p>
        </div>
    </div>

    <!-- 7-DAY DISCIPLINE TREND CHART (Exact from Image 5 Middle) -->
    <div class="mt-8 p-6 rounded-3xl bg-slate-50/70 border border-slate-200/80">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h4 class="font-extrabold text-sm text-slate-900">Tren Kedisiplinan 7 Hari Terakhir</h4>
                <p class="text-xs text-slate-500">Statistik Kehadiran Periode September 2026</p>
            </div>
            <span class="text-xs bg-white px-2.5 py-1 rounded-xl font-bold border border-slate-200 text-slate-600">Sep 2026</span>
        </div>

        <div class="h-48 flex items-end justify-between gap-2 px-2 pt-6 pb-2 border-b border-slate-200">
            <!-- 19 Jum -->
            <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end">
                <div class="w-full max-w-[32px] bg-slate-200 rounded-t-xl overflow-hidden flex flex-col justify-end" style="height: 90%;">
                    <div class="bg-amber-400 w-full h-4"></div>
                    <div class="bg-rose-500 w-full h-8"></div>
                </div>
                <span class="text-[10px] font-bold text-slate-500 text-center">19<br><span class="text-slate-400">Jum</span></span>
            </div>

            <!-- 20 Sab (Weekend) -->
            <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end">
                <div class="w-full max-w-[32px] bg-slate-200/60 rounded-t-xl" style="height: 40%;"></div>
                <span class="text-[10px] font-bold text-slate-400 text-center">20<br>Sab</span>
            </div>

            <!-- 21 Min (Weekend) -->
            <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end">
                <div class="w-full max-w-[32px] bg-slate-200/60 rounded-t-xl" style="height: 40%;"></div>
                <span class="text-[10px] font-bold text-slate-400 text-center">21<br>Min</span>
            </div>

            <!-- 22 Sen (Terlambat) -->
            <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end">
                <div class="w-full max-w-[32px] bg-slate-200 rounded-t-xl overflow-hidden flex flex-col justify-end" style="height: 90%;">
                    <div class="bg-amber-400 w-full h-12"></div>
                </div>
                <span class="text-[10px] font-bold text-slate-500 text-center">22<br><span class="text-slate-400">Sen</span></span>
            </div>

            <!-- 23 Sel (Tepat Waktu) -->
            <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end">
                <div class="w-full max-w-[32px] bg-blue-600 rounded-t-xl" style="height: 85%;"></div>
                <span class="text-[10px] font-bold text-slate-500 text-center">23<br><span class="text-slate-400">Sel</span></span>
            </div>

            <!-- 24 Rab (Tepat Waktu) -->
            <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end">
                <div class="w-full max-w-[32px] bg-blue-600 rounded-t-xl" style="height: 85%;"></div>
                <span class="text-[10px] font-bold text-slate-500 text-center">24<br><span class="text-slate-400">Rab</span></span>
            </div>

            <!-- 25 Hari ini (Tepat Waktu - Emerald) -->
            <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end">
                <div class="w-full max-w-[32px] bg-emerald-500 rounded-t-xl ring-2 ring-emerald-300 shadow-md shadow-emerald-500/20" style="height: 90%;"></div>
                <span class="text-[10px] font-extrabold text-blue-600 text-center">25<br><span class="font-bold">Hari ini</span></span>
            </div>
        </div>

        <div class="mt-4 flex items-center justify-center space-x-6 text-xs font-semibold text-slate-600">
            <span class="flex items-center space-x-1.5"><span class="w-3 h-3 rounded-full bg-blue-600"></span><span>Tepat Waktu</span></span>
            <span class="flex items-center space-x-1.5"><span class="w-3 h-3 rounded-full bg-amber-400"></span><span>Terlambat</span></span>
            <span class="flex items-center space-x-1.5"><span class="w-3 h-3 rounded-full bg-rose-500"></span><span>Alpa</span></span>
        </div>
    </div>

    <!-- MENU CEPAT (Exact from Image 5 Middle Bottom) -->
    <div class="mt-8">
        <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-4">MENU CEPAT</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="{{ route('bk') }}" class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-blue-300 hover:shadow-md transition-all flex items-center justify-between text-left block">
                <div class="flex items-center space-x-3 text-xs">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                        <i data-lucide="shield" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="font-bold text-slate-900">Cek Poin BK</p>
                        <p class="text-slate-500 text-[11px]">Rincian tata tertib & surat</p>
                    </div>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
            </a>

            <button @click="alert('Daftar Prestasi:\n1. Juara 2 LKS Web Tech (+30 Poin)\n2. Koordinator Terbaik Regu Piket (+10 Poin)')" class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-emerald-300 hover:shadow-md transition-all flex items-center justify-between text-left">
                <div class="flex items-center space-x-3 text-xs">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                        <i data-lucide="trophy" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="font-bold text-slate-900">Prestasi Siswa</p>
                        <p class="text-slate-500 text-[11px]">Poin penghargaan & sertifikat</p>
                    </div>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
            </button>
        </div>
    </div>

    <!-- AKTIVITAS TERBARU (Exact from Image 5 Bottom) -->
    <div class="mt-8 space-y-4">
        <div class="flex items-center justify-between">
            <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 flex items-center space-x-1.5">
                <i data-lucide="clock" class="w-3.5 h-3.5 text-blue-600"></i>
                <span>AKTIVITAS TERBARU</span>
            </h4>
            <span class="text-xs font-bold text-slate-400">Bulan Ini</span>
        </div>

        <div class="space-y-3 text-xs">
            <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                        <i data-lucide="calendar" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="font-bold text-slate-900">Panggilan BK: Dra. Ni Luh Suastini</p>
                        <p class="text-[11px] text-slate-500">Jadwal evaluasi kedisiplinan ruang BK</p>
                    </div>
                </div>
                <span class="text-slate-500 font-semibold text-[11px]">08:30</span>
            </div>

            <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                        <i data-lucide="check" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="font-bold text-slate-900">Reward Juara 2 LKS Web Tech</p>
                        <p class="text-[11px] text-slate-500">Ditambahkan +30 poin penghargaan</p>
                    </div>
                </div>
                <span class="text-slate-500 font-semibold text-[11px]">Kemarin</span>
            </div>

            <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center">
                        <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="font-bold text-slate-900">Pelanggaran Terlambat Sekolah</p>
                        <p class="text-[11px] text-slate-500">+5 Poin dicatat oleh Guru Piket</p>
                    </div>
                </div>
                <span class="text-slate-500 font-semibold text-[11px]">22 Sep</span>
            </div>
        </div>
    </div>

    <!-- TOMBOL LOGOUT RESMI SISWA (Hanya di Halaman Profil) -->
    <div class="mt-8 pt-6 border-t border-slate-200">
        <div class="p-4 sm:p-5 rounded-2xl bg-rose-50/70 border border-rose-200/80 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-3 text-center sm:text-left w-full sm:w-auto">
                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                </div>
                <div>
                    <h5 class="text-xs sm:text-sm font-bold text-slate-900">Keluar dari Akun Siswa</h5>
                    <p class="text-[11px] text-slate-500">Keluar dari sesi login dan kembali ke Halaman Awal portal</p>
                </div>
            </div>
            <a href="{{ route('logout') }}" 
               class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs flex items-center justify-center space-x-2 shadow-sm transition-all active:scale-95">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                <span>Keluar Akun (Logout)</span>
            </a>
        </div>
    </div>
</div>
@endsection
