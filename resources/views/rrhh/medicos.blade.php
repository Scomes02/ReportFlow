@extends('layouts.app')

@section('titulo', 'Médicos - ' . $especialidad->nombre)

@section('contenido')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('rrhh.especialidades') }}" class="text-xs text-gray-500 hover:text-brandPrimario font-bold flex items-center mb-2">
                <i class="fas fa-arrow-left mr-1"></i> Volver a Especialidades
            </a>
            <h3 class="text-xl font-bold text-gray-800">Médicos de {{ $especialidad->nombre }}</h3>
            <p class="text-xs text-gray-500">Selecciona un médico para ver sus informes</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr class="text-gray-500 text-[10px] uppercase font-bold">
                    <th class="px-6 py-3">Médico</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3 text-center">Informes Firmados</th>
                    <th class="px-6 py-3 text-center">Acciones</th>
                    <th class="px-6 py-3 text-center">Liquidación</th>  <!-- ✅ NUEVA COLUMNA -->
                </tr>
            </thead>
            <tbody>
                @forelse($medicos as $medico)
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-bold text-gray-800 text-sm">{{ $medico->name }}</td>
                    <td class="px-6 py-4 text-xs text-gray-600">{{ $medico->email }}</td>
                    <td class="px-6 py-4 text-xs text-center">
                        <span class="bg-emerald-50 text-emerald-700 px-3 py-1 rounded-full font-black border border-emerald-100 text-sm">
                            {{ $medico->estudios_como_medico_count ?? 0 }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs text-center">
                        <a href="{{ route('rrhh.informes.medico', $medico->name) }}" 
                           class="bg-brandDark hover:bg-blue-900 text-white px-4 py-2 rounded-lg text-xs font-bold transition shadow inline-block">
                            Ver Informes <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </td>
                    <!-- ✅ NUEVA COLUMNA: Botón de Liquidación -->
                    <td class="px-6 py-4 text-xs text-center">
                        <a href="{{ route('rrhh.liquidacion.detalle', $medico->id) }}" 
                           class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-xs font-bold transition shadow inline-block">
                            <i class="fas fa-file-invoice-dollar mr-1.5"></i> Ver Liquidación
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                        No hay médicos con informes registrados en esta especialidad
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection