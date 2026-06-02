<?php

namespace Tests\Unit;

use App\Models\Movimiento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MovimientoModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_or_create_returns_a_valid_movement_id(): void
    {
        $movimiento = Movimiento::query()->firstOrCreate([
            'Nom_Movimiento' => 'Reserva por pedido',
        ]);

        $this->assertGreaterThan(0, (int) $movimiento->getKey());
        $this->assertSame((int) $movimiento->getKey(), (int) $movimiento->Id_Movimiento);
    }
}
