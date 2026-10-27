<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Servicio de interoperabilidad con el servidor PACS dcm4chee-arc-light.
 * Implementa los estándares DICOMweb (QIDO-RS, WADO-RS, STOW-RS).
 */
class Dcm4cheeService
{
    private string $baseUrl;
    private string $dicomwebUrl;
    private string $viewerUrl;
    private string $ohifViewerUrl;
    private string $aet;
    private int $timeout;
    private bool $enabled;

    public function __construct()
    {
        $this->enabled = (bool) config('pacs.enabled', true);
        $this->baseUrl = (string) config('pacs.base_url', 'http://localhost:8080/dcm4chee-arc');
        $this->dicomwebUrl = (string) config('pacs.dicomweb_url', 'http://localhost:8080/dcm4chee-arc/aets/DCM4CHEE/rs');
        $this->viewerUrl = (string) config('pacs.viewer_url', 'http://localhost:8080/dcm4chee-arc/ui2');
        $this->ohifViewerUrl = (string) config('pacs.ohif_viewer_url', 'http://localhost:3000/viewer');
        $this->aet = (string) config('pacs.aet', 'DCM4CHEE');
        $this->timeout = (int) config('pacs.timeout', 10);
    }

    /**
     * Verifica si el módulo PACS está habilitado en la configuración.
     */
    public function estaHabilitado(): bool
    {
        return $this->enabled;
    }

