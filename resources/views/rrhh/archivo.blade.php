@extends('layouts.app')

@section('titulo', 'Archivo de Informes')

@section('contenido')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
        <div>
            <h3 class="text-xl font-bold text-gray-800 tracking-tight">Archivo de Informes</h3>
            <p class="text-xs text-gray-500">Filtrado por especialidad médica y fecha de realización del estudio.</p>
        </div>
        <div class="text-xs font-semibold text-gray-600 bg-gray-100 border border-gray-200 px-3 py-1.5 rounded-lg shadow-sm">
            Total en archivo: <strong>{{ $informes->total() }}</strong> informe(s)
        </div>
    </div>

    <!-- Barra de Filtros por Especialidad y Fecha de Realización -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('rrhh.archivo') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
            <!-- Selector de Especialidad -->
            <div>
                <label for="especialidad_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Especialidad</label>
                <select name="especialidad_id" id="especialidad_id"
                        class="w-full h-10 border border-gray-300 rounded-lg px-3 text-sm focus:ring-1 focus:ring-brandPrimario focus:outline-none bg-white">
                    <option value="">Todas las especialidades</option>
                    @foreach($especialidades as $esp)
                        <option value="{{ $esp->id }}" {{ (string)$especialidadId === (string)$esp->id ? 'selected' : '' }}>
                            {{ $esp->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Fecha de Realización Desde -->
            <div>
                <label for="fecha_desde" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Realización Desde</label>
                <input type="date" name="fecha_desde" id="fecha_desde" value="{{ $fechaDesde }}"
                       class="w-full h-10 border border-gray-300 rounded-lg px-3 text-sm focus:ring-1 focus:ring-brandPrimario focus:outline-none bg-white">
            </div>

            <!-- Fecha de Realización Hasta -->
            <div>
                <label for="fecha_hasta" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Realización Hasta</label>
                <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ $fechaHasta }}"
                       class="w-full h-10 border border-gray-300 rounded-lg px-3 text-sm focus:ring-1 focus:ring-brandPrimario focus:outline-none bg-white">
            </div>

            <!-- Botones de Acción (Disposición inicial en la 4ta columna, con ancho actual de botones y separación) -->
            <div>
                <label class="block text-xs font-bold invisible select-none mb-1" aria-hidden="true">&nbsp;</label>
                <div class="flex items-center gap-4">
                    <button type="submit"
                            class="h-10 min-w-[130px] bg-brandPrimario hover:opacity-90 text-white font-bold px-8 rounded-lg text-xs shadow-sm inline-flex items-center justify-center gap-2 transition whitespace-nowrap">
                        <i class="fas fa-filter text-[11px]"></i>
                        <span>Filtrar</span>
                    </button>
                    @if($especialidadId || $fechaDesde || $fechaHasta)
                        <a href="{{ route('rrhh.archivo') }}"
                           class="h-10 min-w-[90px] bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-5 rounded-lg text-xs border border-gray-300 inline-flex items-center justify-center transition whitespace-nowrap"
                           title="Limpiar filtros">
                            Limpiar
                        </a>
                    @endif
                </div>
            </div>
        </form>

        @if($especialidadId || $fechaDesde || $fechaHasta)
            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center gap-2 text-xs text-gray-500 flex-wrap">
                <span class="font-bold text-gray-700">Filtros activos:</span>
                @if($especialidadId)
                    @php $espSeleccionada = $especialidades->firstWhere('id', $especialidadId); @endphp
                    <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded text-[11px] font-medium">
                        Especialidad: {{ $espSeleccionada?->nombre ?? $especialidadId }}
                    </span>
                @endif
                @if($fechaDesde)
                    <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded text-[11px] font-medium">
                        Desde: {{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }}
                    </span>
                @endif
                @if($fechaHasta)
                    <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded text-[11px] font-medium">
                        Hasta: {{ \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') }}
                    </span>
                @endif
            </div>
        @endif
    </div>

    <!-- Listado de Informes -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b bg-gray-50 flex items-center justify-between">
            <span class="text-xs font-bold text-gray-700 uppercase">Informes Archivados</span>
            <span class="text-[11px] text-gray-500">Ordenados por fecha de realización</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr class="text-gray-500 text-[10px] uppercase font-bold">
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">Paciente</th>
                        <th class="px-4 py-3">Especialidad</th>
                        <th class="px-4 py-3">Estudio</th>
                        <th class="px-4 py-3">Médico</th>
                        <th class="px-4 py-3">Fecha Realización</th>
                        <th class="px-4 py-3">Fecha Firma</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($informes ?? [] as $informe)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-xs font-mono text-gray-400">#{{ $informe->id }}</td>
                        <td class="px-4 py-3 text-xs font-bold text-gray-800">
                            <div>{{ $informe->paciente_nombre }}</div>
                            <div class="text-[10px] text-gray-400 font-mono font-normal">DNI: {{ $informe->paciente_dni }}</div>
                        </td>
                        <td class="px-4 py-3 text-xs">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $informe->tipoEstudio->especialidad->nombre ?? 'General' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-700 font-medium">{{ $informe->tipoEstudio->nombre ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $informe->medico->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-gray-800">
                            {{ $informe->fecha_estudio ? $informe->fecha_estudio->format('d/m/Y H:i') : 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            {{ $informe->firmado_at ? $informe->firmado_at->format('d/m/Y H:i') : 'N/A' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-gray-400 text-xs">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i class="fas fa-search text-gray-300 text-2xl"></i>
                                <span>No se encontraron informes con los filtros seleccionados.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $informes->links() ?? '' }}
    </div>
</div>
@endsection