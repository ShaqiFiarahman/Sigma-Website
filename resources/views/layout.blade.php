<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGMA — @yield('title')</title>
    <meta name="description" content="SIGMA — Sistem Informasi Tanggap Bencana.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FAFAFA] text-slate-900 min-h-screen flex flex-col font-sans selection:bg-primary-100 selection:text-primary-900">

    {{-- NAVBAR --}}
    <nav id="mainNavbar" class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            
            <div class="flex items-center gap-8">
                {{-- Brand --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 shrink-0 group">
                    <div class="w-8 h-8 rounded-lg bg-slate-900 flex items-center justify-center text-white transition-transform group-hover:scale-105">
                        <i class="bi bi-shield-check text-sm"></i>
                    </div>
                    <span class="font-bold tracking-tight text-slate-900">SIGMA</span>
                </a>

                {{-- Desktop nav links --}}
                <ul class="hidden md:flex items-center gap-1">
                    <li>
                        <a href="{{ route('dashboard') }}" 
                           class="px-3 py-2 rounded-md text-sm font-medium transition-colors 
                                  {{ Route::is('dashboard') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('laporan') }}" 
                           class="px-3 py-2 rounded-md text-sm font-medium transition-colors 
                                  {{ Route::is('laporan') || Route::is('detail') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            Laporan
                        </a>
                    </li>
                </ul>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('create') }}" class="hidden md:inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="bi bi-plus-lg mr-2 text-xs"></i> Buat Laporan
                </a>
                
                {{-- User profile --}}
                <div class="hidden md:flex items-center gap-3 pl-4 border-l border-slate-200">
                    <div class="text-right">
                        <p class="text-sm font-semibold text-slate-900 leading-none">Admin</p>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-xs text-slate-500 hover:text-red-600 transition-colors mt-1 inline-block">Logout</button>
                        </form>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 text-sm font-bold">
                        A
                    </div>
                </div>

                {{-- Mobile toggle --}}
                <button id="mobileToggle" type="button" class="md:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                    <i class="bi bi-list text-xl" id="mobileIcon"></i>
                </button>
            </div>
        </div>

        {{-- Mobile dropdown --}}
        <div id="mobileMenu" class="md:hidden hidden border-t border-slate-200 bg-white px-4 py-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium {{ Route::is('dashboard') ? 'bg-slate-50 text-slate-900' : 'text-slate-600' }}">Dashboard</a>
            <a href="{{ route('laporan') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium {{ Route::is('laporan') || Route::is('detail') ? 'bg-slate-50 text-slate-900' : 'text-slate-600' }}">Laporan</a>
            <a href="{{ route('create') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium {{ Route::is('create') ? 'bg-slate-50 text-slate-900' : 'text-slate-600' }}">Buat Laporan</a>
            <div class="border-t border-slate-100 mt-2 pt-2">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg">Logout</button>
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