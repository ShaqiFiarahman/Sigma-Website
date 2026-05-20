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
            // Citizens/Volunteers can see public disasters (Resolved, Siaga, Awas)
            // AND their own reports (including Pending or Decline)
            $query->where(function($q) use ($user) {
                $q->whereIn('status', [
                    Disaster::STATUS_RESOLVED,
                    Disaster::STATUS_SIAGA_1,
                    Disaster::STATUS_SIAGA_2,
                    Disaster::STATUS_AWAS
                ]);
                if ($user) {
                    $q->orWhere('user_id', $user->id);
                }
            });
        }

        $disasters = $query->get();

        return view('user.search', compact('disasters'));
    }

    /**
     * Show shelter info page (sesuai Android ShelterInfoScreen)
     */
    public function shelterPage()
    {
        $shelters = Shelter::all()->map(fn($s) => $this->shelterToArray($s))->toArray();
        return view('user.shelter', compact('shelters'));
    }

    /**
     * API: Get disasters as JSON for map markers
     */
    public function disasters()
    {
        $disasters = Disaster::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereIn('status', [
                Disaster::STATUS_RESOLVED,
                Disaster::STATUS_SIAGA_1,
                Disaster::STATUS_SIAGA_2,
                Disaster::STATUS_AWAS
            ])
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
            'id'        => $s->id,
            'name'      => $s->name,
            'address'   => $s->address,
            'distance'  => '—', // Will be calculated client-side
            'capacity'  => $s->capacity_label,
            'status'    => $s->status,
            'lat'       => $s->latitude,
            'lng'       => $s->longitude,
            'logistics' => $s->logistics ?? [],
            'contact_phone' => $s->contact_phone,
        ];
    }
}
