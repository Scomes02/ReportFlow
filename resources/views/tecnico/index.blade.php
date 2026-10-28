@extends('layouts.app')

@section('titulo', 'Mis Estudios Cargados')

@section('contenido')
<div x-data="{ modalAbierto: false }">

    <div class="flex justify-between items-center mb-6">
        <p class="text-sm text-gray-500">Historial de estudios subidos y derivados a especialidad.</p>
        <button @click="modalAbierto = true"
                class="bg-brandSecundario hover:opacity-90 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm">
            + Nuevo Estudio
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                <tr>
                    <th class="px-4 py-3">Paciente</th>
                    <th class="px-4 py-3">DNI</th>
                    <th class="px-4 py-3">Tipo de Estudio</th>
                    <th class="px-4 py-3">Fecha</th>
                    <th class="px-4 py-3">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($estudios as $estudio)
                    <tr class="text-sm" x-data="{ verMotivo: false }">
                        <td class="px-4 py-3 font-bold text-brandPrimario">{{ $estudio->paciente_nombre }}</td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $estudio->paciente_dni }}</td>
                        <td class="px-4 py-3">
                            <span class="font-medium text-slate-800">{{ $estudio->tipoEstudio->nombre }}</span>
                            @if ($estudio->tieneDicom())
                                <div class="inline-flex items-center gap-1 mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 whitespace-nowrap" style="font-size: 10px; line-height: 14px;">
                                    <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor" style="width: 12px; height: 12px; min-width: 12px; flex-shrink: 0;" class="text-indigo-600">
                                        <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4zM3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 00-1 1v2a1 1 0 102 0v-2a1 1 0 00-1-1zm4 0a1 1 0 00-1 1v2a1 1 0 102 0v-2a1 1 0 00-1-1z"/>
                                    </svg>
                                    <span>DICOM [{{ $estudio->modalidad ?? 'CR' }}]</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $estudio->fecha_estudio->format('d/m/Y - H:i') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $estudio->estado->badgeClasses() }}">
                                {{ $estudio->estado->label() }}
                            </span>

                            @if ($estudio->estado === \App\Enums\EstadoEstudio::Rechazado && $estudio->motivo_rechazo)
                                <button @click="verMotivo = !verMotivo" type="button"
                                        class="block mt-1 text-[10px] text-red-600 font-semibold hover:underline">
                                    Ver motivo
                                </button>
                                <div x-show="verMotivo" x-cloak
                                     class="mt-1 max-w-xs bg-red-50 border border-red-200 text-red-700 text-[11px] rounded-lg p-2">
                                    {{ $estudio->motivo_rechazo }}
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400">
                            Todavía no cargaste ningún estudio.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $estudios->links() }}

    <div x-show="modalAbierto" x-cloak
         class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
        <div @click.outside="modalAbierto = false"
             class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">

            <div class="bg-brandPrimario text-white p-4 flex justify-between items-center">
                <h3 class="font-bold">Nuevo Estudio</h3>
                <button @click="modalAbierto = false" class="text-white/70 hover:text-white">✕</button>
            </div>

            <form method="POST" action="{{ route('tecnico.estudios.store') }}"
                  enctype="multipart/form-data" class="p-6 space-y-4"
                  x-data="{ origen: 'archivo', studyUid: '' }">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Nombre del Paciente</label>
                    <input type="text" name="paciente_nombre" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-brandSecundario focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">DNI</label>
                        <input type="text" name="paciente_dni" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-brandSecundario focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Edad</label>
                        <input type="number" name="paciente_edad" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-brandSecundario focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Teléfono (opcional)</label>
                        <input type="text" name="paciente_telefono" placeholder="261 555-1234"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-brandSecundario focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Email (opcional)</label>
                        <input type="email" name="paciente_email" placeholder="paciente@email.com"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-brandSecundario focus:outline-none">
                    </div>
                </div>
                <p class="text-[11px] text-gray-400 -mt-2">
                    Se usan para que Call Center pueda avisarle al paciente cuando el informe esté listo.
                </p>

                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Especialidad</label>
                    <select name="especialidad_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-brandSecundario focus:outline-none">
                        <option value="">Seleccionar...</option>
                        @foreach ($especialidades as $especialidad)
                            <option value="{{ $especialidad->id }}">{{ $especialidad->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Tipo de Estudio</label>
                    <select name="tipo_estudio_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-brandSecundario focus:outline-none">
                        <option value="">Seleccionar...</option>
                        @foreach ($especialidades as $especialidad)
                            @foreach ($especialidad->tiposEstudio as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->nombre }} ({{ $especialidad->nombre }})</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Fecha del Estudio</label>
                    <input type="datetime-local" name="fecha_estudio" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-brandSecundario focus:outline-none">
                </div>

                {{-- Selector de Origen: Archivos Locales o Conexión PACS dcm4chee --}}
                <div class="border border-slate-200 rounded-xl p-3 bg-slate-50/70 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-700">Imágenes y Datos Clínicos</label>
                        <div class="inline-flex rounded-lg border border-slate-200 bg-white p-0.5 text-xs shadow-sm">
                            <button type="button" @click="origen = 'archivo'"
                                    :class="origen === 'archivo' ? 'bg-slate-900 text-white font-bold' : 'text-slate-600 hover:text-slate-900'"
                                    class="px-2.5 py-1 rounded-md transition-colors">
                                📁 Archivos
                            </button>
                            <button type="button" @click="origen = 'pacs'"
                                    :class="origen === 'pacs' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-600 hover:text-slate-900'"
                                    class="px-2.5 py-1 rounded-md transition-colors flex items-center gap-1">
                                <span>📡 PACS DICOM</span>
                            </button>
                        </div>
                    </div>

                    {{-- Sección PACS DICOM (dcm4chee) --}}
                    <div x-show="origen === 'pacs'" x-cloak class="space-y-3 pt-1 border-t border-slate-200">
                        <div class="bg-indigo-50 border border-indigo-200 p-2.5 rounded-lg text-[11px] text-indigo-900 leading-relaxed">
                            <strong>Servidor dcm4chee-arc-light:</strong> Podés vincular el estudio mediante su <code>StudyInstanceUID</code> para que el médico visualice las series DICOM directamente desde el visor.
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            <div class="col-span-2">
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">StudyInstanceUID</label>
                                <input type="text" name="study_instance_uid" x-model="studyUid"
                                       placeholder="1.2.840.113619.2.55.3..."
                                       class="w-full border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs font-mono focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Modalidad</label>
                                <select name="modalidad" class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                                    <option value="CR">CR (Radiografía)</option>
                                    <option value="CT">CT (Tomografía)</option>
                                    <option value="MR">MR (Resonancia)</option>
                                    <option value="DX">DX (Rx Digital)</option>
                                    <option value="US">US (Ecografía)</option>
                                    <option value="NM">NM (Med. Nuclear)</option>
                                    <option value="XA">XA (Angiografía)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Accession Number (Opcional)</label>
                            <input type="text" name="accession_number" placeholder="ACC-2026-001"
                                   class="w-full border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs font-mono focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                        </div>
                    </div>

                    {{-- Carga de archivos --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Archivos adjuntos (PDF, JPG, PNG, DCM hasta 50MB c/u)
                        </label>
                        <input type="file" name="archivos[]" multiple accept=".pdf,.jpg,.jpeg,.png,.dcm"
                               :required="origen === 'archivo' && !studyUid"
                               class="w-full border-2 border-dashed border-gray-300 rounded-lg p-3 text-xs text-gray-500 bg-white">
                        <p class="text-[10px] text-slate-400 mt-1" x-show="origen === 'pacs'">
                            Opcional si vinculaste un StudyInstanceUID del PACS dcm4chee.
                        </p>
                    </div>

                    @error('archivos')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit"
                            class="bg-brandPrimario hover:opacity-90 text-white px-5 py-2 rounded-lg text-xs font-bold shadow">
                        Cargar Estudio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection