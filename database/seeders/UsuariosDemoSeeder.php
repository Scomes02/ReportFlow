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
            ['name' => 'Dr. Juan Pérez', 'email' => 'medico@reportflow.local'], // médico de dev-login
            ['name' => 'Dra. María García', 'email' => 'maria.garcia@reportflow.local'],
            ['name' => 'Dra. Lucía Fernández', 'email' => 'lucia.fernandez@reportflow.local'],
            ['name' => 'Dr. Martín Gómez', 'email' => 'martin.gomez@reportflow.local'],
            ['name' => 'Dra. Valeria Torres', 'email' => 'valeria.torres@reportflow.local'],
            ['name' => 'Dr. Jorge Medina', 'email' => 'jorge.medina@reportflow.local'],
            ['name' => 'Dra. Silvia Paz', 'email' => 'silvia.paz@reportflow.local'],
            ['name' => 'Dr. Ricardo Silva', 'email' => 'ricardo.silva@reportflow.local'],
            ['name' => 'Dra. Carolina Ruiz', 'email' => 'carolina.ruiz@reportflow.local'],
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

        foreach ($medicos as $datos) {
            $usuarios['medicos'][] = User::firstOrCreate(
                ['email' => $datos['email']],
                [
                    'name' => $datos['name'],
                    'password' => Hash::make('password'),
                    'role' => 'medico',
                ]
            );
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