{{-- Admin Laporan: Confirm Modal --}}
<div id="confirmModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm sigma-modal-backdrop"></div>
    <div class="relative bg-white rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl border border-slate-100/80 sigma-modal-content">
        <div class="p-6 text-center">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white flex items-center justify-center mx-auto mb-5 shadow-lg shadow-emerald-500/20 sigma-modal-icon" style="transform: rotate(3deg);">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-2">Selesaikan Laporan?</h3>
            <p class="text-xs text-slate-500 leading-relaxed mb-6">Apakah Anda yakin ingin mengubah status laporan ini menjadi <strong>Selesai</strong>? Laporan yang diselesaikan akan ditandai sebagai teratasi.</p>
            <div class="flex items-center gap-2.5">
                <button type="button" id="confirmCancelBtn" class="flex-1 py-2.5 text-xs font-bold text-slate-600 border border-slate-200 bg-white rounded-xl hover:bg-slate-50 transition-colors cursor-pointer">Batalkan</button>
                <button type="button" id="confirmOkBtn" class="flex-1 py-2.5 text-xs font-bold text-white rounded-xl shadow-md transition-all hover:opacity-95 cursor-pointer" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">Ya, Selesai</button>
            </div>
        </div>
    </div>
</div>
