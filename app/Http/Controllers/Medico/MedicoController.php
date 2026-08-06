<?php

namespace App\Http\Controllers\Medico;  // ← Cambiar a Medico

use App\Http\Controllers\Controller;   // ← Importar Controller base
use Illuminate\Http\Request;

class MedicoController extends Controller
{
    public function index()
    {
        // Datos de prueba
        $estudios = [
            (object)[
                'id' => 'ECG-2026-06-001',
                'paciente' => 'Ana Rodriguez',
                'edad' => 38,
                'tipo_estudio' => 'Electrocardiograma',
                'fecha' => '23/06/2026 - 14:00',
                'tecnico' => 'Luis Pérez',
                'estado' => 'Nuevo'
            ],
            (object)[
                'id' => 'HLT-2026-06-002',
                'paciente' => 'Juan Gómez',
                'edad' => 45,
                'tipo_estudio' => 'Holter 24 hs',
                'fecha' => '26/06/2026 - 10:30',
                'tecnico' => 'Luis Pérez',
                'estado' => 'Finalizado'
            ],
        ];

        return view('medico.index', compact('estudios'));
    }

    public function guardarInforme(Request $request, $id)
    {
        return redirect()->back()->with('success', '✅ Estudio informado y firmado con éxito.');
    }
}
