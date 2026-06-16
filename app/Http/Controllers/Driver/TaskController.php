<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        // Set mobil ke 'rented' saat tugas dimulai
        $task->car->update(['status' => 'rented']);

        // Set driver ke 'on_duty' saat tugas dimulai
        $driver = Driver::where('user_id', Auth::id())->first();
        if ($driver) {
            $driver->update(['status' => 'on_duty']);
        }

        return back()->with('success', 'Tugas dimulai. Selamat bertugas!');
    }

    /**
     * Submit delivery proof (photo) and confirm car has been delivered.
     * Status remains 'ongoing' — only admin can mark as 'completed'.
     */
    public function completeTask(Request $request, $id)
    {
        $request->validate([
            'delivery_proof' => 'required|image|mimes:jpeg,jpg,png|max:5120',
        ], [
            'delivery_proof.required' => 'Foto bukti pengantaran wajib diupload',
            'delivery_proof.image' => 'File harus berupa gambar',
            'delivery_proof.mimes' => 'Format gambar harus JPEG, JPG, atau PNG',
            'delivery_proof.max' => 'Ukuran gambar maksimal 5MB',
        ]);

        $task = Booking::where('driver_id', Auth::id())
            ->where('status', 'ongoing')
            ->findOrFail($id);

        // Upload delivery proof
        $proofPath = $request->file('delivery_proof')->store('delivery_proofs', 'public');

        // Delete old delivery proof if exists
        if ($task->delivery_proof) {
            Storage::disk('public')->delete($task->delivery_proof);
        }

        $task->update([
            'delivery_proof' => $proofPath,
            // Status TETAP 'ongoing' — hanya admin yang dapat menyelesaikan pemesanan
        ]);

        return redirect()->route('driver.tasks.index')
            ->with('success', 'Bukti pengantaran berhasil dikirim. Menunggu konfirmasi admin.');
    }
}
