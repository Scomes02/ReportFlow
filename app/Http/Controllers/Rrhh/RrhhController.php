<?php

namespace App\Http\Controllers\Rrhh;

use App\Http\Controllers\Controller;
use App\Models\Estudio;
use App\Models\Especialidad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

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
     * Informes de un médico específico
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
     * Archivo general de informes
     */
    public function archivoGeneral(): View
    {
        $meses = Estudio::where('estado', 'informado')
            ->selectRaw('DISTINCT DATE_FORMAT(firmado_at, "%m/%Y") as mes')
            ->orderBy('mes', 'desc')
            ->pluck('mes')
            ->toArray();

        $informes = Estudio::where('estado', 'informado')
            ->with(['tipoEstudio', 'tipoEstudio.especialidad', 'medico'])
            ->latest('firmado_at')
            ->paginate(20);

        return view('rrhh.archivo', compact('informes', 'meses'));
    }

    /**
     * Informes de un mes específico
     */
    public function archivoMes(string $mes): View
    {
        $meses = Estudio::where('estado', 'informado')
            ->selectRaw('DISTINCT DATE_FORMAT(firmado_at, "%m/%Y") as mes')
            ->orderBy('mes', 'desc')
            ->pluck('mes')
            ->toArray();

        $informes = Estudio::where('estado', 'informado')
            ->whereRaw('DATE_FORMAT(firmado_at, "%m/%Y") = ?', [$mes])
            ->with(['tipoEstudio', 'tipoEstudio.especialidad', 'medico'])
            ->latest('firmado_at')
            ->paginate(20);

        return view('rrhh.archivo-mes', compact('informes', 'meses', 'mes'));
    }
    /**
     * Detalle de liquidación de un médico
     */
    public function detalleLiquidacion($medicoId)
    {
        // Buscar el médico
        $medico = User::where('id', $medicoId)
            ->where('role', 'medico')
            ->firstOrFail();

        // Obtener la especialidad del médico (de sus estudios)
        $especialidadSlug = null;  // ✅ Variable correcta
        $primerEstudio = Estudio::where('medico_id', $medico->id)
            ->where('estado', 'informado')
            ->with('tipoEstudio.especialidad')
            ->first();

        if ($primerEstudio && $primerEstudio->tipoEstudio && $primerEstudio->tipoEstudio->especialidad) {
            $especialidadSlug = $primerEstudio->tipoEstudio->especialidad->slug;  // ✅ Variable correcta
        }

        // Obtener todos los estudios informados del médico
        $estudios = Estudio::where('medico_id', $medico->id)
            ->where('estado', 'informado')
            ->with(['tipoEstudio', 'tipoEstudio.especialidad'])
            ->orderBy('firmado_at', 'desc')
            ->get();

        // Estadísticas de liquidación
        $totalEstudios = $estudios->count();

        // Agrupar por mes
        $estudiosPorMes = $estudios->groupBy(function ($estudio) {
            return $estudio->firmado_at ? $estudio->firmado_at->format('m/Y') : 'Sin fecha';
        });

        // Calcular honorarios (ejemplo: $50 por estudio)
        $honorarioPorEstudio = 50;
        $totalHonorarios = $totalEstudios * $honorarioPorEstudio;

        // Últimos 6 meses de actividad
        $mesesActividad = $estudiosPorMes->keys()->take(6)->toArray();

        return view('rrhh.detalle-liquidacion', compact(
            'medico',
            'estudios',
            'totalEstudios',
            'estudiosPorMes',
            'totalHonorarios',
            'honorarioPorEstudio',
            'mesesActividad',
            'especialidadSlug'  
        ));
    }
}
