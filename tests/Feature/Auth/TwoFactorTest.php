<?php

namespace Tests\Feature\Auth;

use App\Mail\CodigoDobleFactor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TwoFactorTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_con_credenciales_correctas_redirige_a_desafio_2fa_y_envia_correo(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'medico@reportflow.local',
            'password' => Hash::make('password'),
            'role' => 'medico',
            'two_factor_enabled' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'medico@reportflow.local',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('2fa.challenge'));
        $this->assertGuest();

        $user->refresh();
        $this->assertNotNull($user->two_factor_code);
        $this->assertNotNull($user->two_factor_expires_at);

        Mail::assertSent(CodigoDobleFactor::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) && $mail->codigo === $user->two_factor_code;
        });
    }

    public function test_pantalla_2fa_muestra_interfaz_y_codigo_demo(): void
    {
        $user = User::factory()->create([
            'email' => 'tecnico.prueba@reportflow.local',
            'role' => 'tecnico',
        ]);
        $codigo = $user->generarCodigo2FA();

        $response = $this->withSession(['auth.2fa.user_id' => $user->id])
            ->get(route('2fa.challenge'));

        $response->assertStatus(200);
        $response->assertSee('Verificación de Seguridad');
        $response->assertSee($codigo);
        $response->assertSee('Modo Demostración / Evaluación');
    }

    public function test_codigo_valido_inicia_sesion_y_redirige_segun_rol(): void
    {
        $user = User::factory()->create([
            'email' => 'medico@reportflow.local',
            'role' => 'medico',
        ]);
        $codigo = $user->generarCodigo2FA();

        $response = $this->withSession(['auth.2fa.user_id' => $user->id])
            ->post(route('2fa.verify'), [
                'codigo' => $codigo,
            ]);

        $response->assertRedirect('/medico/estudios');
        $this->assertAuthenticatedAs($user);

        $user->refresh();
        $this->assertNull($user->two_factor_code);
        $this->assertNull($user->two_factor_expires_at);
    }

    public function test_codigo_incorrecto_no_inicia_sesion_y_muestra_error(): void
    {
        $user = User::factory()->create([
            'email' => 'medico@reportflow.local',
            'role' => 'medico',
        ]);
        $user->generarCodigo2FA();

        $response = $this->withSession(['auth.2fa.user_id' => $user->id])
            ->post(route('2fa.verify'), [
                'codigo' => '999999',
            ]);

        $response->assertSessionHasErrors('codigo');
        $this->assertGuest();
    }

    public function test_codigo_expirado_no_inicia_sesion(): void
    {
        $user = User::factory()->create([
            'email' => 'medico@reportflow.local',
            'role' => 'medico',
        ]);
        $codigo = $user->generarCodigo2FA();
        $user->forceFill(['two_factor_expires_at' => now()->subMinute()])->save();

        $response = $this->withSession(['auth.2fa.user_id' => $user->id])
            ->post(route('2fa.verify'), [
                'codigo' => $codigo,
            ]);

        $response->assertSessionHasErrors('codigo');
        $this->assertGuest();
    }

    public function test_reenviar_codigo_genera_uno_nuevo_y_envia_correo(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'medico@reportflow.local',
            'role' => 'medico',
        ]);
        $codigoViejo = $user->generarCodigo2FA();

        $response = $this->withSession(['auth.2fa.user_id' => $user->id])
            ->post(route('2fa.resend'));

        $response->assertSessionHas('status');

        $user->refresh();
        $this->assertNotEquals($codigoViejo, $user->two_factor_code);

        Mail::assertSent(CodigoDobleFactor::class);
    }
}
