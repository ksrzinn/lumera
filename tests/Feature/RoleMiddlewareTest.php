<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_access_patient_area(): void
    {
        $user = User::factory()->patient()->create();

        $this->actingAs($user)->get('/paciente')->assertOk();
    }

    public function test_professional_can_access_professional_area(): void
    {
        $user = User::factory()->professional()->create();

        $this->actingAs($user)->get('/profissional')->assertOk();
    }

    public function test_patient_cannot_access_professional_area(): void
    {
        $user = User::factory()->patient()->create();

        $this->actingAs($user)->get('/profissional')->assertForbidden();
    }

    public function test_professional_cannot_access_patient_area(): void
    {
        $user = User::factory()->professional()->create();

        $this->actingAs($user)->get('/paciente')->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/paciente')->assertRedirect('/login');
        $this->get('/profissional')->assertRedirect('/login');
    }

    public function test_dashboard_redirects_each_role_to_its_own_area(): void
    {
        $patient = User::factory()->patient()->create();
        $professional = User::factory()->professional()->create();

        $this->actingAs($patient)->get('/dashboard')->assertRedirect('/paciente');
        $this->actingAs($professional)->get('/dashboard')->assertRedirect('/profissional');
    }

    public function test_login_redirects_each_role_to_its_own_area(): void
    {
        $patient = User::factory()->patient()->create();
        $this->post('/login', ['email' => $patient->email, 'password' => 'password'])
            ->assertRedirect('/paciente');
        $this->post('/logout');

        $professional = User::factory()->professional()->create();
        $this->post('/login', ['email' => $professional->email, 'password' => 'password'])
            ->assertRedirect('/profissional');
    }
}
