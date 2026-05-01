<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGMA Admin — @yield('title')</title>
    <meta name="description" content="SIGMA — Sistem Informasi Gerak Maju Tanggap Bencana.">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🛡️</text></svg>">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-900 min-h-screen">

    {{-- ══════════════════════════════════════════
         NAVBAR
    ══════════════════════════════════════════ --}}
    <nav id="mainNavbar"
         class="bg-white border-b border-slate-200 sticky top-0 z-50 transition-shadow duration-300">
        <div class="max-w-screen-xl mx-auto px-6 h-16 flex items-center gap-4">

            {{-- Brand --}}
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-2.5 shrink-0 mr-4 group">
                <div class="w-9 h-9 rounded-[10px] bg-gradient-to-br from-blue-600 to-blue-400
                            flex items-center justify-center text-white text-base
                            shadow-[0_3px_10px_rgba(29,78,216,0.35)]
                            group-hover:scale-105 transition-transform duration-200">
                    <i class="bi bi-shield-fill-check"></i>
                </div>
                <span class="text-[1.1rem] font-extrabold tracking-tight text-slate-900">
                    SIG<span class="text-blue-600">MA</span>
                </span>
            </a>

            {{-- Desktop nav links --}}
            <ul class="hidden md:flex items-center gap-1 mr-auto">
                <li>
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150
                              {{ Route::is('dashboard')
                                 ? 'bg-blue-50 text-blue-600'
                                 : 'text-slate-500 hover:bg-slate-100 hover:text-slate-800' }}">
                        <i class="bi bi-grid-fill text-[0.85rem]"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('laporan') }}"
                       class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150
                              {{ Route::is('laporan') || Route::is('detail')
                                 ? 'bg-blue-50 text-blue-600'
                                 : 'text-slate-500 hover:bg-slate-100 hover:text-slate-800' }}">
                        <i class="bi bi-file-earmark-text-fill text-[0.85rem]"></i> Data Laporan
                    </a>
                </li>
                <li>
                    <a href="{{ route('create') }}"
                       class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150
                              {{ Route::is('create')
                                 ? 'bg-blue-50 text-blue-600'
                                 : 'text-slate-500 hover:bg-slate-100 hover:text-slate-800' }}">
                        <i class="bi bi-plus-circle-fill text-[0.85rem]"></i> Buat Laporan
                    </a>
                </li>
            </ul>

            {{-- Separator --}}
            <div class="hidden md:block w-px h-6 bg-slate-200 shrink-0"></div>

            {{-- User + Logout --}}
            <div class="hidden md:flex items-center gap-2 ml-auto md:ml-0">
                <div class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-slate-100 transition-colors cursor-default">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-600 to-sky-400
                                flex items-center justify-center text-white text-xs font-bold shrink-0">
                        AD
                    </div>
                    <div class="hidden xl:block">
                        <p class="text-xs font-semibold text-slate-800 leading-none">Admin</p>
                        <p class="text-[0.68rem] text-slate-400 leading-none mt-0.5">Super Admin</p>
                    </div>
                </div>
                <a href="{{ route('login') }}"
                   class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 text-slate-500
                          text-xs font-semibold transition-all hover:border-red-300 hover:text-red-600 hover:bg-red-50">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </a>
            </div>

            {{-- Mobile hamburger --}}
            <button id="mobileToggle" type="button"
                    class="md:hidden ml-auto p-2 rounded-lg border border-slate-200 text-slate-500
                           hover:bg-slate-100 transition-colors"
                    aria-label="Toggle menu">
                <i class="bi bi-list text-xl" id="mobileIcon"></i>
            </button>
        </div>

        {{-- Mobile dropdown --}}
        <div id="mobileMenu"
             class="md:hidden hidden border-t border-slate-200 bg-white px-4 py-3 flex flex-col gap-1">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-semibold transition-colors
                      {{ Route::is('dashboard') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i class="bi bi-grid-fill"></i> Dashboard
            </a>
            <a href="{{ route('laporan') }}"
               class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-semibold transition-colors
                      {{ Route::is('laporan') || Route::is('detail') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i class="bi bi-file-earmark-text-fill"></i> Data Laporan
            </a>
            <a href="{{ route('create') }}"
               class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-semibold transition-colors
                      {{ Route::is('create') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i class="bi bi-plus-circle-fill"></i> Buat Laporan
            </a>
            <div class="border-t border-slate-100 mt-1 pt-2 flex items-center justify-between">
                <div class="flex items-center gap-2 px-2">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-600 to-sky-400
                                flex items-center justify-center text-white text-[0.65rem] font-bold">AD</div>
                    <span class="text-xs font-semibold text-slate-700">Admin</span>
                </div>
                <a href="{{ route('login') }}"
                   class="flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold
                          text-slate-500 hover:text-red-600 hover:border-red-300 hover:bg-red-50 transition-all">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    {{-- ══════════════════════════════════════════
         MAIN CONTENT
    ══════════════════════════════════════════ --}}
    <main>
        <div class="max-w-screen-xl mx-auto px-6 py-8 pb-16 animate-fade-up">

            {{-- Page Header --}}
            <div class="flex items-center justify-between mb-7 flex-wrap gap-3">
                <h1 class="flex items-center gap-2.5 text-xl font-bold text-slate-900">
                    <span class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 text-base shrink-0">
                        @if(Route::is('dashboard'))
                            <i class="bi bi-grid-fill"></i>
                        @elseif(Route::is('laporan'))
                            <i class="bi bi-file-earmark-text-fill"></i>
                        @elseif(Route::is('create'))
                            <i class="bi bi-plus-circle-fill"></i>
                        @elseif(Route::is('detail'))
                            <i class="bi bi-file-earmark-check-fill"></i>
                        @else
                            <i class="bi bi-circle-fill"></i>
                        @endif
                    </span>
                    @yield('title')
                </h1>
                @yield('page-actions')
            </div>

            @yield('content')
        </div>
    </main>

    <script>
        // Mobile menu toggle
        const toggle = document.getElementById('mobileToggle');
        const menu   = document.getElementById('mobileMenu');
        const icon   = document.getElementById('mobileIcon');
        toggle?.addEventListener('click', () => {
            menu.classList.toggle('hidden');
            icon.className = menu.classList.contains('hidden')
                ? 'bi bi-list text-xl'
                : 'bi bi-x-lg text-xl';
        });

        // Navbar shadow on scroll
        window.addEventListener('scroll', () => {
            document.getElementById('mainNavbar')
                    ?.classList.toggle('shadow-md', window.scrollY > 8);
        });
    </script>

    @yield('scripts')
</body>
</html>