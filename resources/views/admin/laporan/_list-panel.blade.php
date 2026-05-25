{{-- Admin Laporan: Left Panel - List --}}
<div class="w-[340px] shrink-0 flex flex-col bg-white border border-slate-200/80 rounded-2xl overflow-hidden sticky top-24 self-start" style="box-shadow: 0 1px 3px rgba(10,15,30,0.06); max-height: calc(100vh - 140px);">

    {{-- Search --}}
    <div class="p-3 border-b border-slate-100">
        <div class="relative mb-2">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" id="adminSearch" placeholder="Cari laporan..."
                   class="w-full pl-8 pr-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20 bg-slate-50 focus:bg-white text-slate-800 placeholder:text-slate-400">
        </div>
        <div class="flex gap-1 flex-wrap">
            <button data-tab="PENDING" class="tab-btn px-2 py-1 text-[10px] font-semibold rounded-md">Pending</button>
            <button data-tab="AWAS" class="tab-btn px-2 py-1 text-[10px] font-semibold rounded-md">Awas</button>
            <button data-tab="SIAGA_1" class="tab-btn px-2 py-1 text-[10px] font-semibold rounded-md">Siaga 1</button>
            <button data-tab="SIAGA_2" class="tab-btn px-2 py-1 text-[10px] font-semibold rounded-md">Siaga 2</button>
        </div>
    </div>

    {{-- List --}}
    <div class="flex-1 overflow-y-auto" id="laporanList">
        @foreach($disasters as $d)
            @php
                $borderColor = match($d->status) {
                    'AWAS' => '#D32F2F', 'SIAGA_1' => '#EA580C', 'SIAGA_2' => '#7C3AED',
                    'RESOLVED' => '#10B981', 'DECLINE' => '#94A3B8', default => '#F59E0B',
                };
            @endphp
             <div class="laporan-item px-3 py-2.5 cursor-pointer hover:bg-blue-50/50 transition-colors border-b border-slate-50"
                  data-id="{{ $d->id }}"
                  data-status="{{ $d->status }}"
                  data-title="{{ $d->title }}"
                  data-reporter="{{ $d->reporter_name }}"
                  style="border-left: 3px solid {{ $borderColor }};">
                <div class="flex items-center justify-between gap-2 mb-0.5">
                    <p class="text-[11px] font-semibold text-slate-900 line-clamp-1">{{ $d->title }}</p>
                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded shrink-0
                        {{ $d->status === 'PENDING' ? 'bg-amber-50 text-amber-700' : '' }}
                        {{ $d->status === 'AWAS' ? 'bg-red-50 text-red-700' : '' }}
                        {{ $d->status === 'SIAGA_1' ? 'bg-orange-50 text-orange-700' : '' }}
                        {{ $d->status === 'SIAGA_2' ? 'bg-violet-50 text-violet-700' : '' }}
                        {{ $d->status === 'RESOLVED' ? 'bg-emerald-50 text-emerald-700' : '' }}
                        {{ $d->status === 'DECLINE' ? 'bg-slate-100 text-slate-500' : '' }}
                    ">{{ $d->status_label }}</span>
                </div>
                <div class="flex items-center gap-1.5 text-[10px] text-slate-400">
                    <span>{{ $d->created_at->diffForHumans() }}</span>
                    <span>·</span>
                    <span class="truncate">{{ $d->reporter_name }}</span>
                </div>
            </div>
        @endforeach
    </div>
</div>
