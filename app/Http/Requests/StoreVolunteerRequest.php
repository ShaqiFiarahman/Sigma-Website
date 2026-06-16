<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVolunteerRequest extends FormRequest
{
    // Otorisasi Permintaan
    public function authorize(): bool
    {
        // Pastiin user sudah login sebelum mendaftar sebagai relawan
        return auth()->check();
    }

    // Aturan Validasi
    public function rules(): array
    {
        // Definisikan aturan validasi untuk input pendaftaran relawan
        return [
            'name'         => 'required|string|max:255',
            'skill'        => 'required|in:MEDIS,SAR,LOGISTIK,KONSUMSI,PSIKOSOSIAL,PENDIDIKAN',
            'address'      => 'required|string',
            'phone_number' => 'required|string|max:20',
        ];
    }

    // Pesan Validasi Kustom
    public function messages(): array
    {
        // Berikan pesan error kustom dalam bahasa Indonesia untuk input data relawan
        return [
            'name.required'         => 'Nama lengkap wajib diisi.',
            'skill.required'        => 'Keahlian wajib dipilih.',
            'skill.in'              => 'Keahlian yang dipilih tidak valid.',
            'address.required'      => 'Alamat wajib diisi.',
            'phone_number.required' => 'Nomor telepon wajib diisi.',
            'phone_number.max'      => 'Nomor telepon maksimal 20 karakter.',
        ];
    }
}
