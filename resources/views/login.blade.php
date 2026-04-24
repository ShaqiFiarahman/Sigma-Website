<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIGMA Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bs-primary: #19376D; /* Deep Navy */
            --bs-primary-rgb: 25, 55, 109;
            --bs-body-bg: #F8FAFC;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bs-body-bg);
            color: #1E293B;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Modern Buttons */
        .btn {
            font-weight: 600;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }
        .btn-primary { 
            background-color: #19376D !important; 
            border-color: #19376D !important; 
            color: #FFFFFF !important;
            box-shadow: 0 4px 12px rgba(25, 55, 109, 0.2);
        }
        .btn-primary:hover, .btn-primary:active { 
            background-color: #0B2447 !important; 
            border-color: #0B2447 !important; 
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(25, 55, 109, 0.3);
        }
        
        /* Clean Cards */
        .login-card {
            background-color: #FFFFFF !important;
            border: none !important;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            width: 100%;
            max-width: 420px;
        }

        .login-header {
            background: linear-gradient(135deg, #19376D, #2B4C8C);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 1px solid #E2E8F0;
            background-color: #F8FAFC;
        }
        .form-control:focus {
            border-color: #19376D;
            box-shadow: 0 0 0 4px rgba(25, 55, 109, 0.1);
            background-color: #FFFFFF;
        }

        .form-floating > label {
            color: #64748B;
        }

        h1, h2, h3, h4, h5, h6 {
            color: #0F172A;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .text-muted { color: #64748B !important; }
    </style>
</head>

<body>
    <div class="container d-flex justify-content-center">
        <div class="card login-card border-0">
            <div class="login-header">
                <i class="bi bi-shield-shaded fs-1 mb-2"></i>
                <h3 class="fw-bold mb-0">SIGMA</h3>
                <p class="mb-0 opacity-75">Disaster Information System</p>
            </div>
            <div class="card-body p-4 p-md-5">
                <h5 class="text-center mb-4 fw-bold">Login Admin</h5>

                <form action="{{ route('dashboard') }}" method="GET">
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" placeholder="name@example.com"
                            value="admin@sigma.com" required>
                        <label for="email">Email address</label>
                    </div>
                    <div class="form-floating mb-4">
                        <input type="password" class="form-control" id="password" placeholder="Password"
                            value="password" required>
                        <label for="password">Password</label>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="rememberCheck" checked>
                            <label class="form-check-label text-muted" for="rememberCheck">
                                Ingat saya
                            </label>
                        </div>
                    </div>
                    <button class="btn btn-primary w-100 py-2 btn-lg mb-3" type="submit">Login</button>
                    <div class="text-center">
                        <a href="#" class="text-decoration-none small text-muted">Lupa Password?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>