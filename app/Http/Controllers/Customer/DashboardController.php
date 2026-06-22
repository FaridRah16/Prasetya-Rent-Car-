<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display customer dashboard with personal statistics.
     */
    public function index()
    {
        $userId = Auth::id();

        $totalBookings = Booking::where('user_id', $userId)->count();

        $activeBookings = Booking::where('user_id', $userId)
            ->whereIn('status', ['pending', 'confirmed', 'ongoing'])
            ->count();

        // Pengeluaran tidak menghitung booking yang dibatalkan (uang di-refund).
        $totalSpending = Booking::where('user_id', $userId)
            ->where('payment_status', 'paid')
            ->where('status', '!=', 'cancelled')
            ->sum('total_price');

        return view('customer.dashboard', compact(
            'totalBookings',
            'activeBookings',
            'totalSpending'
        ));
    }
}
