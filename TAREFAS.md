# TAREFAS.md

## Como usar este arquivo (instruções para o Claude Code)

- Trabalhe uma etapa por vez, na ordem abaixo. Não pule etapas.
- Ao concluir cada item, marque `[x]` neste arquivo na mesma hora.
- Ao terminar uma etapa: rode o app, liste o que foi feito e o que testar manualmente, faça o commit e espere meu OK antes de começar a próxima.
- Se um item estiver bloqueado ou ambíguo, deixe `[ ]`, escreva uma nota `> BLOQUEIO: motivo` logo abaixo e me pergunte.
- Siga o `CLAUDE.md` (stack fechada e fora de escopo). As telas de referência estão em `docs/telas/`.
- Marque com `TODO: revisar` qualquer afirmação clínica que eu precise conferir.

Legenda: `[ ]` pendente, `[x]` concluído.

---

## Etapa 0. Setup
- [x] Criar projeto Laravel com Breeze (stack Vue + Inertia)
- [x] Criar `docker-compose.yml` com app (php-fpm), nginx e postgres
- [x] Configurar `.env` e `.env.example` para PostgreSQL e `MAIL_MAILER=log`
- [x] Subir os containers e confirmar que a tela inicial abre
- [x] Inicializar o repositório Git e fazer o primeiro commit

## Etapa 1. Telas de referência e layout base
- [x] Listar as imagens em `docs/telas/` e criar uma tabela tela x rota x componente em `docs/mapa-telas.md`
- [x] Definir paleta de cores e fontes no `tailwind.config` a partir das imagens
- [x] Criar layout base (header, menu, rodapé) igual às imagens
- [x] Criar componentes reutilizáveis (botão, card, input, alerta) no padrão visual das telas

## Etapa 2. Auth e roles
- [x] Migration: adicionar `role` (`patient` ou `professional`) e `data_nascimento` em `users`
- [x] Tela de cadastro com escolha de perfil e checkbox de consentimento (LGPD)
- [x] Remover rotas e telas de recuperação de senha e verificação de e-mail
- [x] Criar middleware `role` e registrar nas rotas
- [x] Redirecionar para o dashboard correto conforme o perfil após o login
- [x] Teste automatizado do middleware `role`

## Etapa 3. Páginas de informação (públicas)
- [x] O que é o câncer de colo de útero
- [x] Fatores de risco
- [x] Prevenção
- [x] HPV e vacinação
- [x] Sintomas
- [x] Exames
- [x] Rodapé de cada página com fontes (INCA e Ministério da Saúde)
- [x] Menu de navegação entre as páginas

## Etapa 4. Questionário de saúde
- [ ] Migration e model `health_questionnaires`
- [ ] Form Request com validação (idade, preventivo, data do último, camisinha, contraceptivo, vacina HPV)
- [ ] Formulário do questionário igual à tela de referência
- [ ] Classe de regras que devolve `baixo_risco`, `atencao` ou `alta_prioridade`
- [ ] Testes automatizados da classe de regras (todas as faixas e casos de borda)
- [ ] Tela de resultado com aviso de que é orientação educativa, não diagnóstico
- [ ] Histórico dos questionários anteriores do paciente

## Etapa 5. Ciclo menstrual
- [ ] Migration e model `cycle_entries`
- [ ] CRUD de registros (início, fim, fluxo, sintomas, notas) com Form Request
- [ ] Visualização em calendário ou lista, conforme a tela de referência
- [ ] Garantir que cada paciente só acesse os próprios registros

## Etapa 6. Lembretes e agendamentos
- [ ] Migration e model `reminders`
- [ ] CRUD (título, data/hora, tipo: preventivo, consulta ou outro)
- [ ] Marcar como concluído
- [ ] Destaque visual para lembretes vencidos e próximos
- [ ] Garantir que cada paciente só acesse os próprios lembretes

## Etapa 7. Dashboard e perfil do paciente
- [ ] Card do próximo lembrete
- [ ] Card do último ciclo registrado
- [ ] Card da situação do preventivo (a partir do último questionário)
- [ ] Tela de perfil: editar dados pessoais
- [ ] Histórico consolidado (questionários e ciclos)

## Etapa 8. Fale com um médico
- [ ] Migrations e models `conversations` (patient_id, professional_id, assunto, status) e `messages` (conversation_id, sender_id, corpo)
- [ ] Paciente: criar conversa, enviar mensagem e ver respostas
- [ ] Profissional: caixa de entrada com conversas abertas, responder e fechar
- [ ] Profissional: lista de pacientes que enviaram mensagem
- [ ] Polling a cada 5 a 10 segundos no Vue para atualizar a conversa
- [ ] Garantir que só os participantes acessem a conversa

## Etapa 9. Serviços de saúde próximos
- [ ] Botão que usa a Geolocation API do navegador
- [ ] Abrir o Google Maps com a busca "UBS perto de mim" usando as coordenadas
- [ ] Fallback: campo para digitar a cidade quando a permissão for negada
- [ ] Tratar erros (permissão negada, sem HTTPS, timeout) com mensagem clara

## Etapa 10. Painel do profissional
- [x] Dashboard do profissional (mensagens pendentes e pacientes recentes)
- [x] Perfil do profissional

## Etapa 11. Acabamento e entrega
- [x] Seeders com 1 paciente e 1 profissional demo, com dados de exemplo (ciclos, lembretes, questionário, conversa)
- [x] Revisar cada tela contra as imagens em `docs/telas/` e corrigir divergências
- [x] Revisar responsividade básica (desktop e celular)
- [x] Confirmar que senhas usam hash e que rotas exigem autenticação e role corretos
- [x] Deploy na Contabo com Nginx e HTTPS (necessário para geolocalização)
- [x] Escrever `docs/roteiro-demo.md` com o passo a passo da apresentação
- [x] Escrever `docs/relatorio-notas.md` com LGPD, fontes e limitações do MVP
- [x] README com instruções para subir o projeto

---

## Opcional (só se sobrar tempo, não iniciar sem eu pedir)
- [x] Trocar polling por Reverb (WebSocket)
- [x] Validação de CRM no cadastro de profissional
- [x] Lembretes por e-mail
