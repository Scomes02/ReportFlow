{{--
    Placeholder de sidebar. Cuando junten las vistas con la otra dupla,
    esto probablemente se unifique en un solo layout compartido por
    todos los roles (cambia el logo y los ítems de menú según
    auth()->user()->rol, no la estructura general).
--}}
<aside class="w-full md:w-56 bg-white border-r border-gray-200 p-4 shrink-0">
    <img src="{{ asset('images/logo-hu-uso-diario.png') }}" alt="Hospital Universitario" class="h-10 mb-6">

    <nav class="space-y-1">
        @if(auth()->user() && auth()->user()->role === 'tecnico')
            <a href="{{ route('tecnico.estudios.index') }}" 
               class="flex items-center px-3 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('tecnico.*') ? 'text-brandPrimario bg-blue-50' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-upload w-5 text-center mr-2"></i>
                Mis Estudios
            </a>
        @endif

        @if(auth()->user() && auth()->user()->role === 'medico')
            <a href="{{ route('medico.estudios.index') }}" 
               class="flex items-center px-3 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('medico.*') ? 'text-brandPrimario bg-blue-50' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-stethoscope w-5 text-center mr-2"></i>
                Worklist Médico
            </a>
        @endif

        @if(auth()->user() && auth()->user()->role === 'rrhh')
            <a href="{{ route('rrhh.dashboard') }}" 
               class="flex items-center px-3 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('rrhh.*') ? 'text-brandPrimario bg-blue-50' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-chart-pie w-5 text-center mr-2"></i>
                Dashboard RRHH
            </a>
            <a href="{{ route('rrhh.especialidades') }}" 
               class="flex items-center px-3 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('rrhh.especialidades') ? 'text-brandPrimario bg-blue-50' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-folder-open w-5 text-center mr-2"></i>
                Especialidades
            </a>
            <a href="{{ route('rrhh.archivo') }}" 
               class="flex items-center px-3 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('rrhh.archivo*') ? 'text-brandPrimario bg-blue-50' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-archive w-5 text-center mr-2"></i>
                Archivo General
            </a>
        @endif
    </nav>
    <!-- Logout  -->
    <div class="mt-auto pt-4 border-t border-gray-200">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                    class="flex items-center w-full px-3 py-2 rounded-lg text-sm font-bold text-red-600 hover:bg-red-50 transition">
                <i class="fas fa-sign-out-alt w-5 text-center mr-2"></i>
                Cerrar Sesión
            </button>
        </form>
    </div>
</aside>