<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display active tasks (ongoing bookings assigned to driver).
     */
    public function index()
    {
        $tasks = Booking::where('driver_id', Auth::id())
            ->whereIn('status', ['confirmed', 'ongoing'])
            ->with(['user', 'car'])
            ->orderBy('start_date', 'asc')
            ->get();

        return view('driver.tasks.index', compact('tasks'));
    }

    /**
     * Display task history (completed bookings).
     */
    public function history()
    {
        $tasks = Booking::where('driver_id', Auth::id())
            ->whereIn('status', ['completed', 'cancelled'])
            ->with(['user', 'car'])
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        return view('driver.tasks.history', compact('tasks'));
    }

    /**
     * Display the specified task.
     */
    public function show($id)
    {
        $task = Booking::where('driver_id', Auth::id())
            ->with(['user', 'car'])
            ->findOrFail($id);

        return view('driver.tasks.show', compact('task'));
    }

    /**
     * Update task status to ongoing.
     */
    public function startTask($id)
    {
        $task = Booking::where('driver_id', Auth::id())
            ->where('status', 'confirmed')
            ->findOrFail($id);

        $task->update(['status' => 'ongoing']);

        return back()->with('success', 'Tugas dimulai. Selamat bertugas!');
    }

    /**
     * Complete task.
     */
    public function completeTask($id)
    {
        $task = Booking::where('driver_id', Auth::id())
            ->where('status', 'ongoing')
            ->findOrFail($id);

        $task->update(['status' => 'completed']);

        // Update car status back to available
        $task->car->update(['status' => 'available']);

        // Update driver status back to available
        if ($task->driver && $task->driver->user) {
            $task->driver->update(['status' => 'available']);
        }

        return redirect()->route('driver.tasks.index')
            ->with('success', 'Tugas selesai. Terima kasih!');
    }
}
