<?php

namespace App\Http\Controllers\Tecnico;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tecnico\CrearEstudioRequest;
use App\Models\Especialidad;
use App\Models\Estudio;
use App\Services\AlmacenamientoEstudioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EstudioController extends Controller
{
    public function __construct(
        private readonly AlmacenamientoEstudioService $almacenamiento,
    ) {}

    public function index(): View
    {
        $estudios = Estudio::query()
            ->where('tecnico_id', auth()->id())
            ->with(['tipoEstudio'])
            ->latest('fecha_estudio')
            ->paginate(15);

        return view('tecnico.index', [
            'estudios' => $estudios,
            'especialidades' => Especialidad::with('tiposEstudio')->get(),
        ]);
    }

    public function store(CrearEstudioRequest $request): RedirectResponse
    {
        $datos = $request->safe()->except('archivos');
        $datos['tecnico_id'] = auth()->id();

        $this->almacenamiento->crear($datos, $request->file('archivos'));

        return redirect()
            ->route('tecnico.estudios.index')
            ->with('status', 'Estudio cargado correctamente y derivado a la especialidad correspondiente.');
    }
}