@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

    <style>
        /* Styles removed to match admin dashboard */

        .map-container {
            width: 100%;
            height: 550px;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid rgba(202, 196, 208, 0.55);
            box-shadow: 0 4px 24px rgba(102, 80, 164, 0.08);
        }

        .legend-card {
            /* background: #FFFFFF; */
            border: 1px solid rgba(202, 196, 208, 0.55);
            border-radius: 16px;
            padding: 1rem 1.25rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
            color: #1D1B20;
            padding: 0.25rem 0;
        }

        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 3px;
            flex-shrink: 0;
        }

        .info-window-title {
            font-weight: 700;
            font-size: 14px;
            color: #1D1B20;
            margin-bottom: 4px;
        }

        .info-window-status {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 6px;
            margin-bottom: 6px;
        }

        .info-window-desc {
            font-size: 12px;
            color: #625b71;
            margin-bottom: 6px;
        }

        .info-window-meta {
            font-size: 11px;
            color: #9e9e9e;
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

        .menu-card:hover::before {
            opacity: 1;
        }

        .menu-card>* {
            position: relative;
            z-index: 1;
        }

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

        .news-scroll {
            scrollbar-width: none !important;
            /* Firefox */
            -ms-overflow-style: none !important;
            /* IE and Edge */
        }

        .news-scroll::-webkit-scrollbar {
            display: none !important;
            /* Chrome, Safari and Opera */
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

        .news-info {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }

        .news-danger {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border-color: #fecaca;
        }

        .news-warning {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border-color: #fde68a;
        }

        .news-badge {
            align-self: flex-start;
            font-size: 0.625rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
        }

        .news-info .news-badge {
            background: #e0f2fe;
            color: #0369a1;
        }

        .news-danger .news-badge {
            background: #fee2e2;
            color: #b91c1c;
        }

        .news-warning .news-badge {
            background: #fef3c7;
            color: #b45309;
        }

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

        .news-scroll::-webkit-scrollbar {
            height: 6px;
        }

        .news-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .news-scroll .news-card {
            scroll-snap-align: start;
        }

        .warning-banner {
            border-radius: 16px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 4px 12px rgba(10, 15, 30, 0.05);
            border: 1px solid transparent;
            transition: all 0.3s ease;
        }

        .banner-danger {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border-color: #fca5a5;
            color: #b91c1c;
        }

        .banner-safe {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-color: #bbf7d0;
            color: #15803d;
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

            0%,
            100% {
                box-shadow: 0 8px 24px rgba(239, 68, 68, 0.35);
            }

            50% {
                box-shadow: 0 12px 32px rgba(239, 68, 68, 0.6);
            }
        }

        .fab-emergency {
            animation: pulse-soft 2.5s ease-in-out infinite;
        }
    </style>

    {{-- WELCOME BANNER --}}
    <x-welcome-banner />

    <div class="space-y-8 pb-6">

        {{-- Warning Banner --}}
        <div id="warningBanner" class="warning-banner animate-fade-up banner-danger">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center shrink-0" id="warningIconBg">
                <i class="bi bi-exclamation-triangle-fill text-xl text-red-600" id="warningIcon"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-extrabold tracking-wider text-red-700 mb-0.5" id="warningTitle">PERINGATAN DARURAT
                </p>
                <p class="text-sm leading-snug text-red-900 font-medium" id="warningText">Sedang memeriksa laporan di
                    sekitar Anda...</p>
            </div>
            <button type="button" id="dismissWarning"
                class="shrink-0 p-2.5 rounded-lg hover:bg-red-200/80 transition-colors text-red-700">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        {{-- News Section --}}
        <x-news-section :news="$news ?? []" />

        {{-- Menu Layanan --}}
        <section class="animate-fade-up mb-8" style="animation-delay: 0.15s;">
            <div class="mb-4 px-1">
                <h2 class="section-title">Menu Layanan</h2>
                <p class="text-xs text-slate-500 mt-0.5">Akses cepat layanan SIGMA</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($menu ?? [] as $item)
                    @if(in_array($item['id'] ?? null, [1, 2])) @continue @endif
                    @php
                        $href = match ($item['id'] ?? null) {
                            1 => route('map'),               // Peta Bencana
                            2 => route('laporan.create'),     // Lapor Bencana
                            3 => route('shelter'),            // Info Posko
                            5 => route('volunteer.create'),   // Registrasi Relawan
                            7 => route('search'),             // Cari Bencana
                            10 => route('panduan'),            // Panduan Bencana
                            6 => route('laporan.index'),      // Verifikasi Laporan (Admin)
                            default => '#',
                        };
                    @endphp
                    <div onclick="window.location.href='{{ $href }}'" class="menu-card group cursor-pointer">
                        <div class="menu-icon-wrap">
                            <i class="bi {{ $item['icon'] ?? 'bi-box' }}"></i>
                        </div>
                        <p class="font-bold text-sm mb-1 text-slate-900">{{ $item['title'] ?? 'Menu' }}</p>
                        <p class="text-xs text-slate-500 leading-relaxed">{{ $item['description'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Peta Bencana Section --}}
        <x-disaster-map />
    </div>

@section('footer')
    <x-footer />
@endsection

    <script>
        document.getElementById('dismissWarning')?.addEventListener('click', () => {
            const banner = document.getElementById('warningBanner');
            banner.style.opacity = '0';
            banner.style.transform = 'translateY(-10px)';
            banner.style.transition = 'all 0.3s ease';
            setTimeout(() => banner.style.display = 'none', 300);
        });

        function updateWarningBanner(count) {
            const banner = document.getElementById('warningBanner');
            const iconBg = document.getElementById('warningIconBg');
            const icon = document.getElementById('warningIcon');
            const title = document.getElementById('warningTitle');
            const text = document.getElementById('warningText');
            const dismissBtn = document.getElementById('dismissWarning');

            if (!banner) return;

            if (count > 0) {
                banner.className = 'warning-banner animate-fade-up banner-danger';
                iconBg.className = 'w-12 h-12 rounded-full bg-red-100 flex items-center justify-center shrink-0';
                icon.className = 'bi bi-bell-fill text-xl text-red-600';
                title.className = 'text-xs font-extrabold tracking-wider text-red-700 mb-0.5';
                title.textContent = 'PERINGATAN DARURAT';
                text.className = 'text-sm font-medium text-red-900';

                let message = `Ada <strong>${count}</strong> laporan baru di sekitar ${window.userCityName || 'Anda'}`;

                if (count === 1 && window.firstNearbyDisasterTitle) {
                    const dTitle = window.firstNearbyDisasterTitle.toLowerCase();
                    let type = "laporan";
                    if (dTitle.includes('banjir')) type = "laporan banjir";
                    else if (dTitle.includes('gempa')) type = "laporan gempa";
                    else if (dTitle.includes('kebakaran')) type = "laporan kebakaran";
                    message = `Ada 1 <strong>${type}</strong> baru di sekitar ${window.userCityName || 'Anda'}`;
                }

                text.innerHTML = `${message}.
                    <div class="mt-2.5">
                        <a href="{{ route('search') }}" class="inline-flex items-center gap-1.5 text-xs font-bold bg-red-600 text-white px-3 py-1.25 rounded-full hover:bg-red-700 transition-colors shadow-sm hover:shadow-md">
                            Lihat Detail <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>`;
                if (dismissBtn) dismissBtn.className = 'shrink-0 p-2.5 rounded-full hover:bg-red-200/80 transition-colors text-red-700';
            } else {
                banner.className = 'warning-banner animate-fade-up banner-safe';
                iconBg.className = 'w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center shrink-0';
                icon.className = 'bi bi-check2-circle text-xl text-emerald-600';
                title.className = 'text-xs font-extrabold tracking-wider text-emerald-700 mb-0.5';
                title.textContent = 'AMAN';
                text.className = 'text-sm font-medium text-emerald-900';
                text.textContent = 'Tidak ada laporan darurat di sekitar lokasi Anda.';
                if (dismissBtn) dismissBtn.className = 'shrink-0 p-2.5 rounded-full hover:bg-emerald-200/80 transition-colors text-emerald-700';
            }
        }

        function checkNearbyDisasters(userLat, userLng) {
            fetch('/api/disasters')
                .then(response => response.json())
                .then(data => {
                    let nearbyCount = 0;
                    const maxDistance = 15;
                    data.forEach(item => {
                        if (item.lat && item.lng) {
                            const dist = getDistance(userLat, userLng, item.lat, item.lng);
                            if (dist <= maxDistance) {
                                nearbyCount++;
                                if (nearbyCount === 1) window.firstNearbyDisasterTitle = item.title;
                            }
                        }
                    });
                    updateWarningBanner(nearbyCount);
                })
                .catch(() => updateWarningBanner(0));
        }

        function getDistance(lat1, lon1, lat2, lon2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2)**2 + Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLon/2)**2;
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        }

        // News scroll indicators
        const newsScroll = document.querySelector('.news-scroll');
        const indicatorsContainer = document.getElementById('newsIndicators');

        if (newsScroll && indicatorsContainer) {
            for (let i = 0; i < 3; i++) {
                const dot = document.createElement('div');
                dot.className = `w-2 h-2 rounded-full transition-all duration-300 cursor-pointer ${i === 0 ? 'bg-[#2B52C3] w-8' : 'bg-slate-300'}`;
                dot.addEventListener('click', () => {
                    const maxScroll = newsScroll.scrollWidth - newsScroll.clientWidth;
                    newsScroll.scrollTo({ left: i === 0 ? 0 : i === 1 ? maxScroll * 0.5 : maxScroll, behavior: 'smooth' });
                });
                indicatorsContainer.appendChild(dot);
            }

            const dots = indicatorsContainer.querySelectorAll('div');

            newsScroll.addEventListener('wheel', (e) => {
                if (e.deltaY !== 0) { e.preventDefault(); newsScroll.scrollBy({ left: e.deltaY * 2.5, behavior: 'smooth' }); }
            });

            newsScroll.addEventListener('scroll', () => {
                const maxScroll = newsScroll.scrollWidth - newsScroll.clientWidth;
                let activeIndex = 0;
                if (maxScroll > 0) {
                    const pct = newsScroll.scrollLeft / maxScroll;
                    if (pct > 0.33 && pct <= 0.66) activeIndex = 1;
                    else if (pct > 0.66) activeIndex = 2;
                }
                dots.forEach((dot, i) => {
                    dot.className = i === activeIndex ? 'w-8 h-2 rounded-full bg-[#2B52C3] transition-all duration-300' : 'w-2 h-2 rounded-full bg-slate-300 transition-all duration-300';
                });
            });
        }
    </script>

@endsection