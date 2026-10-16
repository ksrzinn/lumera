<?php

namespace Tests\Unit;

use App\Enums\CareLevel;
use App\Models\HealthQuestionnaire;
use App\Support\Anamnesis\AnamnesisAnalyzer;
use App\Support\Anamnesis\AnamnesisResult;
use App\Support\Anamnesis\QuestionnaireDefinition;
use Carbon\CarbonImmutable;
use Tests\Concerns\AnswersQuestionnaire;
use Tests\TestCase;

class AnamnesisAnalyzerTest extends TestCase
{
    use AnswersQuestionnaire;

    private CarbonImmutable $today;

    protected function setUp(): void
    {
        parent::setUp();

        $this->today = CarbonImmutable::parse('2026-10-06');
    }

    private function analyze(array $answers): AnamnesisResult
    {
        $questionnaire = new HealthQuestionnaire(array_merge($this->validAnswers(), $answers));

        return (new AnamnesisAnalyzer)->analyze($questionnaire, $this->today);
    }

    private function assertLevel(CareLevel $expected, array $answers, ?string $message = null): void
    {
        $this->assertSame($expected, $this->analyze($answers)->level, $message ?? json_encode($answers));
    }

    public function test_everything_in_order_is_em_dia_and_not_urgent(): void
    {
        $result = $this->analyze([]);

        $this->assertSame(CareLevel::EmDia, $result->level);
        $this->assertFalse($result->urgent);
    }

    public function test_never_screened_within_screening_age_needs_professional_evaluation(): void
    {
        $result = $this->analyze(['fez_preventivo' => 'nao', 'quando_ultimo_preventivo' => 'nunca']);

        $this->assertSame(CareLevel::AvaliacaoProfissional, $result->level);
        $this->assertContains('exame_nunca_feito', $result->codes());
    }

    public function test_never_screened_outside_screening_age_is_not_a_professional_alert(): void
    {
        $this->assertLevel(CareLevel::EmDia, ['faixa_etaria' => '18_24', 'fez_preventivo' => 'nao']);
        $this->assertLevel(CareLevel::EmDia, ['faixa_etaria' => 'menos_18', 'fez_preventivo' => 'nao']);
        $this->assertLevel(CareLevel::AtencaoPreventiva, ['faixa_etaria' => '65_mais', 'fez_preventivo' => 'nao']);
    }

    public function test_never_screened_with_unknown_age_is_attention(): void
    {
        $this->assertLevel(CareLevel::AtencaoPreventiva, ['faixa_etaria' => 'prefiro_nao_informar', 'fez_preventivo' => 'nao']);
    }

    public function test_every_age_band_in_range_gets_the_professional_alert_when_never_screened(): void
    {
        foreach (['25_29', '30_39', '40_49', '50_59', '60_64'] as $band) {
            $this->assertLevel(CareLevel::AvaliacaoProfissional, ['faixa_etaria' => $band, 'fez_preventivo' => 'nao'], $band);
        }
    }

    public function test_unsure_exam_answers_are_attention_never_professional(): void
    {
        $this->assertLevel(CareLevel::AtencaoPreventiva, ['fez_preventivo' => 'nao_tenho_certeza', 'quando_ultimo_preventivo' => 'nao_lembro_se_fez']);
        $this->assertLevel(CareLevel::AtencaoPreventiva, ['fez_preventivo' => 'sim', 'quando_ultimo_preventivo' => 'nao_lembro_se_fez']);
        $this->assertContains('exame_desconhecido', $this->analyze(['fez_preventivo' => 'nao_tenho_certeza'])->codes());
    }

    public function test_exam_done_without_known_date_is_attention(): void
    {
        $result = $this->analyze(['quando_ultimo_preventivo' => 'sem_data']);

        $this->assertSame(CareLevel::AtencaoPreventiva, $result->level);
        $this->assertContains('exame_sem_data', $result->codes());
    }

