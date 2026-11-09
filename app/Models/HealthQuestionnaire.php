<?php

namespace App\Models;

use App\Support\Anamnesis\AnamnesisAnalyzer;
use App\Support\Anamnesis\AnamnesisResult;
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
    'historico_alteracao',
    'acompanhamento_alteracao',
    'sintomas',
    'barreiras',
])]
class HealthQuestionnaire extends Model
{
    public const UPDATED_AT = null;

    public const PREVENTIVE_OPTIONS = [
        'sim' => 'Sim',
        'nao' => 'Não',
        'nao_lembro' => 'Não lembro',
        'prefiro_nao_responder' => 'Prefiro não responder',
    ];

    public const CONDOM_OPTIONS = [
        'sempre' => 'Sempre',
        'maioria_das_vezes' => 'Na maioria das vezes',
        'as_vezes' => 'Às vezes',
        'raramente' => 'Raramente',
        'nunca' => 'Nunca',
        'nao_se_aplica' => 'Não se aplica',
        'prefiro_nao_responder' => 'Prefiro não responder',
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
        'prefiro_nao_responder' => 'Prefiro não responder',
    ];

    public const ALTERATION_HISTORY_OPTIONS = [
        'sim' => 'Sim',
        'nao' => 'Não',
        'nao_sei' => 'Não sei',
        'prefiro_nao_responder' => 'Prefiro não responder',
    ];

    public const FOLLOW_UP_OPTIONS = [
        'sim' => 'Sim, fiz o acompanhamento recomendado',
        'em_andamento' => 'Estou em acompanhamento',
        'nao' => 'Não',
        'nao_sei' => 'Não sei',
    ];

    public const SYMPTOM_OPTIONS = [
        'sangramento_pos_relacao' => 'Sangramento após relações sexuais',
        'sangramento_fora_menstruacao' => 'Sangramento vaginal fora do período menstrual',
        'sangramento_pos_menopausa' => 'Sangramento após a menopausa',
        'dor_pelvica_persistente' => 'Dor pélvica que não passa',
        'corrimento_persistente' => 'Corrimento diferente do habitual que não passa',
        'sangramento_intenso_com_mal_estar' => 'Sangramento intenso com tontura, desmaio ou mal-estar',
    ];

    public const SYMPTOM_NONE = 'nenhum';

    public const BARRIER_OPTIONS = [
        'sem_tempo' => 'Falta de tempo',
        'dificuldade_agendamento' => 'Dificuldade para agendar',
        'medo' => 'Medo',
        'vergonha' => 'Vergonha',
        'falta_informacao' => 'Falta de informação',
    ];

    public const BARRIER_NONE = 'nenhuma';

    public const SKIPPED = 'prefiro_nao_responder';

    protected function casts(): array
    {
        return [
            'data_ultimo_preventivo' => 'date',
            'sintomas' => 'array',
            'barreiras' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function analysis(): AnamnesisResult
    {
        return (new AnamnesisAnalyzer)->analyze($this, $this->created_at);
    }
}
