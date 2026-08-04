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
                    <tr class="text-sm">
                        <td class="px-4 py-3 font-bold text-brandPrimario">{{ $estudio->paciente_nombre }}</td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $estudio->paciente_dni }}</td>
                        <td class="px-4 py-3">{{ $estudio->tipoEstudio->nombre }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $estudio->fecha_estudio->format('d/m/Y - H:i') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $estudio->estado->badgeClasses() }}">
                                {{ $estudio->estado->label() }}
                            </span>
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
                  enctype="multipart/form-data" class="p-6 space-y-4">
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

                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Archivos (PDF/JPG/PNG, hasta 20MB c/u)</label>
                    <input type="file" name="archivos[]" multiple required accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full border-2 border-dashed border-gray-300 rounded-lg p-4 text-xs text-gray-500">
                </div>

                @error('archivos')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror

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