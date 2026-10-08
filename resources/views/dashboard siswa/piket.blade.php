@extends('dashboard siswa.app')

@section('title', 'Jadwal & Tugas Piket Kelas')

@section('content')
<div class="w-full space-y-6"
     x-data="{
        piketList: [
            { title: 'Menyapu dan mengepel lantai kelas', desc: 'Dikerjakan pagi hari sebelum jam pertama (07:15 WITA)', done: true },
            { title: 'Bersihkan papan tulis & rapikan spidol', desc: 'Pastikan penghapus bersih & siap digunakan guru', done: true },
            { title: 'Rapikan meja lab & kabel PC Lab PPLG', desc: 'Setelah praktikum kejuruan selesai (14:00 WITA)', done: false },
            { title: 'Kosongkan tong sampah & matikan AC/Lampu', desc: 'Pukul 15:30 WITA sebelum gerbang kelas dikunci', done: false }
        ],
        isTuntas: {{ (!empty($piketHariIni) && $piketHariIni->status === 'Selesai') ? 'true' : 'false' }},
        showNotifTuntas: false,
        notifMessage: 'Tuntas! Laporan piket kebersihan kelas berhasil diselesaikan dan dilaporkan ke Wali Kelas XI PPLG 1.',
        loading: false,

        getGreeting() {
            const hr = new Date().getHours();
            if (hr >= 4 && hr < 11) return 'Selamat Pagi,';
            if (hr >= 11 && hr < 15) return 'Selamat Siang,';
            if (hr >= 15 && hr < 18) return 'Selamat Sore,';
            return 'Selamat Malam,';
        },

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

    <!-- ========================================================================= -->
    <!-- 1. TOP HEADER HERO (Identical Across Mapel, BK, Piket)                     -->
    <!-- ========================================================================= -->
    <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 text-white rounded-3xl sm:rounded-[32px] p-5 sm:p-7 shadow-lg shadow-blue-500/15 relative overflow-hidden">
        <!-- Ambient decorative shapes -->
        <div class="absolute -top-16 -right-16 w-56 h-56 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-12 -left-12 w-48 h-48 rounded-full bg-indigo-500/20 blur-xl pointer-events-none"></div>

        <!-- Top Navigation Row: Back Button, School Brand, Notification Bell -->
        <div class="relative z-10 flex items-center justify-between pb-4 sm:pb-5 border-b border-white/10">
            <a href="{{ route('dashboard') }}" 
               class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white/15 hover:bg-white/25 backdrop-blur-md flex items-center justify-center text-white transition-all active:scale-95 shadow-sm"
               title="Kembali ke Dashboard">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>

            <!-- Logo Resmi SMK TI Bali Global Badung (Asli Full Color logo-smk.png) -->
            <div class="bg-white/95 rounded-2xl py-2 px-4 sm:px-5 inline-flex items-center shadow-md">
                <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK TI Bali Global Badung" class="h-9 sm:h-11 md:h-13 w-auto object-contain">
            </div>

            <div class="relative">
                <button type="button" 
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white/15 hover:bg-white/25 backdrop-blur-md flex items-center justify-center text-white transition-all active:scale-95 shadow-sm"
                        title="Pemberitahuan Piket">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-blue-600"></span>
                </button>
            </div>
        </div>

        <!-- Middle Row: Greeting & Name on Left; White Circular Avatar on Right -->
        <div class="relative z-10 flex items-center justify-between pt-4 sm:pt-5">
            <div>
                <p class="text-xs sm:text-sm text-blue-100 font-medium" x-text="getGreeting()">Selamat Pagi,</p>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white leading-tight mt-0.5">
                    {{ $siswa->nama ?? 'Nama Siswa' }}
                </h1>
                <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-blue-500/50 sm:bg-white/20 text-[11px] sm:text-xs font-semibold text-white backdrop-blur-md mt-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>Kelas {{ $siswa->kelas ?? 'XI PPLG 1' }} • NIS {{ $siswa->nis ?? '2026042' }}</span>
                </div>
            </div>

            <!-- Avatar -->
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-white text-blue-600 flex items-center justify-center shadow-lg ring-4 ring-white/25 shrink-0">
                <i data-lucide="user" class="w-8 h-8 sm:w-9 sm:h-9"></i>
            </div>
        </div>

        <!-- Bottom Row: Segmented 3-Pill Navigation: Mapel, Tugas BK, Piket (ACTIVE) -->
        <div class="relative z-10 mt-5 bg-blue-900/60 backdrop-blur-md p-1.5 rounded-full flex items-center gap-1 border border-white/15 shadow-inner">
            <a href="{{ route('mapel') }}" 
               class="flex-1 flex items-center justify-center space-x-1.5 sm:space-x-2 py-2 px-3 text-xs sm:text-sm font-bold text-white/80 hover:text-white hover:bg-white/10 transition-all rounded-full">
                <i data-lucide="book-open" class="w-4 h-4"></i>
                <span>Mapel</span>
            </a>

            <a href="{{ route('bk') }}" 
               class="flex-1 flex items-center justify-center space-x-1.5 sm:space-x-2 py-2 px-3 text-xs sm:text-sm font-bold text-white/80 hover:text-white hover:bg-white/10 transition-all rounded-full">
                <i data-lucide="user-check" class="w-4 h-4 text-rose-300"></i>
                <span>Tugas BK</span>
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
            </a>

            <!-- Tab 3: Piket (ACTIVE) -->
            <div class="flex-1 flex items-center justify-center space-x-1.5 sm:space-x-2 py-2 px-3 text-xs sm:text-sm font-extrabold bg-white text-blue-700 rounded-full shadow-md">
                <i data-lucide="sparkles" class="w-4 h-4 text-blue-600"></i>
                <span>Piket</span>
            </div>
        </div>
    </div>

    <!-- NOTIFIKASI TUNTAS (Muncul saat dikonfirmasi) -->
    <div x-show="showNotifTuntas" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="p-4 rounded-2xl bg-emerald-600 text-white shadow-xl shadow-emerald-600/25 border border-emerald-500 flex items-start justify-between">
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

    <!-- ========================================================================= -->
    <!-- 2. MAIN RESPONSIVE CONTENT GRID (Mobile: 1 Column, Desktop: 12 Columns)   -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- ===================================================================== -->
        <!-- LEFT COLUMN (lg:col-span-8) - SCHEDULE & CHECKLIST                     -->
        <!-- ===================================================================== -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-5">

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
                    <span class="bg-white text-emerald-900 font-extrabold px-3 py-1 rounded-full">Kelas XI PPLG 1 & Lab PPLG</span>
                </div>
            </div>

            <!-- ANGGOTA REGU PIKET HARI INI -->
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-soft space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">ANGGOTA REGU PIKET HARI INI</h4>
                    <span class="text-xs bg-blue-100 text-blue-700 font-bold px-2.5 py-0.5 rounded-full">Regu 4</span>
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
                            <span class="text-slate-500 font-medium text-[11px]">Area Lab PPLG</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- INTERACTIVE CHECKLIST KEBERSIHAN -->
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-soft space-y-4">
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
            <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200/80 flex items-start space-x-3 text-xs text-blue-900">
                <div class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0 text-[10px] font-bold">i</div>
                <div>
                    <p class="font-extrabold">Catatan Wali Kelas XI PPLG 1:</p>
                    <p class="mt-0.5 text-blue-800/80">Siswa yang tidak hadir piket wajib mengganti piket 2 hari berturut-turut.</p>
                </div>
            </div>

        </div>

        <!-- ===================================================================== -->
        <!-- RIGHT COLUMN (lg:col-span-4) - DESKTOP SIDEBAR WIDGETS                -->
        <!-- ===================================================================== -->
        <div class="lg:col-span-5 xl:col-span-4 space-y-5">

            <!-- Jadwal Regu Piket Sepekan -->
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-soft space-y-4">
                <div class="flex items-center space-x-2.5 pb-3 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold">
                        <i data-lucide="calendar" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm text-slate-900">Jadwal Regu Piket</h4>
                        <p class="text-[11px] text-slate-500">Kelas XI PPLG 1 (Sepekan)</p>
                    </div>
                </div>

                <div class="space-y-2 text-xs">
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-900">Senin</span>
                            <p class="text-[11px] text-slate-500">Regu 1 (Putu Arya & Tim)</p>
                        </div>
                        <span class="text-[10px] font-extrabold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full">Selesai</span>
                    </div>

                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-900">Selasa</span>
                            <p class="text-[11px] text-slate-500">Regu 2 (Made Bagus & Tim)</p>
                        </div>
                        <span class="text-[10px] font-extrabold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full">Selesai</span>
                    </div>

                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-900">Rabu</span>
                            <p class="text-[11px] text-slate-500">Regu 3 (Nyoman Dina & Tim)</p>
                        </div>
                        <span class="text-[10px] font-extrabold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full">Selesai</span>
                    </div>

                    <div class="p-3 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-between">
                        <div>
                            <span class="font-extrabold text-emerald-900">Kamis (Hari Ini)</span>
                            <p class="text-[11px] text-emerald-700 font-medium">Regu 4 (Wahyu Pratama & Tim)</p>
                        </div>
                        <span class="text-[10px] font-extrabold bg-emerald-600 text-white px-2 py-0.5 rounded-full">Aktif</span>
                    </div>

                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-900">Jumat</span>
                            <p class="text-[11px] text-slate-500">Regu 5 (Ketut Sri & Tim)</p>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400">Besok</span>
                    </div>
                </div>
            </div>

            <!-- SOP Kebersihan Kelas -->
            <div class="bg-gradient-to-br from-slate-900 to-blue-950 text-white rounded-3xl p-5 sm:p-6 shadow-soft space-y-3.5">
                <div class="flex items-center space-x-2 text-teal-300 text-xs font-bold uppercase tracking-wider">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span>Standar Kebersihan (SOP)</span>
                </div>
                <h4 class="font-black text-base text-white">Panduan Piket Kelas</h4>
                <ul class="text-xs text-slate-200 space-y-2 leading-relaxed">
                    <li class="flex items-start space-x-2">
                        <span class="text-teal-400 font-bold">•</span>
                        <span>Hadir minimal 20 menit sebelum bel masuk berbunyi (06.40 WITA).</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <span class="text-teal-400 font-bold">•</span>
                        <span>Pastikan komputer Lab PPLG dimatikan dan stopkontak dicabut saat pulang.</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <span class="text-teal-400 font-bold">•</span>
                        <span>Kunci jendela dan pintu kelas diserahkan ke pos security sekolah.</span>
                    </li>
                </ul>
            </div>

        </div>

    </div>

</div>
@endsection
