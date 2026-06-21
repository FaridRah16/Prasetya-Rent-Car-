<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecureFileAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_sim_file(): void
    {
        $user = User::factory()->create();

        $this->get(route('secure.sim', $user->id))
            ->assertRedirect(route('login'));
    }

    public function test_customer_cannot_access_other_users_sim(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($other)
            ->get(route('secure.sim', $owner->id))
            ->assertForbidden();
    }

    public function test_owner_passes_authorization(): void
    {
        $owner = User::factory()->create();

        // Tidak ada file → 404 (artinya otorisasi LOLOS, bukan 403).
        $this->actingAs($owner)
            ->get(route('secure.sim', $owner->id))
            ->assertNotFound();
    }

    public function test_admin_passes_authorization(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $owner = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('secure.sim', $owner->id))
            ->assertNotFound();
    }
}
