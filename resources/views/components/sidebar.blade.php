{{--
    Sidebar compartido por todos los roles. Cambia sus links según
    auth()->user()->role -nunca según qué URL estás mirando-, así que
    lo que ves acá siempre refleja fielmente con qué usuario estás
    logueado en este momento.
--}}
<aside class="w-full md:w-56 bg-white border-r border-gray-200 p-4 shrink-0 flex flex-col">
    <img src="{{ asset('images/logo-hu-uso-diario.png') }}" alt="Hospital Universitario" class="h-10 mb-6">

    <nav class="space-y-1 flex-1">
        @if (auth()->user() && auth()->user()->role === 'tecnico')
            <a href="{{ route('tecnico.estudios.index') }}"
                class="flex items-center px-3 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('tecnico.*') ? 'text-brandPrimario bg-blue-50' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-upload w-5 text-center mr-2"></i>
                Mis Estudios
            </a>
        @endif

        @if (auth()->user() && auth()->user()->role === 'medico')
            <a href="{{ route('medico.estudios.index') }}"
                class="flex items-center px-3 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('medico.estudios.index') ? 'text-brandPrimario bg-blue-50' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-stethoscope w-5 text-center mr-2"></i>
                Worklist Médico
            </a>
            <a href="{{ route('medico.mis-informes') }}"
                class="flex items-center px-3 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('medico.mis-informes') ? 'text-brandPrimario bg-blue-50' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-file-signature w-5 text-center mr-2"></i>
                Mis Informes
            </a>
        @endif

        @if (auth()->user() && auth()->user()->role === 'rrhh')
            <a href="{{ route('rrhh.dashboard') }}"
                class="flex items-center px-3 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('rrhh.dashboard') ? 'text-brandPrimario bg-blue-50' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-chart-pie w-5 text-center mr-2"></i>
                Dashboard RRHH
            </a>
            <a href="{{ route('rrhh.especialidades') }}"
                class="flex items-center px-3 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('rrhh.especialidades') || request()->routeIs('rrhh.medicos*') ? 'text-brandPrimario bg-blue-50' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-folder-open w-5 text-center mr-2"></i>
                Especialidades
            </a>
            <a href="{{ route('rrhh.archivo') }}"
                class="flex items-center px-3 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('rrhh.archivo*') ? 'text-brandPrimario bg-blue-50' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-archive w-5 text-center mr-2"></i>
                Archivo General
            </a>
        @endif

        @if (auth()->user() && auth()->user()->role === 'callcenter')
            <a href="{{ route('callcenter.informes') }}"
                class="flex items-center px-3 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('callcenter.*') ? 'text-brandPrimario bg-blue-50' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-headset w-5 text-center mr-2"></i>
                Informes Listos
            </a>
        @endif
    </nav>

    <!-- Logout -->
    <div class="mt-auto pt-4 border-t border-gray-200">
        @if(auth()->user())
            <p class="px-3 text-[11px] text-gray-400 font-semibold uppercase tracking-wide mb-1">
                {{ \App\Enums\RolUsuario::tryFrom(auth()->user()->role)?->label() ?? auth()->user()->role }}
            </p>
        @endif
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