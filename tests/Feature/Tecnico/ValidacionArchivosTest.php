<?php

namespace Tests\Feature\Tecnico;

use App\Models\User;
use App\Models\Especialidad;
use App\Models\TipoEstudio;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidacionArchivosTest extends TestCase
{
    use RefreshDatabase;


    public function test_no_permite_subir_un_archivo_con_extension_invalida(): void
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


        // Archivo inválido
        $archivoInvalido = UploadedFile::fake()->create(
            'virus.exe',
            500,
            'application/octet-stream'
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
                    $archivoInvalido
                ]

            ]);


        // Assert

        $response->assertSessionHasErrors('archivos.0');


        $this->assertDatabaseCount(
            'estudios',
            0
        );
    }
}