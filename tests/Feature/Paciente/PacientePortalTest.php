<?php

namespace Tests\Feature\Paciente;

use App\Enums\EstadoEstudio;
use App\Enums\SemaforoSalud;
use App\Models\Especialidad;
use App\Models\Estudio;
use App\Models\TipoEstudio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PacientePortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_portal_del_paciente_muestra_informe_con_semaforo_e_historial(): void
    {
        $medico = User::factory()->create(['role' => 'medico', 'name' => 'Dr. Fernando Rossi']);
        $tecnico = User::factory()->create(['role' => 'tecnico']);

        $especialidad = Especialidad::create(['nombre' => 'Radiología', 'slug' => 'radiologia']);
        $tipo = TipoEstudio::create(['nombre' => 'Radiografía de Tórax', 'especialidad_id' => $especialidad->id]);

        $estudio = Estudio::create([
            'paciente_nombre' => 'Roberto Álvarez',
            'paciente_dni' => '28451236',
            'paciente_edad' => 46,
            'tipo_estudio_id' => $tipo->id,
            'region_anatomica' => 'torax',
            'tecnico_id' => $tecnico->id,
            'medico_id' => $medico->id,
            'estado' => EstadoEstudio::Informado,
            'fecha_estudio' => now(),
            'firmado_at' => now(),
            'informe' => 'Silueta cardíaca dentro de límites normales. Parénquima pulmonar sin infiltrados.',
            'semaforo_salud' => SemaforoSalud::Favorable,
            'semaforo_evolucion' => 'verde',
        ]);

        $response = $this->get(route('paciente.informe', $estudio->id));

        $response->assertStatus(200);
        $response->assertSee('Informe de Diagnóstico Médico');
        $response->assertSee('Roberto Álvarez');
        $response->assertSee('Semáforo del Proceso del Paciente');
        $response->assertSee('Evolución Favorable / Normal');
        $response->assertSee('Dr. Fernando Rossi');
        $response->assertSee('Imprimir / Descargar PDF');
    }

    public function test_call_center_muestra_columna_de_semaforo_y_boton_ver_imprimir(): void
    {
        $callcenterUser = User::factory()->create(['role' => 'callcenter']);
        $medico = User::factory()->create(['role' => 'medico']);
        $tecnico = User::factory()->create(['role' => 'tecnico']);

        $especialidad = Especialidad::create(['nombre' => 'Cardiología', 'slug' => 'cardiologia']);
        $tipo = TipoEstudio::create(['nombre' => 'Electrocardiograma', 'especialidad_id' => $especialidad->id]);

        $estudio = Estudio::create([
            'paciente_nombre' => 'Carlos Gardel',
            'paciente_dni' => '12345678',
            'paciente_edad' => 50,
            'tipo_estudio_id' => $tipo->id,
            'tecnico_id' => $tecnico->id,
            'medico_id' => $medico->id,
            'estado' => EstadoEstudio::Informado,
            'fecha_estudio' => now(),
            'firmado_at' => now(),
            'informe' => 'Ritmo sinusal regular.',
            'semaforo_salud' => SemaforoSalud::Estable,
            'semaforo_evolucion' => 'azul',
        ]);

        $response = $this->actingAs($callcenterUser)->get(route('callcenter.informes'));

        $response->assertStatus(200);
        $response->assertSee('Evolución (Semáforo)');
        $response->assertSee('Ver / Imprimir');
        $response->assertSee(route('paciente.informe', $estudio->id));
    }
}
