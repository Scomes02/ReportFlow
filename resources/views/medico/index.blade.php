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
                        <tr class="hover:bg-slate-50/50 transition-colors" x-data="{ modalAbierto: false, mostrarRechazo: false }">
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
                                <button @click="modalAbierto = true; mostrarRechazo = false"
                                        class="bg-red-600 hover:bg-red-700 text-white px-5 py-1.5 rounded-full text-xs font-semibold tracking-wide shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1">
                                    Informar
                                </button>

                                {{-- Modal para Redactar/Firmar Informe, o Rechazar --}}
                                <template x-teleport="body">
                                    <div x-show="modalAbierto" 
                                         x-cloak
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0"
                                         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto">
                                        
                                        <div @click.outside="modalAbierto = false"
                                             class="bg-white rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden border border-slate-100 my-8 text-start max-h-[90vh] flex flex-col">

                                            {{-- Vista 1: redactar y firmar el informe --}}
                                            <div x-show="!mostrarRechazo" class="flex flex-col flex-1 overflow-hidden">
                                                <div class="bg-[#1c3452] text-white px-5 py-4 flex justify-between items-center shrink-0">
                                                    <div>
                                                        <h3 class="font-bold text-sm tracking-wide">Estudio: {{ $estudio->tipoEstudio->nombre ?? 'Estudio' }}</h3>
                                                        <span class="text-[11px] text-slate-300">Paciente: {{ $estudio->paciente_nombre }} (DNI: {{ $estudio->paciente_dni }})</span>
                                                    </div>
                                                    <button @click="modalAbierto = false" class="text-slate-300 hover:text-white transition-colors text-lg leading-none">&times;</button>
                                                </div>

                                                <form method="POST" action="{{ route('medico.estudios.informar', $estudio->id) }}" class="p-6 space-y-4 overflow-y-auto flex-1">
                                                    @csrf

                                                    <!-- Ficha resumida del paciente -->
                                                    <div class="bg-slate-50 p-3.5 rounded-lg border border-slate-200/80 text-xs grid grid-cols-2 sm:grid-cols-4 gap-3 text-slate-600">
                                                        <div><strong class="text-slate-500 block text-[10px] uppercase">Paciente</strong> <span class="text-slate-900 font-semibold">{{ $estudio->paciente_nombre }}</span></div>
                                                        <div><strong class="text-slate-500 block text-[10px] uppercase">Edad / DNI</strong> <span class="text-slate-900">{{ $estudio->paciente_edad }} años ({{ $estudio->paciente_dni }})</span></div>
                                                        <div><strong class="text-slate-500 block text-[10px] uppercase">Fecha Estudio</strong> <span class="text-slate-900">{{ $estudio->fecha_estudio->format('d/m/Y - H:i') }}</span></div>
                                                        <div><strong class="text-slate-500 block text-[10px] uppercase">Técnico</strong> <span class="text-slate-900">{{ $estudio->tecnico_nombre }}</span></div>
                                                    </div>

                                                    <!-- Archivos adjuntos del estudio para que el médico los visualice -->
                                                    <div class="space-y-2">
                                                        <div class="flex items-center justify-between">
                                                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                                                                <svg class="w-4 h-4 text-[#003764]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                                Placas y Archivos del Estudio
                                                            </label>
                                                            <span class="text-[11px] text-slate-500 font-medium">
                                                                {{ $estudio->archivos->count() }} {{ Str::plural('archivo adjunto', $estudio->archivos->count()) }}
                                                            </span>
                                                        </div>

                                                        @if($estudio->archivos->isNotEmpty())
                                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                                                @foreach($estudio->archivos as $archivo)
                                                                    @php
                                                                        $esImagen = str_starts_with($archivo->mime_type ?? '', 'image/');
                                                                        $esPdf = ($archivo->mime_type === 'application/pdf') || str_ends_with(strtolower($archivo->nombre_original), '.pdf');
                                                                        $tamanoKb = round(($archivo->tamano_bytes ?? 0) / 1024, 1);
                                                                        $tamanoStr = $tamanoKb > 1024 ? round($tamanoKb / 1024, 2) . ' MB' : $tamanoKb . ' KB';
                                                                        $urlVer = route('medico.estudios.archivos.ver', [$estudio->id, $archivo->id]);
                                                                    @endphp
                                                                    <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-lg hover:border-blue-300 transition-colors">
                                                                        <div class="flex items-center gap-3 min-w-0">
                                                                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 {{ $esPdf ? 'bg-rose-100 text-rose-600' : ($esImagen ? 'bg-blue-100 text-blue-600' : 'bg-slate-200 text-slate-600') }}">
                                                                                @if($esPdf)
                                                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg>
                                                                                @elseif($esImagen)
                                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                                                @else
                                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                                                @endif
                                                                            </div>
                                                                            <div class="truncate">
                                                                                <p class="text-xs font-semibold text-slate-800 truncate" title="{{ $archivo->nombre_original }}">{{ $archivo->nombre_original }}</p>
                                                                                <span class="text-[10px] text-slate-400">{{ $tamanoStr }}</span>
                                                                            </div>
                                                                        </div>
                                                                        <a href="{{ $urlVer }}" target="_blank"
                                                                           class="inline-flex items-center gap-1.5 bg-[#003764] hover:bg-[#00284a] text-white px-3 py-1.5 rounded-lg text-xs font-semibold shrink-0 shadow-sm transition-colors">
                                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                                            Ver estudio
                                                                        </a>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800 flex items-center gap-2">
                                                                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                                <span>El técnico no adjuntó archivos o placas para este estudio al momento de registrarlo.</span>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div>
                                                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                                            Redacción del Informe ({{ strtoupper($estudio->tipoEstudio->nombre ?? '') }})
                                                        </label>
                                                        <textarea name="informe" rows="5" required 
                                                                  placeholder="Escriba el resultado, observaciones y conclusión diagnóstica aquí..."
                                                                  class="w-full border border-slate-300 rounded-lg p-3 text-xs text-slate-800 focus:ring-2 focus:ring-[#1c3452]/20 focus:border-[#1c3452] focus:outline-none transition-all"></textarea>
                                                    </div>

                                                    <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                                                        <button type="button" @click="mostrarRechazo = true"
                                                                class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg text-xs font-bold transition-all shadow-sm">
                                                            Rehacer
                                                        </button>

                                                        <div class="flex gap-2">
                                                            <button type="button" @click="modalAbierto = false"
                                                                    class="px-4 py-2 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                                                                Cancelar
                                                            </button>
                                                            <button type="submit"
                                                                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-lg text-xs font-bold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1">
                                                                Guardar y firmar
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>

                                            {{-- Vista 2: motivo de rechazo (Rehacer) --}}
                                            <div x-show="mostrarRechazo">
                                                <div class="bg-amber-600 text-white px-5 py-4 flex justify-between items-center">
                                                    <h3 class="font-bold text-sm tracking-wide">Devolver estudio al técnico</h3>
                                                    <button @click="modalAbierto = false" class="text-white/80 hover:text-white transition-colors text-lg leading-none">&times;</button>
                                                </div>

                                                <form method="POST" action="{{ route('medico.estudios.rechazar', $estudio->id) }}" class="p-6 space-y-4">
                                                    @csrf

                                                    <div class="bg-amber-50 border border-amber-200 text-amber-800 p-3 rounded-lg text-xs">
                                                        Este estudio va a volver al técnico <strong>{{ $estudio->tecnico_nombre }}</strong> con estado
                                                        <strong>"Rechazado - Rehacer"</strong>. Explicá qué hay que corregir para que pueda solucionarlo.
                                                    </div>

                                                    <div>
                                                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                                                            Motivo del rechazo
                                                        </label>
                                                        <textarea name="motivo_rechazo" rows="5" required minlength="5"
                                                                  placeholder="Ej: la placa está fuera de foco, hay que repetir la toma..."
                                                                  class="w-full border border-slate-300 rounded-lg p-3 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 focus:outline-none transition-all"></textarea>
                                                    </div>

                                                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                                                        <button type="button" @click="mostrarRechazo = false"
                                                                class="px-4 py-2 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                                                            Volver
                                                        </button>
                                                        <button type="submit"
                                                                class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2 rounded-lg text-xs font-bold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-1">
                                                            Confirmar rechazo
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </template>
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