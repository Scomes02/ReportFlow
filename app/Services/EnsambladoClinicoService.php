<?php

namespace App\Services;

use App\Models\Estudio;
use Illuminate\Support\Collection;

/**
 * Servicio encargado de ensamblar la línea de tiempo evolutiva de imágenes
 * de un paciente, el cruce multidisciplinario y la evaluación de su estado general de salud.
 */
class EnsambladoClinicoService
{
    public function __construct(
        private readonly Dcm4cheeService $pacsService
    ) {}

    /**
     * Construye el timeline cronológico, cruce multidisciplinario y estado general para un estudio.
     *
     * @param Estudio $estudioActual
     * @return array
     */
    public function obtenerMosaicoYEstadoGeneral(Estudio $estudioActual): array
    {
        $dni = $estudioActual->paciente_dni;

        // Recuperar todos los estudios del paciente ordenados cronológicamente
        $estudiosPaciente = Estudio::with(['tipoEstudio.especialidad', 'medico', 'archivos'])
            ->where('paciente_dni', $dni)
            ->orderBy('fecha_estudio', 'asc')
            ->get();

        $timeline = [];
        $cruceMultidisciplinario = [];
        $alertasDetectadas = [];
        $puntajeSeveridad = 0; // 0 = Favorable, 1 = Estable/Seguimiento, 2+ = Atención Requerida

        foreach ($estudiosPaciente as $estudio) {
            $esActual = ($estudio->id === $estudioActual->id);
            $primerArchivo = $estudio->archivos->first(function ($a) {
                return str_starts_with($a->mime_type ?? '', 'image/') 
                    || str_ends_with(strtolower($a->nombre_original ?? ''), '.svg')
                    || str_ends_with(strtolower($a->nombre_original ?? ''), '.png')
                    || str_ends_with(strtolower($a->nombre_original ?? ''), '.jpg')
                    || str_ends_with(strtolower($a->nombre_original ?? ''), '.jpeg');
            }) ?? $estudio->archivos->first();

            $imagenUrl = null;
            if ($primerArchivo) {
                $imagenUrl = route('medico.estudios.archivos.ver', [$estudio->id, $primerArchivo->id]);
            } elseif ($estudio->tieneDicom()) {
                $imagenUrl = $this->pacsService->obtenerUrlRenderDicom($estudio->study_instance_uid);
            }

            // Metadatos y color de especialidad
            $especialidadSlug = $estudio->tipoEstudio->especialidad->slug ?? null;
            $especialidadNombre = $estudio->tipoEstudio->especialidad->nombre ?? 'General';
            $metaEspecialidad = $this->obtenerMetadatosEspecialidad($especialidadSlug);

            // Análisis clínico de texto del informe previo para ponderar estado general
            $textoInforme = strtolower($estudio->informe ?? '');
            $severidadEstudio = 'normal';

            if (str_contains($textoInforme, 'derrame') 
                || str_contains($textoInforme, 'insuficiencia') 
                || str_contains($textoInforme, 'cardiomegalia')
                || str_contains($textoInforme, 'isquemia')
                || str_contains($textoInforme, 'fractura')
                || str_contains($textoInforme, 'masa')
                || str_contains($textoInforme, 'tumor')
            ) {
                $severidadEstudio = 'atencion';
                $puntajeSeveridad += 2;
                $alertasDetectadas[] = [
                    'estudio' => $estudio->tipoEstudio->nombre ?? 'Estudio',
                    'fecha' => $estudio->fecha_estudio->format('d/m/Y'),
                    'detalle' => 'Hallazgo clínico de seguimiento en ' . $especialidadNombre
                ];
            } elseif (str_contains($textoInforme, 'moderado') 
                || str_contains($textoInforme, 'leve') 
                || str_contains($textoInforme, 'control')
                || str_contains($textoInforme, 'degenerativo')
            ) {
                if ($severidadEstudio !== 'atencion') {
                    $severidadEstudio = 'observacion';
                }
                $puntajeSeveridad += 1;
            }

            $datosEstudio = [
                'estudio_id' => $estudio->id,
                'es_actual' => $esActual,
                'estado' => $estudio->estado->value,
                'tipo_estudio' => $estudio->tipoEstudio->nombre ?? 'Estudio',
                'especialidad' => $especialidadNombre,
                'especialidad_slug' => $especialidadSlug,
                'meta_especialidad' => $metaEspecialidad,
                'modalidad' => $estudio->modalidad ?? 'CR',
                'tiene_dicom' => $estudio->tieneDicom(),
                'fecha_estudio' => $estudio->fecha_estudio->format('d/m/Y H:i'),
                'fecha_corta' => $estudio->fecha_estudio->format('d/m/Y'),
                'mes_ano' => $estudio->fecha_estudio->format('m/Y'),
                'medico' => $estudio->medico->name ?? 'Pendiente de informe',
                'informe' => $estudio->informe,
                'semaforo_evolucion' => $estudio->semaforo_evolucion ?? ($severidadEstudio === 'atencion' ? 'rojo' : ($severidadEstudio === 'observacion' ? 'naranja' : 'verde')),
                'imagen_url' => $imagenUrl,
                'severidad' => $severidadEstudio,
            ];

            $timeline[] = $datosEstudio;

            // Cruce Multidisciplinario: recopilamos informes previos de otros médicos
            if (!empty($estudio->informe)) {
                $cruceMultidisciplinario[] = $datosEstudio;
            }
        }

        // Separar estudios previos y determinar el estudio anterior inmediato (más reciente antes del actual)
        $estudiosPrevios = array_values(array_filter($timeline, fn($m) => !$m['es_actual']));
        $estudioAnterior = !empty($estudiosPrevios) ? end($estudiosPrevios) : null;

        // Determinar Estado General Sintético
        $estadoGeneral = $this->calcularEstadoGeneral($puntajeSeveridad, $estudiosPaciente, $alertasDetectadas);

        return [
            'paciente_dni' => $dni,
            'paciente_nombre' => $estudioActual->paciente_nombre,
            'paciente_edad' => $estudioActual->paciente_edad,
            'total_estudios' => count($timeline),
            'tiene_anterior' => !empty($estudioAnterior),
            'estudio_anterior' => $estudioAnterior,
            'timeline' => $timeline,
            'mosaico' => $timeline, // Compatibilidad hacia atrás
            'cruce_multidisciplinario' => $cruceMultidisciplinario,
            'estado_general' => $estadoGeneral,
        ];
    }

