<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGMA Admin - @yield('title')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bs-primary: #19376D; /* Deep Navy */
            --bs-primary-rgb: 25, 55, 109;
            --bs-font-sans-serif: 'Plus Jakarta Sans', sans-serif;
            --bs-body-font-family: 'Plus Jakarta Sans', sans-serif;
            --bs-body-bg: #F8FAFC; /* Clean off-white background */
            --bs-body-color: #1E293B; /* Slate dark text */
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bs-body-bg);
            color: var(--bs-body-color);
        }

        /* Clean Cards */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        /* Primary Elements */
        .text-primary { color: #19376D !important; }
        .bg-primary { background-color: #19376D !important; }
        
        /* Modern Buttons */
        .btn {
            font-weight: 600;
            border-radius: 10px;
            padding: 0.6rem 1.5rem;
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

        /* Navbar */
        .navbar-custom {
            background-color: #FFFFFF;
            border-bottom: 1px solid rgba(0,0,0,0.05) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            padding: 16px 24px;
        }

        .navbar-brand {
            font-weight: 700;
            color: #19376D !important;
            letter-spacing: -0.5px;
        }

        .nav-item .nav-link {
            color: #64748B;
            font-weight: 600;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            transition: all 0.3s;
            margin: 0 0.2rem;
        }

        .nav-item .nav-link:hover,
        .nav-item .nav-link.active {
            color: #19376D !important;
            background-color: #F1F5F9;
        }

        /* General layout */
        .content-area {
            padding: 32px 24px;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .bg-light { background-color: #F8FAFC !important; }
        
        /* Typography overrides */
        h1, h2, h3, h4, h5, h6 {
            color: #0F172A;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        
        .text-muted { color: #64748B !important; }
        .text-dark { color: #0F172A !important; }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg border-bottom navbar-custom sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
                <i class="bi bi-shield-shaded fs-3 me-2"></i>
                <span class="fs-4">SIGMA</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}">
                            <i class="bi bi-grid-1x2-fill me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('laporan') || Route::is('detail') ? 'active' : '' }}"
                            href="{{ route('laporan') }}">
                            <i class="bi bi-file-earmark-text-fill me-1"></i> Data Laporan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('create') ? 'active' : '' }}"
                            href="{{ route('create') }}">
                            <i class="bi bi-plus-circle-fill me-1"></i> Buat Laporan
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center mt-3 mt-lg-0">
                    <span class="me-3 text-muted fw-medium">Admin User</span>
                    <img src="https://ui-avatars.com/api/?name=Admin&background=82A2E7&color=fff&rounded=true"
                        alt="Admin" width="36" height="36" class="rounded-circle me-3 border">
                    <a href="{{ route('login') }}" class="btn btn-outline-danger btn-sm px-3 rounded-pill">
                        <i class="bi bi-box-arrow-left me-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <div class="bg-light flex-grow-1" style="min-height: calc(100vh - 76px);">
        <!-- Content -->
        <div class="container-fluid content-area">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 fw-bold text-dark">@yield('title')</h4>
            </div>
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>