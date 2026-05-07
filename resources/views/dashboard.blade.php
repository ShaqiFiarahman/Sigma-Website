@extends('layout')
@section('title', 'Dashboard')
@section('subtitle', 'Ikhtisar data dan status laporan terkini.')

@section('content')

    {{-- WELCOME BANNER --}}
    <div
        class="bg-slate-900 rounded-xl border border-slate-800 p-8 sm:p-10 flex flex-col justify-center relative overflow-hidden mb-8">
        <div class="relative z-10">
            <h2 class="text-2xl sm:text-3xl font-bold text-white tracking-tight mb-3">Selamat datang, Admin.</h2>
            <p class="text-slate-400 text-sm sm:text-base max-w-xl leading-relaxed">
                Sistem Informasi Tanggap Bencana memberikan Anda kendali penuh untuk memantau laporan masyarakat,
                memverifikasi kejadian, dan mengambil tindakan cepat.
            </p>
        </div>

        {{-- Subtle background element --}}
        <div
            class="absolute right-0 top-0 bottom-0 w-1/3 bg-gradient-to-l from-primary-600/20 to-transparent pointer-events-none">
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Chart Area (Left, span 2) --}}
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-xl p-6 shadow-sm flex flex-col h-full">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-base font-semibold text-slate-900">Statistik Laporan Masuk (7 Hari Terakhir)</h3>
            </div>
            <div class="flex-1 w-full relative min-h-[300px]">
                <canvas id="reportChart"></canvas>
            </div>
        </div>

        {{-- Stat Cards (Right, span 1) --}}
        <div class="flex flex-col gap-4 h-full">
            {{-- Total --}}
            <div
                class="flex-1 bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex items-center justify-between group hover:border-slate-300 transition-colors">
                <div>
                    <p class="text-xs text-slate-500 font-medium mb-1">Total Laporan</p>
                    <p class="text-2xl font-bold text-slate-900">{{ $total ?? 0 }}</p>
                </div>
                <div
                    class="w-12 h-12 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-600 group-hover:bg-white transition-colors">
                    <i class="bi bi-file-earmark-text text-xl"></i>
                </div>
            </div>

            {{-- Pending --}}
            <div
                class="flex-1 bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex items-center justify-between group hover:border-amber-300 transition-colors">
                <div>
                    <p class="text-xs text-slate-500 font-medium mb-1">Menunggu Tindakan</p>
                    <p class="text-2xl font-bold text-slate-900">{{ $pending ?? 0 }}</p>
                </div>
                <div
                    class="w-12 h-12 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 group-hover:bg-white transition-colors">
                    <i class="bi bi-hourglass-split text-xl"></i>
                </div>
            </div>

            {{-- Verified --}}
            <div
                class="flex-1 bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex items-center justify-between group hover:border-emerald-300 transition-colors">
                <div>
                    <p class="text-xs text-slate-500 font-medium mb-1">Terverifikasi</p>
                    <p class="text-2xl font-bold text-slate-900">{{ $selesai ?? 0 }}</p>
                </div>
                <div
                    class="w-12 h-12 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 group-hover:bg-white transition-colors">
                    <i class="bi bi-check-circle text-xl"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- MAP AREA --}}
    <div class="mt-8 bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-base font-semibold text-slate-900">Peta Pantauan Bencana</h3>
                <p class="text-xs text-slate-500 mt-1">Pemantauan lokasi bencana terverifikasi secara real-time.</p>
            </div>
        </div>
        
        {{-- Map Container --}}
        <div class="relative group">
            <div id="map" class="w-full h-[500px] rounded-xl border border-slate-200 z-0 shadow-inner"></div>
            
            {{-- Custom Legend --}}
            <div class="absolute bottom-6 right-6 z-[1000] bg-white/90 backdrop-blur-md border border-slate-200 p-3 rounded-xl shadow-xl pointer-events-none">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Tingkat Bencana</p>
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.5)]"></span>
                        <span class="text-xs font-semibold text-slate-700">Darurat</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-blue-900 shadow-[0_0_8px_rgba(30,58,138,0.5)]"></span>
                        <span class="text-xs font-semibold text-slate-700">Bahaya</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-slate-400 shadow-[0_0_8px_rgba(148,163,184,0.5)]"></span>
                        <span class="text-xs font-semibold text-slate-700">Waspada</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- Google Maps API --}}
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        /* Custom Map Info Window Styling */
        .gm-style-iw {
            padding: 0 !important;
            max-width: 280px !important;
            border-radius: 12px !important;
        }
        .gm-style-iw-d {
            overflow: hidden !important;
            max-height: none !important;
        }
        .gm-style-iw-tc {
            display: none !important;
        }
        .gm-ui-hover-effect {
            display: none !important;
        }

        @keyframes marker-pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
        .pulse-red { animation: marker-pulse 2s infinite; }

        /* Custom marker container */
        .custom-marker {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
    </style>

    <script>
        let map;
        let infoWindow;

        function initMap() {
            // --- CHART.JS ---
            const ctx = document.getElementById('reportChart').getContext('2d');
            const labels = {!! json_encode($chartLabels ?? []) !!};
            const data = {!! json_encode($chartData ?? []) !!};
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Laporan',
                        data: data,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#4f46e5',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' }, border: { display: false } },
                        x: { grid: { display: false }, border: { display: false } }
                    }
                }
            });

            // --- GOOGLE MAPS ---
            const center = { lat: -7.7956, lng: 110.3695 };
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 11,
                center: center,
                styles: [
                    { "featureType": "administrative", "elementType": "geometry", "stylers": [{ "visibility": "off" }] },
                    { "featureType": "poi", "stylers": [{ "visibility": "off" }] },
                    { "featureType": "road", "elementType": "labels.icon", "stylers": [{ "visibility": "off" }] },
                    { "featureType": "transit", "stylers": [{ "visibility": "off" }] }
                ],
                disableDefaultUI: false,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true
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
                    
                    let color = item.tingkat_bencana === 'Darurat' ? '#ef4444' : 
                                (item.tingkat_bencana === 'Bahaya' ? '#1e3a8a' : '#94a3b8');

                    // Using simple SVG marker for consistent styling
                    const marker = new google.maps.Marker({
                        position: coords,
                        map: map,
                        icon: {
                            path: google.maps.SymbolPath.CIRCLE,
                            fillColor: color,
                            fillOpacity: 1,
                            strokeWeight: 2,
                            strokeColor: '#ffffff',
                            scale: 8
                        },
                        title: item.judul
                    });

                    const btnColor = item.tingkat_bencana === 'Darurat' ? 'bg-red-600 hover:bg-red-700' : 
                                    (item.tingkat_bencana === 'Bahaya' ? 'bg-blue-900 hover:bg-blue-950' : 'bg-slate-800 hover:bg-slate-900');

                    const popupContent = `
                        <div class="overflow-hidden font-sans" style="width: 280px">
                            <div class="p-5 bg-slate-900 text-white relative">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-bold uppercase tracking-widest opacity-60">${item.tanggal}</span>
                                    <span class="px-2 py-0.5 rounded text-[9px] font-extrabold ${
                                        item.tingkat_bencana === 'Darurat' ? 'bg-red-500' : 
                                        (item.tingkat_bencana === 'Bahaya' ? 'bg-blue-600' : 'bg-slate-500')
                                    }">${item.tingkat_bencana}</span>
                                </div>
                                <h4 class="font-bold text-base leading-tight pr-6">${item.judul}</h4>
                            </div>
                            <div class="p-5 bg-white">
                                <div class="flex items-center gap-2 mb-4">
                                    <div class="w-7 h-7 rounded-full bg-slate-50 flex items-center justify-center border border-slate-100">
                                        <i class="bi bi-geo-alt text-primary-600 text-xs"></i>
                                    </div>
                                    <p class="text-xs text-slate-700 font-bold">${item.lokasi}</p>
                                </div>
                                <p class="text-[11px] text-slate-500 leading-relaxed mb-5 line-clamp-3">${item.deskripsi.substring(0, 100)}...</p>
                                <a href="/laporan/detail/${item.id}" class="block w-full py-2.5 ${btnColor} text-white text-center text-xs font-bold rounded-lg transition-all shadow-sm">
                                    Detail Laporan
                                </a>
                            </div>
                        </div>
                    `;

                    marker.addListener("mouseover", () => {
                        infoWindow.setContent(popupContent);
                        infoWindow.open(map, marker);
                    });
                }
            });
        }
    </script>
@endsection