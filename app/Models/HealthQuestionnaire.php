<?php

namespace App\Models;

use App\Support\Anamnesis\AnamnesisAnalyzer;
use App\Support\Anamnesis\AnamnesisResult;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// As colunas `idade`, `data_ultimo_preventivo` e `metodo_contraceptivo` pertencem a questionários antigos
// e continuam legíveis; o formulário atual grava as demais (ver config/questionnaire.php).
#[Fillable([
    'idade',
    'data_ultimo_preventivo',
    'metodo_contraceptivo',
    'faixa_etaria',
    'vida_sexual_iniciada',
    'idade_primeira_relacao',
    'possui_colo_utero',
    'fez_preventivo',
    'quando_ultimo_preventivo',
    'lembra_ano_preventivo',
    'resultado_preventivo',
    'resultado_alterado_anterior',
    'acompanhamento_alteracao',
    'teste_hpv',
    'frequencia_consultas',
    'usa_contraceptivo',
    'metodos_contraceptivos',
    'tempo_uso_contraceptivo',
    'uso_prolongado_pilula',
    'acredita_anticoncepcional_substitui',
    'usa_camisinha',
    'situacoes_preservativo',
    'sabe_preservativo_hpv',
    'exames_ist',
    'diagnostico_hpv',
    'diagnostico_outra_ist',
    'vacinada_hpv',
    'idade_primeira_dose',
    'sabe_onde_vacinar',
    'acredita_vacina_elimina_rastreio',
    'sintomas',
    'barreiras',
])]
class HealthQuestionnaire extends Model
{
    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'data_ultimo_preventivo' => 'date',
            'metodos_contraceptivos' => 'array',
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
