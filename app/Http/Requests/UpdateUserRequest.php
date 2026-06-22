<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends StoreUserRequest
{
    /**
     * Sama seperti StoreUserRequest, dengan penyesuaian untuk konteks update:
     * - email unik mengabaikan user yang sedang diedit
     * - password opsional (hanya divalidasi jika diisi)
     * - nomor SIM tidak dicek unik (driver yang sama boleh menyimpan ulang)
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['email'] = 'required|email|unique:users,email,' . $this->route('id');
        $rules['password'] = ['nullable', 'confirmed', Password::min(8)->letters()->numbers()->symbols()];
        $rules['license_number'] = 'required_if:role,driver|nullable|string';

        return $rules;
    }
}
