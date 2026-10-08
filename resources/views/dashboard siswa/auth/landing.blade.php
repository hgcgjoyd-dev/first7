@extends('dashboard siswa.auth.auth')

@section('title', 'Halaman Awal Gateway')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-stretch"
     x-data="{
         activeSlide: 0,
         slides: [
             {
                 src: '{{ asset('images/images.png') }}',
                 badge: 'Teaching Factory Modern',
                 title: 'Gedung Teaching Factory',
                 desc: 'Pusat Kejuruan Axioo Class Program & PLN Icon Plus dengan standar industri teknologi terdepan.'
             },
             {
                 src: '{{ asset('images/images2.png') }}',
                 badge: 'Fasilitas Praktik Unggulan',
                 title: 'Gedung Lab Industri & TeFa',
                 desc: 'Ruang kerja dan laboratorium industri komprehensif penunjang keahlian siswa DKV, PPLG , TJKT & BD.'
             },
             {
                 src: '{{ asset('images/images3.png') }}',
                 badge: 'Lingkungan Asri',
                 title: 'Area & Ruang Teori',
                 desc: 'Suasana belajar tertib, representatif, dan menjunjung tinggi kedisiplinan serta budaya Bali.'
             }
         ],
         timer: null,
         startAutoplay() {
             this.timer = setInterval(() => {
                 this.activeSlide = (this.activeSlide + 1) % this.slides.length;
             }, 4500);
         },
         stopAutoplay() {
             if (this.timer) clearInterval(this.timer);
         },
         setSlide(index) {
             this.activeSlide = index;
             this.stopAutoplay();
             this.startAutoplay();
         },
         next() {
             this.activeSlide = (this.activeSlide + 1) % this.slides.length;
             this.stopAutoplay();
             this.startAutoplay();
         },
         prev() {
             this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length;
             this.stopAutoplay();
             this.startAutoplay();
         }
     }"
     x-init="startAutoplay()">

    <!-- LEFT / TOP COLUMN: Interactive Campus Image Showcase (Responsive 7 cols on Desktop, Full on Mobile) -->
    <div class="lg:col-span-7 flex flex-col justify-between space-y-4"
         @mouseenter="stopAutoplay()" 
         @mouseleave="startAutoplay()">
        
        <!-- Showcase Main Visual Box -->
        <div class="relative w-full rounded-3xl sm:rounded-[36px] overflow-hidden bg-slate-900 border border-slate-200/80 shadow-soft group aspect-[4/3] sm:aspect-[16/10] lg:aspect-auto lg:h-[460px]">
            <!-- Slide 0: Gedung Teaching Factory (images.png) -->
            <div x-show="activeSlide === 0"
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 scale-105"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute inset-0 w-full h-full">
                <img src="{{ asset('images/images.png') }}" 
                     alt="Gedung Teaching Factory" 
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-black/20"></div>
            </div>

            <!-- Slide 1: Gedung Lab Industri & TeFa (images2.png) -->
            <div x-show="activeSlide === 1"
                 x-cloak
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 scale-105"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute inset-0 w-full h-full">
                <img src="{{ asset('images/images2.png') }}" 
                     alt="Gedung Lab Industri & TeFa" 
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-black/20"></div>
            </div>

            <!-- Slide 2: Area Kampus & Ruang Teori (images3.png) -->
            <div x-show="activeSlide === 2"
                 x-cloak
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 scale-105"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute inset-0 w-full h-full">
                <img src="{{ asset('images/images3.png') }}" 
                     alt="Area & Ruang Teori" 
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-black/20"></div>
            </div>

            <!-- Top Floating Header inside photo card -->
            <div class="absolute top-4 left-4 right-4 flex items-center justify-between z-20">
                <span class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-full text-[11px] font-bold bg-white/90 text-blue-900 backdrop-blur-md shadow-md">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-rose-500"></i>
                    <span> SMK TI Bali Global Badung</span>
                </span>
                
                <!-- Slide Counter Pill -->
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-black/50 text-white backdrop-blur-md border border-white/20">
                    <span x-text="activeSlide + 1">1</span>/<span>3</span>
                </span>
            </div>

            <!-- Bottom Floating Caption & Controls -->
            <div class="absolute bottom-0 left-0 right-0 p-5 sm:p-7 z-20 text-white">
                <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-extrabold tracking-wider uppercase bg-blue-600 text-white mb-2 shadow-xs"
                      x-text="slides[activeSlide].badge">
                    Teaching Factory Modern
                </span>
                <h3 class="text-xl sm:text-2xl font-black tracking-tight leading-tight text-white drop-shadow-sm" 
                    x-text="slides[activeSlide].title">
                    Gedung Teaching Factory
                </h3>
                <p class="text-xs sm:text-sm text-slate-200 mt-1 line-clamp-2 max-w-lg leading-relaxed"
                   x-text="slides[activeSlide].desc">
                    Pusat Kejuruan Axioo Class Program & PLN Icon Plus dengan standar industri teknologi terdepan.
                </p>

                <!-- Navigation Controls & Indicators -->
                <div class="flex items-center justify-between mt-4 pt-3 border-t border-white/15">
                    <!-- Dot Indicators -->
                    <div class="flex items-center space-x-2">
                        <template x-for="(slide, i) in slides" :key="i">
                            <button @click="setSlide(i)" 
                                    class="h-2 rounded-full transition-all duration-300 cursor-pointer"
                                    :class="activeSlide === i ? 'w-8 bg-blue-400' : 'w-2 bg-white/40 hover:bg-white/70'"
                                    :title="'Foto ' + (i+1)">
                            </button>
                        </template>
                    </div>

                    <!-- Arrow buttons -->
                    <div class="flex items-center space-x-2">
                        <button @click="prev()" 
                                class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/35 backdrop-blur-md flex items-center justify-center text-white transition-all active:scale-95 cursor-pointer"
                                title="Sebelumnya">
                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                        </button>
                        <button @click="next()" 
                                class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/35 backdrop-blur-md flex items-center justify-center text-white transition-all active:scale-95 cursor-pointer"
                                title="Selanjutnya">
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3 Quick Photo Thumbnails below slider (Interactive & Responsive) -->
        <div class="grid grid-cols-3 gap-2.5 sm:gap-3">
            <button @click="setSlide(0)" 
                    class="group relative rounded-2xl overflow-hidden border-2 transition-all text-left bg-slate-900 cursor-pointer aspect-[16/9]"
                    :class="activeSlide === 0 ? 'border-blue-600 shadow-md ring-2 ring-blue-500/30' : 'border-transparent opacity-65 hover:opacity-100'">
                <img src="{{ asset('images/images.png') }}" alt="Gedung Teaching Factory" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent p-2 flex flex-col justify-end">
                    <span class="text-[10px] sm:text-[11px] font-bold text-white truncate">Teaching Factory</span>
                </div>
            </button>
            <button @click="setSlide(1)" 
                    class="group relative rounded-2xl overflow-hidden border-2 transition-all text-left bg-slate-900 cursor-pointer aspect-[16/9]"
                    :class="activeSlide === 1 ? 'border-blue-600 shadow-md ring-2 ring-blue-500/30' : 'border-transparent opacity-65 hover:opacity-100'">
                <img src="{{ asset('images/images2.png') }}" alt="Gedung Lab Industri" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent p-2 flex flex-col justify-end">
                    <span class="text-[10px] sm:text-[11px] font-bold text-white truncate">Lab Industri</span>
                </div>
            </button>
            <button @click="setSlide(2)" 
                    class="group relative rounded-2xl overflow-hidden border-2 transition-all text-left bg-slate-900 cursor-pointer aspect-[16/9]"
                    :class="activeSlide === 2 ? 'border-blue-600 shadow-md ring-2 ring-blue-500/30' : 'border-transparent opacity-65 hover:opacity-100'">
                <img src="{{ asset('images/images3.png') }}" alt="Area & Lapangan" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent p-2 flex flex-col justify-end">
                    <span class="text-[10px] sm:text-[11px] font-bold text-white truncate">Area Kampus</span>
                </div>
            </button>
        </div>
    </div>

    <!-- RIGHT / BOTTOM COLUMN: Gateway & Login Options -->
    <div class="lg:col-span-5 flex flex-col justify-between space-y-4">
        <!-- Header Box with Logo (Matching reference design) -->
        <div class="header-gradient text-white rounded-3xl p-6 sm:p-7 relative overflow-hidden shadow-xl shadow-blue-500/20 text-center">
            <!-- Background light blur -->
            <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-white/10 blur-xl pointer-events-none"></div>

            <!-- Logo Resmi SMK TI Bali Global Badung -->
            <div class="my-4 flex items-center justify-center">
                <div class="bg-white rounded-3xl p-3 sm:p-4 shadow-xl flex items-center justify-center w-32 h-32 sm:w-36 sm:h-36">
                    <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK TI Bali Global Badung" class="w-full h-full object-contain">
                </div>
            </div>
            
            <h3 class="text-sm font-extrabold uppercase tracking-wide text-white">PILIH METODE MASUK</h3>
            <p class="text-[11px] text-blue-100 mt-0.5">Silahkan verifikasi kartu atau akun Anda</p>
        </div>

        <!-- Option 1: Scan Kartu Pelajar -->
        <div class="bg-white border border-slate-200/80 rounded-3xl p-5 space-y-3 shadow-soft hover:shadow-md transition-shadow text-center">
            <div class="w-16 h-16 mx-auto border-2 border-dashed border-blue-500 rounded-2xl flex items-center justify-center bg-blue-50/60 text-2xl font-black text-blue-600 shadow-inner">
                <i data-lucide="scan-barcode" class="w-8 h-8 text-blue-600"></i>
            </div>
            <a href="{{ route('scan') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-3 rounded-xl flex items-center justify-center space-x-2 transition-all block shadow-lg shadow-blue-500/20 active:scale-98 text-xs sm:text-sm">
                <i data-lucide="camera" class="w-4 h-4"></i>
                <span>SCAN KARTU PELAJAR</span>
            </a>
        </div>

        <!-- Divider "ATAU" -->
        <div class="flex items-center justify-center my-1">
            <span class="text-[11px] font-bold text-slate-400 uppercase px-4 bg-transparent tracking-widest">ATAU</span>
        </div>

        <!-- Option 2: Login Akun Manual -->
        <div class="bg-white border border-slate-200/80 rounded-3xl p-5 space-y-3 shadow-soft hover:shadow-md transition-shadow text-center">
            <div class="w-14 h-14 mx-auto bg-slate-50 rounded-2xl border border-slate-200 flex items-center justify-center text-slate-700">
                <i data-lucide="contact" class="w-7 h-7 text-slate-700"></i>
            </div>
            <a href="{{ route('login') }}" class="w-full bg-slate-50 hover:bg-slate-100 text-blue-600 border border-blue-200 font-extrabold py-3 rounded-xl flex items-center justify-center space-x-2 transition-all block active:scale-98 text-xs sm:text-sm">
                <i data-lucide="log-in" class="w-4 h-4"></i>
                <span>LOGIN DENGAN AKUN</span>
            </a>
        </div>
    </div>
</div>
@endsection

