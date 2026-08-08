@extends('layouts.app')

@section('titulo', 'Archivo de Informes')

@section('contenido')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Archivo de Informes</h3>
            <p class="text-xs text-gray-500">Selecciona un período para ver los informes</p>
        </div>
    </div>

    <!-- Carpetas por mes -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($meses ?? [] as $mes)
        <a href="{{ route('rrhh.archivo.mes', $mes) }}" 
           class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center hover:shadow-lg transition {{ $mes === '07/2026' ? 'border-t-4 border-orange-500' : '' }}">
            <i class="fas fa-folder text-3xl text-gray-400 mb-2 block"></i>
            <span class="text-sm font-bold text-gray-700">{{ $mes }}</span>
            <span class="text-xs text-gray-500 block">Informes</span>
        </a>
        @endforeach
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
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $informe->medico->name ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $informe->firmado_at ? $informe->firmado_at->format('d/m/Y H:i') : 'N/A' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400">
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