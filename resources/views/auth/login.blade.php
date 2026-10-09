@extends('layouts.auth')

@section('title', 'Masuk')

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

    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 text-xs p-3.5 rounded-2xl flex items-center space-x-2">
            <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 text-rose-500"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif
    @if (session('status'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs p-3.5 rounded-2xl">
            {{ session('status') }}
        </div>
    @endif

    <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-xs font-extrabold uppercase text-slate-500 mb-1">EMAIL</label>
            <div class="relative">
                <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email" autocomplete="username" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-10 pr-4 py-3 text-xs font-medium focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-hidden transition-all">
            </div>
        </div>

        <div>
            <label class="block text-xs font-extrabold uppercase text-slate-500 mb-1">KATA SANDI</label>
            <div class="relative">
                <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
                <input :type="showPassword ? 'text' : 'password'" 
                       name="password" 
                       placeholder="Kata Sandi" 
                       autocomplete="current-password"
                       required 
                       class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-10 pr-11 py-3 text-xs font-medium focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-hidden transition-all">
                <!-- Eye icon button to toggle password visibility -->
                <button type="button" 
                        @click="showPassword = !showPassword; $nextTick(() => lucide.createIcons())" 
                        class="absolute right-3.5 top-3 text-slate-400 hover:text-blue-600 focus:outline-none transition-colors cursor-pointer p-0.5" 
                        title="Lihat / Sembunyikan Kata Sandi">
                    <template x-if="!showPassword">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </template>
                    <template x-if="showPassword">
                        <i data-lucide="eye-off" class="w-4 h-4 text-blue-600"></i>
                    </template>
                </button>
            </div>
        </div>

        <div class="flex items-center justify-between text-xs">
            <label class="flex items-center space-x-2 cursor-pointer text-slate-600">
                <input type="checkbox" name="remember" value="1" class="rounded text-blue-600 focus:ring-blue-500">
                <span>Simpan info akun</span>
            </label>
            <span class="text-slate-400">Hubungi administrator untuk reset kata sandi.</span>
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-3.5 rounded-2xl shadow-lg shadow-blue-500/25 flex items-center justify-center space-x-2 transition-all active:scale-98 cursor-pointer">
            <i data-lucide="log-in" class="w-4 h-4"></i>
            <span>LOGIN</span>
        </button>
    </form>

</div>
@endsection
