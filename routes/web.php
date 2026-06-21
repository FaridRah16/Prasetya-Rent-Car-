<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Driver\DashboardController as DriverDashboardController;
use App\Http\Controllers\Public\CarController;
use App\Http\Controllers\SecureFileController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    // Selaras dengan katalog: tampilkan mobil tersedia & yang sedang disewa
    // (sembunyikan hanya yang 'maintenance').
    $featuredCars = \App\Models\Car::whereIn('status', ['available', 'rented'])
        ->with('activeBooking')
        ->orderBy('created_at', 'desc')
        ->take(6)
        ->get();
    return view('public.home', compact('featuredCars'));
})->name('home');

Route::get('/cars', [CarController::class, 'index'])->name('cars.index');
Route::get('/cars/{id}', [CarController::class, 'show'])->name('cars.show');

// Berkas PII (foto SIM, bukti bayar/pengantaran) disajikan ber-otorisasi.
Route::middleware('auth')->prefix('secure')->name('secure.')->group(function () {
    Route::get('/users/{id}/sim', [SecureFileController::class, 'sim'])->name('sim');
    Route::get('/bookings/{id}/payment', [SecureFileController::class, 'payment'])->name('payment');
    Route::get('/bookings/{id}/delivery', [SecureFileController::class, 'delivery'])->name('delivery');
});

Route::get('/about', function () {
    return view('public.about');
})->name('about');

Route::get('/contact', function () {
    return view('public.contact');
})->name('contact');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Cars
    Route::get('/cars', [\App\Http\Controllers\Admin\CarController::class, 'index'])->name('cars.index');
    Route::get('/cars/create', [\App\Http\Controllers\Admin\CarController::class, 'create'])->name('cars.create');
    Route::post('/cars', [\App\Http\Controllers\Admin\CarController::class, 'store'])->name('cars.store');
    Route::get('/cars/{id}', [\App\Http\Controllers\Admin\CarController::class, 'show'])->name('cars.show');
    Route::get('/cars/{id}/edit', [\App\Http\Controllers\Admin\CarController::class, 'edit'])->name('cars.edit');
    Route::put('/cars/{id}', [\App\Http\Controllers\Admin\CarController::class, 'update'])->name('cars.update');
    Route::delete('/cars/{id}', [\App\Http\Controllers\Admin\CarController::class, 'destroy'])->name('cars.destroy');
    Route::post('/cars/{id}/toggle-status', [\App\Http\Controllers\Admin\CarController::class, 'toggleStatus'])->name('cars.toggleStatus');
    
    // Bookings
    Route::get('/bookings', [\App\Http\Controllers\Admin\BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{id}', [\App\Http\Controllers\Admin\BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{id}/update-status', [\App\Http\Controllers\Admin\BookingController::class, 'updateStatus'])->name('bookings.updateStatus');
    Route::post('/bookings/{id}/assign-driver', [\App\Http\Controllers\Admin\BookingController::class, 'assignDriver'])->name('bookings.assignDriver');
    Route::post('/bookings/{id}/verify-payment', [\App\Http\Controllers\Admin\BookingController::class, 'verifyPayment'])->name('bookings.verifyPayment');
    Route::post('/bookings/{id}/reject-payment', [\App\Http\Controllers\Admin\BookingController::class, 'rejectPayment'])->name('bookings.rejectPayment');
    
    // Users
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
    Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
    Route::get('/users/{id}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{id}/verify', [\App\Http\Controllers\Admin\UserController::class, 'verifyUser'])->name('users.verify');
    Route::post('/users/{id}/reject-verification', [\App\Http\Controllers\Admin\UserController::class, 'rejectVerification'])->name('users.rejectVerification');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
});

// Customer Routes
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    
    // Bookings
    Route::get('/bookings', [\App\Http\Controllers\Customer\BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [\App\Http\Controllers\Customer\BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [\App\Http\Controllers\Customer\BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{id}', [\App\Http\Controllers\Customer\BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{id}/upload-payment', [\App\Http\Controllers\Customer\BookingController::class, 'uploadPayment'])->name('bookings.uploadPayment');
    Route::post('/bookings/{id}/cancel', [\App\Http\Controllers\Customer\BookingController::class, 'cancel'])->name('bookings.cancel');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/password', [ProfileController::class, 'editPassword'])->name('profile.password');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.deleteAvatar');
    Route::post('/profile/verification', [ProfileController::class, 'submitVerification'])->name('profile.verification');
});

// Driver Routes
Route::middleware(['auth', 'role:driver'])->prefix('driver')->name('driver.')->group(function () {
    Route::get('/dashboard', [DriverDashboardController::class, 'index'])->name('dashboard');
    
    // Tasks
    Route::get('/tasks', [\App\Http\Controllers\Driver\TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/history', [\App\Http\Controllers\Driver\TaskController::class, 'history'])->name('tasks.history');
    Route::get('/tasks/{id}', [\App\Http\Controllers\Driver\TaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{id}/start', [\App\Http\Controllers\Driver\TaskController::class, 'startTask'])->name('tasks.start');
    Route::post('/tasks/{id}/complete', [\App\Http\Controllers\Driver\TaskController::class, 'completeTask'])->name('tasks.complete');
});
