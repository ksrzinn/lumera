<?php

namespace Tests\Concerns;

trait AnswersQuestionnaire
{
    /**
     * Respostas completas que resultam em "acompanhamento aparentemente em dia".
     *
     * @return array<string, mixed>
     */
    protected function validAnswers(): array
    {
        return [
            'faixa_etaria' => '30_39',
            'vida_sexual_iniciada' => 'sim',
            'idade_primeira_relacao' => '18_20',
            'possui_colo_utero' => 'sim',
            'fez_preventivo' => 'sim',
            'quando_ultimo_preventivo' => 'menos_1_ano',
            'lembra_ano_preventivo' => 'sim_ano',
            'resultado_preventivo' => 'normal',
            'resultado_alterado_anterior' => 'nao',
            'acompanhamento_alteracao' => 'nao_se_aplica',
            'teste_hpv' => 'nao',
            'frequencia_consultas' => 'anual',
            'usa_contraceptivo' => 'sim',
            'metodos_contraceptivos' => ['pilula'],
            'tempo_uso_contraceptivo' => '1_a_5_anos',
            'uso_prolongado_pilula' => 'nao',
            'acredita_anticoncepcional_substitui' => 'nao',
            'usa_camisinha' => 'sempre',
            'situacoes_preservativo' => 'todas',
            'sabe_preservativo_hpv' => 'sim',
            'exames_ist' => 'sim_12_meses',
            'diagnostico_hpv' => 'nao',
            'diagnostico_outra_ist' => 'nao',
            'vacinada_hpv' => 'completou',
            'idade_primeira_dose' => '9_14',
            'sabe_onde_vacinar' => 'sim',
            'acredita_vacina_elimina_rastreio' => 'nao',
            'sintomas' => ['nenhum'],
            'barreiras' => ['nenhuma'],
        ];
    }
}
