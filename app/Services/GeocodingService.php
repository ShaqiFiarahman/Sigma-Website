<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    /**
     * Reverse geocode coordinates to a human-readable location name.
     *
     * @param float $latitude
     * @param float $longitude
     * @return string
     */
    public function reverseGeocode(float $latitude, float $longitude): string
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'SigmaApp/1.0',
            ])->timeout(5)->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'json',
                'lat' => $latitude,
                'lon' => $longitude,
                'zoom' => 18,
            ]);

            if ($response->successful()) {
                return $response->json('display_name') ?? 'Lokasi tidak diketahui';
            }
        } catch (\Exception $e) {
            Log::warning('Reverse geocoding failed: ' . $e->getMessage());
        }

        return 'Lokasi tidak diketahui';
    }
}
