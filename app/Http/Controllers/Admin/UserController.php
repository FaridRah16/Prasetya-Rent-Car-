<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s]+$/u'],
            'email' => 'required|email|unique:users,email',
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'whatsapp_number' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()->symbols()],
            'role' => 'required|in:admin,customer,driver',
            'license_number' => 'required_if:role,driver|nullable|string|unique:drivers,license_number',
        ], [
            'name.required' => 'Nama harus diisi',
            'name.regex' => 'Nama hanya boleh mengandung huruf',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'phone.required' => 'Nomor telepon harus diisi',
            'phone.regex' => 'Nomor telepon hanya boleh mengandung angka',
            'whatsapp_number.regex' => 'Nomor WhatsApp hanya boleh mengandung angka',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'password.letters' => 'Password harus mengandung huruf',
            'password.numbers' => 'Password harus mengandung angka',
            'password.symbols' => 'Password harus mengandung simbol',
            'role.required' => 'Role harus dipilih',
            'license_number.required_if' => 'Nomor SIM harus diisi untuk driver',
            'license_number.unique' => 'Nomor SIM sudah terdaftar',
        ]);

        DB::transaction(function () use ($request) {
            $user = new User([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'whatsapp_number' => $request->whatsapp_number,
                'password' => $request->password, // Cast 'hashed' di model otomatis meng-hash password
            ]);
            $user->role = $request->role; // di-set eksplisit (role tidak mass-assignable)
            $user->save();

            // If role is driver, create driver record
            if ($request->role === 'driver') {
                Driver::create([
                    'user_id' => $user->id,
                    'license_number' => $request->license_number,
                    'status' => 'available',
                ]);
            }
        });

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        $user = User::with(['bookings', 'driver'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        $user = User::with('driver')->findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s]+$/u'],
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'whatsapp_number' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'password' => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()->symbols()],
            'role' => 'required|in:admin,customer,driver',
            'license_number' => 'required_if:role,driver|nullable|string',
        ], [
            'name.required' => 'Nama harus diisi',
            'name.regex' => 'Nama hanya boleh mengandung huruf',
            'email.required' => 'Email harus diisi',
            'email.unique' => 'Email sudah terdaftar',
            'phone.required' => 'Nomor telepon harus diisi',
            'phone.regex' => 'Nomor telepon hanya boleh mengandung angka',
            'whatsapp_number.regex' => 'Nomor WhatsApp hanya boleh mengandung angka',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'password.letters' => 'Password harus mengandung huruf',
            'password.numbers' => 'Password harus mengandung angka',
            'password.symbols' => 'Password harus mengandung simbol',
            'license_number.required_if' => 'Nomor SIM harus diisi untuk driver',
        ]);

        DB::transaction(function () use ($request, $user) {
            $user->fill([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'whatsapp_number' => $request->whatsapp_number,
            ]);
            $user->role = $request->role; // di-set eksplisit (role tidak mass-assignable)

            // Update password if provided
            if ($request->filled('password')) {
                $user->password = $request->password; // Cast 'hashed' di model otomatis meng-hash password
            }

            $user->save();

            // Handle driver record
            if ($request->role === 'driver') {
                if ($user->driver) {
                    // Update existing driver
                    $user->driver->update([
                        'license_number' => $request->license_number,
                    ]);
                } else {
                    // Create new driver record
                    Driver::create([
                        'user_id' => $user->id,
                        'license_number' => $request->license_number,
                        'status' => 'available',
                    ]);
                }
            } else {
                // Delete driver record if role changed from driver
                if ($user->driver) {
                    $user->driver->delete();
                }
            }
        });

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diupdate');
    }

    /**
     * Remove the specified user.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri');
        }

        // Check if user has active bookings
        if ($user->role === 'customer') {
            $activeBookings = $user->bookings()
                ->whereIn('status', ['pending', 'confirmed', 'ongoing'])
                ->count();

            if ($activeBookings > 0) {
                return back()->with('error', 'Tidak dapat menghapus user yang memiliki booking aktif');
            }
        }

        // Hapus file avatar dari storage jika ada
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Hapus foto SIM dari storage jika ada
        if ($user->sim_photo) {
            Storage::disk('local')->delete($user->sim_photo);
            Storage::disk('public')->delete($user->sim_photo);
        }

        // Hapus record driver secara eksplisit jika user adalah driver
        if ($user->driver) {
            $user->driver->delete();
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus');
    }

    /**
     * Approve a customer's account verification.
     */
    public function verifyUser($id)
    {
        $user = User::findOrFail($id);

        if (! $user->sim_photo) {
            return back()->with('error', 'User belum mengunggah foto SIM, tidak dapat diverifikasi');
        }

        // di-set eksplisit (field verifikasi tidak mass-assignable)
        $user->verification_status = 'verified';
        $user->verified_at = now();
        $user->save();

        return back()->with('success', 'Akun ' . $user->name . ' berhasil diverifikasi');
    }

    /**
     * Reject a customer's account verification (kembali ke belum terverifikasi).
     */
    public function rejectVerification($id)
    {
        $user = User::findOrFail($id);

        // Hapus foto SIM agar customer mengunggah ulang
        if ($user->sim_photo) {
            Storage::disk('local')->delete($user->sim_photo);
            Storage::disk('public')->delete($user->sim_photo);
        }

        // di-set eksplisit (field verifikasi tidak mass-assignable)
        $user->verification_status = 'unverified';
        $user->sim_photo = null;
        $user->verified_at = null;
        $user->save();

        return back()->with('success', 'Verifikasi ditolak. Customer harus mengajukan ulang.');
    }
}
