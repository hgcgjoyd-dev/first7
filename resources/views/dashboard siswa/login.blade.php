@extends('layouts.auth')

@section('title', 'Sign In Siswa')

@section('content')
<div class="bg-white rounded-3xl sm:rounded-[36px] p-6 sm:p-8 border border-slate-200/80 shadow-soft space-y-6"
     x-data="{ showPassword: false }">
    <div class="text-center space-y-3">
        <!-- Logo Resmi SMK TI Bali Global Badung -->
        <div class="flex items-center justify-center">
            <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK TI Bali Global Badung" class="w-24 sm:w-28 h-auto object-contain drop-shadow-sm">
        </div>

        <div>
            <h2 class="text-2xl font-black text-slate-900">Sign in</h2>
            <p class="text-xs text-slate-500 mt-0.5">Masukkan Data Akun untuk melanjutkan</p>
        </div>

        <!-- Books Illustration -->
        <div class="py-1 flex items-center justify-center">
            <div class="w-14 h-12 relative flex items-center justify-center text-blue-500">
                <i data-lucide="book-copy" class="w-10 h-10 text-blue-600"></i>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 text-xs p-3.5 rounded-2xl flex items-center space-x-2">
            <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 text-rose-500"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Login Form: Enters to Dashboard via Database Authentication -->
    <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-xs font-extrabold uppercase text-slate-500 mb-1">EMAIL / NIS</label>
            <div class="relative">
                <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
                <input type="text" name="email" value="{{ old('email') }}" placeholder="Masukkan Email atau NIS" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-10 pr-4 py-3 text-xs font-medium focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-hidden transition-all">
            </div>
        </div>

        <div>
            <label class="block text-xs font-extrabold uppercase text-slate-500 mb-1">KATA SANDI</label>
            <div class="relative">
                <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
                <input :type="showPassword ? 'text' : 'password'" 
                       name="password" 
                       value="" 
                       placeholder="Masukkan Kata Sandi" 
                       required 
                       class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-10 pr-11 py-3 text-xs font-medium focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-hidden transition-all">
                <!-- Eye icon button to toggle password visibility (menggunakan SVG langsung agar tidak duplikasi) -->
                <button type="button" 
                        @click="showPassword = !showPassword" 
                        class="absolute right-3.5 top-3 text-slate-400 hover:text-blue-600 focus:outline-none transition-colors cursor-pointer p-0.5" 
                        title="Lihat / Sembunyikan Kata Sandi">
                    <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                        <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <svg x-show="showPassword" x-cloak xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 text-blue-600">
                        <path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49"/>
                        <path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"/>
                        <path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143"/>
                        <path d="m2 2 20 20"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="flex items-center justify-between text-xs">
            <label class="flex items-center space-x-2 cursor-pointer text-slate-600">
                <input type="checkbox" name="remember" class="rounded text-blue-600 focus:ring-blue-500">
                <span>Simpan info akun</span>
            </label>
            <a href="#" class="text-blue-600 hover:underline font-semibold">Lupa kata sandi?</a>
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-3.5 rounded-2xl shadow-lg shadow-blue-500/25 flex items-center justify-center space-x-2 transition-all active:scale-98 cursor-pointer">
            <i data-lucide="log-in" class="w-4 h-4"></i>
            <span>LOGIN</span>
        </button>
    </form>

    <div class="flex items-center justify-center my-2">
        <span class="text-xs font-bold text-slate-400 uppercase px-4 bg-white">OR</span>
    </div>

    <!-- Scan Card Option -->
    <a href="{{ route('scan') }}" class="w-full bg-slate-50 hover:bg-slate-100 text-blue-600 border border-blue-200 font-extrabold py-3 rounded-2xl flex items-center justify-center space-x-2 transition-all text-xs block text-center">
        <i data-lucide="scan" class="w-4 h-4 inline-block mr-1"></i>
        <span>SCAN KARTU PELAJAR</span>
    </a>
</div>
@endsection
