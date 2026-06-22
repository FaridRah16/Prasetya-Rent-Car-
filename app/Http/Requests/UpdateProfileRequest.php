<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProfileRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s]+$/u'],
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'whatsapp_number' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama harus diisi',
            'name.regex' => 'Nama hanya boleh mengandung huruf',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'phone.required' => 'Nomor telepon harus diisi',
            'phone.regex' => 'Nomor telepon hanya boleh mengandung angka',
            'whatsapp_number.regex' => 'Nomor WhatsApp hanya boleh mengandung angka',
            'avatar.image' => 'File harus berupa gambar',
            'avatar.mimes' => 'Format gambar harus JPEG, JPG, atau PNG',
            'avatar.max' => 'Ukuran gambar maksimal 2MB',
        ];
    }
}
