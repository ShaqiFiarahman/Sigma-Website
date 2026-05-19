@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation"
        class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-sm text-slate-500">
            Menampilkan
            <span class="font-semibold text-slate-700">{{ $paginator->firstItem() }}</span>
            -
            <span class="font-semibold text-slate-700">{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-semibold text-slate-700">{{ $paginator->total() }}</span>
            hasil
        </p>

        <div class="flex items-center gap-1.5">
            @if ($paginator->onFirstPage())
                <span
                    class="w-9 h-9 flex items-center justify-center rounded-lg border border-slate-200 text-slate-300 cursor-not-allowed text-sm">
                    <i class="bi bi-chevron-left text-xs"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                    class="w-9 h-9 flex items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700 transition-all duration-200 text-sm">
                    <i class="bi bi-chevron-left text-xs"></i>
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="w-9 h-9 flex items-center justify-center text-sm text-slate-400">...</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-bold text-white shadow-sm"
                                style="background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%); box-shadow: 0 2px 6px rgba(30,58,138,0.25);">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                                class="w-9 h-9 flex items-center justify-center rounded-lg border border-slate-200 text-sm font-medium text-slate-600 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700 transition-all duration-200">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                    class="w-9 h-9 flex items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700 transition-all duration-200 text-sm">
                    <i class="bi bi-chevron-right text-xs"></i>
                </a>
            @else
                <span
                    class="w-9 h-9 flex items-center justify-center rounded-lg border border-slate-200 text-slate-300 cursor-not-allowed text-sm">
                    <i class="bi bi-chevron-right text-xs"></i>
                </span>
            @endif
        </div>
    </nav>
@endif