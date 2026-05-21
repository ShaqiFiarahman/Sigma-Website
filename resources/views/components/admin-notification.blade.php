<div class="hidden md:block relative" id="notifWrapper">
    {{-- Bell Button --}}
    <button type="button" id="notifBtn"
            class="relative w-10 h-10 flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-all duration-200">
        <i class="bi bi-bell text-lg"></i>
        <span id="notifBadge" class="hidden absolute top-1 right-1 min-w-[18px] h-[18px] flex items-center justify-center text-[9px] font-bold text-white bg-red-500 rounded-full px-1 animate-pulse">
            0
        </span>
    </button>

    {{-- Dropdown Panel --}}
    <div id="notifDropdown" class="hidden absolute right-0 top-full mt-2 w-80 bg-white border border-slate-200/80 rounded-2xl overflow-hidden z-50"
         style="box-shadow: 0 10px 40px rgba(10,15,30,0.15);">

        {{-- Header --}}
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between"
             style="background: linear-gradient(135deg, #0A0F1E 0%, #1e3a8a 100%);">
            <div class="flex items-center gap-2">
                <i class="bi bi-bell-fill text-white/80 text-sm"></i>
                <span class="text-xs font-bold text-white">Notifikasi</span>
            </div>
            <span id="notifCount" class="text-[10px] font-bold text-blue-200 bg-white/15 px-2 py-0.5 rounded-full">0 baru</span>
        </div>

        {{-- List --}}
        <div id="notifList" class="max-h-72 overflow-y-auto">
            <div class="px-5 py-8 text-center">
                <i class="bi bi-hourglass-split text-slate-300 text-lg block mb-1"></i>
                <p class="text-xs text-slate-400">Memuat notifikasi...</p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50">
            <button type="button" onclick="markAllRead()" id="markReadBtn"
                    class="w-full text-[11px] font-semibold text-blue-600 hover:text-blue-800 text-center cursor-pointer transition-colors hidden">
                <i class="bi bi-check2-all mr-1"></i> Tandai semua sudah dibaca
            </button>
            <button type="button" onclick="window.location.href='{{ route('laporan.index') }}'"
                    class="w-full text-[11px] font-semibold text-slate-500 hover:text-slate-700 text-center cursor-pointer transition-colors mt-1">
                Lihat Semua Laporan
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    const notifBtn = document.getElementById('notifBtn');
    const notifDropdown = document.getElementById('notifDropdown');
    const notifBadge = document.getElementById('notifBadge');
    const notifCount = document.getElementById('notifCount');
    const notifList = document.getElementById('notifList');
    const markReadBtn = document.getElementById('markReadBtn');

    if (!notifBtn) return;

    // Toggle dropdown
    notifBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        notifDropdown.classList.toggle('hidden');
    });
    document.addEventListener('click', () => notifDropdown?.classList.add('hidden'));
    notifDropdown?.addEventListener('click', (e) => e.stopPropagation());

    // Get last seen timestamp from localStorage
    function getLastSeen() {
        return localStorage.getItem('sigma_notif_last_seen') || '1970-01-01T00:00:00.000Z';
    }

    function setLastSeen() {
        localStorage.setItem('sigma_notif_last_seen', new Date().toISOString());
    }

    window.markAllRead = function() {
        setLastSeen();
        notifBadge.classList.add('hidden');
        markReadBtn.classList.add('hidden');
        // Remove "new" indicators
        document.querySelectorAll('.notif-new-dot').forEach(el => el.remove());
        notifCount.textContent = '0 baru';
    };

    function fetchNotifications() {
        fetch('/api/pending-reports')
            .then(r => r.json())
            .then(data => {
                const lastSeen = getLastSeen();
                const unseen = data.filter(item => item.created_at > lastSeen);
                const count = unseen.length;

                // Badge
                if (count > 0) {
                    notifBadge.textContent = count > 9 ? '9+' : count;
                    notifBadge.classList.remove('hidden');
                    markReadBtn.classList.remove('hidden');
                } else {
                    notifBadge.classList.add('hidden');
                    markReadBtn.classList.add('hidden');
                }
                notifCount.textContent = count + ' baru';

                // List
                if (data.length === 0) {
                    notifList.innerHTML = `
                        <div class="px-5 py-8 text-center">
                            <i class="bi bi-check-circle text-emerald-300 text-2xl block mb-2"></i>
                            <p class="text-xs font-medium text-slate-500">Semua laporan sudah ditinjau</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Tidak ada laporan pending saat ini.</p>
                        </div>`;
                    return;
                }

                notifList.innerHTML = data.map(item => {
                    const isNew = item.created_at > lastSeen;
                    return `
                        <div class="px-5 py-3.5 flex items-start gap-3 hover:bg-slate-50 cursor-pointer transition-colors border-b border-slate-50 last:border-0"
                             onclick="window.location.href='/laporan/detail/${item.id}'">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 mt-0.5 ${isNew ? 'bg-blue-100' : 'bg-slate-100'}">
                                <i class="bi bi-megaphone-fill text-xs ${isNew ? 'text-blue-600' : 'text-slate-400'}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-slate-800 truncate">${item.title}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-1.5">
                                    <span>${item.reporter}</span> &middot; <span>${item.date}</span>
                                </p>
                            </div>
                            ${isNew ? '<span class="notif-new-dot w-2 h-2 rounded-full bg-blue-500 shrink-0 mt-2"></span>' : ''}
                        </div>`;
                }).join('');
            })
            .catch(() => {
                notifList.innerHTML = `
                    <div class="px-5 py-8 text-center">
                        <i class="bi bi-wifi-off text-slate-300 text-lg block mb-1"></i>
                        <p class="text-xs text-slate-400">Gagal memuat notifikasi</p>
                    </div>`;
            });
    }

    fetchNotifications();
    setInterval(fetchNotifications, 30000);
})();
</script>
