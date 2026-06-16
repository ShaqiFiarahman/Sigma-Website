<script>
    async function loadDisasters() {
        console.log('loadDisasters: Function called');
        try {
            const url = '{{ route("api.disasters") }}';
            console.log('loadDisasters: Fetching from URL:', url);
            const response = await fetch(url);
            console.log('loadDisasters: Fetch response status:', response.status);
            
            if (!response.ok) {
                console.error('loadDisasters: Fetch response was not OK:', response.status, response.statusText);
                return;
            }

            const disasters = await response.json();
            console.log('loadDisasters: Fetched disasters count:', disasters.length, disasters);

            // simpen id bencana yang aktif dari api response
            const activeIds = new Set(disasters.map(d => Number(d.id)));

            disasters.forEach(d => {
                const color = statusColors[d.status] || '#FFA000';

                // deteksi tipe bencana dari disaster_type atau fallback dari title
                let dtype = (d.disaster_type || 'unknown').toLowerCase();
                if (dtype === 'unknown') {
                    const t = d.title.toLowerCase();
                    if (t.includes('banjir')) dtype = 'flood';
                    else if (t.includes('kebakaran') || t.includes('api')) dtype = 'fire';
                    else if (t.includes('gempa')) dtype = 'earthquake';
                    else if (t.includes('longsor')) dtype = 'landslide';
                    else if (t.includes('tsunami')) dtype = 'tsunami';
                    else if (t.includes('gunung') || t.includes('vulkan')) dtype = 'volcano';
                    else if (t.includes('badai') || t.includes('angin') || t.includes('petir')) dtype = 'storm';
                }
                const iconPath = typePaths[dtype] || typePaths['unknown'];

                // setup warna badge sama konten infowindow
                let badgeBg, badgeColor;
                switch (d.status) {
                    case 'AWAS':    badgeBg = '#FEF2F2'; badgeColor = '#B91C1C'; break;
                    case 'SIAGA_1': badgeBg = '#FFF7ED'; badgeColor = '#C2410C'; break;
                    case 'SIAGA_2': badgeBg = '#F5F3FF'; badgeColor = '#6D28D9'; break;
                    case 'RESOLVED': badgeBg = '#F0FDF4'; badgeColor = '#15803D'; break;
                    default:        badgeBg = '#FFFBEB'; badgeColor = '#B45309'; break;
                }

                const dateParts = formatLocalDate(d.date).split(' ');
                const displayDate = dateParts.length >= 4
                    ? `${dateParts[0]} ${dateParts[1]}, ${dateParts[3]} ${dateParts[4] || ''}`
                    : formatLocalDate(d.date);
                const shortReporter = getShortReporter(d.reporter);

                const markerIcon = {
                    url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                        <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 44 44">
                          <style>
                            @keyframes glow { 0%,100%{opacity:0.1;r:20} 50%{opacity:0.3;r:21} }
                            .pulse-glow { animation: glow 2s ease-in-out infinite; }
                          </style>
                          <circle cx="22" cy="22" r="20" fill="${color}" class="pulse-glow"/>
                          <circle cx="22" cy="22" r="15" fill="${color}" opacity="0.25"/>
                          <circle cx="22" cy="22" r="11" fill="${color}"/>
                          <g transform="translate(14, 14) scale(1)" fill="none">
                            ${iconPath}
                          </g>
                        </svg>
                    `),
                    scaledSize: new google.maps.Size(44, 44),
                    anchor: new google.maps.Point(22, 22),
                };

                // cek kalo marker udah dibikin sebelumnya
                const existing = disasterMarkers[d.id];
                if (!existing) {
                    console.log('loadDisasters: Creating marker for disaster ID:', d.id, 'at', d.lat, d.lng);
                    // buat marker baru
                    const marker = new google.maps.Marker({
                        position: { lat: Number(d.lat), lng: Number(d.lng) },
                        map: map,
                        title: d.title,
                        icon: markerIcon,
                        optimized: false
                    });

                    marker.addListener('click', () => {
                        infoWindow.setContent(buildDisasterPopup(d, badgeBg, badgeColor, shortReporter, displayDate));
                        infoWindow.open(map, marker);
                    });

                    disasterMarkers[d.id] = {
                        marker: marker,
                        data: d,
                        badgeBg: badgeBg,
                        badgeColor: badgeColor,
                        shortReporter: shortReporter,
                        displayDate: displayDate
                    };

                    // kalo bukan initial load, berarti ini bencana baru pas jalan (real-time)
                    if (!initialLoad && window.userRole !== 'admin') {
                        showDisasterToast(d);
                    }
                } else {
                    // marker udah ada, cek kalo status atau datanya berubah
                    if (existing.data.status !== d.status || existing.data.title !== d.title || existing.data.description !== d.description) {
                        console.log('loadDisasters: Updating marker for disaster ID:', d.id);
                        existing.marker.setIcon(markerIcon);
                        existing.marker.setTitle(d.title);

                        // daftarin ulang click listener pake konten yang paling baru
                        google.maps.event.clearInstanceListeners(existing.marker);
                        existing.marker.addListener('click', () => {
                            infoWindow.setContent(buildDisasterPopup(d, badgeBg, badgeColor, shortReporter, displayDate));
                            infoWindow.open(map, existing.marker);
                        });

                        existing.data = d;
                        existing.badgeBg = badgeBg;
                        existing.badgeColor = badgeColor;
                        existing.shortReporter = shortReporter;
                        existing.displayDate = displayDate;
                    }
                }
            });

            // hapus marker bencana yang udah ga aktif
            Object.keys(disasterMarkers).forEach(id => {
                const numId = Number(id);
                if (!activeIds.has(numId)) {
                    console.log('loadDisasters: Removing inactive marker for ID:', numId);
                    disasterMarkers[numId].marker.setMap(null);
                    delete disasterMarkers[numId];
                }
            });

            // zoom ke bencana paling baru pas awal load
            if (initialLoad) {
                if (disasters.length > 0) {
                    const newest = disasters[0]; // data pertama adalah yang paling baru karena diurutkan pake latest()
                    console.log('loadDisasters: Initial load zoom to newest disaster at:', newest.lat, newest.lng);
                    map.setCenter({ lat: Number(newest.lat), lng: Number(newest.lng) });
                    map.setZoom(14);
                    hasZoomedToNewest = true;
                } else {
                    console.log('loadDisasters: No disasters found, fitting bounds');
                    fitBounds();
                }
                initialLoad = false;
            }
        } catch (error) {
            console.error('Failed to load disasters:', error);
        }
    }

    async function loadShelters() {
        console.log('loadShelters: Function called');
        try {
            const url = '{{ route("api.shelters") }}';
            console.log('loadShelters: Fetching from URL:', url);
            const response = await fetch(url);
            console.log('loadShelters: Fetch response status:', response.status);

            if (!response.ok) {
                console.error('loadShelters: Fetch response was not OK:', response.status, response.statusText);
                return;
            }

            const shelters = await response.json();
            console.log('loadShelters: Fetched shelters count:', shelters.length, shelters);

            shelters.forEach(s => {
                console.log('loadShelters: Creating marker for shelter ID:', s.id, 'at', s.lat, s.lng);
                const marker = new google.maps.Marker({
                    position: { lat: Number(s.lat), lng: Number(s.lng) },
                    map: map,
                    title: s.name,
                    icon: {
                        url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#10B981" viewBox="0 0 16 16"><path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5z"/></svg>'),
                        scaledSize: new google.maps.Size(24, 24),
                    },
                });

                marker.addListener('click', () => {
                    infoWindow.setContent(buildShelterPopup(s));
                    infoWindow.open(map, marker);
                });

                shelterMarkers.push(marker);
            });

            if (!hasZoomedToNewest) {
                console.log('loadShelters: Fitting bounds');
                fitBounds();
            }
        } catch (error) {
            console.error('Failed to load shelters:', error);
        }
    }
</script>
