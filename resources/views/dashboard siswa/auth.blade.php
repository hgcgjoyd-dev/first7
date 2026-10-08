<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Masuk') - SMK TI Bali Global Badung</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .header-gradient {
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 50%, #3b82f6 100%);
        }
        @keyframes scan-laser {
            0% { top: 10%; opacity: 0.3; }
            50% { top: 85%; opacity: 1; }
            100% { top: 10%; opacity: 0.3; }
        }
        .animate-laser {
            animation: scan-laser 2.4s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-[#f4f7fb] text-slate-800 font-sans antialiased min-h-screen selection:bg-blue-600 selection:text-white flex flex-col justify-between p-4 sm:p-6"
      x-init="$nextTick(() => lucide.createIcons())">

    <!-- Top Simple Nav -->
    <div class="{{ (request()->routeIs('landing') || request()->path() === '/') ? 'max-w-5xl' : 'max-w-xl' }} mx-auto w-full flex items-center justify-between pb-4">
        @if(!request()->routeIs('landing') && request()->path() !== '/')
            <a href="{{ route('landing') }}" class="flex items-center space-x-2 text-xs font-bold text-slate-500 hover:text-blue-600 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Halaman Awal</span>
            </a>
        @else
            <div class="flex items-center space-x-2 text-xs font-semibold text-slate-500">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                <span>SMK TI Bali Global Badung</span>
            </div>
        @endif
    </div>

    <!-- Main Card Body -->
    <main class="{{ (request()->routeIs('landing') || request()->path() === '/') ? 'max-w-5xl' : 'max-w-md' }} mx-auto w-full my-auto transition-all">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="text-center py-4 text-xs text-slate-400 font-semibold tracking-wider uppercase">
        SMK TI BALI GLOBAL BADUNG &bull; 2026
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>

