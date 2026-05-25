@props(['type' => 'success'])

@if(session('msg'))
    @php
        $msg = session('msg');
        $styles = [
            'approved' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-800', 'border' => 'border-emerald-200', 'icon' => 'bi-check-circle-fill', 'iconColor' => 'text-emerald-500'],
            'rejected' => ['bg' => 'bg-red-50', 'text' => 'text-red-800', 'border' => 'border-red-200', 'icon' => 'bi-x-circle-fill', 'iconColor' => 'text-red-500'],
            'created'  => ['bg' => 'bg-blue-50', 'text' => 'text-blue-800', 'border' => 'border-blue-200', 'icon' => 'bi-info-circle-fill', 'iconColor' => 'text-blue-500'],
        ];
        $style = $styles[$msg] ?? $styles['approved'];
    @endphp
    <div class="mb-5 p-4 rounded-xl flex items-center gap-3 text-sm font-medium {{ $style['bg'] }} {{ $style['text'] }} border {{ $style['border'] }}">
        <i class="bi {{ $style['icon'] }} {{ $style['iconColor'] }}"></i>
        <span class="flex-1">{{ $slot->isEmpty() ? ($msg === 'approved' ? 'Berhasil diperbarui.' : ($msg === 'rejected' ? 'Berhasil ditolak.' : 'Berhasil.')) : $slot }}</span>
        <button onclick="this.parentElement.remove()" class="ml-auto opacity-60 hover:opacity-100"><i class="bi bi-x-lg"></i></button>
    </div>
@endif
