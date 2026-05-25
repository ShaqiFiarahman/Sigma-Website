<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Disaster;
use App\Models\Volunteer;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        $stats = [
            'active' => Disaster::whereIn('status', [Disaster::STATUS_AWAS, Disaster::STATUS_SIAGA_1, Disaster::STATUS_SIAGA_2])->count(),
            'need_verify' => Disaster::where('status', Disaster::STATUS_PENDING)->count(),
        ];

        $disasters = Disaster::whereNotIn('status', [Disaster::STATUS_RESOLVED, Disaster::STATUS_DECLINE])
            ->latest()
            ->get();

        return view('admin.laporan.index', compact('stats', 'disasters'));
    }

    public function detail($id)
    {
        $disaster = Disaster::with('user')->findOrFail($id);

        return response()->json([
            'id' => $disaster->id,
            'title' => $disaster->title,
            'description' => $disaster->description,
            'photo_url' => $disaster->photo_url,
            'latitude' => $disaster->latitude,
            'longitude' => $disaster->longitude,
            'location' => $disaster->location,
            'status' => $disaster->status,
            'status_label' => $disaster->status_label,
            'disaster_type' => $disaster->disaster_type ?? 'unknown',
            'type_name' => $disaster->type_name,
            'reporter_name' => $disaster->reporter_name,
            'created_at' => $disaster->created_at->format('d M Y, H:i'),
            'time_ago' => $disaster->created_at->diffForHumans(),
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $disaster = Disaster::findOrFail($id);

        $request->validate([
            'disaster_type' => 'required|string|in:flood,fire,earthquake,landslide,tsunami,storm,volcano,unknown',
            'status'        => 'required|string|in:PENDING,AWAS,SIAGA_1,SIAGA_2,RESOLVED,DECLINE',
        ]);

        $disaster->update([
            'disaster_type' => $request->disaster_type,
            'status'        => $request->status,
            'verified_by'   => $request->status === 'PENDING' ? null : auth()->id(),
        ]);

        // Jika bencana selesai atau ditolak, hapus penugasan relawan
        if (in_array($request->status, ['RESOLVED', 'DECLINE'])) {
            Volunteer::where('disaster_id', $disaster->id)
                ->update([
                    'disaster_id'            => null,
                    'assignment'             => null,
                    'assignment_notified_at' => null,
                ]);
        }

        $msg = $request->status === 'DECLINE' ? 'rejected' : 'approved';

        return redirect()->route('laporan.show', $id)->with('msg', $msg);
    }

    public function updateType(Request $request, $id)
    {
        $request->validate([
            'disaster_type' => 'required|string|in:flood,fire,earthquake,landslide,tsunami,storm,volcano,unknown',
        ]);

        $disaster = Disaster::findOrFail($id);
        $disaster->update(['disaster_type' => $request->disaster_type]);

        return response()->json(['success' => true]);
    }
}
