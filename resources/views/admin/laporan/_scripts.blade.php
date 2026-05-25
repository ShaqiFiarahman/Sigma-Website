{{-- Admin Laporan: Scripts --}}
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}" async defer></script>
<script>
    let currentId = null;
    let miniMap = null, miniMarker = null;

    // Tabs - click same tab to deselect (show all)
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            if (btn.classList.contains('active')) {
                btn.classList.remove('active');
            } else {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            }
            filterList();
        });
    });

    document.getElementById('adminSearch').addEventListener('input', filterList);
    document.getElementById('adminSearch').addEventListener('keydown', (e) => {
        if (e.key === 'Enter') e.preventDefault();
    });

    function filterList() {
        const query = document.getElementById('adminSearch').value.toLowerCase().trim();
        const activeTab = document.querySelector('.tab-btn.active');
        const tab = activeTab ? activeTab.dataset.tab : null;
        document.querySelectorAll('.laporan-item').forEach(item => {
            const title = (item.dataset.title || '').toLowerCase();
            const reporter = (item.dataset.reporter || '').toLowerCase();
            const status = item.dataset.status;

            const matchTab = !tab || status === tab;
            const matchSearch = !query || title.includes(query) || reporter.includes(query);
            item.style.display = (matchTab && matchSearch) ? '' : 'none';
        });
    }

    // Click item
    document.querySelectorAll('.laporan-item').forEach(item => {
        item.addEventListener('click', () => {
            document.querySelectorAll('.laporan-item').forEach(i => i.classList.remove('active'));
            item.classList.add('active');
            loadDetail(item.dataset.id);
        });
    });

    function loadDetail(id) {
        currentId = id;
        fetch(`/admin/api/laporan/${id}`)
            .then(r => r.json())
            .then(d => {
                document.getElementById('detailEmpty').classList.add('hidden');
                document.getElementById('detailContent').classList.remove('hidden');

                // Photo
                const photoDiv = document.getElementById('detailPhoto');
                let photos = [];
                try { photos = d.photo_url ? JSON.parse(d.photo_url) : []; } catch(e) { photos = d.photo_url ? [d.photo_url] : []; }
                if (Array.isArray(photos) && photos.length > 0) {
                    document.getElementById('detailImg').src = photos[0];
                    photoDiv.classList.remove('hidden');
                } else {
                    photoDiv.classList.add('hidden');
                }

                // Info
                document.getElementById('detailTitle').textContent = d.title;
                document.getElementById('detailMeta').textContent = `${d.type_name} · ${d.time_ago}`;
                document.getElementById('detailLocation').textContent = d.location || 'Tidak diketahui';
                document.getElementById('detailCoords').textContent = d.latitude && d.longitude ? `${d.latitude}, ${d.longitude}` : '';
                document.getElementById('detailReporter').textContent = d.reporter_name;
                document.getElementById('detailTime').textContent = d.created_at;
                document.getElementById('detailDesc').textContent = d.description || 'Tidak ada deskripsi.';

                // Badge
                const badge = document.getElementById('detailBadge');
                const styles = { PENDING:'bg-amber-50 text-amber-700', AWAS:'bg-red-50 text-red-700', SIAGA_1:'bg-orange-50 text-orange-700', SIAGA_2:'bg-violet-50 text-violet-700', RESOLVED:'bg-emerald-50 text-emerald-700', DECLINE:'bg-slate-100 text-slate-600' };
                badge.className = `text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0 ${styles[d.status] || ''}`;
                badge.textContent = d.status_label;

                // Selects
                document.getElementById('actionStatus').value = d.status;
                document.getElementById('actionType').value = d.disaster_type || 'unknown';

                // Map
                if (d.latitude && d.longitude && typeof google !== 'undefined') {
                    const pos = { lat: parseFloat(d.latitude), lng: parseFloat(d.longitude) };
                    if (!miniMap) {
                        miniMap = new google.maps.Map(document.getElementById('detailMiniMap'), {
                            zoom: 14, center: pos, disableDefaultUI: true, gestureHandling: 'greedy',
                            styles: [{ featureType: "poi", stylers: [{ visibility: "off" }] }]
                        });
                        miniMarker = new google.maps.Marker({ position: pos, map: miniMap });
                    } else {
                        miniMap.setCenter(pos);
                        miniMarker.setPosition(pos);
                    }
                }
            });
    }

    function showConfirmModal(callback) {
        const modal = document.getElementById('confirmModal');
        const content = modal.querySelector('.transform');
        const okBtn = document.getElementById('confirmOkBtn');
        const cancelBtn = document.getElementById('confirmCancelBtn');

        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);

        const closeModal = () => {
            modal.classList.add('opacity-0');
            content.classList.add('scale-95');
            setTimeout(() => { modal.classList.add('hidden'); }, 200);
        };

        const newOkBtn = okBtn.cloneNode(true);
        const newCancelBtn = cancelBtn.cloneNode(true);
        okBtn.parentNode.replaceChild(newOkBtn, okBtn);
        cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);

        newOkBtn.addEventListener('click', () => { closeModal(); callback(true); });
        newCancelBtn.addEventListener('click', () => { closeModal(); callback(false); });
        modal.onclick = (e) => { if (e.target === modal) { closeModal(); callback(false); } };
    }

    // Save
    document.getElementById('btnSaveAction').addEventListener('click', () => {
        if (!currentId) return;
        const statusVal = document.getElementById('actionStatus').value;
        if (statusVal === 'RESOLVED') {
            showConfirmModal((confirmed) => {
                if (confirmed) submitSaveForm(statusVal);
            });
        } else {
            submitSaveForm(statusVal);
        }
    });

    function submitSaveForm(statusVal) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/laporan/update-status/${currentId}`;
        form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="status" value="${statusVal}"><input type="hidden" name="disaster_type" value="${document.getElementById('actionType').value}">`;
        document.body.appendChild(form);
        form.submit();
    }
</script>
