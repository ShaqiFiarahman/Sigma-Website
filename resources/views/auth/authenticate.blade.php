<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGMA — Autentikasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --abyss: #0A0F1E;
            --accent: #3B6FE8;
            --frost: #E4F0F6;
        }
        body {
            background-color: #F8FAFC;
            background-image: radial-gradient(#e2e8f0 1.5px, transparent 1.5px);
            background-size: 24px 24px;
        }
        .auth-card {
            width: 100%;
            max-width: 900px;
            min-height: 580px;
            background: #fff;
            border-radius: 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            display: flex;
        }
        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            transition: all 0.6s ease-in-out;
        }
        .sign-in-container {
            left: 0;
            width: 50%;
            z-index: 2;
        }
        .sign-up-container {
            left: 0;
            width: 50%;
            opacity: 0;
            z-index: 1;
        }
        .auth-card.right-panel-active .sign-in-container {
            transform: translateX(100%);
        }
        .auth-card.right-panel-active .sign-up-container {
            transform: translateX(100%);
            opacity: 1;
            z-index: 5;
            animation: show 0.6s;
        }
        @keyframes show {
            0%, 49.99% { opacity: 0; z-index: 1; }
            50%, 100% { opacity: 1; z-index: 5; }
        }
        .overlay-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: transform 0.6s ease-in-out;
            z-index: 100;
        }
        .auth-card.right-panel-active .overlay-container {
            transform: translateX(-100%);
        }
        .overlay {
            background: linear-gradient(135deg, var(--abyss) 0%, #1e3a8a 100%);
            color: #fff;
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }
        .auth-card.right-panel-active .overlay {
            transform: translateX(50%);
        }
        .overlay-panel {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 40px;
            text-align: center;
            top: 0;
            height: 100%;
            width: 50%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }
        .overlay-left {
            transform: translateX(-20%);
        }
        .auth-card.right-panel-active .overlay-left {
            transform: translateX(0);
        }
        .overlay-right {
            right: 0;
            transform: translateX(0);
        }
        .auth-card.right-panel-active .overlay-right {
            transform: translateX(20%);
        }
        .ghost-btn {
            background-color: transparent;
            border: 2px solid #fff;
            color: #fff;
            padding: 10px 40px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .ghost-btn:hover {
            background: #fff;
            color: var(--abyss);
        }
        .input-group {
            position: relative;
            margin-bottom: 15px;
        }
        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #f1f4f9;
            font-size: 14px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            color: #1e293b;
        }
        .input-group input::placeholder {
            color: #94a3b8;
        }
        .input-group input:focus {
            border-color: var(--accent);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(59, 111, 232, 0.1);
            outline: none;
        }

        .submit-btn {
            background: linear-gradient(135deg, #1e3a8a 0%, var(--accent) 100%);
            box-shadow: 0 4px 12px rgba(59, 111, 232, 0.25);
            color: #fff;
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            margin-top: 10px;
            transition: all 0.3s;
            cursor: pointer;
        }
        .submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(59, 111, 232, 0.35);
        }
        .submit-btn:active {
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .auth-card {
                flex-direction: column;
                min-height: auto;
                max-width: 450px;
                border-radius: 24px;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 16px -6px rgba(0, 0, 0, 0.05);
                border: 1px solid rgba(10, 15, 30, 0.08);
            }
            .overlay-container {
                display: none;
            }
            .sign-in-container, .sign-up-container {
                width: 100%;
                position: relative;
                height: auto;
            }
            .auth-card.right-panel-active .sign-in-container {
                display: none;
            }
            .sign-up-container {
                display: none;
                opacity: 1;
            }
            .auth-card.right-panel-active .sign-up-container {
                display: block;
                transform: none;
            }
            .mobile-toggle {
                display: block !important;
            }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">

    <div class="auth-card" id="authCard">
        {{-- Sign Up --}}
        <div class="form-container sign-up-container">
            <form action="{{ route('register.post') }}" method="POST" class="h-full flex flex-col justify-center px-6 py-10 sm:px-16 bg-white">
                @csrf
                <div class="mb-8 text-center sm:text-left flex flex-col items-center sm:items-start gap-3">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white transition-all duration-300"
                        style="background: linear-gradient(135deg, #0A0F1E 0%, #1e3a8a 100%); box-shadow: 0 4px 12px rgba(10,15,30,0.25);">
                        <i class="bi bi-shield-check text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900 leading-tight">Buat Akun</h2>
                        <p class="text-sm text-slate-500 mt-1">Bergabung dengan SIGMA hari ini.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="input-group">
                        <i class="bi bi-person"></i>
                        <input type="text" name="name" placeholder="Nama Lengkap" required value="{{ old('name') }}">
                    </div>
                    <div class="input-group">
                        <i class="bi bi-envelope"></i>
                        <input type="email" name="email" placeholder="Email" required value="{{ old('email') }}">
                    </div>
                    <div class="input-group">
                        <i class="bi bi-lock"></i>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="input-group">
                        <i class="bi bi-lock-fill"></i>
                        <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Mulai Sekarang</button>
                
                <p class="mobile-toggle hidden mt-6 text-center text-sm text-slate-500">
                    Sudah punya akun? <button type="button" class="text-accent font-bold" onclick="togglePanel()">Masuk</button>
                </p>
            </form>
        </div>

        {{-- Sign In --}}
        <div class="form-container sign-in-container">
            <form action="{{ route('login.post') }}" method="POST" class="h-full flex flex-col justify-center px-6 py-10 sm:px-16 bg-white">
                @csrf
                <div class="mb-8 text-center sm:text-left flex flex-col items-center sm:items-start gap-3">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white transition-all duration-300"
                        style="background: linear-gradient(135deg, #0A0F1E 0%, #1e3a8a 100%); box-shadow: 0 4px 12px rgba(10,15,30,0.25);">
                        <i class="bi bi-shield-check text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900 leading-tight">Selamat Datang</h2>
                        <p class="text-sm text-slate-500 mt-1">Masuk ke SIGMA Dashboard.</p>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-600 text-xs border border-red-100">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-4 p-3 rounded-lg bg-emerald-50 text-emerald-600 text-xs border border-emerald-100">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="space-y-4">
                    <div class="input-group">
                        <i class="bi bi-envelope"></i>
                        <input type="email" name="email" placeholder="Email" required value="{{ old('email') }}">
                    </div>
                    <div class="input-group">
                        <i class="bi bi-lock"></i>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-4 mb-6">
                    <label class="flex items-center gap-2.5 cursor-pointer group select-none">
                        <div class="relative w-4 h-4 flex items-center justify-center">
                            <input type="checkbox" name="remember" class="peer sr-only">
                            <div class="absolute inset-0 rounded-md border border-slate-200 bg-slate-50 transition-all duration-200 peer-checked:bg-blue-600 peer-checked:border-blue-600 group-hover:border-slate-300 shadow-sm"></div>
                            <i class="bi bi-check text-white text-[14px] leading-none z-10 scale-0 peer-checked:scale-100 transition-transform duration-200"></i>
                        </div>
                        <span class="text-xs text-slate-500 font-medium group-hover:text-slate-600 transition-colors">Ingat saya</span>
                    </label>
                    <a href="#" class="text-xs text-accent font-semibold hover:underline">Lupa Password?</a>
                </div>

                <button type="submit" class="submit-btn">Masuk Sekarang</button>




                <p class="mobile-toggle hidden mt-6 text-center text-sm text-slate-500">
                    Belum punya akun? <button type="button" class="text-accent font-bold" onclick="togglePanel()">Daftar</button>
                </p>
            </form>
        </div>

        {{-- Overlay --}}
        <div class="overlay-container">
            <div class="overlay">
                <!-- Premium Background Patterns -->
                <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(white 1.5px, transparent 1.5px); background-size: 24px 24px;"></div>
                <svg class="absolute inset-0 w-full h-full opacity-[0.05] pointer-events-none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" preserveAspectRatio="none">
                    <path d="M-100,100 C200,50 300,250 500,100 C700,0 800,200 900,100" fill="none" stroke="white" stroke-width="2" />
                    <path d="M-100,150 C200,100 300,300 500,150 C700,50 800,250 900,150" fill="none" stroke="white" stroke-width="1.8" />
                    <path d="M-100,200 C200,150 300,350 500,200 C700,100 800,300 900,200" fill="none" stroke="white" stroke-width="1.5" />
                    <path d="M-100,250 C200,200 300,400 500,250 C700,150 800,350 900,250" fill="none" stroke="white" stroke-width="1.2" />
                    <path d="M-100,300 C200,250 300,450 500,300 C700,200 800,400 900,300" fill="none" stroke="white" stroke-width="0.9" />
                    <path d="M-100,350 C200,300 300,500 500,350 C700,250 800,450 900,350" fill="none" stroke="white" stroke-width="0.6" />
                </svg>

                <div class="overlay-panel overlay-left px-12">
                    <div class="flex flex-col items-center justify-center -mt-8">
                        <div class="text-white mb-5 transition-transform duration-300 hover:scale-105">
                            <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                                <polyline points="10 17 15 12 10 7" />
                                <line x1="15" y1="12" x2="3" y2="12" />
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold tracking-tight text-white mb-2">Sudah Punya Akun?</h1>
                        <p class="text-xs text-white/60 mb-5 max-w-xs leading-relaxed">Masuk ke akun Anda untuk melanjutkan pemantauan bencana secara real-time.</p>
                        <button class="ghost-btn" id="signIn">Masuk</button>
                    </div>
                </div>
                <div class="overlay-panel overlay-right px-12">
                    <div class="flex flex-col items-center justify-center -mt-8">
                        <div class="text-white mb-5 transition-transform duration-300 hover:scale-105">
                            <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <line x1="19" y1="8" x2="19" y2="14" />
                                <line x1="16" y1="11" x2="22" y2="11" />
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold tracking-tight text-white mb-2">Belum Bergabung?</h1>
                        <p class="text-xs text-white/60 mb-5 max-w-xs leading-relaxed">Daftarkan diri Anda sekarang untuk menjadi bagian dari sistem informasi tanggap bencana SIGMA.</p>
                        <button class="ghost-btn" id="signUp">Mulai Sekarang</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const signUpButton = document.getElementById('signUp');
        const signInButton = document.getElementById('signIn');
        const container = document.getElementById('authCard');

        signUpButton.addEventListener('click', () => {
            container.classList.add("right-panel-active");
        });

        signInButton.addEventListener('click', () => {
            container.classList.remove("right-panel-active");
        });

        function togglePanel() {
            container.classList.toggle("right-panel-active");
        }
    </script>
</body>
</html>
