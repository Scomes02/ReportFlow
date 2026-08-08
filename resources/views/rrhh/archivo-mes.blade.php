@extends('layouts.app')

@section('titulo', 'Informes - ' . ($mes ?? ''))

@section('contenido')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('rrhh.archivo') }}" 
               class="text-xs text-gray-500 hover:text-brandPrimario font-bold flex items-center mb-2">
                <i class="fas fa-arrow-left mr-1"></i> Volver al Archivo
            </a>
            <h3 class="text-xl font-bold text-gray-800">Informes de {{ $mes ?? '' }}</h3>
            <p class="text-xs text-gray-500">Total: {{ $informes->total() ?? 0 }} informes</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr class="text-gray-500 text-[10px] uppercase font-bold">
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Paciente</th>
                    <th class="px-4 py-3">DNI</th>
                    <th class="px-4 py-3">Estudio</th>
                    <th class="px-4 py-3">Médico</th>
                    <th class="px-4 py-3">Fecha Firma</th>
                </tr>
            </thead>
            <tbody>
                @forelse($informes ?? [] as $informe)
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-xs font-bold text-gray-800">{{ $informe->id }}</td>
                    <td class="px-4 py-3 text-xs font-semibold text-gray-800">{{ $informe->paciente_nombre }}</td>
                    <td class="px-4 py-3 text-xs font-mono text-brandDark font-bold">{{ $informe->paciente_dni }}</td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $informe->tipoEstudio->nombre ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $informe->medico->name ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $informe->firmado_at ? $informe->firmado_at->format('d/m/Y H:i') : 'N/A' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                        No hay informes para este período
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