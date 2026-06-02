<?php

namespace Tests\Feature\Middleware;

use App\Models\Usuario;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolSeeder::class);
    }

    public function test_guest_is_redirected_from_dashboard_to_login(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_client_can_access_dashboard(): void
    {
        $usuario = Usuario::factory()->create();

        $this->actingAs($usuario)
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_admin_is_redirected_to_admin_panel_after_login(): void
    {
        $admin = Usuario::factory()->admin()->create([
            'Usu_Correo' => 'admin.middleware@example.com',
        ]);

        $this->post('/login', [
            'email' => $admin->Usu_Correo,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_guest_cannot_access_login_when_already_authenticated(): void
    {
        $usuario = Usuario::factory()->create();

        $this->actingAs($usuario)
            ->get(route('login'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_admin_guest_redirect_from_login_goes_to_admin_dashboard(): void
    {
        $admin = Usuario::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('login'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_non_admin_cannot_access_admin_routes(): void
    {
        $usuario = Usuario::factory()->create();

        $this->actingAs($usuario)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_passes_admin_middleware(): void
    {
        $admin = Usuario::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.marcas.index'));

        $this->assertNotEquals(403, $response->status(), 'Un administrador no debe recibir 403 por el middleware admin.');
        $this->assertFalse($response->isRedirect(route('login')));
    }
}
