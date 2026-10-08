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
        isTuntas: {{ (!empty($piketHariIni) && $piketHariIni->status === 'Selesai') ? 'true' : 'false' }},
        showNotifTuntas: false,
        notifMessage: 'Tuntas! Laporan piket kebersihan kelas berhasil diselesaikan dan dilaporkan ke Wali Kelas XI PPLG 1.',
        loading: false,

        init() {
            const savedItems = localStorage.getItem('piket_items_state');
            if (savedItems) {
                try {
                    this.piketList = JSON.parse(savedItems);
                } catch(e) {}
            }
            if (localStorage.getItem('piket_is_tuntas') === 'true') {
                this.isTuntas = true;
            }
            this.$nextTick(() => {
                if (window.lucide) lucide.createIcons();
            });
        },

        get completedCount() {
            return this.piketList.filter(item => item.done).length;
        },

        toggleItem(index) {
            this.piketList[index].done = !this.piketList[index].done;
            this.isTuntas = (this.completedCount === this.piketList.length);
            localStorage.setItem('piket_items_state', JSON.stringify(this.piketList));
            localStorage.setItem('piket_is_tuntas', this.isTuntas ? 'true' : 'false');
            this.$nextTick(() => {
                if (window.lucide) lucide.createIcons();
            });
        },

        konfirmasiPiket() {
            this.loading = true;
            // Tandai semua checklist selesai
            this.piketList.forEach(item => item.done = true);
            this.isTuntas = true;
            localStorage.setItem('piket_items_state', JSON.stringify(this.piketList));
            localStorage.setItem('piket_is_tuntas', 'true');

            fetch('{{ route('piket.confirm') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: 'Selesai' })
            }).then(res => res.json())
              .then(data => {
                  this.loading = false;
                  this.showNotifTuntas = true;
                  this.$nextTick(() => {
                      if (window.lucide) lucide.createIcons();
                  });
              }).catch(err => {
                  this.loading = false;
                  this.showNotifTuntas = true;
                  this.$nextTick(() => {
                      if (window.lucide) lucide.createIcons();
                  });
              });
        }
     }">
    
    <!-- Top Tab Navigation Pills -->
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

    <!-- NOTIFIKASI TUNTAS (Muncul saat dikonfirmasi) -->
    <div x-show="showNotifTuntas" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="mb-6 p-4 rounded-2xl bg-emerald-600 text-white shadow-xl shadow-emerald-600/25 border border-emerald-500 flex items-start justify-between animate-in">
        <div class="flex items-center space-x-3.5">
            <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center shrink-0">
                <i data-lucide="check-circle-2" class="w-6 h-6 text-white"></i>
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <span class="bg-white text-emerald-800 text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase tracking-wider">TUNTAS</span>
                    <h4 class="font-extrabold text-sm text-white">Laporan Kebersihan Terkirim</h4>
                </div>
                <p class="text-xs text-emerald-100 mt-0.5" x-text="notifMessage"></p>
            </div>
        </div>
        <button type="button" @click="showNotifTuntas = false" class="text-emerald-200 hover:text-white p-1 rounded-lg cursor-pointer">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>

    <!-- SCHEDULE BANNER: TUGAS HARI INI -->
    <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-3xl p-6 text-white space-y-4 shadow-lg shadow-emerald-600/15">
        <div class="flex items-center justify-between">
            <span class="bg-white/20 backdrop-blur-md text-white font-extrabold text-[11px] px-3 py-1 rounded-full uppercase">
                ⏰ TUGAS HARI INI
            </span>
            <div class="flex items-center space-x-2">
                <template x-if="isTuntas">
                    <span class="text-xs bg-emerald-400 text-slate-900 px-3 py-1 rounded-full font-black flex items-center space-x-1 shadow-sm">
                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        <span>TUNTAS</span>
                    </span>
                </template>
                <template x-if="!isTuntas">
                    <span class="text-xs bg-white/10 px-2.5 py-1 rounded-full font-bold">4 Anggota Regu</span>
                </template>
            </div>
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

    <!-- ANGGOTA REGU PIKET HARI INI -->
    <div class="mt-8 space-y-4">
        <div class="flex items-center justify-between">
            <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">ANGGOTA REGU PIKET HARI INI</h4>
            <span class="text-xs bg-blue-100 text-blue-700 font-bold px-2 py-0.5 rounded-full">Regu 4</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
            <div class="p-3.5 rounded-2xl bg-blue-50/70 border border-blue-200 flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-extrabold flex items-center justify-center">1</div>
                <div>
                    <p class="font-bold text-slate-900">{{ $siswa->nama ?? 'Wahyu Pratama' }}</p>
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

    <!-- INTERACTIVE CHECKLIST KEBERSIHAN -->
    <div class="mt-8 space-y-4">
        <div class="flex items-center justify-between">
            <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">CHECKLIST KEBERSIHAN</h4>
            <div class="flex items-center space-x-2">
                <template x-if="isTuntas">
                    <span class="bg-emerald-100 text-emerald-800 text-[11px] font-black px-2.5 py-0.5 rounded-full uppercase">
                        ✓ SEMUA TUNTAS (100%)
                    </span>
                </template>
                <template x-if="!isTuntas">
                    <span class="text-xs font-extrabold text-emerald-600" x-text="completedCount + ' dari ' + piketList.length + ' Selesai (' + Math.round((completedCount/piketList.length)*100) + '%)'"></span>
                </template>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
            <div class="bg-emerald-500 h-full rounded-full transition-all duration-300" :style="'width: ' + ((completedCount/piketList.length)*100) + '%'"></div>
        </div>

        <div class="space-y-3 pt-2">
            <template x-for="(item, idx) in piketList" :key="idx">
                <div class="p-4 rounded-2xl border flex items-start justify-between transition-all cursor-pointer"
                     :class="item.done ? 'bg-emerald-50/40 border-emerald-200 hover:bg-emerald-50/70' : 'bg-white border-slate-200 hover:bg-slate-50'"
                     @click="toggleItem(idx)">
                    <div class="flex items-start space-x-3 text-xs">
                        <input type="checkbox" :checked="item.done" class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500 pointer-events-none accent-emerald-600">
                        <div>
                            <p :class="item.done ? 'line-through text-slate-400 font-medium' : 'text-slate-900 font-bold'" x-text="(idx + 1) + '. ' + item.title"></p>
                            <p class="text-[11px] text-slate-500 mt-0.5" x-text="item.desc"></p>
                        </div>
                    </div>
                    <span :class="item.done ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'" 
                          class="text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0 ml-2" 
                          x-text="item.done ? 'Selesai' : 'Belum'">
                    </span>
                </div>
            </template>
        </div>

        <!-- TOMBOL KONFIRMASI -->
        <button type="button" 
                @click="konfirmasiPiket()" 
                :disabled="loading"
                :class="isTuntas ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-500/25' : 'bg-blue-600 hover:bg-blue-700 shadow-blue-500/25'"
                class="w-full text-white font-extrabold py-3.5 rounded-2xl shadow-lg flex items-center justify-center space-x-2 transition-all mt-4 cursor-pointer active:scale-98">
            <template x-if="loading">
                <span class="inline-flex items-center space-x-2">
                    <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                    <span>Memperbarui Status ke Database...</span>
                </span>
            </template>
            <template x-if="!loading">
                <span class="inline-flex items-center space-x-2">
                    <i data-lucide="check-check" class="w-5 h-5"></i>
                    <span x-text="isTuntas ? '✓ Laporan Piket Sudah Tuntas & Dilaporkan' : 'Konfirmasi Selesai & Lapor Wali Kelas'"></span>
                </span>
            </template>
        </button>
    </div>

    <!-- CATATAN WALI KELAS -->
    <div class="mt-6 p-4 rounded-2xl bg-blue-50 border border-blue-200/80 flex items-start space-x-3 text-xs text-blue-900">
        <div class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0 text-[10px] font-bold">i</div>
        <div>
            <p class="font-extrabold">Catatan Wali Kelas XI PPLG 1:</p>
            <p class="mt-0.5 text-blue-800/80">Siswa yang tidak hadir piket wajib mengganti piket 2 hari berturut-turut.</p>
        </div>
    </div>
</div>
@endsection
