@extends('layouts.app')

@section('title', 'Jadwal & Tugas Piket Kelas')

@section('content')
<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-soft"
     x-data="{
        piketList: [
            { title: 'Menyapu dan mengepel lantai kelas', desc: 'Dikerjakan pagi hari sebelum jam pertama (07:15 WITA)', done: true },
            { title: 'Bersihkan papan tulis & rapikan spidol', desc: 'Pastikan penghapus bersih & siap digunakan guru', done: true },
            { title: 'Rapikan meja lab & kabel PC Lab RPL 2', desc: 'Setelah praktikum kejuruan selesai (14:00 WITA)', done: false },
            { title: 'Kosongkan tong sampah & matikan AC/Lampu', desc: 'Pukul 15:30 WITA sebelum gerbang kelas dikunci', done: false }
        ],
        get completedCount() {
            return this.piketList.filter(item => item.done).length;
        },
        toggleItem(index) {
            this.piketList[index].done = !this.piketList[index].done;
        }
     }">
    
    <!-- Top Tab Navigation Pills (Exact from Image 3 Header) -->
    <div class="flex items-center space-x-2 bg-slate-100 p-1.5 rounded-2xl mb-6">
        <a href="{{ route('mapel') }}" class="flex-1 py-2.5 px-4 rounded-xl font-bold text-xs text-slate-600 hover:text-slate-900 flex items-center justify-center space-x-2">
            <i data-lucide="book-open" class="w-4 h-4"></i>
            <span>Mapel</span>
        </a>
        <a href="{{ route('bk') }}" class="flex-1 py-2.5 px-4 rounded-xl font-bold text-xs text-slate-600 hover:text-slate-900 flex items-center justify-center space-x-2">
            <i data-lucide="shield-alert" class="w-4 h-4"></i>
            <span>Tugas BK</span>
            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
        </a>
        <a href="{{ route('piket') }}" class="flex-1 py-2.5 px-4 rounded-xl font-bold text-xs bg-white text-blue-600 shadow-xs flex items-center justify-center space-x-2">
            <i data-lucide="sparkles" class="w-4 h-4"></i>
            <span>Piket</span>
        </a>
    </div>

    <!-- SCHEDULE BANNER: TUGAS HARI INI (Exact from Image 3 Right Top) -->
    <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-3xl p-6 text-white space-y-4 shadow-lg shadow-emerald-600/15">
        <div class="flex items-center justify-between">
            <span class="bg-white/20 backdrop-blur-md text-white font-extrabold text-[11px] px-3 py-1 rounded-full uppercase">
                ⏰ TUGAS HARI INI
            </span>
            <span class="text-xs bg-white/10 px-2.5 py-1 rounded-full font-bold">4 Anggota Regu</span>
        </div>
        <div>
            <h3 class="text-2xl font-black">Kamis • Regu 4</h3>
            <p class="text-xs text-emerald-100 mt-0.5">Waktu: 06.40 WITA & Bel Pulang Sekolah</p>
        </div>
        <div class="pt-2 flex items-center space-x-2 text-xs">
            <span class="text-emerald-200">Area Tugas:</span>
            <span class="bg-white text-emerald-900 font-extrabold px-3 py-1 rounded-full">Kelas XI PPLG 1 & Lab RPL</span>
        </div>
    </div>

    <!-- ANGGOTA REGU PIKET HARI INI (Exact from Image 3 Right) -->
    <div class="mt-8 space-y-4">
        <div class="flex items-center justify-between">
            <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">ANGGOTA REGU PIKET HARI INI</h4>
            <span class="text-xs bg-blue-100 text-blue-700 font-bold px-2 py-0.5 rounded-full">Regu 4</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
            <div class="p-3.5 rounded-2xl bg-blue-50/70 border border-blue-200 flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-extrabold flex items-center justify-center">1</div>
                <div>
                    <p class="font-bold text-slate-900">Nama Siswa (Wahyu Pratama)</p>
                    <span class="text-blue-600 font-extrabold text-[11px]">⭐ Koordinator</span>
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 font-extrabold flex items-center justify-center">2</div>
                <div>
                    <p class="font-bold text-slate-900">I Kadek Arya</p>
                    <span class="text-slate-500 font-medium text-[11px]">Area Ruang Kelas</span>
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 font-extrabold flex items-center justify-center">3</div>
                <div>
                    <p class="font-bold text-slate-900">Ni Made Ayu</p>
                    <span class="text-slate-500 font-medium text-[11px]">Area Papan & Meja</span>
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 font-extrabold flex items-center justify-center">4</div>
                <div>
                    <p class="font-bold text-slate-900">I Nyoman Gede</p>
                    <span class="text-slate-500 font-medium text-[11px]">Area Lab RPL 2</span>
                </div>
            </div>
        </div>
    </div>

    <!-- INTERACTIVE CHECKLIST KEBERSIHAN (Exact from Image 3 Right Middle) -->
    <div class="mt-8 space-y-4">
        <div class="flex items-center justify-between">
            <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">CHECKLIST KEBERSIHAN</h4>
            <span class="text-xs font-extrabold text-emerald-600" x-text="completedCount + ' dari 4 Selesai (' + Math.round((completedCount/4)*100) + '%)'">2 dari 4 Selesai (50%)</span>
        </div>

        <!-- Progress Bar -->
        <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
            <div class="bg-emerald-500 h-full rounded-full transition-all duration-300" :style="'width: ' + ((completedCount/4)*100) + '%'"></div>
        </div>

        <div class="space-y-3 pt-2">
            <template x-for="(item, idx) in piketList" :key="idx">
                <div class="p-4 rounded-2xl border border-slate-200 flex items-start justify-between bg-white cursor-pointer hover:bg-slate-50 transition-colors"
                     @click="toggleItem(idx)">
                    <div class="flex items-start space-x-3 text-xs">
                        <input type="checkbox" :checked="item.done" class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500 pointer-events-none">
                        <div>
                            <p :class="item.done ? 'line-through text-slate-400' : 'text-slate-900 font-bold'" x-text="(idx + 1) + '. ' + item.title"></p>
                            <p class="text-[11px] text-slate-500 mt-0.5" x-text="item.desc"></p>
                        </div>
                    </div>
                    <span :class="item.done ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'" 
                          class="text-[10px] font-bold px-2 py-0.5 rounded-full" 
                          x-text="item.done ? 'Selesai' : 'Belum'">
                    </span>
                </div>
            </template>
        </div>

        <button @click="alert('Laporan piket kelas berhasil dikonfirmasi dan dilaporkan kepada Wali Kelas XI PPLG 1!')" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold py-3.5 rounded-2xl shadow-lg shadow-emerald-500/20 flex items-center justify-center space-x-2 transition-all mt-4">
            <i data-lucide="check-check" class="w-5 h-5"></i>
            <span>Konfirmasi Selesai & Lapor Wali Kelas</span>
        </button>
    </div>

    <!-- CATATAN WALI KELAS (Exact from Image 3 Right Bottom) -->
    <div class="mt-6 p-4 rounded-2xl bg-blue-50 border border-blue-200/80 flex items-start space-x-3 text-xs text-blue-900">
        <div class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0 text-[10px] font-bold">i</div>
        <div>
            <p class="font-extrabold">Catatan Wali Kelas XI PPLG 1:</p>
            <p class="mt-0.5 text-blue-800/80">Siswa yang tidak hadir piket wajib mengganti piket 2 hari berturut-turut.</p>
        </div>
    </div>
</div>
@endsection

