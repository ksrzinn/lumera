<?php

namespace App\Enums;

enum RiskLevel: string
{
    case BaixoRisco = 'baixo_risco';
    case Atencao = 'atencao';
    case AltaPrioridade = 'alta_prioridade';

    public function label(): string
    {
        return match ($this) {
            self::BaixoRisco => 'Baixo risco',
            self::Atencao => 'Atenção',
            self::AltaPrioridade => 'Alta prioridade',
        };
    }

    /**
     * Orientação educativa exibida na tela de resultado. Não é diagnóstico.
     */
    public function orientation(): string
    {
        // TODO: revisar (textos de orientação; conferir com INCA e Ministério da Saúde)
        return match ($this) {
            self::BaixoRisco => 'Pelas suas respostas, seus cuidados preventivos parecem em dia. Continue fazendo o exame preventivo na periodicidade indicada e mantenha os hábitos de cuidado.',
            self::Atencao => 'Há pontos que merecem atenção: o uso consistente de camisinha e a vacinação contra o HPV ajudam a reduzir o risco. Converse com um profissional de saúde sobre essas medidas.',
            self::AltaPrioridade => 'Pelas suas respostas, vale priorizar o exame preventivo (Papanicolau). Procure uma unidade básica de saúde para saber como e quando realizá-lo.',
        };
    }
}
