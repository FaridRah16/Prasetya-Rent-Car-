<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Car;
use App\Models\Driver;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin
        User::create([
            'name' => 'Admin Prasetya',
            'email' => 'admin@prasetyarentcar.com',
            'password' => 'password', // Cast 'hashed' di model otomatis meng-hash password
            'role' => 'admin',
            'phone' => '08123456789',
        ]);

        // Create Drivers
        $driver1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@driver.com',
            'password' => 'password', // Cast 'hashed' di model otomatis meng-hash password
            'role' => 'driver',
            'phone' => '08234567890',
        ]);

        Driver::create([
            'user_id' => $driver1->id,
            'license_number' => 'B123456789',
            'status' => 'available',
        ]);

        $driver2 = User::create([
            'name' => 'Agus Wijaya',
            'email' => 'agus@driver.com',
            'password' => 'password', // Cast 'hashed' di model otomatis meng-hash password
            'role' => 'driver',
            'phone' => '08345678901',
        ]);

        Driver::create([
            'user_id' => $driver2->id,
            'license_number' => 'B987654321',
            'status' => 'available',
        ]);

        // Create Customers
        User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@customer.com',
            'password' => 'password', // Cast 'hashed' di model otomatis meng-hash password
            'role' => 'customer',
            'phone' => '08456789012',
        ]);

        User::create([
            'name' => 'Andi Pratama',
            'email' => 'andi@customer.com',
            'password' => 'password', // Cast 'hashed' di model otomatis meng-hash password
            'role' => 'customer',
            'phone' => '08567890123',
        ]);

        User::create([
            'name' => 'Dewi Lestari',
            'email' => 'dewi@customer.com',
            'password' => 'password', // Cast 'hashed' di model otomatis meng-hash password
            'role' => 'customer',
            'phone' => '08678901234',
        ]);

        // Create Cars
        Car::create([
            'name' => 'Toyota Avanza',
            'brand' => 'Toyota',
            'type' => 'MPV',
            'year' => 2023,
            'color' => 'Putih',
            'plate_number' => 'B 1234 ABC',
            'price_per_day' => 350000,
            'status' => 'available',
            'seats' => 7,
            'description' => 'Mobil keluarga yang nyaman dan irit bahan bakar. Cocok untuk perjalanan keluarga atau rombongan.',
        ]);

        Car::create([
            'name' => 'Honda Jazz',
            'brand' => 'Honda',
            'type' => 'Hatchback',
            'year' => 2022,
            'color' => 'Hitam',
            'plate_number' => 'B 5678 DEF',
            'price_per_day' => 300000,
            'status' => 'available',
            'seats' => 5,
            'description' => 'Mobil city car yang lincah dan ekonomis. Sempurna untuk perjalanan dalam kota.',
        ]);

        Car::create([
            'name' => 'Mitsubishi Xpander',
            'brand' => 'Mitsubishi',
            'type' => 'MPV',
            'year' => 2023,
            'color' => 'Silver',
            'plate_number' => 'B 9012 GHI',
            'price_per_day' => 400000,
            'status' => 'available',
            'seats' => 7,
            'description' => 'MPV modern dengan desain sporty dan ruang kabin yang luas.',
        ]);

        Car::create([
            'name' => 'Toyota Innova Reborn',
            'brand' => 'Toyota',
            'type' => 'MPV',
            'year' => 2022,
            'color' => 'Abu-abu',
            'plate_number' => 'B 3456 JKL',
            'price_per_day' => 450000,
            'status' => 'available',
            'seats' => 8,
            'description' => 'Mobil keluarga premium dengan kenyamanan maksimal untuk perjalanan jauh.',
        ]);

        Car::create([
            'name' => 'Daihatsu Terios',
            'brand' => 'Daihatsu',
            'type' => 'SUV',
            'year' => 2021,
            'color' => 'Merah',
            'plate_number' => 'B 7890 MNO',
            'price_per_day' => 380000,
            'status' => 'available',
            'seats' => 7,
            'description' => 'SUV tangguh untuk berbagai medan. Ideal untuk perjalanan adventure.',
        ]);
    }
}
