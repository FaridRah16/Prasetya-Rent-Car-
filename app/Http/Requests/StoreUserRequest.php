<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * Otorisasi sudah ditangani middleware route (auth + role:admin).
     */
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
            'email' => 'required|email|unique:users,email',
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'whatsapp_number' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()->symbols()],
            'role' => 'required|in:admin,customer,driver',
            'license_number' => 'required_if:role,driver|nullable|string|unique:drivers,license_number',
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
            'email.unique' => 'Email sudah terdaftar',
            'phone.required' => 'Nomor telepon harus diisi',
            'phone.regex' => 'Nomor telepon hanya boleh mengandung angka',
            'whatsapp_number.regex' => 'Nomor WhatsApp hanya boleh mengandung angka',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'password.letters' => 'Password harus mengandung huruf',
            'password.numbers' => 'Password harus mengandung angka',
            'password.symbols' => 'Password harus mengandung simbol',
            'role.required' => 'Role harus dipilih',
            'license_number.required_if' => 'Nomor SIM harus diisi untuk driver',
            'license_number.unique' => 'Nomor SIM sudah terdaftar',
        ];
    }
}
