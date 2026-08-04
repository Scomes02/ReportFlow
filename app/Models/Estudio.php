<?php

namespace App\Models;

use App\Enums\EstadoEstudio;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Estudio clínico. El Técnico crea el registro y adjunta los archivos;
 * el Médico lo redacta/firma. Acá dejamos los campos y relaciones que
 * el Técnico necesita completar al crear el estudio -el resto (informe,
 * firmado_at, motivo_rechazo) se termina de definir cuando se junte el
 * schema con la otra dupla.
 */
class Estudio extends Model
{
    use HasFactory;

    protected $fillable = [
        'paciente_nombre',
        'paciente_dni',
        'paciente_edad',
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
}