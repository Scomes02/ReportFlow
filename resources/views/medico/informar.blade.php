@extends('layouts.app')

@section('titulo', 'ReportFlow — Diagnóstico Multidisciplinario e Informe Dual')

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

    $estudioPrevioData = null;
    if ($estudioPrevio) {
        $archivoPrevio = $estudioPrevio->archivos->first();
        $estudioPrevioData = [
            'id' => $estudioPrevio->id,
            'fecha' => $estudioPrevio->fecha_estudio->format('d/m/Y - H:i'),
            'fecha_corta' => $estudioPrevio->fecha_estudio->format('d/m/Y'),
            'tipo' => $estudioPrevio->tipoEstudio->nombre ?? 'Estudio Previo',
            'especialidad' => $estudioPrevio->tipoEstudio->especialidad->nombre ?? 'Especialidad',
            'modalidad' => $estudioPrevio->modalidad ?? 'CR',
            'informe' => $estudioPrevio->informe,
            'informe_paciente' => $estudioPrevio->informe_paciente,
            'semaforo' => $estudioPrevio->semaforo_salud?->value ?? 'favorable',
            'url' => $archivoPrevio ? route('medico.estudios.archivos.ver', [$estudioPrevio->id, $archivoPrevio->id]) : null,
            'es_imagen' => $archivoPrevio ? str_starts_with($archivoPrevio->mime_type ?? '', 'image/') : false,
        ];
    }
@endphp

