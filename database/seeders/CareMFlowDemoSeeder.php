<?php

namespace Database\Seeders;

use App\Enums\EstadoEstudio;
use App\Enums\SemaforoSalud;
use App\Models\ArchivoEstudio;
use App\Models\Especialidad;
use App\Models\Estudio;
use App\Models\TipoEstudio;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class CareMFlowDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ejecutar seeders base requeridos
        $this->call([
            EspecialidadSeeder::class,
            TecnicoDePruebaSeeder::class,
            UsuariosDemoSeeder::class,
        ]);

        $tecnico = User::where('role', 'tecnico')->first() ?? User::first();
        $medico = User::where('role', 'medico')->first() ?? User::first();

        // 2. Asignar regiones anatómicas por defecto a todos los tipos de estudio
        $regionesPorTipo = [
            'Radiografía de Tórax' => 'torax',
            'Radiografía de Tórax Frente y Perfil' => 'torax',
            'Espirometría' => 'torax',
            'Electrocardiograma' => 'cardiovascular',
            'Holter 24hs' => 'cardiovascular',
            'Ecocardiograma' => 'cardiovascular',
            'Ergometría' => 'cardiovascular',
            'Angiografía Coronaria' => 'cardiovascular',
            'Tomografía Axial Computada de Cerebro' => 'cerebro',
            'Electroencefalograma' => 'cerebro',
            'Potenciales Evocados' => 'cerebro',
            'Evaluación Neurocognitiva' => 'cerebro',
            'Radiografía' => 'musculoesqueletico',
            'Resonancia Magnética' => 'musculoesqueletico',
            'Resonancia Magnética Nuclear de Rodilla' => 'musculoesqueletico',
            'Endoscopía Digestiva Alta' => 'digestivo',
            'Dermatoscopía Digital' => 'piel',
            'Fondo de Ojo' => 'ojos',
            'Campo Visual' => 'ojos',
            'Evaluación Psicológica' => 'salud_mental',
        ];

        foreach ($regionesPorTipo as $nombreTipo => $region) {
            TipoEstudio::where('nombre', $nombreTipo)->update(['region_anatomica_defecto' => $region]);
        }

        // Obtener o crear tipos de estudio para los casos clínicos
        $espRadio = Especialidad::firstOrCreate(['slug' => 'radiologia'], ['nombre' => 'Radiología']);
        $espCardio = Especialidad::firstOrCreate(['slug' => 'cardiologia'], ['nombre' => 'Cardiología']);
        $espNeumo = Especialidad::firstOrCreate(['slug' => 'neumonologia'], ['nombre' => 'Neumonología']);
        $espNeuro = Especialidad::firstOrCreate(['slug' => 'neurologia'], ['nombre' => 'Neurología']);
        $espTrauma = Especialidad::firstOrCreate(['slug' => 'traumatologia'], ['nombre' => 'Traumatología']);

        $tipoRxTorax = TipoEstudio::firstOrCreate(
            ['nombre' => 'Radiografía de Tórax Frente y Perfil'],
            ['especialidad_id' => $espRadio->id, 'region_anatomica_defecto' => 'torax']
        );
        $tipoRxNeumo = TipoEstudio::firstOrCreate(
            ['nombre' => 'Radiografía de Tórax'],
            ['especialidad_id' => $espNeumo->id, 'region_anatomica_defecto' => 'torax']
        );
        $tipoEcoCardio = TipoEstudio::firstOrCreate(
            ['nombre' => 'Ecocardiograma'],
            ['especialidad_id' => $espCardio->id, 'region_anatomica_defecto' => 'cardiovascular']
        );
        $tipoCtCerebro = TipoEstudio::firstOrCreate(
            ['nombre' => 'Tomografía Axial Computada de Cerebro'],
            ['especialidad_id' => $espNeuro->id, 'region_anatomica_defecto' => 'cerebro']
        );
        $tipoRmRodilla = TipoEstudio::firstOrCreate(
            ['nombre' => 'Resonancia Magnética Nuclear de Rodilla'],
            ['especialidad_id' => $espTrauma->id, 'region_anatomica_defecto' => 'musculoesqueletico']
        );
        $tipoAngio = TipoEstudio::firstOrCreate(
            ['nombre' => 'Angiografía Coronaria'],
            ['especialidad_id' => $espCardio->id, 'region_anatomica_defecto' => 'cardiovascular']
        );

        // SVGs Médicos Realistas de Demostración
        $svgRxToraxActual = $this->generarSvgRxTorax('Rx Tórax Actual (Hoy) - Derrame laminar derecho y cardiomegalia');
        $svgRxToraxPrevio = $this->generarSvgRxTorax('Rx Tórax Previa (Hace 3 meses) - Control basal', true);
        $svgEcocardiograma = $this->generarSvgEcocardiograma();
        $svgCtCerebro = $this->generarSvgCerebro();
        $svgRmRodilla = $this->generarSvgRodilla();

        // =========================================================================
        // CASO ESTRELLA: Roberto Carlos Álvarez (Demostración Completa)
        // =========================================================================

        $robertoDni = '28451236';
        $robertoNombre = 'Roberto Carlos Álvarez';
        $robertoTelefono = '261 4521-3367';
        $robertoEmail = 'roberto.alvarez@gmail.com';

        // Estudio 1 de Roberto: Ecocardiograma (hace 8 meses)
        $estudioEco = Estudio::create([
            'paciente_nombre' => $robertoNombre,
            'paciente_dni' => $robertoDni,
            'paciente_edad' => 46,
            'paciente_telefono' => $robertoTelefono,
            'paciente_email' => $robertoEmail,
            'tipo_estudio_id' => $tipoEcoCardio->id,
            'tecnico_id' => $tecnico->id,
            'medico_id' => $medico->id,
            'fecha_estudio' => Carbon::now()->subMonths(8),
            'estado' => EstadoEstudio::Informado,
            'region_anatomica' => 'cardiovascular',
            'modalidad' => 'US',
            'informe' => "Ecocardiograma Doppler Color:\nVentrículo izquierdo dilatado con motilidad global deprimida. Fracción de eyección calculada en 42%. Signos compatibles con insuficiencia cardíaca congestiva e hipertensión venocapilar pulmonar secundaria. Leve reflujo mitral funcional.\nConclusión: Cardiopatía dilatada con disfunción sistólica moderada.",
            'informe_paciente' => "Hola Roberto:\nTu estudio muestra que el músculo de tu corazón está trabajando con un poco menos de fuerza de lo habitual (bombea al 42% en lugar de los valores típicos superiores al 55%). Esto puede generar sensación de falta de aire o cansancio al realizar esfuerzos.\nTu médico cardiólogo ajustará la medicación para aliviar la carga de trabajo de tu corazón.",
            'semaforo_salud' => SemaforoSalud::ControlRequerido,
            'semaforo_evolucion' => 'amarillo',
            'resumen_evolucion' => "Disfunción sistólica ventricular izquierda (FEy 42%). Requiere control cardiológico periódico.",
            'firmado_at' => Carbon::now()->subMonths(8)->addHours(4),
        ]);
        $this->guardarArchivoDemo($estudioEco, 'ecocardiograma_doppler.svg', $svgEcocardiograma);

        // Estudio 2 de Roberto: Rx Tórax (hace 3 meses)
        $estudioRxPrevio = Estudio::create([
            'paciente_nombre' => $robertoNombre,
            'paciente_dni' => $robertoDni,
            'paciente_edad' => 46,
            'paciente_telefono' => $robertoTelefono,
            'paciente_email' => $robertoEmail,
            'tipo_estudio_id' => $tipoRxNeumo->id,
            'tecnico_id' => $tecnico->id,
            'medico_id' => $medico->id,
            'fecha_estudio' => Carbon::now()->subMonths(3),
            'estado' => EstadoEstudio::Informado,
            'region_anatomica' => 'torax',
            'modalidad' => 'CR',
            'informe' => "Radiografía de Tórax Digital:\nIncremento simétrico de la trama vascular hilio-basal bilateral compatible con redistribución de flujo. Seno costofrénico derecho con ocupación por derrame pleural laminar moderado. Índice cardiotorácico aumentado (0.58), compatible con cardiomegalia grado II a expensas de cavidades izquierdas.",
            'informe_paciente' => "Estimado Roberto:\nEn la placa de tórax se observa una pequeña acumulación de líquido en la base del pulmón derecho y la silueta del corazón algo agrandada. Esto coincide con el trabajo que viene realizando tu corazón y explica la sensación de pesadéz al respirar.\nEs fundamental continuar con la medicación indicada por tu especialista.",
            'semaforo_salud' => SemaforoSalud::ControlRequerido,
            'semaforo_evolucion' => 'amarillo',
            'resumen_evolucion' => "Derrame pleural derecho laminar secundario a sobrecarga cardiopulmonar.",
            'firmado_at' => Carbon::now()->subMonths(3)->addHours(2),
        ]);
        $this->guardarArchivoDemo($estudioRxPrevio, 'rx_torax_previa.svg', $svgRxToraxPrevio);

        // Estudio 3 de Roberto: Rx Tórax Frente y Perfil ACTUAL (Hoy - Pendiente de informar)
        $estudioRxActual = Estudio::create([
            'paciente_nombre' => $robertoNombre,
            'paciente_dni' => $robertoDni,
            'paciente_edad' => 46,
            'paciente_telefono' => $robertoTelefono,
            'paciente_email' => $robertoEmail,
            'tipo_estudio_id' => $tipoRxTorax->id,
            'tecnico_id' => $tecnico->id,
            'fecha_estudio' => Carbon::now()->subHours(2),
            'estado' => EstadoEstudio::Nuevo,
            'region_anatomica' => 'torax',
            'modalidad' => 'CR',
            'study_instance_uid' => '1.2.840.113619.2.55.3.28451236.4521',
            'accession_number' => 'ACC-20261028-001',
            'pacs_status' => 'ONLINE',
        ]);
        $this->guardarArchivoDemo($estudioRxActual, 'rx_torax_actual.svg', $svgRxToraxActual);

        // =========================================================================
        // CASO 2: María Florencia Acosta (Neurología - Control Evolutivo TAC)
        // =========================================================================
        $florenciaDni = '33654789';
        $florenciaNombre = 'María Florencia Acosta';

        // Estudio previo informado (hace 5 meses)
        $estudioFlorenciaPrevio = Estudio::create([
            'paciente_nombre' => $florenciaNombre,
            'paciente_dni' => $florenciaDni,
            'paciente_edad' => 38,
            'paciente_telefono' => '261 6234-1290',
            'paciente_email' => 'florencia.acosta@gmail.com',
            'tipo_estudio_id' => $tipoCtCerebro->id,
            'tecnico_id' => $tecnico->id,
            'medico_id' => $medico->id,
            'fecha_estudio' => Carbon::now()->subMonths(5),
            'estado' => EstadoEstudio::Informado,
            'region_anatomica' => 'cerebro',
            'modalidad' => 'CT',
            'informe' => "Tomografía Computada de Cerebro:\nEstructuras encefálicas simétricas. Sistema ventricular y cisternas de la base de morfología y calibre conservados. No se observan colecciones hemáticas ni áreas de isquemia aguda. Discreto engrosamiento mucoso en celdillas etmoidales.",
            'informe_paciente' => "Hola Florencia:\nTu tomografía cerebral está normal. No se observan lesiones, inflamaciones agudas ni signos de alerta. Hay una leve congestión en los senos nasales habitual por alergia.",
            'semaforo_salud' => SemaforoSalud::Favorable,
            'semaforo_evolucion' => 'verde',
            'resumen_evolucion' => "TAC de encéfalo dentro de límites normales.",
            'firmado_at' => Carbon::now()->subMonths(5)->addHours(5),
        ]);
        $this->guardarArchivoDemo($estudioFlorenciaPrevio, 'ct_cerebro_previo.svg', $svgCtCerebro);

        // Estudio nuevo de Florencia (Hoy - Pendiente)
        $estudioFlorenciaActual = Estudio::create([
            'paciente_nombre' => $florenciaNombre,
            'paciente_dni' => $florenciaDni,
            'paciente_edad' => 38,
            'paciente_telefono' => '261 6234-1290',
            'paciente_email' => 'florencia.acosta@gmail.com',
            'tipo_estudio_id' => $tipoCtCerebro->id,
            'tecnico_id' => $tecnico->id,
            'fecha_estudio' => Carbon::now()->subHours(4),
            'estado' => EstadoEstudio::Nuevo,
            'region_anatomica' => 'cerebro',
            'modalidad' => 'CT',
            'study_instance_uid' => '1.2.840.113619.2.55.3.33654789.6234',
            'accession_number' => 'ACC-20261028-002',
            'pacs_status' => 'ONLINE',
        ]);
        $this->guardarArchivoDemo($estudioFlorenciaActual, 'ct_cerebro_actual.svg', $svgCtCerebro);

        // =========================================================================
        // CASO 3: Diego Fernando Molina (Traumatología)
        // =========================================================================
        $diegoDni = '25874123';
        $diegoNombre = 'Diego Fernando Molina';

        $estudioDiegoPrevio = Estudio::create([
            'paciente_nombre' => $diegoNombre,
            'paciente_dni' => $diegoDni,
            'paciente_edad' => 51,
            'paciente_telefono' => '261 4123-5678',
            'paciente_email' => 'diego.molina@hotmail.com',
            'tipo_estudio_id' => $tipoRmRodilla->id,
            'tecnico_id' => $tecnico->id,
            'medico_id' => $medico->id,
            'fecha_estudio' => Carbon::now()->subMonths(7),
            'estado' => EstadoEstudio::Informado,
            'region_anatomica' => 'musculoesqueletico',
            'modalidad' => 'MR',
            'informe' => "RMN de Rodilla Derecha:\nMenisco medial con hiperintensidad grado I en cuerno posterior compatible con degeneración mucoide sin solución de continuidad en superficie articular. Ligamentos cruzados y colaterales íntegros. Discreto derrame articular sinovial.",
            'informe_paciente' => "Hola Diego:\nTu resonancia muestra un desgaste inicial leve en el cartílago de la rodilla derecha sin rotura ligamentaria ni meniscal importante. Con kinesiología y fortalecimiento muscular suele evolucionar muy bien.",
            'semaforo_salud' => SemaforoSalud::Estable,
            'semaforo_evolucion' => 'azul',
            'resumen_evolucion' => "Meniscopatía medial grado I estable.",
            'firmado_at' => Carbon::now()->subMonths(7)->addDay(),
        ]);
        $this->guardarArchivoDemo($estudioDiegoPrevio, 'rmn_rodilla_previa.svg', $svgRmRodilla);

        $estudioDiegoActual = Estudio::create([
            'paciente_nombre' => $diegoNombre,
            'paciente_dni' => $diegoDni,
            'paciente_edad' => 51,
            'paciente_telefono' => '261 4123-5678',
            'paciente_email' => 'diego.molina@hotmail.com',
            'tipo_estudio_id' => $tipoRmRodilla->id,
            'tecnico_id' => $tecnico->id,
            'fecha_estudio' => Carbon::now()->subHours(6),
            'estado' => EstadoEstudio::Nuevo,
            'region_anatomica' => 'musculoesqueletico',
            'modalidad' => 'MR',
            'study_instance_uid' => '1.2.840.113619.2.55.3.25874123.4123',
            'accession_number' => 'ACC-20261028-003',
            'pacs_status' => 'ONLINE',
        ]);
        $this->guardarArchivoDemo($estudioDiegoActual, 'rmn_rodilla_actual.svg', $svgRmRodilla);

        // =========================================================================
        // CASO 4: Sergio Daniel Ledesma (Cardiología / Angiografía)
        // =========================================================================
        $sergioDni = '22147896';
        $sergioNombre = 'Sergio Daniel Ledesma';

        $estudioSergio = Estudio::create([
            'paciente_nombre' => $sergioNombre,
            'paciente_dni' => $sergioDni,
            'paciente_edad' => 59,
            'paciente_telefono' => '261 5123-4890',
            'paciente_email' => 'sergio.ledesma@yahoo.com',
            'tipo_estudio_id' => $tipoAngio->id,
            'tecnico_id' => $tecnico->id,
            'fecha_estudio' => Carbon::now()->subHours(8),
            'estado' => EstadoEstudio::Nuevo,
            'region_anatomica' => 'cardiovascular',
            'modalidad' => 'XA',
            'study_instance_uid' => '1.2.840.113619.2.55.3.22147896.5123',
            'accession_number' => 'ACC-20261028-004',
            'pacs_status' => 'ONLINE',
        ]);
        $this->guardarArchivoDemo($estudioSergio, 'angiografia_coronaria.svg', $svgEcocardiograma);
    }

    /**
     * Guarda el archivo SVG en el disco 'estudios' y crea el registro de ArchivoEstudio.
     */
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
            // Continúa sin fallar si storage tiene permisos restringidos
        }
    }

    /**
     * Genera una placa SVG de Radiografía de Tórax altamente realista.
     */
    private function generarSvgRxTorax(string $subtitulo, bool $esPrevio = false): string
    {
        $opacidadDerrame = $esPrevio ? '0.25' : '0.65';
        $anchoCorazon = $esPrevio ? '90' : '110';
        $fechaTexto = $esPrevio ? 'ESTUDIO COMPARATIVO PREVIO' : 'ESTUDIO ACTUAL';
        $colorEtiqueta = $esPrevio ? '#f59e0b' : '#10b981';

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 700" width="100%" height="100%">
    <rect width="600" height="700" fill="#030712"/>
    
    <!-- Metadatos Radiológicos DICOM -->
    <text x="30" y="35" fill="{$colorEtiqueta}" font-family="monospace" font-size="12" font-weight="bold">● {$fechaTexto}</text>
    <text x="30" y="55" fill="#94a3b8" font-family="monospace" font-size="11">MOD: CR | KVP: 125 | mA: 300 | EXP: 14ms</text>
    <text x="30" y="73" fill="#64748b" font-family="monospace" font-size="10">{$subtitulo}</text>
    <text x="540" y="45" fill="#f8fafc" font-family="monospace" font-size="22" font-weight="bold">R</text>

    <!-- Parénquima Pulmonar (Campos Izquierdo y Derecho) -->
    <path d="M 160 170 C 120 240, 110 400, 175 510 C 230 490, 260 450, 270 370 C 260 250, 220 190, 160 170 Z" fill="#0f172a" stroke="#334155" stroke-width="2"/>
    <path d="M 440 170 C 480 240, 490 400, 425 510 C 370 490, 340 450, 330 370 C 340 250, 380 190, 440 170 Z" fill="#0f172a" stroke="#334155" stroke-width="2"/>

    <!-- Derrame Pleural en Seno Costofrénico Derecho -->
    <path d="M 125 460 Q 155 515 220 500 C 180 505 140 490 125 460 Z" fill="#94a3b8" opacity="{$opacidadDerrame}" stroke="#cbd5e1" stroke-width="1.5"/>

    <!-- Silueta Cardíaca (Cardiomegalia con ICT incrementado) -->
    <ellipse cx="320" cy="420" rx="{$anchoCorazon}" ry="75" fill="#334155" stroke="#475569" stroke-width="2" transform="rotate(-15 320 420)"/>

    <!-- Costillas (Opacidades óseas) -->
    <path d="M 175 220 Q 300 260 425 220" fill="none" stroke="#e2e8f0" stroke-width="8" opacity="0.35" stroke-linecap="round"/>
    <path d="M 155 270 Q 300 315 445 270" fill="none" stroke="#e2e8f0" stroke-width="9" opacity="0.35" stroke-linecap="round"/>
    <path d="M 135 325 Q 300 370 465 325" fill="none" stroke="#e2e8f0" stroke-width="9" opacity="0.35" stroke-linecap="round"/>
    <path d="M 125 385 Q 300 435 475 385" fill="none" stroke="#e2e8f0" stroke-width="10" opacity="0.35" stroke-linecap="round"/>
    <path d="M 130 450 Q 300 500 470 450" fill="none" stroke="#e2e8f0" stroke-width="10" opacity="0.35" stroke-linecap="round"/>

    <!-- Columna Central y Clavículas -->
    <rect x="290" y="140" width="20" height="380" fill="#64748b" opacity="0.45" rx="3"/>
    <path d="M 145 160 Q 235 190 300 190" fill="none" stroke="#f1f5f9" stroke-width="11" stroke-linecap="round"/>
    <path d="M 455 160 Q 365 190 300 190" fill="none" stroke="#f1f5f9" stroke-width="11" stroke-linecap="round"/>

    <!-- Barra de Calibración Inferior -->
    <text x="30" y="660" fill="#64748b" font-family="monospace" font-size="10">ReportFlow Imaging Engine | W: 2500 L: -600 (Preset Pulmonar)</text>
    <rect x="30" y="675" width="540" height="3" fill="#334155" rx="1.5"/>
</svg>
SVG;
    }

    /**
     * Genera una placa SVG de Ecocardiograma Doppler Color.
     */
    private function generarSvgEcocardiograma(): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 700" width="100%" height="100%">
    <rect width="600" height="700" fill="#020617"/>
    <text x="30" y="35" fill="#38bdf8" font-family="monospace" font-size="12" font-weight="bold">● ECOCARDIOGRAMA DOPPLER COLOR</text>
    <text x="30" y="55" fill="#94a3b8" font-family="monospace" font-size="11">MOD: US | FREQ: 3.5 MHz | FEy: 42% | GAIN: 68dB</text>

    <!-- Sector ultrasónico en abanico -->
    <path d="M 300 120 L 100 520 A 320 320 0 0 0 500 520 Z" fill="#0b132b" stroke="#1e293b" stroke-width="2"/>

    <!-- Cavidades Cardíacas (4 Cámaras) -->
    <ellipse cx="260" cy="330" rx="45" ry="60" fill="#1e293b" stroke="#0ea5e9" stroke-width="2" opacity="0.7"/>
    <ellipse cx="340" cy="330" rx="40" ry="55" fill="#1e293b" stroke="#0ea5e9" stroke-width="2" opacity="0.7"/>
    <ellipse cx="270" cy="430" rx="35" ry="45" fill="#1e293b" stroke="#0284c7" stroke-width="2" opacity="0.6"/>
    <ellipse cx="335" cy="430" rx="30" ry="40" fill="#1e293b" stroke="#0284c7" stroke-width="2" opacity="0.6"/>

    <!-- Doppler Color Flujo Mitral (Azul/Rojo) -->
    <path d="M 280 370 Q 295 400 310 380 Q 290 350 280 370 Z" fill="#ef4444" opacity="0.8"/>
    <path d="M 300 380 Q 320 410 330 390 Q 315 370 300 380 Z" fill="#3b82f6" opacity="0.8"/>

    <!-- Trazado ECG inferior -->
    <path d="M 30 630 L 150 630 L 160 620 L 170 650 L 180 580 L 190 640 L 200 630 L 330 630 L 340 620 L 350 650 L 360 580 L 370 640 L 380 630 L 570 630" fill="none" stroke="#22c55e" stroke-width="2"/>
    <text x="30" y="665" fill="#64748b" font-family="monospace" font-size="10">ECG Sincronizado: 74 lpm | Ritmo Sinusal</text>
