<?php

namespace App\Services;

use App\Models\Estudio;

/**
 * Servicio de Cruce Multidisciplinario en ReportFlow.
 * 
 * Analiza estudios previos del paciente en otras especialidades
 * e identifica correlaciones clínicas anatómicas, patrones cruzados
 * y alertas prioritarias para asistir al médico en el diagnóstico.
 */
class CruceMultidisciplinarioService
{
    /**
     * Analiza los estudios del paciente en otras especialidades y genera alertas clínicas de correlación
     */
    public function analizarCruces(Estudio $estudioActual): array
    {
        $especialidadActualId = $estudioActual->tipoEstudio?->especialidad_id;
        $regionActual = strtolower($estudioActual->region_anatomica ?? '');

        // Estudios previos en OTRAS especialidades
        $estudiosOtrasEspecialidades = Estudio::with(['tipoEstudio.especialidad', 'medico', 'archivos'])
            ->where('paciente_dni', $estudioActual->paciente_dni)
            ->where('id', '!=', $estudioActual->id)
            ->whereHas('tipoEstudio', function ($q) use ($especialidadActualId) {
                if ($especialidadActualId) {
                    $q->where('especialidad_id', '!=', $especialidadActualId);
                }
            })
            ->whereNotNull('informe')
            ->orderBy('fecha_estudio', 'desc')
            ->get();

        $alertas = [];
        $antecedentesPorEspecialidad = [];

        foreach ($estudiosOtrasEspecialidades as $estudioPrevio) {
            $especialidadNombre = $estudioPrevio->tipoEstudio->especialidad->nombre ?? 'Otra Especialidad';
            $regionPrevia = strtolower($estudioPrevio->region_anatomica ?? '');
            $informePrevio = strtolower($estudioPrevio->informe ?? '');
            $primerArchivo = $estudioPrevio->archivos->first();

            $semaforo = $estudioPrevio->semaforo_salud;
            $semaforoValor = $semaforo?->value ?? ($estudioPrevio->semaforo_evolucion ?? 'favorable');
            $semaforoBadge = $semaforo?->badgeClasses() ?? 'bg-emerald-100 text-emerald-800 border-emerald-300';
            $semaforoIcono = $semaforo?->icono() ?? '🟢';

            // Agrupación de antecedentes por especialidad
            $antecedentesPorEspecialidad[$especialidadNombre][] = [
                'estudio_id' => $estudioPrevio->id,
                'estudio' => $estudioPrevio->tipoEstudio->nombre ?? 'Estudio',
                'modalidad' => $estudioPrevio->modalidad ?? 'CR',
                'fecha' => $estudioPrevio->fecha_estudio->format('d/m/Y'),
                'medico' => $estudioPrevio->medico->name ?? 'Médico',
                'informe' => $estudioPrevio->informe,
                'resumen_evolucion' => $estudioPrevio->resumen_evolucion,
                'semaforo' => $semaforoValor,
                'semaforo_badge' => $semaforoBadge,
                'semaforo_icono' => $semaforoIcono,
                'archivo_url' => $primerArchivo ? route('medico.estudios.archivos.ver', [$estudioPrevio->id, $primerArchivo->id]) : null,
            ];

            // 1. Detección de coincidencia anatómica cruzada
            if ($regionActual && $regionPrevia && $regionActual === $regionPrevia) {
                $alertas[] = [
                    'tipo' => 'coincidencia_anatomica',
                    'nivel' => 'info',
                    'icono' => 'ℹ️',
                    'titulo' => "Misma región anatómica evaluada en {$especialidadNombre}",
                    'mensaje' => "El paciente cuenta con un estudio de {$estudioPrevio->tipoEstudio->nombre} realizado el {$estudioPrevio->fecha_estudio->format('d/m/Y')}. Se recomienda cotejar hallazgos anatómicos.",
                    'estudio_id' => $estudioPrevio->id,
                    'fecha' => $estudioPrevio->fecha_estudio->format('d/m/Y'),
                ];
            }

            // 2. Detección de patrones cardiopulmonares frecuentes
            if (
                ($regionActual === 'torax' || str_contains($regionActual, 'cardio')) &&
                (str_contains($informePrevio, 'insuficiencia') || str_contains($informePrevio, 'cardiomegalia') || str_contains($informePrevio, 'derrame'))
            ) {
                $alertas[] = [
                    'tipo' => 'correlacion_clinica',
                    'nivel' => 'alerta',
                    'icono' => '⚠️',
                    'titulo' => "Correlación Cardiopulmonar Detectada ({$especialidadNombre})",
                    'mensaje' => "Antecedente de hallazgos cardiopulmonares previos ({$estudioPrevio->fecha_estudio->format('d/m/Y')}). Sugerencia: evaluar silueta cardíaca y parénquima pulmonar en la placa actual.",
                    'estudio_id' => $estudioPrevio->id,
                    'fecha' => $estudioPrevio->fecha_estudio->format('d/m/Y'),
                ];
            }

            // 3. Alerta por semáforo prioritario en otro estudio
            if ($semaforoValor === 'atencion_inmediata' || $semaforoValor === 'rojo') {
                $alertas[] = [
                    'tipo' => 'antecedente_critico',
                    'nivel' => 'critico',
                    'icono' => '🚨',
                    'titulo' => "Alerta Prioritaria en {$especialidadNombre}",
                    'mensaje' => "El estudio previo del {$estudioPrevio->fecha_estudio->format('d/m/Y')} fue clasificado con semáforo de atención prioritaria.",
                    'estudio_id' => $estudioPrevio->id,
                    'fecha' => $estudioPrevio->fecha_estudio->format('d/m/Y'),
                ];
            }
        }

        return [
            'total_estudios_cruzados' => $estudiosOtrasEspecialidades->count(),
            'total_especialidades' => count($antecedentesPorEspecialidad),
            'alertas' => array_values(collect($alertas)->unique('titulo')->all()),
            'antecedentes' => $antecedentesPorEspecialidad,
        ];
    }
}
