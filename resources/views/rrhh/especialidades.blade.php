@extends('layouts.app')

@section('titulo', 'Especialidades - RRHH')

@section('contenido')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Carpetas de Especialidades</h3>
            <p class="text-xs text-gray-500">Audita y liquida las prestaciones de los médicos</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($especialidades as $esp)
        <a href="{{ route('rrhh.medicos', $esp->slug) }}" 
           class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 flex flex-col items-center justify-center hover:shadow-lg hover:-translate-y-1 transition-all border-t-4 {{ $esp->slug === 'cardiologia' ? 'border-red-500' : 'border-blue-500' }}">
            <span class="{{ $esp->slug === 'cardiologia' ? 'bg-red-50 text-red-500' : 'bg-blue-50 text-blue-500' }} p-5 rounded-full mb-4">
                <i class="fas {{ $esp->slug === 'cardiologia' ? 'fa-heartbeat' : 'fa-lungs' }} text-5xl"></i>
            </span>
            <h2 class="text-xl font-black text-gray-800 uppercase mb-1">{{ $esp->nombre }}</h2>
            <p class="text-xs text-gray-500 font-medium mb-3">Auditoría y Liquidación</p>
            <span class="{{ $esp->slug === 'cardiologia' ? 'bg-red-100 text-red-800 border-red-200' : 'bg-blue-100 text-blue-800 border-blue-200' }} text-xs font-bold px-3 py-1 rounded-full border">
                {{ $esp->estudios_informados_count ?? 0 }} Informes
            </span>
        </a>
        @endforeach
    </div>
</div>
@endsection