<?php

namespace Tests\Feature\Auth;

use App\Models\Usuario;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolSeeder::class);
    }

    public function test_password_can_be_updated_from_dashboard_profile(): void
    {
        $user = Usuario::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('dashboard'))
            ->put(route('dashboard.profile.password'), [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('tab', 'profile');

        $this->assertTrue(Hash::check('new-password', $user->refresh()->Usu_Pass));
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $user = Usuario::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('dashboard'))
            ->put(route('dashboard.profile.password'), [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('password', 'current_password')
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('tab', 'profile');
    }
}
