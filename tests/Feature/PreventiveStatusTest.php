<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\PreventiveStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreventiveStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        $this->travelBack();

        parent::tearDown();
    }

    private function answer(User $user, array $overrides = []): void
    {
        $questionnaire = $user->healthQuestionnaires()->make(array_merge([
            'idade' => 30,
            'fez_preventivo' => 'sim',
            'data_ultimo_preventivo' => '2024-04-01',
            'historico_alteracao' => 'nao',
            'usa_camisinha' => 'sempre',
            'metodo_contraceptivo' => 'pilula',
            'vacinada_hpv' => 'sim',
            'sintomas' => ['nenhum'],
            'barreiras' => ['nenhuma'],
        ], $overrides));
        $questionnaire->created_at = '2026-10-01';
        $questionnaire->save();
    }

    public function test_status_ages_with_the_current_date_not_the_answer_date(): void
    {
        $user = User::factory()->patient()->create();
        $this->answer($user);

        $this->travelTo(now()->setDate(2026, 10, 2));
        $this->assertSame('em_dia', PreventiveStatus::for($user)['level']);

        $this->travelTo(now()->setDate(2028, 10, 2));
        $this->assertSame('atencao_preventiva', PreventiveStatus::for($user)['level']);
    }

    public function test_exam_note_explains_each_missing_date_case(): void
    {
        $cases = [
            [['data_ultimo_preventivo' => null], 'não sabe a data'],
            [['fez_preventivo' => 'nao', 'data_ultimo_preventivo' => null], 'ainda não fez'],
            [['fez_preventivo' => 'prefiro_nao_responder', 'data_ultimo_preventivo' => null], 'preferiu não informar'],
        ];

        foreach ($cases as [$answers, $expected]) {
            $user = User::factory()->patient()->create();
            $this->answer($user, $answers);

            $this->assertStringContainsString($expected, PreventiveStatus::for($user)['exam_note'], $expected);
        }
    }
}
