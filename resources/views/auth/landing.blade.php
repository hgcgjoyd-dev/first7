@extends('layouts.auth') 
 
@section('title', 'Halaman Awal Gateway') 
 
@section('content') 
<div class="space-y-6 text-center"> 
    <!-- Header Box with Logo (Exact from Image 1 Left) --> 
    <div class="header-gradient text-white rounded-3xl sm:rounded-[2.5rem] p-8 sm:p-10 relative overflow-hidden shadow-2xl shadow-blue-500/25"> 
        <!-- Background light blur --> 
        <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-white/10 blur-xl pointer-events-none"></div> 
 
        <div class="flex items-center justify-between text-xs mb-5"> 
            <span class="flex items-center space-x-1.5 bg-black/25 backdrop-blur-md px-3 py-1 rounded-full text-white"> 
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> 
                <span class="font-bold tracking-wider">ONLINE</span> 
            </span> 
            <a href="{{ route('login') }}" class="bg-white/20 hover:bg-white/30 backdrop-blur-md px-3.5 py-1 rounded-full font-bold transition-all text-white flex items-center space-x-1"> 
                <i data-lucide="log-in" class="w-3.5 h-3.5"></i> 
                <span>SIGN IN</span> 
            </a> 
        </div> 
         
        <p class="text-xs uppercase tracking-widest font-extrabold text-blue-200">SELAMAT DATANG</p> 
         
        <!-- Logo Resmi SMK TI Bali Global Badung (PNG langsung, tanpa tulisan dobel) --> 
        <div class="my-5 flex items-center justify-center"> 
            <div class="bg-white rounded-3xl p-3 sm:p-4 shadow-xl flex items-center justify-center w-28 sm:w-32 h-28 sm:h-32"> 
                <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK TI Bali Global Badung" class="w-full h-full object-contain"> 
            </div> 
        </div> 
         
        <h3 class="text-sm sm:text-base font-extrabold uppercase tracking-wide text-blue-100">SILAHKAN LOGIN DIBAWAH</h3> 
    </div> 
 
    <!-- Option 1: Scan Kartu Pelajar (Exact from Image 1 Left) --> 
    <div class="bg-white border border-slate-200/80 rounded-3xl p-6 sm:p-7 space-y-4 shadow-soft hover:shadow-md transition-shadow"> 
        <div class="w-24 h-24 mx-auto border-2 border-dashed border-blue-500 rounded-3xl flex items-center justify-center bg-blue-50/50 text-4xl font-black text-blue-600 shadow-inner"> 
            A 
        </div> 
        <a href="{{ route('scan') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-3.5 rounded-2xl flex items-center justify-center space-x-2 transition-all block shadow-lg shadow-blue-500/20 active:scale-[0.98]"> 
            <i data-lucide="camera" class="w-4 h-4"></i> 
            <span>SCAN KARTU PELAJAR</span> 
        </a> 
    </div> 
 
    <!-- Divider "ATAU" --> 
    <div class="flex items-center justify-center my-3"> 
        <span class="text-xs font-bold text-slate-400 uppercase px-4 bg-transparent tracking-widest">ATAU</span> 
    </div> 
 
    <!-- Option 2: Login Akun Manual (Exact from Image 1 Left) --> 
    <div class="bg-white border border-slate-200/80 rounded-3xl p-6 sm:p-7 space-y-4 shadow-soft hover:shadow-md transition-shadow"> 
        <div class="w-20 h-20 mx-auto bg-slate-50 rounded-2xl border border-slate-200 flex items-center justify-center text-slate-700"> 
            <i data-lucide="contact" class="w-10 h-10 text-slate-700"></i> 
        </div> 
        <a href="{{ route('login') }}" class="w-full bg-slate-50 hover:bg-slate-100 text-blue-600 border border-blue-300 font-extrabold py-3.5 rounded-2xl flex items-center justify-center space-x-2 transition-all block active:scale-[0.98]"> 
            <i data-lucide="log-in" class="w-4 h-4"></i> 
            <span>LOGIN DENGAN AKUN</span> 
        </a> 
    </div> 
</div> 
@endsection