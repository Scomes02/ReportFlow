<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Adenda: comentario que el médico agrega a un informe ya firmado,
 * una vez que pasaron las 24hs de edición directa. Es un anexo,
 * no reemplaza el texto original del informe.
 */
class Adenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'estudio_id',
        'medico_id',
        'contenido',
    ];

    public function estudio(): BelongsTo
    {
        return $this->belongsTo(Estudio::class);
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'medico_id');
    }
}