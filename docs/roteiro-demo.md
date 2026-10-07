# Roteiro da demonstração

Duração estimada: 8 a 10 minutos. Use dois navegadores (ou uma janela anônima) para mostrar paciente e profissional ao mesmo tempo.

## Antes de começar

1. `docker compose up -d` e confirme que <http://localhost:8088> abre.
2. Recrie os dados de exemplo: `docker compose exec app php artisan migrate:fresh --seed`.
3. Permita a localização do navegador para `localhost` (ou deixe bloqueada para mostrar o fallback).
4. Contas (senha `password`): `paciente@cuidar.test` e `profissional@cuidar.test`.

## Passo a passo

1. **Abertura** (`/`): marca "Cuidar" e os botões Entrar e Criar conta.
2. **Cadastro** (`/register`): mostre a escolha de perfil (paciente ou profissional), a data de nascimento e o
   checkbox de consentimento (LGPD). Tente enviar sem marcar o consentimento para mostrar a validação. Não conclua o cadastro.
3. **Informações** (`/informacoes`, sem login): abra "HPV e vacinação" e mostre o aviso educativo e as fontes (INCA e Ministério da Saúde).
4. **Login como paciente** (`paciente@cuidar.test`): o login leva para o painel da paciente.
5. **Dashboard**: aponte a faixa de lembrete vencido, o próximo lembrete (selo "Próximo"), o último ciclo e a situação do preventivo.
6. **Questionário** (`Questionário de saúde`): responda os 5 passos. Para mostrar as faixas de risco:
   - preventivo "Não" resulta em Alta prioridade;
   - preventivo recente com camisinha "Às vezes" resulta em Atenção;
   - preventivo recente com camisinha "Sempre" e vacinada resulta em Baixo risco.
   Mostre o aviso de que o resultado não é diagnóstico e o histórico.
7. **Ciclo** (`/ciclo`): registre um novo ciclo com sintomas e mostre o aviso que aparece ao marcar um sintoma.
8. **Lembretes** (`/lembretes`): conclua um lembrete, use os filtros e crie um novo.
9. **Serviços de saúde** (`/servicos`): clique em "Usar minha localização" e mostre o Google Maps abrindo com "UBS perto de mim".
   Em seguida mostre o campo de cidade.
10. **Chat**: com a paciente, abra "Fale com um médico" e envie uma mensagem.
11. **Profissional** (outro navegador, `profissional@cuidar.test`): mostre o painel com mensagens pendentes e pacientes
    recentes, abra a conversa e responda. A resposta aparece na janela da paciente em até 5 segundos (polling). Encerre a conversa.
12. **Controle de acesso**: logada como paciente, abra `/profissional` (erro 403).
13. **Perfil**: mostre a edição de dados pessoais e a opção de excluir a conta (não confirme).

## Se algo der errado

- Lembretes sem destaque: rode o seeder de novo (as datas são relativas a hoje).
- Localização não funciona: confirme que a URL é `localhost` ou HTTPS e que a permissão não está bloqueada. Use o campo de cidade.
- Mensagem não chega: a atualização é por polling de 5 segundos; espere um pouco.
