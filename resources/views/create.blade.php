@extends('layout')

@section('title', 'Buat Laporan Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-plus-circle text-primary me-2"></i>Form Pelaporan Bencana</h5>
            </div>
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('laporan.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="judul" class="form-label fw-semibold">Judul Laporan <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control" id="judul" placeholder="Contoh: Banjir bandang di Perumahan Indah" required>
                    </div>

                    <div class="mb-4">
                        <label for="lokasi" class="form-label fw-semibold">Lokasi Kejadian <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-geo-alt text-danger"></i></span>
                            <input type="text" name="lokasi" class="form-control" id="lokasi" placeholder="Contoh: Jl. Merdeka No. 10, Jakarta" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="tingkat_bencana" class="form-label fw-semibold">Tingkat Bencana <span class="text-danger">*</span></label>
                        <select name="tingkat_bencana" id="tingkat_bencana" class="form-select" required>
                            <option value="">Pilih tingkat darurat...</option>
                            <option value="Awas">Awas (Tertinggi/Kritis)</option>
                            <option value="Siaga 1">Siaga 1 (Sangat Bahaya)</option>
                            <option value="Siaga 2">Siaga 2 (Bahaya)</option>
                            <option value="Waspada">Waspada (Potensi Bahaya)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="deskripsi" class="form-label fw-semibold">Deskripsi Kejadian <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" class="form-control" id="deskripsi" rows="6" placeholder="Ceritakan detail kejadian secara singkat dan jelas..." required></textarea>
                        <div class="form-text">Pastikan deskripsi mencakup situasi terkini, jumlah korban jiwa jika ada, dan bantuan yang paling dibutuhkan.</div>
                    </div>

                    <div class="mb-4">
                        <label for="foto" class="form-label fw-semibold">Dokumentasi Foto (Opsional)</label>
                        <input class="form-control" type="file" id="foto" accept="image/*">
                        <div class="form-text">Unggah foto situasi di lapangan (maks. 5MB)</div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-3">
                        <a href="{{ route('dashboard') }}" class="btn btn-light px-4 fw-semibold text-muted">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">
                            <i class="bi bi-send-fill me-2"></i>Kirim Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
