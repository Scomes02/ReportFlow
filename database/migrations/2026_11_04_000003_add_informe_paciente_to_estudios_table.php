<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estudios', function (Blueprint $table) {
            if (!Schema::hasColumn('estudios', 'informe_paciente')) {
                $table->text('informe_paciente')->nullable()->after('informe');
            }
        });
    }

    public function down(): void
    {
        Schema::table('estudios', function (Blueprint $table) {
            if (Schema::hasColumn('estudios', 'informe_paciente')) {
                $table->dropColumn('informe_paciente');
            }
        });
    }
};
