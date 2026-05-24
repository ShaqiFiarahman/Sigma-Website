@extends('layouts.app')
@section('title', 'Kelola Laporan')
@section('subtitle', 'Verifikasi, tinjau, dan kelola seluruh laporan bencana.')

@section('page-actions')
    <button type="button" onclick="window.location.href='{{ route('admin.dashboard') }}'" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all duration-200 shadow-sm cursor-pointer group">
        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 19l-7-7 7-7" />
        </svg>
        <span>Kembali</span>
    </button>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- Mini Stats --}}
    <div class="flex items-center gap-6 mb-5 text-sm">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-red-500"></span>
            <span class="text-slate-500">Laporan Aktif</span>
            <span class="font-bold text-slate-900">{{ $stats['active'] }}</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
            <span class="text-slate-500">Butuh Verifikasi</span>
            <span class="font-bold text-slate-900">{{ $stats['need_verify'] }}</span>
        </div>
    </div>
    <div class="flex gap-4">

        {{-- LEFT: List --}}
        <div class="w-[340px] shrink-0 flex flex-col bg-white border border-slate-200/80 rounded-2xl overflow-hidden sticky top-24 self-start" style="box-shadow: 0 1px 3px rgba(10,15,30,0.06); max-height: calc(100vh - 140px);">

            {{-- Search --}}
            <div class="p-3 border-b border-slate-100">
                <div class="relative mb-2">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="adminSearch" placeholder="Cari laporan..."
                           class="w-full pl-8 pr-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20 bg-slate-50 focus:bg-white text-slate-800 placeholder:text-slate-400">
                </div>
                <div class="flex gap-1 flex-wrap">
                    <button data-tab="PENDING" class="tab-btn px-2 py-1 text-[10px] font-semibold rounded-md">Pending</button>
                    <button data-tab="AWAS" class="tab-btn px-2 py-1 text-[10px] font-semibold rounded-md">Awas</button>
                    <button data-tab="SIAGA_1" class="tab-btn px-2 py-1 text-[10px] font-semibold rounded-md">Siaga 1</button>
                    <button data-tab="SIAGA_2" class="tab-btn px-2 py-1 text-[10px] font-semibold rounded-md">Siaga 2</button>
                </div>
            </div>

            {{-- List --}}
            <div class="flex-1 overflow-y-auto" id="laporanList">
                @foreach($disasters as $d)
                    @php
                        $borderColor = match($d->status) {
                            'AWAS' => '#D32F2F', 'SIAGA_1' => '#EA580C', 'SIAGA_2' => '#7C3AED',
                            'RESOLVED' => '#10B981', 'DECLINE' => '#94A3B8', default => '#F59E0B',
                        };
                    @endphp
                     <div class="laporan-item px-3 py-2.5 cursor-pointer hover:bg-blue-50/50 transition-colors border-b border-slate-50"
                          data-id="{{ $d->id }}"
                          data-status="{{ $d->status }}"
                          data-title="{{ $d->title }}"
                          data-reporter="{{ $d->reporter_name }}"
                          style="border-left: 3px solid {{ $borderColor }};">
                        <div class="flex items-center justify-between gap-2 mb-0.5">
                            <p class="text-[11px] font-semibold text-slate-900 line-clamp-1">{{ $d->title }}</p>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded shrink-0
                                {{ $d->status === 'PENDING' ? 'bg-amber-50 text-amber-700' : '' }}
                                {{ $d->status === 'AWAS' ? 'bg-red-50 text-red-700' : '' }}
                                {{ $d->status === 'SIAGA_1' ? 'bg-orange-50 text-orange-700' : '' }}
                                {{ $d->status === 'SIAGA_2' ? 'bg-violet-50 text-violet-700' : '' }}
                                {{ $d->status === 'RESOLVED' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                {{ $d->status === 'DECLINE' ? 'bg-slate-100 text-slate-500' : '' }}
                            ">{{ $d->status_label }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-[10px] text-slate-400">
                            <span>{{ $d->created_at->diffForHumans() }}</span>
                            <span>·</span>
                            <span class="truncate">{{ $d->reporter_name }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- No pagination since we load all and filter client-side --}}
        </div>

        {{-- RIGHT: Detail --}}
        <div class="flex-1 min-w-0" id="detailPanel">

            {{-- Empty state --}}
            <div class="bg-white border border-slate-200/80 rounded-2xl flex items-center justify-center py-32" id="detailEmpty" style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
                <div class="text-center">
                    <i class="bi bi-file-earmark-text text-3xl text-slate-200 block mb-2"></i>
                    <p class="text-sm text-slate-400">Pilih laporan dari daftar</p>
                </div>
            </div>

            {{-- Detail content --}}
            <div class="hidden" id="detailContent">
                <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden" style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">

                    {{-- Photo --}}
                    <div id="detailPhoto" class="h-44 bg-slate-100 overflow-hidden relative hidden">
                        <img id="detailImg" src="" class="w-full h-full object-cover" alt="">
                    </div>

                    <div class="p-5 space-y-5">

                        {{-- Title --}}
                        <div>
                            <div class="flex items-start justify-between gap-3 mb-1">
                                <h2 class="text-lg font-bold text-slate-900" id="detailTitle"></h2>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0" id="detailBadge"></span>
                            </div>
                            <p class="text-xs text-slate-500" id="detailMeta"></p>
                        </div>

                        {{-- Info --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-[10px] text-slate-400 font-medium mb-1">Lokasi</p>
                                <p class="text-xs font-medium text-slate-800" id="detailLocation"></p>
                                <p class="text-[10px] text-slate-400 mt-0.5 font-mono" id="detailCoords"></p>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-400 font-medium mb-1">Pelapor</p>
                                <p class="text-xs font-medium text-slate-800" id="detailReporter"></p>
                                <p class="text-[10px] text-slate-400 mt-0.5" id="detailTime"></p>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div>
                            <p class="text-[10px] text-slate-400 font-medium mb-1">Deskripsi</p>
                            <p class="text-xs text-slate-600 leading-relaxed" id="detailDesc"></p>
                        </div>

                        {{-- Action --}}
                        <div class="border-t border-slate-100 pt-4">
                            <p class="text-xs font-bold text-slate-700 mb-3">Tindakan</p>
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="text-[10px] text-slate-500 block mb-1">Status</label>
                                    <select id="actionStatus" class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:border-blue-400 bg-white">
                                        <option value="PENDING">Pending</option>
                                        <option value="AWAS">Awas</option>
                                        <option value="SIAGA_1">Siaga 1</option>
                                        <option value="SIAGA_2">Siaga 2</option>
                                        <option value="RESOLVED">Selesai</option>
                                        <option value="DECLINE">Tolak</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[10px] text-slate-500 block mb-1">Jenis Bencana</label>
                                    <select id="actionType" class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:border-blue-400 bg-white">
                                        <option value="flood">Banjir</option>
                                        <option value="fire">Kebakaran</option>
                                        <option value="earthquake">Gempa</option>
                                        <option value="landslide">Longsor</option>
                                        <option value="tsunami">Tsunami</option>
                                        <option value="storm">Badai</option>
                                        <option value="volcano">Gunung Meletus</option>
                                        <option value="unknown">Lainnya</option>
                                    </select>
                                </div>
                            </div>
                            <button type="button" id="btnSaveAction"
                                    class="w-full py-2 text-xs font-semibold text-white rounded-lg cursor-pointer"
                                    style="background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%);">
                                Simpan Perubahan
                            </button>
                        </div>

                        {{-- Mini Map --}}
                        <div id="detailMiniMap" class="w-full h-72 rounded-xl border border-slate-100 overflow-hidden bg-slate-100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Custom Confirm Modal --}}
<div id="confirmModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden opacity-0 transition-opacity duration-200">
    <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl border border-slate-100/80 transform scale-95 transition-transform duration-200">
        <div class="p-6 text-center">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white flex items-center justify-center mx-auto mb-5 shadow-lg shadow-emerald-500/20 transform rotate-3 hover:rotate-0 transition-transform duration-300">
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

<style>
    .tab-btn { background: white; color: #64748b; border: 1px solid #e2e8f0; }
    .tab-btn:hover { color: #1e40af; border-color: #93c5fd; }
    .tab-btn.active { background: #0F172A; color: white; border-color: transparent; }
    .laporan-item.active { background: #EFF6FF !important; }
</style>
@endsection

@section('scripts')
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
                if (confirmed) {
                    submitSaveForm(statusVal);
                }
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
@endsection