    public function test_exam_up_to_three_years_ago_is_recent_and_older_is_not(): void
    {
        foreach (['menos_1_ano', '1_a_2_anos', '2_a_3_anos'] as $period) {
            $this->assertLevel(CareLevel::EmDia, ['quando_ultimo_preventivo' => $period], $period);
        }

        $result = $this->analyze(['quando_ultimo_preventivo' => 'mais_3_anos']);
        $this->assertSame(CareLevel::AtencaoPreventiva, $result->level);
        $this->assertContains('exame_antigo', $result->codes());
    }

    public function test_altered_or_repeat_result_without_full_follow_up_needs_professional_evaluation(): void
    {
        foreach (['parcial', 'nao', 'aguardando', 'nao_se_aplica', 'prefiro_nao_responder'] as $followUp) {
            foreach (['alterado', 'repetir'] as $result) {
                $answers = ['resultado_preventivo' => $result, 'acompanhamento_alteracao' => $followUp];
                $this->assertLevel(CareLevel::AvaliacaoProfissional, $answers, "$result/$followUp");
            }
        }

        $analysis = $this->analyze(['resultado_alterado_anterior' => 'sim', 'acompanhamento_alteracao' => 'nao']);
        $this->assertContains('alteracao_sem_acompanhamento', $analysis->codes());
    }

    public function test_altered_result_with_complete_follow_up_does_not_raise_the_level(): void
    {
        $result = $this->analyze(['resultado_alterado_anterior' => 'sim', 'acompanhamento_alteracao' => 'sim_todo']);

        $this->assertSame(CareLevel::EmDia, $result->level);
        $this->assertContains('alteracao_com_acompanhamento', $result->codes());
    }

    public function test_unknown_or_missing_result_is_attention(): void
    {
        $this->assertLevel(CareLevel::AtencaoPreventiva, ['resultado_preventivo' => 'nao_sei']);
        $this->assertLevel(CareLevel::AtencaoPreventiva, ['resultado_preventivo' => 'sem_resultado']);
        $this->assertLevel(CareLevel::AtencaoPreventiva, ['resultado_alterado_anterior' => 'nao_sei']);
    }

    public function test_no_cervix_or_sexual_life_not_started_skips_screening_findings(): void
    {
        foreach ([['possui_colo_utero' => 'nao'], ['vida_sexual_iniciada' => 'nao']] as $answers) {
            $result = $this->analyze($answers + ['fez_preventivo' => 'nao', 'faixa_etaria' => '30_39']);

            $this->assertSame(CareLevel::EmDia, $result->level, json_encode($answers));
            $this->assertNotContains('exame_nunca_feito', $result->codes());
        }

        $this->assertContains('sem_colo_utero', $this->analyze(['possui_colo_utero' => 'nao'])->codes());
        $this->assertContains('vida_sexual_nao_iniciada', $this->analyze(['vida_sexual_iniciada' => 'nao'])->codes());
    }

    public function test_unknown_cervix_status_is_attention_but_skipped_answer_is_not(): void
    {
        $this->assertLevel(CareLevel::AtencaoPreventiva, ['possui_colo_utero' => 'nao_sei']);
        $this->assertLevel(CareLevel::EmDia, ['possui_colo_utero' => 'prefiro_nao_responder']);
    }

    public function test_each_symptom_needs_professional_evaluation_even_with_recent_exam(): void
    {
        foreach (array_keys(QuestionnaireDefinition::options('sintomas')) as $symptom) {
            if (in_array($symptom, ['nenhum', 'prefiro_nao_responder'], true)) {
                continue;
            }

            $this->assertLevel(CareLevel::AvaliacaoProfissional, ['sintomas' => [$symptom]], $symptom);
        }
    }

    public function test_only_heavy_bleeding_with_malaise_is_urgent(): void
    {
        $this->assertFalse($this->analyze(['sintomas' => ['sangramento_pos_relacao']])->urgent);
        $this->assertTrue($this->analyze(['sintomas' => ['sangramento_intenso_com_mal_estar']])->urgent);
    }

