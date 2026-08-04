<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tipo de estudio (Electrocardiograma, Holter 24hs, Espirometría, ...).
 * Pertenece a una Especialidad. Reemplaza el objeto DB.tiposEstudio
 * hardcodeado del prototipo.
 */
class TipoEstudio extends Model
{
    use HasFactory;
    protected $table = 'tipos_estudio';

    protected $fillable = [
        'especialidad_id',
        'nombre',
    ];

    public function especialidad(): BelongsTo
    {
        return $this->belongsTo(Especialidad::class);
    }

    public function estudios(): HasMany
    {
        return $this->hasMany(Estudio::class);
    }
}