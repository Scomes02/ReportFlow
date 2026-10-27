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
            $table->string('study_instance_uid', 128)->nullable()->index()->after('tipo_estudio_id');
            $table->string('accession_number', 64)->nullable()->index()->after('study_instance_uid');
            $table->string('modalidad', 16)->nullable()->after('accession_number');
            $table->string('pacs_status', 32)->default('local')->after('modalidad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estudios', function (Blueprint $table) {
            $table->dropIndex(['study_instance_uid']);
            $table->dropIndex(['accession_number']);
            $table->dropColumn(['study_instance_uid', 'accession_number', 'modalidad', 'pacs_status']);
        });
    }
};
