<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archivos_estudio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudio_id')->constrained('estudios')->cascadeOnDelete();
            $table->string('disco');
            $table->string('path');
            $table->string('nombre_original');
            $table->string('mime_type');
            $table->unsignedBigInteger('tamano_bytes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archivos_estudio');
    }
};