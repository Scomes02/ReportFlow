{{--
    Placeholder de sidebar. Cuando junten las vistas con la otra dupla,
    esto probablemente se unifique en un solo layout compartido por
    todos los roles (cambia el logo y los ítems de menú según
    auth()->user()->rol, no la estructura general).
--}}
<aside class="w-full md:w-56 bg-white border-r border-gray-200 p-4 shrink-0">
    <img src="{{ asset('images/logo-hu-uso-diario.png') }}" alt="Hospital Universitario" class="h-10 mb-6">

    <nav class="space-y-1">
        <a href="{{ route('tecnico.estudios.index') }}"
           class="flex items-center px-3 py-2 rounded-lg text-sm font-bold text-brandPrimario bg-blue-50">
            Mis Estudios
        </a>
    </nav>
</aside>