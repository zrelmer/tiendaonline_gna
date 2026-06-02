<?php

namespace Tests\Feature\Auth;

use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolSeeder::class);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'Usu_Nombre' => 'Usuario prueba',
            'Usu_Correo' => 'nuevo@example.com',
            'Usu_Telefono' => '55551234',
            'Usu_Pass' => 'password',
            'Usu_Pass_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertDatabaseHas('tb_usuario', [
            'Usu_Correo' => 'nuevo@example.com',
        ]);
    }
}