</svg>
SVG;
    }

    /**
     * Genera una placa SVG de Tomografía de Cerebro.
     */
    private function generarSvgCerebro(): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 700" width="100%" height="100%">
    <rect width="600" height="700" fill="#000000"/>
    <text x="30" y="35" fill="#a855f7" font-family="monospace" font-size="12" font-weight="bold">● TOMOGRAFÍA COMPUTADA DE CEREBRO (CORTE AXIAL)</text>
    <text x="30" y="55" fill="#94a3b8" font-family="monospace" font-size="11">MOD: CT | SLICE: 5.0mm | FOV: 240mm | W: 80 L: 35</text>

    <!-- Calota craneal (Hueso) -->
    <ellipse cx="300" cy="360" rx="190" ry="230" fill="#18181b" stroke="#ffffff" stroke-width="12"/>

    <!-- Parénquima Cerebral -->
    <ellipse cx="300" cy="360" rx="180" ry="220" fill="#27272a"/>

    <!-- Cisura interhemisférica -->
    <line x1="300" y1="145" x2="300" y2="575" stroke="#09090b" stroke-width="3"/>

    <!-- Ventrículos Laterales -->
    <path d="M 285 300 C 270 320 260 360 280 390 C 290 370 295 330 285 300 Z" fill="#09090b"/>
    <path d="M 315 300 C 330 320 340 360 320 390 C 310 370 305 330 315 300 Z" fill="#09090b"/>

    <text x="30" y="665" fill="#64748b" font-family="monospace" font-size="10">ReportFlow Neurological Imaging Module</text>
