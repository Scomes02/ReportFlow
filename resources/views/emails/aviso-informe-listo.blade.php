<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu informe médico está disponible</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: Arial, Helvetica, sans-serif; color: #374151;">
    @php
        $semaforo = $estudio->semaforo_salud;
        $semaforoEstilos = [
            'favorable' => ['fondo' => '#ecfdf5', 'borde' => '#a7f3d0', 'texto' => '#065f46'],
            'estable' => ['fondo' => '#f0f9ff', 'borde' => '#bae6fd', 'texto' => '#075985'],
            'control_requerido' => ['fondo' => '#fffbeb', 'borde' => '#fde68a', 'texto' => '#92400e'],
            'atencion_inmediata' => ['fondo' => '#fff1f2', 'borde' => '#fecdd3', 'texto' => '#9f1239'],
        ];
        $semaforoEstilo = $semaforo ? ($semaforoEstilos[$semaforo->value] ?? null) : null;
    @endphp

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f3f4f6; padding: 24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb;">
                    <tr>
                        <td style="background-color: #003764; color: #ffffff; padding: 24px 32px;">
                            <p style="margin: 0; font-size: 13px; letter-spacing: 1px; text-transform: uppercase; opacity: 0.8;">Hospital Universitario</p>
                            <h1 style="margin: 6px 0 0; font-size: 22px; line-height: 1.3;">Tu informe médico está disponible</h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 32px;">
                            <p style="margin: 0 0 16px; font-size: 15px; line-height: 1.6;">
                                Hola <strong>{{ $estudio->paciente_nombre }}</strong>:
                            </p>

                            <p style="margin: 0 0 24px; font-size: 15px; line-height: 1.6;">
                                El informe de tu estudio <strong>{{ $estudio->tipoEstudio->nombre ?? 'médico' }}</strong>
                                ya fue firmado por el profesional. Lo incluimos completo a continuación para que puedas
                                leerlo y descargarlo sin necesidad de concurrir al hospital.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px 20px; font-size: 14px; line-height: 1.8;">
                                        <div><span style="color: #6b7280;">Paciente:</span> {{ $estudio->paciente_nombre }}</div>
                                        <div><span style="color: #6b7280;">DNI:</span> {{ $estudio->paciente_dni }}</div>
                                        <div><span style="color: #6b7280;">Estudio:</span> {{ $estudio->tipoEstudio->nombre ?? 'N/A' }}</div>
                                        @if($estudio->tipoEstudio?->especialidad)
                                            <div><span style="color: #6b7280;">Especialidad:</span> {{ $estudio->tipoEstudio->especialidad->nombre }}</div>
                                        @endif
                                        <div><span style="color: #6b7280;">Fecha del estudio:</span> {{ $estudio->fecha_estudio?->format('d/m/Y') ?? 'N/A' }}</div>
                                        <div><span style="color: #6b7280;">Profesional:</span> {{ $estudio->medico->name ?? 'N/A' }}</div>
                                    </td>
                                </tr>
                            </table>

                            @if($semaforo && $semaforoEstilo)
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: {{ $semaforoEstilo['fondo'] }}; border: 1px solid {{ $semaforoEstilo['borde'] }}; border-radius: 8px; margin-bottom: 24px;">
                                    <tr>
                                        <td style="padding: 16px 20px;">
                                            <p style="margin: 0 0 6px; font-size: 13px; font-weight: bold; color: {{ $semaforoEstilo['texto'] }};">
                                                {{ $semaforo->icono() }} Estado general: {{ $semaforo->label() }}
                                            </p>
                                            <p style="margin: 0; font-size: 13px; line-height: 1.6; color: {{ $semaforoEstilo['texto'] }};">
                                                {{ $semaforo->explicacionPaciente() }}
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            <h2 style="margin: 0 0 12px; font-size: 16px; line-height: 1.4; color: #111827;">
                                Informe médico de diagnóstico
                            </h2>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border: 1px solid #d1d5db; border-radius: 8px; margin-bottom: 16px;">
                                <tr>
                                    <td style="padding: 20px; font-size: 14px; line-height: 1.7; color: #111827; background-color: #ffffff;">
                                        {!! nl2br(e($estudio->informe ?? 'Sin texto de informe cargado.')) !!}
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 24px; font-size: 13px; line-height: 1.7; color: #4b5563; text-align: right;">
                                <strong style="color: #111827;">{{ $estudio->medico->name ?? 'Profesional firmante' }}</strong><br>
                                Médico Especialista &bull; Hospital Universitario (UNCuyo)<br>
                                <span style="font-size: 12px; color: #6b7280;">
                                    Firmado digitalmente el {{ $estudio->firmado_at?->format('d/m/Y H:i') ?? $estudio->updated_at?->format('d/m/Y H:i') }} hs
                                </span>
                            </p>

                            @if(filled($estudio->informe_paciente))
                                <h2 style="margin: 0 0 12px; font-size: 16px; line-height: 1.4; color: #111827;">
                                    Explicación en lenguaje claro
                                </h2>

                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; margin-bottom: 24px;">
                                    <tr>
                                        <td style="padding: 20px; font-size: 14px; line-height: 1.7; color: #0c4a6e;">
                                            {!! nl2br(e($estudio->informe_paciente)) !!}
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            <table role="presentation" cellspacing="0" cellpadding="0" style="margin: 0 auto 16px;">
                                <tr>
                                    <td style="border-radius: 8px; background-color: #003764;">
                                        <a href="{{ route('paciente.informe', $estudio->id) }}"
                                           style="display: inline-block; padding: 12px 24px; font-size: 14px; font-weight: bold; color: #ffffff; text-decoration: none;">
                                            Ver informe online
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 8px; font-size: 13px; line-height: 1.6; color: #6b7280; text-align: center;">
                                Desde el enlace también podés imprimir el informe o guardarlo en PDF.
                            </p>
                            <p style="margin: 0; font-size: 15px; line-height: 1.6;">
                                No es necesario que te acerques al hospital para retirarlo. Si tenés alguna consulta,
                                comunicate con Recepción.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #f9fafb; border-top: 1px solid #e5e7eb; padding: 18px 32px;">
                            <p style="margin: 0 0 8px; font-size: 12px; line-height: 1.6; color: #6b7280;">
                                Este informe es confidencial y está dirigido únicamente a {{ $estudio->paciente_nombre }}.
                                Si no sos el destinatario, eliminá este mensaje.
                            </p>
                            <p style="margin: 0; font-size: 12px; line-height: 1.6; color: #6b7280;">
                                Este correo se generó automáticamente desde ReportFlow. No respondas a este mensaje.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
