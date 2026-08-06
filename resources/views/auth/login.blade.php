<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ReportFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .bg-brandDark { background-color: #1e3a5f; }
        .text-brandDark { color: #1e3a5f; }
        .bg-brandLight { background-color: #f3f4f6; }
        .border-brandDark { border-color: #1e3a5f; }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="bg-brandDark text-white p-4 rounded-2xl inline-block shadow-lg mb-4">
                    <i class="fas fa-hospital-user text-4xl"></i>
                </div>
                <h1 class="text-3xl font-extrabold text-brandDark">ReportFlow</h1>
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
                                <i class="fas fa-user mr-2 text-brandDark"></i> Usuario
                            </label>
                            <input type="text" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-brandDark focus:ring-2 focus:ring-brandDark/20 transition"
                                   placeholder="ej: tecnico.prueba@reportflow.local"
                                   required>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                <i class="fas fa-lock mr-2 text-brandDark"></i> Contraseña
                            </label>
                            <input type="password" 
                                   name="password" 
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-brandDark focus:ring-2 focus:ring-brandDark/20 transition"
                                   placeholder="••••••••"
                                   required>
                        </div>

                        <button type="submit" 
                                class="w-full bg-brandDark hover:bg-blue-900 text-white py-3 rounded-lg text-sm font-bold transition shadow-md hover:shadow-lg flex items-center justify-center">
                            <i class="fas fa-sign-in-alt mr-2"></i> Ingresar al Portal
                        </button>
                    </div>
                </form>

                <!-- Credenciales de prueba -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-xs text-gray-500 text-center mb-3">🔑 Credenciales de prueba</p>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="bg-gray-50 p-2 rounded border border-gray-200">
                            <span class="font-bold text-brandDark">Técnico:</span><br>
                            <span class="text-gray-600">tecnico.prueba@reportflow.local</span>
                        </div>
                        <div class="bg-gray-50 p-2 rounded border border-gray-200">
                            <span class="font-bold text-brandDark">RRHH:</span><br>
                            <span class="text-gray-600">rrhh@reportflow.local</span>
                        </div>
                        <div class="bg-gray-50 p-2 rounded border border-gray-200 col-span-2">
                            <span class="font-bold text-brandDark">Médico:</span><br>
                            <span class="text-gray-600">medico@reportflow.local</span>
                        </div>
                        <div class="bg-gray-50 p-2 rounded border border-gray-200 col-span-2 text-center">
                            <span class="text-gray-500">Contraseña: <strong>password</strong></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>