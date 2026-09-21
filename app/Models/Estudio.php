<?php

namespace App\Models;

use App\Enums\EstadoEstudio;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Estudio clínico. El Técnico crea el registro y adjunta los archivos;
 * el Médico lo redacta/firma; RRHH audita cuántos informes hizo cada
 * médico; Call Center confirma al paciente si ya está listo para retirar.
 */
class Estudio extends Model
{
    use HasFactory;

    protected $fillable = [
        'paciente_nombre',
        'paciente_dni',
        'paciente_edad',
        'paciente_telefono',
        'paciente_email',
        'tipo_estudio_id',
        'tecnico_id',
        'estado',
        'fecha_estudio',
    ];

    protected $casts = [
        'estado' => EstadoEstudio::class,
        'fecha_estudio' => 'datetime',
        'firmado_at' => 'datetime',
    ];

    public function tipoEstudio(): BelongsTo
    {
        return $this->belongsTo(TipoEstudio::class);
    }

    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'medico_id');
    }

    public function archivos(): HasMany
    {
        return $this->hasMany(ArchivoEstudio::class);
    }

    public function adendas(): HasMany
    {
        return $this->hasMany(Adenda::class);
    }

    // ============================================
    // ACCESSORS (Propiedades calculadas)
    // ============================================

    /**
     * Obtener la especialidad a través del tipo de estudio
     */
    public function getEspecialidadAttribute()
    {
        return $this->tipoEstudio->especialidad ?? null;
    }

    /**
     * Obtener el nombre de la especialidad
     */
    public function getEspecialidadNombreAttribute()
    {
        return $this->tipoEstudio->especialidad->nombre ?? 'Sin especialidad';
    }

    /**
     * Obtener el nombre del médico
     */
    public function getMedicoNombreAttribute()
    {
        return $this->medico->name ?? 'Sin asignar';
    }

    /**
     * Obtener el nombre del técnico
     */
    public function getTecnicoNombreAttribute()
    {
        return $this->tecnico->name ?? 'Sin asignar';
    }

    /**
     * Un estudio está "listo para entregar" cuando el médico ya lo
     * firmó. Se centraliza acá -en vez de comparar el enum a mano en
     * cada vista- porque Call Center, RRHH y el propio Médico necesitan
     * la misma regla exacta.
     */
    public function estaListoParaEntregar(): bool
    {
        return $this->estado === EstadoEstudio::Informado;
    }
}