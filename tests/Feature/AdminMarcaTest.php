<?php

namespace Tests\Feature;

use App\Models\Marca;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsCatalog;
use Tests\TestCase;

class AdminMarcaTest extends TestCase
{
    use RefreshDatabase;
    use SeedsCatalog;

    public function test_admin_can_create_update_and_delete_marca_without_products(): void
    {
        $this->seedRolesAndStatuses();
        $admin = Usuario::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.marcas.store'), [
                'Nom_Marca' => 'Sony',
                'slug_Marca' => 'sony',
                'Descrip_Marca' => 'Marca Sony de prueba',
            ])
            ->assertRedirect(route('admin.marcas.index'))
            ->assertSessionHas('success');

        $marca = Marca::query()->where('slug_Marca', 'sony')->first();
        $this->assertNotNull($marca);

        $this->actingAs($admin)
            ->put(route('admin.marcas.update', $marca), [
                'Nom_Marca' => 'Sony Corp',
                'slug_Marca' => 'sony-corp',
                'Descrip_Marca' => 'Descripción actualizada',
            ])
            ->assertRedirect(route('admin.marcas.edit', $marca))
            ->assertSessionHas('success');

        $marca->refresh();
        $this->assertSame('Sony Corp', $marca->Nom_Marca);

        $this->actingAs($admin)
            ->delete(route('admin.marcas.destroy', $marca))
            ->assertRedirect(route('admin.marcas.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('tb_marca', ['Id_Marca' => $marca->Id_Marca]);
    }

    public function test_admin_cannot_delete_marca_with_products(): void
    {
        $this->seedRolesAndStatuses();
        $admin = Usuario::factory()->admin()->create();
        $marca = $this->createMarca(['Nom_Marca' => 'HP', 'slug_Marca' => 'hp']);
        $this->createProducto(marca: $marca);

        $this->actingAs($admin)
            ->delete(route('admin.marcas.destroy', $marca))
            ->assertRedirect(route('admin.marcas.index'))
            ->assertSessionHasErrors('marca');

        $this->assertDatabaseHas('tb_marca', ['Id_Marca' => $marca->Id_Marca]);
    }

    public function test_regular_user_cannot_access_admin_marcas(): void
    {
        $this->seedRolesAndStatuses();
        $usuario = Usuario::factory()->create();

        $this->actingAs($usuario)
            ->get(route('admin.marcas.index'))
            ->assertForbidden();
    }
}
