<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EspecialidadSeeder extends Seeder
{
    public function run(): void
    {
        $especialidades = [
            'Cardiología' => ['Electrocardiograma', 'Holter 24hs', 'Ecocardiograma', 'Ergometría'],
            'Neumonología' => ['Espirometría', 'Radiografía de Tórax'],
            'Neurología' => ['Electroencefalograma', 'Potenciales Evocados', 'Evaluación Neurocognitiva'],
            'Traumatología' => ['Radiografía', 'Resonancia Magnética'],
            'Oftalmología' => ['Fondo de Ojo', 'Campo Visual'],
            'Gastroenterología' => ['Endoscopía Digestiva Alta', 'Prueba de Difusión Pulmonar'],
            'Dermatología' => ['Dermatoscopía Digital'],
            'Psiquiatría' => ['Evaluación Psicológica'],
        ];

        foreach ($especialidades as $nombre => $tipos) {
            $especialidad = Especialidad::firstOrCreate(
                ['slug' => Str::slug($nombre)],
                ['nombre' => $nombre]
            );

            foreach ($tipos as $tipoNombre) {
                $especialidad->tiposEstudio()->firstOrCreate(['nombre' => $tipoNombre]);
            }
        }
    }
}