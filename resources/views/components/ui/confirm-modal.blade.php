@props([
    'id' => 'confirmModal',
    'title' => 'Konfirmasi',
    'description' => 'Apakah Anda yakin ingin melanjutkan?',
    'confirmLabel' => 'Ya, Lanjutkan',
    'cancelLabel' => 'Batalkan',
    'icon' => 'check',
    'color' => 'emerald',
])

@php
    $gradients = [
        'emerald' => 'from-emerald-500 to-teal-400',
        'red' => 'from-red-500 to-rose-400',
        'blue' => 'from-blue-500 to-indigo-400',
    ];
    $shadows = [
        'emerald' => 'shadow-emerald-500/20',
        'red' => 'shadow-red-500/20',
        'blue' => 'shadow-blue-500/20',
    ];
    $btnStyles = [
        'emerald' => 'background: linear-gradient(135deg, #059669 0%, #10b981 100%);',
        'red' => 'background: linear-gradient(135deg, #dc2626 0%, #f43f5e 100%);',
        'blue' => 'background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%);',
    ];
@endphp

<div id="{{ $id }}" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm sigma-modal-backdrop"></div>
    <div class="relative bg-white rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl border border-slate-100/80 sigma-modal-content">
        <div class="p-6 text-center">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr {{ $gradients[$color] ?? $gradients['emerald'] }} text-white flex items-center justify-center mx-auto mb-5 shadow-lg {{ $shadows[$color] ?? $shadows['emerald'] }} sigma-modal-icon" style="transform: rotate(3deg);">
                @if($icon === 'check')
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                @elseif($icon === 'trash')
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                @elseif($icon === 'warning')
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                @endif
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-2">{{ $title }}</h3>
            <p class="text-xs text-slate-500 leading-relaxed mb-6">{!! $description !!}</p>
            <div class="flex items-center gap-2.5">
                <button type="button" data-action="cancel" class="flex-1 py-2.5 text-xs font-bold text-slate-600 border border-slate-200 bg-white rounded-xl hover:bg-slate-50 transition-colors cursor-pointer">{{ $cancelLabel }}</button>
                <button type="button" data-action="confirm" class="flex-1 py-2.5 text-xs font-bold text-white rounded-xl shadow-md transition-all hover:opacity-95 cursor-pointer" style="{{ $btnStyles[$color] ?? $btnStyles['emerald'] }}">{{ $confirmLabel }}</button>
            </div>
        </div>
    </div>
</div>
