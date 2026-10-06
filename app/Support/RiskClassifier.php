<?php

namespace App\Support;

use App\Enums\RiskLevel;
use App\Models\HealthQuestionnaire;
use Carbon\CarbonInterface;

class RiskClassifier
{
    private const MAX_YEARS_SINCE_PREVENTIVE = 3;

    public function classify(HealthQuestionnaire $questionnaire, ?CarbonInterface $today = null): RiskLevel
    {
        $today ??= now();

        if ($this->preventiveIsMissingOrOverdue($questionnaire, $today)) {
            return RiskLevel::AltaPrioridade;
        }

        // TODO: revisar ("não sei" sobre a vacina é tratado como sem vacina confirmada)
        if ($questionnaire->usa_camisinha !== 'sempre' || $questionnaire->vacinada_hpv !== 'sim') {
            return RiskLevel::Atencao;
        }

        return RiskLevel::BaixoRisco;
    }

    private function preventiveIsMissingOrOverdue(HealthQuestionnaire $questionnaire, CarbonInterface $today): bool
    {
        // TODO: revisar ("não lembro" é tratado como preventivo não confirmado)
        if ($questionnaire->fez_preventivo !== 'sim' || $questionnaire->data_ultimo_preventivo === null) {
            return true;
        }

        return $questionnaire->data_ultimo_preventivo
            ->lt($today->startOfDay()->subYears(self::MAX_YEARS_SINCE_PREVENTIVE));
    }
}
