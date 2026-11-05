<?php

namespace App\Http\Controllers\Rrhh;

use App\Http\Controllers\Controller;
use App\Models\Especialidad;
use App\Models\Estudio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RrhhController extends Controller
{
    /**
     * Dashboard principal de RRHH
     */
    public function index(): View
    {
        // Estadísticas generales
        $totalEstudios = Estudio::count();
        $estudiosInformados = Estudio::where('estado', 'informado')->count();
        $estudiosPendientes = Estudio::where('estado', 'nuevo')->count();
        $estudiosRechazados = Estudio::where('estado', 'rechazado')->count();

        // Especialidades con conteo usando consulta manual
        $especialidades = Especialidad::all();
        foreach ($especialidades as $esp) {
            // Contar estudios informados de esta especialidad
            $esp->estudios_informados_count = Estudio::whereHas('tipoEstudio', function ($query) use ($esp) {
                $query->where('especialidad_id', $esp->id);
            })->where('estado', 'informado')->count();
        }

        // Médicos con conteos de informes
        $medicos = User::where('role', 'medico')
            ->withCount(['estudiosComoMedico' => function ($query) {
                $query->where('estado', 'informado');
            }])
            ->get();

        return view('rrhh.dashboard', compact(
            'totalEstudios',
            'estudiosInformados',
            'estudiosPendientes',
            'estudiosRechazados',
            'especialidades',
            'medicos'
        ));
    }

    /**
     * Vista de especialidades
     */
    public function especialidades(): View
    {
        // Especialidades con conteo manual
        $especialidades = Especialidad::all();
        foreach ($especialidades as $esp) {
            $esp->estudios_informados_count = Estudio::whereHas('tipoEstudio', function ($query) use ($esp) {
                $query->where('especialidad_id', $esp->id);
            })->where('estado', 'informado')->count();
        }

        return view('rrhh.especialidades', compact('especialidades'));
    }

    /**
     * Lista de médicos por especialidad
     */
    public function medicos(string $especialidad): View
    {
        $especialidad = Especialidad::where('slug', $especialidad)->firstOrFail();

        // Obtener IDs de tipos de estudio de esta especialidad
        $tiposEstudioIds = $especialidad->tiposEstudio->pluck('id')->toArray();

        // Encontrar médicos que hayan firmado estudios de esta especialidad
        $medicosIds = Estudio::whereIn('tipo_estudio_id', $tiposEstudioIds)
            ->where('estado', 'informado')
            ->whereNotNull('medico_id')
            ->distinct()
            ->pluck('medico_id')
            ->toArray();

        // Obtener los médicos con sus conteos específicos para esta especialidad
        $medicos = User::where('role', 'medico')
            ->whereIn('id', $medicosIds)
            ->withCount(['estudiosComoMedico' => function ($query) use ($tiposEstudioIds) {
                $query->where('estado', 'informado')
                    ->whereIn('tipo_estudio_id', $tiposEstudioIds);
            }])
            ->get();

        return view('rrhh.medicos', compact('especialidad', 'medicos'));
    }

    /**
     * Informes de un médico específico (listado paginado, por nombre)
     */
    public function informesMedico(string $nombre): View
    {
        $medico = User::where('name', $nombre)
            ->where('role', 'medico')
            ->firstOrFail();

        $informes = Estudio::with(['tipoEstudio', 'tipoEstudio.especialidad'])
            ->where('medico_id', $medico->id)
            ->where('estado', 'informado')
            ->latest('firmado_at')
            ->paginate(15);

        return view('rrhh.informes-medico', compact('medico', 'informes'));
    }

    /**
     * Resumen de un médico: cuántos informes firmó en total y por mes.
     * No maneja ningún monto ni arancel -RRHH acá solo audita volumen
     * de trabajo, la liquidación de honorarios no forma parte del sistema.
     */
    public function resumenInformesMedico(int $medicoId): View
    {
        $medico = User::where('id', $medicoId)
            ->where('role', 'medico')
            ->firstOrFail();

        // Especialidad del médico, inferida de sus propios estudios informados
        $especialidadSlug = null;
        $primerEstudio = Estudio::where('medico_id', $medico->id)
            ->where('estado', 'informado')
            ->with('tipoEstudio.especialidad')
            ->first();

        if ($primerEstudio?->tipoEstudio?->especialidad) {
            $especialidadSlug = $primerEstudio->tipoEstudio->especialidad->slug;
        }

        $estudios = Estudio::where('medico_id', $medico->id)
            ->where('estado', 'informado')
            ->with(['tipoEstudio', 'tipoEstudio.especialidad'])
            ->orderBy('firmado_at', 'desc')
            ->get();

        $totalInformes = $estudios->count();

        $informesPorMes = $estudios->groupBy(function (Estudio $estudio) {
            return $estudio->firmado_at ? $estudio->firmado_at->format('m/Y') : 'Sin fecha';
        });

        $mesesActividad = $informesPorMes->keys()->take(6)->toArray();

        return view('rrhh.resumen-informes-medico', compact(
            'medico',
            'estudios',
            'totalInformes',
            'informesPorMes',
            'mesesActividad',
            'especialidadSlug'
        ));
    }

    /**
     * Archivo general de informes filtrados por especialidad y fecha de realización del estudio
     */
    public function archivoGeneral(Request $request): View
    {
        $especialidades = Especialidad::orderBy('nombre')->get();

        $especialidadId = $request->query('especialidad_id');
        $fechaDesde = $request->query('fecha_desde');
        $fechaHasta = $request->query('fecha_hasta');

        $informes = Estudio::where('estado', 'informado')
            ->with(['tipoEstudio.especialidad', 'medico'])
            ->when($especialidadId, function ($query, $id) {
                $query->whereHas('tipoEstudio', function ($subQuery) use ($id) {
                    $subQuery->where('especialidad_id', $id);
                });
            })
            ->when($fechaDesde, function ($query, $desde) {
                $query->whereDate('fecha_estudio', '>=', $desde);
            })
            ->when($fechaHasta, function ($query, $hasta) {
                $query->whereDate('fecha_estudio', '<=', $hasta);
            })
            ->orderByDesc('fecha_estudio')
            ->paginate(20)
            ->withQueryString();

        return view('rrhh.archivo', compact(
            'informes',
            'especialidades',
            'especialidadId',
            'fechaDesde',
            'fechaHasta'
        ));
    }

    /**
     * Informes de un mes específico de realización del estudio, con filtro opcional por especialidad
     */
    public function archivoMes(Request $request, string $mes): View
    {
        $expresionMes = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', fecha_estudio)"
            : "DATE_FORMAT(fecha_estudio, '%Y-%m')";

        $meses = Estudio::where('estado', 'informado')
            ->selectRaw("DISTINCT {$expresionMes} as mes")
            ->orderBy('mes', 'desc')
            ->pluck('mes')
            ->filter()
            ->toArray();

        $especialidades = Especialidad::orderBy('nombre')->get();
        $especialidadId = $request->query('especialidad_id');

        $informes = Estudio::where('estado', 'informado')
            ->whereRaw("{$expresionMes} = ?", [$mes])
            ->with(['tipoEstudio.especialidad', 'medico'])
            ->when($especialidadId, function ($query, $id) {
                $query->whereHas('tipoEstudio', function ($subQuery) use ($id) {
                    $subQuery->where('especialidad_id', $id);
                });
            })
            ->orderByDesc('fecha_estudio')
            ->paginate(20)
            ->withQueryString();

        return view('rrhh.archivo-mes', compact(
            'informes',
            'meses',
            'mes',
            'especialidades',
            'especialidadId'
        ));
    }
}