<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        return $admin;
    }

    public function test_admin_can_store_a_car(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.cars.store'), [
                'name' => 'Toyota Avanza',
                'brand' => 'Toyota',
                'type' => 'MPV',
                'year' => 2022,
                'color' => 'Putih',
                'plate_number' => 'B 1234 XYZ',
                'price_per_day' => 350000,
                'seats' => 7,
                'transmission' => 'Automatic',
                'fuel' => 'Bensin',
                'status' => 'available',
            ])
            ->assertRedirect(route('admin.cars.index'));

        $this->assertDatabaseHas('cars', [
            'plate_number' => 'B 1234 XYZ',
            'transmission' => 'Automatic',
            'fuel' => 'Bensin',
        ]);
    }

    public function test_store_car_fails_validation_when_required_fields_missing(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.cars.store'), [])
            ->assertSessionHasErrors(['name', 'brand', 'transmission', 'fuel']);
    }
}
