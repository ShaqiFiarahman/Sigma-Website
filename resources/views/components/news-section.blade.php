@props(['news' => []])

<section class="animate-fade-up" style="animation-delay: 0.1s;">
    <div class="flex items-center justify-between mb-4 px-1">
        <div>
            <h2 class="section-title">Berita Terkini</h2>
            <p class="text-xs text-slate-500 mt-0.5">Informasi dan peringatan terbaru</p>
        </div>
        <a href="{{ route('news.index') }}"
            class="text-sm font-semibold hover:underline text-blue-600 flex items-center gap-1 transition-colors">
            Lihat Semua
        </a>
    </div>

    <div class="news-scroll flex gap-4 overflow-x-auto pb-4">
        @forelse($news as $item)
            <div onclick="window.open('{{ $item['url'] ?? '#' }}', '_blank')"
                class="group bg-white border border-slate-100 rounded-2xl overflow-hidden flex flex-col justify-between shadow-sm hover:shadow-[0_15px_30px_rgba(0,0,0,0.15)] hover:-translate-y-[6px] transition-all duration-300 cursor-pointer"
                style="min-width: 280px; max-width: 280px;">
                <div class="relative h-32 overflow-hidden">
                    @if(isset($item['image_url']) && $item['image_url'])
                        <img src="{{ $item['image_url'] }}"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            alt="{{ $item['title'] }}" loading="lazy" decoding="async">
                    @else
                        <div class="w-full h-full bg-slate-100 flex items-center justify-center">
                            <i class="bi bi-newspaper text-3xl text-slate-300"></i>
                        </div>
                    @endif

                    <div class="absolute top-3 left-3 bg-white/90 text-blue-800 text-[10px] font-bold px-2.5 py-1.5 rounded-lg uppercase backdrop-blur-sm">
                        {{ $item['source'] ?? 'BERITA' }}
                    </div>
                </div>

                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <p class="font-bold text-sm leading-relaxed line-clamp-2 text-slate-900 mb-3">
                            {{ $item['title'] ?? '-' }}
                        </p>
                        <div class="flex items-center gap-1.5 text-xs text-slate-500">
                            <i class="bi bi-clock"></i>
                            <span>{{ $item['time'] ?? '' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="w-full py-8 text-center text-slate-500">
                <i class="bi bi-newspaper text-4xl mb-2 block"></i>
                Berita belum tersedia
            </div>
        @endforelse
    </div>

    <div class="flex justify-center gap-1.5 mt-2" id="newsIndicators"></div>
</section>
