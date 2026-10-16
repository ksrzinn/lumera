<?php

// Textos da análise da anamnese. Orientação educativa, nunca diagnóstico.
// Fontes: INCA e Ministério da Saúde.
// TODO: revisar (todos os textos clínicos abaixo precisam de conferência da profissional de saúde)

return [
    'nivel' => [
        'em_dia' => [
            'label' => 'Acompanhamento aparentemente em dia',
            'summary' => 'Com base nas suas respostas, não identificamos pendência conhecida de rastreamento ou acompanhamento. Isso não significa ausência de doenças: continue com seus cuidados e consultas.',
            'follow_up' => 'Mantenha o acompanhamento de saúde e converse com um profissional sobre quando fazer o próximo exame preventivo.',
        ],
        'atencao_preventiva' => [
            'label' => 'Atenção preventiva',
            'summary' => 'Com base nas suas respostas, identificamos alguns pontos importantes para a sua prevenção. Eles não indicam doença, são oportunidades de cuidado.',
            'follow_up' => 'Procure a unidade básica de saúde (UBS) de referência para conferir datas, resultados e vacinação, e tirar suas dúvidas.',
        ],
        'avaliacao_profissional' => [
            'label' => 'Necessidade de avaliação profissional',
            'summary' => 'Com base nas suas respostas, vale conversar com um profissional de saúde em breve. Isso não é um diagnóstico: só uma avaliação presencial pode esclarecer a sua situação.',
            'follow_up' => 'Agende uma consulta na UBS de referência ou com um profissional de saúde o quanto antes.',
        ],
    ],

    'exame_nota' => [
        'data' => 'Último exame informado: :data.',
        'periodo' => 'Último exame: :periodo.',
        'sem_data' => 'Você informou que fez o exame, mas não sabe a data.',
        'nunca_feito' => 'Você informou que ainda não fez o exame.',
        'desconhecido' => 'Você não soube ou preferiu não informar sobre o exame.',
    ],

    'exame_desconhecido' => [
        'title' => 'Não foi possível confirmar seu exame preventivo',
        'guidance' => 'Você não soube ou preferiu não informar sobre o exame preventivo. Consulte seu histórico na unidade de saúde ou converse com um profissional para saber quando fez o último e quando deve fazer o próximo.',
    ],
    'exame_nunca_feito' => [
        'title' => 'Exame preventivo ainda não realizado',
        'guidance' => 'Você informou que nunca fez o exame preventivo. Procure a UBS para verificar se você está na faixa etária indicada para o rastreamento e como realizá-lo.',
    ],
    'exame_fora_da_faixa' => [
        'title' => 'Exame preventivo e faixa etária',
        'guidance' => 'O rastreamento do câncer do colo do útero é indicado para uma faixa etária específica. Pela sua idade, converse com um profissional de saúde para saber se já é o momento.',
    ],
    'exame_acima_da_faixa' => [
        'title' => 'Exame preventivo e faixa etária',
        'guidance' => 'Você informou que nunca fez o exame preventivo. Converse com um profissional de saúde para avaliar se ainda há necessidade de realizá-lo, considerando sua idade e seu histórico.',
    ],
    'exame_sem_data' => [
        'title' => 'Data do último exame preventivo não informada',
        'guidance' => 'Você fez o exame, mas não sabe a data. Procure seu histórico na unidade de saúde ou converse com um profissional para verificar quando deve fazer o próximo.',
    ],
    'exame_antigo' => [
        'title' => 'Verificar se o exame preventivo precisa ser atualizado',
        'guidance' => 'Seu último exame preventivo foi há algum tempo. Converse com um profissional de saúde para avaliar a necessidade de atualização, considerando sua idade, seu histórico e as diretrizes de rastreamento vigentes.',
    ],

    'alteracao_com_acompanhamento' => [
        'title' => 'Histórico de alteração em acompanhamento',
        'guidance' => 'Você informou histórico de HPV, lesão ou alteração em exame e que faz ou fez o acompanhamento recomendado. Siga as orientações da sua equipe de saúde. Um diagnóstico anterior de HPV não significa câncer.',
    ],
    'alteracao_sem_acompanhamento' => [
        'title' => 'Histórico de alteração sem acompanhamento confirmado',
        'guidance' => 'Você informou histórico de HPV, lesão no colo do útero ou alteração em exame, sem acompanhamento confirmado. Procure um serviço de saúde para verificar se está tudo em dia. Um diagnóstico anterior de HPV não significa câncer.',
    ],
    'sintoma_sangramento_pos_relacao' => [
        'title' => 'Sangramento após relações sexuais',
        'guidance' => 'Esse sintoma pode ter várias causas e merece avaliação profissional. Procure a UBS ou um profissional de saúde.',
    ],
    'sintoma_sangramento_fora_menstruacao' => [
        'title' => 'Sangramento vaginal fora do período menstrual',
        'guidance' => 'Esse sintoma pode ter várias causas e merece avaliação profissional. Procure a UBS ou um profissional de saúde.',
    ],
    'sintoma_sangramento_pos_menopausa' => [
        'title' => 'Sangramento após a menopausa',
        'guidance' => 'Esse sintoma pode ter várias causas e merece avaliação profissional. Procure a UBS ou um profissional de saúde.',
    ],
    'sintoma_dor_pelvica_persistente' => [
        'title' => 'Dor pélvica persistente',
        'guidance' => 'Esse sintoma pode ter várias causas e merece avaliação profissional. Procure a UBS ou um profissional de saúde.',
    ],
    'sintoma_corrimento_persistente' => [
        'title' => 'Corrimento persistente, diferente do habitual',
        'guidance' => 'Esse sintoma pode ter várias causas e merece avaliação profissional. Procure a UBS ou um profissional de saúde.',
    ],
    'sintoma_sangramento_intenso_com_mal_estar' => [
        'title' => 'Sangramento intenso com tontura, desmaio ou mal-estar',
        'guidance' => 'Procure atendimento de urgência agora: vá a um pronto atendimento ou ligue para o SAMU (192). Esse sintoma pode ter várias causas e precisa ser avaliado rapidamente.',
    ],
    'sintomas_desconhecido' => [
        'title' => 'Sintomas não informados',
        'guidance' => 'Você preferiu não informar sobre sintomas. Se notar sangramento fora do habitual, dor pélvica ou corrimento persistente, procure um profissional de saúde.',
    ],

    'camisinha_sempre' => [
        'title' => 'Uso de preservativo',
        'guidance' => 'Usar preservativo em todas as relações é um hábito importante: reduz o risco de várias infecções sexualmente transmissíveis (ISTs). Ele reduz o risco de HPV, mas não protege totalmente, e não substitui a vacina contra o HPV nem o exame preventivo.',
    ],
    'camisinha_inconsistente' => [
        'title' => 'Uso do preservativo',
        'guidance' => 'O uso consistente do preservativo ajuda a reduzir o risco de ISTs, incluindo o HPV, embora não ofereça proteção total. Ele também não substitui a vacina contra o HPV nem o exame preventivo. Converse com a equipe de saúde, sem julgamentos, sobre o que funciona melhor para você.',
    ],
    'metodo_nao_protege_ist' => [
        'title' => 'Outros métodos contraceptivos e ISTs',
        'guidance' => 'Pílula, DIU, injetável e implante ajudam a prevenir a gravidez, mas não protegem contra infecções sexualmente transmissíveis.',
    ],

    'vacina_realizada' => [
        'title' => 'Vacina contra o HPV',
        'guidance' => 'A vacina contra o HPV é uma medida importante de prevenção, mas não elimina completamente o risco de câncer do colo do útero. Mantenha o exame preventivo em dia.',
    ],
    'vacina_pendente' => [
        'title' => 'Situação da vacina contra o HPV',
        'guidance' => 'Verifique sua situação vacinal em uma unidade de saúde. A indicação depende dos critérios de elegibilidade e do calendário de vacinação vigente.',
    ],

    'barreira_sem_tempo' => [
        'title' => 'Falta de tempo',
        'guidance' => 'Pergunte na sua UBS de referência quais são os horários disponíveis para o exame preventivo e as consultas.',
    ],
    'barreira_dificuldade_agendamento' => [
        'title' => 'Dificuldade para agendar',
        'guidance' => 'Procure a UBS de referência do seu bairro para informações sobre como agendar o exame e as consultas.',
    ],
    'barreira_medo' => [
        'title' => 'Medo',
        'guidance' => 'É normal sentir medo. Você pode conversar com a equipe de saúde sobre suas preocupações e pedir explicações durante todo o atendimento.',
    ],
    'barreira_vergonha' => [
        'title' => 'Vergonha',
        'guidance' => 'É normal sentir vergonha. Você pode conversar com a equipe de saúde sobre suas preocupações e pedir explicações durante todo o atendimento.',
    ],
    'barreira_falta_informacao' => [
        'title' => 'Falta de informação',
        'guidance' => 'O exame preventivo busca identificar alterações no colo do útero antes que causem sintomas, e é uma das formas mais importantes de prevenção. Veja as páginas de informação do Lumera e converse com a equipe de saúde.',
    ],

    'sem_colo_utero' => [
        'title' => 'Sem útero ou colo do útero',
        'guidance' => 'Você informou que não tem útero e colo do útero. Nesse caso, o rastreamento do câncer do colo do útero pode não se aplicar. Confirme com um profissional de saúde se ainda há alguma recomendação de acompanhamento para você.',
    ],
    'vida_sexual_nao_iniciada' => [
        'title' => 'Vida sexual ainda não iniciada',
        'guidance' => 'O rastreamento do câncer do colo do útero é indicado para quem já iniciou a vida sexual. Quando isso acontecer, converse com um profissional de saúde sobre quando começar o exame preventivo e sobre a vacina contra o HPV.',
    ],
    'colo_utero_desconhecido' => [
        'title' => 'Útero e colo do útero não confirmados',
        'guidance' => 'Você não soube informar se tem útero e colo do útero. Converse com um profissional de saúde para confirmar, pois isso define se o rastreamento se aplica ao seu caso.',
    ],
    'exame_nunca_feito_idade_desconhecida' => [
        'title' => 'Exame preventivo ainda não realizado',
        'guidance' => 'Você informou que nunca fez o exame preventivo e não informou sua faixa etária. Procure a UBS para saber se você está na faixa etária indicada para o rastreamento.',
    ],
    'resultado_desconhecido' => [
        'title' => 'Resultado de exame não confirmado',
        'guidance' => 'Você não sabe ou não buscou o resultado de um exame. Procure a unidade de saúde onde fez o exame para pegar o resultado e conversar com um profissional.',
    ],
    'duvida_contraceptivo_substitui' => [
        'title' => 'Anticoncepcional e exame preventivo',
        'guidance' => 'Anticoncepcionais ajudam a prevenir a gravidez, mas não substituem o exame preventivo e não protegem contra infecções sexualmente transmissíveis. O exame continua importante com qualquer método contraceptivo.',
    ],
    'duvida_vacina_substitui' => [
        'title' => 'Vacina contra o HPV e exame preventivo',
        'guidance' => 'A vacina contra o HPV é uma medida importante de prevenção, mas não elimina a necessidade do rastreamento do câncer do colo do útero. Mesmo vacinada, mantenha o exame preventivo conforme a orientação de um profissional de saúde.',
    ],
    'diagnostico_hpv' => [
        'title' => 'Diagnóstico anterior de HPV',
        'guidance' => 'Ter tido diagnóstico de HPV não significa ter câncer. Siga as orientações da sua equipe de saúde sobre acompanhamento e exames.',
    ],
    'diagnostico_ist' => [
        'title' => 'Diagnóstico anterior de outra IST',
        'guidance' => 'Converse com um profissional de saúde sobre o acompanhamento adequado e sobre como se proteger de novas infecções.',
    ],
    'exames_ist_sugestao' => [
        'title' => 'Exames de infecções sexualmente transmissíveis',
        'guidance' => 'Converse com um profissional de saúde sobre quando fazer exames para detectar infecções sexualmente transmissíveis. Pergunte na UBS quais testes estão disponíveis.',
    ],
    'preservativo_hpv_orientacao' => [
        'title' => 'Preservativo e HPV',
        'guidance' => 'O preservativo reduz o risco de transmissão do HPV, mas não oferece proteção total contra o vírus. Ele também não substitui a vacina contra o HPV nem o exame preventivo.',
    ],
    'vacina_onde' => [
        'title' => 'Onde receber a vacina contra o HPV',
        'guidance' => 'Pergunte na UBS de referência onde e como receber a vacina. A oferta gratuita pelo SUS segue critérios de elegibilidade e o calendário de vacinação vigente.',
    ],
    'consultas_pouco_frequentes' => [
        'title' => 'Consultas ginecológicas',
        'guidance' => 'Manter consultas regulares com um profissional de saúde ajuda a acompanhar sua saúde e esclarecer dúvidas, mesmo sem sintomas. Pergunte na UBS de referência como agendar.',
    ],
    'pilula_prolongada' => [
        'title' => 'Uso prolongado de pílula anticoncepcional',
        'guidance' => 'O uso de qualquer método contraceptivo não substitui o exame preventivo. Converse com um profissional de saúde sobre o método mais adequado para você e mantenha o rastreamento em dia.',
    ],

    'versao_anterior' => [
        'title' => 'Questionário de versão anterior',
        'guidance' => 'Este questionário foi respondido antes de o questionário atual existir (várias perguntas não foram feitas). Refaça o questionário para uma análise completa.',
    ],
];