    /**
     * Comprueba si el servidor dcm4chee-arc-light está online y responde peticiones.
     */
    public function verificarConexion(): bool
    {
        if (! $this->enabled) {
            return false;
        }

        try {
            // Consulta de eco básico a la raíz de dcm4chee o endpoint DICOMweb
            $response = Http::timeout($this->timeout)
                ->accept('application/json')
                ->get(rtrim($this->baseUrl, '/') . '/ctrl/status');

            if ($response->successful()) {
                return true;
            }

            // Fallback: intentar listar estudios con límite 1
            $qido = Http::timeout($this->timeout)
                ->accept('application/json')
                ->get(rtrim($this->dicomwebUrl, '/') . '/studies', ['limit' => 1]);

            return $qido->successful();
        } catch (Exception $e) {
            Log::warning('dcm4chee-arc-light no responde en ' . $this->baseUrl . ': ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Consulta información y metadatos de un estudio en el PACS por su StudyInstanceUID (QIDO-RS).
     *
     * @return array<string, mixed>|null
     */
    public function consultarEstudio(string $studyInstanceUID): ?array
    {
        if (! $this->enabled || empty($studyInstanceUID)) {
            return null;
        }

        try {
            $url = rtrim($this->dicomwebUrl, '/') . '/studies';
            $response = Http::timeout($this->timeout)
                ->accept('application/json')
                ->get($url, ['StudyInstanceUID' => $studyInstanceUID]);

            if (! $response->successful()) {
                return null;
            }

            $datos = $response->json();
            if (empty($datos) || ! is_array($datos)) {
                return null;
            }

            $primerEstudio = $datos[0] ?? [];

            return [
                'study_instance_uid' => $studyInstanceUID,
                'patient_id' => $this->extraerValorTag($primerEstudio, '00100020'), // DNI / ID
                'patient_name' => $this->extraerValorTag($primerEstudio, '00100010'), // Nombre
                'modality' => $this->extraerValorTag($primerEstudio, '00080061') ?? $this->extraerValorTag($primerEstudio, '00080060'), // Modalidad
                'study_date' => $this->extraerValorTag($primerEstudio, '00080020'), // Fecha
                'study_description' => $this->extraerValorTag($primerEstudio, '00081030'), // Descripción
                'number_of_series' => (int) ($this->extraerValorTag($primerEstudio, '00201206') ?? 1),
                'number_of_instances' => (int) ($this->extraerValorTag($primerEstudio, '00201208') ?? 1),
            ];
        } catch (Exception $e) {
            Log::error('Error al consultar estudio en dcm4chee (' . $studyInstanceUID . '): ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Consulta las series de un estudio en el PACS (QIDO-RS).
     *
     * @return array<int, array<string, mixed>>
     */
    public function consultarSeries(string $studyInstanceUID): array
    {
        if (! $this->enabled || empty($studyInstanceUID)) {
            return [];
        }

        try {
            $url = rtrim($this->dicomwebUrl, '/') . '/studies/' . $studyInstanceUID . '/series';
            $response = Http::timeout($this->timeout)
                ->accept('application/json')
                ->get($url);

            if (! $response->successful()) {
                return [];
            }

            $series = $response->json();
            if (! is_array($series)) {
                return [];
            }

            $resultado = [];
            foreach ($series as $serie) {
                $resultado[] = [
                    'series_instance_uid' => $this->extraerValorTag($serie, '0020000E'),
                    'modality' => $this->extraerValorTag($serie, '00080060'),
                    'series_number' => $this->extraerValorTag($serie, '00200011'),
                    'series_description' => $this->extraerValorTag($serie, '0008103E'),
                    'number_of_instances' => (int) ($this->extraerValorTag($serie, '00201209') ?? 1),
                ];
            }

            return $resultado;
        } catch (Exception $e) {
            Log::error('Error al consultar series en dcm4chee: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Busca estudios en dcm4chee asociados a un DNI / PatientID de paciente.
     *
     * @return array<int, array<string, mixed>>
     */
    public function buscarEstudiosPorPaciente(string $patientDni): array
    {
        if (! $this->enabled || empty($patientDni)) {
            return [];
        }

        try {
            $url = rtrim($this->dicomwebUrl, '/') . '/studies';
            $response = Http::timeout($this->timeout)
                ->accept('application/json')
                ->get($url, ['PatientID' => $patientDni]);

            if (! $response->successful()) {
                return [];
            }

            $datos = $response->json();
            if (! is_array($datos)) {
                return [];
            }

            $estudios = [];
            foreach ($datos as $item) {
                $uid = $this->extraerValorTag($item, '0020000D');
                if ($uid) {
                    $estudios[] = [
                        'study_instance_uid' => $uid,
                        'patient_id' => $this->extraerValorTag($item, '00100020'),
                        'patient_name' => $this->extraerValorTag($item, '00100010'),
                        'modality' => $this->extraerValorTag($item, '00080061'),
                        'study_date' => $this->extraerValorTag($item, '00080020'),
                        'study_description' => $this->extraerValorTag($item, '00081030'),
                    ];
                }
            }

            return $estudios;
        } catch (Exception $e) {
            Log::error('Error buscando estudios por paciente en dcm4chee: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Obtiene la URL directa al visor web integrado de dcm4chee (ui2).
     */
    public function obtenerUrlVisor(string $studyInstanceUID): string
    {
        return rtrim($this->viewerUrl, '/') . '/#/study/' . urlencode($studyInstanceUID);
    }

    /**
     * Obtiene la URL para abrir el estudio en OHIF Viewer.
     */
    public function obtenerUrlOhif(string $studyInstanceUID): string
    {
        return rtrim($this->ohifViewerUrl, '/') . '?StudyInstanceUIDs=' . urlencode($studyInstanceUID);
    }

    /**
     * URL para solicitar una imagen renderizada vía WADO-RS.
     */
    public function obtenerUrlWadoRendered(string $studyUID, string $seriesUID, string $sopUID, int $frame = 1): string
    {
        return rtrim($this->dicomwebUrl, '/') . "/studies/{$studyUID}/series/{$seriesUID}/instances/{$sopUID}/frames/{$frame}/rendered";
    }

    /**
     * Auxiliar para extraer el valor de un tag DICOM en la estructura JSON estándar de DICOMweb.
     */
    private function extraerValorTag(array $dicomJson, string $tag): ?string
    {
        if (! isset($dicomJson[$tag])) {
            return null;
        }

        $tagObj = $dicomJson[$tag];

        if (isset($tagObj['Value']) && is_array($tagObj['Value']) && count($tagObj['Value']) > 0) {
            $val = $tagObj['Value'][0];
            if (is_array($val) && isset($val['Alphabetic'])) {
                return (string) $val['Alphabetic'];
            }

            return (string) $val;
        }

        return null;
    }
}
