<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:100',
            'type' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'required|string|max:50',
            'plate_number' => 'required|string|max:20|unique:cars,plate_number',
            'price_per_day' => 'required|numeric|min:0',
            'seats' => 'required|integer|min:1|max:20',
            'transmission' => 'required|string|in:Manual,Automatic,CVT',
            'fuel' => 'required|string|in:Bensin,Diesel,Hybrid,Listrik',
            'status' => 'required|in:available,rented,maintenance',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama mobil harus diisi',
            'brand.required' => 'Brand mobil harus diisi',
            'type.required' => 'Tipe mobil harus diisi',
            'year.required' => 'Tahun mobil harus diisi',
            'year.integer' => 'Tahun harus berupa angka',
            'year.min' => 'Tahun minimal 1900',
            'year.max' => 'Tahun maksimal ' . (date('Y') + 1),
            'color.required' => 'Warna mobil harus diisi',
            'plate_number.required' => 'Nomor plat harus diisi',
            'plate_number.unique' => 'Nomor plat sudah terdaftar',
            'price_per_day.required' => 'Harga sewa harus diisi',
            'price_per_day.numeric' => 'Harga harus berupa angka',
            'seats.required' => 'Jumlah kursi harus diisi',
            'seats.integer' => 'Jumlah kursi harus berupa angka',
            'transmission.required' => 'Transmisi harus dipilih',
            'transmission.in' => 'Transmisi tidak valid',
            'fuel.required' => 'Bahan bakar harus dipilih',
            'fuel.in' => 'Bahan bakar tidak valid',
            'status.required' => 'Status mobil harus dipilih',
            'image.image' => 'File harus berupa gambar',
            'image.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'image.max' => 'Ukuran gambar maksimal 2MB',
            'gallery.array' => 'Format galeri harus berupa array',
            'gallery.*.image' => 'File galeri harus berupa gambar',
            'gallery.*.mimes' => 'Format gambar galeri harus jpeg, png, atau jpg',
            'gallery.*.max' => 'Ukuran gambar galeri maksimal 2MB',
        ];
    }
}
