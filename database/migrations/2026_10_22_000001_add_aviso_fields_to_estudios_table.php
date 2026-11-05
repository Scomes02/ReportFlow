<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estudios', function (Blueprint $table) {
            $table->dateTime('aviso_enviado_at')->nullable()->after('firmado_at');
            $table->foreignId('aviso_enviado_por')->nullable()->after('aviso_enviado_at')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('estudios', function (Blueprint $table) {
            $table->dropConstrainedForeignId('aviso_enviado_por');
            $table->dropColumn('aviso_enviado_at');
        });
    }
};
