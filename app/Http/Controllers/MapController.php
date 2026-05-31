<?php

namespace App\Http\Controllers;

use App\Models\Disaster;
use App\Models\Shelter;
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
        $user = auth()->user();
        $role = $user?->role ?? 'MASYARAKAT';

        $query = Disaster::latest();

        if (strtolower($role) !== 'admin') {
            // Citizens/Volunteers can see active public disasters (SIAGA_1, SIAGA_2, AWAS - i.e. not RESOLVED/selesai)
            // AND their own reports (including pending, rejected, or resolved)
            $query->where(function($q) use ($user) {
                $q->whereIn('status', [
                    'SIAGA_1',
                    'SIAGA_2',
                    'AWAS'
                ]);
                if ($user) {
                    $q->orWhere('user_id', $user->id);
                }
            });
        }

        $disasters = $query->get();

        return view('laporan.search', compact('disasters'));
    }

    /**
     * Show shelter info page (sesuai Android ShelterInfoScreen)
     */
    public function shelterPage()
    {
        $query = Shelter::query();

        // Server-side filter
        if (request('status')) {
            $query->where('status', request('status'));
        }
        if (request('q')) {
            $query->where('name', 'like', '%' . request('q') . '%');
        }
        if (request('sort') === 'terdekat') {
            // Default order, client will sort
        }

        $shelters = $query->paginate(6)->through(fn($s) => $this->shelterToArray($s));
        return view('shelter.index', compact('shelters'));
    }

    /**
     * API: Get disasters as JSON for map markers
     */
    public function disasters()
    {
        $disasters = Disaster::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereIn('status', [
                'SIAGA_1',
                'SIAGA_2',
                'AWAS'
            ])
            ->latest()
            ->get()
            ->map(fn($d) => [
                'id'            => $d->id,
                'title'         => $d->title,
                'description'   => \Illuminate\Support\Str::limit($d->description, 100),
                'lat'           => $d->latitude,
                'lng'           => $d->longitude,
                'status'        => $d->status,
                'statusLabel'   => $d->status_label,
                'reporter'      => $d->reporter_name,
                'date'          => $d->created_at?->toIso8601String(),
                'type'          => 'disaster',
                'disaster_type' => $d->disaster_type,
                'type_icon'     => $d->type_icon,
                'type_color'    => $d->type_color,
                'type_name'     => $d->type_name,
                'photo'         => $d->photo_url ? (json_decode($d->photo_url)[0] ?? null) : null,
            ]);

        return response()->json($disasters);
    }

    /**
     * API: Get shelters as JSON for map markers
     */
    public function shelters()
    {
        $shelters = Shelter::all()->map(fn($s) => $this->shelterToArray($s))->toArray();
        return response()->json($shelters);
    }

    /**
     * Convert Shelter model to array format for views/API
     */
    private function shelterToArray(Shelter $s): array
    {
        return [
            'id'            => $s->id,
            'name'          => $s->name,
            'address'       => $s->address,
            'distance'      => '—', // Will be calculated client-side
            'capacity'      => $s->capacity_label,
            'status'        => $s->status,
            'lat'           => $s->latitude,
            'lng'           => $s->longitude,
            'logistics'     => $s->getDynamicLogistics(),
            'contact_phone' => $s->contact_phone,
            'photo_url'     => $s->photo_url,
        ];
    }
}
