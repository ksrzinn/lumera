# Análise da Anamnese Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Substituir a classificação de risco do questionário por uma análise de anamnese baseada em combinação de respostas, com 3 níveis, achados individuais e orientações, sem diagnóstico.

**Architecture:** `AnamnesisAnalyzer::analyze()` (classe pura, sem IA, sem pontuação) devolve `AnamnesisResult` (nível, urgência, lista de `Finding`). O nível é o achado de maior peso. Textos ficam em `lang/pt_BR/anamnese.php` (um arquivo para a profissional revisar). O nível continua calculado na leitura, nunca armazenado.

**Tech Stack:** Laravel 12, Inertia/Vue 3, PostgreSQL (jsonb), PHPUnit.

**Spec:** texto da profissional colado na sessão de 2026-10-15 (seções 1 a 9). Decisões do usuário: remover regra fixa de 3 anos, renomear o enum, implementar o conjunto completo de campos.

## Global Constraints

- Orientação educativa, nunca diagnóstico. Nunca afirmar "livre de doença" nem que sintoma indica câncer.
- "não sei", "não lembro", "prefiro não responder" = desconhecido, nunca negativo.
- Sem pontuação numérica de risco. Regras por combinação de condições.
- Não inventar números clínicos. Toda afirmação clínica leva `TODO: revisar`. Fontes: INCA e Ministério da Saúde.
- Interface em PT-BR, código em inglês, Form Requests para validação, controllers enxutos.
- Testes automatizados só para a classe de regras e o middleware `role` (CLAUDE.md). Form Request e telas: teste manual.
- Sem e-mail, filas, scheduler, pacotes de permissão.
- Um commit por tarefa.

## Review Focus

- Linha antiga (colunas novas nulas) não pode gerar achados de sintoma/histórico; deve mostrar aviso de versão anterior.
- "prefiro não responder" no preventivo nunca pode virar `avaliacao_profissional`.
- Sintoma isolado não pode ser mascarado por exame recente.
- Mulher vacinada e com camisinha ainda recebe achado de rastreamento se exame ausente.
- Seleção de `nenhum`/`prefiro_nao_responder` junto de outros sintomas deve ser rejeitada.

## Pontos a confirmar com a profissional (viram constantes com `TODO: revisar`)

1. `RECENT_EXAM_YEARS` (3 pela citologia do INCA 2016, ou 5 pelo teste DNA-HPV). Provisório: 3.
2. Faixa etária de rastreamento 25–64. Provisório.
3. Texto de urgência (SAMU 192 / pronto atendimento).
4. Opção "não se aplica" para camisinha (quem não tem vida sexual ativa). Fora da spec, proposta nossa.
5. Vacina: sem opção "esquema incompleto" (MS adotou dose única em 2024, conferir).

## File Structure

| Arquivo | Responsabilidade |
|---|---|
| `database/migrations/*_add_anamnesis_fields_to_health_questionnaires.php` | colunas novas, todas nullable |
| `app/Models/HealthQuestionnaire.php` | fillable, casts, constantes de opções, `analysis()` |
| `app/Http/Requests/StoreHealthQuestionnaireRequest.php` | validação condicional e exclusividade |
| `app/Enums/CareLevel.php` (substitui `RiskLevel`) | `EmDia`, `AtencaoPreventiva`, `AvaliacaoProfissional`, `weight()`, `label()`, `followUp()` |
| `app/Support/Anamnesis/Finding.php` | value object: code, level, title, guidance |
| `app/Support/Anamnesis/AnamnesisResult.php` | level, urgent, findings |
| `app/Support/Anamnesis/AnamnesisAnalyzer.php` (substitui `RiskClassifier`) | regras |
| `lang/pt_BR/anamnese.php` | títulos e orientações por código |
| `app/Support/PreventiveStatus.php`, `HistoryController`, `QuestionnaireController` | usar `analyze()` |
| `resources/js/Pages/Questionnaire/{Form,Show,Index}.vue`, `History/Index.vue`, `Exams/Index.vue`, `Patient/Dashboard.vue` | UI |
| `resources/js/lib/careLevel.js` | mapa de cores do badge (hoje copiado em 5 arquivos) |
| `tests/Unit/AnamnesisAnalyzerTest.php` (substitui `RiskClassifierTest`) | matriz de casos |
| `database/seeders/DemoSeeder.php`, `README.md`, `TAREFAS.md`, `docs/*`, `CLAUDE.md` | dados e docs |

---

### Task 1: Migration e model

**Files:** Create migration; Modify `app/Models/HealthQuestionnaire.php`.

