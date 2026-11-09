<?php

namespace App\Support\Anamnesis;

use App\Enums\CareLevel;
use App\Models\HealthQuestionnaire;
use Carbon\CarbonInterface;

/**
 * Regras por combinação de respostas, sem pontuação. "Não sei", "não lembro" e
 * "prefiro não responder" são tratadas como informação desconhecida, nunca como resposta negativa.
 */
class AnamnesisAnalyzer
{
    // TODO: revisar (janela de exame considerado recente; citologia do INCA usa 3 anos, teste DNA-HPV usa 5)
    private const RECENT_EXAM_YEARS = 3;

    // TODO: revisar (faixa etária de rastreamento, INCA / Ministério da Saúde)
    private const SCREENING_MIN_AGE = 25;

    private const SCREENING_MAX_AGE = 64;

    private const NON_BARRIER_METHODS = ['pilula', 'dispositivo_intrauterino', 'injetavel', 'implante'];

    private const INCONSISTENT_CONDOM_USE = ['maioria_das_vezes', 'as_vezes', 'raramente', 'nunca'];

    private const UNKNOWN = ['nao_lembro', 'nao_sei', HealthQuestionnaire::SKIPPED];

    public function analyze(HealthQuestionnaire $questionnaire, ?CarbonInterface $today = null): AnamnesisResult
    {
        $today ??= now();

        $findings = [
            ...$this->examFindings($questionnaire, $today),
            ...$this->alterationFindings($questionnaire),
            ...$this->symptomFindings($questionnaire),
            ...$this->condomFindings($questionnaire),
            ...$this->vaccineFindings($questionnaire),
            ...$this->barrierFindings($questionnaire),
        ];

        if ($questionnaire->sintomas === null) {
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
        $answer = $questionnaire->fez_preventivo;

        if (in_array($answer, self::UNKNOWN, true)) {
            return [new Finding('exame_desconhecido', CareLevel::AtencaoPreventiva)];
        }

        if ($answer === 'nao') {
            return [$this->neverScreenedFinding($questionnaire->idade)];
        }

        if ($answer !== 'sim') {
            return [];
        }

        if ($questionnaire->data_ultimo_preventivo === null) {
            return [new Finding('exame_sem_data', CareLevel::AtencaoPreventiva)];
        }

        if ($questionnaire->data_ultimo_preventivo->lt($today->startOfDay()->subYears(self::RECENT_EXAM_YEARS))) {
            return [new Finding('exame_antigo', CareLevel::AtencaoPreventiva)];
        }

        return [];
    }

    private function neverScreenedFinding(int $age): Finding
    {
        if ($age < self::SCREENING_MIN_AGE) {
            return new Finding('exame_fora_da_faixa', CareLevel::EmDia);
        }

        if ($age > self::SCREENING_MAX_AGE) {
            return new Finding('exame_acima_da_faixa', CareLevel::AtencaoPreventiva);
        }

        return new Finding('exame_nunca_feito', CareLevel::AvaliacaoProfissional);
    }

    /**
     * @return list<Finding>
     */
    private function alterationFindings(HealthQuestionnaire $questionnaire): array
    {
        $history = $questionnaire->historico_alteracao;

        if ($history === 'sim') {
            return [
                in_array($questionnaire->acompanhamento_alteracao, ['sim', 'em_andamento'], true)
                    ? new Finding('alteracao_com_acompanhamento', CareLevel::EmDia)
                    : new Finding('alteracao_sem_acompanhamento', CareLevel::AvaliacaoProfissional),
            ];
        }

        if (in_array($history, self::UNKNOWN, true)) {
            return [new Finding('historico_desconhecido', CareLevel::AtencaoPreventiva)];
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

        foreach (array_keys(HealthQuestionnaire::SYMPTOM_OPTIONS) as $symptom) {
            if (in_array($symptom, $reported, true)) {
                $findings[] = new Finding("sintoma_{$symptom}", CareLevel::AvaliacaoProfissional);
            }
        }

        if (in_array(HealthQuestionnaire::SKIPPED, $reported, true)) {
            $findings[] = new Finding('sintomas_desconhecido', CareLevel::AtencaoPreventiva);
        }

        return $findings;
    }

    /**
     * @return list<Finding>
     */
    private function condomFindings(HealthQuestionnaire $questionnaire): array
    {
        $answer = $questionnaire->usa_camisinha;

        if ($answer === 'sempre') {
            return [new Finding('camisinha_sempre', CareLevel::EmDia)];
        }

        if (! in_array($answer, self::INCONSISTENT_CONDOM_USE, true)) {
            return [];
        }

        $findings = [new Finding('camisinha_inconsistente', CareLevel::AtencaoPreventiva)];

        if (in_array($questionnaire->metodo_contraceptivo, self::NON_BARRIER_METHODS, true)) {
            $findings[] = new Finding('metodo_nao_protege_ist', CareLevel::EmDia);
        }

        return $findings;
    }

    /**
     * @return list<Finding>
     */
    private function vaccineFindings(HealthQuestionnaire $questionnaire): array
    {
        return [
            $questionnaire->vacinada_hpv === 'sim'
                ? new Finding('vacina_realizada', CareLevel::EmDia)
                : new Finding('vacina_pendente', CareLevel::AtencaoPreventiva),
        ];
    }

    /**
     * @return list<Finding>
     */
    private function barrierFindings(HealthQuestionnaire $questionnaire): array
    {
        $reported = $questionnaire->barreiras ?? [];
        $findings = [];

        foreach (array_keys(HealthQuestionnaire::BARRIER_OPTIONS) as $barrier) {
            if (in_array($barrier, $reported, true)) {
                $findings[] = new Finding("barreira_{$barrier}", CareLevel::EmDia);
            }
        }

        return $findings;
    }
}
