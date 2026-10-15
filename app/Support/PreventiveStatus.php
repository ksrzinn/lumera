<?php

namespace App\Support;

use App\Models\User;

class PreventiveStatus
{
    /**
     * Situação do preventivo a partir do último questionário, ou null se ainda não respondeu.
     *
     * @return array{questionnaire_id: int, answered_at: string, level: string, level_label: string, last_preventive: ?string}|null
     */
    public static function for(User $user): ?array
    {
        $questionnaire = $user->healthQuestionnaires()->latest()->first();

        if ($questionnaire === null) {
            return null;
        }

        $level = $questionnaire->analysis()->level;

        return [
            'questionnaire_id' => $questionnaire->id,
            'answered_at' => $questionnaire->created_at->toDateString(),
            'level' => $level->value,
            'level_label' => $level->label(),
            'last_preventive' => $questionnaire->data_ultimo_preventivo?->toDateString(),
        ];
    }
}
