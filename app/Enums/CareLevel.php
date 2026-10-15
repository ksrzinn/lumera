<?php

namespace App\Enums;

enum CareLevel: string
{
    case EmDia = 'em_dia';
    case AtencaoPreventiva = 'atencao_preventiva';
    case AvaliacaoProfissional = 'avaliacao_profissional';

    public function weight(): int
    {
        return match ($this) {
            self::EmDia => 0,
            self::AtencaoPreventiva => 1,
            self::AvaliacaoProfissional => 2,
        };
    }

    public function label(): string
    {
        return __("anamnese.nivel.{$this->value}.label");
    }

    /**
     * Resumo da classificação geral. Orientação educativa, não diagnóstico.
     */
    public function summary(): string
    {
        return __("anamnese.nivel.{$this->value}.summary");
    }

    public function followUp(): string
    {
        return __("anamnese.nivel.{$this->value}.follow_up");
    }
}