<div x-data="{
    tieneDicom: {{ $estudio->tieneDicom() ? 'true' : 'false' }},
    visorModo: '{{ $estudio->tieneDicom() ? 'dicom' : 'archivos' }}',
    archivos: {{ json_encode($archivosData) }},
    activo: 0,
    
    // Modo comparativo lado a lado (1x2)
    modoComparativo: {{ $estudioPrevio ? 'true' : 'false' }},
    estudioPrevioSeleccionado: {{ json_encode($estudioPrevioData) }},
    sincronizarFiltros: true,
    
    // Controles visor actual (Izquierda)
    zoom: 1,
    rotacion: 0,
    brillo: 100,
    contraste: 100,
    invertido: false,
    presetActivo: 'normal',

    // Controles visor previo (Derecha)
    zoomPrevio: 1,
    rotacionPrevio: 0,
    brilloPrevio: 100,
    contrastePrevio: 100,
    invertidoPrevio: false,
    presetActivoPrevio: 'normal',

    reglaActiva: false,
    mostrarOverlay: true,
    modalRechazo: false,
    panelCrucesAbierto: false,
    
    // Pestaña del editor de informe dual
    pestanaInforme: 'tecnico',
    semaforoSalud: '{{ $estudio->semaforo_salud?->value ?? 'favorable' }}',

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

        if (this.sincronizarFiltros) {
            this.brilloPrevio = this.brillo;
            this.contrastePrevio = this.contraste;
            this.invertidoPrevio = this.invertido;
            this.presetActivoPrevio = this.presetActivo;
        }
    },

    resetFiltros() {
        this.zoom = 1;
        this.rotacion = 0;
        this.brillo = 100;
        this.contraste = 100;
        this.invertido = false;
        this.presetActivo = 'normal';

        if (this.sincronizarFiltros) {
            this.zoomPrevio = 1;
            this.rotacionPrevio = 0;
            this.brilloPrevio = 100;
            this.contrastePrevio = 100;
            this.invertidoPrevio = false;
            this.presetActivoPrevio = 'normal';
        }
    },

    zoomIn() {
        this.zoom = Math.min(this.zoom + 0.25, 3.5);
        if (this.sincronizarFiltros) this.zoomPrevio = this.zoom;
    },
    zoomOut() {
        this.zoom = Math.max(this.zoom - 0.25, 0.5);
        if (this.sincronizarFiltros) this.zoomPrevio = this.zoom;
    },
    rotar() {
        this.rotacion = (this.rotacion + 90) % 360;
        if (this.sincronizarFiltros) this.rotacionPrevio = this.rotacion;
    },

    cargarEstudioEnComparativa(item) {
        this.estudioPrevioSeleccionado = {
            id: item.id,
            fecha: item.fecha_hora,
            fecha_corta: item.fecha,
            tipo: item.tipo_estudio,
            especialidad: item.especialidad,
            modalidad: item.modalidad,
            informe: item.informe,
            informe_paciente: item.informe_paciente,
            semaforo: item.semaforo_salud,
            url: item.miniatura_url,
            es_imagen: true
        };
        this.modoComparativo = true;
    },

    // ESTADO Y MÉTODOS DEL DICTADO POR VOZ (WHISPER LOCAL ON-PREMISE)
    dictando: false,
    procesandoAudio: false,
    segundosDictado: 0,
    timerDictado: null,
    mediaRecorder: null,
    audioChunks: [],
    whisperEstado: 'verificando',
    mensajeDictado: '',
    errorDictado: null,
    campoActivoDictado: 'campoInforme',

    async verificarWhisper() {
        try {
            const resp = await fetch('{{ route('medico.dictado.estado') }}');
            const data = await resp.json();
            this.whisperEstado = data.activo ? 'activo' : 'inactivo';
        } catch (e) {
            this.whisperEstado = 'inactivo';
        }
    },

    async toggleDictado(campoRef = 'campoInforme') {
        if (this.dictando) {
            this.detenerDictado(campoRef);
        } else {
            await this.iniciarDictado(campoRef);
        }
    },

    async iniciarDictado(campoRef = 'campoInforme') {
        this.errorDictado = null;
        this.campoActivoDictado = campoRef;

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            this.errorDictado = 'Tu navegador no permite capturar audio de micrófono.';
            return;
        }

        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            this.audioChunks = [];
            this.mediaRecorder = new MediaRecorder(stream);

            this.mediaRecorder.ondataavailable = (event) => {
                if (event.data && event.data.size > 0) {
                    this.audioChunks.push(event.data);
                }
            };

            this.mediaRecorder.onstop = async () => {
                const audioBlob = new Blob(this.audioChunks, { type: 'audio/webm' });
                stream.getTracks().forEach(track => track.stop());
                await this.enviarAudioWhisper(audioBlob, campoRef);
            };

            this.mediaRecorder.start();
            this.dictando = true;
            this.segundosDictado = 0;
            this.timerDictado = setInterval(() => {
                this.segundosDictado++;
            }, 1000);

        } catch (err) {
            console.error('Error micrófono:', err);
            this.errorDictado = 'No se pudo acceder al micrófono. Por favor habilite los permisos en el navegador.';
        }
    },

    detenerDictado(campoRef = 'campoInforme') {
        if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {
            this.mediaRecorder.stop();
        }
        this.dictando = false;
        clearInterval(this.timerDictado);
    },

    cancelarDictado() {
        if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {
            this.mediaRecorder.ondataavailable = null;
            this.mediaRecorder.onstop = null;
            this.mediaRecorder.stop();
        }
        this.dictando = false;
        this.procesandoAudio = false;
        clearInterval(this.timerDictado);
        this.audioChunks = [];
    },

    async enviarAudioWhisper(audioBlob, campoRef = 'campoInforme') {
        this.procesandoAudio = true;
        this.mensajeDictado = 'Procesando dictado con Whisper Local...';

        const formData = new FormData();
        formData.append('audio', audioBlob, 'dictado.webm');
        formData.append('prompt', '{{ $estudio->tipoEstudio->nombre ?? 'Estudio' }}, {{ $estudio->region_anatomica_efectiva ?? 'general' }}');

        try {
            const response = await fetch('{{ route('medico.dictado.transcribir') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok && data.exito) {
                const textoTranscrito = data.texto ? data.texto.trim() : '';
                if (textoTranscrito) {
                    const el = this.$refs[campoRef];
                    if (el) {
                        const inicio = el.selectionStart || 0;
                        const fin = el.selectionEnd || 0;
                        const textoPrevio = el.value || '';
                        
                        const separador = (textoPrevio.length > 0 && !textoPrevio.endsWith(' ') && !textoPrevio.endsWith('\n')) ? ' ' : '';
                        const nuevoTexto = textoPrevio.substring(0, inicio) + separador + textoTranscrito + textoPrevio.substring(fin);
                        el.value = nuevoTexto;
                        el.dispatchEvent(new Event('input'));
                        el.focus();
                        const nuevaPos = inicio + separador.length + textoTranscrito.length;
                        el.setSelectionRange(nuevaPos, nuevaPos);
                    }
                }
                this.mensajeDictado = `Transcripción completada (${data.duracion_audio}s en ${data.tiempo_procesamiento_seg}s)`;
                setTimeout(() => { this.mensajeDictado = ''; }, 4500);
            } else {
                this.errorDictado = data.error || 'Error al transcribir el dictado.';
            }
        } catch (e) {
            console.error('Error enviando dictado:', e);
            this.errorDictado = 'No se pudo conectar con el microservicio local de Whisper.';
        } finally {
            this.procesandoAudio = false;
        }
    },

    formatoTiempo(segundos) {
        const m = Math.floor(segundos / 60).toString().padStart(2, '0');
        const s = (segundos % 60).toString().padStart(2, '0');
        return `${m}:${s}`;
    }
}" x-init="verificarWhisper()" class="space-y-4">

    {{-- BARRA SUPERIOR: METADATOS Y ACCIONES PRINCIPALES --}}
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3 bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('medico.estudios.index') }}" 
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Volver a Worklist
            </a>
            <div class="h-5 w-px bg-slate-200 hidden sm:block"></div>
            <div>
                <h2 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2 flex-wrap">
                    <span>Estudio #{{ $estudio->id }}: {{ $estudio->tipoEstudio->nombre ?? 'Estudio' }}</span>
                    @if($estudio->tieneDicom())
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-[10px] font-black bg-indigo-50 text-indigo-700 border border-indigo-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                            DICOM PACS
                        </span>
                        @if($estudio->modalidad)
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-700">
                                {{ $estudio->modalidad }}
                            </span>
                        @endif
                    @endif
                    @if($estudio->region_anatomica_efectiva)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200 uppercase">
                            🎯 Región: {{ $estudio->region_anatomica_efectiva }}
                        </span>
                    @endif
                </h2>
                <p class="text-xs text-slate-500">
                    Paciente: <strong class="text-slate-800">{{ $estudio->paciente_nombre }}</strong> (DNI: {{ $estudio->paciente_dni }}) — {{ $estudio->paciente_edad }} años
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 text-xs flex-wrap">
            {{-- Botón para activar o desactivar la vista comparativa (1x2) --}}
            <button type="button" @click="modoComparativo = !modoComparativo"
                    :class="modoComparativo ? 'bg-indigo-600 text-white shadow-sm ring-2 ring-indigo-500/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold transition">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="8" height="18" rx="1"/><rect x="13" y="3" width="8" height="18" rx="1"/></svg>
                <span x-text="modoComparativo ? 'Vista Simple' : 'Comparar Lado a Lado (1x2)'"></span>
                @if($lineaTiempo->count() > 1)
                    <span class="ml-0.5 px-1.5 py-0.2 rounded-full text-[10px] font-mono bg-white/20 text-current">
                        {{ $lineaTiempo->count() - 1 }} previo(s)
                    </span>
                @endif
            </button>

            @if($estudio->tieneDicom() && $viewerUrl)
                <a href="{{ $viewerUrl }}" target="_blank" 
                   style="background-color: #4f46e5; color: #ffffff;"
                   title="Abrir este estudio en la consola web oficial de dcm4chee"
                   class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-sm">
                    <svg width="14" height="14" style="width: 14px; height: 14px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>Abrir en dcm4chee</span>
                </a>
            @endif

            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold border {{ $estudio->estado->badgeClasses() }}">
                {{ $estudio->estado->label() }}
            </span>
        </div>
    </div>

    {{-- BANNER DE ALERTAS CLÍNICAS MULTIDISCIPLINARIAS --}}
    @if(!empty($cruceMultidisciplinario['alertas']))
        <div class="space-y-2">
            @foreach($cruceMultidisciplinario['alertas'] as $alerta)
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/90 rounded-xl p-3 text-xs text-amber-900 flex items-start gap-3 shadow-sm">
                    <span class="text-base select-none shrink-0">{{ $alerta['icono'] ?? '⚠️' }}</span>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <strong class="font-bold text-amber-950">{{ $alerta['titulo'] }}</strong>
                            <span class="text-[10px] font-mono text-amber-700">{{ $alerta['fecha'] ?? '' }}</span>
                        </div>
                        <p class="mt-0.5 text-amber-800 leading-relaxed">{{ $alerta['mensaje'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- GRID PRINCIPAL: VISOR CLÍNICO (IZQUIERDA) Y REDACCIÓN DUAL (DERECHA) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

        {{-- PANEL IZQUIERDO: VISOR CLÍNICO DUAL Y LÍNEA DE TIEMPO (7 DE 12 COLUMNAS) --}}
        <div class="lg:col-span-7 xl:col-span-8 flex flex-col space-y-3">
            
            {{-- TARJETA DEL VISOR --}}
            <div class="bg-slate-950 rounded-xl border border-slate-800 shadow-md overflow-hidden flex flex-col"
                 style="position: relative; min-height: 600px; height: 680px; background-color: #020617;">
                
                {{-- Barra de Herramientas Superior del Visor --}}
                <div class="bg-slate-900 text-white px-3.5 py-2 flex flex-wrap items-center justify-between gap-2 shrink-0 border-b border-slate-800 select-none text-xs">
                    
                    {{-- Modo DICOM / Archivos --}}
                    <div class="flex items-center gap-2">
                        @if($estudio->tieneDicom())
                            <div class="flex items-center bg-slate-800 p-0.5 rounded-lg text-xs font-bold">
                                <button type="button" @click="visorModo = 'dicom'" 
                                        :class="visorModo === 'dicom' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white'"
                                        class="px-2.5 py-1 rounded-md transition-colors flex items-center gap-1">
                                    <span>📡 DICOM PACS</span>
                                </button>
                                @if(count($archivosData) > 0)
                                    <button type="button" @click="visorModo = 'archivos'" 
                                            :class="visorModo === 'archivos' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white'"
                                            class="px-2 py-1 rounded-md transition-colors">
                                        Adjuntos ({{ count($archivosData) }})
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Presets de Ventana Radiológica (W/L) --}}
                    <div class="flex items-center gap-1">
                        <span class="text-[10px] text-slate-400 font-bold uppercase mr-1 hidden sm:inline">Ventana (W/L):</span>
                        <button type="button" @click="aplicarPreset('normal')" 
                                :class="presetActivo === 'normal' ? 'bg-slate-700 text-white font-bold' : 'text-slate-400 hover:text-slate-200'"
                                class="px-2 py-1 rounded text-xs transition">Normal</button>
                        <button type="button" @click="aplicarPreset('hueso')" 
                                :class="presetActivo === 'hueso' ? 'bg-slate-700 text-cyan-300 font-bold' : 'text-slate-400 hover:text-slate-200'"
                                class="px-2 py-1 rounded text-xs transition" title="Preset ventana ósea">🦴 Hueso</button>
                        <button type="button" @click="aplicarPreset('tejido')" 
                                :class="presetActivo === 'tejido' ? 'bg-slate-700 text-cyan-300 font-bold' : 'text-slate-400 hover:text-slate-200'"
                                class="px-2 py-1 rounded text-xs transition" title="Preset partes blandas">🫀 Tejido</button>
                        <button type="button" @click="aplicarPreset('pulmon')" 
                                :class="presetActivo === 'pulmon' ? 'bg-slate-700 text-cyan-300 font-bold' : 'text-slate-400 hover:text-slate-200'"
                                class="px-2 py-1 rounded text-xs transition" title="Preset ventana pulmonar">🫁 Pulmón</button>
                        <button type="button" @click="aplicarPreset('invertir')" 
                                :class="invertido ? 'bg-amber-500 text-black font-bold' : 'text-slate-400 hover:text-slate-200'"
                                class="px-2 py-1 rounded text-xs transition" title="Invertir contraste">🌓 Invertir</button>
                    </div>

                    {{-- Herramientas de navegación: Zoom, Rotación, Regla e Info --}}
                    <div class="flex items-center gap-1">
                        <button type="button" @click="zoomIn()" title="Acercar (+)" class="p-1 rounded hover:bg-slate-800 text-slate-300 hover:text-white transition">🔍+</button>
                        <button type="button" @click="zoomOut()" title="Alejar (-)" class="p-1 rounded hover:bg-slate-800 text-slate-300 hover:text-white transition">🔍-</button>
                        <button type="button" @click="rotar()" title="Rotar 90°" class="p-1 rounded hover:bg-slate-800 text-slate-300 hover:text-white transition">🔄</button>
                        <button type="button" @click="resetFiltros()" title="Restablecer 1:1" class="px-1.5 py-1 rounded hover:bg-slate-800 text-slate-300 hover:text-white transition text-[10px] font-bold">1:1</button>
                        <button type="button" @click="reglaActiva = !reglaActiva" 
                                :class="reglaActiva ? 'bg-amber-600 text-white' : 'hover:bg-slate-800 text-slate-300'"
                                title="Regla milimétrica" class="p-1 rounded transition">📏</button>
                        <button type="button" @click="mostrarOverlay = !mostrarOverlay" 
                                :class="mostrarOverlay ? 'bg-slate-700 text-cyan-400 border border-cyan-500/30' : 'text-slate-500 hover:text-white'"
                                title="Mostrar/ocultar metadatos en esquinas" class="px-1.5 py-1 rounded transition text-[10px] font-mono font-bold">INFO</button>
                    </div>
                </div>

                {{-- CONTENIDO DEL VISOR: MODO SIMPLE O COMPARATIVO LADO A LADO (1x2) --}}
                <div class="flex-1 bg-black relative overflow-hidden flex items-stretch"
                     style="position: relative; height: 100%; min-height: 520px; background-color: #000000; display: flex; flex-direction: row;">
                    
                    {{-- PANEL 1: ESTUDIO ACTUAL (IZQUIERDA) --}}
                    <div :class="modoComparativo ? 'w-1/2 border-r border-slate-800' : 'w-full'"
                         class="relative h-full flex items-center justify-center overflow-hidden transition-all duration-200"
                         style="position: relative; height: 100%; overflow: hidden; background-color: #000000;"
                         :style="modoComparativo ? 'width: 50%; flex: 0 0 50%; max-width: 50%;' : 'width: 100%; flex: 1 1 100%; max-width: 100%;'">
                        
                        {{-- Identificador de panel --}}
                        <div class="absolute top-2.5 left-2.5 z-30 pointer-events-none"
                             style="position: absolute; top: 10px; left: 10px; z-index: 30;">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/90 text-black uppercase tracking-wider">
                                ESTUDIO ACTUAL ({{ $estudio->fecha_estudio->format('d/m/Y') }})
                            </span>
                        </div>

                        {{-- Metadatos flotantes en las esquinas --}}
                        <div x-show="mostrarOverlay" x-cloak
                             style="position: absolute; top: 38px; left: 10px; z-index: 20; background: rgba(0,0,0,0.8); color: #22d3ee; padding: 4px 8px; border-radius: 6px; font-family: monospace; font-size: 10px; line-height: 1.3; pointer-events: none; border: 1px solid rgba(34,211,238,0.25);">
                            <p class="font-bold text-white">{{ strtoupper($estudio->paciente_nombre) }}</p>
                            <p>MOD: {{ $estudio->modalidad ?? 'CR' }} | ID: {{ $estudio->paciente_dni }}</p>
                        </div>

                        <div x-show="mostrarOverlay" x-cloak
                             style="position: absolute; bottom: 10px; left: 10px; z-index: 20; background: rgba(0,0,0,0.8); color: #fcd34d; padding: 4px 8px; border-radius: 6px; font-family: monospace; font-size: 9px; pointer-events: none; border: 1px solid rgba(251,191,36,0.25);">
                            <p>W/L: <span x-text="contraste"></span>/<span x-text="brillo"></span> | Zoom: <span x-text="Math.round(zoom * 100) + '%'"></span></p>
                        </div>

                        {{-- Regla diagnóstica milimétrica --}}
                        <div x-show="reglaActiva" x-cloak 
                             style="position: absolute; z-index: 20; left: 50%; top: 50%; transform: translate(-50%, -50%); pointer-events: none; background: rgba(251, 191, 36, 0.95); color: #000; padding: 4px 12px; border-radius: 6px; font-family: monospace; font-size: 11px; font-weight: bold; border: 1px solid #000;">
                            <span>├────────── 50 mm ──────────┤</span>
                        </div>

                        {{-- Placa o imagen médica actual --}}
                        <div class="w-full h-full flex items-center justify-center p-2"
                             style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; padding: 8px;">
                            @if(count($archivosData) > 0)
                                <img :src="archivos[0]?.url"
                                     :style="`transform: scale(${zoom}) rotate(${rotacion}deg); filter: contrast(${contraste}%) brightness(${brillo}%) ${invertido ? 'invert(1)' : ''}; transition: filter 0.15s ease, transform 0.15s ease; max-height: 94%; max-width: 94%; width: auto; height: auto; object-fit: contain;`"
                                     class="select-none shadow-2xl rounded"
                                     alt="Placa Estudio Actual" />
                            @else
                                <div class="text-center p-6 text-slate-500">
                                    <p class="text-sm font-bold text-slate-300">Estudio DICOM PACS</p>
                                    <p class="text-xs font-mono text-slate-400 mt-1">UID: {{ substr($estudio->study_instance_uid, 0, 25) }}...</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- PANEL 2: ESTUDIO PREVIO COMPARATIVO (DERECHA, MODO 1x2) --}}
                    <div x-show="modoComparativo" x-cloak
                         class="w-1/2 relative h-full flex items-center justify-center overflow-hidden bg-slate-950"
                         style="position: relative; height: 100%; overflow: hidden; background-color: #020617; width: 50%; flex: 0 0 50%; max-width: 50%;">
                        
                        <template x-if="estudioPrevioSeleccionado">
                            <div class="w-full h-full relative flex items-center justify-center"
                                 style="position: relative; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                                
                                {{-- Cabecera del panel previo con selector rápido --}}
                                <div class="absolute top-2.5 left-2.5 right-2.5 z-30 flex items-center justify-between pointer-events-none"
                                     style="position: absolute; top: 10px; left: 10px; right: 10px; z-index: 30; display: flex; justify-content: space-between;">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/90 text-black uppercase tracking-wider">
                                        ESTUDIO PREVIO: <span x-text="estudioPrevioSeleccionado.fecha_corta"></span>
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-800 text-slate-200 border border-slate-700"
                                          x-text="estudioPrevioSeleccionado.especialidad + ' (' + estudioPrevioSeleccionado.modalidad + ')'">
                                    </span>
                                </div>

                                {{-- Metadatos flotantes del estudio previo --}}
                                <div x-show="mostrarOverlay" x-cloak
                                     style="position: absolute; top: 38px; left: 10px; z-index: 20; background: rgba(0,0,0,0.8); color: #fbbf24; padding: 4px 8px; border-radius: 6px; font-family: monospace; font-size: 10px; line-height: 1.3; pointer-events: none; border: 1px solid rgba(251,191,36,0.25);">
                                    <p class="font-bold text-white" x-text="estudioPrevioSeleccionado.tipo"></p>
                                    <p x-text="estudioPrevioSeleccionado.fecha"></p>
                                </div>

                                <div x-show="mostrarOverlay" x-cloak
                                     style="position: absolute; bottom: 10px; right: 10px; z-index: 20; background: rgba(0,0,0,0.8); color: #94a3b8; padding: 4px 8px; border-radius: 6px; font-family: monospace; font-size: 9px; pointer-events: none; text-align: right; border: 1px solid rgba(148,163,184,0.2);">
                                    <p class="text-amber-400 font-bold">● Comparativa Histórica</p>
                                    <p>Zoom: <span x-text="Math.round(zoomPrevio * 100) + '%'"></span></p>
                                </div>

                                {{-- Placa del estudio previo --}}
                                <div class="w-full h-full flex items-center justify-center p-2"
                                     style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; padding: 8px;">
                                    <template x-if="estudioPrevioSeleccionado.url">
                                        <img :src="estudioPrevioSeleccionado.url"
                                             :style="`transform: scale(${zoomPrevio}) rotate(${rotacionPrevio}deg); filter: contrast(${contrastePrevio}%) brightness(${brilloPrevio}%) ${invertidoPrevio ? 'invert(1)' : ''}; transition: filter 0.15s ease, transform 0.15s ease; max-height: 94%; max-width: 94%; width: auto; height: auto; object-fit: contain;`"
                                             class="select-none shadow-2xl rounded"
                                             alt="Placa Comparativa Previa" />
                                    </template>
                                    <template x-if="!estudioPrevioSeleccionado.url">
                                        <div class="text-center p-6 text-slate-500">
                                            <p class="text-sm font-bold text-slate-300" x-text="estudioPrevioSeleccionado.tipo"></p>
                                            <p class="text-xs text-slate-400 mt-1" x-text="'Informe previo: ' + (estudioPrevioSeleccionado.informe ? estudioPrevioSeleccionado.informe.substring(0, 80) + '...' : 'Sin archivo adjunto')"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template x-if="!estudioPrevioSeleccionado">
                            <div class="text-center p-8 text-slate-500">
                                <p class="text-xs text-slate-400">Este paciente no registra estudios previos cargados.</p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- LÍNEA DE TIEMPO DEL PACIENTE (TIMELINE MULTIDISCIPLINARIO HORIZONTAL) --}}
            <div class="bg-white rounded-xl border border-slate-200/80 p-3 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Línea de Tiempo del Paciente (Timeline Clínico)
                    </h4>
                    <span class="text-[10px] text-slate-400">
                        {{ $lineaTiempo->count() }} estudio(s) registrado(s) para este paciente
                    </span>
                </div>

                {{-- Tira deslizante de estudios del paciente --}}
                <div class="flex items-center gap-2.5 overflow-x-auto pb-1.5 scrollbar-thin">
                    @forelse($lineaTiempo as $item)
                        <button type="button" 
                                @click="cargarEstudioEnComparativa({{ json_encode($item) }})"
                                :class="estudioPrevioSeleccionado && estudioPrevioSeleccionado.id === {{ $item['id'] }} ? 'border-amber-500 bg-amber-50/70 shadow-sm' : ({{ $item['es_actual'] ? 'true' : 'false' }} ? 'border-emerald-500 bg-emerald-50/50' : 'border-slate-200 bg-slate-50/60 hover:bg-slate-100')"
                                class="shrink-0 w-52 p-2.5 rounded-lg border text-left transition-all cursor-pointer group">
                            
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-mono font-bold text-slate-500">{{ $item['fecha'] }}</span>
                                @if($item['es_actual'])
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-emerald-600 text-white">ACTUAL</span>
                                @else
                                    <span class="text-[10px]">{{ $item['semaforo_icono'] ?? '🟢' }}</span>
                                @endif
                            </div>

                            <p class="text-xs font-bold text-slate-800 truncate" title="{{ $item['tipo_estudio'] }}">{{ $item['tipo_estudio'] }}</p>
                            <p class="text-[10px] text-indigo-700 font-semibold">{{ $item['especialidad'] }} · {{ $item['modalidad'] }}</p>

                            @if(!$item['es_actual'])
                                <div class="mt-1 pt-1 border-t border-slate-200/60 flex items-center justify-between text-[10px] text-slate-500 group-hover:text-indigo-600">
                                    <span>Comparar en visor</span>
                                    <span>👉</span>
                                </div>
                            @endif
                        </button>
                    @empty
                        <p class="text-xs text-slate-400 italic py-2">No hay otros estudios históricos registrados para este paciente.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- PANEL DERECHO: METADATOS CLÍNICOS, CRUCES Y REDACCIÓN DUAL (5 DE 12 COLUMNAS) --}}
        <div class="lg:col-span-5 xl:col-span-4 space-y-4">

            {{-- FICHA DEL PACIENTE Y CRUCE MULTIDISCIPLINARIO --}}
            <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Ficha del Paciente
                    </h3>
                    <span class="text-[10px] font-mono text-slate-400">DNI: {{ $estudio->paciente_dni }}</span>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs text-slate-600 bg-slate-50 p-2.5 rounded-lg border border-slate-200/60">
                    <div><strong>Paciente:</strong> <span class="text-slate-900 block font-semibold truncate">{{ $estudio->paciente_nombre }}</span></div>
                    <div><strong>Edad:</strong> <span class="text-slate-900 block">{{ $estudio->paciente_edad }} años</span></div>
                    <div><strong>Fecha:</strong> <span class="text-slate-900 block">{{ $estudio->fecha_estudio->format('d/m/Y H:i') }}</span></div>
                    <div><strong>Técnico:</strong> <span class="text-slate-900 block truncate">{{ $estudio->tecnico_nombre }}</span></div>
                </div>

                {{-- Acordeón colapsable con antecedentes en otras especialidades --}}
                @if(!empty($cruceMultidisciplinario['antecedentes']))
                    <div class="pt-1">
                        <button type="button" @click="panelCrucesAbierto = !panelCrucesAbierto"
                                class="w-full flex items-center justify-between py-1.5 px-2.5 rounded-lg text-xs font-bold bg-indigo-50 hover:bg-indigo-100 text-indigo-900 transition-colors">
                            <span class="flex items-center gap-1.5">
                                <span>🧬 Cruce Multidisciplinario ({{ $cruceMultidisciplinario['total_estudios_cruzados'] }})</span>
                            </span>
                            <span x-text="panelCrucesAbierto ? '▲ Cerrar' : '▼ Ver Antecedentes'"></span>
                        </button>

                        <div x-show="panelCrucesAbierto" x-cloak class="mt-2 space-y-2 max-h-60 overflow-y-auto pr-1">
                            @foreach($cruceMultidisciplinario['antecedentes'] as $esp => $estudiosEsp)
                                <div class="border border-slate-200 rounded-lg p-2.5 bg-slate-50/50 text-xs">
                                    <div class="font-bold text-indigo-950 flex items-center justify-between border-b border-slate-200 pb-1 mb-1.5">
                                        <span>{{ $esp }}</span>
                                        <span class="text-[10px] text-slate-500">{{ count($estudiosEsp) }} estudio(s)</span>
                                    </div>
                                    @foreach($estudiosEsp as $ant)
                                        <div class="text-[11px] space-y-1 mb-2 last:mb-0">
                                            <div class="flex items-center justify-between text-slate-700">
                                                <strong>{{ $ant['estudio'] }}</strong>
                                                <span class="font-mono text-slate-400 text-[10px]">{{ $ant['fecha'] }}</span>
                                            </div>
                                            <p class="text-slate-600 bg-white p-1.5 rounded border border-slate-100 leading-snug">
                                                {{ $ant['informe'] }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- REDACCIÓN DEL INFORME DUAL (OFICIAL MÉDICO + EXPLICATIVO PARA EL PACIENTE) --}}
            <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm p-4 space-y-4">
                <form method="POST" action="{{ route('medico.estudios.informar.guardar', $estudio->id) }}" class="space-y-4">
                    @csrf

                    {{-- Selector de Pestañas: Informe Técnico vs. Para el Paciente --}}
                    <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                        <div class="flex items-center gap-1">
                            <button type="button" @click="pestanaInforme = 'tecnico'"
                                    :class="pestanaInforme === 'tecnico' ? 'bg-slate-900 text-white font-bold' : 'text-slate-600 hover:text-slate-900'"
                                    class="px-3 py-1.5 rounded-lg text-xs transition cursor-pointer">
                                📋 Informe Médico Oficial
                            </button>
                            <button type="button" @click="pestanaInforme = 'paciente'"
                                    :class="pestanaInforme === 'paciente' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-600 hover:text-slate-900'"
                                    class="px-3 py-1.5 rounded-lg text-xs transition flex items-center gap-1 cursor-pointer">
                                <span>📈 Evolución del Paciente</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            </button>
                        </div>
                        <span class="text-[10px] text-slate-400">Dr/a. {{ auth()->user()->name }}</span>
                    </div>

                    {{-- PESTAÑA 1: INFORME MÉDICO OFICIAL (TÉCNICO) --}}
                    <div x-show="pestanaInforme === 'tecnico'" class="space-y-2">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Diagnóstico Radiológico Formal
                            </label>

                            {{-- HERRAMIENTA DE DICTADO POR VOZ (WHISPER LOCAL) --}}
                            <div class="flex items-center gap-1.5">
                                <template x-if="whisperEstado === 'activo'">
                                    <div class="flex items-center gap-1.5">
                                        {{-- Botón iniciar dictado --}}
                                        <button type="button" 
                                                x-show="!dictando && !procesandoAudio" 
                                                @click="toggleDictado('campoInforme')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 transition-all shadow-sm cursor-pointer group"
                                                title="Dictar diagnóstico con Whisper Local (presione para comenzar)">
                                            <svg class="w-3.5 h-3.5 text-indigo-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                                            <span>🎙️ Dictar por Voz</span>
                                            <span class="px-1 py-0.2 rounded text-[9px] font-mono font-bold bg-indigo-200 text-indigo-800">Local AI</span>
                                        </button>

                                        {{-- Estado grabando --}}
                                        <div x-show="dictando && campoActivoDictado === 'campoInforme'" x-cloak class="flex items-center gap-1.5 bg-rose-50 border border-rose-300 px-2.5 py-1 rounded-lg text-xs font-bold text-rose-700 shadow-sm animate-pulse">
                                            <span class="w-2.5 h-2.5 rounded-full bg-rose-600 animate-ping"></span>
                                            <span>Grabando: <span x-text="formatoTiempo(segundosDictado)"></span></span>
                                            <button type="button" 
                                                    @click="detenerDictado('campoInforme')" 
                                                    class="ml-1 bg-rose-600 hover:bg-rose-700 text-white px-2 py-0.5 rounded text-[11px] font-bold transition cursor-pointer">
                                                ⏹️ Detener
                                            </button>
                                            <button type="button" 
                                                    @click="cancelarDictado()" 
                                                    class="text-rose-400 hover:text-rose-800 ml-1 text-xs cursor-pointer" 
                                                    title="Descartar audio sin transcribir">
                                                ✕
                                            </button>
                                        </div>

                                        {{-- Estado procesando --}}
                                        <div x-show="procesandoAudio && campoActivoDictado === 'campoInforme'" x-cloak class="flex items-center gap-1.5 bg-amber-50 border border-amber-300 px-2.5 py-1 rounded-lg text-xs font-bold text-amber-800 shadow-sm">
                                            <svg class="animate-spin h-3.5 w-3.5 text-amber-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            <span>Transcribiendo con Whisper Local...</span>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="whisperEstado === 'inactivo'">
                                    <span class="inline-flex items-center gap-1 text-[10px] text-slate-400" title="Inicie servicios/whisper/iniciar_dictado.bat para habilitar dictado">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                        Dictado desconectado
                                    </span>
                                </template>
                            </div>
                        </div>

                        {{-- Mensajes de estado del dictado --}}
                        <div x-show="mensajeDictado && campoActivoDictado === 'campoInforme'" x-cloak class="text-[11px] text-emerald-800 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-lg flex items-center justify-between shadow-xs">
                            <span class="flex items-center gap-1.5 font-medium">
                                <span>✨</span>
                                <span x-text="mensajeDictado"></span>
                            </span>
                            <button type="button" @click="mensajeDictado = ''" class="text-emerald-500 hover:text-emerald-800 text-sm font-bold">&times;</button>
                        </div>
                        <div x-show="errorDictado && campoActivoDictado === 'campoInforme'" x-cloak class="text-[11px] text-rose-800 bg-rose-50 border border-rose-200 px-3 py-1.5 rounded-lg flex items-center justify-between shadow-xs">
                            <span class="flex items-center gap-1.5 font-medium">
                                <span>⚠️</span>
                                <span x-text="errorDictado"></span>
                            </span>
                            <button type="button" @click="errorDictado = null" class="text-rose-500 hover:text-rose-800 text-sm font-bold">&times;</button>
                        </div>

                        <textarea x-ref="campoInforme" name="informe" rows="10" required minlength="3"
                                  placeholder="Describa aquí los hallazgos radiológicos observados, mediciones, conclusión diagnóstica y sugerencias clínicas con terminología médica formal (puede escribir o usar el botón '🎙️ Dictar por Voz')..."
                                  class="w-full border border-slate-300 rounded-xl p-3.5 text-xs text-slate-800 leading-relaxed focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition-all shadow-inner">{{ old('informe', $estudio->informe) }}</textarea>
                        <p class="text-[11px] text-slate-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-sky-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Documento con validez legal médica y destino a médicos derivantes y especialistas.</span>
                        </p>
                    </div>

                    {{-- PESTAÑA 2: EXPLICACIÓN PARA EL PACIENTE Y SEMÁFORO DE SALUD --}}
                    <div x-show="pestanaInforme === 'paciente'" x-cloak class="space-y-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Semáforo de Evolución y Estado de Salud
                            </label>
                            
                            {{-- Selector visual del Semáforo --}}
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <label class="border rounded-lg p-2.5 flex items-center gap-2 cursor-pointer transition"
                                       :class="semaforoSalud === 'favorable' ? 'border-emerald-500 bg-emerald-50/70 font-bold text-emerald-900 ring-1 ring-emerald-500' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                                    <input type="radio" name="semaforo_salud" value="favorable" x-model="semaforoSalud" class="hidden">
                                    <span class="text-base">🟢</span>
                                    <div>
                                        <p class="font-bold">Favorable / Normal</p>
                                        <p class="text-[10px] text-slate-500">Sin hallazgos de alarma</p>
                                    </div>
                                </label>

                                <label class="border rounded-lg p-2.5 flex items-center gap-2 cursor-pointer transition"
                                       :class="semaforoSalud === 'estable' ? 'border-sky-500 bg-sky-50/70 font-bold text-sky-900 ring-1 ring-sky-500' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                                    <input type="radio" name="semaforo_salud" value="estable" x-model="semaforoSalud" class="hidden">
                                    <span class="text-base">🔵</span>
                                    <div>
                                        <p class="font-bold">Estable sin cambios</p>
                                        <p class="text-[10px] text-slate-500">Igual a estudios previos</p>
                                    </div>
                                </label>

                                <label class="border rounded-lg p-2.5 flex items-center gap-2 cursor-pointer transition"
                                       :class="semaforoSalud === 'control_requerido' ? 'border-amber-500 bg-amber-50/70 font-bold text-amber-900 ring-1 ring-amber-500' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                                    <input type="radio" name="semaforo_salud" value="control_requerido" x-model="semaforoSalud" class="hidden">
                                    <span class="text-base">🟡</span>
                                    <div>
                                        <p class="font-bold">Control Periódico</p>
                                        <p class="text-[10px] text-slate-500">Seguimiento en 3/6 meses</p>
                                    </div>
                                </label>

                                <label class="border rounded-lg p-2.5 flex items-center gap-2 cursor-pointer transition"
                                       :class="semaforoSalud === 'atencion_inmediata' ? 'border-rose-500 bg-rose-50/70 font-bold text-rose-900 ring-1 ring-rose-500' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                                    <input type="radio" name="semaforo_salud" value="atencion_inmediata" x-model="semaforoSalud" class="hidden">
                                    <span class="text-base">🔴</span>
                                    <div>
                                        <p class="font-bold">Atención Inmediata</p>
                                        <p class="text-[10px] text-slate-500">Consulta prioritaria</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- COMPARATIVA TEMPORAL (EVOLUCIÓN) CON DICTADO POR VOZ --}}
                        <div>
                            <div class="flex items-center justify-between flex-wrap gap-2 mb-1.5">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                    Comparativa Temporal (Evolución)
                                </label>

                                {{-- HERRAMIENTA DE DICTADO PARA COMPARATIVA TEMPORAL --}}
                                <div class="flex items-center gap-1.5">
                                    <template x-if="whisperEstado === 'activo'">
                                        <div class="flex items-center gap-1.5">
                                            {{-- Botón iniciar dictado --}}
                                            <button type="button" 
                                                    x-show="!dictando && !procesandoAudio" 
                                                    @click="toggleDictado('campoResumenEvolucion')"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 transition-all shadow-xs cursor-pointer group"
                                                    title="Dictar evolución temporal con Whisper Local">
                                                <svg class="w-3.5 h-3.5 text-indigo-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                                                <span>🎙️ Dictar</span>
                                                <span class="px-1 py-0.2 rounded text-[9px] font-mono font-bold bg-indigo-200 text-indigo-800">Local AI</span>
                                            </button>

                                            {{-- Estado grabando --}}
                                            <div x-show="dictando && campoActivoDictado === 'campoResumenEvolucion'" x-cloak class="flex items-center gap-1.5 bg-rose-50 border border-rose-300 px-2.5 py-1 rounded-lg text-xs font-bold text-rose-700 shadow-sm animate-pulse">
                                                <span class="w-2.5 h-2.5 rounded-full bg-rose-600 animate-ping"></span>
                                                <span>Grabando evolución: <span x-text="formatoTiempo(segundosDictado)"></span></span>
                                                <button type="button" @click="detenerDictado('campoResumenEvolucion')" class="ml-1 bg-rose-600 hover:bg-rose-700 text-white px-2 py-0.5 rounded text-[11px] font-bold transition cursor-pointer">⏹️ Detener</button>
                                                <button type="button" @click="cancelarDictado()" class="text-rose-400 hover:text-rose-800 ml-1 text-xs cursor-pointer" title="Descartar audio">✕</button>
                                            </div>

                                            {{-- Estado procesando --}}
                                            <div x-show="procesandoAudio && campoActivoDictado === 'campoResumenEvolucion'" x-cloak class="flex items-center gap-1.5 bg-amber-50 border border-amber-300 px-2.5 py-1 rounded-lg text-xs font-bold text-amber-800 shadow-sm">
                                                <svg class="animate-spin h-3.5 w-3.5 text-amber-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                <span>Transcribiendo evolución...</span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Mensajes de estado del dictado --}}
                            <div x-show="mensajeDictado && campoActivoDictado === 'campoResumenEvolucion'" x-cloak class="text-[11px] text-emerald-800 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-lg mb-1.5 flex items-center justify-between shadow-xs">
                                <span class="flex items-center gap-1.5 font-medium">
                                    <span>✨</span>
                                    <span x-text="mensajeDictado"></span>
                                </span>
                                <button type="button" @click="mensajeDictado = ''" class="text-emerald-500 hover:text-emerald-800 text-sm font-bold">&times;</button>
                            </div>
                            <div x-show="errorDictado && campoActivoDictado === 'campoResumenEvolucion'" x-cloak class="text-[11px] text-rose-800 bg-rose-50 border border-rose-200 px-3 py-1.5 rounded-lg mb-1.5 flex items-center justify-between shadow-xs">
                                <span class="flex items-center gap-1.5 font-medium">
                                    <span>⚠️</span>
                                    <span x-text="errorDictado"></span>
                                </span>
                                <button type="button" @click="errorDictado = null" class="text-rose-500 hover:text-rose-800 text-sm font-bold">&times;</button>
                            </div>

                            <textarea x-ref="campoResumenEvolucion" name="resumen_evolucion" rows="5"
                                      placeholder="Describa la evolución temporal respecto a estudios previos (ej: Se observa reducción del derrame pleural respecto al control de 2025, sin nuevos focos parenquimatosos ni complicaciones agudas... Puede escribir o usar el botón '🎙️ Dictar')..."
                                      class="w-full border border-slate-300 rounded-xl p-3.5 text-xs text-slate-800 leading-relaxed focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition-all shadow-inner">{{ old('resumen_evolucion', $estudio->resumen_evolucion) }}</textarea>
                            <p class="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Resumen comparativo con el historial previo para seguimiento clínico y monitoreo de la patología.</span>
                            </p>
                        </div>
                    </div>

                    {{-- BOTONES SIMÉTRICOS CENTRADOS AL PIE (REHACER Y FIRMAR) --}}
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

    {{-- MODAL DE RECHAZO (REHACER / DEVOLVER AL TÉCNICO) --}}
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
