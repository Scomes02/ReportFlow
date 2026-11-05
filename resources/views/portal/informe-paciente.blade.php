<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReportFlow — Informe de Salud: {{ $estudio->paciente_nombre }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-shadow-none { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen py-8 px-4 sm:px-6 lg:px-8">

    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Barra de Acciones Superior (No imprimible) --}}
        <div class="no-print flex items-center justify-between bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="flex h-3 w-3 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                <span class="text-xs font-semibold text-slate-700">Informe Oficial Firmado &bull; Hospital Universitario (UNCuyo)</span>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="inline-flex items-center gap-2 bg-[#003764] hover:bg-[#002847] text-white px-4 py-2 rounded-xl text-xs font-bold shadow-sm transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Imprimir / Descargar PDF</span>
                </button>
                <a href="{{ url()->previous() }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 px-3 py-2">
                    Volver
                </a>
            </div>
        </div>

        {{-- Documento Principal --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm print-shadow-none overflow-hidden">
            
            {{-- Encabezado Institucional --}}
            <div class="bg-gradient-to-r from-[#003764] via-[#084b80] to-[#002647] p-8 text-white relative">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
                    <div>
                        <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-medium text-emerald-300 mb-3 border border-white/10">
                            <span>Hospital Universitario UNCuyo &bull; ReportFlow</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Informe de Diagnóstico Médico</h1>
                        <p class="text-xs text-slate-300 mt-1">Plataforma Integrada de Diagnóstico y Seguimiento Clínico</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm p-4 rounded-2xl border border-white/10 text-right sm:min-w-[200px]">
                        <span class="text-[10px] text-slate-300 uppercase tracking-widest block font-bold">Estudio N°</span>
                        <span class="text-xl font-black font-mono text-white tracking-wider">#{{ str_pad($estudio->id, 6, '0', STR_PAD_LEFT) }}</span>
                        <span class="text-[11px] text-slate-300 block mt-1 font-mono">{{ $estudio->fecha_estudio->format('d/m/Y - H:i') }} hs</span>
                    </div>
                </div>
            </div>

            {{-- Ficha del Paciente y Estudio --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-6 bg-slate-50/80 border-b border-slate-200 text-xs">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Paciente</span>
                    <span class="font-bold text-slate-900 text-sm block mt-0.5">{{ $estudio->paciente_nombre }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Documento / Edad</span>
                    <span class="font-semibold text-slate-800 font-mono text-sm block mt-0.5">DNI {{ $estudio->paciente_dni }} &bull; {{ $estudio->paciente_edad }} años</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Estudio Solicitado</span>
                    <span class="font-semibold text-slate-800 text-sm block mt-0.5">{{ $estudio->tipoEstudio->nombre ?? 'Estudio' }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Especialidad / Modalidad</span>
                    <span class="font-semibold text-slate-800 text-sm block mt-0.5">{{ $estudio->tipoEstudio->especialidad->nombre ?? 'Diagnóstico' }} ({{ $estudio->modalidad ?? 'CR' }})</span>
                </div>
            </div>

            <div class="p-8 space-y-8">

                {{-- SECCIÓN 1: SEMÁFORO DE EVOLUCIÓN DEL PACIENTE --}}
                @php
                    $semaforo = $estudio->semaforo_salud;
                    $valorSemaforo = $semaforo?->value ?? ($estudio->semaforo_evolucion ?? 'favorable');
                    $configEstilos = match($valorSemaforo) {
                        'favorable', 'verde' => [
                            'bg' => 'bg-emerald-50',
                            'border' => 'border-emerald-300',
                            'badge' => 'bg-emerald-600 text-white',
                            'icon' => '🟢',
                            'titulo' => 'Evolución Favorable / Normal',
                            'desc' => $semaforo?->explicacionPaciente() ?? 'El estudio no muestra alteraciones patológicas significativas o presenta una evolución positiva respecto a estudios anteriores.'
                        ],
                        'estable', 'azul' => [
                            'bg' => 'bg-sky-50',
                            'border' => 'border-sky-300',
                            'badge' => 'bg-sky-600 text-white',
                            'icon' => '🔵',
                            'titulo' => 'Cuadro Clínico Estable',
                            'desc' => $semaforo?->explicacionPaciente() ?? 'La condición clínica evaluada se mantiene sin variaciones respecto a los controles anteriores.'
                        ],
                        'control_requerido', 'amarillo', 'naranja' => [
                            'bg' => 'bg-amber-50',
                            'border' => 'border-amber-300',
                            'badge' => 'bg-amber-600 text-white',
                            'icon' => '🟡',
                            'titulo' => 'Control Periódico Requerido',
                            'desc' => $semaforo?->explicacionPaciente() ?? 'Se observan hallazgos de evolución intermedia que ameritan un nuevo estudio de control según criterio del especialista.'
                        ],
                        'atencion_inmediata', 'rojo' => [
                            'bg' => 'bg-rose-50',
                            'border' => 'border-rose-300',
                            'badge' => 'bg-rose-600 text-white',
                            'icon' => '🔴',
                            'titulo' => 'Atención Médica Prioritaria',
                            'desc' => $semaforo?->explicacionPaciente() ?? 'Se identifican hallazgos prioritarios que requieren seguimiento médico a la brevedad con su especialista de cabecera.'
                        ],
                        default => [
                            'bg' => 'bg-slate-50',
                            'border' => 'border-slate-300',
                            'badge' => 'bg-slate-600 text-white',
                            'icon' => '🩺',
                            'titulo' => 'Evaluación Médica Registrada',
                            'desc' => 'Estudio evaluado por el cuerpo médico de la institución.'
                        ],
                    };
                @endphp

                <div class="rounded-2xl border-2 {{ $configEstilos['border'] }} {{ $configEstilos['bg'] }} p-6 space-y-3">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center gap-2.5">
                            <span class="text-2xl">{{ $configEstilos['icon'] }}</span>
                            <h2 class="text-base font-extrabold text-slate-900">{{ $configEstilos['titulo'] }}</h2>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $configEstilos['badge'] }}">
                            Semáforo del Proceso del Paciente
                        </span>
                    </div>
                    <p class="text-sm text-slate-700 leading-relaxed font-medium">
                        {{ $configEstilos['desc'] }}
                    </p>
                    @if($estudio->resumen_evolucion)
                        <div class="mt-3 pt-3 border-t border-slate-200/60 flex items-start gap-2 text-xs text-slate-600">
                            <span class="font-bold text-slate-800">Nota de Evolución:</span>
                            <span>{{ $estudio->resumen_evolucion }}</span>
                        </div>
                    @endif
                </div>

                {{-- SECCIÓN 2: LÍNEA DE TIEMPO DEL PACIENTE (HISTORIAL EVOLUTIVO) --}}
                @if(count($lineaTiempo) > 1)
                    <div class="space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">📈</span>
                            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">Línea de Tiempo del Paciente (Historial Evolutivo)</h2>
                        </div>
                        <div class="relative pl-6 space-y-4 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                            @foreach($lineaTiempo as $hito)
                                <div class="relative flex items-start gap-3">
                                    <div class="absolute -left-6 top-1 w-4 h-4 rounded-full border-2 {{ $hito['es_actual'] ? 'bg-[#003764] border-sky-200 scale-125' : 'bg-white border-slate-300' }}"></div>
                                    <div class="flex-1 bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs {{ $hito['es_actual'] ? 'ring-2 ring-[#003764]/20 bg-sky-50/30' : '' }}">
                                        <div class="flex items-center justify-between font-medium">
                                            <span class="font-bold text-slate-900">{{ $hito['tipo_estudio'] }} ({{ $hito['modalidad'] }})</span>
                                            <span class="font-mono text-slate-500">{{ $hito['fecha'] }}</span>
                                        </div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">
                                            Especialidad: <strong>{{ $hito['especialidad'] }}</strong> &bull; Estado: <strong>{{ ucfirst($hito['estado']) }}</strong>
                                        </div>
                                        @if($hito['informe'])
                                            <p class="text-[11px] text-slate-600 mt-1.5 italic bg-white p-2 rounded border border-slate-100">
                                                "{{ \Illuminate\Support\Str::limit($hito['informe'], 120) }}"
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- SECCIÓN 3: INFORME MÉDICO OFICIAL TÉCNICO --}}
                <div class="border-t-2 border-slate-200 pt-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">📋</span>
                            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">Informe Médico Oficial</h2>
                        </div>
                        <span class="text-[11px] font-mono text-slate-400">Documento Clínico de Diagnóstico</span>
                    </div>

                    <div class="bg-slate-50 rounded-2xl border border-slate-200 p-6 text-sm text-slate-800 leading-relaxed font-mono whitespace-pre-line">
                        {{ $estudio->informe ?? 'Sin texto de informe cargado.' }}
                    </div>

                    {{-- Firma Digital del Especialista --}}
                    <div class="flex justify-end pt-8">
                        <div class="text-center min-w-[260px] border-t-2 border-slate-800 pt-3">
                            <div class="text-xs font-bold text-slate-900">{{ $estudio->medico->name ?? 'Dr. Profesional Firmante' }}</div>
                            <div class="text-[11px] text-slate-500">Médico Especialista &bull; Hospital Universitario (UNCuyo)</div>
                            <div class="text-[10px] font-mono text-slate-400 mt-1">
                                Firmado digitalmente el {{ $estudio->firmado_at?->format('d/m/Y H:i') ?? $estudio->updated_at->format('d/m/Y H:i') }} hs
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            {{-- Pie de Página Institucional --}}
            <div class="bg-slate-100 border-t border-slate-200 p-4 text-center text-[11px] text-slate-500">
                Hospital Universitario &bull; Universidad Nacional de Cuyo &bull; Plataforma ReportFlow
            </div>

        </div>

    </div>

</body>
</html>
