<?php

namespace App\Support\Anamnesis;

use App\Enums\CareLevel;
use App\Models\HealthQuestionnaire;
use Carbon\CarbonInterface;

/**
 * Regras por combinação de respostas, sem pontuação. "Não sei", "não lembro" e
 * "prefiro não responder" são informação desconhecida, nunca resposta negativa.
 *
 * Questionários antigos (sem `faixa_etaria`) são lidos pelas colunas antigas
 * (`idade`, `data_ultimo_preventivo`, `metodo_contraceptivo`).
 */
class AnamnesisAnalyzer
{
    // TODO: revisar (janela de exame considerado recente; citologia do INCA usa 3 anos, teste DNA-HPV usa 5)
    private const RECENT_EXAM_YEARS = 3;

    // TODO: revisar (faixa etária de rastreamento 25 a 64 anos, INCA / Ministério da Saúde)
    private const SCREENING_BANDS = ['25_29', '30_39', '40_49', '50_59', '60_64'];

    private const BELOW_SCREENING_BANDS = ['menos_18', '18_24'];

    private const NON_BARRIER_METHODS = [
        'pilula', 'diu_cobre', 'diu_hormonal', 'implante_hormonal', 'injecao', 'adesivo', 'anel_vaginal',
        'implante_outro_hormonal', 'laqueadura',
        // valores do questionário antigo
        'dispositivo_intrauterino', 'injetavel', 'implante',
    ];

    private const INCONSISTENT_CONDOM_USE = ['maioria_das_vezes', 'as_vezes', 'raramente', 'nunca'];

    private const SKIPPED = 'prefiro_nao_responder';

    private const UNSURE_EXAM = ['nao_tenho_certeza', 'nao_lembro', self::SKIPPED];

    public function analyze(HealthQuestionnaire $questionnaire, ?CarbonInterface $today = null): AnamnesisResult
    {
        $today ??= now();

        $findings = [
            ...$this->examFindings($questionnaire, $today),
            ...$this->alterationFindings($questionnaire),
            ...$this->symptomFindings($questionnaire),
            ...$this->condomFindings($questionnaire),
            ...$this->vaccineFindings($questionnaire),
            ...$this->knowledgeFindings($questionnaire),
            ...$this->barrierFindings($questionnaire),
        ];

        if ($questionnaire->faixa_etaria === null) {
            $findings[] = new Finding('versao_anterior', CareLevel::EmDia);
        }

        usort($findings, fn (Finding $a, Finding $b) => $b->level->weight() <=> $a->level->weight());

        return new AnamnesisResult(
            level: $findings[0]->level ?? CareLevel::EmDia,
            urgent: in_array('sangramento_intenso_com_mal_estar', $questionnaire->sintomas ?? [], true),
            findings: $findings,
        );
    }

    /**
     * @return list<Finding>
     */
    private function examFindings(HealthQuestionnaire $questionnaire, CarbonInterface $today): array
    {
        if ($questionnaire->possui_colo_utero === 'nao') {
            return [new Finding('sem_colo_utero', CareLevel::EmDia)];
        }

        if ($questionnaire->vida_sexual_iniciada === 'nao') {
            return [new Finding('vida_sexual_nao_iniciada', CareLevel::EmDia)];
        }

        $findings = $questionnaire->possui_colo_utero === 'nao_sei'
            ? [new Finding('colo_utero_desconhecido', CareLevel::AtencaoPreventiva)]
            : [];

        $answer = $questionnaire->fez_preventivo;
        $period = $this->examPeriod($questionnaire, $today);

        if ($answer === 'nao') {
            return [...$findings, $this->neverScreenedFinding($this->ageBand($questionnaire))];
        }

        if (in_array($answer, self::UNSURE_EXAM, true) || $period === 'nao_lembro_se_fez' || ($answer === 'sim' && $period === 'nunca')) {
            return [...$findings, new Finding('exame_desconhecido', CareLevel::AtencaoPreventiva)];
        }

        if ($answer === 'sim' && $period === 'sem_data') {
            return [...$findings, new Finding('exame_sem_data', CareLevel::AtencaoPreventiva)];
        }

        if ($answer === 'sim' && $period === 'mais_3_anos') {
            return [...$findings, new Finding('exame_antigo', CareLevel::AtencaoPreventiva)];
        }

        return $findings;
    }

