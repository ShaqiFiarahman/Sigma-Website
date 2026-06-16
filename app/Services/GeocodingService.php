<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    // Geocoding Terbalik
    public function reverseGeocode(float $latitude, float $longitude): string
    {
        try {
            // Kirim HTTP request ke OpenStreetMap Nominatim API untuk reverse geocode
            $response = Http::withHeaders([
                'User-Agent' => 'SigmaApp/1.0',
            ])->timeout(5)->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'json',
                'lat' => $latitude,
                'lon' => $longitude,
                'zoom' => 18,
            ]);

            // Kalau request berhasil, kembalikan nama lokasi yang didapatkan
            if ($response->successful()) {
                return $response->json('display_name') ?? 'Lokasi tidak diketahui';
            }
        } catch (\Exception $e) {
            Log::warning('Reverse geocoding failed: ' . $e->getMessage());
        }

        return 'Lokasi tidak diketahui';
    }
}
