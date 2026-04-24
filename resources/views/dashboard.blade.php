@extends('layout')

@section('title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm text-center py-4 bg-white"
                style="border-left: 4px solid #19376D !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-center align-items-center mb-2">
                        <i class="bi bi-file-earmark-text text-primary fs-1 me-3"></i>
                        <h2 class="display-5 fw-bold text-dark mb-0">{{ $total ?? 0 }}</h2>
                    </div>
                    <h5 class="card-title text-muted mb-0">Total Laporan</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm text-center py-4 bg-white"
                style="border-left: 4px solid #ffc107 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-center align-items-center mb-2">
                        <i class="bi bi-hourglass-split text-warning fs-1 me-3"></i>
                        <h2 class="display-5 fw-bold text-dark mb-0">{{ $pending ?? 0 }}</h2>
                    </div>
                    <h5 class="card-title text-muted mb-0">Laporan Pending</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm text-center py-4 bg-white"
                style="border-left: 4px solid #198754 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-center align-items-center mb-2">
                        <i class="bi bi-check-circle text-success fs-1 me-3"></i>
                        <h2 class="display-5 fw-bold text-dark mb-0">{{ $selesai ?? 0 }}</h2>
                    </div>
                    <h5 class="card-title text-muted mb-0">Laporan Selesai</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mt-2">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2 text-primary"></i>Ringkasan Sistem</h5>
        </div>
        <div class="card-body p-4">
            <h5 class="fw-bold">Selamat datang kembali, Admin!</h5>
            <p class="text-muted" style="line-height: 1.8;">
                Anda berada di halaman panel admin <strong>Disaster Information System (SIGMA)</strong>. Sistem ini
                digunakan untuk memantau, mengelola, dan menindaklanjuti laporan bencana alam maupun situasi darurat yang
                dikirimkan oleh masyarakat. Silakan cek menu <strong>Data Laporan</strong> untuk meninjau laporan yang masih
                berstatus pending dan membutuhkan persetujuan segera.
            </p>
            <div class="d-flex gap-2 mt-3">
                <a href="{{ route('laporan') }}" class="btn btn-primary">Lihat Data Laporan <i class="bi bi-arrow-right ms-1"></i></a>
                <a href="{{ route('create') }}" class="btn btn-outline-primary"><i class="bi bi-plus-circle ms-1"></i> Buat Laporan</a>
            </div>
        </div>
    </div>
@endsection