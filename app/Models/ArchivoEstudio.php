<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Archivo adjunto a un Estudio (PDF, JPG, DICOM, etc).
 * Un estudio puede tener más de un archivo (ej. Holter con varias placas),
 * por eso es una tabla propia y no una sola columna `path` en `estudios`.
 */
class ArchivoEstudio extends Model
{
    use HasFactory;
    protected $table = 'archivos_estudio';

    protected $fillable = [
        'estudio_id',
        'disco',
        'path',
        'nombre_original',
        'mime_type',
        'tamano_bytes',
    ];

    public function estudio(): BelongsTo
    {
        return $this->belongsTo(Estudio::class);
    }

    /** URL temporal firmada para previsualizar/descargar sin exponer el disco directo. */
    public function urlTemporal(int $minutos = 15): string
    {
        return Storage::disk($this->disco)->temporaryUrl(
            $this->path,
            now()->addMinutes($minutos)
        );
    }
}