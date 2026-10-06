<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_questionnaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('idade');
            $table->string('fez_preventivo');
            $table->date('data_ultimo_preventivo')->nullable();
            $table->string('usa_camisinha');
            $table->string('metodo_contraceptivo')->nullable();
            $table->string('vacinada_hpv');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_questionnaires');
    }
};
