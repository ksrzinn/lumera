<?php

namespace Tests\Unit;

use App\Enums\CareLevel;
use App\Models\HealthQuestionnaire;
use App\Support\Anamnesis\AnamnesisAnalyzer;
use App\Support\Anamnesis\AnamnesisResult;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class AnamnesisAnalyzerTest extends TestCase
{
    private CarbonImmutable $today;

    protected function setUp(): void
    {
        parent::setUp();

        $this->today = CarbonImmutable::parse('2026-10-06');
    }

    private function analyze(array $answers): AnamnesisResult
    {
        $questionnaire = new HealthQuestionnaire(array_merge([
            'idade' => 30,
            'fez_preventivo' => 'sim',
            'data_ultimo_preventivo' => '2025-10-06',
            'historico_alteracao' => 'nao',
            'acompanhamento_alteracao' => null,
            'usa_camisinha' => 'sempre',
            'metodo_contraceptivo' => 'pilula',
            'vacinada_hpv' => 'sim',
            'sintomas' => ['nenhum'],
            'barreiras' => ['nenhuma'],
        ], $answers));

        return (new AnamnesisAnalyzer)->analyze($questionnaire, $this->today);
    }

    public function test_everything_in_order_is_em_dia_and_not_urgent(): void
    {
        $result = $this->analyze([]);

        $this->assertSame(CareLevel::EmDia, $result->level);
        $this->assertFalse($result->urgent);
    }

    public function test_never_screened_within_screening_age_needs_professional_evaluation(): void
    {
        $result = $this->analyze(['fez_preventivo' => 'nao', 'data_ultimo_preventivo' => null]);

        $this->assertSame(CareLevel::AvaliacaoProfissional, $result->level);
        $this->assertContains('exame_nunca_feito', $result->codes());
    }

    public function test_never_screened_below_screening_age_is_not_raised(): void
    {
        $result = $this->analyze(['idade' => 20, 'fez_preventivo' => 'nao', 'data_ultimo_preventivo' => null]);

        $this->assertSame(CareLevel::EmDia, $result->level);
        $this->assertContains('exame_fora_da_faixa', $result->codes());
    }

    public function test_never_screened_above_screening_age_is_attention(): void
    {
        $result = $this->analyze(['idade' => 70, 'fez_preventivo' => 'nao', 'data_ultimo_preventivo' => null]);

        $this->assertSame(CareLevel::AtencaoPreventiva, $result->level);
    }

    public function test_unknown_exam_answers_are_attention_never_professional(): void
    {
        foreach (['nao_lembro', 'prefiro_nao_responder'] as $answer) {
            $result = $this->analyze(['fez_preventivo' => $answer, 'data_ultimo_preventivo' => null]);

            $this->assertSame(CareLevel::AtencaoPreventiva, $result->level, $answer);
            $this->assertContains('exame_desconhecido', $result->codes());
        }
    }

    public function test_exam_done_without_known_date_is_attention(): void
    {
        $result = $this->analyze(['data_ultimo_preventivo' => null]);

        $this->assertSame(CareLevel::AtencaoPreventiva, $result->level);
        $this->assertContains('exame_sem_data', $result->codes());
    }

    public function test_exam_exactly_at_recent_window_is_still_em_dia(): void
    {
        $result = $this->analyze(['data_ultimo_preventivo' => '2023-10-06']);

        $this->assertSame(CareLevel::EmDia, $result->level);
    }

    public function test_exam_one_day_past_recent_window_is_attention_not_professional(): void
    {
        $result = $this->analyze(['data_ultimo_preventivo' => '2023-10-05']);

        $this->assertSame(CareLevel::AtencaoPreventiva, $result->level);
        $this->assertContains('exame_antigo', $result->codes());
    }

    public function test_altered_history_without_follow_up_needs_professional_evaluation(): void
    {
        foreach (['nao', 'nao_sei'] as $followUp) {
            $result = $this->analyze(['historico_alteracao' => 'sim', 'acompanhamento_alteracao' => $followUp]);

            $this->assertSame(CareLevel::AvaliacaoProfissional, $result->level, $followUp);
            $this->assertContains('alteracao_sem_acompanhamento', $result->codes());
        }
    }

    public function test_altered_history_with_follow_up_does_not_raise_the_level(): void
    {
        foreach (['sim', 'em_andamento'] as $followUp) {
            $result = $this->analyze(['historico_alteracao' => 'sim', 'acompanhamento_alteracao' => $followUp]);

            $this->assertSame(CareLevel::EmDia, $result->level, $followUp);
        }
    }

    public function test_unknown_altered_history_is_attention(): void
    {
        foreach (['nao_sei', 'prefiro_nao_responder'] as $answer) {
            $result = $this->analyze(['historico_alteracao' => $answer]);

            $this->assertSame(CareLevel::AtencaoPreventiva, $result->level, $answer);
        }
    }

    public function test_each_symptom_needs_professional_evaluation_even_with_recent_exam(): void
    {
        foreach (array_keys(HealthQuestionnaire::SYMPTOM_OPTIONS) as $symptom) {
            $result = $this->analyze(['sintomas' => [$symptom]]);

            $this->assertSame(CareLevel::AvaliacaoProfissional, $result->level, $symptom);
        }
    }

    public function test_ordinary_symptom_is_not_urgent(): void
    {
        $result = $this->analyze(['sintomas' => ['sangramento_pos_relacao']]);

        $this->assertFalse($result->urgent);
    }

    public function test_heavy_bleeding_with_malaise_is_urgent(): void
    {
        $result = $this->analyze(['sintomas' => ['sangramento_intenso_com_mal_estar']]);

        $this->assertTrue($result->urgent);
        $this->assertSame(CareLevel::AvaliacaoProfissional, $result->level);
    }

    public function test_skipped_symptoms_question_is_attention_not_negative(): void
    {
        $result = $this->analyze(['sintomas' => ['prefiro_nao_responder']]);

        $this->assertSame(CareLevel::AtencaoPreventiva, $result->level);
    }

    public function test_inconsistent_condom_use_is_attention(): void
    {
        foreach (['maioria_das_vezes', 'as_vezes', 'raramente', 'nunca'] as $answer) {
            $result = $this->analyze(['usa_camisinha' => $answer]);

            $this->assertSame(CareLevel::AtencaoPreventiva, $result->level, $answer);
            $this->assertContains('camisinha_inconsistente', $result->codes());
        }
    }

    public function test_condom_not_applicable_or_skipped_does_not_raise_the_level(): void
    {
        foreach (['nao_se_aplica', 'prefiro_nao_responder'] as $answer) {
            $this->assertSame(CareLevel::EmDia, $this->analyze(['usa_camisinha' => $answer])->level, $answer);
        }
    }

    public function test_other_contraceptive_without_condom_adds_sti_note(): void
    {
        $result = $this->analyze(['usa_camisinha' => 'nunca', 'metodo_contraceptivo' => 'pilula']);

        $this->assertContains('metodo_nao_protege_ist', $result->codes());
    }

    public function test_no_sti_note_when_condom_is_always_used(): void
    {
        $this->assertNotContains('metodo_nao_protege_ist', $this->analyze([])->codes());
    }

    public function test_missing_or_unknown_hpv_vaccine_is_attention(): void
    {
        foreach (['nao', 'nao_sei', 'prefiro_nao_responder'] as $answer) {
            $result = $this->analyze(['vacinada_hpv' => $answer]);

            $this->assertSame(CareLevel::AtencaoPreventiva, $result->level, $answer);
            $this->assertContains('vacina_pendente', $result->codes());
        }
    }

    public function test_access_barriers_alone_do_not_raise_the_level_but_are_reported(): void
    {
        $result = $this->analyze(['barreiras' => ['medo', 'sem_tempo']]);

        $this->assertSame(CareLevel::EmDia, $result->level);
        $this->assertContains('barreira_medo', $result->codes());
        $this->assertContains('barreira_sem_tempo', $result->codes());
    }

    public function test_most_important_level_wins_and_all_findings_are_kept(): void
    {
        $result = $this->analyze([
            'sintomas' => ['dor_pelvica_persistente'],
            'usa_camisinha' => 'nunca',
            'vacinada_hpv' => 'nao',
        ]);

        $this->assertSame(CareLevel::AvaliacaoProfissional, $result->level);
        $this->assertContains('sintoma_dor_pelvica_persistente', $result->codes());
        $this->assertContains('camisinha_inconsistente', $result->codes());
        $this->assertContains('vacina_pendente', $result->codes());
    }

    public function test_vaccine_and_condom_do_not_hide_missing_screening(): void
    {
        $result = $this->analyze(['fez_preventivo' => 'nao', 'data_ultimo_preventivo' => null]);

        $this->assertSame(CareLevel::AvaliacaoProfissional, $result->level);
    }

    public function test_legacy_row_has_no_symptom_or_history_findings_and_flags_old_version(): void
    {
        $result = $this->analyze(['historico_alteracao' => null, 'sintomas' => null, 'barreiras' => null]);

        $this->assertSame(CareLevel::EmDia, $result->level);
        $this->assertContains('versao_anterior', $result->codes());
    }

    public function test_contraceptive_method_alone_does_not_change_the_level(): void
    {
        $this->assertSame(CareLevel::EmDia, $this->analyze(['metodo_contraceptivo' => null])->level);
        $this->assertSame(CareLevel::EmDia, $this->analyze(['metodo_contraceptivo' => 'nenhum'])->level);
    }

    public function test_every_finding_has_translated_title_and_guidance(): void
    {
        $result = $this->analyze([
            'fez_preventivo' => 'nao_lembro',
            'data_ultimo_preventivo' => null,
            'historico_alteracao' => 'sim',
            'acompanhamento_alteracao' => 'nao',
            'usa_camisinha' => 'raramente',
            'vacinada_hpv' => 'nao',
            'sintomas' => ['sangramento_intenso_com_mal_estar'],
            'barreiras' => ['vergonha', 'falta_informacao', 'dificuldade_agendamento'],
        ]);

        foreach ($result->findings as $finding) {
            $this->assertStringNotContainsString('anamnese.', $finding->title(), $finding->code);
            $this->assertStringNotContainsString('anamnese.', $finding->guidance(), $finding->code);
        }
    }
}
