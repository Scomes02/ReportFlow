<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use App\Models\Estudio;
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

    /**
     * 1. WORKLIST: Muestra los estudios pendientes de informar de la especialidad del médico
     */
    public function index(): View
    {
        $medico = auth()->user()->load('especialidad');

        $estudios = Estudio::with(['tipoEstudio.especialidad', 'tecnico', 'archivos'])
            ->where('estado', 'nuevo')
            ->when($medico->especialidad_id, function ($query, $especialidadId) {
                $query->whereHas('tipoEstudio', function ($q) use ($especialidadId) {
                    $q->where('especialidad_id', $especialidadId);
                });
            }, function ($query) {
                // Si el médico aún no tiene especialidad asignada, no se le muestran estudios
                $query->whereRaw('1 = 0');
            })
            ->orderBy('fecha_estudio', 'desc')
            ->get();

        return view('medico.index', compact('estudios', 'medico'));
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
     * 3. PANTALLA DEDICADA PARA INFORMAR ESTUDIO (SPLIT-SCREEN)
     * Visor integrado a la izquierda y formulario de informe médico a la derecha.
     */
    public function informar(int $id): View|RedirectResponse
    {
        $medico = auth()->user()->load('especialidad');
        $estudio = Estudio::with(['tipoEstudio.especialidad', 'tecnico', 'archivos'])->findOrFail($id);

        if ($estudio->estado === \App\Enums\EstadoEstudio::Informado) {
            return redirect()
                ->route('medico.mis-informes')
                ->with('status', 'Este estudio ya ha sido informado y firmado.');
        }

        if ($medico->especialidad_id && $estudio->tipoEstudio?->especialidad_id !== $medico->especialidad_id) {
            return redirect()
                ->route('medico.estudios.index')
                ->with('error', 'No tiene permisos para acceder a un estudio de otra especialidad.');
        }

        return view('medico.informar', compact('estudio', 'medico'));
    }

    /**
     * 4. GUARDAR INFORME FIRMADO
     */
    public function guardarInforme(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'informe' => ['required', 'string', 'min:3'],
        ]);

        $medico = auth()->user();
        $estudio = Estudio::with('tipoEstudio')->findOrFail($id);

        if ($medico->especialidad_id && $estudio->tipoEstudio?->especialidad_id !== $medico->especialidad_id) {
            return redirect()
                ->route('medico.estudios.index')
                ->with('error', 'No tiene permisos para informar un estudio de otra especialidad.');
        }

        $estudio->informe = $request->informe;
        $estudio->estado = 'informado';
        $estudio->medico_id = $medico->id;
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

        $medico = auth()->user();
        $estudio = Estudio::with('tipoEstudio')->findOrFail($id);

        if ($medico->especialidad_id && $estudio->tipoEstudio?->especialidad_id !== $medico->especialidad_id) {
            return redirect()
                ->route('medico.estudios.index')
                ->with('error', 'No tiene permisos para rechazar un estudio de otra especialidad.');
        }

        $estudio->estado = 'rechazado';
        $estudio->motivo_rechazo = $request->motivo_rechazo;
        $estudio->medico_id = $medico->id; // queda registrado quién lo rechazó
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
     * Permite al médico ver o descargar de forma segura el archivo adjunto (PDF, imagen, etc.)
     */
    public function verArchivo(int $id, int $archivoId)
    {
        $medico = auth()->user();
        $estudio = Estudio::with('tipoEstudio')->findOrFail($id);

        if ($medico->especialidad_id && $estudio->tipoEstudio?->especialidad_id !== $medico->especialidad_id) {
            abort(403, 'No tiene permisos para acceder a los archivos de este estudio.');
        }

        $archivo = $estudio->archivos()->findOrFail($archivoId);

        if (! Storage::disk($archivo->disco)->exists($archivo->path)) {
            abort(404, 'El archivo físico no se encuentra disponible en el almacenamiento.');
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