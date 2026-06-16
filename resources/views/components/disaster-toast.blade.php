{{-- template html buat toast notifikasi bencana dinamis via js --}}
<template id="disaster-toast-template">
    <div class="toast-slide-in pointer-events-auto bg-[#FCFBF9]/95 backdrop-blur-md border border-[#EDEBE6]/80 shadow-[0_8px_30px_rgb(10,15,30,0.06)] rounded-2xl p-4 flex gap-3 transition-all duration-300 hover:shadow-[0_8px_30px_rgb(10,15,30,0.10)]">
        <div class="toast-icon-container flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-full">
            <i class="toast-icon bi text-lg animate-pulse"></i>
        </div>
        <div class="flex-grow min-w-0">
            <div class="flex items-start justify-between">
                <span class="toast-status text-[10px] font-bold uppercase tracking-wider"></span>
                <button class="opacity-60 hover:opacity-100 focus:outline-none ml-2 shrink-0 transition-opacity duration-150" onclick="this.closest('.toast-slide-in').remove()">
                    <i class="bi bi-x text-base text-slate-500"></i>
                </button>
            </div>
            <h4 class="toast-title text-xs font-bold text-slate-900 mt-0.5 truncate"></h4>
            <p class="toast-desc text-[11px] text-slate-500 mt-0.5 line-clamp-1 leading-relaxed"></p>
            <div class="flex items-center justify-between mt-3">
                <span class="text-[9px] text-slate-400 font-medium">
                    <i class="bi bi-clock"></i> Baru saja
                </span>
                <button class="toast-action flex items-center gap-1 text-[10px] font-bold px-2.5 py-1.5 rounded-lg text-white shadow-sm transition-all duration-200 hover:brightness-95 hover:shadow focus:outline-none">
                    <i class="bi bi-geo-alt-fill"></i> Lihat Lokasi
                </button>
            </div>
        </div>
    </div>
</template>
