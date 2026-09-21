<?php

namespace Tests\Feature\Tecnico;

use App\Models\User;
use App\Models\Especialidad;
use App\Models\TipoEstudio;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrearEstudioTest extends TestCase
{
    use RefreshDatabase;


    public function test_un_tecnico_puede_registrar_un_estudio(): void
    {

        // Arrange
        $tecnico = User::factory()->create();


        $especialidad = Especialidad::create([
            'nombre'=>'Radiología',
            'slug'=>'radiologia'
        ]);


        $tipoEstudio = TipoEstudio::create([
            'especialidad_id'=>$especialidad->id,
            'nombre'=>'RX Tórax'
        ]);


        $archivo = UploadedFile::fake()->create(
            'estudio.pdf',
            500,
            'application/pdf'
        );


        // Act
        $response = $this
            ->actingAs($tecnico)
            ->post('/tecnico/estudios', [

                'paciente_nombre'=>'Juan Pérez',
                'paciente_dni'=>'12345678',
                'paciente_edad'=>45,

                'especialidad_id'=>$especialidad->id,

                'tipo_estudio_id'=>$tipoEstudio->id,

                'fecha_estudio'=>now()->format('Y-m-d H:i'),

                'archivos'=>[
                    $archivo
                ],

            ]);


        // Assert
        $response->assertRedirect();


        $this->assertDatabaseHas('estudios',[
            'paciente_nombre'=>'Juan Pérez',
            'paciente_dni'=>'12345678',
            'tecnico_id'=>$tecnico->id,
            'estado'=>'nuevo'
        ]);
    }
}