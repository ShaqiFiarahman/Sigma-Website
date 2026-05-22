<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGMA — @yield('title')</title>
    <meta name="description" content="SIGMA — Sistem Informasi Tanggap Bencana.">
    <link rel="preload" href="/fonts/PlusJakartaSans-Regular.ttf" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="/fonts/PlusJakartaSans-Bold.ttf" as="font" type="font/ttf" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://maps.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://maps.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"></noscript>
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
                radial-gradient(ellipse 80% 50% at 50% -20%, rgba(59, 111, 232, 0.08) 0%, transparent 70%);
        }

        /* Navbar glassy style */
        #mainNavbar {
            background: rgba(255, 255, 255, 0.97);
            border-bottom: 1px solid rgba(10, 15, 30, 0.08);
        }

        /* Active nav link with gradient underline */
        .nav-active {
            background: linear-gradient(135deg, rgba(59, 111, 232, 0.10) 0%, rgba(59, 111, 232, 0.05) 100%);
            color: var(--accent) !important;
            font-weight: 600;
        }

        /* Animated gradient brand mark */
        .brand-mark {
            background: linear-gradient(135deg, var(--abyss) 0%, #1e3a8a 100%);
            box-shadow: 0 2px 8px rgba(10, 15, 30, 0.25);
        }

        /* CTA button */
        .btn-primary {
            background: linear-gradient(135deg, #1e3a8a 0%, var(--accent) 100%);
            box-shadow: 0 2px 8px rgba(59, 111, 232, 0.25);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--accent) 0%, #5B8DF5 100%);
            box-shadow: 0 4px 14px rgba(59, 111, 232, 0.35);
            transform: translateY(-1px);
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-up {
            animation: fadeUp 0.45s cubic-bezier(.22, .68, 0, 1.2) both;
        }
    </style>
</head>

<body class="text-slate-900 min-h-screen flex flex-col font-sans selection:bg-blue-100 selection:text-blue-900">

    {{-- NAVBAR --}}
    <nav id="mainNavbar" class="sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">

            <div class="flex items-center gap-8">
                {{-- Brand --}}
                <a href="{{ strtolower(auth()->user()->role ?? '') === 'admin' ? route('admin.dashboard') : route('dashboard') }}"
                    class="flex items-center gap-2.5 shrink-0 group">
                    <div
                        class="brand-mark w-10 h-10 rounded-lg flex items-center justify-center text-white transition-all duration-300 group-hover:scale-105 group-hover:shadow-lg">
                        <i class="bi bi-shield-check text-base"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold tracking-tight text-slate-900 text-lg leading-none">SIGMA</span>
                        <span class="text-[10px] text-slate-500 font-medium leading-tight">Sistem Informasi Gawat
                            Darurat dan Mitigasi Bencana</span>
                    </div>
                </a>

                {{-- Desktop nav links — hanya untuk Admin/BNPB --}}
                @if(strtolower(auth()->user()->role ?? '') === 'admin')
                    {{-- Removed: Dashboard and Laporan links --}}
                @endif
            </div>

            <div class="flex items-center gap-4">
                {{-- Tombol CTA berdasarkan role --}}
                @if(strtolower(auth()->user()->role ?? '') === 'admin')
                    {{-- Notifikasi --}}
                    <x-admin-notification />
                    <button type="button" onclick="window.location.href='{{ route('laporan.index') }}'"
                        class="btn-primary hidden md:inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white rounded-lg transition-all duration-200 cursor-pointer">
                        <i class="bi bi-file-earmark-text mr-1.5 text-xs"></i> Kelola Laporan
                    </button>
                @else
                    <button type="button" onclick="window.location.href='{{ route('laporan.create') }}'"
                        class="btn-primary hidden md:inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white rounded-lg transition-all duration-200 cursor-pointer">
                        <i class="bi bi-plus-lg mr-1.5 text-xs"></i> Buat Laporan
                    </button>
                @endif

                {{-- User profile (clickable to open modal) --}}
                <div class="hidden md:flex items-center gap-3 pl-4 border-l border-slate-200 cursor-pointer group"
                    onclick="document.getElementById('profileModal').classList.remove('hidden')">
                    <div class="text-right">
                        <p class="text-sm font-semibold text-slate-900 leading-none group-hover:text-blue-800 transition-colors">{{ auth()->user()->full_name }}</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">
                            {{ auth()->user()->role }}
                        </p>
                    </div>
                    <div class="w-11 h-11 rounded-full flex items-center justify-center text-white text-base font-bold transition-transform group-hover:scale-105"
                        style="background: linear-gradient(135deg, #0A0F1E 0%, #1e3a8a 100%);">
                        {{ substr(auth()->user()->full_name ?? 'U', 0, 1) }}
                    </div>
                </div>

                {{-- Mobile toggle --}}
                <button id="mobileToggle" type="button"
                    class="md:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                    <i class="bi bi-list text-xl" id="mobileIcon"></i>
                </button>
            </div>
        </div>

        {{-- Mobile dropdown --}}
        <div id="mobileMenu"
            class="md:hidden hidden border-t border-slate-200/80 bg-white/95 backdrop-blur-md px-4 py-3 space-y-1">
            <a href="{{ strtolower(auth()->user()->role ?? '') === 'admin' ? route('admin.dashboard') : route('dashboard') }}"
                class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium {{ Route::is('dashboard') || Route::is('admin.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-slate-600' }}">
                <i class="bi bi-grid-1x2"></i>Dashboard
            </a>
            <a href="{{ route('laporan.index') }}"
                class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium {{ Route::is('laporan.index') || Route::is('laporan.show') ? 'bg-blue-50 text-blue-700' : 'text-slate-600' }}">
                <i class="bi bi-file-earmark-text"></i>Laporan
            </a>
            <a href="{{ route('laporan.create') }}"
                class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium {{ Route::is('laporan.create') ? 'bg-blue-50 text-blue-700' : 'text-slate-600' }}">
                <i class="bi bi-plus-circle"></i>{{ strtolower(auth()->user()->role ?? '') === 'admin' ? 'Kelola Laporan' : 'Buat Laporan' }}
            </a>
            <div class="border-t border-slate-100 mt-2 pt-2">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full text-left flex items-center gap-2 px-3 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg">
                        <i class="bi bi-box-arrow-right"></i>Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-16 animate-fade-up">

        {{-- Page Header --}}
        @if(!Route::is('dashboard') && !Route::is('admin.dashboard'))
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

    @yield('footer')

    {{-- ═══════════════════════════════════════════════════
         PROFILE MODAL
    ═══════════════════════════════════════════════════ --}}
    <div id="profileModal" class="hidden fixed inset-0 z-[9999] flex items-center justify-center p-4"
        onclick="if(event.target===this) this.classList.add('hidden')">
        {{-- Overlay --}}
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

        {{-- Modal Card --}}
        <div class="relative w-full max-w-sm bg-white rounded-3xl shadow-2xl overflow-hidden animate-fade-up">
            
            {{-- Header Gradient --}}
            <div class="relative px-6 pt-8 pb-12 text-center"
                style="background: linear-gradient(135deg, #0A0F1E 0%, #0f1f4a 50%, #1a3068 100%);">
                <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full opacity-20 pointer-events-none"
                    style="background: radial-gradient(circle, #3B6FE8 0%, transparent 70%);"></div>

                {{-- Close Button --}}
                <button onclick="document.getElementById('profileModal').classList.add('hidden')"
                    class="absolute top-4 right-4 w-8 h-8 rounded-full flex items-center justify-center text-white/60 hover:text-white hover:bg-white/10 transition-all">
                    <i class="bi bi-x-lg text-sm"></i>
                </button>

                {{-- Avatar --}}
                <div class="w-20 h-20 rounded-full mx-auto flex items-center justify-center text-white text-2xl font-bold border-4 border-white/20"
                    style="background: linear-gradient(135deg, #3B6FE8 0%, #60a5fa 100%); box-shadow: 0 8px 24px rgba(59,111,232,0.4);">
                    {{ substr(auth()->user()->full_name ?? 'U', 0, 1) }}
                </div>

                {{-- Name & Role --}}
                <h3 class="text-lg font-bold text-white mt-4">{{ auth()->user()->full_name }}</h3>
                <div class="inline-flex items-center gap-1.5 mt-2 px-3 py-1 rounded-full"
                    style="background: rgba(228,240,246,0.15); border: 1px solid rgba(228,240,246,0.2);">
                    @php
                        $role = auth()->user()->role;
                        $roleIcon = match(strtolower($role)) {
                            'admin' => 'bi-shield-check',
                            'relawan' => 'bi-person-badge',
                            default => 'bi-person',
                        };
                    @endphp
                    <i class="{{ $roleIcon }} text-[11px] text-blue-200"></i>
                    <span class="text-[11px] font-bold text-blue-200 uppercase tracking-wider">{{ $role }}</span>
                </div>
            </div>

            {{-- Profile Details --}}
            <div class="px-6 py-6 -mt-4">
                <div class="bg-slate-50 rounded-2xl p-4 space-y-4 border border-slate-100">
                    
                    {{-- Email --}}
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                            style="background: linear-gradient(135deg, #E4F0F6, #C8DFF0);">
                            <i class="bi bi-envelope text-sm" style="color: #1e3a8a;"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Email</p>
                            <p class="text-sm font-semibold text-slate-800 truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>

                    {{-- Role --}}
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                            style="background: linear-gradient(135deg, #E4F0F6, #C8DFF0);">
                            <i class="bi bi-person-gear text-sm" style="color: #1e3a8a;"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Role</p>
                            <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->role }}</p>
                        </div>
                    </div>

                    {{-- Member Since --}}
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                            style="background: linear-gradient(135deg, #E4F0F6, #C8DFF0);">
                            <i class="bi bi-calendar-check text-sm" style="color: #1e3a8a;"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Bergabung</p>
                            <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->created_at?->format('d M Y') ?? (auth()->user()->updated_at?->format('d M Y') ?? 'Tidak tersedia') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="mt-5 space-y-2">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-red-600 bg-red-50 border border-red-100 rounded-xl hover:bg-red-100 hover:border-red-200 transition-all">
                            <i class="bi bi-box-arrow-right text-xs"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const toggle = document.getElementById('mobileToggle');
        const menu = document.getElementById('mobileMenu');
        const icon = document.getElementById('mobileIcon');
        toggle?.addEventListener('click', () => {
            menu.classList.toggle('hidden');
            icon.className = menu.classList.contains('hidden') ? 'bi bi-list text-xl' : 'bi bi-x-lg text-xl';
        });

        // Close modal with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.getElementById('profileModal')?.classList.add('hidden');
            }
        });
    </script>
    @yield('scripts')
</body>

</html>