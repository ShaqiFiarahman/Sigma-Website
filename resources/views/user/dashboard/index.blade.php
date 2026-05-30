@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

    {{-- WELCOME BANNER --}}
    @include('partials._welcome-banner')

    <div class="space-y-8 pb-6">

        {{-- Warning Banner --}}
        @include('partials._warning-banner')

        {{-- VOLUNTEER SECTION --}}
        @if($volunteerData)
            @if($volunteerData->status === 'PENDING')
                <section class="animate-fade-up" style="animation-delay: 0.05s;">
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 relative">
                        <button onclick="this.parentElement.parentElement.remove()" class="absolute top-4 right-4 p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                            <i class="bi bi-x-lg text-xs"></i>
                        </button>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
                                <i class="bi bi-hourglass-split text-amber-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900">Pendaftaran relawan sedang diproses</p>
                                <p class="text-xs text-slate-500 mt-1">Menunggu verifikasi admin. Estimasi 1–3 hari kerja.</p>
                                <p class="text-[11px] text-slate-400 mt-2">Didaftarkan {{ $volunteerData->created_at?->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </section>
            @elseif($volunteerData->status === 'REJECTED')
                <section class="animate-fade-up" style="animation-delay: 0.05s;">
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 relative">
                        <button onclick="this.parentElement.parentElement.remove()" class="absolute top-4 right-4 p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                            <i class="bi bi-x-lg text-xs"></i>
                        </button>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                <i class="bi bi-x-circle text-red-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900 uppercase">Pendaftaran relawan ditolak</p>
                                <p class="text-xs text-slate-500 mt-1">Hubungi admin untuk informasi lebih lanjut atau coba daftar kembali.</p>
                            </div>
                        </div>
                    </div>
                </section>
            @elseif($volunteerData->status === 'FIRED')
                <section class="animate-fade-up" style="animation-delay: 0.05s;">
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 relative">
                        <button onclick="this.parentElement.parentElement.remove()" class="absolute top-4 right-4 p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                            <i class="bi bi-x-lg text-xs"></i>
                        </button>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center shrink-0">
                                <i class="bi bi-person-slash text-slate-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900 uppercase">Status relawan dinonaktifkan</p>
                                <p class="text-xs text-slate-500 mt-1">Akun relawan Anda telah dinonaktifkan oleh admin. Hubungi admin untuk informasi lebih lanjut.</p>
                            </div>
                        </div>
                    </div>
                </section>
            @endif
        @endif

        {{-- News Section --}}
        @include('partials._news-section')

        {{-- Menu Layanan --}}
        @include('partials._menu-section')

        {{-- Peta Bencana Section --}}
        <x-disaster-map />
    </div>

@section('footer')
    <x-footer />
@endsection

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
                if (e.deltaY !== 0) {
                    const maxScroll = newsScroll.scrollWidth - newsScroll.clientWidth;
                    // Only prevent default if there's room to scroll horizontally
                    if (maxScroll > 0) {
                        const atStart = newsScroll.scrollLeft <= 0;
                        const atEnd = newsScroll.scrollLeft >= maxScroll - 1;
                        // Allow vertical page scroll if already at the edge
                        if ((e.deltaY < 0 && atStart) || (e.deltaY > 0 && atEnd)) {
                            return;
                        }
                        e.preventDefault();
                        newsScroll.scrollBy({ left: e.deltaY * 2.5, behavior: 'smooth' });
                    }
                }
            }, { passive: false });

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
