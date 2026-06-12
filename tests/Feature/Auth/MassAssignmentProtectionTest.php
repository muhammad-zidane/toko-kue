<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MassAssignmentProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_register_with_admin_role_via_mass_assignment(): void
    {
        // Attempt to register with 'role'=>'admin' in payload
        $response = $this->post('/register', [
            'name' => 'Hacker User',
            'email' => 'hacker@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'admin',  // ← Attempt injection
        ]);

        // User should still be authenticated (registration successful)
        $this->assertAuthenticated();

        // But role must remain 'customer', not 'admin'
        $this->assertDatabaseHas('users', [
            'email' => 'hacker@example.com',
            'role' => 'customer',  // ← Role not 'admin'
        ]);
    }
}
