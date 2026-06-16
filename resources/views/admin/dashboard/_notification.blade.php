<div class="hidden md:block relative" id="notifWrapper">
    <button type="button" id="notifBtn"
            class="relative w-10 h-10 flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-all duration-200">
        <i class="bi bi-bell text-lg"></i>
        <span id="notifBadge" class="hidden absolute top-1 right-1 min-w-[18px] h-[18px] flex items-center justify-center text-[9px] font-bold text-white bg-red-500 rounded-full px-1 animate-pulse">0</span>
    </button>

    <div id="notifDropdown" class="hidden absolute right-0 top-full mt-2 w-80 bg-white border border-slate-200/80 rounded-2xl overflow-hidden z-50 notif-dropdown"
         style="box-shadow: 0 20px 40px -15px rgba(10, 15, 30, 0.25), 0 0 0 1px rgba(10, 15, 30, 0.045);">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between" style="background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);">
            <div class="flex items-center gap-1.5">
                <i class="bi bi-bell text-slate-500 text-xs"></i>
                <span class="text-xs font-bold text-slate-700">Notifikasi</span>
            </div>
            <span id="notifCount" class="hidden">0 baru</span>
        </div>
        <div id="notifList" class="max-h-72 overflow-y-auto pb-2">
            <div class="px-5 py-8 text-center"><p class="text-xs text-slate-400">Memuat...</p></div>
        </div>
        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50 flex items-center justify-center">
            <button type="button" onclick="markAllRead()" id="markReadBtn" class="w-full text-[10px] font-bold text-blue-600 hover:text-blue-800 text-center cursor-pointer tracking-wider uppercase hidden">Tandai sudah dibaca</button>
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
    const bellIcon = notifBtn?.querySelector('.bi-bell');
    if (!notifBtn) return;

    // buka dropdown notifikasi dan jalankan animasi lonceng
    function openDropdown() {
        isOpen = true;
        notifDropdown.classList.remove('hidden', 'is-closing');
        notifDropdown.classList.add('is-opening');
        if (bellIcon) { bellIcon.classList.add('notif-bell-active'); setTimeout(() => bellIcon.classList.remove('notif-bell-active'), 600); }
        notifDropdown.addEventListener('animationend', function handler() {
            notifDropdown.classList.remove('is-opening');
            notifDropdown.classList.add('is-open');
            notifDropdown.removeEventListener('animationend', handler);
        });
    }

    // tutup dropdown notifikasi dengan animasi fade-out
    function closeDropdown() {
        if (!isOpen) return;
        isOpen = false;
        notifDropdown.classList.remove('is-open', 'is-opening');
        notifDropdown.classList.add('is-closing');
        notifDropdown.addEventListener('animationend', function handler() {
            notifDropdown.classList.remove('is-closing');
            notifDropdown.classList.add('hidden');
            notifDropdown.removeEventListener('animationend', handler);
        });
    }

    notifBtn.addEventListener('click', (e) => { e.stopPropagation(); isOpen ? closeDropdown() : openDropdown(); });
    document.addEventListener('click', () => closeDropdown());
    notifDropdown?.addEventListener('click', (e) => e.stopPropagation());

    // kelola state baca notifikasi pake localStorage biar ga ke-reset pas refresh
    function getLastSeen() { return localStorage.getItem('sigma_notif_last_seen') || '1970-01-01T00:00:00.000Z'; }
    function setLastSeen() { localStorage.setItem('sigma_notif_last_seen', new Date().toISOString()); }

    // tandai semua notifikasi udah dibaca dan sembunyiin badge merah
    window.markAllRead = function() {
        setLastSeen();
        notifBadge.classList.add('hidden');
        markReadBtn.classList.add('hidden');
        document.querySelectorAll('.notif-new-dot').forEach(el => el.remove());
        notifCount.textContent = '0 baru';
    };

    // ambil data notifikasi dari api (polling)
    function fetchNotifications() {
        fetch('{{ route("api.pending_reports") }}')
            .then(r => r.json())
            .then(data => {
                const lastSeen = getLastSeen();
                const unseen = data.filter(item => item.created_at > lastSeen);
                if (unseen.length > 0) {
                    notifBadge.textContent = unseen.length > 9 ? '9+' : unseen.length;
                    notifBadge.classList.remove('hidden');
                    markReadBtn.classList.remove('hidden');
                } else {
                    notifBadge.classList.add('hidden');
                    markReadBtn.classList.add('hidden');
                }
                notifCount.textContent = unseen.length + ' baru';

                if (data.length === 0) {
                    notifList.innerHTML = '<div class="px-5 py-8 text-center"><p class="text-xs text-slate-400">Tidak ada laporan pending</p></div>';
                    return;
                }
                notifList.innerHTML = data.map(item => {
                    const isNew = item.created_at > lastSeen;
                    return `<div class="notif-item px-5 py-3 flex items-start gap-3 hover:bg-slate-50/80 cursor-pointer border-b border-slate-100/50 last:border-0" onclick="window.location.href='/laporan/detail/${item.id}'">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-slate-800 truncate">${item.title}</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">${item.reporter} · ${item.date}</p>
                        </div>
                        ${isNew ? '<span class="notif-new-dot w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0 mt-2"></span>' : ''}
                    </div>`;
                }).join('');
            })
            .catch(() => { notifList.innerHTML = '<div class="px-5 py-8 text-center"><p class="text-xs text-slate-400">Gagal memuat</p></div>'; });
    }

    fetchNotifications();
    setInterval(fetchNotifications, 30000);
})();
</script>
