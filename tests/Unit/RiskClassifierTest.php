<?php

namespace Tests\Unit;

use App\Enums\RiskLevel;
use App\Models\HealthQuestionnaire;
use App\Support\RiskClassifier;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class RiskClassifierTest extends TestCase
{
    private CarbonImmutable $today;

    protected function setUp(): void
    {
        parent::setUp();

        $this->today = CarbonImmutable::parse('2026-10-06');
    }

    private function classify(array $answers): RiskLevel
    {
        $questionnaire = new HealthQuestionnaire(array_merge([
            'idade' => 30,
            'fez_preventivo' => 'sim',
            'data_ultimo_preventivo' => '2025-10-06',
            'usa_camisinha' => 'sempre',
            'metodo_contraceptivo' => 'pilula',
            'vacinada_hpv' => 'sim',
        ], $answers));

        return (new RiskClassifier)->classify($questionnaire, $this->today);
    }

    public function test_recent_preventive_with_condom_and_vaccine_is_low_risk(): void
    {
        $this->assertSame(RiskLevel::BaixoRisco, $this->classify([]));
    }

    public function test_never_had_preventive_is_high_priority(): void
    {
        $level = $this->classify(['fez_preventivo' => 'nao', 'data_ultimo_preventivo' => null]);

        $this->assertSame(RiskLevel::AltaPrioridade, $level);
    }

    public function test_does_not_remember_preventive_is_high_priority(): void
    {
        $level = $this->classify(['fez_preventivo' => 'nao_lembro', 'data_ultimo_preventivo' => null]);

        $this->assertSame(RiskLevel::AltaPrioridade, $level);
    }

    public function test_preventive_more_than_three_years_ago_is_high_priority(): void
    {
        $level = $this->classify(['data_ultimo_preventivo' => '2023-10-05']);

        $this->assertSame(RiskLevel::AltaPrioridade, $level);
    }

    public function test_preventive_exactly_three_years_ago_is_not_overdue(): void
    {
        $level = $this->classify(['data_ultimo_preventivo' => '2023-10-06']);

        $this->assertSame(RiskLevel::BaixoRisco, $level);
    }

    public function test_preventive_done_without_date_is_high_priority(): void
    {
        $level = $this->classify(['data_ultimo_preventivo' => null]);

        $this->assertSame(RiskLevel::AltaPrioridade, $level);
    }

    public function test_inconsistent_condom_use_is_attention(): void
    {
        $this->assertSame(RiskLevel::Atencao, $this->classify(['usa_camisinha' => 'as_vezes']));
        $this->assertSame(RiskLevel::Atencao, $this->classify(['usa_camisinha' => 'nunca']));
    }

    public function test_no_hpv_vaccine_is_attention(): void
    {
        $this->assertSame(RiskLevel::Atencao, $this->classify(['vacinada_hpv' => 'nao']));
    }

    public function test_unknown_hpv_vaccine_status_is_attention(): void
    {
        $this->assertSame(RiskLevel::Atencao, $this->classify(['vacinada_hpv' => 'nao_sei']));
    }

    public function test_high_priority_wins_over_attention(): void
    {
        $level = $this->classify([
            'fez_preventivo' => 'nao',
            'data_ultimo_preventivo' => null,
            'usa_camisinha' => 'nunca',
            'vacinada_hpv' => 'nao',
        ]);

        $this->assertSame(RiskLevel::AltaPrioridade, $level);
    }

    public function test_contraceptive_method_does_not_change_the_result(): void
    {
        $this->assertSame(RiskLevel::BaixoRisco, $this->classify(['metodo_contraceptivo' => null]));
        $this->assertSame(RiskLevel::BaixoRisco, $this->classify(['metodo_contraceptivo' => 'nenhum']));
    }
}
