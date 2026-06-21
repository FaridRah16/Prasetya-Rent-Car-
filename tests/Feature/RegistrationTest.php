<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_customer_account(): void
    {
        $this->post('/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '08123456789',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $user = User::where('email', 'budi@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame('customer', $user->role);
        $this->assertSame('unverified', $user->verification_status);
    }

    public function test_register_ignores_role_and_verification_from_request(): void
    {
        // Upaya privilege-escalation: kirim role=admin & verified lewat form.
        $this->post('/register', [
            'name' => 'Penyusup',
            'email' => 'penyusup@example.com',
            'phone' => '08123456789',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
            'role' => 'admin',
            'verification_status' => 'verified',
        ]);

        $user = User::where('email', 'penyusup@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame('customer', $user->role, 'role dari request harus diabaikan');
        $this->assertSame('unverified', $user->verification_status, 'verification_status dari request harus diabaikan');
    }
}
