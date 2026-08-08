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
            @php
                // Diccionario de colores e íconos dinámicos según el slug
                $estilo = match($esp->slug) {
                    'cardiologia' => [
                        'border' => 'border-red-500', 'bg_icon' => 'bg-red-50 text-red-500',
                        'icon' => 'fa-heartbeat', 'badge' => 'bg-red-100 text-red-800 border-red-200'
                    ],
                    'neumonologia' => [
                        'border' => 'border-sky-500', 'bg_icon' => 'bg-sky-50 text-sky-500',
                        'icon' => 'fa-lungs', 'badge' => 'bg-sky-100 text-sky-800 border-sky-200'
                    ],
                    'neurologia' => [
                        'border' => 'border-purple-500', 'bg_icon' => 'bg-purple-50 text-purple-500',
                        'icon' => 'fa-brain', 'badge' => 'bg-purple-100 text-purple-800 border-purple-200'
                    ],
                    'traumatologia' => [
                        'border' => 'border-orange-500', 'bg_icon' => 'bg-orange-50 text-orange-500',
                        'icon' => 'fa-bone', 'badge' => 'bg-orange-100 text-orange-800 border-orange-200'
                    ],
                    'oftalmologia' => [
                        'border' => 'border-teal-500', 'bg_icon' => 'bg-teal-50 text-teal-500',
                        'icon' => 'fa-eye', 'badge' => 'bg-teal-100 text-teal-800 border-teal-200'
                    ],
                    'gastroenterologia' => [
                        'border' => 'border-yellow-500', 'bg_icon' => 'bg-yellow-50 text-yellow-600',
                        'icon' => 'fa-procedures', 'badge' => 'bg-yellow-100 text-yellow-800 border-yellow-200'
                    ],
                    'dermatologia' => [
                        'border' => 'border-pink-500', 'bg_icon' => 'bg-pink-50 text-pink-500',
                        'icon' => 'fa-allergies', 'badge' => 'bg-pink-100 text-pink-800 border-pink-200'
                    ],
                    'psiquiatria' => [
                        'border' => 'border-indigo-500', 'bg_icon' => 'bg-indigo-50 text-indigo-500',
                        'icon' => 'fa-head-side-virus', 'badge' => 'bg-indigo-100 text-indigo-800 border-indigo-200'
                    ],
                    default => [
                        'border' => 'border-blue-500', 'bg_icon' => 'bg-blue-50 text-blue-500',
                        'icon' => 'fa-stethoscope', 'badge' => 'bg-blue-100 text-blue-800 border-blue-200'
                    ],
                };
            @endphp

            <a href="{{ route('rrhh.medicos', $esp->slug) }}" 
               class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 flex flex-col items-center justify-center hover:shadow-lg hover:-translate-y-1 transition-all border-t-4 {{ $estilo['border'] }}">
                <span class="{{ $estilo['bg_icon'] }} p-5 rounded-full mb-4">
                    <i class="fas {{ $estilo['icon'] }} text-5xl"></i>
                </span>
                <h2 class="text-xl font-black text-gray-800 uppercase mb-1 text-center">{{ $esp->nombre }}</h2>
                <p class="text-xs text-gray-500 font-medium mb-3">Auditoría y Liquidación</p>
                <span class="{{ $estilo['badge'] }} text-xs font-bold px-3 py-1 rounded-full border">
                    {{ $esp->estudios_informados_count ?? 0 }} Informes
                </span>
            </a>
        @endforeach
    </div>
</div>
@endsection