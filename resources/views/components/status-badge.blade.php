@props(['status'])

@php
    $config = match(strtolower($status)) {
        'awas'     => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-100', 'dot' => 'bg-red-500', 'label' => 'Awas', 'pulse' => true],
        'siaga 1', 'siaga_1'  => ['bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'border' => 'border-orange-100', 'dot' => 'bg-orange-500', 'label' => 'Siaga 1', 'pulse' => false],
        'siaga 2', 'siaga_2'  => ['bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'border' => 'border-violet-100', 'dot' => 'bg-violet-500', 'label' => 'Siaga 2', 'pulse' => false],
        'pending'  => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'dot' => 'bg-amber-500', 'label' => 'Pending', 'pulse' => true],
        'resolved' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-100', 'dot' => 'bg-emerald-500', 'label' => 'Resolved', 'pulse' => false],
        'decline'  => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'border' => 'border-slate-200', 'dot' => 'bg-slate-400', 'label' => 'Ditolak', 'pulse' => false],
        default    => ['bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'border' => 'border-slate-200', 'dot' => 'bg-slate-400', 'label' => $status, 'pulse' => false],
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border {$config['bg']} {$config['text']} {$config['border']}"]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $config['dot'] }} {{ $config['pulse'] ? 'animate-pulse' : '' }}"></span>
    {{ $config['label'] }}
</span>
