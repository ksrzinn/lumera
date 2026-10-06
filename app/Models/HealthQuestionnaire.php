<?php

namespace App\Models;

use App\Enums\RiskLevel;
use App\Support\RiskClassifier;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'idade',
    'fez_preventivo',
    'data_ultimo_preventivo',
    'usa_camisinha',
    'metodo_contraceptivo',
    'vacinada_hpv',
])]
class HealthQuestionnaire extends Model
{
    public const UPDATED_AT = null;

    public const PREVENTIVE_OPTIONS = [
        'sim' => 'Sim',
        'nao' => 'Não',
        'nao_lembro' => 'Não lembro',
    ];

    public const CONDOM_OPTIONS = [
        'sempre' => 'Sempre',
        'as_vezes' => 'Às vezes',
        'nunca' => 'Nunca',
    ];

    public const CONTRACEPTIVE_OPTIONS = [
        'nenhum' => 'Nenhum',
        'pilula' => 'Pílula',
        'dispositivo_intrauterino' => 'DIU',
        'injetavel' => 'Injetável',
        'implante' => 'Implante',
        'camisinha' => 'Camisinha',
        'outro' => 'Outro',
    ];

    public const HPV_VACCINE_OPTIONS = [
        'sim' => 'Sim',
        'nao' => 'Não',
        'nao_sei' => 'Não sei',
    ];

    protected function casts(): array
    {
        return [
            'data_ultimo_preventivo' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function riskLevel(): RiskLevel
    {
        return (new RiskClassifier)->classify($this, $this->created_at);
    }
}
