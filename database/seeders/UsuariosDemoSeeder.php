<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Usuarios de demostración: técnicos, médicos, RRHH y Call Center, con
 * nombres reales (de mentira, pero consistentes). Antes, RRHH y Call
 * Center solo se creaban al pasar por /dev-login-rrhh o
 * /dev-login-callcenter -por eso el login "de verdad" fallaba si nunca
 * habías visitado esas rutas. Ahora quedan creados apenas se siembra
 * la base, así el formulario de /login funciona directo con esas
 * credenciales sin depender de ningún atajo.
 */
class UsuariosDemoSeeder extends Seeder
{
    /**
     * @return array<string, array<int, User>>
     */
    public function run(): array
    {
        $medicos = [
            ['name' => 'Dr. Juan Pérez', 'email' => 'medico@reportflow.local', 'especialidad_slug' => 'cardiologia'], // médico de dev-login
            ['name' => 'Dra. María García', 'email' => 'maria.garcia@reportflow.local', 'especialidad_slug' => 'cardiologia'],
            ['name' => 'Dra. Lucía Fernández', 'email' => 'lucia.fernandez@reportflow.local', 'especialidad_slug' => 'neumonologia'],
            ['name' => 'Dr. Martín Gómez', 'email' => 'martin.gomez@reportflow.local', 'especialidad_slug' => 'neurologia'],
            ['name' => 'Dra. Valeria Torres', 'email' => 'valeria.torres@reportflow.local', 'especialidad_slug' => 'traumatologia'],
            ['name' => 'Dr. Jorge Medina', 'email' => 'jorge.medina@reportflow.local', 'especialidad_slug' => 'oftalmologia'],
            ['name' => 'Dra. Silvia Paz', 'email' => 'silvia.paz@reportflow.local', 'especialidad_slug' => 'gastroenterologia'],
            ['name' => 'Dr. Ricardo Silva', 'email' => 'ricardo.silva@reportflow.local', 'especialidad_slug' => 'dermatologia'],
            ['name' => 'Dra. Carolina Ruiz', 'email' => 'carolina.ruiz@reportflow.local', 'especialidad_slug' => 'psiquiatria'],
        ];

        $tecnicos = [
            ['name' => 'Tec. Luis Pérez', 'email' => 'luis.perez@reportflow.local'],
            ['name' => 'Tec. Camila Sosa', 'email' => 'camila.sosa@reportflow.local'],
        ];

        // Estos dos antes solo existían si visitabas /dev-login-rrhh o
        // /dev-login-callcenter al menos una vez. Ahora quedan en el seeder.
        $administrativos = [
            ['name' => 'RRHH - Administración', 'email' => 'rrhh@reportflow.local', 'role' => 'rrhh'],
            ['name' => 'Call Center - Recepción', 'email' => 'callcenter@reportflow.local', 'role' => 'callcenter'],
        ];

        $usuarios = [];
        $especialidadesPorSlug = \App\Models\Especialidad::pluck('id', 'slug');

        foreach ($medicos as $datos) {
            $especialidadId = $especialidadesPorSlug[$datos['especialidad_slug']] ?? null;

            $usuario = User::firstOrCreate(
                ['email' => $datos['email']],
                [
                    'name' => $datos['name'],
                    'password' => Hash::make('password'),
                    'role' => 'medico',
                    'especialidad_id' => $especialidadId,
                ]
            );

            // Si el usuario ya existía pero no tenía especialidad asignada, la actualizamos
            if ($especialidadId && ! $usuario->especialidad_id) {
                $usuario->especialidad_id = $especialidadId;
                $usuario->save();
            }

            $usuarios['medicos'][] = $usuario;
        }

        foreach ($tecnicos as $datos) {
            $usuarios['tecnicos'][] = User::firstOrCreate(
                ['email' => $datos['email']],
                [
                    'name' => $datos['name'],
                    'password' => Hash::make('password'),
                    'role' => 'tecnico',
                ]
            );
        }

        foreach ($administrativos as $datos) {
            $usuarios['administrativos'][] = User::firstOrCreate(
                ['email' => $datos['email']],
                [
                    'name' => $datos['name'],
                    'password' => Hash::make('password'),
                    'role' => $datos['role'],
                ]
            );
        }

        return $usuarios;
    }
}