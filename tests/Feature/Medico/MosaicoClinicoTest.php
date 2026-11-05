<?php

namespace Tests\Feature\Medico;

use App\Models\Especialidad;
use App\Models\Estudio;
use App\Models\TipoEstudio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MosaicoClinicoTest extends TestCase
{
    use RefreshDatabase;

    public function test_medico_puede_ver_mosaico_clinico_y_estado_general_del_paciente(): void
    {
        $medico = User::factory()->create(['role' => 'medico']);
        $tecnico = User::factory()->create(['role' => 'tecnico']);

        $especialidad = Especialidad::create(['nombre' => 'Cardiología']);
        $tipo = TipoEstudio::create(['nombre' => 'Ecocardiograma Doppler', 'especialidad_id' => $especialidad->id]);

        // Estudio histórico previo del mismo paciente
        Estudio::create([
            'paciente_nombre' => 'Alberto Cortéz',
            'paciente_dni' => '14234567',
            'paciente_edad' => 60,
            'fecha_estudio' => now()->subMonths(3),
            'tipo_estudio_id' => $tipo->id,
            'estado' => 'informado',
            'tecnico_id' => $tecnico->id,
            'medico_id' => $medico->id,
            'informe' => 'Hipertrofia concéntrica severa y cardiomegalia en seguimiento.',
        ]);

        // Estudio en curso del paciente
        $estudioActual = Estudio::create([
            'paciente_nombre' => 'Alberto Cortéz',
            'paciente_dni' => '14234567',
            'paciente_edad' => 60,
            'fecha_estudio' => now(),
            'tipo_estudio_id' => $tipo->id,
            'estado' => 'pendiente',
            'tecnico_id' => $tecnico->id,
        ]);

        $this->actingAs($medico);

        $response = $this->get(route('medico.estudios.informar', $estudioActual->id));

        $response->assertStatus(200);
        $response->assertViewHas('mosaicoClinico');
        
        $mosaicoClinico = $response->viewData('mosaicoClinico');
        $this->assertEquals(2, $mosaicoClinico['total_estudios']);
        $this->assertEquals('atencion', $mosaicoClinico['estado_general']['nivel']);

        // Verificar que la vista contiene la comparativa lado a lado, timeline y semáforo
        $response->assertSee('Línea de Tiempo del Paciente (Timeline Clínico)');
        $response->assertSee('Semáforo de Evolución');
        $response->assertSee('Alberto Cortéz');
    }
}
