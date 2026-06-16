<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLaporanRequest extends FormRequest
{
    // Otorisasi Permintaan
    public function authorize(): bool
    {
        // Pastiin user sudah login sebelum membuat laporan
        return auth()->check();
    }

    // Aturan Validasi
    public function rules(): array
    {
        // Definisikan aturan validasi untuk input laporan bencana
        return [
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto'      => 'required|array|max:3',
            'foto.*'    => 'image|max:25600',
        ];
    }

    // Pesan Validasi Kustom
    public function messages(): array
    {
        // Berikan pesan error kustom dalam bahasa Indonesia untuk setiap aturan validasi
        return [
            'judul.required'     => 'Judul laporan wajib diisi.',
            'deskripsi.required' => 'Deskripsi laporan wajib diisi.',
            'latitude.required'  => 'Lokasi (latitude) wajib ditentukan.',
            'longitude.required' => 'Lokasi (longitude) wajib ditentukan.',
            'foto.required'      => 'Minimal satu foto wajib diunggah.',
            'foto.max'           => 'Maksimal 3 foto yang dapat diunggah.',
            'foto.*.image'       => 'Berkas yang diunggah harus berupa gambar.',
            'foto.*.max'         => 'Ukuran setiap foto maksimal 25 MB.',
        ];
    }
}
