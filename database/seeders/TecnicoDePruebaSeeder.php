<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TecnicoDePruebaSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'tecnico.prueba@reportflow.local'],
            [
                'name' => 'Técnico de Prueba',
                'password' => Hash::make('password'),
            ]
        );
    }
}