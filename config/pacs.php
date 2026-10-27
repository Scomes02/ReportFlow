<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de dcm4chee-arc-light (Servidor PACS DICOM)
    |--------------------------------------------------------------------------
    |
    | Define los parámetros de conexión con el archivo clínico dcm4chee
    | y los visores DICOMweb (QIDO-RS, WADO-RS, STOW-RS, OHIF Viewer).
    |
    */

    // Indica si la integración con el PACS está activa
    'enabled' => env('DCM4CHEE_ENABLED', true),

    // URL base de la instancia dcm4chee-arc-light
    'base_url' => env('DCM4CHEE_URL', 'http://localhost:8080/dcm4chee-arc'),

    // Application Entity Title (AET) configurado en el PACS
    'aet' => env('DCM4CHEE_AET', 'DCM4CHEE'),

    // Endpoint DICOMweb (QIDO-RS / WADO-RS)
    'dicomweb_url' => env('DCM4CHEE_DICOMWEB_URL', 'http://localhost:8080/dcm4chee-arc/aets/DCM4CHEE/rs'),

    // URL del visor nativo web (dcm4chee ui2)
    'viewer_url' => env('DCM4CHEE_VIEWER_URL', 'http://localhost:8080/dcm4chee-arc/ui2'),

    // URL de OHIF Viewer (si se usa una instancia independiente)
    'ohif_viewer_url' => env('OHIF_VIEWER_URL', 'http://localhost:3000/viewer'),

    // Timeout para peticiones HTTP al PACS (en segundos)
    'timeout' => (int) env('DCM4CHEE_TIMEOUT', 10),
];
