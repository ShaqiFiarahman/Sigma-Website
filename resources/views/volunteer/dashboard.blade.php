@extends('layouts.app')
@section('title', 'Dashboard Relawan')

@section('content')

    <x-welcome-banner />

    <div class="space-y-6 pb-6">

        <x-warning-banner />

        {{-- Profile --}}
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 max-w-2xl" style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold shrink-0"
                     style="background: linear-gradient(135deg, #0A0F1E 0%, #1e3a8a 100%);">
                    {{ strtoupper(substr($volunteer->name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-sm font-bold text-slate-900">{{ $volunteer->name }}</h2>
                        @if($volunteer->status === 'APPROVED')
                            <span class="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded">Aktif</span>
                        @endif
                    </div>
                    <p class="text-[11px] text-slate-500">{{ $volunteer->skill }} · {{ $volunteer->volunteer_code }}</p>
                </div>
            </div>
            <div class="text-xs text-slate-500 space-y-1 pl-[52px]">
                <p>{{ auth()->user()->email }}</p>
                <p>{{ $volunteer->phone_number }} · {{ $volunteer->address }}</p>
            </div>
        </div>

        @if($volunteer->status === 'APPROVED')
            {{-- Stats --}}
            <div class="grid grid-cols-3 gap-3 max-w-2xl">
                <div class="bg-white border border-slate-200/80 rounded-xl p-4" style="box-shadow: 0 1px 2px rgba(10,15,30,0.04);">
                    <p class="text-[10px] text-slate-400 font-medium mb-1">Penugasan</p>
                    <p class="text-sm font-bold text-slate-900">{{ $volunteer->assignment ?? '—' }}</p>
                </div>
                <div class="bg-white border border-slate-200/80 rounded-xl p-4" style="box-shadow: 0 1px 2px rgba(10,15,30,0.04);">
                    <p class="text-[10px] text-slate-400 font-medium mb-1">Keahlian</p>
                    <p class="text-sm font-bold text-slate-900">{{ $volunteer->skill }}</p>
                </div>
                <div class="bg-white border border-slate-200/80 rounded-xl p-4" style="box-shadow: 0 1px 2px rgba(10,15,30,0.04);">
                    <p class="text-[10px] text-slate-400 font-medium mb-1">Bergabung</p>
                    <p class="text-sm font-bold text-slate-900">{{ $volunteer->created_at->format('d M Y') }}</p>
                </div>
            </div>

            {{-- Assignment --}}
            @if($volunteer->assignment)
                <div class="bg-white border border-slate-200/80 rounded-2xl p-5 max-w-2xl" style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
                    <p class="text-xs font-medium text-slate-500 mb-2">Penugasan Aktif</p>
                    <p class="text-sm font-bold text-slate-900 mb-1">{{ $volunteer->assignment }}</p>
                    <p class="text-xs text-slate-500 mb-3">Anda ditugaskan di lokasi ini. Hubungi Admin jika ada kendala.</p>
                    <button type="button"
                            onclick="window.open('https://api.whatsapp.com/send?phone=6285934415914&text={{ urlencode('Halo Admin, saya relawan ' . $volunteer->name . ' (' . $volunteer->volunteer_code . ') ingin bertanya mengenai penugasan di ' . $volunteer->assignment) }}', '_blank')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white rounded-lg cursor-pointer"
                            style="background: #25D366;">
                        <i class="bi bi-whatsapp text-[10px]"></i> Hubungi Admin
                    </button>
                </div>
            @endif
        @endif

        <x-news-section :news="$news" />
        <x-disaster-map />
    </div>

@section('footer')
    <x-footer />
@endsection

@section('scripts')
<script>
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

@endsection
