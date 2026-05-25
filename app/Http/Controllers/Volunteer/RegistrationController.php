<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\Volunteer;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function create()
    {
        $skills = Volunteer::getSkillOptions();
        $existing = Volunteer::where('user_id', auth()->id())->first();
        return view('user.register-volunteer', compact('skills', 'existing'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'skill'        => 'required|in:MEDIS,SAR,LOGISTIK,KONSUMSI,PSIKOSOSIAL',
            'address'      => 'required|string',
            'phone_number' => 'required|string|max:20',
        ]);

        $user = auth()->user();

        $existing = Volunteer::where('user_id', $user->id)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Anda sudah terdaftar sebagai relawan.');
        }

        Volunteer::create([
            'user_id'      => $user->id,
            'name'         => $request->name,
            'skill'        => $request->skill,
            'address'      => $request->address,
            'phone_number' => $request->phone_number,
            'status'       => Volunteer::STATUS_PENDING,
        ]);

        return redirect()->route('dashboard')
            ->with('msg', 'Pendaftaran relawan berhasil. Menunggu verifikasi Admin.');
    }
}
