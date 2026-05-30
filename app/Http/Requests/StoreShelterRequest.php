<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShelterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return strtolower(auth()->user()->role ?? '') === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
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

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
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
