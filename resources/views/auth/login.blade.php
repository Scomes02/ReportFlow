<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/Solo-logo-hu-uso-diario.png') }}">
    <title>Login - ReportFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-100 font-sans text-brandTexto">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md" style="max-width: 520px;">
            <!-- Logo -->
            <div class="text-center mb-8">
                <img src="{{ asset('images/logo-hu-uso-diario.png') }}" alt="Hospital Universitario" class="h-14 mx-auto mb-4">
                <h1 class="text-3xl font-black text-brandPrimario">ReportFlow</h1>
                <p class="text-gray-500 text-sm mt-1">Sistema de Gestión de Estudios Clínicos</p>
            </div>

            <!-- Formulario de Login -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6 text-center">Iniciar Sesión</h2>

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if(session('status'))
                    <div class="bg-blue-50 border border-blue-200 text-brandPrimario px-4 py-3 rounded-lg mb-4 text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                <i class="fas fa-user mr-2 text-brandPrimario"></i> Usuario
                            </label>
                            <input type="text"
                                   name="email"
                                   value="{{ old('email') }}"
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-brandPrimario focus:ring-2 focus:ring-brandPrimario/20 transition"
                                   placeholder="ej: tecnico.prueba@reportflow.local"
                                   required>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                <i class="fas fa-lock mr-2 text-brandPrimario"></i> Contraseña
                            </label>
                            <input type="password"
                                   name="password"
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-brandPrimario focus:ring-2 focus:ring-brandPrimario/20 transition"
                                   placeholder="••••••••"
                                   required>
                        </div>

                        <button type="submit"
                                class="w-full bg-brandPrimario hover:opacity-90 text-white py-3 rounded-lg text-sm font-bold transition shadow-md hover:shadow-lg flex items-center justify-center">
                            <i class="fas fa-sign-in-alt mr-2"></i> Ingresar al Portal
                        </button>
                    </div>
                </form>

                <!-- Credenciales de prueba -->
                <div class="mt-6 pt-5 border-t border-gray-200">
                    <p class="text-xs text-gray-500 text-center mb-3">
                        <i class="fas fa-key text-amber-500 mr-1"></i> Credenciales de prueba <span class="text-gray-400" style="font-size: 10px;">(clic para autocompletar)</span>
                    </p>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div onclick="document.querySelector('input[name=email]').value='tecnico.prueba@reportflow.local'; document.querySelector('input[name=password]').value='password';"
                             title="Clic para usar credenciales de Técnico"
                             class="bg-gray-50 hover:bg-blue-50/70 hover:border-brandPrimario/50 p-2.5 rounded-lg border border-gray-200 transition-all cursor-pointer select-none">
                            <span class="font-bold text-brandPrimario block text-xs">Técnico:</span>
                            <span class="text-gray-600 font-mono block" style="font-size: 11px; word-break: break-all; margin-top: 2px;">tecnico.prueba@reportflow.local</span>
                        </div>
                        <div onclick="document.querySelector('input[name=email]').value='medico@reportflow.local'; document.querySelector('input[name=password]').value='password';"
                             title="Clic para usar credenciales de Médico"
                             class="bg-gray-50 hover:bg-blue-50/70 hover:border-brandPrimario/50 p-2.5 rounded-lg border border-gray-200 transition-all cursor-pointer select-none">
                            <span class="font-bold text-brandPrimario block text-xs">Médico:</span>
                            <span class="text-gray-600 font-mono block" style="font-size: 11px; word-break: break-all; margin-top: 2px;">medico@reportflow.local</span>
                        </div>
                        <div onclick="document.querySelector('input[name=email]').value='productividad@reportflow.local'; document.querySelector('input[name=password]').value='password';"
                             title="Clic para usar credenciales de Productividad"
                             class="bg-gray-50 hover:bg-blue-50/70 hover:border-brandPrimario/50 p-2.5 rounded-lg border border-gray-200 transition-all cursor-pointer select-none">
                            <span class="font-bold text-brandPrimario block text-xs">Productividad:</span>
                            <span class="text-gray-600 font-mono block" style="font-size: 11px; word-break: break-all; margin-top: 2px;">productividad@reportflow.local</span>
                        </div>
                        <div onclick="document.querySelector('input[name=email]').value='callcenter@reportflow.local'; document.querySelector('input[name=password]').value='password';"
                             title="Clic para usar credenciales de Call Center"
                             class="bg-gray-50 hover:bg-blue-50/70 hover:border-brandPrimario/50 p-2.5 rounded-lg border border-gray-200 transition-all cursor-pointer select-none">
                            <span class="font-bold text-brandPrimario block text-xs">Call Center:</span>
                            <span class="text-gray-600 font-mono block" style="font-size: 11px; word-break: break-all; margin-top: 2px;">callcenter@reportflow.local</span>
                        </div>
                        <div class="bg-gray-50 p-2 rounded-lg border border-gray-200 col-span-2 text-center text-xs text-gray-500">
                            Contraseña: <strong class="text-gray-700 font-mono">password</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>