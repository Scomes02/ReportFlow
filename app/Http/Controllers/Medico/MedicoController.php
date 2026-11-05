<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use App\Models\Estudio;
use App\Services\Dcm4cheeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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
        private readonly Dcm4cheeService $pacsService,
        private readonly \App\Services\EnsambladoClinicoService $ensambladoService,
        private readonly \App\Services\HistorialPacienteService $historialService,
        private readonly \App\Services\CruceMultidisciplinarioService $cruceService,
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

        // Mapear cantidad de estudios previos por paciente para el badge del worklist
        $dnis = $estudios->pluck('paciente_dni')->unique()->filter();
        $conteoPreviosPorDni = Estudio::whereIn('paciente_dni', $dnis)
            ->where('estado', '!=', 'nuevo')
            ->select('paciente_dni', DB::raw('count(*) as total'))
            ->groupBy('paciente_dni')
            ->pluck('total', 'paciente_dni')
            ->all();

        $pacsOnline = $this->pacsService->verificarConexion();

        return view('medico.index', compact('estudios', 'pacsOnline', 'conteoPreviosPorDni'));
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

        // Historial longitudinal y cruces multidisciplinarios
        $lineaTiempo = $this->historialService->obtenerLineaDeTiempo($estudio->paciente_dni, $estudio->id);
        $estudioPrevio = $this->historialService->obtenerEstudioPrevioComparativo($estudio);
        $cruceMultidisciplinario = $this->cruceService->analizarCruces($estudio);
        $cruceResultados = $cruceMultidisciplinario;

        // Ensamblado clínico de imágenes y evaluación del estado general
        $mosaicoClinico = $this->ensambladoService->obtenerMosaicoYEstadoGeneral($estudio);

        return view('medico.informar', compact(
            'estudio',
            'pacsInfo',
            'pacsSeries',
            'viewerUrl',
            'ohifUrl',
            'mosaicoClinico',
            'lineaTiempo',
            'estudioPrevio',
            'cruceMultidisciplinario',
            'cruceResultados'
        ));
    }

    /**
     * 4. GUARDAR INFORME FIRMADO
     */
    public function guardarInforme(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'informe' => ['required', 'string', 'min:3'],
            'semaforo_salud' => ['nullable', 'string', 'in:favorable,estable,control_requerido,atencion_inmediata,verde,amarillo,naranja,rojo'],
            'semaforo_evolucion' => ['nullable', 'string'],
            'resumen_evolucion' => ['nullable', 'string', 'max:500'],
            'informe_paciente' => ['nullable', 'string'],
        ]);

        $estudio = Estudio::findOrFail($id);

        $estudio->informe = $request->informe;

        // Mapeo semáforo de evolución del paciente
        $semaforoInput = $request->input('semaforo_salud') ?? $request->input('semaforo_evolucion', 'favorable');
        $mapaSemaforoSalud = [
            'verde' => 'favorable',
            'favorable' => 'favorable',
            'azul' => 'estable',
            'estable' => 'estable',
            'amarillo' => 'control_requerido',
            'naranja' => 'control_requerido',
            'control_requerido' => 'control_requerido',
            'rojo' => 'atencion_inmediata',
            'atencion_inmediata' => 'atencion_inmediata',
        ];
        $valorSalud = $mapaSemaforoSalud[$semaforoInput] ?? 'favorable';

        if (\Illuminate\Support\Facades\Schema::hasColumn('estudios', 'semaforo_salud')) {
            $estudio->semaforo_salud = $valorSalud;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('estudios', 'semaforo_evolucion')) {
            $mapaEvolucion = [
                'favorable' => 'verde',
                'estable' => 'azul',
                'control_requerido' => 'amarillo',
                'atencion_inmediata' => 'rojo',
            ];
            $estudio->semaforo_evolucion = $mapaEvolucion[$valorSalud] ?? $semaforoInput;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('estudios', 'resumen_evolucion')) {
            $estudio->resumen_evolucion = $request->input('resumen_evolucion');
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('estudios', 'informe_paciente')) {
            $estudio->informe_paciente = $request->input('informe_paciente');
        }

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

    /**
     * 9. DICTADO POR VOZ MÉDICO CON WHISPER LOCAL
     */
    public function transcribirDictado(Request $request): JsonResponse
    {
        $request->validate([
            'audio' => 'required|file',
            'prompt' => 'nullable|string|max:500',
        ]);

        $audioFile = $request->file('audio');
        $whisperUrl = config('services.whisper.url', 'http://127.0.0.1:8000');

        try {
            $response = Http::timeout(90)
                ->attach(
                    'audio',
                    file_get_contents($audioFile->getRealPath()),
                    $audioFile->getClientOriginalName() ?: 'dictado.webm'
                )
                ->post("{$whisperUrl}/transcribir", [
                    'prompt_personalizado' => $request->input('prompt')
                ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'exito' => false,
                'error' => 'Error en el microservicio local de transcripción Whisper.',
                'detalle' => $response->body()
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'error' => 'El servicio local de Whisper no responde en ' . $whisperUrl . '. Verifique que el servicio esté activo.',
                'detalle' => $e->getMessage()
            ], 503);
        }
    }

    /**
     * Estado del servicio Whisper local
     */
    public function estadoDictado(): JsonResponse
    {
        $whisperUrl = config('services.whisper.url', 'http://127.0.0.1:8000');

        try {
            $response = Http::timeout(2)->get("{$whisperUrl}/salud");
            if ($response->successful()) {
                return response()->json(['activo' => true, 'datos' => $response->json()]);
            }
        } catch (\Exception $e) {
            // Silencioso
        }

        return response()->json(['activo' => false]);
    }
}