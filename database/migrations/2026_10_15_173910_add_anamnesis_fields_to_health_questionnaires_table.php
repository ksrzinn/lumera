<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const STRING_COLUMNS = [
        'faixa_etaria',
        'vida_sexual_iniciada',
        'idade_primeira_relacao',
        'possui_colo_utero',
        'quando_ultimo_preventivo',
        'lembra_ano_preventivo',
        'resultado_preventivo',
        'resultado_alterado_anterior',
        'acompanhamento_alteracao',
        'teste_hpv',
        'frequencia_consultas',
        'usa_contraceptivo',
        'tempo_uso_contraceptivo',
        'uso_prolongado_pilula',
        'acredita_anticoncepcional_substitui',
        'situacoes_preservativo',
        'sabe_preservativo_hpv',
        'exames_ist',
        'diagnostico_hpv',
        'diagnostico_outra_ist',
        'idade_primeira_dose',
        'sabe_onde_vacinar',
        'acredita_vacina_elimina_rastreio',
    ];

    private const JSON_COLUMNS = ['metodos_contraceptivos', 'sintomas', 'barreiras'];

    public function up(): void
    {
        Schema::table('health_questionnaires', function (Blueprint $table) {
            foreach (self::STRING_COLUMNS as $column) {
                $table->string($column)->nullable();
            }

            foreach (self::JSON_COLUMNS as $column) {
                $table->jsonb($column)->nullable();
            }

            $table->unsignedSmallInteger('idade')->nullable()->change();
            $table->string('usa_camisinha')->nullable()->change();
            $table->string('vacinada_hpv')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('health_questionnaires', function (Blueprint $table) {
            $table->dropColumn([...self::STRING_COLUMNS, ...self::JSON_COLUMNS]);
        });
    }
};
