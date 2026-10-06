<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('titulo');
            $table->dateTime('data_hora');
            $table->string('tipo');
            $table->boolean('concluido')->default(false);

            $table->index(['user_id', 'data_hora']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
