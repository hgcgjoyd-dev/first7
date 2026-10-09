@extends('layouts.auth')

@section('title', 'Scan Kartu Pelajar')

@section('content')
<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-soft text-center space-y-6">
    <div>
        <!-- Logo Resmi SMK TI Bali Global Badung (Sudah termasuk tulisan resmi) -->
        <div class="flex items-center justify-center mb-3">
            <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK TI Bali Global Badung" class="w-24 sm:w-28 h-auto object-contain drop-shadow-sm">
        </div>
        
        <h2 class="text-2xl font-black text-slate-900 mt-2">SCAN DISINI!</h2>
        <p class="text-xs text-slate-500">Masukkan nomor kartu milik akun yang sedang masuk.</p>
    </div>

    @if ($errors->any())
        <p class="rounded-xl bg-rose-50 p-3 text-sm text-rose-700">{{ $errors->first() }}</p>
    @endif

    @if (isset($siswa))
        <p class="text-sm text-slate-600">Akun aktif: <strong>{{ $siswa->nama_siswa }}</strong></p>
    @endif
    <form method="POST" action="{{ route('scan.post') }}" class="space-y-3">
        @csrf
        <label for="code" class="sr-only">Nomor kartu siswa</label>
        <input id="code" name="code" value="{{ old('code') }}" required maxlength="20" autocomplete="off"
               class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
               placeholder="Nomor kartu siswa">
        <button type="submit" class="w-full rounded-xl bg-blue-600 py-3 font-bold text-white hover:bg-blue-700">
            Catat presensi
        </button>
    </form>

    <a href="{{ route('dashboard.siswa') }}" class="text-sm font-semibold text-blue-700 hover:underline">Kembali ke dashboard</a>
    <div class="flex items-center justify-center my-1">
        <span class="text-xs font-bold text-slate-400 uppercase px-4 bg-white">ATAU</span>
    </div>
    <a href="{{ route('login') }}"
       class="w-full bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 font-extrabold py-3 rounded-2xl flex items-center justify-center space-x-2 transition-all text-xs block text-center">
        <i data-lucide="user-check" class="w-4 h-4 inline-block mr-1"></i>
        <span>MASUK DENGAN AKUN</span>
    </a>
</div>
@endsection
