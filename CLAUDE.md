# CLAUDE.md

## Projeto
MVP web de prevenção do câncer de colo de útero, para um trabalho de faculdade. Será demonstrado em sala. Prioridade: simples, funcionando e fiel às telas de referência em `docs/telas/`.

## Stack (não trocar)
- Laravel + Breeze (Vue 3 + Inertia), Tailwind
- PostgreSQL
- Docker Compose (app php-fpm, nginx, postgres)
- Monolito. Sem API separada, sem Nuxt, sem Livewire

## Fora do escopo (não implementar)
- E-mail, recuperação de senha, verificação de e-mail
- Scheduler, filas, push notifications
- Pacotes de permissões (Spatie etc.)
- Validação de CRM, aprovação de cadastro
- WebSocket/Reverb (só se eu pedir depois)

## Regras de negócio
- Cadastro aberto, com escolha de perfil `patient` ou `professional` e checkbox de consentimento (LGPD)
- Roles via coluna `role` em `users` + middleware `role`. Redirecionar por perfil após o login
- Lembretes: apenas lista dentro do app, com destaque no dashboard quando vencidos ou próximos
- Fale com um médico: tabela `messages` com `conversation_id` e `sender_id`, atualização por polling (5 a 10s) no Vue
- Serviços próximos: Geolocation API do navegador, abre Google Maps com busca "UBS perto de mim" usando as coordenadas. Fallback: campo para digitar a cidade
- Questionário: classe de regras (sem IA) que devolve `baixo_risco`, `atencao` ou `alta_prioridade`
  - Preventivo nunca feito ou há mais de 3 anos: alta prioridade
  - Uso inconsistente de camisinha ou sem vacina HPV: atenção
  - Caso contrário: baixo risco

## Conteúdo de saúde
- Tudo é orientação educativa, nunca diagnóstico. Mostrar esse aviso na tela de resultado
- Fontes: INCA e Ministério da Saúde. Não inventar números nem recomendações
- Marcar com `TODO: revisar` qualquer afirmação clínica que eu precise conferir

## Convenções
- Textos da interface em português do Brasil
- Código (variáveis, tabelas, rotas) em inglês, exceto onde já definido em português no modelo de dados
- Controllers enxutos, validação em Form Requests
- Um commit por etapa, mensagem clara

## Fluxo de trabalho
- Trabalhar uma etapa por vez, na ordem: setup, telas base, auth e roles, páginas de informação, questionário, ciclo, lembretes, dashboard e perfil, mensagens, serviços próximos, acabamento
- Ao terminar cada etapa: rodar o app, listar o que foi feito e o que testar, e esperar meu OK antes da próxima
- Testes automatizados só para: classe de regras do questionário e middleware de role
- Seeders com 1 paciente e 1 profissional demo, com dados de exemplo
- Siga o TAREFAS.md: leia no início de cada sessão, trabalhe na primeira etapa com itens pendentes e marque [x] ao concluir cada item.

## Modelo de dados
- users: id, nome, email, senha, role, data_nascimento
- cycle_entries: id, user_id, data_inicio, data_fim, fluxo, sintomas, notas
- health_questionnaires: id, user_id, idade, fez_preventivo, data_ultimo_preventivo, usa_camisinha, metodo_contraceptivo, vacinada_hpv, created_at
- reminders: id, user_id, titulo, data_hora, tipo, concluido
- messages: id, conversation_id, sender_id, corpo, created_at (e uma tabela `conversations` com patient_id, professional_id, assunto, status)
