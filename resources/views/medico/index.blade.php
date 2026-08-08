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

    {{-- Encabezado de la Sección --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 tracking-tight">Worklist de Estudios Pendientes</h2>
            <p class="text-xs text-slate-500 mt-0.5">Gestión e informe médico de estudios cargados.</p>
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
                        <tr class="hover:bg-slate-50/50 transition-colors" x-data="{ modalAbierto: false, mostrarRechazo: false }">
                            <td class="px-4 py-3.5 font-mono text-[11px] text-slate-400">#{{ $estudio->id }}</td>
                            <td class="px-4 py-3.5 font-bold text-slate-900">{{ $estudio->paciente_nombre }}</td>
                            <td class="px-4 py-3.5 text-slate-700">{{ $estudio->tipoEstudio->nombre ?? 'Estudio' }}</td>
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
                                             class="bg-white rounded-xl shadow-2xl w-full max-w-xl overflow-hidden border border-slate-100 my-8 text-start">

                                            {{-- Vista 1: redactar y firmar el informe --}}
                                            <div x-show="!mostrarRechazo">
                                                <div class="bg-[#1c3452] text-white px-5 py-4 flex justify-between items-center">
                                                    <h3 class="font-bold text-sm tracking-wide">Estudio: {{ $estudio->tipoEstudio->nombre ?? 'Estudio' }}</h3>
                                                    <button @click="modalAbierto = false" class="text-slate-300 hover:text-white transition-colors text-lg leading-none">&times;</button>
                                                </div>

                                                <form method="POST" action="{{ route('medico.estudios.informar', $estudio->id) }}" class="p-6 space-y-4">
                                                    @csrf

                                                    <!-- Ficha resumida del paciente -->
                                                    <div class="bg-slate-50 p-3.5 rounded-lg border border-slate-200/80 text-xs grid grid-cols-2 gap-2 text-slate-600">
                                                        <div><strong>Paciente:</strong> <span class="text-slate-900 font-semibold">{{ $estudio->paciente_nombre }}</span></div>
                                                        <div><strong>Edad:</strong> <span class="text-slate-900">{{ $estudio->paciente_edad }} años</span></div>
                                                        <div><strong>Fecha:</strong> <span class="text-slate-900">{{ $estudio->fecha_estudio->format('d/m/Y - H:i') }}</span></div>
                                                        <div><strong>Técnico:</strong> <span class="text-slate-900">{{ $estudio->tecnico_nombre }}</span></div>
                                                    </div>

                                                    <div>
                                                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                                                            Informe {{ strtoupper($estudio->tipoEstudio->nombre ?? '') }}
                                                        </label>
                                                        <textarea name="informe" rows="6" required 
                                                                  placeholder="Escriba el resultado, observaciones y conclusión del estudio aquí..."
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