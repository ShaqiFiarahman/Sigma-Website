<div class="relative rounded-2xl overflow-hidden mb-8"
    style="background: linear-gradient(135deg, #0A0F1E 0%, #0f1f4a 50%, #1a3068 100%);">

    <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full opacity-20 pointer-events-none"
        style="background: radial-gradient(circle, #E4F0F6 0%, transparent 70%);"></div>
    <div class="absolute bottom-0 left-1/3 w-96 h-48 opacity-10 pointer-events-none"
        style="background: radial-gradient(ellipse, #3B6FE8 0%, transparent 70%);"></div>

    <div class="absolute inset-0 opacity-15 pointer-events-none">
        <img src="{{ asset('images/indonesia_map.webp') }}" class="w-full h-full object-cover object-center"
            alt="" loading="lazy" decoding="async">
    </div>

    <div class="relative z-10 px-12 sm:px-16 py-14 flex flex-col sm:flex-row sm:items-center justify-between gap-12">
        <div class="max-w-3xl">
            <h2 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight mb-3">Halo,
                {{ auth()->user()->full_name ?? 'Pengguna' }}.
            </h2>
            <p class="text-sm sm:text-base leading-relaxed" style="color: rgba(228,240,246,0.7);">
                Pantau informasi bencana dan laporkan kejadian di sekitar Anda secara cepat.
            </p>
        </div>
        <div class="shrink-0 hidden sm:flex flex-col items-end gap-1">
            <p class="text-2xl font-bold text-white" id="liveClock">--:--</p>
            <p class="text-xs" style="color: rgba(228,240,246,0.45);" id="liveDate">—</p>
            <p class="text-xs font-medium text-white/70 mt-1 flex items-center gap-1">
                <i class="bi bi-geo-alt-fill text-[10px]"></i> <span id="userCity">Mencari lokasi...</span>
            </p>
            <p class="text-xs font-medium text-white/70 flex items-center gap-1">
                <i id="weatherIcon" class="bi bi-cloud text-[10px]"></i> <span id="userWeather">Memuat cuaca...</span>
            </p>
        </div>
    </div>
</div>

<script>
    (function() {
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const clockEl = document.getElementById('liveClock');
            if (clockEl) clockEl.textContent = `${hours}:${minutes}`;

            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const dateEl = document.getElementById('liveDate');
            if (dateEl) dateEl.textContent = now.toLocaleDateString('id-ID', options);
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Geolocation
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
        } else {
            const cityEl = document.getElementById('userCity');
            if (cityEl) cityEl.textContent = 'Geolocation tidak didukung';
            
            const weatherEl = document.getElementById('userWeather');
            if (weatherEl) weatherEl.textContent = 'Cuaca tidak tersedia';
        }

        function successCallback(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            // Reverse geocoding using Nominatim (free)
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=14&addressdetails=1`)
                .then(response => response.json())
                .then(data => {
                    const city = data.address.city || data.address.town || data.address.municipality || data.address.suburb || data.address.village || data.address.state || 'Lokasi tidak diketahui';
                    const cityEl = document.getElementById('userCity');
                    if (cityEl) cityEl.textContent = city;

                    window.userCityName = city;
                })
                .catch(error => {
                    console.error('Error fetching location:', error);
                    const cityEl = document.getElementById('userCity');
                    if (cityEl) cityEl.textContent = 'Gagal memuat lokasi';
                });

            // Check nearby disasters if defined on user dashboard
            if (typeof checkNearbyDisasters === 'function') {
                checkNearbyDisasters(lat, lng);
            }

            // Fetch weather
            fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lng}&current_weather=true`)
                .then(response => response.json())
                .then(data => {
                    const weather = data.current_weather;
                    const temp = Math.round(weather.temperature);
                    const code = weather.weathercode;
                    
                    const weatherEl = document.getElementById('userWeather');
                    const iconEl = document.getElementById('weatherIcon');
                    
                    if (weatherEl) {
                        weatherEl.textContent = `${temp}°C · ${getWeatherDesc(code)}`;
                    }
                    if (iconEl) {
                        iconEl.className = `bi ${getWeatherIcon(code)} text-[10px]`;
                    }
                })
                .catch(error => {
                    console.error('Error fetching weather:', error);
                    const weatherEl = document.getElementById('userWeather');
                    if (weatherEl) weatherEl.textContent = 'Gagal memuat cuaca';
                });
        }

        function getWeatherDesc(code) {
            const descriptions = {
                0: 'Cerah',
                1: 'Cerah Berawan',
                2: 'Berawan',
                3: 'Mendung',
                45: 'Berkabut',
                48: 'Berkabut',
                51: 'Gerimis Ringan',
                53: 'Gerimis',
                55: 'Gerimis Lebat',
                61: 'Hujan Ringan',
                63: 'Hujan',
                65: 'Hujan Lebat',
                71: 'Salju Ringan',
                73: 'Salju',
                75: 'Salju Lebat',
                80: 'Hujan Deras Ringan',
                81: 'Hujan Deras',
                82: 'Hujan Deras Sangat Lebat',
                95: 'Badai Petir',
                96: 'Badai Petir dengan Hujan Es',
                99: 'Badai Petir dengan Hujan Es Lebat'
            };
            return descriptions[code] || 'Unknown';
        }

        function getWeatherIcon(code) {
            if (code === 0) return 'bi-brightness-high';
            if (code >= 1 && code <= 3) return 'bi-cloud-sun';
            if (code === 45 || code === 48) return 'bi-cloud-fog';
            if (code >= 51 && code <= 55) return 'bi-cloud-drizzle';
            if (code >= 61 && code <= 65) return 'bi-cloud-rain';
            if (code >= 71 && code <= 75) return 'bi-cloud-snow';
            if (code >= 80 && code <= 82) return 'bi-cloud-rain-heavy';
            if (code >= 95) return 'bi-cloud-lightning';
            return 'bi-cloud';
        }

        function errorCallback(error) {
            console.error('Geolocation error:', error);
            const cityEl = document.getElementById('userCity');
            if (cityEl) cityEl.textContent = 'Akses lokasi ditolak';
            
            const weatherEl = document.getElementById('userWeather');
            if (weatherEl) weatherEl.textContent = 'Cuaca tidak tersedia';

            if (typeof updateWarningBanner === 'function') {
                updateWarningBanner(0);
            }
        }
    })();
</script>
