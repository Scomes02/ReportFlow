@extends('layouts.app')

@section('titulo', 'Mis Informes - Médico')

@section('contenido')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg text-sm flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 tracking-tight">Mis Informes Realizados</h2>
            <p class="text-xs text-slate-500 mt-0.5">Historial de estudios informados y gestión de adendas (>24hs).</p>
        </div>
        <a href="{{ route('medico.estudios.index') }}" class="text-xs font-semibold text-[#1c3452] hover:underline">
            &larr; Volver a Worklist
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-4 py-3.5">ID ESTUDIO</th>
                        <th class="px-4 py-3.5">PACIENTE</th>
                        <th class="px-4 py-3.5">ESTUDIO</th>
                        <th class="px-4 py-3.5">FECHA FIRMA</th>
                        <th class="px-4 py-3.5">ESTADO PERMISO</th>
                        <th class="px-4 py-3.5">COMENTARIOS</th>
                        <th class="px-4 py-3.5 text-end">ACCIONES</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-600">
                    @forelse ($estudios as $estudio)
                        @php
                            // Calculamos las horas pasadas desde que se firmó el informe
                            $fechaFirma = $estudio->firmado_at ?? $estudio->updated_at;
                            $horasPasadas = $fechaFirma->diffInHours(now());
                            $esEditable = $horasPasadas < 24;
                        @endphp

                        <tr class="hover:bg-slate-50/50 transition-colors align-top" x-data="{ modalAbierto: false, verComentarios: false }">
                            <td class="px-4 py-3.5 font-mono text-[11px] text-slate-400">#{{ $estudio->id }}</td>
                            <td class="px-4 py-3.5 font-bold text-slate-900">{{ $estudio->paciente_nombre }}</td>
                            <td class="px-4 py-3.5 text-slate-700">{{ $estudio->tipoEstudio->nombre ?? 'Estudio' }}</td>
                            <td class="px-4 py-3.5 text-slate-500">{{ $fechaFirma->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3.5">
                                @if($esEditable)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-green-100 text-green-800 border border-green-200">
                                        Editable (< 24hs)
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        Solo Adendas (> 24hs)
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                @if($estudio->adendas->isEmpty())
                                    <span class="text-slate-400 italic">Sin comentarios</span>
                                @else
                                    <button @click="verComentarios = !verComentarios"
                                            class="inline-flex items-center gap-1 text-[#1c3452] font-semibold hover:underline">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-1l-4 4v-4H9z"></path></svg>
                                        {{ $estudio->adendas->count() }} {{ Str::plural('comentario', $estudio->adendas->count()) }}
                                    </button>

                                    <div x-show="verComentarios" x-cloak class="mt-2 space-y-2 max-w-xs">
                                        @foreach ($estudio->adendas as $adenda)
                                            <div class="bg-slate-50 border border-slate-200 rounded-lg p-2.5">
                                                <p class="text-slate-700">{{ $adenda->contenido }}</p>
                                                <p class="text-[10px] text-slate-400 mt-1">{{ $adenda->created_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-end">
                                <button @click="modalAbierto = true"
                                        class="{{ $esEditable ? 'bg-blue-600 hover:bg-blue-700' : 'bg-slate-700 hover:bg-slate-800' }} text-white px-4 py-1.5 rounded-full text-xs font-semibold tracking-wide shadow-sm transition-all">
                                    {{ $esEditable ? 'Editar Informe' : 'Añadir Adenda' }}
                                </button>

                                {{-- MODAL DINÁMICO (Edición o Adenda) --}}
                                <template x-teleport="body">
                                    <div x-show="modalAbierto" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                                        <div @click.outside="modalAbierto = false" class="bg-white rounded-xl shadow-2xl w-full max-w-xl overflow-hidden text-start border border-slate-100">
                                            <div class="{{ $esEditable ? 'bg-blue-600' : 'bg-slate-800' }} text-white px-5 py-4 flex justify-between items-center">
                                                <h3 class="font-bold text-sm">
                                                    {{ $esEditable ? 'Editar Informe' : 'Añadir Adenda al Informe' }}
                                                </h3>
                                                <button @click="modalAbierto = false" class="text-white/70 hover:text-white text-lg leading-none">&times;</button>
                                            </div>

                                            <form method="POST" action="{{ $esEditable ? route('medico.informes.update', $estudio->id) : route('medico.adendas.store', $estudio->id) }}" class="p-6 space-y-4">
                                                @csrf
                                                @if($esEditable) @method('PUT') @endif

                                                <div class="bg-slate-50 p-3 rounded-lg border border-slate-200 text-xs mb-4">
                                                    <p><strong>Paciente:</strong> {{ $estudio->paciente_nombre }}</p>
                                                    <p><strong>Estudio:</strong> {{ $estudio->tipoEstudio->nombre ?? 'Estudio' }}</p>
                                                    @if(!$esEditable)
                                                        <div class="mt-2 pt-2 border-t border-slate-200 text-slate-500 italic">
                                                            "{{ Str::limit($estudio->informe, 100) }}"
                                                        </div>
                                                    @endif
                                                </div>

                                                <div>
                                                    <label class="block text-[11px] font-bold text-slate-500 mb-1">
                                                        {{ $esEditable ? 'MODIFICAR TEXTO DEL INFORME' : 'TEXTO DE LA ADENDA / COMENTARIO' }}
                                                    </label>
                                                    <textarea name="{{ $esEditable ? 'informe' : 'contenido' }}" rows="5" required 
                                                              class="w-full border border-slate-300 rounded-lg p-3 text-xs focus:ring-2 focus:ring-[#1c3452]/20 focus:border-[#1c3452] transition-all"
                                                    >{{ $esEditable ? $estudio->informe : '' }}</textarea>
                                                </div>

                                                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                                    <button type="button" @click="modalAbierto = false" class="px-4 py-2 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100">Cancelar</button>
                                                    <button type="submit" class="{{ $esEditable ? 'bg-blue-600 hover:bg-blue-700' : 'bg-slate-800 hover:bg-slate-900' }} text-white px-5 py-2 rounded-lg text-xs font-bold shadow-sm">
                                                        Guardar Cambios
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </template>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                                No has realizado ningún informe todavía.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection