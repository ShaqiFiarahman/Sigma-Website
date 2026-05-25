@props(['route' => null, 'label' => 'Kembali', 'useHistory' => false])

<button type="button"
    onclick="{{ $useHistory ? 'history.back()' : "window.location.href='" . $route . "'" }}"
    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all duration-200 shadow-sm cursor-pointer group">
    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M19 12H5M12 19l-7-7 7-7" />
    </svg>
    <span>{{ $label }}</span>
</button>
