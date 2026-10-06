<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['data_inicio', 'data_fim', 'fluxo', 'sintomas', 'notas'])]
class CycleEntry extends Model
{
    public const FLOW_OPTIONS = [
        'leve' => 'Leve',
        'moderado' => 'Moderado',
        'intenso' => 'Intenso',
    ];

    public const SYMPTOM_OPTIONS = [
        'sangramento_fora_do_periodo' => 'Sangramento fora do período menstrual',
        'corrimento_odor_forte' => 'Corrimento com odor forte',
        'dor_relacao_sexual' => 'Dor durante a relação sexual',
        'dor_pelvica' => 'Dor pélvica',
        'nenhum' => 'Nenhum sintoma',
    ];

    protected function casts(): array
    {
        return [
            'data_inicio' => 'date',
            'data_fim' => 'date',
            'sintomas' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
