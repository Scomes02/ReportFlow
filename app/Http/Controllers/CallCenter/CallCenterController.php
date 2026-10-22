<?php

namespace App\Http\Controllers\CallCenter;

use App\Http\Controllers\Controller;
use App\Mail\AvisoInformeListo;
use App\Models\Estudio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

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
     * Envía al paciente un correo avisándole que su informe ya está listo
     * para retirar. El correo no incluye el contenido clínico del informe:
     * solo confirma que puede pasar a buscarlo.
     */
    public function enviarAviso(Request $request, int $id): RedirectResponse
    {
        $estudio = Estudio::findOrFail($id);

        if (! $estudio->estaListoParaEntregar()) {
            return back()->with('error', 'Ese informe todavía no está firmado, no se le puede avisar al paciente.');
        }

        $email = trim((string) $estudio->paciente_email);

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return back()->with('error', 'El paciente no tiene un email válido cargado. Actualizá el contacto antes de enviar el aviso.');
        }

        try {
            Mail::to($email)->send(new AvisoInformeListo($estudio));
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'No se pudo enviar el correo. Revisá la configuración de mail e intentá nuevamente.');
        }

        $estudio->forceFill([
            'aviso_enviado_at' => now(),
            'aviso_enviado_por' => $request->user()->id,
        ])->save();

        return back()->with(
            'success',
            "Aviso enviado por correo a {$estudio->paciente_nombre} ({$email})."
        );
    }
}
