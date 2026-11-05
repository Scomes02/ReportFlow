<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('estudios') && !Schema::hasColumn('estudios', 'semaforo_evolucion')) {
            Schema::table('estudios', function (Blueprint $table) {
                $table->string('semaforo_evolucion', 20)->nullable()->default('verde')->after('informe');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('estudios') && Schema::hasColumn('estudios', 'semaforo_evolucion')) {
            Schema::table('estudios', function (Blueprint $table) {
                $table->dropColumn('semaforo_evolucion');
            });
        }
    }
};
