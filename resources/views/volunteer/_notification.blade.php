{{-- Volunteer Notification Bell (Navbar) --}}
{{-- Data $volunteerNotif is provided by VolunteerNotificationComposer --}}

@if($volunteerNotif)
<div class="hidden md:block relative" id="volunteerNotifWrapper">
    <button type="button" id="volunteerNotifBtn"
            class="relative w-10 h-10 flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-all duration-200">
        <i class="bi bi-bell text-lg"></i>
        <span class="absolute top-1 right-1 min-w-[18px] h-[18px] flex items-center justify-center text-[9px] font-bold text-white bg-red-500 rounded-full px-1 animate-pulse">1</span>
    </button>

    <div id="volunteerNotifDropdown" class="hidden absolute right-0 top-full mt-2 w-96 bg-white border border-slate-200/80 rounded-2xl overflow-hidden z-50 animate-fade-in"
         style="box-shadow: 0 20px 40px -15px rgba(10, 15, 30, 0.25), 0 0 0 1px rgba(10, 15, 30, 0.045);">
        
        {{-- Header --}}
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between" style="background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);">
            <div class="flex items-center gap-1.5">
                <i class="bi bi-bell-fill text-blue-500 text-xs"></i>
                <span class="text-xs font-bold text-slate-700">Penugasan Baru</span>
            </div>
            <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-100">Menunggu Konfirmasi</span>
        </div>

        {{-- Content --}}
        <div class="p-5">
            <div class="flex items-start gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                     style="background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%);">
                    <i class="bi bi-geo-alt-fill text-white text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-900">{{ $volunteerNotif->assignment }}</p>
                    @if($volunteerNotif->disaster)
                        <p class="text-xs text-slate-500 mt-0.5">Bencana: <span class="font-semibold text-slate-700">{{ $volunteerNotif->disaster->title }}</span></p>
                    @endif
                    <p class="text-[10px] text-slate-400 mt-1">Ditugaskan oleh {{ $volunteerNotif->assignedByUser->full_name ?? 'Admin SIGMA' }}</p>
                </div>
            </div>

            <p class="text-xs text-slate-500 mb-4">Apakah Anda bersedia menerima penugasan ini?</p>

            <div class="flex gap-2">
                <form action="{{ route('volunteer.accept_assignment') }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2.5 text-xs font-bold text-white rounded-xl transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-1.5"
                            style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); box-shadow: 0 2px 8px rgba(16,185,129,0.25);">
                        <i class="bi bi-check-circle-fill text-[11px]"></i> Terima
                    </button>
                </form>
                <button type="button" onclick="document.getElementById('rejectModalNavbar').classList.remove('hidden')"
                        class="flex-1 px-4 py-2.5 text-xs font-bold text-rose-600 bg-rose-50 border border-rose-200 rounded-xl hover:bg-rose-100 transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-1.5">
                    <i class="bi bi-x-circle text-[11px]"></i> Tolak
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tolak Penugasan (dari Navbar) --}}
<div id="rejectModalNavbar" class="fixed inset-0 z-[9999] flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="document.getElementById('rejectModalNavbar').classList.add('hidden')"></div>
    <div class="relative bg-white w-full max-w-md mx-4 rounded-3xl px-7 pt-8 pb-7 shadow-2xl">
        <div class="mb-5">
            <div class="w-11 h-11 rounded-xl bg-rose-100 flex items-center justify-center mb-4">
                <i class="bi bi-x-circle-fill text-rose-500 text-lg"></i>
            </div>
            <h4 class="text-lg font-bold text-slate-900">Tolak Penugasan</h4>
            <p class="text-sm text-slate-500 mt-1">Mohon berikan alasan mengapa Anda tidak bisa menerima penugasan ke <span class="font-bold text-slate-700">{{ $volunteerNotif->assignment }}</span>.</p>
        </div>

        <form action="{{ route('volunteer.reject_assignment') }}" method="POST">
            @csrf
            <div class="mb-5">
                <label class="block text-xs font-bold text-slate-700 mb-2">Alasan Penolakan <span class="text-rose-500">*</span></label>
                <textarea name="rejection_reason" rows="4" required
                          class="w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20 resize-none"
                          placeholder="Contoh: Sedang sakit, lokasi terlalu jauh, ada keperluan mendesak..."></textarea>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('rejectModalNavbar').classList.add('hidden')"
                        class="flex-1 py-3 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-2xl transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-3 text-sm font-semibold text-white rounded-2xl transition-all cursor-pointer active:scale-[0.98] hover:brightness-105"
                        style="background: linear-gradient(135deg, #f43f5e 0%, #be123c 100%);">
                    Kirim Penolakan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    const btn = document.getElementById('volunteerNotifBtn');
    const dropdown = document.getElementById('volunteerNotifDropdown');
    if (!btn || !dropdown) return;

    btn.addEventListener('click', (e) => { e.stopPropagation(); dropdown.classList.toggle('hidden'); });
    document.addEventListener('click', () => dropdown.classList.add('hidden'));
    dropdown.addEventListener('click', (e) => e.stopPropagation());
})();
</script>
@endif
