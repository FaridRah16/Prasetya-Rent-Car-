<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(LoginRequest $request)
    {
        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect based on role
            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->isDriver()) {
                return redirect()->intended(route('driver.dashboard'));
            } else {
                return redirect()->intended(route('customer.dashboard'));
            }
        }

        throw ValidationException::withMessages([
            'email' => 'Email atau password salah.',
        ]);
    }

    /**
     * Show register form.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle register request.
     */
    public function register(RegisterRequest $request)
    {
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password, // Cast 'hashed' di model otomatis meng-hash password
        ]);
        $user->role = 'customer'; // di-set eksplisit (role tidak mass-assignable)
        $user->save();

        Auth::login($user);

        return redirect()->route('customer.dashboard')->with('success', 'Registrasi berhasil! Selamat datang di Prasetya Rent Car.');
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah keluar.');
    }

    /**
     * Show "lupa password" form.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Kirim link reset password ke email.
     *
     * Pesan sukses bersifat netral (tidak membocorkan apakah email terdaftar)
     * untuk mencegah enumerasi akun.
     */
    public function sendResetLink(ForgotPasswordRequest $request)
    {
        PasswordBroker::sendResetLink($request->only('email'));

        return back()->with('success', 'Jika email terdaftar, link reset password telah dikirim. Silakan cek email Anda.');
    }

    /**
     * Show form reset password (dari link di email).
     */
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Proses reset password.
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = PasswordBroker::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->password = $password; // Cast 'hashed' meng-hash otomatis
                $user->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === PasswordBroker::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login dengan password baru.')
            : back()->withErrors(['email' => __($status)]);
    }
}
