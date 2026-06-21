<?php

namespace App\Http\Requests;

class UpdateCarRequest extends StoreCarRequest
{
    /**
     * Sama seperti StoreCarRequest, hanya aturan unik plat yang mengabaikan
     * mobil yang sedang diedit.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['plate_number'] = 'required|string|max:20|unique:cars,plate_number,' . $this->route('id');

        return $rules;
    }
}
