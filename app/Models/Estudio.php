<?php

namespace App\Models;

use App\Enums\EstadoEstudio;
use App\Enums\SemaforoSalud;
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
        'region_anatomica',
        'study_instance_uid',
        'accession_number',
        'modalidad',
        'pacs_status',
        'tecnico_id',
        'medico_id',
        'estado',
        'informe',
        'semaforo_salud',
        'semaforo_evolucion',
        'resumen_evolucion',
        'fecha_estudio',
        'firmado_at',
    ];

    protected $casts = [
        'estado' => EstadoEstudio::class,
        'semaforo_salud' => SemaforoSalud::class,
        'fecha_estudio' => 'datetime',
        'firmado_at' => 'datetime',
        'aviso_enviado_at' => 'datetime',
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

    public function avisoEnviadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aviso_enviado_por');
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
     * Región anatómica efectiva del estudio (prioriza el valor directo, luego el defecto del tipo, o lo infiere).
     */
    public function getRegionAnatomicaEfectivaAttribute(): string
    {
        if (!empty($this->region_anatomica)) {
            return $this->region_anatomica;
        }

        if (!empty($this->tipoEstudio?->region_anatomica_defecto)) {
            return $this->tipoEstudio->region_anatomica_defecto;
        }

        $nombre = mb_strtolower($this->tipoEstudio?->nombre ?? '');
        if (str_contains($nombre, 'cerebro') || str_contains($nombre, 'cráneo') || str_contains($nombre, 'craneo') || str_contains($nombre, 'encefal') || str_contains($nombre, 'head')) {
            return 'cerebro';
        }
        if (str_contains($nombre, 'tórax') || str_contains($nombre, 'torax') || str_contains($nombre, 'pulm') || str_contains($nombre, 'pleur') || str_contains($nombre, 'espiromet')) {
            return 'torax';
        }
        if (str_contains($nombre, 'cardio') || str_contains($nombre, 'coronari') || str_contains($nombre, 'coraz') || str_contains($nombre, 'ecg') || str_contains($nombre, 'electrocardi') || str_contains($nombre, 'holter') || str_contains($nombre, 'ergometr') || str_contains($nombre, 'angiograf')) {
            return 'cardiovascular';
        }
        if (str_contains($nombre, 'rodilla') || str_contains($nombre, 'óseo') || str_contains($nombre, 'oseo') || str_contains($nombre, 'musculo') || str_contains($nombre, 'columna') || str_contains($nombre, 'hueso') || str_contains($nombre, 'articular') || str_contains($nombre, 'trauma')) {
            return 'musculoesqueletico';
        }
        if (str_contains($nombre, 'digestiv') || str_contains($nombre, 'endoscop') || str_contains($nombre, 'colon') || str_contains($nombre, 'gástric') || str_contains($nombre, 'gastric')) {
            return 'digestivo';
        }
        if (str_contains($nombre, 'ojo') || str_contains($nombre, 'visual') || str_contains($nombre, 'fondo')) {
            return 'ojos';
        }
        if (str_contains($nombre, 'piel') || str_contains($nombre, 'dermat')) {
            return 'piel';
        }
        if (str_contains($nombre, 'psicol') || str_contains($nombre, 'mental')) {
            return 'salud_mental';
        }

        return 'general';
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

    /**
     * Determina si el estudio tiene imágenes médicas almacenadas en el servidor PACS (dcm4chee)
     */
    public function tieneDicom(): bool
    {
        return ! empty($this->study_instance_uid);
    }

    /**
     * URL para visualizar el estudio en el visor DICOM web (OHIF Viewer o dcm4chee ui2)
     */
    public function dicomViewerUrl(): ?string
    {
        if (! $this->tieneDicom()) {
            return null;
        }

        $viewerUrl = config('pacs.viewer_url', 'http://localhost:8080/dcm4chee-arc/ui2');

        // Formato para visor dcm4chee ui2 o visor integrado OHIF
        return rtrim($viewerUrl, '/') . '/#/study/' . $this->study_instance_uid;
    }

    /**
     * URL para consultar las instancias DICOM vía DICOMweb WADO-RS
     */
    public function dicomWadoUrl(): ?string
    {
        if (! $this->tieneDicom()) {
            return null;
        }

        $dicomwebUrl = config('pacs.dicomweb_url', 'http://localhost:8080/dcm4chee-arc/aets/DCM4CHEE/rs');

        return rtrim($dicomwebUrl, '/') . '/studies/' . $this->study_instance_uid;
    }
}