    private function neverScreenedFinding(string $band): Finding
    {
        return match (true) {
            in_array($band, self::SCREENING_BANDS, true) => new Finding('exame_nunca_feito', CareLevel::AvaliacaoProfissional),
            in_array($band, self::BELOW_SCREENING_BANDS, true) => new Finding('exame_fora_da_faixa', CareLevel::EmDia),
            $band === '65_mais' => new Finding('exame_acima_da_faixa', CareLevel::AtencaoPreventiva),
            default => new Finding('exame_nunca_feito_idade_desconhecida', CareLevel::AtencaoPreventiva),
        };
    }

    private function examPeriod(HealthQuestionnaire $questionnaire, CarbonInterface $today): ?string
    {
        if ($questionnaire->quando_ultimo_preventivo !== null) {
            return $questionnaire->quando_ultimo_preventivo;
        }

        if ($questionnaire->fez_preventivo !== 'sim') {
            return null;
        }

        $date = $questionnaire->data_ultimo_preventivo;

        return match (true) {
            $date === null => 'sem_data',
            $date->lt($today->copy()->startOfDay()->subYears(self::RECENT_EXAM_YEARS)) => 'mais_3_anos',
            default => 'menos_1_ano',
        };
    }

    private function ageBand(HealthQuestionnaire $questionnaire): string
    {
        if ($questionnaire->faixa_etaria !== null) {
            return $questionnaire->faixa_etaria;
        }

        return match (true) {
            $questionnaire->idade === null => 'prefiro_nao_informar',
            $questionnaire->idade < 18 => 'menos_18',
            $questionnaire->idade < 25 => '18_24',
            $questionnaire->idade < 30 => '25_29',
            $questionnaire->idade < 40 => '30_39',
            $questionnaire->idade < 50 => '40_49',
            $questionnaire->idade < 60 => '50_59',
            $questionnaire->idade < 65 => '60_64',
            default => '65_mais',
        };
    }

    /**
     * @return list<Finding>
     */
    private function alterationFindings(HealthQuestionnaire $questionnaire): array
    {
        $altered = in_array($questionnaire->resultado_preventivo, ['alterado', 'repetir'], true)
            || $questionnaire->resultado_alterado_anterior === 'sim';

        if ($altered) {
            return [
                $questionnaire->acompanhamento_alteracao === 'sim_todo'
                    ? new Finding('alteracao_com_acompanhamento', CareLevel::EmDia)
                    : new Finding('alteracao_sem_acompanhamento', CareLevel::AvaliacaoProfissional),
            ];
        }

        if (in_array($questionnaire->resultado_preventivo, ['nao_sei', 'sem_resultado'], true)
            || $questionnaire->resultado_alterado_anterior === 'nao_sei') {
            return [new Finding('resultado_desconhecido', CareLevel::AtencaoPreventiva)];
        }

        return [];
    }

    /**
     * @return list<Finding>
     */
    private function symptomFindings(HealthQuestionnaire $questionnaire): array
    {
        $reported = $questionnaire->sintomas ?? [];
        $findings = [];

        foreach (array_keys(QuestionnaireDefinition::options('sintomas')) as $symptom) {
            if (in_array($symptom, ['nenhum', self::SKIPPED], true)) {
                continue;
            }

            if (in_array($symptom, $reported, true)) {
                $findings[] = new Finding("sintoma_{$symptom}", CareLevel::AvaliacaoProfissional);
            }
        }

        if (in_array(self::SKIPPED, $reported, true)) {
            $findings[] = new Finding('sintomas_desconhecido', CareLevel::AtencaoPreventiva);
        }

        return $findings;
    }

