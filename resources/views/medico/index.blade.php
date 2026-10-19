@extends('layouts.app')

@section('titulo', 'Worklist - Médico')

@section('contenido')
<div class="space-y-6">

    {{-- Notificación de éxito despachada por el controlador --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg text-sm flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- Notificación de error --}}
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-lg text-sm flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Aviso si el médico no tiene especialidad asignada --}}
    @if(!$medico->especialidad_id)
        <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-lg text-xs flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <span>Su usuario aún no tiene una especialidad asignada en el sistema. Contacte a Administración para que le asignen su especialidad y pueda visualizar sus estudios pendientes.</span>
        </div>
    @endif

    {{-- Encabezado de la Sección --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-xl font-bold text-slate-800 tracking-tight">Worklist de Estudios Pendientes</h2>
                @if($medico->especialidad)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-[#003764] border border-blue-200">
                        {{ $medico->especialidad->nombre }}
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
                @if($medico->especialidad)
                    Estudios pendientes asignados a su especialidad ({{ $medico->especialidad->nombre }}).
                @else
                    Gestión e informe médico de estudios cargados.
                @endif
            </p>
        </div>
    </div>

    {{-- Tabla de Estudios estilo UI Médica --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-4 py-3.5">ID ESTUDIO</th>
                        <th class="px-4 py-3.5">PACIENTE</th>
                        <th class="px-4 py-3.5">TIPO DE ESTUDIO</th>
                        <th class="px-4 py-3.5">ARCHIVOS</th>
                        <th class="px-4 py-3.5">FECHA/HORA</th>
                        <th class="px-4 py-3.5">TÉCNICO</th>
                        <th class="px-4 py-3.5">ESTADO</th>
                        <th class="px-4 py-3.5 text-end">ACCIONES</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-600">
                    @forelse ($estudios as $estudio)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-3.5 font-mono text-[11px] text-slate-400">#{{ $estudio->id }}</td>
                            <td class="px-4 py-3.5 font-bold text-slate-900">{{ $estudio->paciente_nombre }}</td>
                            <td class="px-4 py-3.5 text-slate-700">{{ $estudio->tipoEstudio->nombre ?? 'Estudio' }}</td>
                            <td class="px-4 py-3.5">
                                @if($estudio->archivos->isNotEmpty())
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        {{ $estudio->archivos->count() }} {{ Str::plural('archivo', $estudio->archivos->count()) }}
                                    </span>
                                @else
                                    <span class="text-[10px] text-slate-400 italic">Sin archivos</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-slate-500">{{ $estudio->fecha_estudio->format('d/m/Y - H:i') }}</td>
                            <td class="px-4 py-3.5 text-slate-600">{{ $estudio->tecnico_nombre }}</td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold border {{ $estudio->estado->badgeClasses() }}">
                                    {{ $estudio->estado->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-end">
                                <a href="{{ route('medico.estudios.informar', $estudio->id) }}"
                                   class="inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white px-4 py-1.5 rounded-full text-xs font-semibold tracking-wide shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1">
                                    <span>Informar</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    @if($medico->especialidad)
                                        <span>No hay estudios pendientes de {{ $medico->especialidad->nombre }} en este momento.</span>
                                    @else
                                        <span>No hay estudios disponibles para informar.</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection