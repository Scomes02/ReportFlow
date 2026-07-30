<?php

namespace App\Enums;

/**
 * Roles del sistema. Admin queda separado de RRHH (decisión de negocio):
 * Admin gestiona altas de usuarios, RRHH audita y liquida honorarios.
 */
enum RolUsuario: string
{
    case Admin = 'admin';
    case Tecnico = 'tecnico';
    case Medico = 'medico';
    case Rrhh = 'rrhh';
    case CallCenter = 'callcenter';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrador',
            self::Tecnico => 'Técnico',
            self::Medico => 'Médico',
            self::Rrhh => 'RRHH / Administración',
            self::CallCenter => 'Call Center',
        };
    }
}