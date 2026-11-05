<?php

namespace Tests\Unit;

use App\Models\Especialidad;
use App\Models\Estudio;
use App\Models\TipoEstudio;
use App\Models\User;
use App\Services\Dcm4cheeService;
use App\Services\EnsambladoClinicoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class EnsambladoClinicoServiceTest extends TestCase
{
    use RefreshDatabase;

    private EnsambladoClinicoService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $pacsMock = Mockery::mock(Dcm4cheeService::class);
        $pacsMock->shouldReceive('obtenerUrlRenderDicom')->andReturn(null);
        $this->service = new EnsambladoClinicoService($pacsMock);
    }

    public function test_evalua_paciente_sin_informes_previos_como_basal_inicial(): void
    {
        $especialidad = Especialidad::create(['nombre' => 'Cardiología']);
        $tipo = TipoEstudio::create(['nombre' => 'Electrocardiograma', 'especialidad_id' => $especialidad->id]);

        $estudio = Estudio::create([
            'paciente_nombre' => 'Carlos Gardel',
            'paciente_dni' => '12345678',
            'paciente_edad' => 45,
            'fecha_estudio' => now(),
            'tipo_estudio_id' => $tipo->id,
            'estado' => 'pendiente',
            'tecnico_id' => User::factory()->create(['role' => 'tecnico'])->id,
        ]);

        $resultado = $this->service->obtenerMosaicoYEstadoGeneral($estudio);

        $this->assertEquals('12345678', $resultado['paciente_dni']);
        $this->assertEquals(1, $resultado['total_estudios']);
        $this->assertEquals('inicial', $resultado['estado_general']['nivel']);
        $this->assertEquals('🩺', $resultado['estado_general']['icono']);
    }

    public function test_detecta_alerta_clinica_con_hallazgos_criticos(): void
    {
        $especialidad = Especialidad::create(['nombre' => 'Radiología']);
        $tipo = TipoEstudio::create(['nombre' => 'Radiografía de Tórax', 'especialidad_id' => $especialidad->id]);
        $tecnico = User::factory()->create(['role' => 'tecnico']);
        $medico = User::factory()->create(['role' => 'medico']);

        // Estudio previo con hallazgo crítico
        Estudio::create([
            'paciente_nombre' => 'María López',
            'paciente_dni' => '20111222',
            'paciente_edad' => 52,
            'fecha_estudio' => now()->subMonths(2),
            'tipo_estudio_id' => $tipo->id,
            'estado' => 'informado',
            'tecnico_id' => $tecnico->id,
            'medico_id' => $medico->id,
            'informe' => 'Se observa derrame pleural y marcada cardiomegalia.',
        ]);

        // Estudio actual
        $estudioActual = Estudio::create([
            'paciente_nombre' => 'María López',
            'paciente_dni' => '20111222',
            'paciente_edad' => 52,
            'fecha_estudio' => now(),
            'tipo_estudio_id' => $tipo->id,
            'estado' => 'pendiente',
            'tecnico_id' => $tecnico->id,
        ]);

        $resultado = $this->service->obtenerMosaicoYEstadoGeneral($estudioActual);

        $this->assertEquals(2, $resultado['total_estudios']);
        $this->assertEquals('atencion', $resultado['estado_general']['nivel']);
        $this->assertEquals('⚠️', $resultado['estado_general']['icono']);
        $this->assertNotEmpty($resultado['estado_general']['alertas']);
        $this->assertEquals('atencion', $resultado['mosaico'][0]['severidad']);
    }

    public function test_evalua_paciente_con_evolucion_favorable(): void
    {
        $especialidad = Especialidad::create(['nombre' => 'Traumatología']);
        $tipo = TipoEstudio::create(['nombre' => 'Resonancia de Rodilla', 'especialidad_id' => $especialidad->id]);
        $tecnico = User::factory()->create(['role' => 'tecnico']);
        $medico = User::factory()->create(['role' => 'medico']);

        // Estudio previo normal
        Estudio::create([
            'paciente_nombre' => 'Juan Pérez',
            'paciente_dni' => '30444555',
            'paciente_edad' => 30,
            'fecha_estudio' => now()->subMonth(),
            'tipo_estudio_id' => $tipo->id,
            'estado' => 'informado',
            'tecnico_id' => $tecnico->id,
            'medico_id' => $medico->id,
            'informe' => 'Estructuras óseas y ligamentos normales, sin signos patológicos.',
        ]);

        // Estudio actual
        $estudioActual = Estudio::create([
            'paciente_nombre' => 'Juan Pérez',
            'paciente_dni' => '30444555',
            'paciente_edad' => 30,
            'fecha_estudio' => now(),
            'tipo_estudio_id' => $tipo->id,
            'estado' => 'pendiente',
            'tecnico_id' => $tecnico->id,
        ]);

        $resultado = $this->service->obtenerMosaicoYEstadoGeneral($estudioActual);

        $this->assertEquals(2, $resultado['total_estudios']);
        $this->assertTrue($resultado['tiene_anterior']);
        $this->assertNotNull($resultado['estudio_anterior']);
        $this->assertNotEmpty($resultado['timeline']);
        $this->assertNotEmpty($resultado['cruce_multidisciplinario']);
        $this->assertEquals('Traumatología', $resultado['cruce_multidisciplinario'][0]['especialidad']);
        $this->assertEquals('#f59e0b', $resultado['cruce_multidisciplinario'][0]['meta_especialidad']['color']);
        $this->assertEquals('favorable', $resultado['estado_general']['nivel']);
        $this->assertEquals('✅', $resultado['estado_general']['icono']);
    }
}
