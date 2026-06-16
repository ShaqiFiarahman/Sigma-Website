<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google-site-verification" content="3W_tHhKZuwz5ezm514XgrspY6bgIn7p5cGe1aui6dhI" />
    @if(trim(View::yieldContent('title')) === 'SIGMA')
        <title>SIGMA</title>
    @else
        <title>@yield('title') | SIGMA - Sistem Informasi Mitigasi Bencana</title>
    @endif
    <meta name="description"
        content="@yield('meta_description', 'SIGMA (Sistem Informasi Gawat Darurat dan Mitigasi Bencana) - Pantau peta bencana real-time, cari info posko shelter terdekat, dan kirim laporan darurat di sekitar Anda.')">
    <meta name="keywords"
        content="SIGMA, mitigasi bencana, tanggap darurat, peta bencana, posko evakuasi, shelter bencana, info bencana indonesia">
    <link rel="preload" href="{{ Vite::asset('resources/fonts/PlusJakartaSans-Regular.ttf') }}" as="font"
        type="font/ttf" crossorigin>
    <link rel="preload" href="{{ Vite::asset('resources/fonts/PlusJakartaSans-Bold.ttf') }}" as="font" type="font/ttf"
        crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://maps.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://maps.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"
        media="print" onload="this.media='all'">
    <noscript>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    </noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="text-slate-900 min-h-screen flex flex-col font-sans selection:bg-blue-100 selection:text-blue-900">

    {{-- Elemen Latar Belakang Ambient (Hanya visual belakang, tidak menghalangi interaksi) --}}
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <!-- Orb 1 (Biru-Indigo lembut di kanan atas) -->
        <div
            class="absolute -top-[10%] -right-[10%] w-[55vw] h-[55vw] max-w-[650px] max-h-[650px] rounded-full bg-gradient-to-br from-blue-400/15 to-indigo-500/15 blur-[120px] animate-ambient-slow">
        </div>

        <!-- Orb 2 (Teal-Sky lembut di tengah kiri) -->
        <div
            class="absolute top-[30%] -left-[15%] w-[50vw] h-[50vw] max-w-[550px] max-h-[550px] rounded-full bg-gradient-to-tr from-teal-400/10 to-sky-400/10 blur-[100px] animate-ambient-slower">
        </div>

        <!-- Orb 3 (Biru Muda/Cyan di bawah kanan) -->
        <div
            class="absolute bottom-[5%] right-[10%] w-[45vw] h-[45vw] max-w-[500px] max-h-[500px] rounded-full bg-gradient-to-tr from-sky-300/10 to-teal-400/10 blur-[110px] animate-ambient-slow">
        </div>
    </div>

    {{-- NAVBAR --}}
    <nav id="mainNavbar" class="sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">

            <div class="flex items-center gap-8">
                {{-- Brand --}}
                <a href="{{ auth()->check() && strtolower(auth()->user()->role) === 'admin' ? route('admin.dashboard') : route('dashboard') }}"
                    class="flex items-center gap-2.5 shrink-0 group">
                    <div
                        class="brand-mark w-10 h-10 rounded-lg flex items-center justify-center text-white transition-all duration-300 group-hover:scale-105 group-hover:shadow-lg">
                        <i class="bi bi-shield-check text-base"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold tracking-tight text-slate-900 text-lg leading-none">SIGMA</span>
                        <span class="text-[10px] text-slate-500 font-medium leading-tight hidden sm:block">Sistem
                            Informasi Gawat
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
                @auth
                    @if(strtolower(auth()->user()->role) === 'admin')
                        {{-- Notifikasi --}}
                        @include('admin.dashboard._notification')
                        <button type="button" onclick="window.location.href='{{ route('admin.laporan') }}'"
                            class="btn-primary hidden md:inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white rounded-lg transition-all duration-200 cursor-pointer">
                            <i class="bi bi-file-earmark-text mr-1.5 text-xs"></i> Kelola Laporan
                        </button>
                    @else
                        {{-- Notifikasi Relawan --}}
                        @include('volunteer._notification')
                        <button type="button" onclick="window.location.href='{{ route('laporan.create') }}'"
                            class="btn-primary hidden md:inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white rounded-lg transition-all duration-200 cursor-pointer">
                            <i class="bi bi-plus-lg mr-1.5 text-xs"></i> Buat Laporan
                        </button>
                    @endif
                @else
                    <button type="button" onclick="window.location.href='{{ route('laporan.create') }}'"
                        class="btn-primary hidden md:inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white rounded-lg transition-all duration-200 cursor-pointer">
                        <i class="bi bi-plus-lg mr-1.5 text-xs"></i> Buat Laporan
                    </button>
                @endauth

                {{-- User profile (clickable to toggle dropdown) --}}
                @auth
                    <div class="relative pl-2 md:pl-4 border-l border-slate-200">
                        <button type="button" id="profileDropdownBtn" aria-expanded="false" aria-haspopup="true"
                            class="flex items-center gap-3 cursor-pointer group focus:outline-none">
                            <div class="text-right hidden md:block">
                                <p
                                    class="text-sm font-semibold text-slate-900 leading-none group-hover:text-blue-800 transition-colors">
                                    {{ auth()->user()->short_name }}</p>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">
                                    {{ auth()->user()->role }}
                                </p>
                            </div>
                            <div class="profile-trigger-avatar w-11 h-11 rounded-full flex items-center justify-center text-white text-base font-bold transition-all duration-300 group-hover:scale-105"
                                style="background: linear-gradient(135deg, #0A0F1E 0%, #1e3a8a 100%);">
                                {{ substr(auth()->user()->full_name ?? 'U', 0, 1) }}
                            </div>
                        </button>

                        {{-- Profile Dropdown --}}
                        <x-profile-dropdown />
                    </div>
                @else
                    <div class="relative pl-2 md:pl-4 border-l border-slate-200">
                        <a href="{{ route('login') }}"
                            class="flex items-center gap-3 cursor-pointer group focus:outline-none">
                            <div class="text-right hidden md:block">
                                <p
                                    class="text-sm font-semibold text-slate-900 leading-none group-hover:text-blue-800 transition-colors">
                                    Login</p>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">
                                    Masyarakat
                                </p>
                            </div>
                            <div class="profile-trigger-avatar w-11 h-11 rounded-full flex items-center justify-center text-white transition-all duration-300 group-hover:scale-105"
                                style="background: linear-gradient(135deg, #0A0F1E 0%, #1e3a8a 100%);">
                                <i class="bi bi-person-fill text-base"></i>
                            </div>
                        </a>
                    </div>
                @endauth

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
            <a href="{{ auth()->check() && strtolower(auth()->user()->role) === 'admin' ? route('admin.dashboard') : route('dashboard') }}"
                class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium {{ Route::is('dashboard') || Route::is('admin.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-slate-600' }}">
                <i class="bi bi-grid-1x2"></i>Dashboard
            </a>
            <a href="{{ route('laporan.index') }}"
                class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium {{ Route::is('laporan.index') || Route::is('laporan.show') ? 'bg-blue-50 text-blue-700' : 'text-slate-600' }}">
                <i class="bi bi-file-earmark-text"></i>Laporan
            </a>
            <a href="{{ route('laporan.create') }}"
                class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium {{ Route::is('laporan.create') ? 'bg-blue-50 text-blue-700' : 'text-slate-600' }}">
                <i
                    class="bi bi-plus-circle"></i>{{ auth()->check() && strtolower(auth()->user()->role) === 'admin' ? 'Kelola Laporan' : 'Buat Laporan' }}
            </a>
            <div class="border-t border-slate-100 mt-2 pt-2">
                @auth
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full text-left flex items-center gap-2 px-3 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg">
                            <i class="bi bi-box-arrow-right"></i>Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                        class="w-full text-left flex items-center gap-2 px-3 py-2.5 text-sm font-medium text-blue-600 hover:bg-blue-50 rounded-lg">
                        <i class="bi bi-arrow-right"></i>Login
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-16 animate-fade-up">

        {{-- Page Header --}}
        @if(!Route::is('dashboard') && !Route::is('admin.dashboard') && !Route::is('volunteer.dashboard'))
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

    {{-- Toast Notification Container (Below navbar on the right) --}}
    <div id="disaster-toast-container"
        class="fixed top-24 right-6 z-[9999] flex flex-col gap-3 pointer-events-none w-[calc(100%-2rem)] max-w-sm sm:w-80">
    </div>

    {{-- Reusable Toast HTML Template --}}
    <x-disaster-toast />

    <script>
        window.userRole = "{{ auth()->check() ? strtolower(auth()->user()->role) : '' }}";
    </script>
    @yield('scripts')
</body>

</html>