**Interfaces:**
- Produces colunas: `historico_alteracao` string null, `acompanhamento_alteracao` string null, `sintomas` jsonb null, `barreiras` jsonb null. Valores novos em `fez_preventivo` (`prefiro_nao_responder`), `usa_camisinha` (`maioria_das_vezes`, `raramente`, `prefiro_nao_responder`), `vacinada_hpv` (`prefiro_nao_responder`) são só strings, sem mudança de schema se as colunas já são `string`.
- Produces constantes: `ALTERATION_HISTORY_OPTIONS`, `FOLLOW_UP_OPTIONS`, `SYMPTOM_OPTIONS`, `BARRIER_OPTIONS`, e `CONDOM_OPTIONS` ampliada.

- [ ] **Step 1:** Conferir no migration atual que `fez_preventivo`, `usa_camisinha`, `vacinada_hpv` são `string` (se forem enum de banco, trocar por string na nova migration).
- [ ] **Step 2:** Criar migration aditiva com as 4 colunas nullable (`jsonb` via `$table->jsonb()`).
- [ ] **Step 3:** Atualizar `Fillable`, `casts()` (`sintomas`, `barreiras` => `array`) e constantes. Sintomas: `sangramento_pos_relacao`, `sangramento_fora_menstruacao`, `sangramento_pos_menopausa`, `dor_pelvica_persistente`, `corrimento_persistente`, `sangramento_intenso_com_mal_estar`, `nenhum`, `prefiro_nao_responder`. Barreiras: `sem_tempo`, `dificuldade_agendamento`, `medo`, `vergonha`, `falta_informacao`, `nenhuma`.
- [ ] **Step 4:** `php artisan migrate` e `migrate:rollback` para validar ida e volta.
- [ ] **Step 5:** Commit `Anamnese: colunas e opções novas em health_questionnaires`.

### Task 2: Analisador (TDD)

**Files:** Create `CareLevel`, `Finding`, `AnamnesisResult`, `AnamnesisAnalyzer`, `lang/pt_BR/anamnese.php`, `tests/Unit/AnamnesisAnalyzerTest.php`. Delete `RiskLevel`, `RiskClassifier`, `RiskClassifierTest`.

**Interfaces:**
- Produces: `AnamnesisAnalyzer::analyze(HealthQuestionnaire $q, ?CarbonInterface $today = null): AnamnesisResult`.
- `AnamnesisResult { CareLevel $level; bool $urgent; array<Finding> $findings; function codes(): array }`.
- `Finding { string $code; CareLevel $level; }` com `title()` e `guidance()` lidos de `__("anamnese.{$code}.title|guidance")`.
- Constantes no analisador: `RECENT_EXAM_YEARS = 3` e `SCREENING_AGE = [25, 64]`, ambas `TODO: revisar`.

- [ ] **Step 1: Escrever os testes que falham** (helper `analyze(array $overrides)` com base "tudo em dia": idade 30, `fez_preventivo=sim`, data há 1 ano, `historico_alteracao=nao`, `usa_camisinha=sempre`, `vacinada_hpv=sim`, `sintomas=['nenhum']`, `barreiras=['nenhuma']`). Casos, cada um assertando nível e códigos:

```php
public function test_baseline_is_em_dia(): void
{
    $r = $this->analyze([]);
    $this->assertSame(CareLevel::EmDia, $r->level);
    $this->assertFalse($r->urgent);
}

public function test_never_screened_in_age_range_needs_professional(): void
{
    $r = $this->analyze(['fez_preventivo' => 'nao', 'data_ultimo_preventivo' => null]);
    $this->assertSame(CareLevel::AvaliacaoProfissional, $r->level);
}

public function test_unknown_exam_answers_are_attention_never_professional(): void
{
    foreach (['nao_lembro', 'prefiro_nao_responder'] as $v) {
        $r = $this->analyze(['fez_preventivo' => $v, 'data_ultimo_preventivo' => null]);
        $this->assertSame(CareLevel::AtencaoPreventiva, $r->level);
    }
}

public function test_symptom_is_not_masked_by_recent_exam(): void
{
    $r = $this->analyze(['sintomas' => ['sangramento_pos_relacao']]);
    $this->assertSame(CareLevel::AvaliacaoProfissional, $r->level);
    $this->assertFalse($r->urgent);
}

public function test_heavy_bleeding_with_malaise_is_urgent(): void
{
    $r = $this->analyze(['sintomas' => ['sangramento_intenso_com_mal_estar']]);
    $this->assertTrue($r->urgent);
}

public function test_legacy_row_has_no_symptom_or_history_findings(): void
{
    $r = $this->analyze(['historico_alteracao' => null, 'sintomas' => null, 'barreiras' => null]);
    $this->assertContains('versao_anterior', $r->codes());
    $this->assertSame(CareLevel::EmDia, $r->level);
}
```

