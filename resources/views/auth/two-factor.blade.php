<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/Solo-logo-hu-uso-diario.png') }}">
    <title>Verificación de Seguridad - ReportFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-100 font-sans text-brandTexto">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md" style="max-width: 520px;">
            <!-- Logo -->
            <div class="text-center mb-8">
                <img src="{{ asset('images/logo-hu-uso-diario.png') }}" alt="Hospital Universitario" class="h-14 mx-auto mb-4">
                <h1 class="text-3xl font-black text-brandPrimario">ReportFlow</h1>
                <p class="text-gray-500 text-sm mt-1">Hospital Universitario &bull; UNCuyo</p>
            </div>

            <!-- Tarjeta de Verificación 2FA -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <div class="text-center mb-6">
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-50 text-brandPrimario mb-3">
                        <i class="fas fa-shield-alt text-xl"></i>
                    </span>
                    <h2 class="text-xl font-bold text-gray-800">Verificación de Seguridad</h2>
                    <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">
                        Para proteger la información médica, enviamos un código de 6 dígitos a:<br>
                        <strong class="text-gray-800 font-mono text-xs">{{ $emailOculto }}</strong>
                    </p>
                </div>

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-xs flex items-center gap-2">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                @if(session('status'))
                    <div class="bg-blue-50 border border-blue-200 text-brandPrimario px-4 py-3 rounded-lg mb-4 text-xs flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-600"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <!-- Formulario de Código -->
                <form id="form-2fa" method="POST" action="{{ route('2fa.verify') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2 text-center">
                                Código de Verificación
                            </label>
                            <input type="text"
                                   id="input-codigo"
                                   name="codigo"
                                   maxlength="6"
                                   inputmode="numeric"
                                   pattern="[0-9]*"
                                   autocomplete="one-time-code"
                                   placeholder="000000"
                                   autofocus
                                   class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-300 rounded-xl text-center font-mono font-bold text-2xl tracking-[0.4em] focus:outline-none focus:border-brandPrimario focus:bg-white focus:ring-2 focus:ring-brandPrimario/20 transition text-brandPrimario"
                                   required>
                        </div>

                        <button type="submit"
                                class="w-full bg-brandPrimario hover:opacity-90 text-white py-3 rounded-lg text-sm font-bold transition shadow-md hover:shadow-lg flex items-center justify-center cursor-pointer">
                            <i class="fas fa-lock-open mr-2"></i> Confirmar e Ingresar al Portal
                        </button>
                    </div>
                </form>

                <!-- Tarjeta de ayuda para Demostración (solo en desarrollo y pruebas) -->
                @if(!app()->isProduction() && !empty($codigoDemo))
                    <div class="mt-6 pt-5 border-t border-gray-200">
                        <div onclick="document.getElementById('input-codigo').value='{{ $codigoDemo }}'; document.getElementById('form-2fa').submit();"
                             title="Hacé clic aquí para autocompletar e ingresar de inmediato"
                             class="bg-amber-50 hover:bg-amber-100/80 border border-amber-300 text-amber-900 p-3.5 rounded-xl text-center cursor-pointer transition-all shadow-xs select-none">
                            <div class="flex items-center justify-center gap-1.5 text-xs font-bold text-amber-800">
                                <i class="fas fa-flask text-amber-600"></i>
                                <span>Modo Demostración / Evaluación</span>
                            </div>
                            <p class="text-xs text-amber-900 mt-1.5">
                                Código generado para esta sesión:
                                <span class="font-mono text-base font-black tracking-widest bg-white px-2.5 py-0.5 rounded-md border border-amber-300 text-brandPrimario inline-block ml-1">
                                    {{ $codigoDemo }}
                                </span>
                            </p>
                            <span class="text-[11px] text-amber-700 block mt-1.5 font-semibold">
                                ⚡ (Hacé clic sobre este recuadro para autocompletar e ingresar)
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Acciones secundarias -->
                <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                    <form method="POST" action="{{ route('2fa.resend') }}">
                        @csrf
                        <button type="submit" class="text-brandPrimario hover:underline font-semibold cursor-pointer">
                            <i class="fas fa-sync-alt mr-1"></i> Reenviar código
                        </button>
                    </form>

                    <form method="POST" action="{{ route('2fa.cancel') }}">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-gray-800 cursor-pointer">
                            <i class="fas fa-arrow-left mr-1"></i> Volver al login
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
