@extends('layout')
@section('title', 'Dashboard')
@section('subtitle', 'Ikhtisar data dan status laporan terkini.')

@section('content')

    {{-- WELCOME BANNER --}}
    <div class="relative rounded-2xl overflow-hidden mb-8"
        style="background: linear-gradient(135deg, #0A0F1E 0%, #0f1f4a 50%, #1a3068 100%);">

        {{-- Decorative blobs --}}
        <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full opacity-20 pointer-events-none"
            style="background: radial-gradient(circle, #E4F0F6 0%, transparent 70%);"></div>
        <div class="absolute bottom-0 left-1/3 w-96 h-48 opacity-10 pointer-events-none"
            style="background: radial-gradient(ellipse, #3B6FE8 0%, transparent 70%);"></div>
        <div class="absolute top-4 left-1/2 w-48 h-48 rounded-full opacity-5 pointer-events-none"
            style="background: #E4F0F6;"></div>

        <div class="relative z-10 px-12 sm:px-16 py-14 flex flex-col sm:flex-row sm:items-center justify-between gap-12">
            <div class="max-w-3xl">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight mb-3">Selamat datang, Admin.</h2>
                <p class="text-base sm:text-lg leading-relaxed" style="color: rgba(228,240,246,0.7);">
                    Pantau laporan bencana, verifikasi kejadian, dan ambil tindakan cepat dari satu panel kendali terpusat. SIGMA siap membantu koordinasi tim Anda secara real-time hari ini.
                </p>
            </div>
            <div class="shrink-0 hidden sm:flex flex-col items-end gap-1">
                <p class="text-xs font-medium" style="color: rgba(228,240,246,0.45);">Hari ini</p>
                <p class="text-2xl font-bold text-white" id="liveClock">--:--</p>
                <p class="text-xs" style="color: rgba(228,240,246,0.45);" id="liveDate">—</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Chart Area (Left, span 2) --}}
        <div class="lg:col-span-2 bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm flex flex-col h-full"
            style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Statistik Laporan Masuk</h3>
                    <p class="text-xs text-slate-400 mt-0.5">7 hari terakhir</p>
                </div>
                <div
                    class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium text-blue-700 bg-blue-50 border border-blue-100">
                    <i class="bi bi-graph-up-arrow text-blue-500"></i> Tren Mingguan
                </div>
            </div>
            <div class="flex-1 w-full relative min-h-[280px]">
                <canvas id="reportChart"></canvas>
            </div>
        </div>

        {{-- Stat Cards (Right, span 1) --}}
        <div class="flex flex-col gap-4 h-full">
            {{-- Total --}}
            <div class="flex-1 bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm flex items-center justify-between group hover:shadow-md transition-all duration-300 cursor-default"
                style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
                <div>
                    <p class="text-xs text-slate-500 font-medium mb-1">Total Laporan</p>
                    <p class="text-3xl font-bold text-slate-900">{{ $total ?? 0 }}</p>
                    <p class="text-xs text-slate-400 mt-1">Keseluruhan entri</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-slate-600 transition-all duration-300 group-hover:scale-110"
                    style="background: linear-gradient(135deg, #E4F0F6 0%, #C8DFF0 100%);">
                    <i class="bi bi-file-earmark-text text-xl" style="color: #0A0F1E;"></i>
                </div>
            </div>

            {{-- Pending --}}
            <div class="flex-1 bg-white border border-amber-100 rounded-2xl p-5 shadow-sm flex items-center justify-between group hover:shadow-md transition-all duration-300 cursor-default"
                style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
                <div>
                    <p class="text-xs text-slate-500 font-medium mb-1">Menunggu Tindakan</p>
                    <p class="text-3xl font-bold text-amber-600">{{ $pending ?? 0 }}</p>
                    <p class="text-xs text-amber-400 mt-1">Perlu diverifikasi</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center transition-all duration-300 group-hover:scale-110"
                    style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);">
                    <i class="bi bi-hourglass-split text-xl text-amber-600"></i>
                </div>
            </div>

            {{-- Verified --}}
            <div class="flex-1 bg-white border border-emerald-100 rounded-2xl p-5 shadow-sm flex items-center justify-between group hover:shadow-md transition-all duration-300 cursor-default"
                style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
                <div>
                    <p class="text-xs text-slate-500 font-medium mb-1">Terverifikasi</p>
                    <p class="text-3xl font-bold text-emerald-600">{{ $selesai ?? 0 }}</p>
                    <p class="text-xs text-emerald-400 mt-1">Laporan valid</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center transition-all duration-300 group-hover:scale-110"
                    style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);">
                    <i class="bi bi-check-circle text-xl text-emerald-600"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- MAP AREA --}}
    <div class="mt-6 bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden"
        style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">

        {{-- Map Header with gradient accent --}}
        <div class="px-6 py-5 flex items-center justify-between"
            style="background: linear-gradient(135deg, #0A0F1E 0%, #0f1f4a 100%);">
            <div>
                <h3 class="text-base font-semibold text-white">Peta Pantauan Bencana</h3>
                <p class="text-xs mt-0.5" style="color: rgba(228,240,246,0.55);">Pemantauan lokasi bencana terverifikasi
                    secara real-time.</p>
            </div>
        </div>

        {{-- Map Container --}}
        <div class="relative">
            <div id="map" class="w-full h-[480px] z-0"></div>

            {{-- Custom Legend --}}
            <div class="absolute bottom-5 right-5 z-[1000] p-3.5 rounded-xl pointer-events-none"
                style="background: rgba(255,255,255,0.92); backdrop-filter: blur(12px); border: 1px solid rgba(10,15,30,0.10); box-shadow: 0 4px 20px rgba(10,15,30,0.12);">
                <p class="text-[10px] font-bold uppercase tracking-wider mb-2.5" style="color: #94a3b8;">Tingkat Bencana</p>
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.6)]"></span>
                        <span class="text-xs font-semibold text-slate-700">Darurat</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full shadow-[0_0_8px_rgba(30,58,138,0.6)]"
                            style="background: #1e3a8a;"></span>
                        <span class="text-xs font-semibold text-slate-700">Bahaya</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-400 shadow-[0_0_8px_rgba(148,163,184,0.6)]"></span>
                        <span class="text-xs font-semibold text-slate-700">Waspada</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- Google Maps API --}}
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap"
        async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* Custom Map Info Window Styling */
        .gm-style-iw {
            padding: 0 !important;
            max-width: 280px !important;
            border-radius: 16px !important;
        }

        .gm-style-iw-c {
            padding: 0 !important;
            border-radius: 16px !important;
            background-color: transparent !important;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.25) !important;
        }

        .gm-style-iw-d {
            overflow: hidden !important;
            max-height: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .gm-style-iw-tc {
            display: none !important;
        }

        .gm-ui-hover-effect {
            display: none !important;
        }

        @keyframes pulse-red {
            0% {
                transform: translate(-50%, -50%) scale(0.9);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            }

            70% {
                transform: translate(-50%, -50%) scale(1);
                box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
            }

            100% {
                transform: translate(-50%, -50%) scale(0.9);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        @keyframes pulse-blue {
            0% {
                transform: translate(-50%, -50%) scale(0.9);
                box-shadow: 0 0 0 0 rgba(30, 58, 138, 0.7);
            }

            70% {
                transform: translate(-50%, -50%) scale(1);
                box-shadow: 0 0 0 10px rgba(30, 58, 138, 0);
            }

            100% {
                transform: translate(-50%, -50%) scale(0.9);
                box-shadow: 0 0 0 0 rgba(30, 58, 138, 0);
            }
        }

        @keyframes pulse-slate {
            0% {
                transform: translate(-50%, -50%) scale(0.9);
                box-shadow: 0 0 0 0 rgba(148, 163, 184, 0.7);
            }

            70% {
                transform: translate(-50%, -50%) scale(1);
                box-shadow: 0 0 0 10px rgba(148, 163, 184, 0);
            }

            100% {
                transform: translate(-50%, -50%) scale(0.9);
                box-shadow: 0 0 0 0 rgba(148, 163, 184, 0);
            }
        }

        .custom-marker {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 2.5px solid white;
            position: absolute;
            cursor: pointer;
            transform: translate(-50%, -50%);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        .marker-darurat {
            background-color: #ef4444;
            animation: pulse-red 2s infinite;
        }

        .marker-bahaya {
            background-color: #1e3a8a;
            animation: pulse-blue 2s infinite;
        }

        .marker-waspada {
            background-color: #94a3b8;
            animation: pulse-slate 2s infinite;
        }
    </style>

    <script>
        // Live clock
        function updateClock() {
            const now = new Date();
            const clock = document.getElementById('liveClock');
            const date = document.getElementById('liveDate');
            if (clock) clock.textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            if (date) date.textContent = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long' });
        }
        updateClock();
        setInterval(updateClock, 1000);

        let map;
        let infoWindow;

        window.closeMapPopup = function () {
            if (infoWindow) infoWindow.close();
        };

        function initMap() {
            // --- CHART.JS ---
            const ctx = document.getElementById('reportChart').getContext('2d');
            const labels = {!! json_encode($chartLabels ?? []) !!};
            const data = {!! json_encode($chartData ?? []) !!};

            const gradient = ctx.createLinearGradient(0, 0, 0, 280);
            gradient.addColorStop(0, 'rgba(59,111,232,0.18)');
            gradient.addColorStop(1, 'rgba(59,111,232,0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Laporan',
                        data: data,
                        borderColor: '#3B6FE8',
                        backgroundColor: gradient,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#3B6FE8',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        fill: true,
                        tension: 0.45
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0A0F1E',
                            titleColor: '#E4F0F6',
                            bodyColor: 'rgba(228,240,246,0.7)',
                            padding: 10,
                            cornerRadius: 10,
                            displayColors: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(10,15,30,0.05)' },
                            border: { display: false },
                            ticks: { precision: 0, color: '#94a3b8', font: { size: 11 } }
                        },
                        x: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: { color: '#94a3b8', font: { size: 11 } }
                        }
                    }
                }
            });

            // --- GOOGLE MAPS ---
            class CustomMarker extends google.maps.OverlayView {
                constructor(position, element, map, onClick) {
                    super();
                    this.position = position;
                    this.element = element;
                    this.element.addEventListener('click', (e) => {
                        e.stopPropagation();
                        if (onClick) onClick();
                    });
                    this.setMap(map);
                }
                onAdd() { this.getPanes().overlayMouseTarget.appendChild(this.element); }
                draw() {
                    const pos = this.getProjection().fromLatLngToDivPixel(this.position);
                    if (pos) {
                        this.element.style.left = pos.x + 'px';
                        this.element.style.top = pos.y + 'px';
                    }
                }
                onRemove() { if (this.element.parentNode) this.element.parentNode.removeChild(this.element); }
                getPosition() { return this.position; }
            }

            const center = { lat: -7.7956, lng: 110.3695 };
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 11,
                center: center,
                styles: [
                    { featureType: "administrative", elementType: "geometry", stylers: [{ visibility: "off" }] },
                    { featureType: "poi", stylers: [{ visibility: "off" }] },
                    { featureType: "road", elementType: "labels.icon", stylers: [{ visibility: "off" }] },
                    { featureType: "transit", stylers: [{ visibility: "off" }] },
                    { featureType: "water", elementType: "geometry", stylers: [{ color: "#c8dff0" }] },
                    { featureType: "landscape", elementType: "geometry", stylers: [{ color: "#f0f4f8" }] },
                ],
                disableDefaultUI: false,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true,
                gestureHandling: 'greedy'
            });

            // Close InfoWindow when clicking anywhere on map
            map.addListener('click', () => {
                if (infoWindow) infoWindow.close();
            });

            infoWindow = new google.maps.InfoWindow();

            const disasterData = {!! json_encode(session('laporans', [])) !!};
            const geoMapping = {
                'Bantul': { lat: -7.8897, lng: 110.3289 },
                'Sleman': { lat: -7.7233, lng: 110.3650 },
                'Kulon Progo': { lat: -7.8333, lng: 110.1583 },
                'Gunung Kidul': { lat: -7.9999, lng: 110.6000 },
                'Yogyakarta': { lat: -7.7956, lng: 110.3695 }
            };

            disasterData.forEach(item => {
                if (item.status === 'Verified') {
                    let coords = geoMapping[item.lokasi] || {
                        lat: -7.7956 + (Math.random() * 0.1 - 0.05),
                        lng: 110.3695 + (Math.random() * 0.1 - 0.05)
                    };

                    let colorClass = item.tingkat_bencana === 'Darurat' ? 'marker-darurat' :
                        (item.tingkat_bencana === 'Bahaya' ? 'marker-bahaya' : 'marker-waspada');

                    const markerEl = document.createElement('div');
                    markerEl.className = `custom-marker ${colorClass}`;
                    markerEl.title = item.judul;

                    const levelBadgeBg = item.tingkat_bencana === 'Darurat' ? 'background:#ef4444' :
                        (item.tingkat_bencana === 'Bahaya' ? 'background:#1e3a8a' : 'background:#64748b');

                    const popupContent = `
                            <div style="width:276px; font-family:system-ui,sans-serif; border-radius:16px; overflow:hidden; position:relative;">
                                <button type="button"
                                    style="position:absolute;top:12px;right:12px;width:26px;height:26px;border-radius:50%;background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.3);color:white;cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:110;font-size:13px;"
                                    onclick="closeMapPopup()">✕</button>
                                <div style="padding:18px 18px 14px; background:linear-gradient(135deg, #0A0F1E 0%, #0f1f4a 100%);">
                                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;padding-right:28px;">
                                        <span style="font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:rgba(228,240,246,0.55);">${item.tanggal}</span>
                                        <span style="padding:2px 8px;border-radius:6px;font-size:9px;font-weight:800;color:white;${levelBadgeBg}">${item.tingkat_bencana}</span>
                                    </div>
                                    <h4 style="font-size:14px;font-weight:700;color:white;line-height:1.35;margin:0;">${item.judul}</h4>
                                </div>
                                <div style="padding:14px 18px 18px; background:white;">
                                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                                        <div style="width:30px;height:30px;border-radius:50%;background:#E4F0F6;display:flex;align-items:center;justify-content:center;shrink:0;">
                                            <i class="bi bi-geo-alt" style="color:#1e3a8a;font-size:13px;"></i>
                                        </div>
                                        <div>
                                            <p style="font-size:9px;color:#94a3b8;font-weight:700;letter-spacing:.08em;text-transform:uppercase;margin:0 0 2px;">Lokasi</p>
                                            <p style="font-size:12px;color:#0f172a;font-weight:700;margin:0;">${item.lokasi}</p>
                                        </div>
                                    </div>
                                    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:10px;margin-bottom:14px;">
                                        <p style="font-size:11px;color:#475569;line-height:1.6;margin:0;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">${item.deskripsi}</p>
                                    </div>
                                    <a href="/laporan/detail/${item.id}"
                                       style="display:flex;align-items:center;justify-content:center;gap:6px;width:100%;padding:9px;background:linear-gradient(135deg,#0A0F1E 0%,#1e3a8a 100%);color:white;text-decoration:none;font-size:12px;font-weight:700;border-radius:10px;transition:opacity .2s;"
                                       onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                                        Detail Laporan <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        `;

                    const marker = new CustomMarker(coords, markerEl, map, () => {
                        infoWindow.setContent(popupContent);
                        infoWindow.setPosition(coords);
                        infoWindow.open(map);
                    });
                }
            });
        }
    </script>
@endsection