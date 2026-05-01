<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIGMA Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Local Fonts -->
    <style>
        @font-face {
            font-family: 'Plus Jakarta Sans';
            src: url('{{ asset('fonts/PlusJakartaSans-Regular.ttf') }}') format('truetype');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'Plus Jakarta Sans';
            src: url('{{ asset('fonts/PlusJakartaSans-Medium.ttf') }}') format('truetype');
            font-weight: 500;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'Plus Jakarta Sans';
            src: url('{{ asset('fonts/PlusJakartaSans-SemiBold.ttf') }}') format('truetype');
            font-weight: 600;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'Plus Jakarta Sans';
            src: url('{{ asset('fonts/PlusJakartaSans-Bold.ttf') }}') format('truetype');
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }

        :root {
            --bs-primary: #19376D;
            --bs-primary-dark: #0B2447;
            --bs-body-bg: #F8FAFC;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bs-body-bg);
            color: #1E293B;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            /* Subtle background pattern */
            background-image: radial-gradient(#19376D 0.5px, transparent 0.5px), radial-gradient(#19376D 0.5px, #F8FAFC 0.5px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
            background-attachment: fixed;
            background-color: #F8FAFC;
            opacity: 0.99;
        }

        .login-wrapper {
            width: 100%;
            max-width: 1000px;
            background: #FFFFFF;
            border-radius: 24px;
            box-shadow: 0 24px 48px rgba(25, 55, 109, 0.08), 0 8px 16px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            display: flex;
            flex-direction: row;
            margin: 20px;
            position: relative;
            z-index: 10;
        }

        /* Branding Side */
        .login-branding {
            flex: 1;
            background: linear-gradient(135deg, var(--bs-primary), var(--bs-primary-dark));
            color: white;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        /* Decorative circles */
        .login-branding::before,
        .login-branding::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            transition: transform 0.5s ease;
        }

        .login-branding::before {
            width: 300px;
            height: 300px;
            top: -100px;
            left: -100px;
        }

        .login-branding::after {
            width: 400px;
            height: 400px;
            bottom: -150px;
            right: -150px;
        }

        .login-wrapper:hover .login-branding::before {
            transform: scale(1.1);
        }

        .login-wrapper:hover .login-branding::after {
            transform: scale(1.05);
        }

        .brand-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 24px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            z-index: 1;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .brand-title {
            font-size: 2.75rem;
            font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 0.5rem;
            z-index: 1;
        }

        .brand-subtitle {
            font-size: 1.1rem;
            opacity: 0.85;
            font-weight: 500;
            z-index: 1;
        }

        /* Form Side */
        .login-form-container {
            flex: 1;
            padding: 70px 80px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #FFFFFF;
        }

        .login-heading {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0F172A;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .login-subheading {
            color: #64748B;
            margin-bottom: 2.5rem;
            font-size: 0.95rem;
        }

        .form-control {
            border-radius: 12px;
            padding: 0.8rem 1.2rem;
            border: 1.5px solid #E2E8F0;
            background-color: #F8FAFC;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 4px rgba(25, 55, 109, 0.1);
            background-color: #FFFFFF;
        }

        .form-floating>label {
            color: #64748B;
            padding-left: 1.2rem;
            font-weight: 500;
        }

        .form-floating>.form-control:focus~label,
        .form-floating>.form-control:not(:placeholder-shown)~label {
            transform: scale(0.85) translateY(-0.7rem) translateX(0.15rem);
            color: var(--bs-primary);
            background: #ffffff;
            padding: 0 5px;
            height: auto;
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
            color: #FFFFFF !important;
            font-weight: 600;
            border-radius: 12px;
            padding: 0.85rem 1.5rem;
            font-size: 1.05rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(25, 55, 109, 0.2);
        }

        .btn-primary:hover,
        .btn-primary:active {
            background-color: var(--bs-primary-dark) !important;
            border-color: var(--bs-primary-dark) !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(25, 55, 109, 0.3);
        }

        .form-check-input {
            cursor: pointer;
            border-color: #CBD5E1;
        }

        .form-check-input:checked {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
        }

        .forgot-link {
            color: var(--bs-primary);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: var(--bs-primary-dark);
            text-decoration: underline;
        }

        @media (max-width: 992px) {
            .login-form-container {
                padding: 50px 40px;
            }
        }

        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
                max-width: 450px;
            }

            .login-branding {
                padding: 40px 20px;
            }

            .login-form-container {
                padding: 40px 30px;
            }

            .brand-icon {
                width: 80px;
                height: 80px;
                font-size: 3rem;
            }

            body {
                background-image: none;
                padding: 20px 0;
            }
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <!-- Left Branding Side -->
        <div class="login-branding">
            <div class="brand-icon">
                <i class="bi bi-shield-check"></i>
            </div>
            <h1 class="brand-title">SIGMA</h1>
            <p class="brand-subtitle">Sistem Informasi Tanggap Bencana</p>
        </div>

        <!-- Right Form Side -->
        <div class="login-form-container">
            <h2 class="login-heading">Selamat Datang</h2>
            <p class="login-subheading">Silakan masuk menggunakan akun admin Anda.</p>

            <form action="{{ route('dashboard') }}" method="GET">
                <div class="form-floating mb-4">
                    <input type="email" class="form-control" id="email" placeholder="name@example.com"
                        value="admin@sigma.com" required>
                    <label for="email"><i class="bi bi-envelope me-2"></i>Alamat Email</label>
                </div>
                <div class="form-floating mb-4">
                    <input type="password" class="form-control" id="password" placeholder="Password" value="password"
                        required>
                    <label for="password"><i class="bi bi-lock me-2"></i>Password</label>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="rememberCheck" checked>
                        <label class="form-check-label text-muted" style="font-size: 0.9rem;" for="rememberCheck">
                            Ingat saya
                        </label>
                    </div>
                    <a href="#" class="forgot-link" style="font-size: 0.9rem;">Lupa password?</a>
                </div>

                <button class="btn btn-primary w-100 mb-2" type="submit">
                    Masuk <i class="bi bi-box-arrow-in-right ms-2"></i>
                </button>
            </form>
        </div>
    </div>
</body>

</html>