Completar com a matriz: idade 20 e 70 sem exame; `sim` sem data; data exatamente no limite vs 1 dia depois; histórico `sim` + acompanhamento `nao`/`nao_sei` (profissional) vs `em_andamento` (não eleva); cada condom `maioria_das_vezes`..`nunca` (atenção) vs `sempre`; vacina `nao`/`nao_sei`/`prefiro_nao_responder`; barreiras isoladas não elevam; prioridade (sintoma + camisinha + vacina => profissional com os 3 códigos); método contraceptivo isolado não muda nível.

- [ ] **Step 2:** `php artisan test --filter=AnamnesisAnalyzerTest` deve falhar (classe inexistente).
- [ ] **Step 3:** Implementar `CareLevel`, `Finding`, `AnamnesisResult`, `AnamnesisAnalyzer` e `lang/pt_BR/anamnese.php`. Regras conforme o design: exame (idade fora da faixa => informativo/atenção, nunca "atrasado" por tempo isolado), histórico, sintomas, camisinha (+ aviso de que outro método não protege contra IST e de que preservativo não substitui vacina nem rastreamento, sempre informativo), vacina, barreiras (só informativas). Nível = achado de maior peso, `EmDia` se nenhum. Texto de `EmDia`: "Pelas suas respostas, não identificamos pendência conhecida de rastreamento." Toda chave de clínica com `TODO: revisar`.
- [ ] **Step 4:** Testes passam.
- [ ] **Step 5:** Commit `Anamnese: analisador de regras por combinação de respostas`.

### Task 3: Form Request e controller

**Files:** Modify `StoreHealthQuestionnaireRequest.php`, `QuestionnaireController.php`.

- [ ] **Step 1:** `data_ultimo_preventivo`: `nullable|date|before_or_equal:today` (remover `required_if`); manter anulação quando `fez_preventivo != sim`.
- [ ] **Step 2:** `acompanhamento_alteracao`: `required_if:historico_alteracao,sim`, anulado nos demais casos.
- [ ] **Step 3:** `sintomas` e `barreiras`: `required|array|min:1`, itens `distinct` e `Rule::in`. Em `after()`, rejeitar `nenhum`/`nenhuma`/`prefiro_nao_responder` combinados com outros itens.
- [ ] **Step 4:** Controller: `create()` envia as novas opções; `store()` inalterado; `index()`/`show()` usam `analyze()` e enviam `level`, `label`, `urgent`, `findings` (code, level, title, guidance) e `follow_up`.
- [ ] **Step 5:** Teste manual das validações (tabela no relatório da etapa). Commit.

### Task 4: Telas

**Files:** `Form.vue`, `Show.vue`, `Index.vue`, `History/Index.vue`, `Exams/Index.vue`, `Patient/Dashboard.vue`, novo `resources/js/lib/careLevel.js`.

- [ ] **Step 1:** `careLevel.js` com cores/rótulos por nível; trocar nos 5 arquivos.
- [ ] **Step 2:** `Form.vue`: perguntas novas, follow-up condicional (acompanhamento, data), sintomas e barreiras com exclusividade no cliente, opção "prefiro não responder".
- [ ] **Step 3:** `Show.vue` em 4 blocos: faixa de urgência (se `urgent`), classificação geral, pontos de atenção, orientações e acompanhamento. Manter o aviso "orientação educativa, não diagnóstico".
- [ ] **Step 4:** `PreventiveStatus`, `HistoryController` e dashboard com os novos rótulos.
- [ ] **Step 5:** `npm run build`, rodar no navegador, percorrer os 3 níveis e o caso urgente. Commit.

### Task 5: Seeders, docs e CLAUDE.md

- [ ] **Step 1:** `DemoSeeder`: questionário antigo => `avaliacao_profissional`, recente => `atencao_preventiva`, preenchendo campos novos.
- [ ] **Step 2:** Atualizar `README.md`, `TAREFAS.md` (nova etapa de anamnese), `docs/relatorio-notas.md` (limitações, `TODO: revisar`), `docs/roteiro-demo.md`, `docs/mapa-telas.md` se afetado.
- [ ] **Step 3:** `CLAUDE.md`: regra do questionário e modelo de `health_questionnaires` (**pede OK explícito do usuário antes de editar**).
- [ ] **Step 4:** `php artisan migrate:fresh --seed`, suíte completa (`php artisan test`), corrigir qualquer falha, mesmo que não seja nossa. Commit.

### Task 6: Finalização

- [ ] Rodar suíte e build; listar o que testar manualmente; `superpowers:finishing-a-development-branch`. Atenção: o worktree partiu de `origin/main`; o `main` local tem 1 commit à frente (`4cdf844`, guia de deploy) que precisa entrar no merge.
