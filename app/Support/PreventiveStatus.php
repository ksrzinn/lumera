<?php

namespace App\Support;

use App\Models\HealthQuestionnaire;
use App\Models\User;
use App\Support\Anamnesis\AnamnesisAnalyzer;
use App\Support\Anamnesis\QuestionnaireDefinition;

class PreventiveStatus
{
    private const KNOWN_PERIODS = ['menos_1_ano', '1_a_2_anos', '2_a_3_anos', 'mais_3_anos'];

    /**
     * Situação preventiva a partir do último questionário, ou null se ainda não respondeu.
     *
     * @return array{questionnaire_id: int, answered_at: string, level: string, level_label: string, exam_note: string}|null
     */
    public static function for(User $user): ?array
    {
        $questionnaire = $user->healthQuestionnaires()->latest()->first();

        if ($questionnaire === null) {
            return null;
        }

        $level = (new AnamnesisAnalyzer)->analyze($questionnaire)->level;

        return [
            'questionnaire_id' => $questionnaire->id,
            'answered_at' => $questionnaire->created_at->toDateString(),
            'level' => $level->value,
            'level_label' => $level->label(),
            'exam_note' => self::examNote($questionnaire),
        ];
    }

    private static function examNote(HealthQuestionnaire $questionnaire): string
    {
        if ($questionnaire->data_ultimo_preventivo !== null) {
            return __('anamnese.exame_nota.data', ['data' => $questionnaire->data_ultimo_preventivo->format('d/m/Y')]);
        }

        $period = $questionnaire->quando_ultimo_preventivo;

        if ($questionnaire->fez_preventivo === 'sim' && in_array($period, self::KNOWN_PERIODS, true)) {
            $label = QuestionnaireDefinition::options('quando_ultimo_preventivo')[$period];

            return __('anamnese.exame_nota.periodo', ['periodo' => lcfirst($label)]);
        }

        $case = match (true) {
            $questionnaire->fez_preventivo === 'sim' => 'sem_data',
            $questionnaire->fez_preventivo === 'nao' => 'nunca_feito',
            default => 'desconhecido',
        };

        return __("anamnese.exame_nota.{$case}");
    }
}
