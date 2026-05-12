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
            background: #f8fafc;
            font-size: 14px;
            transition: all 0.3s;
        }
        .input-group input:focus {
            border-color: var(--accent);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(59, 111, 232, 0.1);
            outline: none;
        }
        .submit-btn {
            background: linear-gradient(135deg, var(--abyss) 0%, #1e3a8a 100%);
            color: #fff;
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            margin-top: 10px;
            transition: all 0.3s;
        }
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(10, 15, 30, 0.2);
        }

        @media (max-width: 768px) {
            .auth-card {
                flex-direction: column;
                min-height: 700px;
                max-width: 450px;
            }
            .overlay-container {
                display: none;
            }
            .sign-in-container, .sign-up-container {
                width: 100%;
                position: relative;
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
            <form action="{{ route('register.post') }}" method="POST" class="h-full flex flex-col justify-center px-10 sm:px-16 bg-white">
                @csrf
                <div class="mb-8 text-center sm:text-left">
                    <h2 class="text-2xl font-bold text-slate-900">Buat Akun</h2>
                    <p class="text-sm text-slate-500 mt-1">Bergabung dengan SIGMA hari ini.</p>
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

                <button type="submit" class="submit-btn">DAFTAR SEKARANG</button>
                
                <p class="mobile-toggle hidden mt-6 text-center text-sm text-slate-500">
                    Sudah punya akun? <button type="button" class="text-accent font-bold" onclick="togglePanel()">Masuk</button>
                </p>
            </form>
        </div>

        {{-- Sign In --}}
        <div class="form-container sign-in-container">
            <form action="{{ route('login.post') }}" method="POST" class="h-full flex flex-col justify-center px-10 sm:px-16 bg-white">
                @csrf
                <div class="mb-8 text-center sm:text-left">
                    <h2 class="text-2xl font-bold text-slate-900">Selamat Datang</h2>
                    <p class="text-sm text-slate-500 mt-1">Masuk ke SIGMA Dashboard.</p>
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
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-accent">
                        <span class="text-xs text-slate-500">Ingat saya</span>
                    </label>
                    <a href="#" class="text-xs text-accent font-semibold hover:underline">Lupa Password?</a>
                </div>

                <button type="submit" class="submit-btn">MASUK SEKARANG</button>

                <p class="mobile-toggle hidden mt-6 text-center text-sm text-slate-500">
                    Belum punya akun? <button type="button" class="text-accent font-bold" onclick="togglePanel()">Daftar</button>
                </p>
            </form>
        </div>

        {{-- Overlay --}}
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mb-6">
                        <i class="bi bi-shield-check text-white text-3xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold mb-4">Sudah Punya Akun?</h1>
                    <p class="text-sm text-white/70 mb-8 leading-relaxed">Masuk ke akun Anda untuk melanjutkan pemantauan bencana secara real-time.</p>
                    <button class="ghost-btn" id="signIn">MASUK</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mb-6">
                        <i class="bi bi-person-plus text-white text-3xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold mb-4">Belum Bergabung?</h1>
                    <p class="text-sm text-white/70 mb-8 leading-relaxed">Daftarkan diri Anda sekarang untuk menjadi bagian dari sistem informasi tanggap bencana SIGMA.</p>
                    <button class="ghost-btn" id="signUp">DAFTAR</button>
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
