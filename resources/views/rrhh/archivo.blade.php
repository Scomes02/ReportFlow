@extends('layouts.app')

@section('titulo', 'Archivo de Informes')

@section('contenido')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Archivo de Informes</h3>
            <p class="text-xs text-gray-500">Seleccioná un período para ver los informes</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b bg-gray-50">
            <span class="text-xs font-bold text-gray-700 uppercase">Filtrar informes</span>
        </div>
        <form method="GET" action="{{ route('rrhh.archivo') }}" class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Médico</label>
                <select name="medico_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-brandPrimario focus:outline-none">
                    <option value="">Todos</option>
                    @foreach($medicos ?? [] as $medico)
                        <option value="{{ $medico->id }}" @selected(request('medico_id') == $medico->id)>{{ $medico->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Especialidad</label>
                <select name="especialidad_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-brandPrimario focus:outline-none">
                    <option value="">Todas</option>
                    @foreach($especialidades ?? [] as $especialidad)
                        <option value="{{ $especialidad->id }}" @selected(request('especialidad_id') == $especialidad->id)>{{ $especialidad->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Tipo de estudio</label>
                <select name="tipo_estudio_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-brandPrimario focus:outline-none">
                    <option value="">Todos</option>
                    @foreach($tiposEstudio ?? [] as $tipoEstudio)
                        <option value="{{ $tipoEstudio->id }}" @selected(request('tipo_estudio_id') == $tipoEstudio->id)>{{ $tipoEstudio->nombre }} @if($tipoEstudio->especialidad) — {{ $tipoEstudio->especialidad->nombre }} @endif</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Desde</label>
                <input type="date" name="desde" value="{{ request('desde') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-brandPrimario focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Hasta</label>
                <input type="date" name="hasta" value="{{ request('hasta') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-brandPrimario focus:outline-none">
            </div>
            <div class="md:col-span-2 lg:col-span-5 flex gap-2">
                <button type="submit" class="bg-brandPrimario hover:opacity-90 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm">
                    <i class="fas fa-filter mr-1"></i> Filtrar
                </button>
                <a href="{{ route('rrhh.archivo') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm font-bold">
                    Limpiar filtros
                </a>
            </div>
        </form>
    </div>

    <!-- Últimos informes -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b bg-gray-50">
            <span class="text-xs font-bold text-gray-700 uppercase">Últimos Informes Firmados</span>
        </div>
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr class="text-gray-500 text-[10px] uppercase font-bold">
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Paciente</th>
                    <th class="px-4 py-3">Estudio</th>
                    <th class="px-4 py-3">Especialidad</th>
                    <th class="px-4 py-3">Médico</th>
                    <th class="px-4 py-3">Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($informes ?? [] as $informe)
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-xs font-bold text-gray-800">{{ $informe->id }}</td>
                    <td class="px-4 py-3 text-xs font-semibold text-gray-800">{{ $informe->paciente_nombre }}</td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $informe->tipoEstudio->nombre ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $informe->tipoEstudio->especialidad->nombre ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $informe->medico->name ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $informe->firmado_at ? $informe->firmado_at->format('d/m/Y H:i') : 'N/A' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                        No hay informes registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $informes->links() ?? '' }}
    </div>
</div>
@endsection