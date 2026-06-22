<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard with statistics.
     */
    public function index()
    {
        // Car statistics
        $totalCars = Car::count();
        $availableCars = Car::where('status', 'available')->count();
        $rentedCars = Car::where('status', 'rented')->count();
        $maintenanceCars = Car::where('status', 'maintenance')->count();

        // Booking statistics
        $todayBookings = Booking::whereDate('created_at', today())->count();
        $pendingBookings = Booking::where('status', 'pending')->count();

        // User statistics
        $totalUsers = User::count();
        $totalCustomers = User::where('role', 'customer')->count();

        // Revenue this month — booking dibatalkan dikecualikan (uang di-refund).
        $monthRevenue = Booking::whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->where('payment_status', 'paid')
            ->where('status', '!=', 'cancelled')
            ->sum('total_price');

        // Recent bookings
        $recentBookings = Booking::with(['user', 'car'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalCars',
            'availableCars',
            'rentedCars',
            'maintenanceCars',
            'todayBookings',
            'pendingBookings',
            'totalUsers',
            'totalCustomers',
            'monthRevenue',
            'recentBookings'
        ));
    }
}
