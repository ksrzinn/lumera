<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adiciona as colunas do questionário de 27 perguntas em bancos que rodaram a
 * versão anterior de 2026_10_15_173910 (só historico_alteracao, acompanhamento_alteracao,
 * sintomas e barreiras). Em bancos que já têm as colunas, não faz nada além de
 * garantir idade, usa_camisinha e vacinada_hpv como nullable.
 */
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
        $missingStrings = $this->missing(self::STRING_COLUMNS);
        $missingJson = $this->missing(self::JSON_COLUMNS);

        Schema::table('health_questionnaires', function (Blueprint $table) use ($missingStrings, $missingJson) {
            foreach ($missingStrings as $column) {
                $table->string($column)->nullable();
            }

            foreach ($missingJson as $column) {
                $table->jsonb($column)->nullable();
            }

            $table->unsignedSmallInteger('idade')->nullable()->change();
            $table->string('usa_camisinha')->nullable()->change();
            $table->string('vacinada_hpv')->nullable()->change();
        });
    }

    /**
     * As colunas podem ter sido criadas por 2026_10_15_173910; quem as remove é o down() dela.
     */
    public function down(): void
    {
    }

    private function missing(array $columns): array
    {
        return array_values(array_filter(
            $columns,
            fn (string $column) => ! Schema::hasColumn('health_questionnaires', $column),
        ));
    }
};
