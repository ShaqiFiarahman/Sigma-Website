<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\Disaster;
use App\Models\Volunteer;
use App\Models\VolunteerReport;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // Riwayat Laporan Relawan
    public function index()
    {
        $user = auth()->user();
        // Ambil data relawan berdasarkan user yang sedang login
        $volunteer = Volunteer::where('user_id', $user->id)->first();

        // Kalau user bukan relawan, kembalikan ke dashboard
        if (!$volunteer) {
            return redirect()->route('dashboard');
        }

        // Ambil laporan tugas relawan beserta info bencana dengan pagination
        $reports = VolunteerReport::where('volunteer_id', $volunteer->id)
            ->with('disaster')
            ->latest()
            ->paginate(10);

        $fields = VolunteerReport::getFieldsForSkill($volunteer->skill);

        return view('volunteer.reports.index', compact('volunteer', 'reports', 'fields'));
    }

    // Formulir Laporan Relawan
    public function create()
    {
        $user = auth()->user();
        // Cari data relawan aktif berdasarkan user yang sedang login
        $volunteer = Volunteer::where('user_id', $user->id)
            ->where('status', Volunteer::STATUS_APPROVED)
            ->first();

        // Kalau belum disetujui sebagai relawan, alihkan kembali ke dashboard dengan pesan error
        if (!$volunteer) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda belum disetujui sebagai relawan.');
        }

        $fields = VolunteerReport::getFieldsForSkill($volunteer->skill);
        // Ambil daftar bencana aktif untuk opsi pelaporan
        $disasters = Disaster::whereNotIn('status', ['PENDING', 'DECLINE', 'RESOLVED'])
            ->latest()
            ->get(['id', 'title', 'location']);

        // Ambil 5 laporan tugas terakhir milik relawan ini
        $recentReports = VolunteerReport::where('volunteer_id', $volunteer->id)
            ->with('disaster')
            ->latest()
            ->limit(5)
            ->get();

        return view('volunteer.reports.create', compact('volunteer', 'fields', 'disasters', 'recentReports'));
    }

    // Simpan Laporan Relawan
    public function store(Request $request)
    {
        $user = auth()->user();
        // Ambil data relawan aktif yang akan menyimpan laporan
        $volunteer = Volunteer::where('user_id', $user->id)
            ->where('status', Volunteer::STATUS_APPROVED)
            ->firstOrFail();

        $fields = VolunteerReport::getFieldsForSkill($volunteer->skill);
        $rules = [
            'notes' => 'nullable|string|max:1000',
            'disaster_id' => 'nullable|exists:disasters,id',
        ];
        // Iterasi kolom dinamis sesuai keahlian relawan untuk membuat aturan validasi
        foreach ($fields as $field) {
            $key = 'data.' . $field['name'];
            $isOptional = isset($field['optional']) && $field['optional'];
            $rulePrefix = $isOptional ? 'nullable' : 'required';

            // Sesuaikan aturan validasi berdasarkan tipe kolom
            if ($field['type'] === 'number') {
                $rules[$key] = "$rulePrefix|integer|min:0";
            } elseif ($field['type'] === 'textarea') {
                $rules[$key] = "$rulePrefix|string|max:500";
            } else {
                $rules[$key] = "$rulePrefix|string|max:255";
            }
        }

        // Validasi data input dinamis yang dikirim relawan
        $request->validate($rules);

        // Simpan laporan tugas relawan baru ke database
        VolunteerReport::create([
            'volunteer_id' => $volunteer->id,
            'disaster_id'  => $request->disaster_id ?: null,
            'skill_type'   => $volunteer->skill,
            'report_data'  => $request->data,
            'notes'        => $request->notes,
            'photo_urls'   => null,
        ]);

        // Jika relawan memiliki tugas penugasan tempat pengungsian, sinkronkan kebutuhan logistik
        if (!empty($volunteer->assignment)) {
            // Cari data pengungsian yang menjadi lokasi penugasan relawan
            $shelter = \App\Models\Shelter::where('name', $volunteer->assignment)->first();
            // Jika pengungsian ditemukan, tentukan input kebutuhan berdasarkan keahlian relawan
            if ($shelter) {
                $needsInput = '';
                // Ambil kebutuhan medis jika relawan berkeahlian medis, atau logistik jika berkeahlian logistik
                if ($volunteer->skill === 'MEDIS' && !empty($request->data['kebutuhan_medis'])) {
                    $needsInput = $request->data['kebutuhan_medis'];
                } elseif ($volunteer->skill === 'LOGISTIK' && !empty($request->data['kebutuhan_mendesak'])) {
                    $needsInput = $request->data['kebutuhan_mendesak'];
                }

                // Jika ada kebutuhan yang diinputkan, pecah string menjadi array barang
                if (!empty($needsInput)) {
                    $splitNeeds = preg_split('/[,;\n\r]+/', $needsInput);
                    $currentLogistics = $shelter->logistics;

                    // Iterasi dan tambahkan barang kebutuhan baru yang belum ada di daftar logistik pengungsian
                    foreach ($splitNeeds as $needItem) {
                        $trimmed = trim($needItem);
                        // Pastikan barang tidak kosong dan belum tercatat sebelumnya
                        if (!empty($trimmed) && !in_array($trimmed, $currentLogistics)) {
                            $currentLogistics[] = $trimmed;
                        }
                    }

                    $shelter->logistics = $currentLogistics;
                    $shelter->save();
                }
            }
        }

        return redirect()->route('volunteer.reports')
            ->with('msg', 'Laporan tugas berhasil dikirim.');
    }
}
