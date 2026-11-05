<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Agregar campos a la tabla estudios
        if (Schema::hasTable('estudios')) {
            Schema::table('estudios', function (Blueprint $table) {
                if (!Schema::hasColumn('estudios', 'region_anatomica')) {
                    $table->string('region_anatomica', 50)->nullable()->after('tipo_estudio_id');
                }
                if (!Schema::hasColumn('estudios', 'semaforo_salud')) {
                    $table->string('semaforo_salud', 30)->nullable()->default('favorable')->after('informe');
                }
                if (!Schema::hasColumn('estudios', 'resumen_evolucion')) {
                    $table->text('resumen_evolucion')->nullable()->after('semaforo_salud');
                }
            });
        }

        // 2. Agregar región anatómica por defecto en tipos_estudio
        if (Schema::hasTable('tipos_estudio')) {
            Schema::table('tipos_estudio', function (Blueprint $table) {
                if (!Schema::hasColumn('tipos_estudio', 'region_anatomica_defecto')) {
                    $table->string('region_anatomica_defecto', 50)->nullable()->after('nombre');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('estudios')) {
            Schema::table('estudios', function (Blueprint $table) {
                $columnas = [];
                if (Schema::hasColumn('estudios', 'region_anatomica')) {
                    $columnas[] = 'region_anatomica';
                }
                if (Schema::hasColumn('estudios', 'semaforo_salud')) {
                    $columnas[] = 'semaforo_salud';
                }
                if (Schema::hasColumn('estudios', 'resumen_evolucion')) {
                    $columnas[] = 'resumen_evolucion';
                }
                if (!empty($columnas)) {
                    $table->dropColumn($columnas);
                }
            });
        }

        if (Schema::hasTable('tipos_estudio') && Schema::hasColumn('tipos_estudio', 'region_anatomica_defecto')) {
            Schema::table('tipos_estudio', function (Blueprint $table) {
                $table->dropColumn('region_anatomica_defecto');
            });
        }
    }
};
