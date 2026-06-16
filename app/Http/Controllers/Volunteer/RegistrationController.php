<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVolunteerRequest;
use App\Models\Volunteer;

class RegistrationController extends Controller
{
    // Registrasi Relawan
    public function create()
    {
        $skills = Volunteer::getSkillOptions();
        // Cari data relawan lama milik user yang sedang login jika ada
        $existing = auth()->check() ? Volunteer::where('user_id', auth()->id())->first() : null;
        return view('user.register-volunteer', compact('skills', 'existing'));
    }

    public function store(StoreVolunteerRequest $request)
    {
        $user = auth()->user();

        // Cek apakah user sudah pernah mendaftar sebagai relawan
        $existing = Volunteer::where('user_id', $user->id)->first();
        // Kalau pendaftarannya sudah ada, langsung kembalikan pesan error
        if ($existing) {
            return redirect()->back()->with('error', 'Anda sudah terdaftar sebagai relawan.');
        }

        // Simpan pendaftaran relawan baru ke database dengan status pending
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
