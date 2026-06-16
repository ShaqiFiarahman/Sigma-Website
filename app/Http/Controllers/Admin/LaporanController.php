<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Disaster;
use App\Models\Volunteer;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    // Daftar Laporan
    public function index()
    {
        $stats = [
            // Hitung jumlah bencana aktif berstatus awas atau siaga
            'active' => Disaster::whereIn('status', [Disaster::STATUS_AWAS, Disaster::STATUS_SIAGA_1, Disaster::STATUS_SIAGA_2])->count(),
            // Hitung jumlah laporan bencana masuk yang membutuhkan verifikasi
            'need_verify' => Disaster::where('status', Disaster::STATUS_PENDING)->count(),
        ];

        // Ambil semua laporan bencana selain yang selesai atau ditolak
        $disasters = Disaster::whereNotIn('status', [Disaster::STATUS_RESOLVED, Disaster::STATUS_DECLINE])
            ->latest()
            ->get();

        return view('admin.laporan.index', compact('stats', 'disasters'));
    }

    // Detail JSON
    public function detail($id)
    {
        // Cari data detail bencana beserta user pembuat laporan berdasarkan ID
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

    // Kelola Status Dan Tipe
    public function updateStatus(Request $request, $id)
    {
        // Cari data laporan bencana berdasarkan ID
        $disaster = Disaster::findOrFail($id);

        // Validasi jenis bencana dan status bencana baru
        $request->validate([
            'disaster_type' => 'required|string|in:flood,fire,earthquake,landslide,tsunami,storm,volcano,unknown',
            'status'        => 'required|string|in:PENDING,AWAS,SIAGA_1,SIAGA_2,RESOLVED,DECLINE',
        ]);

        $disaster->update([
            'disaster_type' => $request->disaster_type,
            'status'        => $request->status,
            'verified_by'   => $request->status === 'PENDING' ? null : auth()->id(),
        ]);

        // Jika status bencana diubah menjadi selesai atau ditolak, hapus penugasan relawan di lokasi tersebut
        if (in_array($request->status, ['RESOLVED', 'DECLINE'])) {
            // Reset tugas relawan yang ditugaskan pada bencana tersebut
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
        // Validasi jenis bencana yang diinputkan
        $request->validate([
            'disaster_type' => 'required|string|in:flood,fire,earthquake,landslide,tsunami,storm,volcano,unknown',
        ]);

        // Cari data laporan bencana berdasarkan ID untuk diperbarui jenis bencananya
        $disaster = Disaster::findOrFail($id);
        $disaster->update(['disaster_type' => $request->disaster_type]);

        return response()->json(['success' => true]);
    }
}
