@extends('layouts.app')

@section('titulo', 'Informar Estudio Clínico')

@section('contenido')
@php
    $archivosData = $estudio->archivos->map(function($a) use ($estudio) {
        $esImagen = str_starts_with($a->mime_type ?? '', 'image/');
        $esPdf = ($a->mime_type === 'application/pdf') || str_ends_with(strtolower($a->nombre_original), '.pdf');
        $tamanoKb = round(($a->tamano_bytes ?? 0) / 1024, 1);
        $tamanoStr = $tamanoKb > 1024 ? round($tamanoKb / 1024, 2) . ' MB' : $tamanoKb . ' KB';

        return [
            'id' => $a->id,
            'nombre' => $a->nombre_original,
            'url' => route('medico.estudios.archivos.ver', [$estudio->id, $a->id]),
            'esImagen' => $esImagen,
            'esPdf' => $esPdf,
            'tamano' => $tamanoStr,
        ];
    })->values();
@endphp

<div x-data="{
    archivos: {{ json_encode($archivosData) }},
    activo: 0,
    zoom: 1,
    rotacion: 0,
    modalRechazo: false,
    resetView() {
        this.zoom = 1;
        this.rotacion = 0;
    },
    zoomIn() {
        this.zoom = Math.min(this.zoom + 0.25, 3.5);
    },
    zoomOut() {
        this.zoom = Math.max(this.zoom - 0.25, 0.5);
    },
    rotar() {
        this.rotacion = (this.rotacion + 90) % 360;
    }
}" class="space-y-4">

    {{-- Barra superior de navegación y contexto --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('medico.estudios.index') }}" 
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Volver a Worklist
            </a>
            <div class="h-5 w-px bg-slate-200 hidden sm:block"></div>
            <div>
                <h2 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Estudio #{{ $estudio->id }}: {{ $estudio->tipoEstudio->nombre ?? 'Estudio' }}</span>
                    @if($estudio->tipoEstudio?->especialidad)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-[#003764] border border-blue-200">
                            {{ $estudio->tipoEstudio->especialidad->nombre }}
                        </span>
                    @endif
                </h2>
                <p class="text-xs text-slate-500">
                    Paciente: <strong class="text-slate-800">{{ $estudio->paciente_nombre }}</strong> (DNI: {{ $estudio->paciente_dni }})
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 text-xs">
            <span class="text-slate-400">Estado:</span>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold border {{ $estudio->estado->badgeClasses() }}">
                {{ $estudio->estado->label() }}
            </span>
        </div>
    </div>

    {{-- Notificaciones de error o éxito --}}
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-lg text-sm flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Grid Split-Screen (Visor a la izquierda 60% / Formulario a la derecha 40%) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- PANEL IZQUIERDO: VISOR INTEGRADO DEL ESTUDIO --}}
        <div class="lg:col-span-7 xl:col-span-8 bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col h-[700px]">
            
            {{-- Encabezado del Visor con Selector de Archivos y Controles --}}
            <div class="bg-slate-900 text-white px-4 py-2.5 flex flex-wrap items-center justify-between gap-2 shrink-0 border-b border-slate-800">
                <div class="flex items-center gap-2 overflow-x-auto max-w-full">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Visor:
                    </span>

                    <template x-for="(arch, idx) in archivos" :key="arch.id">
                        <button type="button" @click="activo = idx; resetView()"
                                :class="activo === idx ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
                                class="px-3 py-1 rounded-md text-xs font-medium transition-colors flex items-center gap-1.5 shrink-0">
                            <span x-show="arch.esPdf">📄</span>
                            <span x-show="arch.esImagen">🖼️</span>
                            <span x-text="arch.nombre" class="max-w-[140px] truncate"></span>
                        </button>
                    </template>
                </div>

                {{-- Controles de imagen (solo visibles si el archivo activo es imagen) --}}
                <div class="flex items-center gap-1 text-xs" x-show="archivos.length > 0 && archivos[activo]?.esImagen">
                    <button type="button" @click="zoomOut()" title="Alejar"
                            class="p-1.5 rounded hover:bg-slate-800 text-slate-300 hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/></svg>
                    </button>
                    <span class="text-[11px] font-mono text-slate-400 px-1" x-text="Math.round(zoom * 100) + '%'"></span>
                    <button type="button" @click="zoomIn()" title="Acercar"
                            class="p-1.5 rounded hover:bg-slate-800 text-slate-300 hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                    </button>
                    <button type="button" @click="rotar()" title="Rotar 90°"
                            class="p-1.5 rounded hover:bg-slate-800 text-slate-300 hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </button>
                    <button type="button" @click="resetView()" title="Restablecer vista"
                            class="p-1.5 rounded hover:bg-slate-800 text-slate-300 hover:text-white transition text-[10px] font-bold">
                        1:1
                    </button>
                    <a :href="archivos[activo]?.url" target="_blank" title="Abrir en pestaña nueva"
                       class="p-1.5 rounded hover:bg-slate-800 text-slate-300 hover:text-white transition ml-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>

                {{-- Enlace externo para PDF --}}
                <div x-show="archivos.length > 0 && archivos[activo]?.esPdf">
                    <a :href="archivos[activo]?.url" target="_blank"
                       class="text-[11px] text-slate-300 hover:text-white inline-flex items-center gap-1 px-2 py-1 bg-slate-800 rounded hover:bg-slate-700 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Abrir PDF en pestaña
                    </a>
                </div>
            </div>

            {{-- Área de Visualización Principal --}}
            <div class="flex-1 bg-slate-950 relative overflow-hidden flex items-center justify-center">
                
                {{-- Caso 1: Hay archivos y es un PDF --}}
                <template x-if="archivos.length > 0 && archivos[activo]?.esPdf">
                    <iframe :src="archivos[activo].url" 
                            class="w-full h-full border-0 bg-white" 
                            title="Visor de PDF">
                    </iframe>
                </template>

                {{-- Caso 2: Hay archivos y es una imagen --}}
                <template x-if="archivos.length > 0 && archivos[activo]?.esImagen">
                    <div class="w-full h-full overflow-auto flex items-center justify-center p-4 cursor-grab active:cursor-grabbing">
                        <img :src="archivos[activo].url" 
                             :style="'transform: scale(' + zoom + ') rotate(' + rotacion + 'deg); transform-origin: center center; transition: transform 0.15s ease;'"
                             class="max-h-full max-w-full object-contain shadow-2xl select-none rounded pointer-events-auto"
                             alt="Placa del estudio clínico" />
                    </div>
                </template>

                {{-- Caso 3: No hay archivos adjuntos en el estudio --}}
                <template x-if="archivos.length === 0">
                    <div class="text-center p-8 max-w-md text-slate-400 space-y-3">
                        <div class="w-16 h-16 rounded-full bg-slate-900 border border-slate-800 flex items-center justify-center mx-auto text-amber-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h4 class="text-slate-200 font-bold text-sm">Sin archivos o placas adjuntas</h4>
                        <p class="text-xs text-slate-400">
                            El técnico no adjuntó placas ni archivos digitales al cargar este estudio. Si necesita el archivo físico para diagnosticar, puede devolver el estudio con la opción <strong>"Rehacer"</strong>.
                        </p>
                    </div>
                </template>
            </div>
        </div>

        {{-- PANEL DERECHO: FICHA DEL PACIENTE Y REDACCIÓN DEL INFORME --}}
        <div class="lg:col-span-5 xl:col-span-4 space-y-4">

            {{-- Ficha resumida del paciente --}}
            <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm p-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[#003764]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Ficha del Paciente y Estudio
                </h3>
                <div class="grid grid-cols-2 gap-3 text-xs text-slate-600">
                    <div>
                        <span class="text-[10px] text-slate-400 block uppercase font-bold">Paciente</span>
                        <strong class="text-slate-900 font-semibold">{{ $estudio->paciente_nombre }}</strong>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block uppercase font-bold">DNI / Edad</span>
                        <span class="text-slate-800">{{ $estudio->paciente_dni }} ({{ $estudio->paciente_edad }} años)</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block uppercase font-bold">Fecha Realización</span>
                        <span class="text-slate-800">{{ $estudio->fecha_estudio->format('d/m/Y - H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block uppercase font-bold">Técnico</span>
                        <span class="text-slate-800">{{ $estudio->tecnico_nombre }}</span>
                    </div>
                </div>
            </div>

            {{-- Formulario para redacción y firma del informe --}}
            <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm p-5 space-y-4">
                <form method="POST" action="{{ route('medico.estudios.informar.guardar', $estudio->id) }}" class="space-y-4">
                    @csrf

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Diagnóstico / Informe Clínico
                            </label>
                            <span class="text-[10px] text-slate-400">Dr/a. {{ auth()->user()->name }}</span>
                        </div>
                        <textarea name="informe" rows="12" required minlength="3"
                                  placeholder="Escriba aquí los hallazgos observados, mediciones diagnósticas, conclusión y recomendaciones..."
                                  class="w-full border border-slate-300 rounded-xl p-3.5 text-xs text-slate-800 leading-relaxed focus:ring-2 focus:ring-[#003764]/20 focus:border-[#003764] focus:outline-none transition-all shadow-inner"></textarea>
                        <p class="text-[11px] text-slate-400 mt-1">
                            ℹ️ Una vez firmado, tendrá una ventana de 24 horas para editarlo directamente antes de pasar a modo de solo adendas.
                        </p>
                    </div>

                    <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-slate-100">
                        <button type="button" @click="modalRechazo = true"
                                class="w-full sm:w-auto bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 px-4 py-2.5 rounded-lg text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Rehacer (Devolver)
                        </button>

                        <button type="submit"
                                class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-lg text-xs font-bold shadow-md transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Guardar y Firmar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal para Devolver Estudio al Técnico (Rehacer) --}}
    <div x-show="modalRechazo" x-cloak
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.outside="modalRechazo = false"
             class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100 text-start">
            <div class="bg-amber-600 text-white px-5 py-4 flex justify-between items-center">
                <h3 class="font-bold text-sm tracking-wide flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Devolver estudio al técnico
                </h3>
                <button @click="modalRechazo = false" class="text-white/80 hover:text-white transition-colors text-lg leading-none">&times;</button>
            </div>

            <form method="POST" action="{{ route('medico.estudios.rechazar', $estudio->id) }}" class="p-6 space-y-4">
                @csrf
                <div class="bg-amber-50 border border-amber-200 text-amber-800 p-3 rounded-lg text-xs leading-relaxed">
                    Este estudio volverá al técnico <strong>{{ $estudio->tecnico_nombre }}</strong> con estado
                    <strong>"Rechazado - Rehacer"</strong>. Describa qué corregir (ej. toma borrosa, imagen cortada, datos inconsistentes).
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Motivo obligatorio del rechazo
                    </label>
                    <textarea name="motivo_rechazo" rows="5" required minlength="5"
                              placeholder="Ej: la placa quedó fuera de foco o el registro del trazado está incompleto..."
                              class="w-full border border-slate-300 rounded-lg p-3 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 focus:outline-none transition-all"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="modalRechazo = false"
                            class="px-4 py-2 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2 rounded-lg text-xs font-bold shadow-sm transition-all">
                        Confirmar rechazo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
