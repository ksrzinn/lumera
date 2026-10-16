<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\Anamnesis\QuestionnaireDefinition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\Concerns\AnswersQuestionnaire;
use Tests\TestCase;

class QuestionnaireValidationTest extends TestCase
{
    use AnswersQuestionnaire;
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->patient()->create();
    }

    private function submit(array $overrides): TestResponse
    {
        return $this->actingAs($this->user)->postJson(route('questionnaires.store'), array_merge($this->validAnswers(), $overrides));
    }

    public function test_valid_answers_are_stored_with_every_question(): void
    {
        $this->submit([])->assertRedirect();

        $stored = $this->user->healthQuestionnaires()->firstOrFail();
        foreach (QuestionnaireDefinition::fields() as $field) {
            $this->assertNotNull($stored->{$field}, $field);
        }
    }

    public function test_every_question_is_required_except_the_conditional_one(): void
    {
        foreach (QuestionnaireDefinition::fields() as $field) {
            if ($field === 'idade_primeira_relacao') {
                continue;
            }

            $answers = $this->validAnswers();
            unset($answers[$field]);

            $this->actingAs($this->user)->postJson(route('questionnaires.store'), $answers)
                ->assertUnprocessable()
                ->assertJsonValidationErrors($field);
        }
    }

    public function test_first_intercourse_age_is_required_only_after_sexual_life_started(): void
    {
        $this->submit(['idade_primeira_relacao' => null])->assertUnprocessable()->assertJsonValidationErrors('idade_primeira_relacao');

        $this->submit(['vida_sexual_iniciada' => 'nao', 'idade_primeira_relacao' => '15_17'])->assertRedirect();
        $this->assertNull($this->user->healthQuestionnaires()->latest('id')->first()->idade_primeira_relacao);
    }

    public function test_unknown_option_is_rejected(): void
    {
        $this->submit(['faixa_etaria' => '200_anos'])->assertUnprocessable()->assertJsonValidationErrors('faixa_etaria');
    }

    public function test_nested_arrays_are_rejected_without_server_error(): void
    {
        $this->submit(['sintomas' => [['x'], 'nenhum']])->assertUnprocessable()->assertJsonValidationErrors('sintomas.0');
        $this->submit(['barreiras' => [['a'], ['b']]])->assertUnprocessable()->assertJsonValidationErrors('barreiras.0');
        $this->submit(['metodos_contraceptivos' => [['x'], 'nenhum']])->assertUnprocessable()->assertJsonValidationErrors('metodos_contraceptivos.0');
    }

    public function test_keyed_arrays_are_rejected(): void
    {
        $this->submit(['sintomas' => ['a' => 'dor_pelvica_persistente']])->assertUnprocessable();
    }

    public function test_exclusive_options_cannot_be_mixed_with_others(): void
    {
        $this->submit(['sintomas' => ['nenhum', 'dor_pelvica_persistente']])->assertUnprocessable()->assertJsonValidationErrors('sintomas');
        $this->submit(['barreiras' => ['nenhuma', 'medo']])->assertUnprocessable()->assertJsonValidationErrors('barreiras');
        $this->submit(['metodos_contraceptivos' => ['nenhum', 'pilula']])->assertUnprocessable()->assertJsonValidationErrors('metodos_contraceptivos');
        $this->submit(['metodos_contraceptivos' => ['pilula', 'preservativo_masculino']])->assertRedirect();
    }
}
