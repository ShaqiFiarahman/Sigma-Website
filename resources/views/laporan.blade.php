@extends('layout')

@section('title', 'Data Laporan')

@section('content')
    @if(session('msg') == 'approved')
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Laporan berhasil di-approve.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif(session('msg') == 'rejected')
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-x-circle-fill me-2"></i> Laporan berhasil di-reject.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif(session('msg') == 'created')
        <div class="alert alert-info alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i> Laporan baru berhasil dibuat.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Daftar Laporan Bencana</h5>
            <div class="input-group" style="width: 250px;">
                <input type="text" class="form-control" placeholder="Cari laporan...">
                <button class="btn btn-outline-secondary" type="button"><i class="bi bi-search"></i></button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Judul Laporan</th>
                            <th>Lokasi</th>
                            <th>Tingkat Bencana</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporans as $index => $laporan)
                            <tr>
                                <td class="ps-4">{{ $index + 1 }}</td>
                                <td class="fw-semibold">{{ $laporan['judul'] }}</td>
                                <td class="text-muted"><i class="bi bi-geo-alt me-1 text-danger"></i>{{ $laporan['lokasi'] }}
                                </td>
                                <td>
                                    @if(!empty($laporan['tingkat_bencana']))
                                        @if($laporan['tingkat_bencana'] == 'Awas')
                                            <span class="badge bg-danger">{{ $laporan['tingkat_bencana'] }}</span>
                                        @elseif(str_contains($laporan['tingkat_bencana'], 'Siaga'))
                                            <span class="badge bg-warning text-dark">{{ $laporan['tingkat_bencana'] }}</span>
                                        @else
                                            <span class="badge bg-info text-dark">{{ $laporan['tingkat_bencana'] }}</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-muted"><small>{{ $laporan['tanggal'] }}</small></td>
                                <td>
                                    @if(strtolower($laporan['status']) == 'pending')
                                        <span class="badge bg-warning text-dark px-2 py-1"><i
                                                class="bi bi-hourglass-split me-1"></i>Pending</span>
                                    @elseif(strtolower($laporan['status']) == 'verified')
                                        <span class="badge bg-success px-2 py-1"><i
                                                class="bi bi-check-circle me-1"></i>Verified</span>
                                    @elseif(strtolower($laporan['status']) == 'danger')
                                        <span class="badge bg-danger px-2 py-1"><i class="bi bi-x-circle me-1"></i>Danger</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('detail', ['id' => $laporan['id']]) }}" class="btn btn-sm btn-info text-white">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada data laporan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3">
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm justify-content-end mb-0">
                    <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                    <li class="page-item active" aria-current="page"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">Next</a></li>
                </ul>
            </nav>
        </div>
    </div>
@endsection