<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use App\Models\Estudio;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Genera un volumen realista de estudios: pacientes con nombre, DNI y
 * datos de contacto, repartidos entre las 8 especialidades y sus
 * médicos correspondientes, con una mezcla de estados (la mayoría
 * informados, algunos nuevos, algunos rechazados) y fechas distribuidas
 * en los últimos meses -para que Archivo General, el Dashboard de RRHH
 * y el listado de Call Center tengan algo real para mostrar.
 */
class EstudioDemoSeeder extends Seeder
{
    /**
     * Pacientes de prueba: nombre, DNI, teléfono (Mendoza, 261) y email.
     * Son datos inventados, pero con formato real de DNI/teléfono argentino.
     */
    private const PACIENTES = [
        ['Roberto Álvarez', '28451236', '261 4521-3367', 'roberto.alvarez@gmail.com'],
        ['Marcela Fernández', '31220456', '261 5566-7890', 'marcela.fernandez@gmail.com'],
        ['Diego Molina', '25874123', '261 4123-5678', 'diego.molina@hotmail.com'],
        ['Florencia Acosta', '33654789', '261 6234-1290', 'florencia.acosta@gmail.com'],
        ['Sergio Ledesma', '22147896', '261 5123-4890', 'sergio.ledesma@yahoo.com'],
        ['Paula Sánchez', '35896214', '261 4890-2231', 'paula.sanchez@gmail.com'],
        ['Fernando Ríos', '27563214', '261 5678-9021', 'fernando.rios@hotmail.com'],
        ['Yamila Torres', '34125896', '261 6123-8890', 'yamila.torres@gmail.com'],
        ['Nicolás Quiroga', '29874563', '261 4567-1230', 'nicolas.quiroga@gmail.com'],
        ['Agustina Peralta', '36214789', '261 5890-3321', 'agustina.peralta@outlook.com'],
        ['Hugo Benítez', '24789563', '261 4321-7789', 'hugo.benitez@gmail.com'],
        ['Carla Domínguez', '32456178', '261 6789-0123', 'carla.dominguez@gmail.com'],
        ['Emanuel Castro', '30125478', '261 5234-6690', 'emanuel.castro@hotmail.com'],
        ['Rocío Villalba', '37896214', '261 4678-9012', 'rocio.villalba@gmail.com'],
        ['Alberto Núñez', '26547893', '261 5901-2345', 'alberto.nunez@yahoo.com'],
        ['Daniela Ojeda', '33012456', '261 6345-6789', 'daniela.ojeda@gmail.com'],
        ['Ezequiel Farías', '28963214', '261 4789-0123', 'ezequiel.farias@gmail.com'],
        ['Mariana Coria', '31789456', '261 5456-7812', 'mariana.coria@hotmail.com'],
        ['Pablo Escudero', '23654178', '261 6012-3456', 'pablo.escudero@gmail.com'],
        ['Lorena Aguirre', '34567891', '261 4234-5671', 'lorena.aguirre@gmail.com'],
        ['Cristian Vera', '27891234', '261 5678-1234', 'cristian.vera@outlook.com'],
        ['Gisela Moyano', '32145698', '261 6890-4567', 'gisela.moyano@gmail.com'],
        ['Rodrigo Funes', '29456781', '261 4567-8901', 'rodrigo.funes@gmail.com'],
        ['Antonella Ávila', '35678912', '261 5789-0234', 'antonella.avila@hotmail.com'],
        ['Julián Correa', '24123789', '261 6234-5678', 'julian.correa@gmail.com'],
        ['Vanina Godoy', '31456278', '261 4890-1235', 'vanina.godoy@gmail.com'],
        ['Maximiliano Luna', '26789123', '261 5345-6780', 'maximiliano.luna@yahoo.com'],
        ['Ayelén Bazán', '33891456', '261 6456-7891', 'ayelen.bazan@gmail.com'],
        ['Facundo Miranda', '28123456', '261 4678-2345', 'facundo.miranda@hotmail.com'],
        ['Micaela Suárez', '30567891', '261 5891-3456', 'micaela.suarez@gmail.com'],
    ];

