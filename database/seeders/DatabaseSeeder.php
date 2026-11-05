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
            ArchivosDemoSeeder::class,    // genera placas y archivos de muestra para los estudios demo
            DicomEstudiosDemoSeeder::class, // estudios DICOM vinculados a PACS dcm4chee
            EstudiosEvolutivosDemoSeeder::class, // pacientes con historial evolutivo encadenado y timeline
            CareMFlowDemoSeeder::class, // casos estrella con TAC cerebral, RMN y Angiografía
        ]);
    }
}