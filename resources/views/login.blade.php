<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SIGMA Admin</title>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 flex items-center justify-center p-5"
      style="background-image: radial-gradient(#19376D 0.5px, transparent 0.5px), radial-gradient(#19376D 0.5px, #F1F5F9 0.5px);
             background-size: 20px 20px;
             background-position: 0 0, 10px 10px;
             background-attachment: fixed;">

    <div class="w-full max-w-[960px] bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-row my-5
                relative z-10">

        {{-- ════════════ LEFT — BRANDING ════════════ --}}
        <div class="flex-1 bg-gradient-to-br from-[#19376D] to-[#0B2447] flex flex-col justify-center
                    items-center text-center px-10 py-16 relative overflow-hidden">
            {{-- Decorative circles --}}
            <div class="absolute w-72 h-72 rounded-full bg-white/5 -top-24 -left-24
                        transition-transform duration-500 group-hover:scale-110"></div>
            <div class="absolute w-96 h-96 rounded-full bg-white/5 -bottom-36 -right-36"></div>

            {{-- Icon --}}
            <div class="relative z-10 w-24 h-24 rounded-3xl bg-white/10 border border-white/20
                        backdrop-blur-sm shadow-xl flex items-center justify-center mb-6">
                <i class="bi bi-shield-check text-5xl text-white"></i>
            </div>

            {{-- Text --}}
            <h1 class="relative z-10 text-5xl font-extrabold tracking-tighter text-white mb-2">SIGMA</h1>
            <p class="relative z-10 text-white/80 text-base font-medium">Sistem Informasi Tanggap Bencana</p>
        </div>

        {{-- ════════════ RIGHT — FORM ════════════ --}}
        <div class="flex-1 flex flex-col justify-center px-16 py-[70px] bg-white max-sm:px-8 max-sm:py-10">

            <h2 class="text-[1.75rem] font-extrabold text-slate-900 tracking-tight mb-1">Selamat Datang</h2>
            <p class="text-slate-500 text-sm mb-10">Silakan masuk menggunakan akun admin Anda.</p>

            <form action="{{ route('dashboard') }}" method="GET" id="loginForm">

                {{-- Email --}}
                <div class="mb-5">
                    <div class="relative">
                        <input type="email" id="email" name="email"
                               value="admin@sigma.com" required
                               placeholder=" "
                               class="peer w-full px-5 pt-5 pb-2.5 rounded-xl border-[1.5px] border-slate-200
                                      bg-slate-50 text-slate-900 text-sm font-medium
                                      focus:outline-none focus:border-[#19376D] focus:bg-white
                                      focus:shadow-[0_0_0_4px_rgba(25,55,109,0.1)]
                                      transition-all duration-200">
                        <label for="email"
                               class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-medium
                                      pointer-events-none transition-all duration-200
                                      peer-focus:top-3.5 peer-focus:text-xs peer-focus:text-[#19376D] peer-focus:translate-y-0
                                      peer-[:not(:placeholder-shown)]:top-3.5 peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:translate-y-0">
                            <i class="bi bi-envelope me-1"></i> Alamat Email
                        </label>
                    </div>
                </div>

                {{-- Password --}}
                <div class="mb-5">
                    <div class="relative">
                        <input type="password" id="password" name="password"
                               value="password" required
                               placeholder=" "
                               class="peer w-full px-5 pt-5 pb-2.5 pr-12 rounded-xl border-[1.5px] border-slate-200
                                      bg-slate-50 text-slate-900 text-sm font-medium
                                      focus:outline-none focus:border-[#19376D] focus:bg-white
                                      focus:shadow-[0_0_0_4px_rgba(25,55,109,0.1)]
                                      transition-all duration-200">
                        <label for="password"
                               class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-medium
                                      pointer-events-none transition-all duration-200
                                      peer-focus:top-3.5 peer-focus:text-xs peer-focus:text-[#19376D] peer-focus:translate-y-0
                                      peer-[:not(:placeholder-shown)]:top-3.5 peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:translate-y-0">
                            <i class="bi bi-lock me-1"></i> Password
                        </label>
                        {{-- Eye toggle --}}
                        <button type="button" id="passToggle"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-[#19376D] transition-colors">
                            <i class="bi bi-eye-fill" id="passIcon"></i>
                        </button>
                    </div>
                </div>

                {{-- Remember + Forgot --}}
                <div class="flex items-center justify-between mb-8">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" checked
                               class="w-4 h-4 rounded accent-[#19376D] cursor-pointer">
                        <span class="text-sm text-slate-500">Ingat saya</span>
                    </label>
                    <a href="#" class="text-sm font-semibold text-[#19376D] hover:text-[#0B2447]
                                      hover:underline transition-colors">Lupa password?</a>
                </div>

                {{-- Submit --}}
                <button type="submit" id="loginBtn"
                        class="w-full py-3.5 rounded-xl bg-[#19376D] text-white font-semibold text-base
                               shadow-[0_4px_12px_rgba(25,55,109,0.25)]
                               hover:bg-[#0B2447] hover:-translate-y-0.5
                               hover:shadow-[0_8px_16px_rgba(25,55,109,0.3)]
                               active:translate-y-0 transition-all duration-200 flex items-center justify-center gap-2">
                    Masuk <i class="bi bi-box-arrow-in-right"></i>
                </button>
            </form>
        </div>
    </div>

    <script>
        // Password toggle
        const passInput  = document.getElementById('password');
        const passToggle = document.getElementById('passToggle');
        const passIcon   = document.getElementById('passIcon');
        passToggle?.addEventListener('click', () => {
            const show = passInput.type === 'password';
            passInput.type = show ? 'text' : 'password';
            passIcon.className = show ? 'bi bi-eye-slash-fill' : 'bi bi-eye-fill';
        });

        // Loading state
        document.getElementById('loginForm')?.addEventListener('submit', () => {
            const btn = document.getElementById('loginBtn');
            btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin-fast"></i> Memverifikasi...';
            btn.disabled = true;
            btn.style.opacity = '0.75';
        });
    </script>
</body>
</html>