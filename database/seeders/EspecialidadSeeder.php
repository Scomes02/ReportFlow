<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use Illuminate\Database\Seeder;

class EspecialidadSeeder extends Seeder
{
    public function run(): void
    {
        $especialidades = [
            'Cardiología' => ['Electrocardiograma', 'Holter 24hs', 'Ecocardiograma'],
            'Neumonología' => ['Espirometría', 'Radiografía de Tórax'],
        ];

        foreach ($especialidades as $nombre => $tipos) {
            $especialidad = Especialidad::create([
                'nombre' => $nombre,
                'slug' => \Illuminate\Support\Str::slug($nombre),
            ]);

            foreach ($tipos as $tipoNombre) {
                $especialidad->tiposEstudio()->create(['nombre' => $tipoNombre]);
            }
        }
    }
}