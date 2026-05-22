@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

    {{-- WELCOME BANNER --}}
    <x-welcome-banner />

    <div class="space-y-8 pb-6">

        {{-- Warning Banner --}}
        <x-warning-banner />

        {{-- VOLUNTEER SECTION --}}
        @if($volunteerData)
            @if($volunteerData->status === 'APPROVED' && $volunteerDashboard)

                {{-- Notifikasi Penugasan Baru --}}
                @if($volunteerData->assignment && !$volunteerData->assignment_notified_at)
                    <section class="animate-fade-up" style="animation-delay: 0.03s;">
                        <div class="bg-blue-600 rounded-2xl p-5 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                                    <i class="bi bi-bell-fill text-white"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white">Penugasan baru dari Admin</p>
                                    <p class="text-xs text-blue-100 mt-0.5">Anda ditugaskan ke <strong>{{ $volunteerData->assignment }}</strong></p>
                                </div>
                            </div>
                            <form action="{{ route('volunteer.dismiss_notification') }}" method="POST" class="shrink-0">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-blue-600 bg-white rounded-lg hover:bg-blue-50 transition-colors">
                                    Mengerti
                                </button>
                            </form>
                        </div>
                    </section>
                @endif

                {{-- Stats --}}
                <section class="grid grid-cols-3 gap-3 animate-fade-up" style="animation-delay: 0.05s;">
                    <div class="bg-white rounded-2xl p-5 border border-slate-100">
                        <p class="text-2xl font-extrabold text-slate-900 tabular-nums">{{ $volunteerDashboard->totalReports }}</p>
                        <p class="text-xs text-slate-500 mt-1">Laporan dikirim</p>
                    </div>
                    <div class="bg-white rounded-2xl p-5 border border-slate-100">
                        <p class="text-2xl font-extrabold text-slate-900 tabular-nums">{{ $volunteerDashboard->reportsThisMonth }}</p>
                        <p class="text-xs text-slate-500 mt-1">Laporan bulan ini</p>
                    </div>
                    <div class="bg-white rounded-2xl p-5 border border-slate-100">
                        <p class="text-2xl font-extrabold text-slate-900 tabular-nums">{{ $volunteerDashboard->activeDisasters }}</p>
                        <p class="text-xs text-slate-500 mt-1">Bencana aktif</p>
                    </div>
                </section>

                {{-- Main Content --}}
                <section class="grid grid-cols-1 lg:grid-cols-5 gap-5 animate-fade-up" style="animation-delay: 0.08s;">

                    {{-- Penugasan + Riwayat --}}
                    <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-100 overflow-hidden">
                        {{-- Penugasan --}}
                        <div class="p-5 pb-4">
                            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-3">Penugasan</p>
                            @if($volunteerData->assignment)
                                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center shrink-0">
                                            <i class="bi bi-geo-alt-fill text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-base font-bold text-slate-900">{{ $volunteerData->assignment }}</p>
                                            <p class="text-xs text-slate-500 mt-1">{{ $volunteerData->skill }} · Aktif</p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                                    <p class="text-sm text-slate-500">Belum ada penugasan</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Admin akan menghubungi saat dibutuhkan.</p>
                                </div>
                            @endif
                        </div>

                        {{-- Divider --}}
                        <div class="border-t border-slate-100"></div>

                        {{-- Riwayat Laporan --}}
                        <div class="p-5">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Riwayat Laporan</p>
                                <a href="{{ route('volunteer.reports') }}" class="text-[11px] font-semibold text-blue-600 hover:text-blue-800">Lihat semua</a>
                            </div>
                            @if($volunteerDashboard->recentReports->count() > 0)
                                <div class="space-y-3">
                                    @foreach($volunteerDashboard->recentReports as $report)
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center shrink-0">
                                                <i class="bi bi-file-text text-slate-500 text-xs"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-semibold text-slate-800 truncate">
                                                    {{ ucfirst(strtolower($report->skill_type)) }}@if($report->disaster) · {{ \Illuminate\Support\Str::limit($report->disaster->title, 20) }}@endif
                                                </p>
                                                <p class="text-[11px] text-slate-400">{{ $report->created_at->format('d M Y, H:i') }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-slate-400">Belum ada laporan.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Sidebar --}}
                    <div class="lg:col-span-2 space-y-4">
                        {{-- Ketersediaan --}}
                        <div class="bg-white rounded-2xl border border-slate-100 p-5">
                            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-3">Ketersediaan</p>
                            <form action="{{ route('volunteer.toggle_availability') }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="submit" name="availability" value="available"
                                        class="px-3 py-2.5 text-xs font-semibold rounded-xl border transition-all cursor-pointer {{ ($volunteerData->availability ?? 'available') === 'available' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 text-slate-500 hover:border-slate-300' }}">
                                        Tersedia
                                    </button>
                                    <button type="submit" name="availability" value="unavailable"
                                        class="px-3 py-2.5 text-xs font-semibold rounded-xl border transition-all cursor-pointer {{ ($volunteerData->availability ?? 'available') === 'unavailable' ? 'border-slate-400 bg-slate-100 text-slate-700' : 'border-slate-200 text-slate-500 hover:border-slate-300' }}">
                                        Tidak tersedia
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Tim --}}
                        <div class="bg-white rounded-2xl border border-slate-100 p-5">
                            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-3">Tim di lokasi</p>
                            @if($volunteerDashboard->teamMembers->count() > 0)
                                <div class="space-y-2.5">
                                    @foreach($volunteerDashboard->teamMembers as $member)
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-full bg-slate-800 flex items-center justify-center text-[10px] font-bold text-white shrink-0">
                                                {{ substr($member->name, 0, 1) }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-semibold text-slate-800 truncate">{{ $member->name }}</p>
                                                <p class="text-[10px] text-slate-400">{{ $member->skill }}</p>
                                            </div>
                                            <span class="w-2 h-2 rounded-full {{ ($member->availability ?? 'available') === 'available' ? 'bg-emerald-400' : 'bg-slate-300' }} shrink-0"></span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-slate-400">
                                    @if($volunteerData->assignment)
                                        Belum ada relawan lain di lokasi ini.
                                    @else
                                        Muncul setelah ada penugasan.
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>

                </section>

            @elseif($volunteerData->status === 'PENDING')
                <section class="animate-fade-up" style="animation-delay: 0.05s;">
                    <div class="bg-white rounded-2xl border border-slate-100 p-6">
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
            @endif
        @endif

        {{-- News Section --}}
        <x-news-section :news="$news ?? []" />

        {{-- Menu Layanan --}}
        <x-menu-section :menu="$menu ?? []" />

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