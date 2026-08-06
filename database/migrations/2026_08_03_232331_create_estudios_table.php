<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudios', function (Blueprint $table) {
            $table->id();
            $table->string('paciente_nombre');
            $table->string('paciente_dni');
            $table->unsignedTinyInteger('paciente_edad');
            $table->foreignId('tipo_estudio_id')->constrained('tipos_estudio')->restrictOnDelete();
            $table->foreignId('tecnico_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('medico_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('estado')->default('nuevo');
            $table->dateTime('fecha_estudio');
            $table->dateTime('firmado_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudios');
    }
};