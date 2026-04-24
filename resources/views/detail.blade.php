@extends('layout')

@section('title', 'Detail Laporan')

@section('content')
    <div class="mb-4">
        <a href="{{ route('laporan') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Data Laporan
        </a>
    </div>

    <div class="row">
        <!-- Main Detail -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
                        <h3 class="mb-0 fw-bold">{{ $laporan['judul'] }}</h3>
                        @if(strtolower($laporan['status']) == 'pending')
                        <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill d-flex align-items-center">
                            <i class="bi bi-hourglass-split me-2"></i>Status: Pending
                        </span>
                        @elseif(strtolower($laporan['status']) == 'verified')
                        <span class="badge bg-success px-3 py-2 fs-6 rounded-pill d-flex align-items-center">
                            <i class="bi bi-check-circle me-2"></i>Status: Verified
                        </span>
                        @elseif(strtolower($laporan['status']) == 'danger')
                        <span class="badge bg-danger px-3 py-2 fs-6 rounded-pill d-flex align-items-center">
                            <i class="bi bi-x-circle me-2"></i>Status: Danger
                        </span>
                        @endif
                    </div>

                    <div class="position-relative mb-4">
                        <img src="https://akcdn.detik.net.id/community/media/visual/2026/04/15/banjir-sukoharjo-1776224414251_169.jpeg?w=700&q=90"
                            alt="Foto Bencana" class="img-fluid rounded-3 w-100 object-fit-cover shadow-sm"
                            style="max-height: 400px;">
                        <span class="position-absolute bottom-0 start-0 m-3 badge bg-dark opacity-75">Dokumentasi
                            Pelapor</span>
                    </div>

                    <h5 class="fw-bold mt-4 mb-3 border-bottom pb-2">Informasi Laporan</h5>
                    <div class="row mb-4 bg-light rounded-3 p-3 mx-0">
                        <div class="col-sm-6 mb-3 mb-sm-0">
                            <p class="text-muted mb-1 small">Nama Pelapor</p>
                            <p class="mb-0 fw-semibold"><i class="bi bi-person me-2 text-primary"></i>Huda Febri</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-muted mb-1 small">Waktu Laporan</p>
                            <p class="mb-0 fw-semibold"><i class="bi bi-calendar-event me-2 text-primary"></i>{{ $laporan['tanggal'] }}</p>
                        </div>
                        <div class="col-sm-6 mt-3">
                            <p class="text-muted mb-1 small">Tingkat Bencana</p>
                            <p class="mb-0 fw-semibold">
                                @if(!empty($laporan['tingkat_bencana']))
                                    @if($laporan['tingkat_bencana'] == 'Awas')
                                        <span class="badge bg-danger fs-6 px-2 py-1">{{ $laporan['tingkat_bencana'] }}</span>
                                    @elseif(str_contains($laporan['tingkat_bencana'], 'Siaga'))
                                        <span class="badge bg-warning text-dark fs-6 px-2 py-1">{{ $laporan['tingkat_bencana'] }}</span>
                                    @else
                                        <span class="badge bg-info text-dark fs-6 px-2 py-1">{{ $laporan['tingkat_bencana'] }}</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary fs-6 px-2 py-1">Belum Ditetapkan</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-sm-6 mt-3">
                            <p class="text-muted mb-1 small">Lokasi Kejadian</p>
                            <p class="mb-0 fw-semibold"><i class="bi bi-geo-alt me-2 text-danger"></i>{{ $laporan['lokasi'] }}</p>
                        </div>
                    </div>

                    <h5 class="fw-bold mt-4 mb-3 border-bottom pb-2">Deskripsi Kejadian</h5>
                    <p class="text-muted text-justify" style="line-height: 1.8; text-align: justify;">
                        {{ $laporan['deskripsi'] }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Sidebar Action -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-shield-check text-primary me-2"></i>Tindakan Admin</h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small mb-4">Tinjau laporan dengan seksama. Tentukan status dari laporan ini untuk
                        diteruskan ke tim penanganan di lapangan.</p>

                    <div class="d-grid gap-3">
                        @if(strtolower($laporan['status']) == 'pending')
                        <form action="{{ route('laporan.update_status', $laporan['id']) }}" method="POST" class="mb-2">
                            @csrf
                            <input type="hidden" name="status" value="Verified">
                            
                            <div class="mb-3">
                                <label for="tingkat_bencana" class="form-label small fw-semibold text-dark">Tetapkan Tingkat Bencana:</label>
                                <select name="tingkat_bencana" id="tingkat_bencana" class="form-select" required>
                                    <option value="">Pilih tingkat darurat...</option>
                                    <option value="Awas">Awas (Tertinggi/Kritis)</option>
                                    <option value="Siaga 1">Siaga 1 (Sangat Bahaya)</option>
                                    <option value="Siaga 2">Siaga 2 (Bahaya)</option>
                                    <option value="Waspada">Waspada (Potensi Bahaya)</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-success py-3 rounded-3 shadow-sm d-flex justify-content-center align-items-center w-100">
                                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                                <span class="fs-6 fw-semibold">Approve (Setujui)</span>
                            </button>
                        </form>

                        <form action="{{ route('laporan.update_status', $laporan['id']) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="Danger">
                            <button type="submit" class="btn btn-outline-danger py-3 rounded-3 d-flex justify-content-center align-items-center w-100 mt-2">
                                <i class="bi bi-x-circle fs-5 me-2"></i>
                                <span class="fs-6 fw-semibold">Decline (Tolak)</span>
                            </button>
                        </form>
                        @else
                        <div class="alert alert-info text-center mb-0 border-0 shadow-sm">
                            <i class="bi bi-info-circle me-1"></i> Laporan ini sudah diproses ({{ ucfirst($laporan['status']) }}).
                        </div>
                        @endif
                    </div>

                    <div class="mt-4 pt-4 border-top">
                        <h6 class="fw-bold mb-3">Catatan Admin (Opsional)</h6>
                        <textarea class="form-control mb-3" rows="3"
                            placeholder="Tambahkan catatan mengapa laporan ini disetujui/ditolak..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection