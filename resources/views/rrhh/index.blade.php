@extends('layouts.app')

@section('titulo', 'Panel de Recursos Humanos')

@section('contenido')
<div class="space-y-6">
    <!-- Tarjetas de estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase">Total Estudios</p>
                    <p class="text-2xl font-black text-brandPrimario">{{ $totalEstudios }}</p>
                </div>
                <span class="bg-blue-100 text-blue-600 p-3 rounded-lg">
                    <i class="fas fa-file-medical text-xl"></i>
                </span>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase">Informados</p>
                    <p class="text-2xl font-black text-green-600">{{ $estudiosInformados }}</p>
                </div>
                <span class="bg-green-100 text-green-600 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-xl"></i>
                </span>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase">Pendientes</p>
                    <p class="text-2xl font-black text-orange-600">{{ $estudiosPendientes }}</p>
                </div>
                <span class="bg-orange-100 text-orange-600 p-3 rounded-lg">
                    <i class="fas fa-clock text-xl"></i>
                </span>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase">Rechazados</p>
                    <p class="text-2xl font-black text-red-600">{{ $estudiosRechazados }}</p>
                </div>
                <span class="bg-red-100 text-red-600 p-3 rounded-lg">
                    <i class="fas fa-times-circle text-xl"></i>
                </span>
            </div>
        </div>
    </div>

    <!-- Enlaces rápidos -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-bold text-gray-700 mb-4">
                <i class="fas fa-stethoscope text-brandPrimario mr-2"></i>
                Especialidades
            </h3>
            <div class="space-y-2">
                @foreach($especialidades as $esp)
                <a href="{{ route('rrhh.medicos', $esp->slug) }}" 
                   class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-brandPrimario/5 transition">
                    <span class="text-sm font-semibold text-gray-700">{{ $esp->nombre }}</span>
                    <span class="text-xs bg-brandPrimario/10 text-brandPrimario px-2 py-1 rounded-full">
                        {{ $esp->estudios_count }} informes
                    </span>
                </a>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-bold text-gray-700 mb-4">
                <i class="fas fa-user-md text-brandPrimario mr-2"></i>
                Médicos Activos
            </h3>
            <div class="space-y-2">
                @foreach($medicos as $medico)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-semibold text-gray-700">{{ $medico->name }}</span>
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">
                        {{ $medico->estudios_como_medico_count }} informes
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Acceso rápido al archivo -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-gray-700">
                    <i class="fas fa-archive text-brandPrimario mr-2"></i>
                    Archivo de Informes
                </h3>
                <p class="text-xs text-gray-500">Accede a todos los informes firmados por mes</p>
            </div>
            <a href="{{ route('rrhh.archivo') }}" 
               class="bg-brandPrimario hover:bg-brandPrimario/90 text-white px-4 py-2 rounded-lg text-xs font-bold transition">
                Ver Archivo
            </a>
        </div>
    </div>
</div>
@endsection