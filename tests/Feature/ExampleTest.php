<?php

namespace Tests\Feature;

use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolSeeder::class);
    }

    public function test_login_page_returns_a_successful_response(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
    }
}
