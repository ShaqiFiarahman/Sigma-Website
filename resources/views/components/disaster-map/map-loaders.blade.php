<script>
    async function loadDisasters() {
        try {
            const response = await fetch('/api/disasters');
            const disasters = await response.json();

            // Set of active disaster IDs from the API response
            const activeIds = new Set(disasters.map(d => Number(d.id)));

            disasters.forEach(d => {
                const color = statusColors[d.status] || '#FFA000';

                // Detect type from disaster_type or fallback from title
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

                // Badge and InfoWindow content setup
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

                // Check if marker already exists
                const existing = disasterMarkers[d.id];
                if (!existing) {
                    // Create new marker
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

                    // If not initial load, this is a new real-time disaster!
                    if (!initialLoad && window.userRole !== 'admin') {
                        showDisasterToast(d);
                    }
                } else {
                    // Marker exists, check if status/data has changed
                    if (existing.data.status !== d.status || existing.data.title !== d.title || existing.data.description !== d.description) {
                        existing.marker.setIcon(markerIcon);
                        existing.marker.setTitle(d.title);

                        // Re-register listener with updated content
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

            // Remove disaster markers that are no longer active
            Object.keys(disasterMarkers).forEach(id => {
                const numId = Number(id);
                if (!activeIds.has(numId)) {
                    disasterMarkers[numId].marker.setMap(null);
                    delete disasterMarkers[numId];
                }
            });

            // Handle initial load zoom to newest disaster
            if (initialLoad) {
                if (disasters.length > 0) {
                    const newest = disasters[0]; // first item is newest due to latest() sorting
                    map.setCenter({ lat: Number(newest.lat), lng: Number(newest.lng) });
                    map.setZoom(14);
                    hasZoomedToNewest = true;
                } else {
                    fitBounds();
                }
                initialLoad = false;
            }
        } catch (error) {
            console.error('Failed to load disasters:', error);
        }
    }

    async function loadShelters() {
        try {
            const response = await fetch('/api/shelters');
            const shelters = await response.json();

            shelters.forEach(s => {
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
                fitBounds();
            }
        } catch (error) {
            console.error('Failed to load shelters:', error);
        }
    }
</script>
