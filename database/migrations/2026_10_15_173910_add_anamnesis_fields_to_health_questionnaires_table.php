<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('health_questionnaires', function (Blueprint $table) {
            $table->string('historico_alteracao')->nullable();
            $table->string('acompanhamento_alteracao')->nullable();
            $table->jsonb('sintomas')->nullable();
            $table->jsonb('barreiras')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('health_questionnaires', function (Blueprint $table) {
            $table->dropColumn(['historico_alteracao', 'acompanhamento_alteracao', 'sintomas', 'barreiras']);
        });
    }
};
