@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

<style>
    /* Header gradient (sesuai Android TopAppBar) */
    .dashboard-header {
        background: linear-gradient(135deg, #6650a4 0%, #533f8a 55%, #7D5260 100%);
        box-shadow: 0 10px 30px rgba(102, 80, 164, 0.25);
        margin: -1.5rem -1.5rem 0 -1.5rem;
        padding: 1.5rem;
    }

    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        background: rgba(255, 255, 255, 0.2);
        color: #FFFFFF;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        padding: 0.2rem 0.6rem;
        border-radius: 999px;
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.25);
    }

    .menu-card {
        background: #FFFFFF;
        border: 1px solid rgba(202, 196, 208, 0.55);
        border-radius: 20px;
        padding: 1.25rem 1rem;
        transition: all 0.22s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        min-height: 150px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .menu-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(234, 221, 255, 0) 0%, rgba(234, 221, 255, 0.4) 100%);
        opacity: 0;
        transition: opacity 0.22s ease;
    }
    .menu-card:hover {
        transform: translateY(-3px);
        border-color: #9379d1;
        box-shadow: 0 14px 28px rgba(102, 80, 164, 0.15);
    }
    .menu-card:hover::before { opacity: 1; }
    .menu-card > * { position: relative; z-index: 1; }

    .menu-icon-wrap {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        background: #EADDFF;
        color: #21005D;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.55rem;
        margin-bottom: 0.875rem;
    }
    .menu-card:hover .menu-icon-wrap {
        background: #6650a4;
        color: #FFFFFF;
    }

    .news-card {
        min-width: 280px;
        max-width: 280px;
        height: 140px;
        border-radius: 20px;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        flex-shrink: 0;
        cursor: pointer;
        transition: transform 0.2s ease;
    }
    .news-card:hover { transform: translateY(-2px); }
    .news-info    { background: #E3F2FD; }
    .news-danger  { background: #FFEBEE; }
    .news-warning { background: #FFF3E0; }

    .news-badge {
        align-self: flex-start;
        font-size: 0.625rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        padding: 0.2rem 0.55rem;
        border-radius: 6px;
        background: rgba(0, 0, 0, 0.08);
        color: #21005D;
    }
    .news-info    .news-badge { color: #0D47A1; }
    .news-danger  .news-badge { color: #B71C1C; }
    .news-warning .news-badge { color: #E65100; }

    .news-scroll {
        display: flex;
        gap: 0.75rem;
        overflow-x: auto;
        padding: 0.25rem 0.25rem 0.75rem 0.25rem;
        scroll-snap-type: x mandatory;
        scrollbar-width: thin;
        scrollbar-color: #CAC4D0 transparent;
    }
    .news-scroll::-webkit-scrollbar { height: 6px; }
    .news-scroll::-webkit-scrollbar-thumb { background: #CAC4D0; border-radius: 3px; }
    .news-scroll .news-card { scroll-snap-align: start; }

    .warning-banner {
        background: #FFEBEE;
        color: #B71C1C;
        border-radius: 16px;
        padding: 0.875rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border: 1px solid #FFCDD2;
    }

    .fab-emergency {
        position: fixed;
        bottom: 1.5rem;
        right: 1.5rem;
        z-index: 40;
        background: #BA1A1A;
        color: #FFFFFF;
        padding: 0.9rem 1.35rem;
        border-radius: 999px;
        font-weight: 800;
        font-size: 0.875rem;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 12px 28px rgba(186, 26, 26, 0.45);
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }
    .fab-emergency:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 32px rgba(186, 26, 26, 0.55);
        background: #8C0C0C;
    }

    .section-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #21005D;
        letter-spacing: -0.01em;
    }

    @keyframes pulse-soft {
        0%, 100% { box-shadow: 0 12px 28px rgba(186, 26, 26, 0.45); }
        50%      { box-shadow: 0 12px 28px rgba(186, 26, 26, 0.75); }
    }
    .fab-emergency { animation: pulse-soft 2s ease-in-out infinite; }
</style>

{{-- HEADER --}}
<div class="dashboard-header">
    <div class="flex items-center justify-between mb-1">
        <div class="min-w-0">
            <h1 class="text-white font-extrabold truncate" style="font-size: 1.35rem; letter-spacing: 0.3px;">
                Halo, {{ $user->full_name ?? 'Pengguna' }}
            </h1>
            <p class="text-white/80 text-xs font-medium tracking-wide uppercase">
                Sistem Informasi Gawat Darurat
            </p>
        </div>
        <span class="role-badge">
            <i class="bi bi-{{ $user->role === 'BNPB' ? 'shield-fill-check' : ($user->role === 'RELAWAN' ? 'heart-pulse-fill' : 'people-fill') }}"></i>
            {{ ucfirst(strtolower($user->role ?? 'MASYARAKAT')) }}
        </span>
    </div>
</div>

<div class="space-y-6 pb-28">

    {{-- Warning Banner --}}
    <div id="warningBanner" class="warning-banner">
        <i class="bi bi-exclamation-triangle-fill text-xl shrink-0"></i>
        <div class="flex-1 min-w-0">
            <p class="text-xs font-extrabold tracking-wider">PERINGATAN DARURAT</p>
            <p class="text-sm leading-snug">Intensitas hujan tinggi berpotensi menyebabkan banjir di wilayah Surakarta.</p>
        </div>
        <button type="button" id="dismissWarning" class="shrink-0 p-1.5 rounded-lg hover:bg-red-200/60 transition-colors" style="color: #B71C1C;">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    {{-- News Section --}}
    <section>
        <div class="flex items-center justify-between mb-3 px-1">
            <h2 class="section-title">Berita Terkini</h2>
            <a href="#" class="text-xs font-semibold hover:underline" style="color: #6650a4;">
                Lihat Semua <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="news-scroll">
            @foreach($news as $item)
                <div class="news-card news-{{ $item['tone'] }}">
                    <div>
                        <span class="news-badge">{{ $item['category'] }}</span>
                    </div>
                    <p class="font-bold text-sm leading-snug line-clamp-2" style="color: #1D1B20;">
                        {{ $item['title'] }}
                    </p>
                    <span class="text-xs font-medium" style="color: rgba(29, 27, 32, 0.6);">
                        <i class="bi bi-clock"></i> {{ $item['time'] }}
                    </span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Menu Layanan --}}
    <section>
        <h2 class="section-title mb-3 px-1">Menu Layanan</h2>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            @foreach($menu as $item)
                @php
                    $href = match($item['id']) {
                        2       => route('laporan.create'),     // Lapor Bencana
                        10      => route('panduan'),            // Panduan Bencana
                        6       => route('laporan.index'),      // Verifikasi Laporan (BNPB)
                        default => '#',
                    };
                @endphp
                <a href="{{ $href }}" class="menu-card group">
                    <div class="menu-icon-wrap">
                        <i class="bi {{ $item['icon'] }}"></i>
                    </div>
                    <p class="font-bold text-sm mb-0.5" style="color: #1D1B20;">{{ $item['title'] }}</p>
                    <p class="text-xs" style="color: #625b71;">{{ $item['description'] }}</p>
                </a>
            @endforeach
        </div>
    </section>
</div>

{{-- FAB Darurat --}}
<button type="button" class="fab-emergency" onclick="alert('Tombol darurat ditekan. Nanti akan menghubungkan ke kontak darurat.')">
    <i class="bi bi-telephone-fill"></i>
    DARURAT
</button>

<script>
    document.getElementById('dismissWarning')?.addEventListener('click', () => {
        document.getElementById('warningBanner').style.display = 'none';
    });
</script>

@endsection
