<?php

namespace App\Http\Controllers;

use App\Models\Disaster;
use App\Models\Shelter;

class MapController extends Controller
{
    // Tampilan Peta
    public function index()
    {
        return view('user.map');
    }

    // Pencarian Laporan Bencana
    public function search()
    {
        $user = auth()->user();
        $role = $user?->role ?? 'MASYARAKAT';

        $query = Disaster::latest();

        // Jika pengguna bukan admin, batasi agar hanya melihat bencana publik aktif atau laporannya sendiri
        if (strtolower($role) !== 'admin') {
            // Filter laporan dengan status tertentu
            $query->where(function($q) use ($user) {
                $q->whereIn('status', [
                    'SIAGA_1',
                    'SIAGA_2',
                    'AWAS'
                ]);
                // Jika user login, tampilkan juga laporan milik mereka sendiri
                if ($user) {
                    $q->orWhere('user_id', $user->id);
                }
            });
        }

        // Ambil data bencana hasil filter ke database
        $disasters = $query->get();

        return view('laporan.search', compact('disasters'));
    }

    // Daftar Posko Pengungsian
    public function shelterPage()
    {
        $query = Shelter::query();

        // Jika ada filter status pengungsian, tambahkan kondisi filter pada query
        if (request('status')) {
            $query->where('status', request('status'));
        }
        // Jika ada parameter pencarian nama, lakukan filter pencarian LIKE
        if (request('q')) {
            $query->where('name', 'like', '%' . request('q') . '%');
        }

        // Ambil data pengungsian dengan pagination dan transformasikan ke format array
        $shelters = $query->paginate(6)->through(fn($s) => $this->shelterToArray($s));
        return view('shelter.index', compact('shelters'));
    }

    // Endpoint Data JSON
    public function disasters()
    {
        // Ambil koordinat bencana aktif yang valid untuk marker peta
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

    public function shelters()
    {
        try {
            // Ambil semua data lokasi pengungsian beserta informasi logistik dan kapasitasnya
            $shelters = Shelter::all()->map(fn($s) => $this->shelterToArray($s))->toArray();
            return response()->json($shelters);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    // Fungsi Bantuan
    private function shelterToArray(Shelter $s): array
    {
        return [
            'id'            => $s->id,
            'name'          => $s->name,
            'address'       => $s->address,
            'distance'      => '—', // Dihitung di sisi klien
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
