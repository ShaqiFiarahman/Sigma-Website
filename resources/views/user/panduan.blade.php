@extends('layouts.app')
@section('title', 'Panduan Bencana')

@section('content')
<style>
    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #6650a4;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .back-btn:hover { color: #533f8a; transform: translateX(-2px); }

    .pdf-card {
        background: #FFFFFF;
        border: 1px solid rgba(202, 196, 208, 0.55);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(102, 80, 164, 0.08);
    }

    .pdf-header {
        background: linear-gradient(135deg, #6650a4 0%, #533f8a 55%, #7D5260 100%);
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .pdf-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
    }

    .download-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #6650a4;
        color: white;
        padding: 0.6rem 1.25rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .download-btn:hover {
        background: #533f8a;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 80, 164, 0.3);
    }
</style>

{{-- Back button --}}
<div class="mb-5">
    <a href="{{ route('dashboard') }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
    </a>
</div>

<div class="pdf-card">
    {{-- Header --}}
    <div class="pdf-header">
        <div class="pdf-icon">
            <i class="bi bi-book-fill"></i>
        </div>
        <div class="flex-1 min-w-0">
            <h2 class="text-white font-extrabold text-base leading-tight">Buku Saku Siaga Bencana</h2>
            <p class="text-white/70 text-xs mt-0.5">BNPB 2019 &middot; Panduan Mitigasi Bencana</p>
        </div>
        <a href="/panduan-bencana.pdf" download class="download-btn shrink-0">
            <i class="bi bi-download"></i>
            <span class="hidden sm:inline">Unduh PDF</span>
        </a>
    </div>

    {{-- PDF Viewer --}}
    <div class="w-full" style="height: 80vh; min-height: 600px;">
        <iframe
            src="/panduan-bencana.pdf"
            class="w-full h-full"
            style="border: none;"
            title="Buku Saku Siaga Bencana BNPB 2019">
        </iframe>
    </div>
</div>

{{-- Fallback jika iframe tidak support --}}
<div class="mt-4 text-center text-sm" style="color: #625b71;">
    PDF tidak tampil?
    <a href="/panduan-bencana.pdf" target="_blank" class="font-semibold hover:underline" style="color: #6650a4;">
        Buka di tab baru <i class="bi bi-box-arrow-up-right"></i>
    </a>
</div>

@endsection