    /**
     * Devuelve paleta cromática e ícono correspondiente a cada especialidad médica.
     */
    public function obtenerMetadatosEspecialidad(?string $slug): array
    {
        return match(strtolower($slug ?? '')) {
            'cardiologia' => [
                'nombre' => 'Cardiología',
                'color' => '#ef4444',
                'bg_badge' => 'bg-rose-50 text-rose-700 border-rose-300',
                'border_accent' => 'border-rose-500',
                'badge' => 'bg-rose-100 text-rose-800 border-rose-200',
                'icono' => '🫀',
            ],
            'neumonologia' => [
                'nombre' => 'Neumonología',
                'color' => '#0284c7',
                'bg_badge' => 'bg-sky-50 text-sky-700 border-sky-300',
                'border_accent' => 'border-sky-500',
                'badge' => 'bg-sky-100 text-sky-800 border-sky-200',
                'icono' => '🫁',
            ],
            'neurologia' => [
                'nombre' => 'Neurología',
                'color' => '#8b5cf6',
                'bg_badge' => 'bg-purple-50 text-purple-700 border-purple-300',
                'border_accent' => 'border-purple-500',
                'badge' => 'bg-purple-100 text-purple-800 border-purple-200',
                'icono' => '🧠',
            ],
            'traumatologia' => [
                'nombre' => 'Traumatología',
                'color' => '#f59e0b',
                'bg_badge' => 'bg-amber-50 text-amber-700 border-amber-300',
                'border_accent' => 'border-amber-500',
                'badge' => 'bg-amber-100 text-amber-800 border-amber-200',
                'icono' => '🦴',
            ],
            'oftalmologia' => [
                'nombre' => 'Oftalmología',
                'color' => '#06b6d4',
                'bg_badge' => 'bg-cyan-50 text-cyan-700 border-cyan-300',
                'border_accent' => 'border-cyan-500',
                'badge' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
                'icono' => '👁️',
            ],
            'gastroenterologia' => [
                'nombre' => 'Gastroenterología',
                'color' => '#10b981',
                'bg_badge' => 'bg-emerald-50 text-emerald-700 border-emerald-300',
                'border_accent' => 'border-emerald-500',
                'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                'icono' => '🩺',
            ],
            'dermatologia' => [
                'nombre' => 'Dermatología',
                'color' => '#ec4899',
                'bg_badge' => 'bg-pink-50 text-pink-700 border-pink-300',
                'border_accent' => 'border-pink-500',
                'badge' => 'bg-pink-100 text-pink-800 border-pink-200',
                'icono' => '🔬',
            ],
            'psiquiatria' => [
                'nombre' => 'Psiquiatría',
                'color' => '#6366f1',
                'bg_badge' => 'bg-indigo-50 text-indigo-700 border-indigo-300',
                'border_accent' => 'border-indigo-500',
                'badge' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                'icono' => '🧩',
            ],
            default => [
                'nombre' => 'General',
                'color' => '#64748b',
                'bg_badge' => 'bg-slate-50 text-slate-700 border-slate-300',
                'border_accent' => 'border-slate-500',
                'badge' => 'bg-slate-100 text-slate-800 border-slate-200',
                'icono' => '📋',
            ]
        };
    }

