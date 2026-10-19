<?php

namespace Database\Seeders;

use App\Models\Estudio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArchivosDemoSeeder extends Seeder
{
    public function run(): void
    {
        $disco = 'estudios';

        // Sample valid minimal PDF
        $samplePdf = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>/Contents 4 0 R>>endobj\n4 0 obj<</Length 55>>stream\nBT /F1 18 Tf 50 700 Td (Hospital Universitario - Estudio Clinico) Tj ET\nendstream\nendobj\nxref\n0 5\n0000000000 65535 f \n0000000009 00000 n \n0000000058 00000 n \n0000000115 00000 n \n0000000216 00000 n \ntrailer<</Size 5/Root 1 0 R>>\nstartxref\n323\n%%EOF\n";

        // Sample PNG image (1x1 dot PNG)
        $samplePng = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');

        $estudios = Estudio::with('tipoEstudio')->whereDoesntHave('archivos')->get();

        foreach ($estudios as $estudio) {
            $tipoNombre = $estudio->tipoEstudio?->nombre ?? 'estudio';
            $slugTipo = Str::slug($tipoNombre);

            // Generar 1 o 2 archivos por estudio
            $archivosConfig = [
                [
                    'nombre' => "toma_{$slugTipo}_{$estudio->id}.pdf",
                    'mime' => 'application/pdf',
                    'contenido' => $samplePdf,
                ],
                [
                    'nombre' => "captura_grafico_{$slugTipo}_{$estudio->id}.png",
                    'mime' => 'image/png',
                    'contenido' => $samplePng,
                ],
            ];

            foreach ($archivosConfig as $cfg) {
                $path = "estudios/{$estudio->id}/{$cfg['nombre']}";
                Storage::disk($disco)->put($path, $cfg['contenido']);

                $estudio->archivos()->create([
                    'disco' => $disco,
                    'path' => $path,
                    'nombre_original' => $cfg['nombre'],
                    'mime_type' => $cfg['mime'],
                    'tamano_bytes' => strlen($cfg['contenido']),
                ]);
            }
        }
    }
}
