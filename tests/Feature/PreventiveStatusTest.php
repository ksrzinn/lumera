<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\PreventiveStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\AnswersQuestionnaire;
use Tests\TestCase;

class PreventiveStatusTest extends TestCase
{
    use AnswersQuestionnaire;
    use RefreshDatabase;

    protected function tearDown(): void
    {
        $this->travelBack();

        parent::tearDown();
    }

    private function answer(User $user, array $answers): void
    {
        $questionnaire = $user->healthQuestionnaires()->make($answers);
        $questionnaire->created_at = '2026-10-01';
        $questionnaire->save();
    }

    public function test_legacy_status_ages_with_the_current_date_not_the_answer_date(): void
    {
        $user = User::factory()->patient()->create();
        $this->answer($user, [
            'idade' => 30,
            'fez_preventivo' => 'sim',
            'data_ultimo_preventivo' => '2024-04-01',
            'usa_camisinha' => 'sempre',
            'vacinada_hpv' => 'sim',
        ]);

        $this->travelTo(now()->setDate(2026, 10, 2));
        $this->assertSame('em_dia', PreventiveStatus::for($user)['level']);

        $this->travelTo(now()->setDate(2028, 10, 2));
        $this->assertSame('atencao_preventiva', PreventiveStatus::for($user)['level']);
    }

    public function test_exam_note_describes_the_informed_exam_in_each_case(): void
    {
        $cases = [
            [['quando_ultimo_preventivo' => '1_a_2_anos'], 'entre 1 e 2 anos atrás'],
            [['quando_ultimo_preventivo' => 'sem_data'], 'não sabe a data'],
            [['fez_preventivo' => 'nao', 'quando_ultimo_preventivo' => 'nunca'], 'ainda não fez'],
            [['fez_preventivo' => 'nao_tenho_certeza', 'quando_ultimo_preventivo' => 'nao_lembro_se_fez'], 'preferiu não informar'],
            [['faixa_etaria' => null, 'quando_ultimo_preventivo' => null, 'data_ultimo_preventivo' => '2024-04-01'], '01/04/2024'],
        ];

        foreach ($cases as [$answers, $expected]) {
            $user = User::factory()->patient()->create();
            $this->answer($user, array_merge($this->validAnswers(), $answers));

            $this->assertStringContainsString($expected, PreventiveStatus::for($user)['exam_note'], $expected);
        }
    }
}
