@props([
    'value' => '0',
    'label' => '',
    'badge' => null,
    'badgeColor' => 'emerald',
])

<div class="bg-white rounded-2xl border border-slate-100 p-5 hover:border-blue-200 hover:shadow-lg hover:shadow-blue-50 transition-all duration-300">
    <div class="flex items-end justify-between">
        <div>
            <p class="text-2xl font-extrabold text-slate-900 tabular-nums leading-none">{{ $value }}</p>
            <p class="text-[12px] text-slate-500 mt-2">{{ $label }}</p>
        </div>
        @if($badge)
            <span class="text-[10px] font-bold text-{{ $badgeColor }}-600 bg-{{ $badgeColor }}-50 px-2 py-1 rounded-lg border border-{{ $badgeColor }}-100">{{ $badge }}</span>
        @endif
    </div>
</div>
