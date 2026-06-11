<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    /**
     * Display a listing of cars.
     */
    public function index(Request $request)
    {
        $query = Car::query();

        // Filter by brand
        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by status (only show available cars)
        $query->where('status', 'available');

        // Sort by price
        if ($request->filled('sort')) {
            if ($request->sort === 'price_asc') {
                $query->orderBy('price_per_day', 'asc');
            } elseif ($request->sort === 'price_desc') {
                $query->orderBy('price_per_day', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $cars = $query->paginate(9);

        // Get unique brands and types for filters
        $brands = Car::distinct()->pluck('brand');
        $types = Car::distinct()->pluck('type');

        return view('public.cars.index', compact('cars', 'brands', 'types'));
    }

    /**
     * Display the specified car.
     */
    public function show($id)
    {
        $car = Car::findOrFail($id);
        return view('public.cars.show', compact('car'));
    }
}
