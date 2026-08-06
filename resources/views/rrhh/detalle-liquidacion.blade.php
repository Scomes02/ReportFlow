@extends('layouts.app')

@section('titulo', 'Detalle de Liquidación - ' . $medico->name)

@section('contenido')
<div class="space-y-6">
    <!-- Botón Volver -->
    <div>
        <a href="{{ route('rrhh.medicos', $especialidadSlug ?? '') }}" 
           class="text-xs text-gray-500 hover:text-brandPrimario font-bold flex items-center">
            <i class="fas fa-arrow-left mr-1.5"></i> Volver a Médicos
        </a>
    </div>

    <!-- Header del Médico -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-black text-gray-800">{{ $medico->name }}</h2>
                <p class="text-sm text-gray-500">
                    <i class="fas fa-envelope mr-2 text-gray-400"></i>{{ $medico->email }}
                </p>
                <p class="text-sm text-gray-500">
                    <i class="fas fa-user-md mr-2 text-gray-400"></i>Médico
                </p>
            </div>
            <div class="bg-emerald-50 border border-emerald-100 text-emerald-800 px-6 py-3 rounded-xl text-center">
                <div class="text-2xl font-black">${{ number_format($totalHonorarios, 2) }}</div>
                <div class="text-xs font-bold uppercase tracking-wider">Total Honorarios</div>
            </div>
        </div>
    </div>

    <!-- Resumen de Actividad -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-2xl font-black text-brandPrimario">{{ $totalEstudios }}</div>
            <div class="text-xs text-gray-500 font-bold uppercase tracking-wider">Total Informes</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-2xl font-black text-green-600">{{ count($estudiosPorMes) }}</div>
            <div class="text-xs text-gray-500 font-bold uppercase tracking-wider">Meses Activos</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-2xl font-black text-orange-600">{{ $mesesActividad[0] ?? 'N/A' }}</div>
            <div class="text-xs text-gray-500 font-bold uppercase tracking-wider">Último Mes</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-2xl font-black text-purple-600">${{ number_format($honorarioPorEstudio, 2) }}</div>
            <div class="text-xs text-gray-500 font-bold uppercase tracking-wider">Por Estudio</div>
        </div>
    </div>

    <!-- Tabla de Detalle por Mes -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
            <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">
                <i class="fas fa-calendar-alt text-brandPrimario mr-2"></i> Detalle por Mes
            </span>
            <span class="text-xs bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full font-bold">
                {{ $totalEstudios }} Prácticas
            </span>
        </div>
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr class="text-gray-500 text-[10px] uppercase font-bold">
                    <th class="px-6 py-3">Período</th>
                    <th class="px-6 py-3 text-center">Cantidad</th>
                    <th class="px-6 py-3 text-right">Honorarios</th>
                </tr>
            </thead>
            <tbody>
                @forelse($estudiosPorMes as $mes => $estudiosDelMes)
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <td class="px-6 py-3 text-sm font-bold text-gray-800">
                        {{ $mes }}
                    </td>
                    <td class="px-6 py-3 text-center">
                        <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">
                            {{ $estudiosDelMes->count() }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-right font-bold text-emerald-600">
                        ${{ number_format($estudiosDelMes->count() * $honorarioPorEstudio, 2) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-8 text-center text-gray-400">
                        No hay estudios informados
                    </td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                <tr>
                    <td class="px-6 py-3 text-sm font-bold text-gray-800">TOTAL</td>
                    <td class="px-6 py-3 text-center font-bold text-gray-800">
                        {{ $totalEstudios }}
                    </td>
                    <td class="px-6 py-3 text-right font-black text-emerald-700 text-lg">
                        ${{ number_format($totalHonorarios, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Últimos Estudios -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b bg-gray-50">
            <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">
                <i class="fas fa-file-medical text-brandPrimario mr-2"></i> Últimos Estudios Informados
            </span>
        </div>
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr class="text-gray-500 text-[10px] uppercase font-bold">
                    <th class="px-4 py-3">ID Estudio</th>
                    <th class="px-4 py-3">Paciente</th>
                    <th class="px-4 py-3">DNI</th>
                    <th class="px-4 py-3">Estudio</th>
                    <th class="px-4 py-3">Especialidad</th>
                    <th class="px-4 py-3">Fecha Firma</th>
                </tr>
            </thead>
            <tbody>
                @forelse($estudios->take(10) as $estudio)
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-xs font-bold text-gray-800">{{ $estudio->id }}</td>
                    <td class="px-4 py-3 text-xs font-semibold text-gray-800">{{ $estudio->paciente_nombre }}</td>
                    <td class="px-4 py-3 text-xs font-mono text-brandDark font-bold">{{ $estudio->paciente_dni }}</td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $estudio->tipoEstudio->nombre ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-600">
                        {{ $estudio->tipoEstudio->especialidad->nombre ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">
                        {{ $estudio->firmado_at ? $estudio->firmado_at->format('d/m/Y H:i') : 'N/A' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                        No hay estudios informados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection