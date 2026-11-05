<?php

namespace Database\Seeders;

use App\Enums\EstadoEstudio;
use App\Models\ArchivoEstudio;
use App\Models\Especialidad;
use App\Models\Estudio;
use App\Models\TipoEstudio;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class EstudiosEvolutivosDemoSeeder extends Seeder
{
    public function run(): void
    {
        $tecnico = User::where('role', 'tecnico')->first() ?? User::factory()->create(['role' => 'tecnico']);
        $medicoCardio = User::where('email', 'medico@reportflow.local')->first() ?? User::where('role', 'medico')->first();
        $medicoNeumo = User::where('email', 'lucia.fernandez@reportflow.local')->first() ?? $medicoCardio;
        $medicoNeuro = User::where('email', 'martin.gomez@reportflow.local')->first() ?? $medicoCardio;
        $medicoTrauma = User::where('email', 'valeria.torres@reportflow.local')->first() ?? $medicoCardio;

        // Especialidades
        $espRadio = Especialidad::firstOrCreate(['slug' => 'radiologia'], ['nombre' => 'Radiología']);
        $espCardio = Especialidad::firstOrCreate(['slug' => 'cardiologia'], ['nombre' => 'Cardiología']);
        $espNeumo = Especialidad::firstOrCreate(['slug' => 'neumonologia'], ['nombre' => 'Neumonología']);
        $espNeuro = Especialidad::firstOrCreate(['slug' => 'neurologia'], ['nombre' => 'Neurología']);
        $espTrauma = Especialidad::firstOrCreate(['slug' => 'traumatologia'], ['nombre' => 'Traumatología']);

        // Tipos de estudio con regiones anatómicas
        $tipoRxTorax = TipoEstudio::firstOrCreate(
            ['nombre' => 'Radiografía de Tórax Frente y Perfil'],
            ['especialidad_id' => $espRadio->id, 'region_anatomica_defecto' => 'torax']
        );
        $tipoEcoCardio = TipoEstudio::firstOrCreate(
            ['nombre' => 'Ecocardiograma'],
            ['especialidad_id' => $espCardio->id, 'region_anatomica_defecto' => 'cardiovascular']
        );
        $tipoEspiro = TipoEstudio::firstOrCreate(
            ['nombre' => 'Espirometría'],
            ['especialidad_id' => $espNeumo->id, 'region_anatomica_defecto' => 'torax']
        );
        $tipoTacCerebro = TipoEstudio::firstOrCreate(
            ['nombre' => 'Tomografía Axial Computada de Cerebro'],
            ['especialidad_id' => $espNeuro->id, 'region_anatomica_defecto' => 'cerebro']
        );
        $tipoRmRodilla = TipoEstudio::firstOrCreate(
            ['nombre' => 'Resonancia Magnética'],
            ['especialidad_id' => $espTrauma->id, 'region_anatomica_defecto' => 'musculoesqueletico']
        );

        // Placas SVG de demostración
        $svgRxToraxPrevia = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 700" width="100%" height="100%">
    <rect width="600" height="700" fill="#040608"/>
    <text x="30" y="40" fill="#38bdf8" font-family="monospace" font-size="13" font-weight="bold">ESTUDIO PREVIO — CONTROL 2024</text>
    <text x="30" y="60" fill="#64748b" font-family="monospace" font-size="11">MOD: CR | KVP: 120 | mA: 200 | PREVIO</text>
    <text x="520" y="40" fill="#ffffff" font-family="monospace" font-size="20" font-weight="bold">R</text>
    <!-- Silueta Pulmonar -->
    <path d="M 160 190 C 130 260, 120 410, 180 500 C 230 480, 260 450, 270 390 C 260 270, 220 210, 160 190 Z" fill="#141c24" stroke="#334155" stroke-width="2"/>
    <path d="M 440 190 C 470 260, 480 410, 420 500 C 370 480, 340 450, 330 390 C 340 270, 380 210, 440 190 Z" fill="#141c24" stroke="#334155" stroke-width="2"/>
    <!-- Silueta Cardíaca previa más chica -->
    <path d="M 270 370 C 275 460, 325 480, 350 450 C 360 420, 345 370, 325 365 Z" fill="#334155" stroke="#475569" stroke-width="2"/>
    <!-- Costillas -->
    <path d="M 180 240 Q 300 280 420 240" fill="none" stroke="#94a3b8" stroke-width="7" opacity="0.4" stroke-linecap="round"/>
    <path d="M 160 290 Q 300 335 440 290" fill="none" stroke="#94a3b8" stroke-width="8" opacity="0.4" stroke-linecap="round"/>
    <path d="M 140 345 Q 300 390 460 345" fill="none" stroke="#94a3b8" stroke-width="8" opacity="0.4" stroke-linecap="round"/>
    <!-- Columna y clavículas -->
    <rect x="292" y="170" width="16" height="340" fill="#64748b" opacity="0.45" rx="3"/>
    <path d="M 150 180 Q 240 210 300 210" fill="none" stroke="#cbd5e1" stroke-width="9" stroke-linecap="round"/>
    <path d="M 450 180 Q 360 210 300 210" fill="none" stroke="#cbd5e1" stroke-width="9" stroke-linecap="round"/>
</svg>
SVG;

        $svgEcoCardio = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 700" width="100%" height="100%">
    <rect width="600" height="700" fill="#030712"/>
    <text x="30" y="40" fill="#f43f5e" font-family="monospace" font-size="13" font-weight="bold">ECOCARDIOGRAMA DOPPLER COLOR</text>
    <text x="30" y="60" fill="#64748b" font-family="monospace" font-size="11">MOD: US | FREQ: 3.5MHz | 4 CH-APICAL</text>
    <!-- Cono sectorial de ultrasonido -->
    <path d="M 300 120 L 100 560 A 300 300 0 0 0 500 560 Z" fill="#0f172a" stroke="#1e293b" stroke-width="2"/>
    <!-- Cavidades Cardíacas -->
    <ellipse cx="260" cy="380" rx="45" ry="70" fill="#1e1b4b" stroke="#6366f1" stroke-width="2" opacity="0.75"/>
    <ellipse cx="340" cy="380" rx="40" ry="65" fill="#1e1b4b" stroke="#6366f1" stroke-width="2" opacity="0.75"/>
    <ellipse cx="270" cy="480" rx="35" ry="40" fill="#172554" stroke="#3b82f6" stroke-width="2" opacity="0.75"/>
    <ellipse cx="335" cy="480" rx="32" ry="38" fill="#172554" stroke="#3b82f6" stroke-width="2" opacity="0.75"/>
    <!-- Flujo Doppler -->
    <path d="M 280 430 Q 300 450 320 430" fill="none" stroke="#f43f5e" stroke-width="5" opacity="0.8"/>
    <text x="30" y="660" fill="#64748b" font-family="monospace" font-size="11">VENTRÍCULO IZQUIERDO FEy: 52% | DOPPLER COLOR</text>
</svg>
SVG;

        $svgRxToraxActual = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 700" width="100%" height="100%">
    <rect width="600" height="700" fill="#05070a"/>
    <text x="30" y="40" fill="#00ffcc" font-family="monospace" font-size="14" font-weight="bold">DICOM PACS — ESTUDIO ACTUAL (2026)</text>
    <text x="30" y="60" fill="#7a8b9e" font-family="monospace" font-size="11">MOD: CR | KVP: 125 | mA: 250 | EXP: 12ms</text>
    <text x="520" y="40" fill="#ffffff" font-family="monospace" font-size="20" font-weight="bold">R</text>
    <path d="M 160 180 C 130 250, 120 400, 180 500 C 230 480, 260 450, 270 380 C 260 260, 220 200, 160 180 Z" fill="#1b242e" stroke="#485c72" stroke-width="2"/>
    <path d="M 440 180 C 470 250, 480 400, 420 500 C 370 480, 340 450, 330 380 C 340 260, 380 200, 440 180 Z" fill="#1b242e" stroke="#485c72" stroke-width="2"/>
    <!-- Silueta Cardíaca aumentada en el estudio actual -->
    <path d="M 270 340 C 285 475, 360 495, 385 460 C 395 415, 365 355, 330 345 Z" fill="#4d5d6e" stroke="#6f849b" stroke-width="2.5"/>
    <path d="M 180 230 Q 300 270 420 230" fill="none" stroke="#cfd8dc" stroke-width="8" opacity="0.45" stroke-linecap="round"/>
    <path d="M 160 280 Q 300 325 440 280" fill="none" stroke="#cfd8dc" stroke-width="9" opacity="0.45" stroke-linecap="round"/>
    <path d="M 140 335 Q 300 380 460 335" fill="none" stroke="#cfd8dc" stroke-width="9" opacity="0.45" stroke-linecap="round"/>
    <rect x="290" y="160" width="20" height="360" fill="#90a4ae" opacity="0.5" rx="4"/>
    <path d="M 150 170 Q 240 200 300 200" fill="none" stroke="#e0e0e0" stroke-width="10" stroke-linecap="round"/>
    <path d="M 450 170 Q 360 200 300 200" fill="none" stroke="#e0e0e0" stroke-width="10" stroke-linecap="round"/>
    <text x="30" y="660" fill="#7a8b9e" font-family="monospace" font-size="11">W/L: PRESET TORAX | ZOOM: 1.0x | HU UNCUYO</text>
</svg>
SVG;

        // Paciente 1: Roberto Carlos Álvarez (3 estudios encadenados)
        $pacienteRoberto = [
            'nombre' => 'Roberto Carlos Álvarez',
            'dni' => '28451236',
            'edad' => 46,
            'telefono' => '261 4521-3367',
            'email' => 'roberto.alvarez@gmail.com',
        ];

        // 1.1 Estudio previo (Hace 14 meses) - Neumonología
        $estudioPrevio1 = Estudio::updateOrCreate(
            [
                'paciente_dni' => $pacienteRoberto['dni'],
                'fecha_estudio' => Carbon::now()->subMonths(14)->setTime(10, 30),
            ],
            [
                'paciente_nombre' => $pacienteRoberto['nombre'],
                'paciente_edad' => $pacienteRoberto['edad'] - 1,
                'paciente_telefono' => $pacienteRoberto['telefono'],
                'paciente_email' => $pacienteRoberto['email'],
                'tipo_estudio_id' => $tipoRxTorax->id,
                'region_anatomica' => 'torax',
                'tecnico_id' => $tecnico->id,
                'medico_id' => $medicoNeumo->id,
                'estado' => EstadoEstudio::Informado,
                'modalidad' => 'CR',
                'informe' => 'Radiografía de Tórax de control. Se observa índice cardiotorácico aumentado y silueta cardíaca prominente sugerente de cardiomegalia moderada. Sin condensaciones alveolares activas ni signos de derrame pleural agudo. Se sugiere interconsulta con Cardiología.',
                'semaforo_salud' => 'control_requerido',
                'semaforo_evolucion' => 'amarillo',
                'resumen_evolucion' => 'Cardiomegalia moderada en seguimiento radiológico.',
                'firmado_at' => Carbon::now()->subMonths(14)->addHours(4),
            ]
        );
        $this->guardarArchivoDemo($estudioPrevio1, 'rx_torax_2024.svg', $svgRxToraxPrevia);

        // 1.2 Estudio intermedio (Hace 6 meses) - Cardiología
        $estudioPrevio2 = Estudio::updateOrCreate(
            [
                'paciente_dni' => $pacienteRoberto['dni'],
                'fecha_estudio' => Carbon::now()->subMonths(6)->setTime(16, 15),
            ],
            [
                'paciente_nombre' => $pacienteRoberto['nombre'],
                'paciente_edad' => $pacienteRoberto['edad'],
                'paciente_telefono' => $pacienteRoberto['telefono'],
                'paciente_email' => $pacienteRoberto['email'],
                'tipo_estudio_id' => $tipoEcoCardio->id,
                'region_anatomica' => 'cardiovascular',
                'tecnico_id' => $tecnico->id,
                'medico_id' => $medicoCardio->id,
                'estado' => EstadoEstudio::Informado,
                'modalidad' => 'US',
                'informe' => 'Ecocardiograma bidimensional: Fracción de eyección conservada (52%). Dilatación auricular izquierda moderada. Insuficiencia mitral y tricuspídea de grado leve. Se recomienda seguimiento cardiológico cada 6 meses y control radiológico anual.',
                'semaforo_salud' => 'control_requerido',
                'semaforo_evolucion' => 'amarillo',
                'resumen_evolucion' => 'Dilatación auricular izquierda con FEy conservada (52%).',
                'firmado_at' => Carbon::now()->subMonths(6)->addHours(6),
            ]
        );
        $this->guardarArchivoDemo($estudioPrevio2, 'ecocardio_2025.svg', $svgEcoCardio);

        // 1.3 Estudio actual (Hoy) - Radiología (Estudio en curso para informar)
        $estudioActual = Estudio::where('paciente_dni', $pacienteRoberto['dni'])
            ->where('estado', EstadoEstudio::Nuevo)
            ->first();

        if ($estudioActual) {
            $estudioActual->region_anatomica = 'torax';
            $estudioActual->save();
            $this->guardarArchivoDemo($estudioActual, 'rx_torax_actual.svg', $svgRxToraxActual);
        } else {
            $estudioActual = Estudio::create([
                'paciente_nombre' => $pacienteRoberto['nombre'],
                'paciente_dni' => $pacienteRoberto['dni'],
                'paciente_edad' => $pacienteRoberto['edad'],
                'paciente_telefono' => $pacienteRoberto['telefono'],
                'paciente_email' => $pacienteRoberto['email'],
                'tipo_estudio_id' => $tipoRxTorax->id,
                'region_anatomica' => 'torax',
                'tecnico_id' => $tecnico->id,
                'fecha_estudio' => Carbon::now()->subHours(2),
                'estado' => EstadoEstudio::Nuevo,
                'modalidad' => 'CR',
                'study_instance_uid' => '1.2.840.113619.2.55.3.28451236.4521',
                'accession_number' => 'ACC-20261027-001',
            ]);
            $this->guardarArchivoDemo($estudioActual, 'rx_torax_actual.svg', $svgRxToraxActual);
        }
    }

    private function guardarArchivoDemo(Estudio $estudio, string $nombreArchivo, string $contenidoSvg): void
    {
        $disco = 'estudios';
        $pathRelativo = "estudios/{$estudio->id}/{$nombreArchivo}";

        try {
            Storage::disk($disco)->put($pathRelativo, $contenidoSvg);

            ArchivoEstudio::updateOrCreate(
                [
                    'estudio_id' => $estudio->id,
                    'nombre_original' => $nombreArchivo,
                ],
                [
                    'disco' => $disco,
                    'path' => $pathRelativo,
                    'mime_type' => 'image/svg+xml',
                    'tamano_bytes' => strlen($contenidoSvg),
                ]
            );
        } catch (\Throwable $e) {
            // Continúa sin interrumpir
        }
    }
}
