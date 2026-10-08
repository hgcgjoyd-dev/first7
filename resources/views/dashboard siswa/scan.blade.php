@extends('dashboard siswa.auth')

@section('title', 'Scan Kartu Pelajar')

@section('content')
<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-soft text-center space-y-6"
     x-data="{
        scanning: false,
        scanned: false,
        scanCard() {
            if (this.scanning || this.scanned) return;
            this.scanning = true;
            setTimeout(() => {
                this.scanning = false;
                this.scanned = true;
                this.$nextTick(() => {
                    if (window.lucide) lucide.createIcons();
                });
                setTimeout(() => {
                    window.location.href = '{{ route('dashboard') }}';
                }, 500);
            }, 800);
        }
     }">
    
    <div>
        <!-- Logo Resmi SMK TI Bali Global Badung (Sudah termasuk tulisan resmi) -->
        <div class="flex items-center justify-center mb-3">
            <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK TI Bali Global Badung" class="w-24 sm:w-28 h-auto object-contain drop-shadow-sm">
        </div>
        
        <h2 class="text-2xl font-black text-slate-900 mt-2">SCAN DISINI!</h2>
        <p class="text-xs text-slate-500">Scan Kartu Pelajar Kamu</p>
    </div>

    <!-- Scanner Viewfinder Box with Glowing Corner Borders (Exact from Image 1 Right) -->
    <div class="w-full aspect-square max-w-[280px] mx-auto rounded-3xl bg-slate-50 border border-slate-200 relative p-4 flex items-center justify-center overflow-hidden transition-all duration-300"
         :class="scanned ? 'bg-emerald-50/60 border-emerald-400' : ''">
        <div class="w-full h-full relative flex items-center justify-center">
            <!-- Cyan Corners (Berganti Hijau saat terdeteksi) -->
            <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 rounded-tl-xl transition-colors duration-300"
                 :class="scanned ? 'border-emerald-500' : 'border-blue-600'"></div>
            <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 rounded-tr-xl transition-colors duration-300"
                 :class="scanned ? 'border-emerald-500' : 'border-blue-600'"></div>
            <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 rounded-bl-xl transition-colors duration-300"
                 :class="scanned ? 'border-emerald-500' : 'border-blue-600'"></div>
            <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 rounded-br-xl transition-colors duration-300"
                 :class="scanned ? 'border-emerald-500' : 'border-blue-600'"></div>
            
            <!-- Laser Animation (Hanya saat memindai) -->
            <div x-show="!scanned" class="absolute left-2 right-2 h-0.5 bg-cyan-500 shadow-[0_0_12px_#06b6d4] animate-laser"></div>
            
            <!-- Default QR Icon -->
            <div x-show="!scanned" class="flex flex-col items-center">
                <i data-lucide="qr-code" class="w-24 h-24 text-slate-300"></i>
            </div>

            <!-- Success State: Kartu Terverifikasi (Langsung masuk tanpa pop-up OK) -->
            <div x-show="scanned" x-cloak class="flex flex-col items-center space-y-2">
                <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <i data-lucide="check" class="w-10 h-10"></i>
                </div>
                <div class="text-xs font-black text-emerald-700 uppercase tracking-wider">Kartu Terdeteksi!</div>
                <div class="text-[11px] font-bold text-slate-700">Wahyu Pratama • XI PPLG 1</div>
            </div>
        </div>
    </div>

    <!-- Tombol Scan: Langsung Masuk ke Dashboard Tanpa Pop-up OK -->
    <button @click="scanCard()" :disabled="scanning || scanned" 
            class="w-full font-extrabold py-3.5 rounded-2xl shadow-lg flex items-center justify-center space-x-2 transition-all active:scale-98"
            :class="scanned ? 'bg-emerald-600 text-white shadow-emerald-500/25' : (scanning ? 'bg-blue-500 text-white cursor-wait' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-blue-500/25')">
        <template x-if="!scanning && !scanned">
            <span class="flex items-center space-x-2">
                <i data-lucide="scan" class="w-4 h-4"></i>
                <span>MULAI SCAN</span>
            </span>
        </template>
        <template x-if="scanning">
            <span class="flex items-center space-x-2">
                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>MEMINDAI KARTU PELAJAR...</span>
            </span>
        </template>
        <template x-if="scanned">
            <span class="flex items-center space-x-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                <span>BERHASIL! MENGALIHKAN...</span>
            </span>
        </template>
    </button>

    <div class="flex items-center justify-center my-2">
        <span class="text-xs font-bold text-slate-400 uppercase px-4 bg-white">OR</span>
    </div>

    <a href="{{ route('login') }}" class="w-full bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 font-extrabold py-3 rounded-2xl flex items-center justify-center space-x-2 transition-all text-xs block text-center">
        <i data-lucide="credit-card" class="w-4 h-4 inline-block mr-1"></i>
        <span>LOGIN MANUAL</span>
    </a>
</div>
@endsection
