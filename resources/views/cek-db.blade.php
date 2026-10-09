<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeriksaan Database - SMK TI Bali Global</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        code, pre { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-xl w-full bg-slate-800 border border-slate-700/60 rounded-3xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
        <!-- Accent Glow -->
        <div class="absolute -top-16 -right-16 w-44 h-44 rounded-full bg-blue-500/10 blur-2xl pointer-events-none"></div>

        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-5 mb-6">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-blue-600/20 text-blue-400 flex items-center justify-center font-bold">
                    <i data-lucide="database" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-white">Status Database</h1>
                    <p class="text-xs text-slate-400">SMK TI Bali Global Badung</p>
                </div>
            </div>
            @if($status === 'success')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>
                    Terhubung
                </span>
            @elseif($status === 'not_found')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 mr-1.5"></span>
                    DB Belum Ada
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400 mr-1.5"></span>
                    Gagal Koneksi
                </span>
            @endif
        </div>

        <!-- Notification Banner -->
        @if($status === 'success')
            <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-4 mb-6 flex items-start space-x-3">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-400 shrink-0 mt-0.5"></i>
                <div class="text-sm">
                    <p class="font-semibold text-emerald-300">Koneksi Database Berhasil!</p>
                    <p class="text-emerald-400/80 text-xs mt-0.5">Database <code>{{ $targetDb }}</code> sudah aktif dan siap digunakan.</p>
                </div>
            </div>
        @elseif($status === 'not_found')
            <div class="bg-amber-500/10 border border-amber-500/20 rounded-2xl p-4 mb-6">
                <div class="flex items-start space-x-3">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-400 shrink-0 mt-0.5"></i>
                    <div class="text-sm">
                        <p class="font-semibold text-amber-300">Database Belum Terdaftar</p>
                        <p class="text-amber-400/80 text-xs mt-0.5">Server MySQL aktif, tapi database <code>{{ $targetDb }}</code> belum dibuat.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-rose-500/10 border border-rose-500/20 rounded-2xl p-4 mb-6 flex items-start space-x-3">
                <i data-lucide="x-circle" class="w-5 h-5 text-rose-400 shrink-0 mt-0.5"></i>
                <div class="text-sm">
                    <p class="font-semibold text-rose-300">MySQL Server Tidak Terjangkau</p>
                    <p class="text-rose-400/80 text-xs mt-1 font-mono break-all">{{ $errorMsg }}</p>
                    <div class="mt-3 text-xs text-rose-300/80 bg-rose-950/40 p-2.5 rounded-lg border border-rose-500/20">
                        <strong>Solusi:</strong> Buka <strong>XAMPP Control Panel</strong> atau <strong>Laragon</strong> lalu klik tombol <strong>Start pada MySQL</strong>.
                    </div>
                </div>
            </div>
        @endif

        <!-- Connection Details Table -->
        <div class="bg-slate-900/60 rounded-2xl p-4 border border-slate-700/50 mb-6 space-y-2 text-xs">
            <div class="flex justify-between py-1.5 border-b border-slate-800">
                <span class="text-slate-400">Database Target:</span>
                <span class="font-mono font-semibold text-blue-400">{{ $targetDb }}</span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-slate-800">
                <span class="text-slate-400">Host & Port:</span>
                <span class="font-mono text-slate-300">{{ $host }}:{{ $port }}</span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-slate-800">
                <span class="text-slate-400">User MySQL:</span>
                <span class="font-mono text-slate-300">{{ $user }}</span>
            </div>
            <div class="flex justify-between py-1.5">
                <span class="text-slate-400">Jumlah Tabel:</span>
                <span class="font-mono text-slate-300">{{ count($tables ?? []) }} tabel</span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('landing') }}" class="text-xs text-slate-400 hover:text-white transition-colors flex items-center space-x-1.5">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Gateway</span>
            </a>
            <a href="{{ route('cek-db') }}" class="text-xs bg-slate-700 hover:bg-slate-600 text-white font-medium px-4 py-2 rounded-xl transition-colors flex items-center space-x-1.5">
                <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                <span>Refresh Status</span>
            </a>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
