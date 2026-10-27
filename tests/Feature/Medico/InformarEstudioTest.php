<?php

namespace Tests\Feature\Medico;

use App\Enums\EstadoEstudio;
use App\Models\Estudio;
use App\Models\TipoEstudio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InformarEstudioTest extends TestCase
{
    use RefreshDatabase;

    public function test_medico_puede_acceder_a_pantalla_informar_con_visor_dicom(): void
    {
        $medico = User::factory()->create(['role' => 'medico']);
        $tecnico = User::factory()->create(['role' => 'tecnico']);
        $especialidad = \App\Models\Especialidad::create([
            'nombre' => 'Radiología',
            'slug' => 'radiologia',
        ]);
        $tipo = TipoEstudio::create([
            'nombre' => 'Radiografía de Tórax',
            'especialidad_id' => $especialidad->id,
        ]);

        $estudio = Estudio::create([
            'paciente_nombre' => 'Ana Martínez',
            'paciente_dni' => '30123456',
            'paciente_edad' => 45,
            'fecha_estudio' => now(),
            'tecnico_id' => $tecnico->id,
            'tipo_estudio_id' => $tipo->id,
            'estado' => EstadoEstudio::Nuevo,
            'study_instance_uid' => '1.2.840.113619.2.55.3.123456789.987',
            'accession_number' => 'ACC-98765',
            'modalidad' => 'CR',
            'pacs_status' => 'ONLINE',
        ]);

        $response = $this->actingAs($medico)->get(route('medico.estudios.informar', $estudio->id));

        $response->assertStatus(200);
        $response->assertSee('Visor PACS DICOM');
        $response->assertSee('1.2.840.113619.2.55.3.123456789.987');
        $response->assertSee('Ana Martínez');
    }

    public function test_medico_puede_guardar_y_firmar_informe(): void
    {
        $medico = User::factory()->create(['role' => 'medico']);
        $tecnico = User::factory()->create(['role' => 'tecnico']);
        $especialidad = \App\Models\Especialidad::create([
            'nombre' => 'Radiología',
            'slug' => 'radiologia-2',
        ]);
        $tipo = TipoEstudio::create([
            'nombre' => 'Radiografía de Tórax',
            'especialidad_id' => $especialidad->id,
        ]);

        $estudio = Estudio::create([
            'paciente_nombre' => 'Ana Martínez',
            'paciente_dni' => '30123456',
            'paciente_edad' => 45,
            'fecha_estudio' => now(),
            'tecnico_id' => $tecnico->id,
            'tipo_estudio_id' => $tipo->id,
            'estado' => EstadoEstudio::Nuevo,
        ]);

        $response = $this->actingAs($medico)->post(route('medico.estudios.informar.guardar', $estudio->id), [
            'informe' => 'Campos pulmonares libres de infiltrados focales. Silueta cardiovascular de configuración normal.',
        ]);

        $response->assertRedirect(route('medico.estudios.index'));
        $this->assertDatabaseHas('estudios', [
            'id' => $estudio->id,
            'estado' => EstadoEstudio::Informado->value,
            'medico_id' => $medico->id,
        ]);
    }
}
