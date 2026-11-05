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

                {{-- SECCIÓN 1 (PRINCIPAL): INFORME MÉDICO OFICIAL DE DIAGNÓSTICO --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b-2 border-slate-200 pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="text-xl">📋</span>
                            <div>
                                <h2 class="text-base font-extrabold text-slate-900 uppercase tracking-wide">Informe Médico de Diagnóstico</h2>
                                <p class="text-[11px] text-slate-500">Documento Oficial de Diagnóstico por Imágenes &bull; Validez Médico-Legal</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200 uppercase">
                            {{ $estudio->modalidad ?? 'CR' }} &bull; {{ $estudio->tipoEstudio->nombre ?? 'Estudio' }}
                        </span>
                    </div>

                    {{-- Cuerpo del Diagnóstico Formal --}}
                    <div class="bg-white rounded-2xl border border-slate-300 p-6 sm:p-8 text-sm text-slate-900 leading-relaxed font-mono whitespace-pre-line shadow-xs">
                        {{ $estudio->informe ?? 'Sin texto de informe cargado.' }}
                    </div>

                    {{-- Firma Digital del Especialista --}}
                    <div class="flex justify-end pt-6">
                        <div class="text-center min-w-[280px] border-t-2 border-slate-800 pt-3">
                            <div class="text-sm font-bold text-slate-900">{{ $estudio->medico->name ?? 'Dr. Profesional Firmante' }}</div>
                            <div class="text-[11px] text-slate-600 font-medium">Médico Especialista &bull; Hospital Universitario (UNCuyo)</div>
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
