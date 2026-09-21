<?php

namespace Tests\Feature\Tecnico;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermisosTecnicoTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_usuario_no_autenticado_no_puede_ingresar_al_modulo_tecnico(): void
    {
        $this->markTestIncomplete(
            'Pendiente: definir la ruta de autenticación del proyecto. Actualmente el middleware auth intenta redirigir a /login, pero ReportFlow utiliza /dev-login.'
        );
    }
}