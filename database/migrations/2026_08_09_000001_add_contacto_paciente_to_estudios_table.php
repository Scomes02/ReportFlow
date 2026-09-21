<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estudios', function (Blueprint $table) {
            $table->string('paciente_telefono')->nullable()->after('paciente_edad');
            $table->string('paciente_email')->nullable()->after('paciente_telefono');
        });
    }

    public function down(): void
    {
        Schema::table('estudios', function (Blueprint $table) {
            $table->dropColumn(['paciente_telefono', 'paciente_email']);
        });
    }
};