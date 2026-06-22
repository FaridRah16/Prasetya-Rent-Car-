<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitVerificationRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show edit profile form.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('customer.profile.edit', compact('user'));
    }

    /**
     * Update profile information.
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'whatsapp_number' => $request->whatsapp_number,
        ];

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        $user->update($data);

        return redirect()->route('customer.profile.edit')
            ->with('success', 'Profile berhasil diperbarui');
    }

    /**
     * Show change password form.
     */
    public function editPassword()
    {
        return view('customer.profile.password');
    }

    /**
     * Update password.
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini tidak sesuai'
            ]);
        }

        // Update password
        $user->update([
            'password' => $request->password, // Cast 'hashed' di model otomatis meng-hash password
        ]);

        return redirect()->route('customer.profile.edit')
            ->with('success', 'Password berhasil diubah');
    }

    /**
     * Delete avatar.
     */
    public function deleteAvatar()
    {
        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return back()->with('success', 'Foto profil berhasil dihapus');
    }

    /**
     * Submit account verification (phone number + SIM photo) for admin approval.
     */
    public function submitVerification(SubmitVerificationRequest $request)
    {
        $user = Auth::user();

        // Tidak perlu submit ulang jika sudah terverifikasi atau sedang menunggu.
        if ($user->isVerified()) {
            return back()->with('error', 'Akun Anda sudah terverifikasi');
        }
        if ($user->isPendingVerification()) {
            return back()->with('error', 'Verifikasi Anda sedang menunggu konfirmasi admin');
        }

        // Hapus foto SIM lama jika ada (cek disk privat & legacy publik)
        if ($user->sim_photo) {
            Storage::disk('local')->delete($user->sim_photo);
            Storage::disk('public')->delete($user->sim_photo);
        }

        // Simpan ke disk privat 'local' (PII, tidak boleh diakses publik)
        $simPath = $request->file('sim_photo')->store('sim_photos', 'local');

        $user->fill([
            'phone' => $request->phone,
            'sim_photo' => $simPath,
        ]);
        $user->verification_status = 'pending'; // di-set eksplisit (tidak mass-assignable)
        $user->save();

        return redirect()->route('customer.profile.edit')
            ->with('success', 'Pengajuan verifikasi berhasil dikirim. Menunggu konfirmasi admin.');
    }
}
