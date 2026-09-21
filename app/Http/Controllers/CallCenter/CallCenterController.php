<?php

namespace App\Http\Controllers\CallCenter;

use App\Http\Controllers\Controller;
use App\Models\Estudio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CallCenterController extends Controller
{
    /**
     * Call Center solo puede ver informes YA CERRADOS (firmados por el
     * médico) -acá no importa qué estudios están "nuevos" o "rechazados",
     * porque todavía no hay nada que entregarle al paciente. Y no se le
     * muestra el texto del informe: eso es información clínica, no de
     * recepción/atención al público.
     */
    public function index(Request $request): View
    {
        $dni = trim((string) $request->query('dni', ''));

        $informes = Estudio::query()
            ->where('estado', 'informado')
            ->with(['tipoEstudio', 'medico'])
            ->when($dni !== '', fn ($query) => $query->where('paciente_dni', 'like', "%{$dni}%"))
            ->latest('firmado_at')
            ->paginate(15)
            ->withQueryString();

        return view('callcenter.informes', compact('informes', 'dni'));
    }

    /**
     * Simula el envío del aviso al paciente (mail/SMS/WhatsApp -no importa
     * el medio, acá no se conecta a ningún proveedor real). Solo confirma
     * que el estudio esté cerrado antes de "enviar" nada.
     */
    public function enviarAviso(Request $request, int $id): RedirectResponse
    {
        $estudio = Estudio::findOrFail($id);

        if (! $estudio->estaListoParaEntregar()) {
            return back()->with('error', 'Ese informe todavía no está firmado, no se le puede avisar al paciente.');
        }

        // Acá no se envía nada de verdad -es una simulación para la demo.
        return back()->with(
            'success',
            "📨 Aviso simulado enviado a {$estudio->paciente_nombre} ({$estudio->paciente_email}) sobre su estudio de " . ($estudio->tipoEstudio->nombre ?? 'estudio') . '.'
        );
    }
}