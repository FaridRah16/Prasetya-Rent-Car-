<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
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
            'car_id' => 'required|exists:cars,id',
            'start_date' => 'required|date|after_or_equal:today',
            'pickup_time' => 'required|date_format:H:i',
            'end_date' => 'required|date|after_or_equal:start_date',
            'return_time' => 'required|date_format:H:i',
            'pickup_location' => 'required|string|max:255',
            'dropoff_location' => 'required|string|max:255',
            'pickup_lat' => 'nullable|numeric',
            'pickup_lng' => 'nullable|numeric',
            'dropoff_lat' => 'nullable|numeric',
            'dropoff_lng' => 'nullable|numeric',
            'driver_id' => 'nullable|exists:drivers,user_id',
            'notes' => 'nullable|string',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'car_id.required' => 'Pilih mobil yang ingin disewa',
            'car_id.exists' => 'Mobil tidak ditemukan',
            'start_date.required' => 'Tanggal mulai harus diisi',
            'start_date.after_or_equal' => 'Tanggal mulai minimal hari ini',
            'pickup_time.required' => 'Jam penjemputan harus diisi',
            'pickup_time.date_format' => 'Format jam penjemputan tidak valid',
            'end_date.required' => 'Tanggal selesai harus diisi',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai',
            'return_time.required' => 'Jam pengembalian harus diisi',
            'return_time.date_format' => 'Format jam pengembalian tidak valid',
            'pickup_location.required' => 'Lokasi penjemputan harus diisi',
            'dropoff_location.required' => 'Lokasi pengantaran harus diisi',
            'driver_id.exists' => 'Driver tidak ditemukan',
        ];
    }
}
