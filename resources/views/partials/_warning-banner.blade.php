{{-- banner peringatan: cek bencana terdekat via geolokasi --}}
<div id="warningBanner" class="warning-banner banner-loading" style="transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);">
    <div class="flex items-center justify-center shrink-0 pl-1.5 pr-2" id="warningIconBg">
        <i class="bi bi-arrow-repeat text-2xl text-slate-500 animate-spin" id="warningIcon"></i>
    </div>
    <div class="flex-1 min-w-0">
        <p class="text-xs font-extrabold tracking-wider text-slate-600 mb-0.5" id="warningTitle">MEMERIKSA AREA</p>
        <p class="text-sm leading-snug text-slate-700 font-medium" id="warningText">Sedang memeriksa laporan di sekitar Anda...</p>
    </div>
    <button type="button" id="dismissWarning"
        class="shrink-0 p-2.5 rounded-lg hover:bg-slate-200/80 transition-colors text-slate-500">
        <i class="bi bi-x-lg"></i>
    </button>
</div>

<script>
(function() {
    document.getElementById('dismissWarning')?.addEventListener('click', () => {
        const banner = document.getElementById('warningBanner');
        banner.style.transition = 'all 0.35s cubic-bezier(0.4, 0, 0.2, 1)';
        banner.style.opacity = '0';
        banner.style.transform = 'scale(0.95)';
        setTimeout(() => banner.style.display = 'none', 350);
    });

    window.updateWarningBanner = function(count) {
        const banner = document.getElementById('warningBanner');
        const icon = document.getElementById('warningIcon');
        const title = document.getElementById('warningTitle');
        const text = document.getElementById('warningText');
        const dismissBtn = document.getElementById('dismissWarning');
        if (!banner) return;

        // sembunyiin konten bentar sebelum diupdate
        banner.style.opacity = '0';
        banner.style.transform = 'translateY(4px)';

        setTimeout(() => {
            if (count > 0) {
                banner.className = 'warning-banner banner-danger';
                icon.className = 'bi bi-bell-fill text-2xl text-red-600 warning-icon-shake';
                title.className = 'text-xs font-extrabold tracking-wider text-red-700 mb-0.5';
                title.textContent = 'PERINGATAN DARURAT';
                text.className = 'text-sm font-medium text-red-900';

                let message = `Ada <strong>${count}</strong> laporan baru di sekitar ${window.userCityName || 'Anda'}`;
                if (count === 1 && window.firstNearbyDisasterTitle) {
                    const dTitle = window.firstNearbyDisasterTitle.toLowerCase();
                    let type = "laporan";
                    if (dTitle.includes('banjir')) type = "laporan banjir";
                    else if (dTitle.includes('gempa')) type = "laporan gempa";
                    else if (dTitle.includes('kebakaran')) type = "laporan kebakaran";
                    message = `Ada 1 <strong>${type}</strong> baru di sekitar ${window.userCityName || 'Anda'}`;
                }

                text.innerHTML = `${message}.<div class="mt-2.5"><a href="/cari-bencana" class="inline-flex items-center gap-1.5 text-xs font-bold bg-red-600 text-white px-3 py-1.25 rounded-full hover:bg-red-700 transition-colors shadow-sm hover:shadow-md">Lihat Detail</a></div>`;
                if (dismissBtn) dismissBtn.className = 'shrink-0 p-2.5 rounded-full hover:bg-red-200/80 transition-colors text-red-700';
            } else {
                banner.className = 'warning-banner banner-safe';
                icon.className = 'bi bi-check2-circle text-2xl text-emerald-600';
                title.className = 'text-xs font-extrabold tracking-wider text-emerald-700 mb-0.5';
                title.textContent = 'AMAN';
                text.className = 'text-sm font-medium text-emerald-900';
                text.textContent = 'Tidak ada laporan darurat di sekitar lokasi Anda.';
                if (dismissBtn) dismissBtn.className = 'shrink-0 p-2.5 rounded-full hover:bg-emerald-200/80 transition-colors text-emerald-700';
            }

            // tampilin konten baru
            banner.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
            banner.style.opacity = '1';
            banner.style.transform = 'translateY(0)';
        }, 250);
    };

    window.checkNearbyDisasters = function(userLat, userLng) {
        fetch('{{ route("api.disasters") }}')
            .then(response => response.json())
            .then(data => {
                let nearbyCount = 0;
                const maxDistance = 15;
                data.forEach(item => {
                    if (item.lat && item.lng) {
                        const dist = _getDistWarn(userLat, userLng, item.lat, item.lng);
                        if (dist <= maxDistance) {
                            nearbyCount++;
                            if (nearbyCount === 1) window.firstNearbyDisasterTitle = item.title;
                        }
                    }
                });
                updateWarningBanner(nearbyCount);
            })
            .catch(() => updateWarningBanner(0));
    };

    function _getDistWarn(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2)**2 + Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLon/2)**2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }
})();
</script>
