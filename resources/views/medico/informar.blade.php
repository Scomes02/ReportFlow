@extends('layouts.app')

@section('titulo', 'Informar Estudio Clínico — Visor PACS DICOM')

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
    tieneDicom: {{ $estudio->tieneDicom() ? 'true' : 'false' }},
    visorModo: '{{ $estudio->tieneDicom() ? 'dicom' : 'archivos' }}',
    archivos: {{ json_encode($archivosData) }},
    activo: 0,
    zoom: 1,
    rotacion: 0,
    brillo: 100,
    contraste: 100,
    invertido: false,
    presetActivo: 'normal',
    reglaActiva: false,
    mostrarOverlay: true,
    modalRechazo: false,

    aplicarPreset(preset) {
        this.presetActivo = preset;
        if (preset === 'hueso') {
            this.brillo = 115;
            this.contraste = 200;
            this.invertido = false;
        } else if (preset === 'tejido') {
            this.brillo = 100;
            this.contraste = 130;
            this.invertido = false;
        } else if (preset === 'pulmon') {
            this.brillo = 90;
            this.contraste = 170;
            this.invertido = false;
        } else if (preset === 'invertir') {
            this.invertido = !this.invertido;
        } else {
            this.resetFiltros();
        }
    },
    resetFiltros() {
        this.zoom = 1;
        this.rotacion = 0;
        this.brillo = 100;
        this.contraste = 100;
        this.invertido = false;
        this.presetActivo = 'normal';
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

    {{-- Barra superior de navegación y metadatos del estudio --}}
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
                    @if($estudio->tieneDicom())
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-[10px] font-black bg-indigo-50 text-indigo-700 border border-indigo-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                            DICOM PACS (dcm4chee)
                        </span>
                        @if($estudio->modalidad)
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-700">
                                {{ $estudio->modalidad }}
                            </span>
                        @endif
                    @endif
                </h2>
                <p class="text-xs text-slate-500">
                    Paciente: <strong class="text-slate-800">{{ $estudio->paciente_nombre }}</strong> (DNI: {{ $estudio->paciente_dni }})
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3 text-xs">
            @if($estudio->tieneDicom() && $viewerUrl)
                <a href="{{ $viewerUrl }}" target="_blank" 
                   style="background-color: #4f46e5; color: #ffffff;"
                   title="Abrir este estudio en la consola web oficial de dcm4chee"
                   class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-sm">
                    <svg width="14" height="14" style="width: 14px; height: 14px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>Abrir en dcm4chee</span>
                </a>
            @endif

            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold border {{ $estudio->estado->badgeClasses() }}">
                {{ $estudio->estado->label() }}
            </span>
        </div>
    </div>

    {{-- Notificaciones flash --}}
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-lg text-sm flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Grid Split-Screen (Visor a la izquierda 60% / Formulario de informe a la derecha 40%) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- PANEL IZQUIERDO: VISOR CLÍNICO (DICOM PACS o ARCHIVOS) --}}
        <div class="lg:col-span-7 xl:col-span-8 bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col h-[750px]">
            
            {{-- Barra de herramientas clínicas superior --}}
            <div class="bg-slate-900 text-white px-4 py-2.5 flex flex-wrap items-center justify-between gap-2 shrink-0 border-b border-slate-800 select-none">
                
                {{-- Selector de origen (si tiene DICOM y archivos locales a la vez) --}}
                <div class="flex items-center gap-2">
                    @if($estudio->tieneDicom())
                        <div class="flex items-center bg-slate-800 p-0.5 rounded-lg text-xs font-bold">
                            <button type="button" @click="visorModo = 'dicom'" 
                                    :class="visorModo === 'dicom' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white'"
                                    class="px-2.5 py-1 rounded-md transition-colors flex items-center gap-1">
                                <span>🩺 DICOM PACS</span>
                            </button>
                            @if(count($archivosData) > 0)
                                <button type="button" @click="visorModo = 'archivos'" 
                                        :class="visorModo === 'archivos' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white'"
                                        class="px-2.5 py-1 rounded-md transition-colors flex items-center gap-1">
                                    <span>📎 Adjuntos ({{ count($archivosData) }})</span>
                                </button>
                            @endif
                        </div>
                    @else
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Visor Médico:
                        </span>
                    @endif
                </div>

                {{-- Presets de Ventana / Nivel (Window/Level) para análisis radiológico --}}
                <div class="flex items-center gap-1 text-xs">
                    <span class="text-[10px] uppercase font-bold text-slate-400 mr-1 hidden sm:inline">Ventana (W/L):</span>
                    <button type="button" @click="aplicarPreset('normal')" 
                            :class="presetActivo === 'normal' ? 'bg-slate-700 text-white font-bold' : 'text-slate-300 hover:bg-slate-800'"
                            class="px-2 py-1 rounded text-[11px] transition" title="Contraste Estándar">
                        Normal
                    </button>
                    <button type="button" @click="aplicarPreset('hueso')" 
                            :class="presetActivo === 'hueso' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-300 hover:bg-slate-800'"
                            class="px-2 py-1 rounded text-[11px] transition" title="Ventana Ósea: Mayor contraste">
                        🦴 Hueso
                    </button>
                    <button type="button" @click="aplicarPreset('tejido')" 
                            :class="presetActivo === 'tejido' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-300 hover:bg-slate-800'"
                            class="px-2 py-1 rounded text-[11px] transition" title="Ventana Tejido Blando">
                        🫀 Tejido
                    </button>
                    <button type="button" @click="aplicarPreset('pulmon')" 
                            :class="presetActivo === 'pulmon' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-300 hover:bg-slate-800'"
                            class="px-2 py-1 rounded text-[11px] transition" title="Ventana Pulmonar">
                        🫁 Pulmón
                    </button>
                    <button type="button" @click="aplicarPreset('invertir')" 
                            :class="invertido ? 'bg-amber-600 text-white font-bold' : 'text-slate-300 hover:bg-slate-800'"
                            class="px-2 py-1 rounded text-[11px] transition" title="Invertir Negativo / Positivo">
                        🌓 Invertir
                    </button>
                </div>

                {{-- Controles de Zoom, Rotación y Escala --}}
                <div class="flex items-center gap-1 text-xs">
                    <button type="button" @click="zoomOut()" title="Alejar (-)"
                            class="p-1.5 rounded hover:bg-slate-800 text-slate-300 hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/></svg>
                    </button>
                    <span class="text-[11px] font-mono text-slate-300 px-1" x-text="Math.round(zoom * 100) + '%'"></span>
                    <button type="button" @click="zoomIn()" title="Acercar (+)"
                            class="p-1.5 rounded hover:bg-slate-800 text-slate-300 hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                    </button>
                    <button type="button" @click="rotar()" title="Rotar 90°"
                            class="p-1.5 rounded hover:bg-slate-800 text-slate-300 hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </button>
                    <button type="button" @click="resetFiltros()" title="Restablecer"
                            class="p-1.5 rounded hover:bg-slate-800 text-slate-300 hover:text-white transition text-[10px] font-bold">
                        1:1
                    </button>
                    <button type="button" @click="reglaActiva = !reglaActiva" 
                            :class="reglaActiva ? 'bg-amber-600 text-white' : 'hover:bg-slate-800 text-slate-300'"
                            title="Regla milimétrica de calibración"
                            class="p-1.5 rounded transition">
                        📏
                    </button>
                    <button type="button" @click="mostrarOverlay = !mostrarOverlay" 
                            :class="mostrarOverlay ? 'bg-slate-700 text-cyan-400 border border-cyan-500/30' : 'text-slate-500 hover:text-white'"
                            title="Activar o desactivar datos superpuestos en la placa"
                            class="px-2 py-1 rounded transition text-[10px] font-mono font-bold">
                        INFO
                    </button>
                </div>
            </div>

            {{-- ÁREA DEL VISOR MÉDICO (FONDO NEGRO RADIOLÓGICO) --}}
            <div class="flex-1 bg-black relative overflow-hidden flex items-center justify-center" style="position: relative; width: 100%; height: 100%; min-height: 500px; background-color: #000000; overflow: hidden;">

                {{-- CASO A: MODO DICOM PACS (dcm4chee-arc-light) --}}
                <div x-show="visorModo === 'dicom'" class="w-full h-full relative flex items-center justify-center" style="position: relative; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                    
                    {{-- Overlay con Metadatos DICOM en las 4 esquinas (Estilo Estación PACS) --}}
                    <div x-show="mostrarOverlay" x-cloak
                         style="position: absolute; top: 12px; left: 12px; z-index: 20; background-color: rgba(0, 0, 0, 0.7); pointer-events: none; padding: 8px 10px; border-radius: 6px; font-family: monospace; font-size: 11px; line-height: 1.3; color: #22d3ee; border: 1px solid rgba(34, 211, 238, 0.25); backdrop-filter: blur(4px);">
                        <p style="font-weight: bold; color: #ffffff;">{{ strtoupper($estudio->paciente_nombre) }}</p>
                        <p>ID: {{ $estudio->paciente_dni }} | Edad: {{ $estudio->paciente_edad }}A</p>
                        <p style="color: #94a3b8;">HU UNCuyo · Radiología</p>
                    </div>

                    <div x-show="mostrarOverlay" x-cloak
                         style="position: absolute; top: 12px; right: 12px; z-index: 20; background-color: rgba(0, 0, 0, 0.7); pointer-events: none; padding: 8px 10px; border-radius: 6px; font-family: monospace; font-size: 11px; line-height: 1.3; text-align: right; color: #22d3ee; border: 1px solid rgba(34, 211, 238, 0.25); backdrop-filter: blur(4px);">
                        <p style="font-weight: bold; color: #ffffff;">MOD: {{ $estudio->modalidad ?? 'CR' }}</p>
                        <p>{{ $estudio->fecha_estudio->format('d/m/Y H:i') }}</p>
                        <p style="color: #94a3b8;">Acc: {{ $estudio->accession_number ?? 'REC-' . $estudio->id }}</p>
                    </div>

                    <div x-show="mostrarOverlay" x-cloak
                         style="position: absolute; bottom: 12px; left: 12px; z-index: 20; background-color: rgba(0, 0, 0, 0.7); pointer-events: none; padding: 6px 10px; border-radius: 6px; font-family: monospace; font-size: 10px; line-height: 1.3; color: #fcd34d; border: 1px solid rgba(251, 191, 36, 0.25); backdrop-filter: blur(4px);">
                        <p>W/L: <span x-text="contraste"></span> / <span x-text="brillo"></span> (<span x-text="presetActivo"></span>)</p>
                        <p>Zoom: <span x-text="Math.round(zoom * 100) + '%'"></span> | Rot: <span x-text="rotacion + '°'"></span></p>
                    </div>

                    <div x-show="mostrarOverlay" x-cloak
                         style="position: absolute; bottom: 12px; right: 12px; z-index: 20; background-color: rgba(0, 0, 0, 0.7); pointer-events: none; padding: 6px 10px; border-radius: 6px; font-family: monospace; font-size: 10px; line-height: 1.3; text-align: right; color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.2); backdrop-filter: blur(4px);">
                        <p style="color: #34d399; font-weight: bold;">● dcm4chee PACS</p>
                        <p style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $estudio->study_instance_uid }}">{{ substr($estudio->study_instance_uid, 0, 24) }}...</p>
                    </div>

                    {{-- Regla diagnóstica milimétrica superpuesta (si se activa) --}}
                    <div x-show="reglaActiva" x-cloak 
                         style="position: absolute; z-index: 20; left: 50%; top: 50%; transform: translate(-50%, -50%); pointer-events: none; background: rgba(251, 191, 36, 0.95); color: #000; padding: 4px 12px; border-radius: 6px; font-family: monospace; font-size: 11px; font-weight: bold; border: 1px solid #000; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
                        <span>├────────── 50 mm ──────────┤</span>
                    </div>

                    {{-- Renderizador de la Imagen Médica con Filtros CSS clínicos dinámicos --}}
                    <div class="w-full h-full overflow-auto flex items-center justify-center p-6 cursor-grab active:cursor-grabbing">
                        @if(count($archivosData) > 0)
                            <img :src="archivos[0]?.url"
                                 :style="`transform: scale(${zoom}) rotate(${rotacion}deg); filter: contrast(${contraste}%) brightness(${brillo}%) ${invertido ? 'invert(1)' : ''}; transition: filter 0.15s ease, transform 0.15s ease;`"
                                 class="max-h-full max-w-full object-contain select-none shadow-2xl rounded pointer-events-auto"
                                 alt="Placa DICOM dcm4chee" />
                        @else
                            {{-- Simulación gráfica PACS de alta fidelidad si aún no tiene archivo local cargado --}}
                            <div class="flex flex-col items-center justify-center text-slate-500 space-y-4 p-8 border border-slate-800 rounded-2xl bg-slate-950/60 max-w-md text-center">
                                <div class="w-20 h-20 rounded-2xl bg-indigo-950/50 border border-indigo-500/30 flex items-center justify-center text-indigo-400 text-3xl font-black">
                                    DICOM
                                </div>
                                <div>
                                    <h4 class="text-white font-bold text-sm">Estudio DICOM en dcm4chee-arc-light</h4>
                                    <p class="text-xs text-slate-400 mt-1 font-mono">UID: {{ $estudio->study_instance_uid }}</p>
                                </div>
                                <div class="flex flex-wrap gap-2 justify-center pt-2">
                                    @if($viewerUrl)
                                        <a href="{{ $viewerUrl }}" target="_blank" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2 rounded-lg transition shadow">
                                            Abrir en Visor dcm4chee
                                        </a>
                                    @endif
                                    @if($ohifUrl)
                                        <a href="{{ $ohifUrl }}" target="_blank" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold px-4 py-2 rounded-lg transition">
                                            Abrir en OHIF Viewer
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- CASO B: MODO ARCHIVOS LOCALES TRADICIONALES (PDF o Imágenes secundarias) --}}
                <div x-show="visorModo === 'archivos'" class="w-full h-full relative flex items-center justify-center">
                    <template x-if="archivos.length > 0 && archivos[activo]?.esPdf">
                        <iframe :src="archivos[activo].url" 
                                class="w-full h-full border-0 bg-white" 
                                title="Visor de PDF">
                        </iframe>
                    </template>

                    <template x-if="archivos.length > 0 && archivos[activo]?.esImagen">
                        <div class="w-full h-full overflow-auto flex items-center justify-center p-4 cursor-grab active:cursor-grabbing">
                            <img :src="archivos[activo].url" 
                                 :style="`transform: scale(${zoom}) rotate(${rotacion}deg); filter: contrast(${contraste}%) brightness(${brillo}%) ${invertido ? 'invert(1)' : ''};`"
                                 class="max-h-full max-w-full object-contain shadow-2xl select-none rounded pointer-events-auto"
                                 alt="Placa del estudio clínico" />
                        </div>
                    </template>

                    <template x-if="archivos.length === 0">
                        <div class="text-center p-8 max-w-md text-slate-400 space-y-3">
                            <h4 class="text-slate-200 font-bold text-sm">Sin archivos adjuntos locales</h4>
                            <p class="text-xs text-slate-400">Este estudio se encuentra registrado en el PACS con su identificador DICOM.</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- PANEL DERECHO: METADATOS CLÍNICOS Y REDACCIÓN DEL INFORME --}}
        <div class="lg:col-span-5 xl:col-span-4 space-y-4">

            {{-- Ficha resumida del paciente y del estudio --}}
            <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm p-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[#003764]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Ficha Clínica del Paciente
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
                        <span class="text-[10px] text-slate-400 block uppercase font-bold">Fecha Estudio</span>
                        <span class="text-slate-800">{{ $estudio->fecha_estudio->format('d/m/Y - H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block uppercase font-bold">Técnico</span>
                        <span class="text-slate-800">{{ $estudio->tecnico_nombre }}</span>
                    </div>
                    @if($estudio->accession_number)
                        <div class="col-span-2 bg-slate-50 p-2 rounded border border-slate-200 text-[11px] font-mono">
                            <span class="text-slate-500">Accession Number:</span> <strong class="text-slate-800">{{ $estudio->accession_number }}</strong>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Formulario para redacción y firma del informe médico --}}
            <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm p-5 space-y-4">
                <form method="POST" action="{{ route('medico.estudios.informar.guardar', $estudio->id) }}" class="space-y-4">
                    @csrf

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Diagnóstico / Informe Clínico Oficial
                            </label>
                            <span class="text-[10px] text-slate-400 font-medium">Dr/a. {{ auth()->user()->name }}</span>
                        </div>
                        <textarea name="informe" rows="12" required minlength="3"
                                  placeholder="Describa aquí los hallazgos radiológicos observados, mediciones, conclusión diagnóstica y sugerencias clínicas..."
                                  class="w-full border border-slate-300 rounded-xl p-3.5 text-xs text-slate-800 leading-relaxed focus:ring-2 focus:ring-[#003764]/20 focus:border-[#003764] focus:outline-none transition-all shadow-inner"></textarea>
                        <p class="text-[11px] text-slate-400 mt-1">
                            ℹ️ Una vez firmado, dispondrá de 24 horas para ediciones directas antes de pasar a modo de solo adendas.
                        </p>
                    </div>

                    <div class="pt-5 flex flex-wrap items-center justify-center gap-4 sm:gap-6 border-t border-slate-100">
                        <button type="button" @click="modalRechazo = true"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-bold bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 shadow-sm hover:shadow transition-all w-[210px] sm:w-[240px] h-12 cursor-pointer">
                            <svg width="18" height="18" style="width: 18px; height: 18px; flex-shrink: 0;" class="text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Rehacer (Devolver)</span>
                        </button>

                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-md hover:shadow-lg transition-all w-[210px] sm:w-[240px] h-12 cursor-pointer">
                            <svg width="18" height="18" style="width: 18px; height: 18px; flex-shrink: 0;" class="text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>Firmar y Guardar</span>
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