    /**
     * Calcula la clasificación de salud del paciente y el resumen evolutivo
     */
    private function calcularEstadoGeneral(int $puntaje, Collection $estudios, array $alertas): array
    {
        $totalInformados = $estudios->where('estado', 'informado')->count();

        if ($totalInformados === 0) {
            return [
                'nivel' => 'inicial',
                'color' => 'sky',
                'badge' => 'bg-sky-50 text-sky-800 border-sky-200',
                'icono' => '🩺',
                'titulo' => 'Evaluación Basal Inicial',
                'descripcion' => 'Este es el primer estudio registrado para el paciente o sus estudios anteriores aún no tienen informe firmado.',
                'sugerencia' => 'Redactar informe clínico basal.',
                'alertas' => [],
            ];
        }

        if ($puntaje >= 2) {
            return [
                'nivel' => 'atencion',
                'color' => 'rose',
                'badge' => 'bg-rose-50 text-rose-800 border-rose-200',
                'icono' => '⚠️',
                'titulo' => 'Atención Clínica Requerida / Seguimiento Estrecho',
                'descripcion' => 'Se detectan antecedentes con patología activa o cambios evolutivos significativos en estudios previos.',
                'sugerencia' => 'Cotejar silueta, parénquima o estructuras anatómicas con los hallazgos descritos en el historial.',
                'alertas' => $alertas,
            ];
        }

        if ($puntaje === 1) {
            return [
                'nivel' => 'observacion',
                'color' => 'amber',
                'badge' => 'bg-amber-50 text-amber-800 border-amber-200',
                'icono' => '🔍',
                'titulo' => 'Cuadro Clínico en Observación / Estable',
                'descripcion' => 'El paciente presenta antecedentes de evolución crónica o hallazgos leves bajo control clínico.',
                'sugerencia' => 'Evaluar estabilidad de lesiones o parámetros respecto al estudio basal previo.',
                'alertas' => $alertas,
            ];
        }

        return [
            'nivel' => 'favorable',
            'color' => 'emerald',
            'badge' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            'icono' => '✅',
            'titulo' => 'Evolución Favorable / Sin Hallazgos Agudos',
            'descripcion' => 'Los estudios previos del paciente no registran patología aguda ni signos de descompensación.',
            'sugerencia' => 'Mantener controles diagnósticos de rutina habitual.',
            'alertas' => [],
        ];
    }
}
