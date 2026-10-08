@extends('layouts.app')

@section('title', 'Tugas Mata Pelajaran')

@section('content')
<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-soft"
     x-data="{
        showSubmitModal: false,
        taskTitle: '',
        openSubmit(title) {
            this.taskTitle = title;
            this.showSubmitModal = true;
            this.$nextTick(() => lucide.createIcons());
        }
     }">
    
    <!-- Top Tab Navigation Pills (Exact from Image 3 Header) -->
    <div class="flex items-center space-x-2 bg-slate-100 p-1.5 rounded-2xl mb-6">
        <a href="{{ route('mapel') }}" class="flex-1 py-2.5 px-4 rounded-xl font-bold text-xs bg-white text-blue-600 shadow-xs flex items-center justify-center space-x-2">
            <i data-lucide="book-open" class="w-4 h-4"></i>
            <span>Mapel</span>
        </a>
        <a href="{{ route('bk') }}" class="flex-1 py-2.5 px-4 rounded-xl font-bold text-xs text-slate-600 hover:text-slate-900 flex items-center justify-center space-x-2">
            <i data-lucide="shield-alert" class="w-4 h-4"></i>
            <span>Tugas BK</span>
            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
        </a>
        <a href="{{ route('piket') }}" class="flex-1 py-2.5 px-4 rounded-xl font-bold text-xs text-slate-600 hover:text-slate-900 flex items-center justify-center space-x-2">
            <i data-lucide="sparkles" class="w-4 h-4"></i>
            <span>Piket</span>
        </a>
    </div>

    <!-- Section Title -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-2">
            <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900">TUGAS MATA PELAJARAN</h3>
            <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-0.5 rounded-full">2 Aktif</span>
        </div>
    </div>

    <div class="space-y-4">
        <!-- TASK CARD 1: PEMROGRAMAN WEB & MOBILE (Exact from Image 3 Left) -->
        <div class="p-6 rounded-3xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:shadow-md transition-all space-y-4">
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                        <i data-lucide="code-2" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">PEMROGRAMAN WEB & MOBILE</span>
                        <h4 class="font-extrabold text-base text-slate-900 mt-0.5">Slice UI Figma Dashboard ke HTML/CSS</h4>
                    </div>
                </div>
                <span class="bg-rose-500 text-white font-extrabold text-[10px] px-2.5 py-1 rounded-full uppercase">BESOK</span>
            </div>

            <p class="text-xs text-slate-600 leading-relaxed">
                Implementasikan tata letak presisi mobile frame 414x896px dengan Tailwind CSS, tombol panah kiri header, serta kartu warna kehadiran.
            </p>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2 border-t border-slate-200/60">
                <div class="flex items-center space-x-2 text-xs text-slate-500 font-semibold">
                    <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
                    <span>25 Sep 2026 • 23:59 WITA</span>
                </div>
                <button @click="openSubmit('Slice UI Figma Dashboard ke HTML/CSS')" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2.5 px-6 rounded-xl shadow-xs transition-all">
                    Kumpulkan Tugas
                </button>
            </div>
        </div>

        <!-- TASK CARD 2: BASIS DATA (RDBMS) (Exact from Image 3 Left) -->
        <div class="p-6 rounded-3xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:shadow-md transition-all space-y-4">
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                        <i data-lucide="database" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider">BASIS DATA (RDBMS)</span>
                        <h4 class="font-extrabold text-base text-slate-900 mt-0.5">Query Relasi Tabel Absensi & Izin</h4>
                    </div>
                </div>
                <span class="bg-amber-100 text-amber-800 font-extrabold text-[10px] px-2.5 py-1 rounded-full uppercase">3 Hari Lagi</span>
            </div>

            <p class="text-xs text-slate-600 leading-relaxed">
                Buat skema SQL DDL untuk mencatat log keterlambatan siswa dan validasi surat dokter izin sakit.
            </p>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2 border-t border-slate-200/60">
                <div class="flex items-center space-x-2 text-xs text-slate-500 font-semibold">
                    <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
                    <span>27 Sep 2026 • 12:00 WITA</span>
                </div>
                <button @click="alert('Detail Soal: Buat script file absensi_schema.sql meliputi foreign key ke tabel users, status enum, dan trigger poin.')" class="bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-bold py-2.5 px-6 rounded-xl transition-all">
                    Detail Soal
                </button>
            </div>
        </div>

        <!-- COMPLETED TASK CARD: MATEMATIKA TERAPAN (Exact from Image 3 Left Bottom) -->
        <div class="p-5 rounded-3xl border border-emerald-200 bg-emerald-50/30 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="check" class="w-5 h-5"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-emerald-700 uppercase">MATEMATIKA TERAPAN</span>
                    <h5 class="font-extrabold text-sm text-slate-900">Latihan Matriks Ordo 3×3</h5>
                </div>
            </div>
            <div class="text-right">
                <span class="font-black text-emerald-700 text-sm">Nilai: 95</span>
                <p class="text-[11px] text-emerald-600 font-semibold">Tuntas</p>
            </div>
        </div>
    </div>

    <!-- SUBMIT MODAL -->
    <div x-show="showSubmitModal" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h4 class="font-extrabold text-sm text-slate-900">Kumpulkan Tugas</h4>
                <button @click="showSubmitModal = false" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <p class="text-xs text-slate-600" x-text="'Mata Pelajaran: ' + taskTitle"></p>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Tautan Repository GitHub / Link Demo</label>
                <input type="url" placeholder="https://github.com/..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs">
            </div>
            <div class="border-2 border-dashed border-slate-200 rounded-2xl p-4 text-center text-xs text-slate-500">
                <i data-lucide="upload" class="w-5 h-5 mx-auto mb-1 text-blue-500"></i>
                <span>Unggah File Dokumen / ZIP (Maks. 25MB)</span>
            </div>
            <div class="flex space-x-2 pt-2">
                <button @click="showSubmitModal = false" class="flex-1 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600">Batal</button>
                <button @click="alert('Tugas ' + taskTitle + ' berhasil dikirimkan!'); showSubmitModal = false" class="flex-1 py-2.5 rounded-xl bg-blue-600 text-white text-xs font-bold shadow-md">Kirimkan</button>
            </div>
        </div>
    </div>
</div>
@endsection

