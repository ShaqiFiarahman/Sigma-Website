<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVolunteerRequest extends FormRequest
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
            'name'         => 'required|string|max:255',
            'skill'        => 'required|in:MEDIS,SAR,LOGISTIK,KONSUMSI,PSIKOSOSIAL,PENDIDIKAN',
            'address'      => 'required|string',
            'phone_number' => 'required|string|max:20',
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
            'name.required'         => 'Nama lengkap wajib diisi.',
            'skill.required'        => 'Keahlian wajib dipilih.',
            'skill.in'              => 'Keahlian yang dipilih tidak valid.',
            'address.required'      => 'Alamat wajib diisi.',
            'phone_number.required' => 'Nomor telepon wajib diisi.',
            'phone_number.max'      => 'Nomor telepon maksimal 20 karakter.',
        ];
    }
}
