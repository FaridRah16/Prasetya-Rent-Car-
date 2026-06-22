<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
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
    public function store(StoreCarRequest $request)
    {
        // Allow-list eksplisit (bukan except()) untuk mencegah mass-assignment.
        $data = $request->only([
            'name', 'brand', 'type', 'year', 'color', 'plate_number',
            'price_per_day', 'seats', 'transmission', 'fuel', 'status', 'description',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // store() menghasilkan nama acak (anti-tebak & anti-tabrakan)
            $data['image'] = $request->file('image')->store('cars', 'public');
        }

        // Handle gallery upload
        if ($request->hasFile('gallery')) {
            $galleryPaths = [];
            foreach ($request->file('gallery') as $file) {
                // store() menghasilkan nama acak (anti-tebak & anti-tabrakan)
                $galleryPaths[] = $file->store('cars/gallery', 'public');
            }
            $data['gallery'] = $galleryPaths;
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
    public function update(UpdateCarRequest $request, $id)
    {
        $car = Car::findOrFail($id);

        // Allow-list eksplisit (bukan except()) untuk mencegah mass-assignment.
        $data = $request->only([
            'name', 'brand', 'type', 'year', 'color', 'plate_number',
            'price_per_day', 'seats', 'transmission', 'fuel', 'status', 'description',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($car->image) {
                Storage::disk('public')->delete($car->image);
            }

            // store() menghasilkan nama acak (anti-tebak & anti-tabrakan)
            $data['image'] = $request->file('image')->store('cars', 'public');
        }

        // Handle gallery upload
        if ($request->hasFile('gallery')) {
            // Delete old gallery images
            if (!empty($car->gallery) && is_array($car->gallery)) {
                foreach ($car->gallery as $oldImg) {
                    Storage::disk('public')->delete($oldImg);
                }
            }

            $galleryPaths = [];
            foreach ($request->file('gallery') as $file) {
                // store() menghasilkan nama acak (anti-tebak & anti-tabrakan)
                $galleryPaths[] = $file->store('cars/gallery', 'public');
            }
            $data['gallery'] = $galleryPaths;
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

        // Booking memakai FK 'restrict' pada car_id: mobil dengan riwayat booking
        // apa pun tidak dapat dihapus (melindungi riwayat finansial & audit).
        // Cek booking aktif lebih dulu agar pesannya lebih spesifik.
        $activeBookings = $car->bookings()
            ->whereIn('status', ['pending', 'confirmed', 'ongoing'])
            ->count();

        if ($activeBookings > 0) {
            return back()->with('error', 'Tidak dapat menghapus mobil yang memiliki booking aktif');
        }

        if ($car->bookings()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus mobil yang memiliki riwayat booking. Data dipertahankan untuk keperluan audit.');
        }

        // Delete image if exists
        if ($car->image) {
            Storage::disk('public')->delete($car->image);
        }

        // Delete gallery images if exist
        if (!empty($car->gallery) && is_array($car->gallery)) {
            foreach ($car->gallery as $galleryImg) {
                Storage::disk('public')->delete($galleryImg);
            }
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
