<?php

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use App\Models\Estudio;
use App\Services\HistorialPacienteService;
use Illuminate\View\View;

class PacientePortalController extends Controller
{
    public function __construct(
        private readonly HistorialPacienteService $historialService
    ) {}

    /**
     * Muestra el Documento de Entrega Oficial del Informe:
     * - Semáforo del proceso del paciente con explicación estandarizada automática
     * - Historial evolutivo del paciente (Timeline)
     * - Informe técnico oficial firmado digitalmente por el especialista
     * - Formato optimizado para visualización y descarga / impresión directa en PDF
     */
    public function verInforme(int $id): View
    {
        $estudio = Estudio::with(['tipoEstudio.especialidad', 'medico', 'tecnico', 'archivos'])
            ->findOrFail($id);

        $lineaTiempo = $this->historialService->obtenerLineaDeTiempo($estudio->paciente_dni, $estudio->id);

        return view('portal.informe-paciente', compact('estudio', 'lineaTiempo'));
    }
}
