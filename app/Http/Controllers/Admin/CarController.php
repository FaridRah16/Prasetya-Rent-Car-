<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    /**
     * Display a listing of cars.
     */
    public function index(Request $request)
    {
        $query = Car::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('brand', 'like', '%' . $request->search . '%')
                  ->orWhere('plate_number', 'like', '%' . $request->search . '%');
            });
        }

        $cars = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('admin.cars.index', compact('cars'));
    }

    /**
     * Show the form for creating a new car.
     */
    public function create()
    {
        return view('admin.cars.create');
    }

    /**
     * Store a newly created car.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:100',
            'type' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'required|string|max:50',
            'plate_number' => 'required|string|max:20|unique:cars,plate_number',
            'price_per_day' => 'required|numeric|min:0',
            'seats' => 'required|integer|min:1|max:20',
            'status' => 'required|in:available,rented,maintenance',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
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
            'status.required' => 'Status mobil harus dipilih',
            'image.image' => 'File harus berupa gambar',
            'image.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'image.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        $data = $request->except('image');

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'car_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('cars', $filename, 'public');
            $data['image'] = $path;
        }

        Car::create($data);

        return redirect()->route('admin.cars.index')
            ->with('success', 'Mobil berhasil ditambahkan');
    }

    /**
     * Display the specified car.
     */
    public function show($id)
    {
        $car = Car::findOrFail($id);
        return view('admin.cars.show', compact('car'));
    }

    /**
     * Show the form for editing the specified car.
     */
    public function edit($id)
    {
        $car = Car::findOrFail($id);
        return view('admin.cars.edit', compact('car'));
    }

    /**
     * Update the specified car.
     */
    public function update(Request $request, $id)
    {
        $car = Car::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:100',
            'type' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'required|string|max:50',
            'plate_number' => 'required|string|max:20|unique:cars,plate_number,' . $id,
            'price_per_day' => 'required|numeric|min:0',
            'seats' => 'required|integer|min:1|max:20',
            'status' => 'required|in:available,rented,maintenance',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'name.required' => 'Nama mobil harus diisi',
            'brand.required' => 'Brand mobil harus diisi',
            'type.required' => 'Tipe mobil harus diisi',
            'year.required' => 'Tahun mobil harus diisi',
            'color.required' => 'Warna mobil harus diisi',
            'plate_number.required' => 'Nomor plat harus diisi',
            'plate_number.unique' => 'Nomor plat sudah terdaftar',
            'price_per_day.required' => 'Harga sewa harus diisi',
            'seats.required' => 'Jumlah kursi harus diisi',
            'status.required' => 'Status mobil harus dipilih',
            'image.image' => 'File harus berupa gambar',
            'image.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        $data = $request->except('image');

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($car->image) {
                Storage::disk('public')->delete($car->image);
            }

            $file = $request->file('image');
            $filename = 'car_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('cars', $filename, 'public');
            $data['image'] = $path;
        }

        $car->update($data);

        return redirect()->route('admin.cars.index')
            ->with('success', 'Mobil berhasil diupdate');
    }

    /**
     * Remove the specified car.
     */
    public function destroy($id)
    {
        $car = Car::findOrFail($id);

        // Check if car has active bookings
        $activeBookings = $car->bookings()
            ->whereIn('status', ['pending', 'confirmed', 'ongoing'])
            ->count();

        if ($activeBookings > 0) {
            return back()->with('error', 'Tidak dapat menghapus mobil yang memiliki booking aktif');
        }

        // Delete image if exists
        if ($car->image) {
            Storage::disk('public')->delete($car->image);
        }

        $car->delete();

        return redirect()->route('admin.cars.index')
            ->with('success', 'Mobil berhasil dihapus');
    }

    /**
     * Toggle car status.
     */
    public function toggleStatus($id)
    {
        $car = Car::findOrFail($id);

        // Only toggle between available and maintenance
        if ($car->status === 'available') {
            $car->update(['status' => 'maintenance']);
            $message = 'Status mobil diubah ke maintenance';
        } elseif ($car->status === 'maintenance') {
            $car->update(['status' => 'available']);
            $message = 'Status mobil diubah ke tersedia';
        } else {
            return back()->with('error', 'Mobil sedang disewa, tidak dapat mengubah status');
        }

        return back()->with('success', $message);
    }
}
