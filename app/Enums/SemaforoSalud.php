<?php

namespace App\Enums;

/**
 * Semáforo visual del Estado General de Salud y Proceso de Evolución del Paciente.
 * 
 * Permite identificar de inmediato la condición y tendencia del paciente sin obligar
 * al profesional a redactar un informe en lenguaje no técnico.
 */
enum SemaforoSalud: string
{
    case Favorable = 'favorable';
    case Estable = 'estable';
    case ControlRequerido = 'control_requerido';
    case AtencionInmediata = 'atencion_inmediata';

    public function label(): string
    {
        return match ($this) {
            self::Favorable => 'Evolución Favorable / Normal',
            self::Estable => 'Estable sin cambios',
            self::ControlRequerido => 'Control Periódico Requerido',
            self::AtencionInmediata => 'Atención Médica Inmediata',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Favorable => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            self::Estable => 'bg-sky-100 text-sky-800 border-sky-300',
            self::ControlRequerido => 'bg-amber-100 text-amber-800 border-amber-300',
            self::AtencionInmediata => 'bg-rose-100 text-rose-800 border-rose-300 animate-pulse',
        };
    }

    public function icono(): string
    {
        return match ($this) {
            self::Favorable => '🟢',
            self::Estable => '🔵',
            self::ControlRequerido => '🟡',
            self::AtencionInmediata => '🔴',
        };
    }

    /**
     * Explicación estandarizada del sistema sobre el proceso del paciente,
     * generada automáticamente sin requerir redacción manual por parte del médico.
     */
    public function explicacionPaciente(): string
    {
        return match ($this) {
            self::Favorable => 'El estudio presenta una evolución positiva o no muestra alteraciones patológicas significativas.',
            self::Estable => 'El cuadro clínico y las estructuras analizadas se mantienen estables respecto a controles previos.',
            self::ControlRequerido => 'Se observan hallazgos de evolución intermedia que ameritan un nuevo estudio de control según criterio del especialista.',
            self::AtencionInmediata => 'Se identifican hallazgos prioritarios que requieren seguimiento médico a la brevedad con su especialista de cabecera.',
        };
    }
}
