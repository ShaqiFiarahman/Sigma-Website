<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGMA — @yield('title')</title>
    <meta name="description" content="SIGMA — Sistem Informasi Tanggap Bencana.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --abyss: #0A0F1E;
            --abyss-mid: #111827;
            --frost: #E4F0F6;
            --frost-mid: #C8DFF0;
            --accent: #3B6FE8;
            --accent-light: #5B8DF5;
        }

        body {
            background-color: #F0F4F8;
            background-image:
                radial-gradient(ellipse 80% 50% at 50% -20%, rgba(59,111,232,0.08) 0%, transparent 70%);
        }

        /* Navbar glassy style */
        #mainNavbar {
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(10,15,30,0.08);
        }

        /* Active nav link with gradient underline */
        .nav-active {
            background: linear-gradient(135deg, rgba(59,111,232,0.10) 0%, rgba(59,111,232,0.05) 100%);
            color: var(--accent) !important;
            font-weight: 600;
        }

        /* Animated gradient brand mark */
        .brand-mark {
            background: linear-gradient(135deg, var(--abyss) 0%, #1e3a8a 100%);
            box-shadow: 0 2px 8px rgba(10,15,30,0.25);
        }

        /* CTA button */
        .btn-primary {
            background: linear-gradient(135deg, #1e3a8a 0%, var(--accent) 100%);
            box-shadow: 0 2px 8px rgba(59,111,232,0.25);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--accent) 0%, #5B8DF5 100%);
            box-shadow: 0 4px 14px rgba(59,111,232,0.35);
            transform: translateY(-1px);
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0);    }
        }
        .animate-fade-up {
            animation: fadeUp 0.45s cubic-bezier(.22,.68,0,1.2) both;
        }
    </style>
</head>
<body class="text-slate-900 min-h-screen flex flex-col font-sans selection:bg-blue-100 selection:text-blue-900">

    {{-- NAVBAR --}}
    <nav id="mainNavbar" class="sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            
            <div class="flex items-center gap-8">
                {{-- Brand --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 shrink-0 group">
                    <div class="brand-mark w-8 h-8 rounded-lg flex items-center justify-center text-white transition-all duration-300 group-hover:scale-105 group-hover:shadow-lg">
                        <i class="bi bi-shield-check text-sm"></i>
                    </div>
                    <span class="font-bold tracking-tight text-slate-900">SIGMA</span>
                </a>

                {{-- Desktop nav links --}}
                <ul class="hidden md:flex items-center gap-1">
                    <li>
                        <a href="{{ route('dashboard') }}" 
                           class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 
                                  {{ Route::is('dashboard') ? 'nav-active' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                            <i class="bi bi-grid-1x2 mr-1.5 text-xs opacity-70"></i>Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('laporan.index') }}" 
                           class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 
                                  {{ Route::is('laporan.index') || Route::is('laporan.show') ? 'nav-active' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                            <i class="bi bi-file-earmark-text mr-1.5 text-xs opacity-70"></i>Laporan
                        </a>
                    </li>
                </ul>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('laporan.create') }}" class="btn-primary hidden md:inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white rounded-lg transition-all duration-200">
                    <i class="bi bi-plus-lg mr-1.5 text-xs"></i> Buat Laporan
                </a>
                
                {{-- User profile --}}
                <div class="hidden md:flex items-center gap-3 pl-4 border-l border-slate-200">
                    <div class="text-right">
                        <p class="text-sm font-semibold text-slate-900 leading-none">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ auth()->user()->role }}</p>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-[10px] text-slate-400 hover:text-red-500 transition-colors mt-1 inline-block font-semibold">Logout</button>
                        </form>
                    </div>
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold" style="background: linear-gradient(135deg, #0A0F1E 0%, #1e3a8a 100%);">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                </div>

                {{-- Mobile toggle --}}
                <button id="mobileToggle" type="button" class="md:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                    <i class="bi bi-list text-xl" id="mobileIcon"></i>
                </button>
            </div>
        </div>

        {{-- Mobile dropdown --}}
        <div id="mobileMenu" class="md:hidden hidden border-t border-slate-200/80 bg-white/95 backdrop-blur-md px-4 py-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium {{ Route::is('dashboard') ? 'bg-blue-50 text-blue-700' : 'text-slate-600' }}">
                <i class="bi bi-grid-1x2"></i>Dashboard
            </a>
            <a href="{{ route('laporan.index') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium {{ Route::is('laporan.index') || Route::is('laporan.show') ? 'bg-blue-50 text-blue-700' : 'text-slate-600' }}">
                <i class="bi bi-file-earmark-text"></i>Laporan
            </a>
            <a href="{{ route('laporan.create') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium {{ Route::is('laporan.create') ? 'bg-blue-50 text-blue-700' : 'text-slate-600' }}">
                <i class="bi bi-plus-circle"></i>Buat Laporan
            </a>
            <div class="border-t border-slate-100 mt-2 pt-2">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left flex items-center gap-2 px-3 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg">
                        <i class="bi bi-box-arrow-right"></i>Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-16 animate-fade-up">
        
        {{-- Page Header --}}
        @if(!Route::is('dashboard'))
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">@yield('title')</h1>
                @if(View::hasSection('subtitle'))
                    <p class="text-sm text-slate-500 mt-1">@yield('subtitle')</p>
                @endif
            </div>
            @yield('page-actions')
        </div>
        @endif

        @yield('content')
    </main>

    <script>
        const toggle = document.getElementById('mobileToggle');
        const menu   = document.getElementById('mobileMenu');
        const icon   = document.getElementById('mobileIcon');
        toggle?.addEventListener('click', () => {
            menu.classList.toggle('hidden');
            icon.className = menu.classList.contains('hidden') ? 'bi bi-list text-xl' : 'bi bi-x-lg text-xl';
        });
    </script>
    @yield('scripts')
</body>
</html>