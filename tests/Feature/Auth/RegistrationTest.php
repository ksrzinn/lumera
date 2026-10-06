<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'data_nascimento' => '1990-05-10',
            'role' => 'professional',
            'password' => 'password',
            'password_confirmation' => 'password',
            'consent' => true,
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'test@example.com', 'role' => 'professional']);
        $response->assertRedirect('/profissional');
    }

    public function test_registration_requires_consent_and_valid_role(): void
    {
        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'data_nascimento' => '1990-05-10',
            'role' => 'admin',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors(['consent', 'role']);

        $this->assertGuest();
    }
}
