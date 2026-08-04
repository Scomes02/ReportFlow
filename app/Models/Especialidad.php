<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Especialidad médica (Cardiología, Neumonología, ...).
 *
 * Se modela como tabla -y no como enum fijo, tal como en el prototipo-
 * porque el hospital va a sumar especialidades sin que eso implique
 * un deploy de código: alcanza con un INSERT + sus tipos de estudio.
 */
class Especialidad extends Model
{
    use HasFactory;
    protected $table = 'especialidades';

    protected $fillable = [
        'nombre',
        'slug',
    ];

    public function tiposEstudio(): HasMany
    {
        return $this->hasMany(TipoEstudio::class);
    }

    public function estudios(): HasMany
    {
        return $this->hasMany(Estudio::class);
    }
}