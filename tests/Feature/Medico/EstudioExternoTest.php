<?php

namespace Tests\Feature\Medico;

use App\Enums\EstadoEstudio;
use App\Models\Especialidad;
use App\Models\Estudio;
use App\Models\TipoEstudio;
use App\Models\User;
use App\Services\HistorialPacienteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EstudioExternoTest extends TestCase
{
    use RefreshDatabase;

    public function test_medico_puede_adjuntar_estudio_externo_pdf_al_historial_del_paciente(): void
    {
        Storage::fake('estudios');

        $especialidad = Especialidad::create([
            'nombre' => 'Cardiología',
            'slug' => 'cardiologia',
        ]);
        $tipo = TipoEstudio::create([
            'nombre' => 'Electrocardiograma',
            'especialidad_id' => $especialidad->id,
        ]);

        $medico = User::factory()->create([
            'role' => 'medico',
            'especialidad_id' => $especialidad->id,
        ]);
        $tecnico = User::factory()->create(['role' => 'tecnico']);

        $estudioActual = Estudio::create([
            'paciente_nombre' => 'Juan Pérez',
            'paciente_dni' => '20123456',
            'paciente_edad' => 52,
            'fecha_estudio' => now(),
            'tecnico_id' => $tecnico->id,
            'tipo_estudio_id' => $tipo->id,
            'estado' => EstadoEstudio::Nuevo,
        ]);

        // Simular archivo PDF aportado por el paciente
        $pdfFile = UploadedFile::fake()->create('ecg_anterior_2022.pdf', 500, 'application/pdf');

        $response = $this->actingAs($medico)->post(route('medico.estudios.adjuntar-externo', $estudioActual->id), [
            'archivo' => $pdfFile,
            'fecha_estudio' => '2023-05-10',
            'tipo_estudio_id' => $tipo->id,
            'institucion_origen' => 'Sanatorio Allende',
            'informe' => 'Trazado previo con ritmo sinusal sin alteraciones agudas.',
            'semaforo_salud' => 'favorable',
        ]);

        $response->assertRedirect(route('medico.estudios.informar', $estudioActual->id));
        $response->assertSessionHas('status');

        // Verificar que se guardó el estudio como externo
        $this->assertDatabaseHas('estudios', [
            'paciente_dni' => '20123456',
            'es_externo' => true,
            'institucion_origen' => 'Sanatorio Allende',
            'modalidad' => 'PDF',
            'estado' => EstadoEstudio::Informado->value,
        ]);

        $estudioExterno = Estudio::where('es_externo', true)->first();
        $this->assertNotNull($estudioExterno);
        $this->assertCount(1, $estudioExterno->archivos);

        // Verificar integración en Línea de Tiempo
        $historialService = app(HistorialPacienteService::class);
        $timeline = $historialService->obtenerLineaDeTiempo('20123456', $estudioActual->id);

        $itemExterno = $timeline->firstWhere('id', $estudioExterno->id);
        $this->assertNotNull($itemExterno);
        $this->assertTrue($itemExterno['es_externo']);
        $this->assertTrue($itemExterno['es_pdf']);
        $this->assertEquals('Sanatorio Allende', $itemExterno['institucion_origen']);

        // Verificar que el médico puede acceder al archivo PDF sin 403
        $archivo = $estudioExterno->archivos->first();
        $responseArchivo = $this->actingAs($medico)->get(route('medico.estudios.archivos.ver', [$estudioExterno->id, $archivo->id]));
        $responseArchivo->assertStatus(200);
    }
}
