@extends('layouts.auth')

@section('title', 'Sign In Siswa')

@section('content')
<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-soft space-y-6">
    <div class="text-center space-y-3">
        <!-- Logo Resmi SMK TI Bali Global Badung (Sudah termasuk tulisan resmi) -->
        <div class="flex items-center justify-center">
            <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK TI Bali Global Badung" class="w-24 sm:w-28 h-auto object-contain drop-shadow-sm">
        </div>

        <div>
            <h2 class="text-2xl font-black text-slate-900">Sign in</h2>
            <p class="text-xs text-slate-500 mt-0.5">Masukkan Data Akun untuk melanjutkan</p>
        </div>

        <!-- Books Illustration (Exact from Image 1 Middle) -->
        <div class="py-1 flex items-center justify-center">
            <div class="w-14 h-12 relative flex items-center justify-center text-blue-500">
                <i data-lucide="book-copy" class="w-10 h-10 text-blue-600"></i>
            </div>
        </div>
    </div>

    <!-- Login Form: Enters to Dashboard -->
    <form action="{{ route('dashboard') }}" method="GET" class="space-y-4">
        <div>
            <label class="block text-xs font-extrabold uppercase text-slate-500 mb-1">EMAIL</label>
            <div class="relative">
                <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
                <input type="email" value="wahyu.pratama@smktibaliglobal.sch.id" placeholder="Masukkan EMAIL" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-10 pr-4 py-3 text-xs font-medium focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-hidden transition-all">
            </div>
        </div>

        <div>
            <label class="block text-xs font-extrabold uppercase text-slate-500 mb-1">KATA SANDI</label>
            <div class="relative">
                <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
                <input type="password" value="••••••••••••" placeholder="Kata Sandi" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-10 pr-4 py-3 text-xs font-medium focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-hidden transition-all">
            </div>
        </div>

        <div class="flex items-center justify-between text-xs">
            <label class="flex items-center space-x-2 cursor-pointer text-slate-600">
                <input type="checkbox" checked class="rounded text-blue-600 focus:ring-blue-500">
                <span>Simpan info akun</span>
            </label>
            <a href="#" class="text-blue-600 hover:underline font-semibold">Lupa kata sandi?</a>
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-3.5 rounded-2xl shadow-lg shadow-blue-500/25 flex items-center justify-center space-x-2 transition-all active:scale-98">
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
