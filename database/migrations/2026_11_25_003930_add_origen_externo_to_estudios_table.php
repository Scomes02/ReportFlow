<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('estudios', function (Blueprint $table) {
            $table->boolean('es_externo')->default(false)->after('estado');
            $table->string('institucion_origen')->nullable()->after('es_externo');
            $table->foreignId('tecnico_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estudios', function (Blueprint $table) {
            $table->dropColumn(['es_externo', 'institucion_origen']);
        });
    }
};
