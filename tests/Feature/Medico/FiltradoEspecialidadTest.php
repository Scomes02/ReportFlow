<?php

namespace Tests\Feature\Medico;

use App\Models\Especialidad;
use App\Models\Estudio;
use App\Models\TipoEstudio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FiltradoEspecialidadTest extends TestCase
{
    use RefreshDatabase;

    public function test_medico_solo_ve_estudios_de_su_especialidad(): void
    {
        // 1. Crear especialidades
        $cardiologia = Especialidad::create(['nombre' => 'Cardiología', 'slug' => 'cardiologia']);
        $neumonologia = Especialidad::create(['nombre' => 'Neumonología', 'slug' => 'neumonologia']);

        // 2. Crear tipos de estudio
        $ecg = TipoEstudio::create(['especialidad_id' => $cardiologia->id, 'nombre' => 'Electrocardiograma']);
        $holter = TipoEstudio::create(['especialidad_id' => $cardiologia->id, 'nombre' => 'Holter 24hs']);
        $espiro = TipoEstudio::create(['especialidad_id' => $neumonologia->id, 'nombre' => 'Espirometría']);

        // 3. Crear técnico y médico cardiólogo
        $tecnico = User::factory()->tecnico()->create();
        $cardiologo = User::factory()->medico($cardiologia->id)->create(['name' => 'Dr. Cardiólogo']);

        // 4. Crear estudios de ambas especialidades
        $estudioCardio1 = Estudio::create([
            'paciente_nombre' => 'Paciente Cardio 1',
            'paciente_dni' => '11111111',
            'paciente_edad' => 50,
            'tipo_estudio_id' => $ecg->id,
            'tecnico_id' => $tecnico->id,
            'estado' => 'nuevo',
            'fecha_estudio' => now(),
        ]);

        $estudioCardio2 = Estudio::create([
            'paciente_nombre' => 'Paciente Cardio 2',
            'paciente_dni' => '22222222',
            'paciente_edad' => 55,
            'tipo_estudio_id' => $holter->id,
            'tecnico_id' => $tecnico->id,
            'estado' => 'nuevo',
            'fecha_estudio' => now(),
        ]);

        $estudioNeumo = Estudio::create([
            'paciente_nombre' => 'Paciente Neumo',
            'paciente_dni' => '33333333',
            'paciente_edad' => 60,
            'tipo_estudio_id' => $espiro->id,
            'tecnico_id' => $tecnico->id,
            'estado' => 'nuevo',
            'fecha_estudio' => now(),
        ]);

        // 5. El cardiólogo accede a la Worklist
        $response = $this->actingAs($cardiologo)->get('/medico/estudios');

        // 6. Debe ver los estudios de cardiología y NO el de neumonología
        $response->assertStatus(200);
        $response->assertSee('Paciente Cardio 1');
        $response->assertSee('Paciente Cardio 2');
        $response->assertSee('Cardiología');
        $response->assertDontSee('Paciente Neumo');
    }

    public function test_medico_no_puede_informar_estudio_de_otra_especialidad(): void
    {
        $cardiologia = Especialidad::create(['nombre' => 'Cardiología', 'slug' => 'cardiologia']);
        $neumonologia = Especialidad::create(['nombre' => 'Neumonología', 'slug' => 'neumonologia']);

        $espiro = TipoEstudio::create(['especialidad_id' => $neumonologia->id, 'nombre' => 'Espirometría']);

        $tecnico = User::factory()->tecnico()->create();
        $cardiologo = User::factory()->medico($cardiologia->id)->create();

        $estudioNeumo = Estudio::create([
            'paciente_nombre' => 'Paciente Neumo',
            'paciente_dni' => '33333333',
            'paciente_edad' => 60,
            'tipo_estudio_id' => $espiro->id,
            'tecnico_id' => $tecnico->id,
            'estado' => 'nuevo',
            'fecha_estudio' => now(),
        ]);

        // El cardiólogo intenta forzar la firma de un estudio de neumonología
        $response = $this->actingAs($cardiologo)->post("/medico/estudios/{$estudioNeumo->id}/informar", [
            'informe' => 'Informe no autorizado',
        ]);

        $response->assertRedirect('/medico/estudios');
        $response->assertSessionHas('error');

        // El estudio debe permanecer en estado 'nuevo'
        $this->assertDatabaseHas('estudios', [
            'id' => $estudioNeumo->id,
            'estado' => 'nuevo',
            'informe' => null,
        ]);
    }
}
