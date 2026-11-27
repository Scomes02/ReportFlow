<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Código de Verificación - ReportFlow</title>
</head>
<body style="margin: 0; padding: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; color: #1e293b;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 520px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;">
        <tr>
            <td style="background-color: #003764; padding: 24px; text-align: center;">
                <h1 style="color: #ffffff; margin: 0; font-size: 20px; font-weight: 800; letter-spacing: -0.5px;">ReportFlow</h1>
                <p style="color: #93c5fd; margin: 4px 0 0; font-size: 12px;">Hospital Universitario &bull; Sistema de Gestión de Estudios Clínicos</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 32px 28px;">
                <p style="font-size: 15px; margin: 0 0 16px; color: #334155;">
                    Hola <strong>{{ $user->name }}</strong>,
                </p>
                <p style="font-size: 14px; margin: 0 0 20px; line-height: 1.5; color: #475569;">
                    Detectamos un intento de inicio de sesión en tu cuenta de ReportFlow. Para continuar con el acceso seguro, ingresá el siguiente código de verificación de 6 dígitos:
                </p>

                <div style="background-color: #f8fafc; border: 2px dashed #003764; border-radius: 12px; padding: 20px; text-align: center; margin: 24px 0;">
                    <span style="display: block; font-size: 11px; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; color: #64748b; margin-bottom: 6px;">Código de Acceso Temporal</span>
                    <span style="font-size: 32px; font-weight: 900; letter-spacing: 8px; color: #003764; font-family: 'Courier New', Courier, monospace;">{{ $codigo }}</span>
                    <span style="display: block; font-size: 11px; color: #94a3b8; margin-top: 6px;">Válido durante los próximos 10 minutos</span>
                </div>

                <p style="font-size: 12px; line-height: 1.5; color: #64748b; margin: 24px 0 0; border-top: 1px solid #f1f5f9; padding-top: 16px;">
                    🛡️ <strong>Aviso de seguridad:</strong> Si no fuiste vos quien intentó iniciar sesión, no compartas este código con nadie y comunicate con el equipo de soporte técnico del hospital.
                </p>
            </td>
        </tr>
        <tr>
            <td style="background-color: #f8fafc; padding: 16px; text-align: center; border-top: 1px solid #e2e8f0; font-size: 11px; color: #94a3b8;">
                Hospital Universitario &bull; Universidad Nacional de Cuyo &bull; Mendoza, Argentina
            </td>
        </tr>
    </table>
</body>
</html>