    public function test_skipped_symptoms_question_is_attention_not_negative(): void
    {
        $this->assertLevel(CareLevel::AtencaoPreventiva, ['sintomas' => ['prefiro_nao_responder']]);
    }

    public function test_inconsistent_condom_use_is_attention(): void
    {
        foreach (['maioria_das_vezes', 'as_vezes', 'raramente', 'nunca'] as $answer) {
            $result = $this->analyze(['usa_camisinha' => $answer]);

            $this->assertSame(CareLevel::AtencaoPreventiva, $result->level, $answer);
            $this->assertContains('camisinha_inconsistente', $result->codes());
        }
    }

    public function test_no_sexual_activity_or_skipped_condom_answer_does_not_raise_the_level(): void
    {
        $this->assertLevel(CareLevel::EmDia, ['usa_camisinha' => 'nao_tenho_relacoes']);
        $this->assertLevel(CareLevel::EmDia, ['usa_camisinha' => 'prefiro_nao_responder']);
    }

    public function test_non_barrier_method_with_inconsistent_condom_adds_sti_note(): void
    {
        $this->assertContains('metodo_nao_protege_ist', $this->analyze(['usa_camisinha' => 'nunca', 'metodos_contraceptivos' => ['diu_hormonal']])->codes());
        $this->assertNotContains('metodo_nao_protege_ist', $this->analyze(['usa_camisinha' => 'nunca', 'metodos_contraceptivos' => ['preservativo_masculino']])->codes());
        $this->assertNotContains('metodo_nao_protege_ist', $this->analyze([])->codes());
    }

    public function test_beliefs_that_contraceptive_or_vaccine_replaces_screening_are_attention(): void
    {
        foreach (['sim', 'nao_sei'] as $answer) {
            $contraceptive = $this->analyze(['acredita_anticoncepcional_substitui' => $answer]);
            $this->assertSame(CareLevel::AtencaoPreventiva, $contraceptive->level, $answer);
            $this->assertContains('duvida_contraceptivo_substitui', $contraceptive->codes());

            $vaccine = $this->analyze(['acredita_vacina_elimina_rastreio' => $answer]);
            $this->assertSame(CareLevel::AtencaoPreventiva, $vaccine->level, $answer);
            $this->assertContains('duvida_vacina_substitui', $vaccine->codes());
        }
    }

    public function test_only_a_completed_hpv_vaccine_schedule_has_no_pending_finding(): void
    {
        foreach (['incompleto', 'nao_sei_se_completou', 'nao', 'nao_lembro', 'prefiro_nao_responder'] as $answer) {
            $result = $this->analyze(['vacinada_hpv' => $answer]);

            $this->assertSame(CareLevel::AtencaoPreventiva, $result->level, $answer);
            $this->assertContains('vacina_pendente', $result->codes());
        }

        $this->assertLevel(CareLevel::EmDia, ['vacinada_hpv' => 'completou']);
    }

