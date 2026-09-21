<?php

namespace Tests\Feature\Tecnico;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstudioControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_tecnico_puede_ver_la_pantalla_de_estudios(): void
    {
        // Arrange
        $tecnico = User::factory()->create();

        $this->actingAs($tecnico);

        // Act
        $response = $this->get('/tecnico/estudios');

        // Assert
        $response->assertStatus(200);
    }
}