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

class DicomEstudiosDemoSeeder extends Seeder
{
    public function run(): void
    {
        $tecnico = User::where('role', 'tecnico')->first() ?? User::factory()->create(['role' => 'tecnico']);
        
        // Especialidades médicas
        $especialidadRadio = Especialidad::firstOrCreate(
            ['slug' => 'radiologia'],
            ['nombre' => 'Radiología']
        );
        $especialidadCardio = Especialidad::firstOrCreate(
            ['slug' => 'cardiologia'],
            ['nombre' => 'Cardiología']
        );
        $especialidadNeuro = Especialidad::firstOrCreate(
            ['slug' => 'neurologia'],
            ['nombre' => 'Neurología']
        );
        $especialidadTrauma = Especialidad::firstOrCreate(
            ['slug' => 'traumatologia'],
            ['nombre' => 'Traumatología']
        );

        $tipoRxTorax = TipoEstudio::firstOrCreate(
            ['nombre' => 'Radiografía de Tórax Frente y Perfil', 'especialidad_id' => $especialidadRadio->id]
        );
        $tipoCtCerebro = TipoEstudio::firstOrCreate(
            ['nombre' => 'Tomografía Axial Computada de Cerebro', 'especialidad_id' => $especialidadNeuro->id]
        );
        $tipoRmRodilla = TipoEstudio::firstOrCreate(
            ['nombre' => 'Resonancia Magnética Nuclear de Rodilla', 'especialidad_id' => $especialidadTrauma->id]
        );
        $tipoAngio = TipoEstudio::firstOrCreate(
            ['nombre' => 'Angiografía Coronaria', 'especialidad_id' => $especialidadCardio->id]
        );

        // SVG Demo Plate de Radiografía con contrastes radiológicos
        $svgPlateRx = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 700" width="100%" height="100%">
    <rect width="600" height="700" fill="#05070a"/>
    <text x="30" y="40" fill="#00ffcc" font-family="monospace" font-size="14" font-weight="bold">DICOM PACS — dcm4chee-arc-light</text>
    <text x="30" y="60" fill="#7a8b9e" font-family="monospace" font-size="11">MOD: CR | KVP: 125 | mA: 250 | EXP: 12ms</text>
    <text x="520" y="40" fill="#ffffff" font-family="monospace" font-size="20" font-weight="bold">R</text>

    <!-- Parénquima Pulmonar y Caja Torácica -->
    <path d="M 160 180 C 130 250, 120 400, 180 500 C 230 480, 260 450, 270 380 C 260 260, 220 200, 160 180 Z" fill="#1b242e" stroke="#485c72" stroke-width="2"/>
    <path d="M 440 180 C 470 250, 480 400, 420 500 C 370 480, 340 450, 330 380 C 340 260, 380 200, 440 180 Z" fill="#1b242e" stroke="#485c72" stroke-width="2"/>

    <!-- Silueta Cardíaca -->
    <path d="M 270 350 C 280 470, 340 490, 370 460 C 380 420, 360 360, 330 350 Z" fill="#4d5d6e" stroke="#6f849b" stroke-width="2"/>

    <!-- Costillas (densidad ósea radiológica) -->
    <path d="M 180 230 Q 300 270 420 230" fill="none" stroke="#cfd8dc" stroke-width="8" opacity="0.45" stroke-linecap="round"/>
    <path d="M 160 280 Q 300 325 440 280" fill="none" stroke="#cfd8dc" stroke-width="9" opacity="0.45" stroke-linecap="round"/>
    <path d="M 140 335 Q 300 380 460 335" fill="none" stroke="#cfd8dc" stroke-width="9" opacity="0.45" stroke-linecap="round"/>
    <path d="M 130 395 Q 300 445 470 395" fill="none" stroke="#cfd8dc" stroke-width="10" opacity="0.45" stroke-linecap="round"/>
    <path d="M 135 460 Q 300 510 465 460" fill="none" stroke="#cfd8dc" stroke-width="10" opacity="0.45" stroke-linecap="round"/>

    <!-- Columna y Esternón central -->
    <rect x="290" y="160" width="20" height="360" fill="#90a4ae" opacity="0.5" rx="4"/>
    
    <!-- Clavículas -->
    <path d="M 150 170 Q 240 200 300 200" fill="none" stroke="#e0e0e0" stroke-width="10" stroke-linecap="round"/>
    <path d="M 450 170 Q 360 200 300 200" fill="none" stroke="#e0e0e0" stroke-width="10" stroke-linecap="round"/>

    <!-- Escala radiológica inferior -->
    <text x="30" y="660" fill="#7a8b9e" font-family="monospace" font-size="11">W/L: PRESET TORAX | ZOOM: 1.0x | ANONYMIZED CLINICAL DATA</text>
    <rect x="30" y="675" width="540" height="4" fill="url(#gradiente)" rx="2"/>
    <defs>
        <linearGradient id="gradiente" x1="0" y1="0" x2="1" y2="0">
            <stop offset="0%" stop-color="#05070a"/>
            <stop offset="50%" stop-color="#78909c"/>
            <stop offset="100%" stop-color="#ffffff"/>
        </linearGradient>
    </defs>
</svg>
SVG;

        $estudiosDicomDemo = [
            [
                'paciente_nombre' => 'Roberto Carlos Álvarez',
                'paciente_dni' => '28451236',
                'paciente_edad' => 46,
                'paciente_telefono' => '261 4521-3367',
                'paciente_email' => 'roberto.alvarez@gmail.com',
                'tipo_estudio_id' => $tipoRxTorax->id,
                'tecnico_id' => $tecnico->id,
                'fecha_estudio' => Carbon::now()->subHours(3),
                'estado' => EstadoEstudio::Nuevo,
                'study_instance_uid' => '1.2.840.113619.2.55.3.28451236.4521',
                'accession_number' => 'ACC-20261027-001',
                'modalidad' => 'CR',
                'pacs_status' => 'ONLINE',
                'archivo_demo' => 'placa_rx_torax.svg',
            ],
            [
                'paciente_nombre' => 'María Florencia Acosta',
                'paciente_dni' => '33654789',
                'paciente_edad' => 38,
                'paciente_telefono' => '261 6234-1290',
                'paciente_email' => 'florencia.acosta@gmail.com',
                'tipo_estudio_id' => $tipoCtCerebro->id,
                'tecnico_id' => $tecnico->id,
                'fecha_estudio' => Carbon::now()->subHours(6),
                'estado' => EstadoEstudio::Nuevo,
                'study_instance_uid' => '1.2.840.113619.2.55.3.33654789.6234',
                'accession_number' => 'ACC-20261027-002',
                'modalidad' => 'CT',
                'pacs_status' => 'ONLINE',
                'archivo_demo' => 'tomografia_cerebro.svg',
            ],
            [
                'paciente_nombre' => 'Diego Fernando Molina',
                'paciente_dni' => '25874123',
                'paciente_edad' => 51,
                'paciente_telefono' => '261 4123-5678',
                'paciente_email' => 'diego.molina@hotmail.com',
                'tipo_estudio_id' => $tipoRmRodilla->id,
                'tecnico_id' => $tecnico->id,
                'fecha_estudio' => Carbon::now()->subHours(12),
                'estado' => EstadoEstudio::Nuevo,
                'study_instance_uid' => '1.2.840.113619.2.55.3.25874123.4123',
                'accession_number' => 'ACC-20261027-003',
                'modalidad' => 'MR',
                'pacs_status' => 'ONLINE',
                'archivo_demo' => 'resonancia_rodilla.svg',
            ],
            [
                'paciente_nombre' => 'Sergio Daniel Ledesma',
                'paciente_dni' => '22147896',
                'paciente_edad' => 59,
                'paciente_telefono' => '261 5123-4890',
                'paciente_email' => 'sergio.ledesma@yahoo.com',
                'tipo_estudio_id' => $tipoAngio->id,
                'tecnico_id' => $tecnico->id,
                'fecha_estudio' => Carbon::now()->subHours(20),
                'estado' => EstadoEstudio::Nuevo,
                'study_instance_uid' => '1.2.840.113619.2.55.3.22147896.5123',
                'accession_number' => 'ACC-20261027-004',
                'modalidad' => 'XA',
                'pacs_status' => 'ONLINE',
                'archivo_demo' => 'angiografia_coronaria.svg',
            ],
        ];

        foreach ($estudiosDicomDemo as $item) {
            $archivoDemo = $item['archivo_demo'];
            unset($item['archivo_demo']);

            $estudio = Estudio::updateOrCreate(
                ['study_instance_uid' => $item['study_instance_uid']],
                $item
            );

            // Guardar placa de demostración en storage
            $disco = 'estudios';
            $pathRelativo = "estudios/{$estudio->id}/{$archivoDemo}";

            try {
                Storage::disk($disco)->put($pathRelativo, $svgPlateRx);

                ArchivoEstudio::updateOrCreate(
                    [
                        'estudio_id' => $estudio->id,
                        'nombre_original' => $archivoDemo,
                    ],
                    [
                        'disco' => $disco,
                        'path' => $pathRelativo,
                        'mime_type' => 'image/svg+xml',
                        'tamano_bytes' => strlen($svgPlateRx),
                    ]
                );
            } catch (\Throwable $e) {
                // Si el disco no está configurado para pruebas, continúa sin interrumpir
            }
        }
    }
}
