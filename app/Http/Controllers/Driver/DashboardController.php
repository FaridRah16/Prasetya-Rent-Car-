<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display driver dashboard with task statistics.
     */
    public function index()
    {
        $userId = Auth::id();

        $activeTasks = Booking::where('driver_id', $userId)
            ->whereIn('status', ['confirmed', 'ongoing'])
            ->count();

        $completedTasks = Booking::where('driver_id', $userId)
            ->where('status', 'completed')
            ->count();

        $ongoingTasks = Booking::where('driver_id', $userId)
            ->where('status', 'ongoing')
            ->count();

        $driverStatus = Auth::user()->driver ? Auth::user()->driver->status : 'available';

        $recentTasks = Booking::where('driver_id', $userId)
            ->with(['user', 'car'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return view('driver.dashboard', compact(
            'activeTasks',
            'completedTasks',
            'ongoingTasks',
            'driverStatus',
            'recentTasks'
        ));
    }
}
