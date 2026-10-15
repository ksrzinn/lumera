# Lumera

MVP web de prevenção do câncer do colo do útero, feito como trabalho de faculdade. Todo o conteúdo é orientação
educativa, nunca diagnóstico. Fontes: INCA e Ministério da Saúde.

**Stack:** Laravel 13, Breeze (Vue 3 + Inertia), Tailwind, PostgreSQL, Docker Compose (php-fpm, nginx, postgres).

## Como subir

Pré-requisitos: Docker com Compose, Node 20+ e PHP/Composer no host (ou rode o Composer pelo container).

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
npm install
npm run build
```

O app abre em <http://localhost:8088>.

| Variável (`.env`) | Padrão | Para quê |
|---|---|---|
| `NGINX_PORT` | `8088` | Porta do app no host |
| `DB_FORWARD_PORT` | `5435` | Porta do PostgreSQL no host |

Para desenvolver o front com recarga automática, use `npm run dev` no lugar de `npm run build`.

## Contas de demonstração

Criadas pelo seeder (`php artisan migrate:fresh --seed` recria tudo do zero). Senha das duas: `minhasenha`.

| Perfil | E-mail |
|---|---|
| Paciente (Ana Silva) | `paciente@cuidar.test` |
| Profissional (Dra. Marina Souza) | `profissional@cuidar.test` |

Os dados de exemplo (lembretes, ciclos, questionários, conversa) usam datas relativas a hoje, então o
destaque de "vencido" e "próximo" aparece sempre que o seeder é executado.

## Testes

```bash
docker compose exec app php artisan test
```

Os testes próprios do projeto cobrem o analisador da anamnese (`tests/Unit/AnamnesisAnalyzerTest.php`)
e o middleware de perfil (`tests/Feature/RoleMiddlewareTest.php`). Os demais vieram do Breeze e foram adaptados.

## Organização

- `app/Support/Anamnesis/AnamnesisAnalyzer.php`: regras da anamnese (sem IA e sem pontuação), devolve `em_dia`, `atencao_preventiva` ou `avaliacao_profissional` e a lista de achados. Textos em `lang/pt_BR/anamnese.php`.
- `app/Http/Middleware/EnsureUserHasRole.php`: middleware `role:patient` e `role:professional`.
- `config/informacoes.php`: texto das páginas de informação, com fontes.
- `resources/js/Pages`: telas Inertia. `docs/mapa-telas.md` relaciona cada tela de `docs/telas/` à rota e ao componente.
- `docs/roteiro-demo.md`: passo a passo da apresentação. `docs/relatorio-notas.md`: LGPD, fontes e limitações.

## Observações

- O chat usa polling a cada 5 segundos (sem WebSocket).
- A geolocalização do navegador só funciona em `localhost` ou em HTTPS.
- Fora do escopo do MVP: e-mail, recuperação de senha, filas, notificações push e validação de CRM.