    private const MOTIVOS_RECHAZO = [
        'La imagen quedó fuera de foco, hay que repetir la toma.',
        'Faltan datos del paciente en el formulario, no coinciden con el DNI.',
        'El archivo adjunto está incompleto, se cortó antes de terminar el estudio.',
        'Se cargó en la especialidad equivocada, corresponde derivarlo de nuevo.',
    ];

    public function run(): void
    {
        // Si ya hay estudios cargados, no volvemos a generar el lote demo
        // -Estudio::create() no es idempotente como firstOrCreate(), así
        // que correr este seeder dos veces duplicaría todo sin este freno-.
        if (Estudio::count() > 0) {
            return;
        }

        $this->call(UsuariosDemoSeeder::class);
        // Seeder::call() no devuelve el valor de run() del seeder invocado,
        // así que resolvemos los usuarios ya creados directamente por email.
        $medicosPorEspecialidad = [
            'cardiologia' => ['medico@reportflow.local', 'maria.garcia@reportflow.local'],
            'neumonologia' => ['lucia.fernandez@reportflow.local'],
            'neurologia' => ['martin.gomez@reportflow.local'],
            'traumatologia' => ['valeria.torres@reportflow.local'],
            'oftalmologia' => ['jorge.medina@reportflow.local'],
            'gastroenterologia' => ['silvia.paz@reportflow.local'],
            'dermatologia' => ['ricardo.silva@reportflow.local'],
            'psiquiatria' => ['carolina.ruiz@reportflow.local'],
        ];

        // Incluye también al técnico de /dev-login (tecnico.prueba@reportflow.local)
        // para que ese usuario, el que más se usa para probar, también tenga
        // estudios propios al entrar a "Mis Estudios Cargados".
        $tecnicos = User::whereIn('email', [
            'luis.perez@reportflow.local',
            'camila.sosa@reportflow.local',
            'tecnico.prueba@reportflow.local',
        ])->get();

        if ($tecnicos->isEmpty()) {
            return; // el seeder de usuarios no corrió, no hay nada que hacer
        }

        $pacientes = self::PACIENTES;

        foreach ($medicosPorEspecialidad as $slug => $emailsMedicos) {
            $especialidad = Especialidad::where('slug', $slug)->first();

            if (! $especialidad) {
                continue;
            }

            $tiposEstudio = $especialidad->tiposEstudio;
            $medicos = User::whereIn('email', $emailsMedicos)->get();

            if ($tiposEstudio->isEmpty() || $medicos->isEmpty()) {
                continue;
            }

            // ~10 estudios por especialidad, para tener un volumen creíble
            for ($i = 0; $i < 10; $i++) {
                $paciente = $pacientes[array_rand($pacientes)];
                [$nombre, $dni, $telefono, $email] = $paciente;

                $tipoEstudio = $tiposEstudio->random();
                $tecnico = $tecnicos->random();
                $medico = $medicos->random();

                $fechaEstudio = Carbon::now()
                    ->subDays(random_int(1, 120))
                    ->subHours(random_int(0, 23));

                $azar = random_int(1, 100);
                $estado = match (true) {
                    $azar <= 70 => 'informado',
                    $azar <= 88 => 'nuevo',
                    default => 'rechazado',
                };

                $datosComunes = [
                    'paciente_nombre' => $nombre,
                    'paciente_dni' => $dni,
                    'paciente_edad' => random_int(18, 85),
                    'paciente_telefono' => $telefono,
                    'paciente_email' => $email,
                    'tipo_estudio_id' => $tipoEstudio->id,
                    'tecnico_id' => $tecnico->id,
                    'fecha_estudio' => $fechaEstudio,
                    'estado' => $estado,
                ];

                match ($estado) {
                    'informado' => Estudio::create([
                        ...$datosComunes,
                        'medico_id' => $medico->id,
                        'informe' => "Estudio de {$tipoEstudio->nombre} sin hallazgos patológicos relevantes. Se sugiere control de rutina según indicación clínica.",
                        'firmado_at' => (clone $fechaEstudio)->addHours(random_int(2, 72)),
                    ]),
                    'rechazado' => Estudio::create([
                        ...$datosComunes,
                        'medico_id' => $medico->id,
                        'motivo_rechazo' => self::MOTIVOS_RECHAZO[array_rand(self::MOTIVOS_RECHAZO)],
                    ]),
                    default => Estudio::create($datosComunes), // 'nuevo': sin médico todavía
                };
            }
        }
    }
}
