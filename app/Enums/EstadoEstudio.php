<?php

namespace App\Enums;

/**
 * Estados posibles de un Estudio dentro del flujo ReportFlow.
 *
 * Nuevo      -> recién subido por el Técnico, pendiente de worklist médico.
 * Informado  -> el Médico redactó y firmó el informe.
 * Rechazado  -> el Médico lo devolvió al Técnico con un motivo, a la espera
 *               de que se vuelva a cargar / corregir el estudio.
 */
enum EstadoEstudio: string
{
    case Nuevo = 'nuevo';
    case Informado = 'informado';
    case Rechazado = 'rechazado';

    public function label(): string
    {
        return match ($this) {
            self::Nuevo => 'Nuevo',
            self::Informado => 'Informado',
            self::Rechazado => 'Rechazado - Rehacer',
        };
    }

    /** Clases Tailwind para el badge de estado, para no repetir colores en cada vista. */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Nuevo => 'bg-slate-100 text-slate-700 border-slate-200',
            self::Informado => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::Rechazado => 'bg-red-100 text-red-800 border-red-200',
        };
    }
}