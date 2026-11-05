<?php

namespace Tests\Feature\Callcenter;

use App\Enums\EstadoEstudio;
use App\Mail\AvisoInformeListo;
use App\Models\Especialidad;
use App\Models\Estudio;
use App\Models\TipoEstudio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EnviarAvisoTest extends TestCase
{
    use RefreshDatabase;

    public function test_envia_el_aviso_al_email_del_paciente(): void
    {
        Mail::fake();

        $estudio = $this->crearEstudio();

        $response = $this->actingAs($this->crearUsuario('callcenter'))
            ->post(route('callcenter.informes.enviar', $estudio->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Mail::assertSent(AvisoInformeListo::class, function (AvisoInformeListo $mail) use ($estudio) {
            return $mail->hasTo('paciente@example.com') && $mail->estudio->is($estudio);
        });

        $estudio->refresh();

        $this->assertNotNull($estudio->aviso_enviado_at);
        $this->assertNotNull($estudio->aviso_enviado_por);
        $this->assertStringContainsString('Ana Pérez', (new AvisoInformeListo($estudio))->render());
    }

    public function test_no_envia_si_el_paciente_no_tiene_email(): void
    {
        Mail::fake();

        $estudio = $this->crearEstudio(['paciente_email' => null]);

        $response = $this->actingAs($this->crearUsuario('callcenter'))
            ->post(route('callcenter.informes.enviar', $estudio->id));

        $response->assertSessionHas('error');

        Mail::assertNothingSent();
        $this->assertNull($estudio->fresh()->aviso_enviado_at);
    }

    public function test_no_envia_si_el_informe_no_esta_firmado(): void
    {
        Mail::fake();

        $estudio = $this->crearEstudio(['estado' => EstadoEstudio::Nuevo]);

        $response = $this->actingAs($this->crearUsuario('callcenter'))
            ->post(route('callcenter.informes.enviar', $estudio->id));

        $response->assertSessionHas('error');

        Mail::assertNothingSent();
    }

    public function test_un_usuario_de_otro_rol_no_puede_enviar_el_aviso(): void
    {
        Mail::fake();

        $estudio = $this->crearEstudio();

        $response = $this->actingAs($this->crearUsuario('tecnico'))
            ->post(route('callcenter.informes.enviar', $estudio->id));

        $response->assertRedirect('/tecnico/estudios');

        Mail::assertNothingSent();
    }

    private function crearEstudio(array $atributos = []): Estudio
    {
        $especialidad = Especialidad::create([
            'nombre' => 'Cardiología',
            'slug' => 'cardiologia',
        ]);

        $tipoEstudio = TipoEstudio::create([
            'especialidad_id' => $especialidad->id,
            'nombre' => 'Electrocardiograma',
        ]);

        $estudio = Estudio::create(array_merge([
            'paciente_nombre' => 'Ana Pérez',
            'paciente_dni' => '30111222',
            'paciente_edad' => 42,
            'paciente_email' => 'paciente@example.com',
            'tipo_estudio_id' => $tipoEstudio->id,
            'tecnico_id' => $this->crearUsuario('tecnico')->id,
            'estado' => EstadoEstudio::Informado,
            'fecha_estudio' => now(),
        ], $atributos));

        $estudio->forceFill([
            'medico_id' => $this->crearUsuario('medico')->id,
            'informe' => 'Informe de prueba.',
            'firmado_at' => now(),
        ])->save();

        return $estudio;
    }

    private function crearUsuario(string $rol): User
    {
        return User::factory()->create(['role' => $rol]);
    }
}
