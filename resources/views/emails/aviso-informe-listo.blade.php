<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe listo para retirar</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: Arial, Helvetica, sans-serif; color: #59595b;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f3f4f6; padding: 24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb;">
                    <tr>
                        <td style="background-color: #003764; color: #ffffff; padding: 24px 32px;">
                            <p style="margin: 0; font-size: 13px; letter-spacing: 1px; text-transform: uppercase; opacity: 0.8;">Hospital Universitario</p>
                            <h1 style="margin: 6px 0 0; font-size: 22px; line-height: 1.3;">Tu informe está listo</h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 32px;">
                            <p style="margin: 0 0 16px; font-size: 15px; line-height: 1.6;">
                                Hola <strong>{{ $estudio->paciente_nombre }}</strong>:
                            </p>

                            <p style="margin: 0 0 24px; font-size: 15px; line-height: 1.6;">
                                El informe de tu estudio <strong>{{ $estudio->tipoEstudio->nombre ?? 'médico' }}</strong>
                                ya fue firmado por el profesional y está disponible para retirar.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px 20px; font-size: 14px; line-height: 1.8;">
                                        <div><span style="color: #6b7280;">Paciente:</span> {{ $estudio->paciente_nombre }}</div>
                                        <div><span style="color: #6b7280;">DNI:</span> {{ $estudio->paciente_dni }}</div>
                                        <div><span style="color: #6b7280;">Estudio:</span> {{ $estudio->tipoEstudio->nombre ?? 'N/A' }}</div>
                                        <div><span style="color: #6b7280;">Fecha del estudio:</span> {{ $estudio->fecha_estudio?->format('d/m/Y') ?? 'N/A' }}</div>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 8px; font-size: 15px; line-height: 1.6;">
                                Podés acercarte al Hospital Universitario en el horario de atención con tu DNI para retirarlo.
                            </p>
                            <p style="margin: 0; font-size: 15px; line-height: 1.6;">
                                Si ya lo retiraste o tenés alguna consulta, comunicate con Recepción.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #f9fafb; border-top: 1px solid #e5e7eb; padding: 18px 32px;">
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
