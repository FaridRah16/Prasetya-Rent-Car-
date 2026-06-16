<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use App\Models\User;

class ReportController extends Controller
{
    /**
     * Display reports and statistics page.
     */
    public function index()
    {
        // Overall booking stats
        $totalBookings = Booking::count();
        $completedBookings = Booking::where('status', 'completed')->count();
        $cancelledBookings = Booking::where('status', 'cancelled')->count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $ongoingBookings = Booking::where('status', 'ongoing')->count();

        // Revenue stats
        $totalRevenue = Booking::where('payment_status', 'paid')->sum('total_price');
        $monthRevenue = Booking::whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->where('payment_status', 'paid')
            ->sum('total_price');

        // Resource counts
        $totalCars = Car::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalDrivers = User::where('role', 'driver')->count();

        // Monthly booking stats (last 6 months)
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyStats[] = [
                'month' => $date->translatedFormat('M Y'),
                'bookings' => Booking::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
                'revenue' => Booking::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->where('payment_status', 'paid')
                    ->sum('total_price'),
            ];
        }

        // Top cars by booking count
        $topCars = Car::withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.reports', compact(
            'totalBookings',
            'completedBookings',
            'cancelledBookings',
            'pendingBookings',
            'confirmedBookings',
            'ongoingBookings',
            'totalRevenue',
            'monthRevenue',
            'totalCars',
            'totalCustomers',
            'totalDrivers',
            'monthlyStats',
            'topCars'
        ));
    }
}
