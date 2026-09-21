<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            EspecialidadSeeder::class,   // 8 especialidades + sus tipos de estudio
            TecnicoDePruebaSeeder::class, // usuario técnico de prueba para /dev-login
            EstudioDemoSeeder::class,     // crea médicos/técnicos demo + ~80 estudios realistas
        ]);
    }
}