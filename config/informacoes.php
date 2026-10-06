<?php

/*
 * Conteúdo educativo das páginas de informação.
 * Fontes: INCA e Ministério da Saúde. Nada aqui é diagnóstico.
 */

$inca = [
    'label' => 'INCA: Câncer do colo do útero (versão para população)',
    'url' => 'https://www.gov.br/inca/pt-br/assuntos/cancer/tipos/colo-do-utero/versao-para-populacao',
];
$incaHpv = [
    'label' => 'INCA: Perguntas frequentes sobre HPV',
    'url' => 'https://www.gov.br/inca/pt-br/acesso-a-informacao/perguntas-frequentes/hpv',
];
$incaRisco = [
    'label' => 'INCA: Fatores de risco',
    'url' => 'https://www.gov.br/inca/pt-br/assuntos/gestor-e-profissional-de-saude/controle-do-cancer-do-colo-do-utero/fatores-de-risco',
];
$msHpv = [
    'label' => 'Ministério da Saúde: Campanha de vacinação contra o HPV',
    'url' => 'https://www.gov.br/saude/pt-br/campanhas-da-saude/2025/hpv',
];

return [
    'o-que-e' => [
        'title' => 'O que é?',
        'icon' => 'info',
        'intro' => 'Entenda o que é o câncer do colo do útero e por que o cuidado preventivo faz diferença.',
        'sections' => [
            [
                'heading' => 'O colo do útero',
                'text' => 'O colo do útero é a parte de baixo do útero, que fica no fundo da vagina. O câncer do colo do útero é um tumor que se desenvolve a partir de alterações nessa região.',
            ],
            [
                'heading' => 'Como a doença evolui',
                'text' => 'É uma doença de desenvolvimento lento. No começo, muitas vezes não causa sintomas, por isso o exame preventivo é tão importante: ele pode encontrar alterações antes de virarem câncer.',
            ],
            [
                'heading' => 'Qual é a principal causa',
                // TODO: revisar (INCA cita que a infecção persistente pelo HPV está presente em quase todos os casos; conferir o percentual antes de citar número)
                'text' => 'A infecção persistente por tipos de risco do HPV (papilomavírus humano) está relacionada à grande maioria dos casos. A maior parte das infecções pelo HPV some sozinha; o problema é quando ela persiste.',
            ],
        ],
        'sources' => [$inca, $incaHpv],
    ],

    'fatores-de-risco' => [
        'title' => 'Fatores de risco',
        'icon' => 'alert',
        'intro' => 'Fatores de risco aumentam a chance de a doença aparecer, mas não significam que ela vai acontecer.',
        'sections' => [
            [
                'heading' => 'Principais fatores',
                'items' => [
                    'Infecção persistente pelo HPV, principalmente os tipos 16 e 18.',
                    'Tabagismo (fumar).',
                    'Início precoce da vida sexual.',
                    'Ter vários parceiros sexuais.',
                    // TODO: revisar (INCA lista uso prolongado de pílula anticoncepcional como fator associado; conferir a redação)
                    'Uso prolongado de pílulas anticoncepcionais.',
                    // TODO: revisar (multiparidade aparece na página para profissionais do INCA)
                    'Ter tido muitos partos (multiparidade).',
                ],
            ],
            [
                'heading' => 'O que fazer',
                'text' => 'Ter um ou mais fatores de risco não é diagnóstico. Vacinar-se contra o HPV, fazer o exame preventivo na periodicidade indicada e não fumar ajudam a reduzir o risco.',
            ],
        ],
        'sources' => [$inca, $incaRisco],
    ],

    'prevencao' => [
        'title' => 'Prevenção',
        'icon' => 'shield',
        'intro' => 'O câncer do colo do útero é uma das doenças mais preveníveis. Veja o que ajuda.',
        'sections' => [
            [
                'heading' => 'Vacina contra o HPV',
                'text' => 'A vacina protege contra os tipos de HPV mais ligados ao câncer do colo do útero e é oferecida pelo SUS. O ideal é vacinar antes do início da vida sexual.',
            ],
            [
                'heading' => 'Exame preventivo (Papanicolau)',
                'text' => 'O exame encontra alterações no colo do útero antes de virarem câncer. Mesmo quem se vacinou precisa continuar fazendo o preventivo.',
            ],
            [
                'heading' => 'Camisinha',
                'text' => 'O uso da camisinha é recomendado e reduz o risco de contágio, mas não protege totalmente contra o HPV, porque o vírus pode estar em áreas que ela não cobre.',
            ],
            [
                'heading' => 'Não fumar',
                'text' => 'O tabagismo é um fator de risco para o câncer do colo do útero.',
            ],
        ],
        'sources' => [$inca, $incaHpv, $msHpv],
    ],

    'hpv-e-vacinacao' => [
        'title' => 'HPV e vacinação',
        'icon' => 'syringe',
        'intro' => 'Saiba o que é o HPV, como ele é transmitido e quem pode se vacinar pelo SUS.',
        'sections' => [
            [
                'heading' => 'O que é o HPV',
                'text' => 'HPV é a sigla de papilomavírus humano, um grupo de vírus que infecta a pele e as mucosas. Existem mais de 200 tipos, e cerca de 40 infectam a região genital e anal. A infecção é muito frequente e, na maioria das vezes, some sozinha.',
            ],
            [
                'heading' => 'Como é transmitido',
                'text' => 'Principalmente por contato sexual direto. Também pode passar da mãe para o bebê no parto. Não há comprovação de contágio por vaso sanitário, piscina ou toalha.',
            ],
            [
                'heading' => 'Quem pode se vacinar pelo SUS',
                // TODO: revisar (calendário muda com frequência; conferir faixas e doses no Ministério da Saúde antes da apresentação)
                'items' => [
                    'Meninas e meninos de 9 a 14 anos: dose única.',
                    'Pessoas imunocomprometidas de 9 a 45 anos: 3 doses.',
                    'Vítimas de violência sexual de 15 a 45 anos: de 2 a 3 doses.',
                ],
            ],
            [
                'heading' => 'Vacinada, mas ainda precisa do preventivo',
                // TODO: revisar (INCA cita que as vacinas protegem contra os tipos 16 e 18, responsáveis por cerca de 70% dos casos)
                'text' => 'As vacinas protegem contra os tipos de HPV responsáveis por cerca de 70% dos casos. Por isso, continue fazendo o exame preventivo mesmo depois de vacinada.',
            ],
        ],
        'sources' => [$incaHpv, $msHpv],
    ],

    'sintomas' => [
        'title' => 'Sintomas',
        'icon' => 'drop',
        'intro' => 'No início, a doença pode não causar nenhum sintoma. Fique atenta a estes sinais.',
        'sections' => [
            [
                'heading' => 'Sinais que merecem atenção',
                'items' => [
                    'Sangramento durante ou depois da relação sexual.',
                    'Sangramento entre as menstruações.',
                    'Sangramento depois da menopausa.',
                    'Dor durante a relação sexual.',
                    'Corrimento com sangue ou com mau cheiro.',
                ],
            ],
            [
                'heading' => 'Em casos mais avançados',
                // TODO: revisar (INCA cita dor pélvica, cansaço, alterações urinárias ou intestinais; conferir lista)
                'text' => 'Podem aparecer dor na região da pelve, cansaço e queixas urinárias ou intestinais.',
            ],
            [
                'heading' => 'O que fazer',
                'text' => 'Esses sinais podem ter várias causas e não são diagnóstico. Se perceber algum deles, procure uma unidade de saúde para ser avaliada. Não ter sintomas também não significa ausência de doença: mantenha o preventivo em dia.',
            ],
        ],
        'sources' => [$inca],
    ],

    'exames' => [
        'title' => 'Exames',
        'icon' => 'exams',
        'intro' => 'Conheça o exame preventivo e quando fazê-lo.',
        'sections' => [
            [
                'heading' => 'O que é o Papanicolau',
                'text' => 'É o exame preventivo do colo do útero. O profissional de saúde introduz um espéculo na vagina, olha o colo do útero e colhe uma pequena amostra de células com uma espátula e uma escovinha. A amostra é analisada em laboratório.',
            ],
            [
                'heading' => 'Quem deve fazer e com que frequência',
                // TODO: revisar (INCA indica 25 a 64 anos; intervalo de 3 anos após dois exames anuais normais; o INCA também cita o teste de DNA-HPV a cada 5 anos a partir de 2024, conferir o que vale no SUS hoje)
                'items' => [
                    'Mulheres de 25 a 64 anos que já tiveram vida sexual.',
                    'Primeiro, dois exames com intervalo de um ano. Se os dois forem normais, o exame passa a ser a cada 3 anos.',
                ],
            ],
            [
                'heading' => 'Onde fazer',
                // TODO: revisar (conferir se o exame está disponível na UBS do seu município)
                'text' => 'O exame é oferecido pelo SUS nas unidades básicas de saúde (UBS). Use a tela de serviços de saúde para encontrar uma perto de você.',
            ],
            [
                'heading' => 'Resultado alterado',
                'text' => 'Um resultado alterado não significa câncer. Significa que é preciso fazer outros exames e acompanhamento com a equipe de saúde.',
            ],
        ],
        'sources' => [$inca],
    ],
];
