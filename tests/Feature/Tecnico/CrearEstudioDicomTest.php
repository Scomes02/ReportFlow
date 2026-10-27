<?php

namespace Tests\Feature\Tecnico;

use App\Models\Especialidad;
use App\Models\TipoEstudio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrearEstudioDicomTest extends TestCase
{
    use RefreshDatabase;

    public function test_tecnico_puede_cargar_estudio_vinculado_a_pacs_dicom(): void
    {
        $tecnico = User::factory()->create(['role' => 'tecnico']);
        $especialidad = Especialidad::create([
            'nombre' => 'Radiología',
            'slug' => 'radiologia',
        ]);
        $tipoEstudio = TipoEstudio::create([
            'especialidad_id' => $especialidad->id,
            'nombre' => 'Tomografía Computada de Tórax',
        ]);

        $response = $this->actingAs($tecnico)->post(route('tecnico.estudios.store'), [
            'paciente_nombre' => 'Carlos Gardel',
            'paciente_dni' => '11223344',
            'paciente_edad' => 52,
            'especialidad_id' => $especialidad->id,
            'tipo_estudio_id' => $tipoEstudio->id,
            'fecha_estudio' => now()->format('Y-m-d H:i'),
            'study_instance_uid' => '1.2.840.113619.2.55.3.99887766.1122',
            'modalidad' => 'CT',
            'accession_number' => 'ACC-20261027-CT01',
        ]);

        $response->assertRedirect(route('tecnico.estudios.index'));

        $this->assertDatabaseHas('estudios', [
            'paciente_nombre' => 'Carlos Gardel',
            'study_instance_uid' => '1.2.840.113619.2.55.3.99887766.1122',
            'modalidad' => 'CT',
            'accession_number' => 'ACC-20261027-CT01',
            'pacs_status' => 'ONLINE',
            'tecnico_id' => $tecnico->id,
            'estado' => 'nuevo',
        ]);
    }
}