    public function test_informational_answers_never_raise_the_level_but_are_reported(): void
    {
        $result = $this->analyze([
            'diagnostico_hpv' => 'sim',
            'diagnostico_outra_ist' => 'sim',
            'exames_ist' => 'nunca',
            'sabe_preservativo_hpv' => 'nao',
            'sabe_onde_vacinar' => 'duvidas',
            'frequencia_consultas' => 'so_sintomas',
            'uso_prolongado_pilula' => 'sim_5_ou_mais',
            'teste_hpv' => 'sim',
        ]);

        $this->assertSame(CareLevel::EmDia, $result->level);
        foreach (['diagnostico_hpv', 'diagnostico_ist', 'exames_ist_sugestao', 'preservativo_hpv_orientacao', 'vacina_onde', 'consultas_pouco_frequentes', 'pilula_prolongada'] as $code) {
            $this->assertContains($code, $result->codes(), $code);
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
        foreach (['sintoma_dor_pelvica_persistente', 'camisinha_inconsistente', 'vacina_pendente'] as $code) {
            $this->assertContains($code, $result->codes());
        }
    }

    public function test_vaccine_and_condom_do_not_hide_missing_screening(): void
    {
        $this->assertLevel(CareLevel::AvaliacaoProfissional, ['fez_preventivo' => 'nao', 'usa_camisinha' => 'sempre', 'vacinada_hpv' => 'completou']);
    }

    public function test_contraceptive_methods_alone_do_not_change_the_level(): void
    {
        $this->assertLevel(CareLevel::EmDia, ['metodos_contraceptivos' => ['nenhum']]);
        $this->assertLevel(CareLevel::EmDia, ['usa_contraceptivo' => 'nao', 'metodos_contraceptivos' => ['nenhum']]);
    }

    public function test_legacy_row_is_classified_from_the_old_columns_and_flags_old_version(): void
    {
        $legacy = new HealthQuestionnaire([
            'idade' => 30,
            'fez_preventivo' => 'sim',
            'data_ultimo_preventivo' => '2025-10-06',
            'usa_camisinha' => 'sempre',
            'vacinada_hpv' => 'sim',
        ]);

        $result = (new AnamnesisAnalyzer)->analyze($legacy, $this->today);

        $this->assertSame(CareLevel::EmDia, $result->level);
        $this->assertContains('versao_anterior', $result->codes());
    }

    public function test_legacy_row_ages_by_its_exam_date_and_age(): void
    {
        $old = new HealthQuestionnaire(['idade' => 30, 'fez_preventivo' => 'sim', 'data_ultimo_preventivo' => '2023-10-05', 'usa_camisinha' => 'sempre', 'vacinada_hpv' => 'sim']);
        $never = new HealthQuestionnaire(['idade' => 30, 'fez_preventivo' => 'nao', 'usa_camisinha' => 'sempre', 'vacinada_hpv' => 'sim']);
        $young = new HealthQuestionnaire(['idade' => 20, 'fez_preventivo' => 'nao', 'usa_camisinha' => 'sempre', 'vacinada_hpv' => 'sim']);
        $analyzer = new AnamnesisAnalyzer;

        $this->assertSame(CareLevel::AtencaoPreventiva, $analyzer->analyze($old, $this->today)->level);
        $this->assertSame(CareLevel::AvaliacaoProfissional, $analyzer->analyze($never, $this->today)->level);
        $this->assertSame(CareLevel::EmDia, $analyzer->analyze($young, $this->today)->level);
    }

    public function test_every_finding_has_translated_title_and_guidance(): void
    {
        $result = $this->analyze([
            'faixa_etaria' => '65_mais',
            'fez_preventivo' => 'nao_tenho_certeza',
            'resultado_preventivo' => 'sem_resultado',
            'resultado_alterado_anterior' => 'sim',
            'acompanhamento_alteracao' => 'parcial',
            'usa_camisinha' => 'raramente',
            'acredita_anticoncepcional_substitui' => 'sim',
            'acredita_vacina_elimina_rastreio' => 'nao_sei',
            'diagnostico_hpv' => 'sim',
            'diagnostico_outra_ist' => 'sim',
            'exames_ist' => 'nao_sei',
            'sabe_preservativo_hpv' => 'ja_ouvi',
            'sabe_onde_vacinar' => 'nao',
            'frequencia_consultas' => 'nao_costuma',
            'uso_prolongado_pilula' => 'sim_nao_sei_tempo',
            'vacinada_hpv' => 'incompleto',
            'sintomas' => ['sangramento_intenso_com_mal_estar'],
            'barreiras' => ['vergonha', 'falta_informacao', 'dificuldade_agendamento'],
        ]);

        foreach ($result->findings as $finding) {
            $this->assertStringNotContainsString('anamnese.', $finding->title(), $finding->code);
            $this->assertStringNotContainsString('anamnese.', $finding->guidance(), $finding->code);
        }
    }
}
