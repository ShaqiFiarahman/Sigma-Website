@extends('layouts.app')
@section('title', 'Berita Terkini')
@section('subtitle', 'Informasi dan peringatan bencana terbaru dari berbagai sumber.')

@section('page-actions')
    <button onclick="window.location.href='{{ route('dashboard') }}'" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all duration-200 shadow-sm cursor-pointer">
        <i class="bi bi-arrow-left text-xs"></i> Kembali
    </button>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- Search & Filter Bar --}}
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden mb-6"
         style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">
        <form action="{{ route('news.index') }}" method="GET" class="p-4 sm:p-5">
            <div class="flex flex-col sm:flex-row gap-3">
                {{-- Search Input --}}
                <div class="relative flex-1">
                    <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Cari berita bencana..."
                           class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all bg-slate-50 focus:bg-white text-slate-800 placeholder:text-slate-400">
                </div>

                {{-- Source Filter --}}
                <div class="relative">
                    <select name="source"
                            class="appearance-none w-full sm:w-44 pl-3.5 pr-8 py-2.5 text-sm font-medium bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all text-slate-700 cursor-pointer">
                        <option value="">Semua Sumber</option>
                        @foreach($sources as $source)
                            <option value="{{ $source }}" {{ request('source') === $source ? 'selected' : '' }}>
                                {{ $source }}
                            </option>
                        @endforeach
                    </select>
                    <i class="bi bi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-200 hover:-translate-y-0.5 flex items-center justify-center gap-2"
                        style="background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%); box-shadow: 0 2px 8px rgba(30,58,138,0.25);">
                    <i class="bi bi-funnel-fill text-xs"></i> Filter
                </button>

                @if(request('q') || request('source'))
                    <a href="{{ route('news.index') }}"
                       class="px-4 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all duration-200 flex items-center justify-center gap-1.5">
                        <i class="bi bi-x-lg text-xs"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Results Count --}}
    <div class="flex items-center justify-between mb-4 px-1">
        <p class="text-sm text-slate-500">
            Menampilkan <span class="font-semibold text-slate-700">{{ $news->total() }}</span> berita
            @if(request('q'))
                untuk "<span class="font-semibold text-slate-700">{{ request('q') }}</span>"
            @endif
        </p>
    </div>

    {{-- News Grid --}}
    @if($news->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($news as $item)
                <article onclick="window.open('{{ $item->url }}', '_blank')" class="cursor-pointer group block bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300"
                         style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
                    {{-- Image --}}
                    <div class="relative h-44 overflow-hidden bg-slate-100">
                        @if($item->image_url)
                            <img src="{{ $item->image_url }}"
                                 alt="{{ $item->title }}"
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                 loading="lazy" decoding="async">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-slate-300">
                                <i class="bi bi-newspaper text-4xl mb-1"></i>
                                <span class="text-xs">Tidak ada gambar</span>
                            </div>
                        @endif

                        {{-- Source Badge --}}
                        <div class="absolute top-3 left-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide bg-white/90 text-blue-800 shadow-sm">
                                {{ $item->source }}
                            </span>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="p-5 flex flex-col flex-1">
                        <h3 class="font-bold text-sm text-slate-900 leading-snug line-clamp-2 mb-2 group-hover:text-blue-700 transition-colors">
                            {{ $item->title }}
                        </h3>

                        <p class="text-xs text-slate-500 line-clamp-3 mb-4 leading-relaxed flex-1">
                            {{ Str::limit(strip_tags($item->summary), 120) }}
                        </p>

                        <div class="pt-3 border-t border-slate-100">
                            <div class="flex items-center gap-1.5 text-xs text-slate-400">
                                <i class="bi bi-clock"></i>
                                <span>{{ $item->published_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($news->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $news->withQueryString()->links() }}
            </div>
        @endif
    @else
        <div class="bg-white border border-slate-200/80 rounded-2xl p-16 text-center"
             style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
            <div class="w-16 h-16 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background: #E4F0F6;">
                <i class="bi bi-newspaper text-2xl" style="color: #1e3a8a;"></i>
            </div>
            <p class="text-sm font-semibold text-slate-800 mb-1">Tidak ada berita ditemukan</p>
            <p class="text-xs text-slate-400">
                @if(request('q') || request('source'))
                    Coba ubah kata kunci atau filter pencarian Anda.
                @else
                    Berita bencana belum tersedia saat ini.
                @endif
            </p>
        </div>
    @endif

</div>
@endsection
