# Notas para o relatório

## LGPD (Lei nº 13.709/2018)

- **Dados tratados:** nome, e-mail, data de nascimento, respostas do questionário de saúde, registros do ciclo,
  lembretes e mensagens do chat. Dados sobre saúde são dados pessoais sensíveis (art. 5º, II, e art. 11).
  TODO: revisar os números dos artigos com o material da disciplina.
- **Consentimento:** o cadastro exige marcar o checkbox de consentimento, e o texto informa a finalidade (uso do aplicativo).
- **Segurança:** senhas guardadas com hash (bcrypt), proteção CSRF, sessão no banco, acesso por perfil
  (`patient` ou `professional`) e cada paciente só acessa os próprios registros (consultas sempre filtradas pelo usuário logado).
- **Exclusão:** a paciente pode excluir a conta no perfil, e os dados associados (questionários, ciclos, lembretes,
  conversas e mensagens) são apagados em cascata.
- **Terceiros:** a localização do navegador não é enviada ao servidor nem gravada. Ela só monta o link para o Google Maps,
  que é um serviço de terceiros com política própria.
- **Minimização:** o app não pede CPF, endereço ou telefone.

## Fontes do conteúdo de saúde

- INCA, Câncer do colo do útero (versão para população): <https://www.gov.br/inca/pt-br/assuntos/cancer/tipos/colo-do-utero/versao-para-populacao>
- INCA, Perguntas frequentes sobre HPV: <https://www.gov.br/inca/pt-br/acesso-a-informacao/perguntas-frequentes/hpv>
- INCA, Fatores de risco: <https://www.gov.br/inca/pt-br/assuntos/gestor-e-profissional-de-saude/controle-do-cancer-do-colo-do-utero/fatores-de-risco>
- Ministério da Saúde, Campanha de vacinação contra o HPV: <https://www.gov.br/saude/pt-br/campanhas-da-saude/2025/hpv>

Todo o conteúdo é orientação educativa, nunca diagnóstico. O aviso aparece nas páginas de informação, na tela de resultado
do questionário, no formulário de ciclo e no rodapé.

## Pontos clínicos a conferir antes de apresentar (`TODO: revisar` no código)

| Onde | O que conferir |
|---|---|
| `config/informacoes.php` (O que é) | Percentual de casos ligados ao HPV persistente (o texto não cita número) |
| `config/informacoes.php` (Fatores de risco) | Pílula anticoncepcional prolongada e multiparidade |
| `config/informacoes.php` (HPV e vacinação) | Faixas etárias e doses da vacina pelo SUS (o calendário muda); tipos 16 e 18 e os 70% dos casos |
| `config/informacoes.php` (Sintomas) | Sintomas de casos avançados |
| `config/informacoes.php` (Exames) | Periodicidade (25 a 64 anos, 3 anos após dois exames normais) e o teste DNA-HPV citado pelo INCA a partir de 2024; oferta do exame na UBS |
| `app/Enums/RiskLevel.php` | Textos de orientação das três faixas |
| `app/Support/RiskClassifier.php` | "Não lembro" (preventivo) e "não sei" (vacina) tratados como não confirmados |
| `resources/js/Pages/Cycle/Form.vue` | Aviso exibido ao marcar sintomas |

## Limitações do MVP

- **Classificação por regras simples:** o questionário não considera a idade. Uma adolescente que nunca fez o preventivo
  recebe "alta prioridade", embora o exame seja indicado a partir dos 25 anos. As regras seguem o CLAUDE.md.
- **Consentimento não registrado:** o checkbox é validado, mas a data do aceite não é gravada (o modelo de dados não tem o campo).
- **Sem recuperação de senha nem verificação de e-mail:** fora do escopo, então não há e-mail no sistema.
- **Sem validação de CRM:** qualquer pessoa pode se cadastrar como profissional, e a paciente escolhe com quem conversar.
  Em produção isso exigiria validação e aprovação do cadastro.
- **Chat por polling (5 s):** sem tempo real, sem indicador de leitura e sem anexos. Não serve para emergências (o app avisa).
- **Serviços próximos:** apenas abre o Google Maps; não há lista própria de unidades de saúde.
- **Segurança básica:** sem registro de auditoria, sem criptografia dos dados em repouso além da do servidor e sem
  limite de tentativas fora do login.
- **Testes automatizados:** só o classificador do questionário e o middleware de perfil, como definido para o MVP.
- **Fuso horário:** o app usa `America/Sao_Paulo`.
- **Sem validação clínica:** o conteúdo não passou por revisão de um profissional de saúde.
