<div class="mt-8">
    <h2 class="flex items-center gap-2 text-lg font-bold mb-4" style="color: #0A0F1E;">
        <i class="bi bi-clock-history" style="color: #3B6FE8;"></i> Riwayat Laporan Anda
    </h2>

    @if($riwayat->isEmpty())
        <div class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-slate-200/80 rounded-2xl bg-slate-50/50">
            <div class="w-14 h-14 rounded-full bg-white shadow-sm flex items-center justify-center mb-3 border border-slate-100">
                <i class="bi bi-journal-text text-xl text-slate-300"></i>
            </div>
            <h3 class="text-sm font-bold text-slate-700 mb-1">Belum Ada Laporan</h3>
            <p class="text-xs text-slate-500 text-center max-w-xs leading-relaxed">Semua laporan kejadian yang Anda kirimkan akan direkam dan ditampilkan di sini.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($riwayat as $item)
                @php
                    $statusColor = match ($item->status) {
                        'AWAS' => ['bg' => '#FFEBEE', 'text' => '#B71C1C', 'label' => 'Awas'],
                        'SIAGA_1' => ['bg' => '#FFF3E0', 'text' => '#E65100', 'label' => 'Siaga 1'],
                        'SIAGA_2' => ['bg' => '#E3F2FD', 'text' => '#0D47A1', 'label' => 'Siaga 2'],
                        'RESOLVED' => ['bg' => '#E8F5E9', 'text' => '#1B5E20', 'label' => 'Resolved'],
                        'DECLINE' => ['bg' => '#FCE4EC', 'text' => '#880E4F', 'label' => 'Decline'],
                        default => ['bg' => '#FFF8E1', 'text' => '#F57F17', 'label' => 'Pending'],
                    };
                @endphp
                <a href="{{ route('laporan.show', $item->id) }}"
                    class="group relative block bg-white border border-slate-200/70 rounded-2xl p-4 hover:border-blue-400/60 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-500/10 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="flex items-start justify-between gap-4 mb-2">
                            <h3 class="font-bold text-sm text-slate-800 leading-snug group-hover:text-blue-600 transition-colors duration-200 line-clamp-1">{{ $item->title }}</h3>
                            <span class="shrink-0 mt-0.5 inline-flex items-center text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider ring-1 ring-inset"
                                style="background: {{ $statusColor['bg'] }}; color: {{ $statusColor['text'] }}; --tw-ring-color: {{ $statusColor['text'] }}40;">
                                {{ $statusColor['label'] }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 line-clamp-1 mb-3 leading-relaxed">{{ $item->description }}</p>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[11px] font-medium text-slate-500 border-t border-slate-100/80 pt-3">
                            <span class="flex items-center gap-1.5 bg-slate-50 px-2 py-1 rounded-md border border-slate-100/50">
                                <i class="bi bi-calendar-event text-blue-500/70"></i> {{ $item->created_at->format('d M Y, H:i') }}
                            </span>
                            @if($item->location)
                                <span class="flex items-center gap-1.5 bg-slate-50 px-2 py-1 rounded-md border border-slate-100/50 truncate max-w-[200px] lg:max-w-[300px]">
                                    <i class="bi bi-geo-alt-fill text-red-400/80"></i> {{ \Illuminate\Support\Str::limit($item->location, 35) }}
                                </span>
                            @elseif($item->latitude && $item->longitude)
                                <span class="flex items-center gap-1.5 bg-slate-50 px-2 py-1 rounded-md border border-slate-100/50">
                                    <i class="bi bi-geo-alt-fill text-red-400/80"></i> {{ number_format($item->latitude, 4) }}, {{ number_format($item->longitude, 4) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
