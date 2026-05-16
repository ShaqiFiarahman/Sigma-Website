@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

<style>
    /* Header gradient (selaras dengan admin) */
    .dashboard-header {
        background: linear-gradient(135deg, #0A0F1E 0%, #0f1f4a 50%, #1a3068 100%);
        box-shadow: 0 4px 20px rgba(10, 15, 30, 0.15);
        margin: -1.5rem -1.5rem 0 -1.5rem;
        padding: 2.5rem 1.5rem;
        position: relative;
        overflow: hidden;
        border-radius: 0 0 24px 24px;
    }

    /* Decorative blobs for header */
    .dashboard-header::before {
        content: '';
        position: absolute;
        top: -4rem;
        right: -4rem;
        width: 16rem;
        height: 16rem;
        border-radius: 50%;
        background: radial-gradient(circle, #E4F0F6 0%, transparent 70%);
        opacity: 0.15;
        pointer-events: none;
    }
    
    .dashboard-header::after {
        content: '';
        position: absolute;
        bottom: -2rem;
        left: 20%;
        width: 24rem;
        height: 12rem;
        background: radial-gradient(ellipse, #3B6FE8 0%, transparent 70%);
        opacity: 0.15;
        pointer-events: none;
    }

    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: rgba(228, 240, 246, 0.15);
        color: #E4F0F6;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        padding: 0.35rem 0.8rem;
        border-radius: 999px;
        backdrop-filter: blur(8px);
        border: 1px solid rgba(228, 240, 246, 0.2);
    }

    .menu-card {
        background: #FFFFFF;
        border: 1px solid rgba(10, 15, 30, 0.06);
        border-radius: 20px;
        padding: 1.5rem 1rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        min-height: 160px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(10, 15, 30, 0.04);
    }
    .menu-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(59, 111, 232, 0) 0%, rgba(59, 111, 232, 0.05) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .menu-card:hover {
        transform: translateY(-4px);
        border-color: rgba(59, 111, 232, 0.4);
        box-shadow: 0 12px 24px rgba(59, 111, 232, 0.12);
    }
    .menu-card:hover::before { opacity: 1; }
    .menu-card > * { position: relative; z-index: 1; }

    .menu-icon-wrap {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        background: linear-gradient(135deg, #E4F0F6 0%, #C8DFF0 100%);
        color: #0A0F1E;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        margin-bottom: 1.25rem;
        transition: all 0.3s ease;
    }
    .menu-card:hover .menu-icon-wrap {
        background: linear-gradient(135deg, #1e3a8a 0%, #3B6FE8 100%);
        color: #FFFFFF;
        transform: scale(1.05);
        box-shadow: 0 8px 16px rgba(59, 111, 232, 0.25);
    }

    .news-card {
        min-width: 280px;
        max-width: 280px;
        height: 145px;
        border-radius: 20px;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        flex-shrink: 0;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 1px solid rgba(10, 15, 30, 0.06);
    }
    .news-card:hover { 
        transform: translateY(-3px); 
        box-shadow: 0 8px 16px rgba(10, 15, 30, 0.08);
    }
    
    .news-info    { background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); }
    .news-danger  { background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border-color: #fecaca; }
    .news-warning { background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-color: #fde68a; }

    .news-badge {
        align-self: flex-start;
        font-size: 0.625rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
    }
    .news-info .news-badge { background: #e0f2fe; color: #0369a1; }
    .news-danger .news-badge { background: #fee2e2; color: #b91c1c; }
    .news-warning .news-badge { background: #fef3c7; color: #b45309; }

    .news-scroll {
        display: flex;
        gap: 1.25rem;
        overflow-x: auto;
        padding: 0.5rem 0.5rem 1rem 0.5rem;
        margin: 0 -0.5rem;
        scroll-snap-type: x mandatory;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }
    .news-scroll::-webkit-scrollbar { height: 6px; }
    .news-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    .news-scroll .news-card { scroll-snap-align: start; }

    .warning-banner {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        color: #b91c1c;
        border-radius: 16px;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        border: 1px solid #fca5a5;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.1);
    }

    .fab-emergency {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        z-index: 40;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: #FFFFFF;
        padding: 1rem 1.5rem;
        border-radius: 999px;
        font-weight: 800;
        font-size: 0.875rem;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        box-shadow: 0 8px 24px rgba(239, 68, 68, 0.35);
        transition: all 0.3s ease;
        border: 1px solid #f87171;
        cursor: pointer;
    }
    .fab-emergency:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(239, 68, 68, 0.45);
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    }

    .section-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #0A0F1E;
        letter-spacing: -0.01em;
    }

    @keyframes pulse-soft {
        0%, 100% { box-shadow: 0 8px 24px rgba(239, 68, 68, 0.35); }
        50%      { box-shadow: 0 12px 32px rgba(239, 68, 68, 0.6); }
    }
    .fab-emergency { animation: pulse-soft 2.5s ease-in-out infinite; }
</style>

{{-- HEADER --}}
<div class="dashboard-header mb-8 mt-2 mx-0 md:mx-auto max-w-7xl md:rounded-2xl md:mt-4">
    <div class="relative z-10 flex items-center justify-between mb-1">
        <div class="min-w-0">
            <h1 class="text-white font-extrabold truncate text-xl sm:text-2xl tracking-tight mb-1">
                Halo, {{ $user->full_name ?? 'Pengguna' }}
            </h1>
            <p class="text-white/70 text-xs sm:text-sm font-medium tracking-wide uppercase">
                Sistem Informasi Gawat Darurat
            </p>
        </div>
        <span class="role-badge">
            <i class="bi bi-{{ ($user->role ?? '') === 'BNPB' ? 'shield-fill-check' : (($user->role ?? '') === 'RELAWAN' ? 'heart-pulse-fill' : 'people-fill') }}"></i>
            {{ ucfirst(strtolower($user->role ?? 'MASYARAKAT')) }}
        </span>
    </div>
</div>

<div class="space-y-8 pb-28">

    {{-- Warning Banner --}}
    <div id="warningBanner" class="warning-banner animate-fade-up">
        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center shrink-0">
            <i class="bi bi-exclamation-triangle-fill text-xl text-red-600"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs font-extrabold tracking-wider text-red-700 mb-0.5">PERINGATAN DARURAT</p>
            <p class="text-sm leading-snug text-red-900 font-medium">Intensitas hujan tinggi berpotensi menyebabkan banjir di wilayah Surakarta.</p>
        </div>
        <button type="button" id="dismissWarning" class="shrink-0 p-2.5 rounded-lg hover:bg-red-200/80 transition-colors text-red-700">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    {{-- News Section --}}
    <section class="animate-fade-up" style="animation-delay: 0.1s;">
        <div class="flex items-center justify-between mb-4 px-1">
            <div>
                <h2 class="section-title">Berita Terkini</h2>
                <p class="text-xs text-slate-500 mt-0.5">Informasi dan peringatan terbaru</p>
            </div>
            <a href="#" class="text-sm font-semibold hover:underline text-blue-600 flex items-center gap-1 transition-colors">
                Lihat Semua <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="news-scroll">
            @foreach($news ?? [] as $item)
                <div class="news-card news-{{ $item['tone'] ?? 'info' }}">
                    <div>
                        <span class="news-badge">{{ $item['category'] ?? 'INFO' }}</span>
                    </div>
                    <p class="font-bold text-sm leading-snug line-clamp-2 mt-2" style="color: #0f172a;">
                        {{ $item['title'] ?? '-' }}
                    </p>
                    <span class="text-xs font-medium mt-auto flex items-center gap-1.5" style="color: #64748b;">
                        <i class="bi bi-clock"></i> {{ $item['time'] ?? '' }}
                    </span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Menu Layanan --}}
    <section class="animate-fade-up" style="animation-delay: 0.2s;">
        <div class="mb-4 px-1">
            <h2 class="section-title">Menu Layanan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Akses cepat layanan SIGMA</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($menu ?? [] as $item)
                @php
                    $href = match($item['id'] ?? null) {
                        1       => route('map'),               // Peta Bencana
                        2       => route('laporan.create'),     // Lapor Bencana
                        3       => route('shelter'),            // Info Posko
                        5       => route('volunteer.create'),   // Registrasi Relawan
                        7       => route('search'),             // Cari Bencana
                        10      => route('panduan'),            // Panduan Bencana
                        6       => route('laporan.index'),      // Verifikasi Laporan (BNPB)
                        default => '#',
                    };
                @endphp
                <a href="{{ $href }}" class="menu-card group">
                    <div class="menu-icon-wrap">
                        <i class="bi {{ $item['icon'] ?? 'bi-box' }}"></i>
                    </div>
                    <p class="font-bold text-sm mb-1 text-slate-900">{{ $item['title'] ?? 'Menu' }}</p>
                    <p class="text-xs text-slate-500 leading-relaxed">{{ $item['description'] ?? '' }}</p>
                </a>
            @endforeach
        </div>
    </section>
</div>

{{-- FAB Darurat --}}
<button type="button" class="fab-emergency" onclick="alert('Tombol darurat ditekan. Nanti akan menghubungkan ke kontak darurat.')">
    <i class="bi bi-telephone-fill"></i>
    PANGGIL DARURAT
</button>

<script>
    document.getElementById('dismissWarning')?.addEventListener('click', () => {
        const banner = document.getElementById('warningBanner');
        banner.style.opacity = '0';
        banner.style.transform = 'translateY(-10px)';
        banner.style.transition = 'all 0.3s ease';
        setTimeout(() => banner.style.display = 'none', 300);
    });
</script>

@endsection
