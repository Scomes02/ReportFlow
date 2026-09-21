<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_estudio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('especialidad_id')->constrained('especialidades')->cascadeOnDelete();
            $table->string('nombre');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_estudio');
    }
};