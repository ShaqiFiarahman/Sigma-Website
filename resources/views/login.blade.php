<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SIGMA Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-color: #F8FAFC;
            background-image: 
                radial-gradient(#e2e8f0 1.5px, transparent 1.5px), 
                radial-gradient(#e2e8f0 1.5px, transparent 1.5px);
            background-size: 24px 24px;
            background-position: 0 0, 12px 12px;
            background-attachment: fixed;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-5 font-sans relative">

    {{-- Main Container --}}
    <div class="w-full max-w-[420px] bg-white rounded-3xl p-8 sm:p-10 shadow-xl relative z-10"
         style="box-shadow: 0 20px 40px rgba(10,15,30,0.05), 0 1px 3px rgba(10,15,30,0.05); border: 1px solid rgba(255,255,255,0.8);">

        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="w-14 h-14 rounded-2xl mx-auto mb-5 flex items-center justify-center shadow-lg"
                 style="background: linear-gradient(135deg, #0A0F1E 0%, #1e3a8a 100%); box-shadow: 0 8px 16px rgba(10,15,30,0.15);">
                <i class="bi bi-shield-check text-white text-2xl"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mb-2">Selamat Datang</h1>
            <p class="text-sm text-slate-500">Login ke SIGMA Admin</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl flex items-start gap-3 bg-red-50 border border-red-100 text-red-700 text-sm">
                <i class="bi bi-exclamation-circle-fill mt-0.5 shrink-0"></i>
                <ul class="space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('login.post') }}" method="POST" id="loginForm" class="space-y-5">
            @csrf

            {{-- Email --}}
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                    Email
                </label>
                <div class="relative">
                    <i class="bi bi-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="email" id="email" name="email" value="admin@sigma.com" required
                        class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 text-sm
                               focus:outline-none focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-500/15 transition-all">
                </div>
            </div>

            {{-- Password --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Password
                    </label>
                    <a href="#" class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline transition-colors">Lupa?</a>
                </div>
                <div class="relative">
                    <i class="bi bi-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="password" id="password" name="password" value="password" required
                        class="w-full pl-11 pr-12 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 text-sm
                               focus:outline-none focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-500/15 transition-all">
                    <button type="button" id="passToggle"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-500 transition-colors">
                        <i class="bi bi-eye-fill" id="passIcon"></i>
                    </button>
                </div>
            </div>

            {{-- Remember Me --}}
            <div class="flex items-center pt-1 pb-2">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" checked class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20 cursor-pointer">
                    <span class="text-sm text-slate-600">Ingat saya di perangkat ini</span>
                </label>
            </div>

            {{-- Submit --}}
            <button type="submit" id="loginBtn"
                class="w-full py-3.5 rounded-xl text-white font-semibold text-sm
                       hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 flex items-center justify-center gap-2"
                style="background: linear-gradient(135deg, #0A0F1E 0%, #1e3a8a 100%); box-shadow: 0 4px 12px rgba(10,15,30,0.2);">
                Masuk <i class="bi bi-arrow-right"></i>
            </button>
        </form>
    </div>

    {{-- Footer Text --}}
    <div class="absolute bottom-6 left-0 w-full text-center pointer-events-none">
        <p class="text-xs text-slate-400 font-medium">SIGMA &copy; {{ date('Y') }} &middot; Tanggap Bencana</p>
    </div>

    <script>
        const passInput  = document.getElementById('password');
        const passToggle = document.getElementById('passToggle');
        const passIcon   = document.getElementById('passIcon');
        passToggle?.addEventListener('click', () => {
            const show = passInput.type === 'password';
            passInput.type = show ? 'text' : 'password';
            passIcon.className = show ? 'bi bi-eye-slash-fill' : 'bi bi-eye-fill';
        });

        document.getElementById('loginForm')?.addEventListener('submit', () => {
            const btn = document.getElementById('loginBtn');
            btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin-fast"></i> Memverifikasi...';
            btn.disabled = true;
            btn.style.opacity = '0.8';
            btn.classList.add('cursor-not-allowed');
        });
    </script>
</body>

</html>