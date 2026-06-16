<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShelterRequest extends FormRequest
{
    // Otorisasi Permintaan
    public function authorize(): bool
    {
        // Pastiin hanya user dengan role admin yang bisa membuat atau mengedit posko
        return strtolower(auth()->user()->role ?? '') === 'admin';
    }

    // Aturan Validasi
    public function rules(): array
    {
        // Definisikan aturan validasi untuk input data posko pengungsian
        return [
            'name'             => 'required|string|max:255',
            'address'          => 'nullable|string|max:500',
            'capacity_current' => 'required|integer|min:0',
            'capacity_max'     => 'required|integer|min:1',
            'status'           => 'required|in:Tersedia,Penuh',
            'logistics'        => 'nullable|string',
            'contact_phone'    => 'nullable|string|max:20',
            'photo'            => 'nullable|image|max:5120',
            'latitude'         => 'nullable|numeric',
            'longitude'        => 'nullable|numeric',
        ];
    }

    // Pesan Validasi Kustom
    public function messages(): array
    {
        // Berikan pesan error kustom dalam bahasa Indonesia untuk input data posko
        return [
            'name.required'             => 'Nama posko wajib diisi.',
            'capacity_current.required' => 'Kapasitas saat ini wajib diisi.',
            'capacity_max.required'     => 'Kapasitas maksimal wajib diisi.',
            'capacity_max.min'          => 'Kapasitas maksimal minimal 1.',
            'status.required'           => 'Status posko wajib dipilih.',
            'status.in'                 => 'Status posko tidak valid.',
            'photo.image'               => 'Berkas yang diunggah harus berupa gambar.',
            'photo.max'                 => 'Ukuran foto maksimal 5 MB.',
        ];
    }
}