    /**
     * @return list<Finding>
     */
    private function condomFindings(HealthQuestionnaire $questionnaire): array
    {
        if ($questionnaire->vida_sexual_iniciada === 'nao') {
            return [];
        }

        $answer = $questionnaire->usa_camisinha;

        if ($answer === 'sempre') {
            return [new Finding('camisinha_sempre', CareLevel::EmDia)];
        }

        if (! in_array($answer, self::INCONSISTENT_CONDOM_USE, true)) {
            return [];
        }

        $findings = [new Finding('camisinha_inconsistente', CareLevel::AtencaoPreventiva)];

        if (array_intersect($this->contraceptiveMethods($questionnaire), self::NON_BARRIER_METHODS) !== []) {
            $findings[] = new Finding('metodo_nao_protege_ist', CareLevel::EmDia);
        }

        return $findings;
    }

    /**
     * @return list<string>
     */
    private function contraceptiveMethods(HealthQuestionnaire $questionnaire): array
    {
        return $questionnaire->metodos_contraceptivos
            ?? array_filter([$questionnaire->metodo_contraceptivo]);
    }

    /**
     * @return list<Finding>
     */
    private function vaccineFindings(HealthQuestionnaire $questionnaire): array
    {
        return match ($questionnaire->vacinada_hpv) {
            null => [],
            'completou', 'sim' => [new Finding('vacina_realizada', CareLevel::EmDia)],
            default => [new Finding('vacina_pendente', CareLevel::AtencaoPreventiva)],
        };
    }

    /**
     * Crenças, diagnósticos e hábitos que geram orientação. Só as dúvidas sobre o rastreamento elevam o nível.
     *
     * @return list<Finding>
     */
    private function knowledgeFindings(HealthQuestionnaire $questionnaire): array
    {
        $rules = [
            ['acredita_anticoncepcional_substitui', ['sim', 'nao_sei'], 'duvida_contraceptivo_substitui', CareLevel::AtencaoPreventiva],
            ['acredita_vacina_elimina_rastreio', ['sim', 'nao_sei'], 'duvida_vacina_substitui', CareLevel::AtencaoPreventiva],
            ['diagnostico_hpv', ['sim'], 'diagnostico_hpv', CareLevel::EmDia],
            ['diagnostico_outra_ist', ['sim'], 'diagnostico_ist', CareLevel::EmDia],
            ['exames_ist', ['nunca', 'nao_sei'], 'exames_ist_sugestao', CareLevel::EmDia],
            ['sabe_preservativo_hpv', ['nao', 'ja_ouvi'], 'preservativo_hpv_orientacao', CareLevel::EmDia],
            ['sabe_onde_vacinar', ['nao', 'duvidas'], 'vacina_onde', CareLevel::EmDia],
            ['frequencia_consultas', ['so_sintomas', 'so_receita', 'nao_costuma'], 'consultas_pouco_frequentes', CareLevel::EmDia],
            ['uso_prolongado_pilula', ['sim_5_ou_mais', 'sim_nao_sei_tempo'], 'pilula_prolongada', CareLevel::EmDia],
        ];

        $findings = [];

        foreach ($rules as [$field, $triggers, $code, $level]) {
            if (in_array($questionnaire->{$field}, $triggers, true)) {
                $findings[] = new Finding($code, $level);
            }
        }

        return $findings;
    }

    /**
     * @return list<Finding>
     */
    private function barrierFindings(HealthQuestionnaire $questionnaire): array
    {
        $reported = $questionnaire->barreiras ?? [];
        $findings = [];

        foreach (array_keys(QuestionnaireDefinition::options('barreiras')) as $barrier) {
            if ($barrier !== 'nenhuma' && in_array($barrier, $reported, true)) {
                $findings[] = new Finding("barreira_{$barrier}", CareLevel::EmDia);
            }
        }

        return $findings;
    }
}
