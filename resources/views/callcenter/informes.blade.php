@extends('layouts.app')

@section('titulo', 'Call Center - Informes Listos')

@section('contenido')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg text-sm flex items-center gap-2 shadow-sm">
            <i class="fas fa-check-circle text-emerald-600"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm flex items-center gap-2 shadow-sm">
            <i class="fas fa-exclamation-circle text-red-600"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div>
        <h2 class="text-xl font-bold text-gray-800 tracking-tight">Informes Listos para Entregar</h2>
        <p class="text-xs text-gray-500 mt-0.5">Solo se muestran estudios ya firmados por el médico. Buscá por DNI para ubicar a un paciente puntual.</p>
    </div>

    <form method="GET" action="{{ route('callcenter.informes') }}" class="flex gap-2 max-w-md">
        <input type="text" name="dni" value="{{ $dni }}" placeholder="Buscar por DNI del paciente..."
               class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-brandPrimario focus:outline-none">
        <button type="submit"
                class="bg-brandPrimario hover:opacity-90 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm">
            <i class="fas fa-search mr-1"></i> Buscar
        </button>
        @if($dni !== '')
            <a href="{{ route('callcenter.informes') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm font-bold">
                Limpiar
            </a>
        @endif
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                <tr>
                    <th class="px-4 py-3">Paciente</th>
                    <th class="px-4 py-3">DNI</th>
                    <th class="px-4 py-3">Tipo de Estudio</th>
                    <th class="px-4 py-3">Médico</th>
                    <th class="px-4 py-3">Fecha Firma</th>
                    <th class="px-4 py-3 text-end">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($informes as $informe)
                    <tr class="text-sm hover:bg-gray-50/60 transition-colors" x-data="{ modalAbierto: false }">
                        <td class="px-4 py-3 font-bold text-brandPrimario">{{ $informe->paciente_nombre }}</td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $informe->paciente_dni }}</td>
                        <td class="px-4 py-3">{{ $informe->tipoEstudio->nombre ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $informe->medico->name ?? 'Sin asignar' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $informe->firmado_at?->format('d/m/Y - H:i') ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-end">
                            <button @click="modalAbierto = true"
                                    class="bg-brandSecundario hover:opacity-90 text-white px-4 py-1.5 rounded-full text-xs font-bold shadow-sm">
                                <i class="fas fa-address-card mr-1"></i> Ver contacto
                            </button>

                            {{-- Modal con datos de contacto y envío simulado --}}
                            <template x-teleport="body">
                                <div x-show="modalAbierto" x-cloak
                                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                                    <div @click.outside="modalAbierto = false"
                                         class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden text-start border border-gray-100">

                                        <div class="bg-brandPrimario text-white px-5 py-4 flex justify-between items-center">
                                            <h3 class="font-bold text-sm">Datos de contacto del paciente</h3>
                                            <button @click="modalAbierto = false" class="text-white/70 hover:text-white text-lg leading-none">&times;</button>
                                        </div>

                                        <div class="p-6 space-y-4">
                                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm space-y-1.5">
                                                <p><i class="fas fa-user w-5 text-gray-400"></i> <strong>{{ $informe->paciente_nombre }}</strong></p>
                                                <p><i class="fas fa-id-card w-5 text-gray-400"></i> DNI: {{ $informe->paciente_dni }}</p>
                                                <p><i class="fas fa-phone w-5 text-gray-400"></i> {{ $informe->paciente_telefono ?? 'Sin teléfono cargado' }}</p>
                                                <p><i class="fas fa-envelope w-5 text-gray-400"></i> {{ $informe->paciente_email ?? 'Sin email cargado' }}</p>
                                                <p class="pt-1 border-t border-gray-200 mt-2 text-gray-600">
                                                    <i class="fas fa-file-medical w-5 text-gray-400"></i> {{ $informe->tipoEstudio->nombre ?? 'Estudio' }} — firmado por {{ $informe->medico->name ?? 'N/A' }}
                                                </p>
                                            </div>

                                            <p class="text-xs text-gray-500">
                                                Al confirmar, se simula el envío de un aviso al paciente para que sepa que puede retirar su informe. No se manda ningún email ni SMS real.
                                            </p>

                                            <form method="POST" action="{{ route('callcenter.informes.enviar', $informe->id) }}" class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                                                @csrf
                                                <button type="button" @click="modalAbierto = false"
                                                        class="px-4 py-2 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100">
                                                    Cerrar
                                                </button>
                                                <button type="submit"
                                                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-lg text-xs font-bold shadow-sm">
                                                    <i class="fas fa-paper-plane mr-1"></i> Enviar aviso al paciente
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-400">
                            @if($dni !== '')
                                No se encontró ningún informe cerrado para el DNI "{{ $dni }}".
                            @else
                                Todavía no hay informes firmados en el sistema.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">
        {{ $informes->links() }}
    </div>
</div>
@endsection