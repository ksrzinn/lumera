<?php

namespace App\Support;

use App\Models\HealthQuestionnaire;
use App\Models\User;
use App\Support\Anamnesis\AnamnesisAnalyzer;

class PreventiveStatus
{
    /**
     * Situação do preventivo a partir do último questionário, ou null se ainda não respondeu.
     *
     * @return array{questionnaire_id: int, answered_at: string, level: string, level_label: string, last_preventive: ?string, exam_note: ?string}|null
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
            'last_preventive' => $questionnaire->data_ultimo_preventivo?->toDateString(),
            'exam_note' => self::examNote($questionnaire),
        ];
    }

    private static function examNote(HealthQuestionnaire $questionnaire): ?string
    {
        if ($questionnaire->data_ultimo_preventivo !== null) {
            return null;
        }

        $case = match ($questionnaire->fez_preventivo) {
            'sim' => 'sem_data',
            'nao' => 'nunca_feito',
            default => 'desconhecido',
        };

        return __("anamnese.exame_nota.{$case}");
    }
}
