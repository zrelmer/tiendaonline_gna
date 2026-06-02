<?php

namespace Database\Factories;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Usuario>
 */
class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'Usu_Nombre' => fake()->name(),
            'Usu_Correo' => fake()->unique()->safeEmail(),
            'Usu_Telefono' => fake()->numerify('########'),
            'Usu_Pass' => static::$password ??= Hash::make('password'),
            'Id_Rol' => Usuario::ROL_USUARIO,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'Id_Rol' => Usuario::ROL_ADMIN,
        ]);
    }
}