</svg>
SVG;
    }

    /**
     * Genera una placa SVG de Resonancia Magnética de Rodilla.
     */
    private function generarSvgRodilla(): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 700" width="100%" height="100%">
    <rect width="600" height="700" fill="#050505"/>
    <text x="30" y="35" fill="#14b8a6" font-family="monospace" font-size="12" font-weight="bold">● RESONANCIA MAGNÉTICA NUCLEAR DE RODILLA (SAGITAL T2)</text>
    <text x="30" y="55" fill="#94a3b8" font-family="monospace" font-size="11">MOD: MR | TR: 3200ms | TE: 85ms | SLICE: 3.0mm</text>

    <!-- Fémur Distal -->
    <path d="M 220 120 L 220 280 C 220 340 380 340 380 280 L 380 120 Z" fill="#262626" stroke="#525252" stroke-width="3"/>
    
    <!-- Tibia Proximal -->
    <path d="M 230 430 C 230 380 370 380 370 430 L 370 600 L 230 600 Z" fill="#262626" stroke="#525252" stroke-width="3"/>

    <!-- Rótula (Patela) -->
    <ellipse cx="170" cy="280" rx="30" ry="45" fill="#262626" stroke="#525252" stroke-width="2"/>

    <!-- Meniscos y Cartílago (Hipersignales articulares) -->
    <polygon points="240,360 270,360 255,380" fill="#737373"/>
    <polygon points="330,360 360,360 345,380" fill="#737373"/>

    <text x="30" y="665" fill="#64748b" font-family="monospace" font-size="10">ReportFlow Musculoskeletal Protocol</text>
</svg>
SVG;
    }
}
