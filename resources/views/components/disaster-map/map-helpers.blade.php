<script>
    function formatLocalDate(isoString) {
        if (!isoString) return '';
        try {
            const date = new Date(isoString);
            if (isNaN(date.getTime())) return isoString;

            const datePart = date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
            const timePart = date.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            }).replace(/\./g, ':');

            const tzPart = new Intl.DateTimeFormat('id-ID', { timeZoneName: 'short' })
                .formatToParts(date)
                .find(part => part.type === 'timeZoneName')?.value || '';

            return `${datePart} ${timePart} ${tzPart}`.trim();
        } catch (e) {
            return isoString;
        }
    }

    function getShortLocation(location) {
        if (!location) return 'Lokasi';
        const parts = location.split(',');
        let short = parts[0].trim();
        if (short.length > 15) {
            short = short.substring(0, 15) + '...';
        }
        return short;
    }

    function getShortReporter(name) {
        if (!name) return '';
        const parts = name.trim().split(/\s+/);
        if (parts.length > 2) {
            return parts.slice(0, 2).join(' ');
        }
        return name;
    }

    const statusColors = {
        'AWAS': '#D32F2F',
        'SIAGA_1': '#EA580C',
        'SIAGA_2': '#7C3AED',
        'PENDING': '#FFA000',
        'RESOLVED': '#10B981',
        'DECLINE': '#64748B'
    };

    const typePaths = {
        'flood': '<path d="M1 9c1.7 0 2.5-1.5 4.2-1.5S7.5 9 9.2 9s2.5-1.5 4.2-1.5S15.7 9 15 9" fill="none" stroke="#FFF" stroke-width="2" stroke-linecap="round"/><path d="M1 13c1.7 0 2.5-1.5 4.2-1.5S7.5 13 9.2 13s2.5-1.5 4.2-1.5" fill="none" stroke="#FFF" stroke-width="2" stroke-linecap="round"/>',
        'fire': '<path d="M8 2C8 2 4 6 4 10c0 2.2 1.8 4 4 4s4-1.8 4-4C12 6 8 2 8 2zM8 12.5c-1.4 0-2.5-1.1-2.5-2.5 0-1.5 2.5-4.5 2.5-4.5s2.5 3 2.5 4.5c0 1.4-1.1 2.5-2.5 2.5z" fill="#FFF"/>',
        'earthquake': '<path d="M2 8h2.5L6 4l2 8 2-6 1.5 4H14" fill="none" stroke="#FFF" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
        'landslide': '<path d="M2 14L8 3l2 4 4-3v10H2z" fill="none" stroke="#FFF" stroke-width="1.5" stroke-linejoin="round"/><circle cx="5" cy="11" r="1.2" fill="#FFF"/><circle cx="9" cy="10" r="1" fill="#FFF"/>',
        'tsunami': '<path d="M1 11c2-3 4-3 6 0s4 3 6 0" fill="none" stroke="#FFF" stroke-width="2" stroke-linecap="round"/><path d="M8 3v5" stroke="#FFF" stroke-width="1.5" stroke-linecap="round"/><path d="M6 5l2-2 2 2" fill="none" stroke="#FFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>',
        'storm': '<path d="M9 2L4 9h4L6 14l6-7H8l3-5z" fill="#FFF"/>',
        'volcano': '<path d="M4 14L7 6l1 2 1-2 1 2 1-2 3 8H4z" fill="none" stroke="#FFF" stroke-width="1.5" stroke-linejoin="round"/><path d="M7 4c0-1 .5-2 1-2s1 1 1 2" fill="none" stroke="#FFF" stroke-width="1" stroke-linecap="round"/>',
        'unknown': '<path d="M8 2l6 12H2L8 2z" fill="none" stroke="#FFF" stroke-width="1.5" stroke-linejoin="round"/><path d="M8 7v3M8 12h.01" stroke="#FFF" stroke-width="1.5" stroke-linecap="round"/>'
    };
</script>
