<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'sim_photo' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'phone.required' => 'Nomor telepon harus diisi',
            'phone.regex' => 'Nomor telepon hanya boleh mengandung angka',
            'sim_photo.required' => 'Foto SIM wajib diupload',
            'sim_photo.image' => 'File harus berupa gambar',
            'sim_photo.mimes' => 'Format gambar harus JPEG, JPG, atau PNG',
            'sim_photo.max' => 'Ukuran gambar maksimal 2MB',
        ];
    }
}
