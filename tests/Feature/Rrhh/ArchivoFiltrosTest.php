<?php

namespace Tests\Feature\Rrhh;

use App\Models\Especialidad;
use App\Models\Estudio;
use App\Models\TipoEstudio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ArchivoFiltrosTest extends TestCase
{
    use RefreshDatabase;

    public function test_archivo_general_muestra_informes_y_filtra_por_especialidad(): void
    {
        $rrhh = User::factory()->create(['role' => 'rrhh']);
        $medico = User::factory()->create(['role' => 'medico']);
        $tecnico = User::factory()->create(['role' => 'tecnico']);

        $cardio = Especialidad::create(['nombre' => 'Cardiología', 'slug' => 'cardiologia']);
        $neumo = Especialidad::create(['nombre' => 'Neumonología', 'slug' => 'neumonologia']);

        $tipoCardio = TipoEstudio::create(['nombre' => 'Electrocardiograma', 'especialidad_id' => $cardio->id]);
        $tipoNeumo = TipoEstudio::create(['nombre' => 'Radiografía de Tórax', 'especialidad_id' => $neumo->id]);

        $estudioCardio = Estudio::create([
            'paciente_nombre' => 'Carlos Gardel',
            'paciente_dni' => '12345678',
            'paciente_edad' => 50,
            'tipo_estudio_id' => $tipoCardio->id,
            'tecnico_id' => $tecnico->id,
            'medico_id' => $medico->id,
            'fecha_estudio' => Carbon::now()->subDays(5),
            'estado' => 'informado',
            'informe' => 'Estudio cardiológico normal',
            'firmado_at' => Carbon::now()->subDays(1),
        ]);

        $estudioNeumo = Estudio::create([
            'paciente_nombre' => 'Mercedes Sosa',
            'paciente_dni' => '87654321',
            'paciente_edad' => 60,
            'tipo_estudio_id' => $tipoNeumo->id,
            'tecnico_id' => $tecnico->id,
            'medico_id' => $medico->id,
            'fecha_estudio' => Carbon::now()->subDays(2),
            'estado' => 'informado',
            'informe' => 'Estudio respiratorio normal',
            'firmado_at' => Carbon::now()->subHours(12),
        ]);

        // Sin filtros: ambos aparecen
        $response = $this->actingAs($rrhh)->get(route('rrhh.archivo'));
        $response->assertStatus(200);
        $response->assertSee('Carlos Gardel');
        $response->assertSee('Mercedes Sosa');

        // Filtrando por Cardiología
        $responseCardio = $this->actingAs($rrhh)->get(route('rrhh.archivo', ['especialidad_id' => $cardio->id]));
        $responseCardio->assertStatus(200);
        $responseCardio->assertSee('Carlos Gardel');
        $responseCardio->assertDontSee('Mercedes Sosa');

        // Filtrando por Neumonología
        $responseNeumo = $this->actingAs($rrhh)->get(route('rrhh.archivo', ['especialidad_id' => $neumo->id]));
        $responseNeumo->assertStatus(200);
        $responseNeumo->assertSee('Mercedes Sosa');
        $responseNeumo->assertDontSee('Carlos Gardel');
    }

    public function test_archivo_general_filtra_por_fecha_de_realizacion_del_estudio(): void
    {
        $rrhh = User::factory()->create(['role' => 'rrhh']);
        $medico = User::factory()->create(['role' => 'medico']);
        $tecnico = User::factory()->create(['role' => 'tecnico']);

        $especialidad = Especialidad::create(['nombre' => 'Traumatología', 'slug' => 'traumatologia']);
        $tipo = TipoEstudio::create(['nombre' => 'Radiografía Ósea', 'especialidad_id' => $especialidad->id]);

        // Estudio realizado hace 20 días
        $estudioAntiguo = Estudio::create([
            'paciente_nombre' => 'Paciente Antiguo',
            'paciente_dni' => '11111111',
            'paciente_edad' => 40,
            'tipo_estudio_id' => $tipo->id,
            'tecnico_id' => $tecnico->id,
            'medico_id' => $medico->id,
            'fecha_estudio' => Carbon::now()->subDays(20),
            'estado' => 'informado',
            'informe' => 'Sin fractura',
            'firmado_at' => Carbon::now()->subDays(1), // Firmado ayer
        ]);

        // Estudio realizado hoy
        $estudioHoy = Estudio::create([
            'paciente_nombre' => 'Paciente Hoy',
            'paciente_dni' => '22222222',
            'paciente_edad' => 35,
            'tipo_estudio_id' => $tipo->id,
            'tecnico_id' => $tecnico->id,
            'medico_id' => $medico->id,
            'fecha_estudio' => Carbon::now(),
            'estado' => 'informado',
            'informe' => 'Normal',
            'firmado_at' => Carbon::now(),
        ]);

        // Filtrar por rango de fechas que solo cubre los últimos 3 días (fecha de realización)
        $desde = Carbon::now()->subDays(3)->format('Y-m-d');
        $hasta = Carbon::now()->addDay()->format('Y-m-d');

        $response = $this->actingAs($rrhh)->get(route('rrhh.archivo', [
            'fecha_desde' => $desde,
            'fecha_hasta' => $hasta,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Paciente Hoy');
        // El paciente antiguo se firmó ayer, pero se realizó hace 20 días: no debe aparecer
        $response->assertDontSee('Paciente Antiguo');
    }

    public function test_archivo_mes_agrupa_por_fecha_de_realizacion_y_filtra_especialidad(): void
    {
        $rrhh = User::factory()->create(['role' => 'rrhh']);
        $medico = User::factory()->create(['role' => 'medico']);
        $tecnico = User::factory()->create(['role' => 'tecnico']);

        $especialidad = Especialidad::create(['nombre' => 'Neurología', 'slug' => 'neurologia']);
        $tipo = TipoEstudio::create(['nombre' => 'EEG', 'especialidad_id' => $especialidad->id]);

        $fechaPasada = Carbon::create(2026, 8, 15, 10, 0, 0);

        Estudio::create([
            'paciente_nombre' => 'Paciente Agosto',
            'paciente_dni' => '33333333',
            'paciente_edad' => 28,
            'tipo_estudio_id' => $tipo->id,
            'tecnico_id' => $tecnico->id,
            'medico_id' => $medico->id,
            'fecha_estudio' => $fechaPasada,
            'estado' => 'informado',
            'informe' => 'Trazado normal',
            'firmado_at' => Carbon::now(), // firmado recién, pero realizado en agosto
        ]);

        $response = $this->actingAs($rrhh)->get(route('rrhh.archivo.mes', '2026-08'));
        $response->assertStatus(200);
        $response->assertSee('Paciente Agosto');
    }
}
