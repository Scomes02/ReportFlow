<?php

namespace App\Services;

use App\Enums\EstadoEstudio;
use App\Models\ArchivoEstudio;
use App\Models\Estudio;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class AlmacenamientoEstudioService
{
    private const DISCO = 'estudios'; // lo vamos a definir en config/filesystems.php en el próximo paso

    /**
     * @param  array<string, mixed>  $datosEstudio
     * @param  UploadedFile[]|null  $archivos
     */
    public function crear(array $datosEstudio, ?array $archivos = null): Estudio
    {
        return DB::transaction(function () use ($datosEstudio, $archivos) {
            $estudio = Estudio::create([
                ...$datosEstudio,
                'estado' => EstadoEstudio::Nuevo,
            ]);

            if (!empty($archivos)) {
                foreach ($archivos as $archivo) {
                    $this->adjuntarArchivo($estudio, $archivo);
                }
            }

            return $estudio->load('archivos');
        });
    }

    public function adjuntarArchivo(Estudio $estudio, UploadedFile $archivo): ArchivoEstudio
    {
        $path = $archivo->store("estudios/{$estudio->id}", self::DISCO);

        return $estudio->archivos()->create([
            'disco' => self::DISCO,
            'path' => $path,
            'nombre_original' => $archivo->getClientOriginalName(),
            'mime_type' => $archivo->getClientMimeType(),
            'tamano_bytes' => $archivo->getSize(),
        ]);
    }
}