@extends('layouts.app')

@section('titulo', 'Informes - ' . ($mes ? \Illuminate\Support\Carbon::createFromFormat('Y-m', $mes)->format('m/Y') : ''))

@section('contenido')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <a href="{{ route('rrhh.archivo') }}" 
               class="text-xs text-gray-500 hover:text-brandPrimario font-bold flex items-center mb-1">
                <i class="fas fa-arrow-left mr-1"></i> Volver al Archivo General
            </a>
            <h3 class="text-xl font-bold text-gray-800">
                Informes Realizados en {{ $mes ? \Illuminate\Support\Carbon::createFromFormat('Y-m', $mes)->format('m/Y') : '' }}
            </h3>
            <p class="text-xs text-gray-500">Filtrado por mes de realización del estudio.</p>
        </div>
        <div class="text-xs font-semibold text-gray-600 bg-gray-100 border border-gray-200 px-3 py-1.5 rounded-lg shadow-sm">
            Total en este período: <strong>{{ $informes->total() }}</strong> informe(s)
        </div>
    </div>

    <!-- Filtro de Especialidad dentro del Mes -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('rrhh.archivo.mes', $mes) }}" class="flex flex-col sm:flex-row items-end gap-3">
            <div class="flex-1 w-full sm:w-auto">
                <label for="especialidad_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Filtrar por Especialidad</label>
                <select name="especialidad_id" id="especialidad_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-brandPrimario focus:outline-none bg-white">
                    <option value="">Todas las especialidades</option>
                    @foreach($especialidades as $esp)
                        <option value="{{ $esp->id }}" {{ (string)$especialidadId === (string)$esp->id ? 'selected' : '' }}>
                            {{ $esp->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit"
                        class="bg-brandPrimario hover:opacity-90 text-white font-bold px-4 py-2 rounded-lg text-xs shadow-sm flex items-center gap-1.5 transition">
                    <i class="fas fa-filter text-[11px]"></i>
                    <span>Filtrar</span>
                </button>
                @if($especialidadId)
                    <a href="{{ route('rrhh.archivo.mes', $mes) }}"
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-3 py-2 rounded-lg text-xs border border-gray-200 transition">
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabla de Informes del Mes -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr class="text-gray-500 text-[10px] uppercase font-bold">
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">Paciente</th>
                        <th class="px-4 py-3">DNI</th>
                        <th class="px-4 py-3">Especialidad</th>
                        <th class="px-4 py-3">Estudio</th>
                        <th class="px-4 py-3">Médico</th>
                        <th class="px-4 py-3">Fecha Realización</th>
                        <th class="px-4 py-3">Fecha Firma</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($informes ?? [] as $informe)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-xs font-mono text-gray-400">#{{ $informe->id }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-gray-800">{{ $informe->paciente_nombre }}</td>
                        <td class="px-4 py-3 text-xs font-mono text-brandDark font-bold">{{ $informe->paciente_dni }}</td>
                        <td class="px-4 py-3 text-xs">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $informe->tipoEstudio->especialidad->nombre ?? 'General' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $informe->tipoEstudio->nombre ?? 'N/A' }}</td>
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
                        <td colspan="8" class="px-4 py-8 text-center text-gray-400 text-xs">
                            No hay informes registrados para los filtros seleccionados en este período.
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