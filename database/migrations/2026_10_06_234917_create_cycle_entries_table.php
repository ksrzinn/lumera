<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cycle_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->string('fluxo')->nullable();
            $table->json('sintomas')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'data_inicio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cycle_entries');
    }
};
