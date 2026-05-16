<?php

namespace App\Http\Controllers;

use App\Models\Disaster;
use Illuminate\Http\Request;

class MapController extends Controller
{
    /**
     * Show interactive map with disaster markers
     */
    public function index()
    {
        return view('user.map');
    }

    /**
     * Show search & filter disasters page (sesuai Android SearchDisasterScreen)
     */
    public function search()
    {
        $disasters = Disaster::where('status', '!=', Disaster::STATUS_DECLINE)
            ->latest()
            ->get();

        return view('user.search', compact('disasters'));
    }

    /**
     * Show shelter info page (sesuai Android ShelterInfoScreen)
     */
    public function shelterPage()
    {
        $shelters = $this->getShelterData();
        return view('user.shelter', compact('shelters'));
    }

    /**
     * API: Get disasters as JSON for map markers
     */
    public function disasters()
    {
        $disasters = Disaster::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('status', '!=', Disaster::STATUS_DECLINE)
            ->latest()
            ->get()
            ->map(fn($d) => [
                'id'          => $d->id,
                'title'       => $d->title,
                'description' => \Illuminate\Support\Str::limit($d->description, 100),
                'lat'         => $d->latitude,
                'lng'         => $d->longitude,
                'status'      => $d->status,
                'statusLabel' => $d->status_label,
                'reporter'    => $d->reporter_name,
                'date'        => $d->created_at?->format('d M Y H:i'),
                'type'        => 'disaster',
            ]);

        return response()->json($disasters);
    }

    /**
     * API: Get shelters as JSON for map markers
     * Data sesuai Android ShelterInfoScreen (mock data)
     */
    public function shelters()
    {
        return response()->json($this->getShelterData());
    }

    /**
     * Shelter data (sesuai Android ShelterInfoScreen mock)
     */
    private function getShelterData(): array
    {
        return [
            ['name' => 'Stadion UNS',            'distance' => '1.2 km', 'capacity' => '80/100',  'status' => 'Tersedia', 'lat' => -7.556303,  'lng' => 110.8580877, 'logistics' => ['Sembako', 'Air Mineral', 'Selimut']],
            ['name' => 'Taman Cerdas Jebres',    'distance' => '1.5 km', 'capacity' => '50/50',   'status' => 'Penuh',    'lat' => -7.5541321, 'lng' => 110.8536159, 'logistics' => ['Popok Bayi', 'Susu Formula', 'Obat-obatan']],
            ['name' => 'Solo Techno Park',       'distance' => '2.2 km', 'capacity' => '30/200',  'status' => 'Tersedia', 'lat' => -7.5560692, 'lng' => 110.8538666, 'logistics' => ['Pakaian Layak Pakai', 'Alat Mandi']],
            ['name' => 'SAR UNS',                'distance' => '0.8 km', 'capacity' => '10/40',   'status' => 'Tersedia', 'lat' => -7.5615699, 'lng' => 110.8594894, 'logistics' => ['Makanan Instan', 'Tikar']],
            ['name' => 'Javanologi UNS',         'distance' => '0.7 km', 'capacity' => '127/250', 'status' => 'Tersedia', 'lat' => -7.556998,  'lng' => 110.8598277, 'logistics' => ['Makanan Instan', 'Alat Mandi', 'Pakaian Layak Pakai']],
            ['name' => 'UNS Tower',              'distance' => '0.45 km','capacity' => '45/125',  'status' => 'Tersedia', 'lat' => -7.5638533, 'lng' => 110.8555975, 'logistics' => ['Susu Formula', 'Obat-obatan', 'Selimut']],
            ['name' => 'Asrama Mahasiswa UNS',   'distance' => '2.4 km', 'capacity' => '300/300', 'status' => 'Penuh',    'lat' => -7.554193,  'lng' => 110.865799,  'logistics' => ['Alat Mandi', 'Sembako', 'Sleeping Bag']],
            ['name' => 'Sekolah Vokasi UNS',     'distance' => '2.6 km', 'capacity' => '145/340', 'status' => 'Tersedia', 'lat' => -7.559502,  'lng' => 110.8383739, 'logistics' => ['Makanan Instan', 'Obat-obatan', 'Air Mineral']],
        ];
    }
}
