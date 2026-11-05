<?php

namespace App\Services;

use App\Models\Estudio;
use Illuminate\Support\Collection;

/**
 * Servicio de Línea de Tiempo e Historial Longitudinal del Paciente en ReportFlow.
 * 
 * Centraliza la extracción de todos los estudios del paciente ordenados
 * cronológicamente para alimentar el Timeline interactivo y el Visor Comparativo Dual.
 */
class HistorialPacienteService
{
    /**
     * Obtiene la línea de tiempo completa del paciente para alimentar el Timeline y el visor dual
     */
    public function obtenerLineaDeTiempo(string $dniPaciente, ?int $estudioActualId = null): Collection
    {
        return Estudio::with(['tipoEstudio.especialidad', 'archivos', 'medico'])
            ->where('paciente_dni', $dniPaciente)
            ->orderBy('fecha_estudio', 'asc')
            ->get()
            ->map(function (Estudio $estudio) use ($estudioActualId) {
                $esActual = $estudio->id === $estudioActualId;
                $primerArchivo = $estudio->archivos->first();

                // Semáforo de evolución / salud
                $semaforo = $estudio->semaforo_salud;
                $semaforoValor = $semaforo?->value ?? ($estudio->semaforo_evolucion ?? 'favorable');
                $semaforoLabel = $semaforo?->label() ?? 'Evolución Favorable / Normal';
                $semaforoBadge = $semaforo?->badgeClasses() ?? 'bg-emerald-100 text-emerald-800 border-emerald-300';
                $semaforoIcono = $semaforo?->icono() ?? '🟢';

                return [
                    'id' => $estudio->id,
                    'es_actual' => $esActual,
                    'fecha' => $estudio->fecha_estudio->format('d/m/Y'),
                    'fecha_hora' => $estudio->fecha_estudio->format('d/m/Y - H:i'),
                    'fecha_corta' => $estudio->fecha_estudio->format('M Y'),
                    'especialidad' => $estudio->tipoEstudio->especialidad->nombre ?? 'General',
                    'especialidad_slug' => $estudio->tipoEstudio->especialidad->slug ?? 'general',
                    'tipo_estudio' => $estudio->tipoEstudio->nombre ?? 'Estudio',
                    'region_anatomica' => $estudio->region_anatomica ?? 'general',
                    'modalidad' => $estudio->modalidad ?? 'CR',
                    'estado' => $estudio->estado->value,
                    'estado_label' => $estudio->estado->label(),
                    'semaforo_salud' => $semaforoValor,
                    'semaforo_label' => $semaforoLabel,
                    'semaforo_badge' => $semaforoBadge,
                    'semaforo_icono' => $semaforoIcono,
                    'informe' => $estudio->informe,
                    'informe_paciente' => $estudio->informe_paciente,
                    'resumen_evolucion' => $estudio->resumen_evolucion,
                    'tiene_dicom' => $estudio->tieneDicom(),
                    'study_instance_uid' => $estudio->study_instance_uid,
                    'miniatura_url' => $primerArchivo ? route('medico.estudios.archivos.ver', [$estudio->id, $primerArchivo->id]) : null,
                    'archivos_count' => $estudio->archivos->count(),
                    'medico_nombre' => $estudio->medico->name ?? null,
                ];
            });
    }

    /**
     * Obtiene el estudio previo más relevante para comparación automática directa
     */
    public function obtenerEstudioPrevioComparativo(Estudio $estudioActual): ?Estudio
    {
        // 1. Busca el estudio previo más reciente de la misma región anatómica o tipo
        $estudioPrevio = Estudio::with(['tipoEstudio.especialidad', 'archivos', 'medico'])
            ->where('paciente_dni', $estudioActual->paciente_dni)
            ->where('id', '!=', $estudioActual->id)
            ->where('fecha_estudio', '<', $estudioActual->fecha_estudio)
            ->where(function ($query) use ($estudioActual) {
                if ($estudioActual->region_anatomica) {
                    $query->where('region_anatomica', $estudioActual->region_anatomica)
                          ->orWhere('tipo_estudio_id', $estudioActual->tipo_estudio_id);
                } else {
                    $query->where('tipo_estudio_id', $estudioActual->tipo_estudio_id);
                }
            })
            ->latest('fecha_estudio')
            ->first();

        // 2. Si no hay de la misma región, devuelve el estudio previo más reciente en general
        if (! $estudioPrevio) {
            $estudioPrevio = Estudio::with(['tipoEstudio.especialidad', 'archivos', 'medico'])
                ->where('paciente_dni', $estudioActual->paciente_dni)
                ->where('id', '!=', $estudioActual->id)
                ->where('fecha_estudio', '<', $estudioActual->fecha_estudio)
                ->latest('fecha_estudio')
                ->first();
        }

        return $estudioPrevio;
    }
}
