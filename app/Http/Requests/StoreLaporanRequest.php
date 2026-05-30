<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLaporanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto'      => 'required|array|max:3',
            'foto.*'    => 'image|max:25600',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
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
