<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Sistem Informasi Kesiswaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
            <a href="{{ route('dashboard') }}" class="font-bold text-blue-800">Sistem Informasi Kesiswaan</a>
            <div class="flex items-center gap-4 text-sm">
                <span>{{ auth()->user()->email }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="font-semibold text-rose-700 hover:text-rose-900">Keluar</button>
                </form>
            </div>
        </div>
        @if (auth()->user()->isAdmin())
            <nav class="mx-auto flex max-w-6xl flex-wrap gap-3 px-4 pb-4 text-sm font-semibold sm:px-6">
                <a href="{{ route('admin.siswa.index') }}" class="text-slate-700 hover:text-blue-700">Siswa</a>
                <a href="{{ route('admin.guru.index') }}" class="text-slate-700 hover:text-blue-700">Guru</a>
                <a href="{{ route('admin.kelas.index') }}" class="text-slate-700 hover:text-blue-700">Kelas</a>
                <a href="{{ route('admin.absensi.index') }}" class="text-slate-700 hover:text-blue-700">Absensi</a>
                <a href="{{ route('admin.absensi.report') }}" class="text-slate-700 hover:text-blue-700">Rekap absensi</a>
                <a href="{{ route('bk.pelanggaran.index') }}" class="text-slate-700 hover:text-blue-700">Pelanggaran</a>
                <a href="{{ route('admin.users.index') }}" class="text-slate-700 hover:text-blue-700">Akun</a>
            </nav>
        @elseif (auth()->user()->isGuruBk())
            <nav class="mx-auto flex max-w-6xl gap-3 px-4 pb-4 text-sm font-semibold sm:px-6">
                <a href="{{ route('bk.pelanggaran.index') }}" class="text-slate-700 hover:text-blue-700">Pelanggaran siswa</a>
            </nav>
        @endif
    </header>

    <main class="mx-auto max-w-6xl space-y-6 px-4 py-8 sm:px-6">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>
</body>
</html>
