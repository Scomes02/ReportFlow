<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use App\Models\Estudio;
use App\Services\Dcm4cheeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MedicoController extends Controller
{
    /**
     * Cuántas horas, desde que se firma un informe, sigue siendo editable
     * directamente. Pasado ese límite, solo se puede agregar una adenda.
     */
    private const HORAS_LIMITE_EDICION = 24;

    public function __construct(
        private readonly Dcm4cheeService $pacsService
    ) {}

    /**
     * 1. WORKLIST: Muestra los estudios pendientes de informar
     */
    public function index(): View
    {
        $estudios = Estudio::with(['tipoEstudio', 'tecnico', 'archivos'])
            ->where('estado', 'nuevo')
            ->orderBy('fecha_estudio', 'desc')
            ->get();

        $pacsOnline = $this->pacsService->verificarConexion();

        return view('medico.index', compact('estudios', 'pacsOnline'));
    }

    /**
     * 2. MIS INFORMES: Muestra los estudios que este médico ya firmó,
     * junto con las adendas que ya tengan cargadas (para poder mostrarlas
     * en la vista sin disparar una consulta extra por cada fila).
     */
    public function misInformes(): View
    {
        $medico = auth()->user();

        $estudios = Estudio::with(['tipoEstudio', 'adendas' => function ($query) {
            $query->latest();
        }])
            ->where('medico_id', $medico->id)
            ->where('estado', 'informado')
            ->orderBy('firmado_at', 'desc')
            ->get();

        return view('medico.mis-informes', compact('estudios'));
    }

    /**
     * 3. PANTALLA DEDICADA DE INFORME CON VISOR DICOM / MULTIMEDIA
     */
    public function informar(int $id): View|RedirectResponse
    {
        $estudio = Estudio::with(['tipoEstudio', 'tecnico', 'archivos'])->findOrFail($id);

        if ($estudio->estado === \App\Enums\EstadoEstudio::Informado) {
            return redirect()
                ->route('medico.mis-informes')
                ->with('status', 'Este estudio ya ha sido informado y firmado.');
        }

        $pacsInfo = null;
        $pacsSeries = [];
        $viewerUrl = null;
        $ohifUrl = null;

        if ($estudio->tieneDicom()) {
            $pacsInfo = $this->pacsService->consultarEstudio($estudio->study_instance_uid);
            $pacsSeries = $this->pacsService->consultarSeries($estudio->study_instance_uid);
            $viewerUrl = $this->pacsService->obtenerUrlVisor($estudio->study_instance_uid);
            $ohifUrl = $this->pacsService->obtenerUrlOhif($estudio->study_instance_uid);
        }

        return view('medico.informar', compact('estudio', 'pacsInfo', 'pacsSeries', 'viewerUrl', 'ohifUrl'));
    }

    /**
     * 4. GUARDAR INFORME FIRMADO
     */
    public function guardarInforme(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'informe' => ['required', 'string', 'min:3'],
        ]);

        $estudio = Estudio::findOrFail($id);

        $estudio->informe = $request->informe;
        $estudio->estado = 'informado';
        $estudio->medico_id = auth()->id();
        $estudio->firmado_at = now();
        $estudio->motivo_rechazo = null; // si venía de un rechazo previo, se limpia
        $estudio->save();

        return redirect()
            ->route('medico.estudios.index')
            ->with('success', '✅ Estudio informado y firmado con éxito.');
    }

    /**
     * 4. RECHAZAR ESTUDIO: Desde la Worklist, en vez de informar.
     * Devuelve el estudio al técnico con un comentario obligatorio
     * explicando qué hay que corregir.
     */
    public function rechazarEstudio(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'motivo_rechazo' => ['required', 'string', 'min:5'],
        ], [
            'motivo_rechazo.required' => 'Tenés que explicar el motivo del rechazo antes de guardar.',
            'motivo_rechazo.min' => 'El motivo es demasiado corto, agregá un poco más de detalle.',
        ]);

        $estudio = Estudio::findOrFail($id);

        $estudio->estado = 'rechazado';
        $estudio->motivo_rechazo = $request->motivo_rechazo;
        $estudio->medico_id = auth()->id(); // queda registrado quién lo rechazó
        $estudio->informe = null;
        $estudio->firmado_at = null;
        $estudio->save();

        return redirect()
            ->route('medico.estudios.index')
            ->with('success', '↩️ Estudio rechazado y devuelto al técnico con tu comentario.');
    }

    /**
     * 5. EDITAR INFORME: Solo si pasaron menos de 24hs (Desde Mis Informes).
     * La ventana de 24hs se valida acá también -no solo en la vista-
     * porque un usuario podría mandar el POST directo sin pasar por el
     * botón deshabilitado, y eso no puede depender únicamente del frontend.
     */
    public function updateInforme(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'informe' => ['required', 'string', 'min:3'],
        ]);

        // Buscamos el estudio asegurándonos de que pertenezca a este médico
        $estudio = Estudio::where('medico_id', auth()->id())->findOrFail($id);

        if (! $this->dentroDeVentanaDeEdicion($estudio)) {
            return redirect()
                ->route('medico.mis-informes')
                ->with('error', 'Ya pasaron más de 24hs desde la firma: este informe solo admite adendas.');
        }

        $estudio->informe = $request->informe;
        $estudio->save();

        return redirect()
            ->route('medico.mis-informes')
            ->with('success', '✏️ Informe modificado correctamente.');
    }

    /**
     * 6. AÑADIR ADENDA: Si ya pasaron más de 24hs (Desde Mis Informes)
     */
    public function guardarAdenda(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'contenido' => ['required', 'string', 'min:3'],
        ]);

        $estudio = Estudio::where('medico_id', auth()->id())->findOrFail($id);

        DB::table('adendas')->insert([
            'estudio_id' => $estudio->id,
            'medico_id' => auth()->id(),
            'contenido' => $request->contenido,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('medico.mis-informes')
            ->with('success', '📎 Adenda agregada al informe correctamente.');
    }

    /**
     * 7. VISUALIZAR ARCHIVO DEL ESTUDIO
     */
    public function verArchivo(int $id, int $archivoId)
    {
        $estudio = Estudio::findOrFail($id);
        $archivo = $estudio->archivos()->findOrFail($archivoId);

        if (! Storage::disk($archivo->disco)->exists($archivo->path)) {
            abort(404, 'El archivo físico no se encuentra disponible.');
        }

        return Storage::disk($archivo->disco)->response(
            $archivo->path,
            $archivo->nombre_original,
            [
                'Content-Disposition' => 'inline; filename="' . $archivo->nombre_original . '"',
                'Content-Type' => $archivo->mime_type ?? 'application/octet-stream',
            ]
        );
    }

    /**
     * 8. VISOR DICOM FULLSCREEN / EMBED PROXY
     */
    public function dicomViewer(int $id): View|RedirectResponse
    {
        $estudio = Estudio::findOrFail($id);

        if (! $estudio->tieneDicom()) {
            return redirect()->back()->with('error', 'El estudio no posee identificador DICOM (StudyInstanceUID).');
        }

        $viewerUrl = $this->pacsService->obtenerUrlVisor($estudio->study_instance_uid);
        $ohifUrl = $this->pacsService->obtenerUrlOhif($estudio->study_instance_uid);
        $pacsInfo = $this->pacsService->consultarEstudio($estudio->study_instance_uid);

        return view('medico.dicom-viewer', compact('estudio', 'viewerUrl', 'ohifUrl', 'pacsInfo'));
    }

    /**
     * Un informe es editable directamente mientras no hayan pasado
     * HORAS_LIMITE_EDICION desde que se firmó. Se centraliza acá para
     * que el Controller y (a futuro) cualquier otro lugar del código usen
     * exactamente la misma regla, en vez de recalcularla a mano en cada vista.
     */
    private function dentroDeVentanaDeEdicion(Estudio $estudio): bool
    {
        if (! $estudio->firmado_at) {
            return false;
        }

        return $estudio->firmado_at->diffInHours(now()) < self::HORAS_LIMITE_EDICION;
    }
}