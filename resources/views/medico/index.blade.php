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

    {{-- Encabezado de la Sección con indicador de conectividad PACS --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 tracking-tight">Worklist de Estudios Pendientes</h2>
            <p class="text-xs text-slate-500 mt-0.5">Gestión e informe médico de estudios cargados con visor PACS DICOM integrado.</p>
        </div>

        {{-- Estado de Conectividad con dcm4chee-arc-light --}}
        <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold {{ $pacsOnline ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }} shadow-sm">
            <span class="relative flex h-2 w-2">
                @if($pacsOnline)
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                @endif
                <span class="relative inline-flex rounded-full h-2 w-2 {{ $pacsOnline ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
            </span>
            <span>PACS dcm4chee: <strong>{{ $pacsOnline ? 'Conectado (' . config('pacs.aet') . ')' : 'Modo Archivos / Offline' }}</strong></span>
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
                            <td class="px-4 py-3.5">
                                <div class="flex flex-col items-start gap-1">
                                    <span class="font-medium text-slate-800">{{ $estudio->tipoEstudio->nombre ?? 'Estudio' }}</span>
                                    @if($estudio->tieneDicom())
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 whitespace-nowrap" style="font-size: 10px; line-height: 14px;">
                                            <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor" style="width: 12px; height: 12px; min-width: 12px; flex-shrink: 0;" class="text-indigo-600">
                                                <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4zM3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 00-1 1v2a1 1 0 102 0v-2a1 1 0 00-1-1zm4 0a1 1 0 00-1 1v2a1 1 0 102 0v-2a1 1 0 00-1-1z"/>
                                            </svg>
                                            <span>DICOM PACS [{{ $estudio->modalidad ?? 'CR' }}]</span>
                                        </span>
                                    @elseif($estudio->archivos->isNotEmpty())
                                        <span class="inline-flex items-center gap-1 text-[10px] text-slate-500">
                                            <span>📷 {{ $estudio->archivos->count() }} archivo(s)</span>
                                        </span>
                                    @endif
                                </div>
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
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Informar</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <span>No hay estudios registrados en el sistema.</span>
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